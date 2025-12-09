<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Appointment.php';
require_once __DIR__ . '/../Models/Doctor.php';

class AppointmentResultController {
    private function findAppointment($appointmentId) {
        $model = new Appointment();
        return $model->findById($appointmentId);
    }

    private function ensureRegularAppointment($appointment) {
        if (!$appointment) {
            $_SESSION['error'] = 'Không tìm thấy lịch hẹn.';
            header('Location: ' . APP_URL . '/appointments');
            exit;
        }
        if (!empty($appointment['package_appointment_id'])) {
            $_SESSION['error'] = 'Chức năng này chỉ áp dụng cho lịch khám thường.';
            header('Location: ' . APP_URL . '/appointments/' . $appointment['id']);
            exit;
        }
    }

    private function ensureCanEdit($appointment) {
        $this->ensureRegularAppointment($appointment);

        $status = $appointment['status'] ?? null;
        if ($status !== 'completed') {
            $_SESSION['error'] = 'Vui lòng hoàn thành lịch hẹn trước khi nhập kết quả.';
            header('Location: ' . APP_URL . '/appointments/' . $appointment['id']);
            exit;
        }

        if (!Auth::isDoctor()) {
            $_SESSION['error'] = 'Bạn không có quyền cập nhật kết quả.';
            header('Location: ' . APP_URL . '/appointments/' . $appointment['id']);
            exit;
        }

        $assignedDoctorId = (int)($appointment['doctor_id'] ?? 0);
        if ($assignedDoctorId === 0) {
            $_SESSION['error'] = 'Lịch hẹn chưa được phân công bác sĩ.';
            header('Location: ' . APP_URL . '/appointments/' . $appointment['id']);
            exit;
        }

        require_once APP_PATH . '/Models/Doctor.php';
        $doctorModel = new Doctor();
        $currentDoctor = $doctorModel->findByUserId(Auth::id());
        if (!$currentDoctor || (int)$currentDoctor['id'] !== $assignedDoctorId) {
            $_SESSION['error'] = 'Chỉ bác sĩ phụ trách mới được cập nhật kết quả.';
            header('Location: ' . APP_URL . '/appointments/' . $appointment['id']);
            exit;
        }
    }

    private function buildItemsFromRequest() {
        $metrics = $_POST['metric_name'] ?? [];
        $values = $_POST['result_value'] ?? [];
        $references = $_POST['reference_range'] ?? [];
        $statuses = $_POST['result_status'] ?? [];
        $notes = $_POST['notes'] ?? [];

        $count = max(count($metrics), count($values), count($references), count($statuses), count($notes));
        $items = [];
        for ($i = 0; $i < $count; $i++) {
            $name = trim((string)($metrics[$i] ?? ''));
            $value = trim((string)($values[$i] ?? ''));
            $reference = trim((string)($references[$i] ?? ''));
            $statusRaw = trim((string)($statuses[$i] ?? ''));
            $note = trim((string)($notes[$i] ?? ''));

            if ($name === '' && $value === '' && $reference === '' && $statusRaw === '' && $note === '') {
                continue;
            }

            $status = strtolower($statusRaw);
            if (!in_array($status, ['normal', 'abnormal', 'pending'], true)) {
                $status = 'normal';
            }

            $items[] = [
                'metric_name' => mb_substr($name, 0, 255),
                'result_value' => mb_substr($value, 0, 255),
                'reference_range' => mb_substr($reference, 0, 255),
                'result_status' => mb_substr($status, 0, 50),
                'notes' => mb_substr($note, 0, 255),
            ];
        }

        return $items;
    }

    private function ensureHasItems(array $items, $appointmentId) {
        if (empty($items)) {
            $_SESSION['error'] = 'Vui lòng nhập ít nhất một dòng kết quả.';
            header('Location: ' . APP_URL . '/appointments/' . $appointmentId);
            exit;
        }
    }

    private function persistResults($appointmentId, array $items, $status, $reviewNote = null) {
        $db = new Database();
        $conn = $db->getConnection();
        $conn->beginTransaction();

        try {
            $headerStmt = $conn->prepare('SELECT id FROM appointment_results WHERE appointment_id = ? LIMIT 1 FOR UPDATE');
            $headerStmt->execute([(int)$appointmentId]);
            $header = $headerStmt->fetch(PDO::FETCH_ASSOC);

            $now = date('Y-m-d H:i:s');
            $reviewNoteValue = ($reviewNote !== null && trim($reviewNote) !== '') ? trim($reviewNote) : null;

            if ($header) {
                $resultId = (int)$header['id'];
                $updateSql = 'UPDATE appointment_results SET status = :status, review_note = :review_note, updated_at = NOW(), approved_at = NULL';
                if ($status === 'draft') {
                    $updateSql .= ', submitted_at = NULL';
                } elseif ($status === 'submitted') {
                    $updateSql .= ', submitted_at = NOW()';
                }
                $updateSql .= ' WHERE id = :id';
                $update = $conn->prepare($updateSql);
                $update->execute([
                    ':status' => $status,
                    ':review_note' => $reviewNoteValue,
                    ':id' => $resultId,
                ]);
            } else {
                $submittedAt = ($status === 'submitted') ? $now : null;
                $insert = $conn->prepare('INSERT INTO appointment_results (appointment_id, status, review_note, submitted_at, approved_at, created_at, updated_at) VALUES (?,?,?,?,NULL,NOW(),NOW())');
                $insert->execute([
                    (int)$appointmentId,
                    $status,
                    $reviewNoteValue,
                    $submittedAt,
                ]);
                $resultId = (int)$conn->lastInsertId();
            }

            $delete = $conn->prepare('DELETE FROM appointment_result_items WHERE result_id = ?');
            $delete->execute([$resultId]);

            if (!empty($items)) {
                $insertItem = $conn->prepare('INSERT INTO appointment_result_items (result_id, metric_name, result_value, reference_range, result_status, notes) VALUES (?,?,?,?,?,?)');
                foreach ($items as $row) {
                    $insertItem->execute([
                        $resultId,
                        $row['metric_name'] !== '' ? $row['metric_name'] : null,
                        $row['result_value'] !== '' ? $row['result_value'] : null,
                        $row['reference_range'] !== '' ? $row['reference_range'] : null,
                        $row['result_status'] !== '' ? $row['result_status'] : null,
                        $row['notes'] !== '' ? $row['notes'] : null,
                    ]);
                }
            }

            $conn->commit();
        } catch (\Throwable $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function save($appointmentId) {
        Auth::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/appointments/' . $appointmentId);
            return;
        }

        $appointment = $this->findAppointment($appointmentId);
        $this->ensureCanEdit($appointment);

        $items = $this->buildItemsFromRequest();
        $this->ensureHasItems($items, $appointmentId);

        $note = isset($_POST['general_note']) ? trim((string)$_POST['general_note']) : null;

        try {
            $this->persistResults($appointmentId, $items, 'draft', $note);
            $_SESSION['success'] = 'Đã lưu kết quả (nháp).';
        } catch (\Throwable $e) {
            $_SESSION['error'] = 'Lỗi lưu kết quả: ' . $e->getMessage();
        }

        header('Location: ' . APP_URL . '/appointments/' . $appointmentId);
    }

    public function submit($appointmentId) {
        Auth::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/appointments/' . $appointmentId);
            return;
        }

        $appointment = $this->findAppointment($appointmentId);
        $this->ensureCanEdit($appointment);

        $items = $this->buildItemsFromRequest();
        $this->ensureHasItems($items, $appointmentId);

        $note = isset($_POST['general_note']) ? trim((string)$_POST['general_note']) : null;

        try {
            $this->persistResults($appointmentId, $items, 'submitted', $note);
            $_SESSION['success'] = 'Đã lưu và nộp kết quả.';
        } catch (\Throwable $e) {
            $_SESSION['error'] = 'Lỗi nộp kết quả: ' . $e->getMessage();
        }

        header('Location: ' . APP_URL . '/appointments/' . $appointmentId);
    }

}
