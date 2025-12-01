<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Helpers/Mailer.php';

class PrescriptionController {
    private function notify($userId, $title, $message, $link = null, $type = 'system') {
        try {
            $db = new Database(); $conn = $db->getConnection();
            $st = $conn->prepare('INSERT INTO notifications (user_id, title, message, link, type, is_read, created_at) VALUES (?,?,?,?,?,0, NOW())');
            $st->execute([(int)$userId, $title, $message, $link, $type]);
        } catch (\Throwable $e) { /* ignore */ }
        try {
            $db = new Database(); $conn = $db->getConnection();
            $u = $conn->prepare('SELECT email FROM users WHERE id = ?');
            $u->execute([(int)$userId]);
            $row = $u->fetch(PDO::FETCH_ASSOC);
            if ($row && !empty($row['email'])) {
                $mailer = new Mailer();
                $url = $link ? (APP_URL . $link) : APP_URL;
                $body = '<p>' . htmlspecialchars($message) . '</p>'
                      . '<p><a href="' . htmlspecialchars($url) . '">Xem chi tiết</a></p>';
                $mailer->send($row['email'], $title, $body);
            }
        } catch (\Throwable $e) { /* ignore */ }
    }

    private function findAppointment($appointmentId) {
        $db = new Database(); $conn = $db->getConnection();
        $st = $conn->prepare('SELECT * FROM appointments WHERE id = ?');
        $st->execute([(int)$appointmentId]);
        return $st->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function ensureHeader($appointmentId) {
        $apt = $this->findAppointment($appointmentId);
        if (!$apt) return [null, null];
        $db = new Database(); $conn = $db->getConnection();
        // Try existing header by appointment
        $st = $conn->prepare('SELECT * FROM prescriptions WHERE appointment_id = ? ORDER BY id DESC LIMIT 1');
        $st->execute([(int)$appointmentId]);
        $header = $st->fetch(PDO::FETCH_ASSOC);
        if ($header) return [$header, $apt];
        // Create new
        $code = 'RX' . date('YmdHis') . rand(100,999);
        $ins = $conn->prepare('INSERT INTO prescriptions (prescription_code, appointment_id, package_appointment_id, doctor_id, patient_id, status, total_items) VALUES (?,?,?,?,?,"draft",0)');
        $ins->execute([
            $code,
            (int)$appointmentId,
            !empty($apt['package_appointment_id']) ? (int)$apt['package_appointment_id'] : null,
            !empty($apt['doctor_id']) ? (int)$apt['doctor_id'] : null,
            !empty($apt['patient_id']) ? (int)$apt['patient_id'] : null,
        ]);
        $id = (int)$conn->lastInsertId();
        $st2 = $conn->prepare('SELECT * FROM prescriptions WHERE id = ?');
        $st2->execute([$id]);
        return [$st2->fetch(PDO::FETCH_ASSOC), $apt];
    }

    // POST: tạo/cập nhật đơn + các dòng thuốc cho 1 appointment
    public function saveForAppointment($appointmentId) {
        Auth::requireLogin();
        if (!Auth::isDoctor() && !Auth::isAdmin()) { $_SESSION['error'] = 'Không có quyền tạo đơn.'; header('Location: ' . APP_URL . '/appointments/' . $appointmentId); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ' . APP_URL . '/appointments/' . $appointmentId); return; }

        list($rx, $apt) = $this->ensureHeader($appointmentId);
        if (!$rx) { $_SESSION['error'] = 'Không tạo được header đơn thuốc.'; header('Location: ' . APP_URL . '/appointments/' . $appointmentId); return; }

        $db = new Database(); $conn = $db->getConnection();

        // Lấy medical_record_id nếu có (liên kết với appointment), nếu không thì NULL
        $medicalRecordId = null;
        try {
            $stmMr = $conn->prepare('SELECT id FROM medical_records WHERE appointment_id = ? ORDER BY id DESC LIMIT 1');
            $stmMr->execute([(int)$appointmentId]);
            $mr = $stmMr->fetch(PDO::FETCH_ASSOC);
            if ($mr && !empty($mr['id'])) { $medicalRecordId = (int)$mr['id']; }
        } catch (\Throwable $e) { $medicalRecordId = null; }

        // Lấy dữ liệu từ form (mảng)
        $medicine_id = $_POST['medicine_id'] ?? [];
        $quantity = $_POST['quantity'] ?? [];
        $dosage = $_POST['dosage'] ?? [];
        $frequency = $_POST['frequency'] ?? [];
        $duration = $_POST['duration'] ?? [];
        $instructions = $_POST['instructions'] ?? [];
        $route = $_POST['route'] ?? [];
        $start_date = $_POST['start_date'] ?? [];
        $end_date = $_POST['end_date'] ?? [];
        $refills_allowed = $_POST['refills_allowed'] ?? [];
        $refills_remaining = $_POST['refills_remaining'] ?? [];

        $n = max(count($medicine_id), count($quantity), count($dosage));

        // Validate có ít nhất 1 dòng hợp lệ (medicine_id và quantity > 0)
        $validCount = 0;
        for ($i=0; $i<$n; $i++) {
            $mid = (int)($medicine_id[$i] ?? 0);
            $qty = (int)($quantity[$i] ?? 0);
            if ($mid > 0 && $qty > 0) { $validCount++; }
        }
        if ($validCount === 0) {
            $_SESSION['error'] = 'Vui lòng chọn ít nhất 1 thuốc từ danh mục (có gợi ý) và số lượng > 0.';
            header('Location: ' . APP_URL . '/appointments/' . $appointmentId);
            return;
        }

        // Transaction: xóa cũ rồi chèn mới
        $conn->beginTransaction();
        try {
            $conn->prepare('DELETE FROM prescription_items WHERE prescription_id = ?')->execute([(int)$rx['id']]);

            $ins = $conn->prepare('INSERT INTO prescription_items (prescription_id, prescription_code, medical_record_id, medicine_id, quantity, dosage, frequency, duration, instructions, route, start_date, end_date, refills_allowed, refills_remaining, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,"active")');
            for ($i=0; $i<$n; $i++) {
                $mid = (int)($medicine_id[$i] ?? 0);
                $qty = (int)($quantity[$i] ?? 0);
                if ($mid <= 0 || $qty <= 0) continue; // bỏ dòng không hợp lệ
                $ins->execute([
                    (int)$rx['id'],
                    $rx['prescription_code'],
                    $medicalRecordId,
                    $mid,
                    $qty,
                    (string)($dosage[$i] ?? ''),
                    (string)($frequency[$i] ?? ''),
                    (string)($duration[$i] ?? ''),
                    (string)($instructions[$i] ?? ''),
                    (string)($route[$i] ?? ''),
                    !empty($start_date[$i]) ? $start_date[$i] : null,
                    !empty($end_date[$i]) ? $end_date[$i] : null,
                    isset($refills_allowed[$i]) ? (int)$refills_allowed[$i] : 0,
                    isset($refills_remaining[$i]) ? (int)$refills_remaining[$i] : 0,
                ]);
            }
            // Cập nhật tổng số item
            $conn->prepare('UPDATE prescriptions SET total_items = (SELECT COUNT(*) FROM prescription_items WHERE prescription_id = ?) WHERE id = ?')->execute([(int)$rx['id'], (int)$rx['id']]);

            // Nếu bác sĩ chọn nộp sau khi lưu
            if (!empty($_POST['submit_after'])) {
                $conn->prepare('UPDATE prescriptions SET status = "submitted" WHERE id = ?')->execute([(int)$rx['id']]);
                $_SESSION['success'] = 'Đã lưu và nộp đơn thuốc.';
            } else {
                $_SESSION['success'] = 'Đã lưu đơn thuốc (nháp).';
            }
            $conn->commit();
        } catch (\Throwable $e) {
            $conn->rollBack();
            $_SESSION['error'] = 'Lỗi lưu đơn thuốc: ' . $e->getMessage();
            header('Location: ' . APP_URL . '/appointments/' . $appointmentId);
            return;
        }
        // Nếu bác sĩ chọn nộp sau khi lưu
        header('Location: ' . APP_URL . '/appointments/' . $appointmentId);
    }

    public function submit($prescriptionId) {
        Auth::requireLogin();
        if (!Auth::isDoctor() && !Auth::isAdmin()) { $_SESSION['error']='Không có quyền.'; header('Location: ' . APP_URL . '/appointments'); return; }
        $db = new Database(); $conn = $db->getConnection();
        $conn->prepare('UPDATE prescriptions SET status = "submitted" WHERE id = ?')->execute([(int)$prescriptionId]);
        $_SESSION['success'] = 'Đã nộp đơn thuốc.';
        header('Location: ' . APP_URL . '/appointments');
    }

    public function approve($prescriptionId) {
        Auth::requireLogin();
        if (!Auth::isAdmin() && !Auth::isDoctor()) { $_SESSION['error']='Không có quyền.'; header('Location: ' . APP_URL . '/appointments'); return; }
        $db = new Database(); $conn = $db->getConnection();
        $conn->prepare('UPDATE prescriptions SET status = "approved" WHERE id = ?')->execute([(int)$prescriptionId]);
        // Notify patient
        $st = $conn->prepare('SELECT patient_id FROM prescriptions WHERE id = ?');
        $st->execute([(int)$prescriptionId]);
        $pid = $st->fetchColumn();
        if ($pid) {
            $u = $conn->prepare('SELECT user_id FROM patients WHERE id = ?');
            $u->execute([(int)$pid]);
            $uid = $u->fetchColumn();
            if ($uid) $this->notify((int)$uid, 'Đơn thuốc đã sẵn sàng', 'Đơn thuốc của bạn đã được duyệt.', '/prescriptions/' . $prescriptionId . '/export-pdf', 'system');
        }
        $_SESSION['success'] = 'Đã duyệt đơn thuốc.';
        header('Location: ' . APP_URL . '/appointments');
    }

    public function dispense($prescriptionId) {
        Auth::requireLogin();
        if (!Auth::isAdmin()) { $_SESSION['error']='Chỉ admin/pharmacy được phát thuốc.'; header('Location: ' . APP_URL . '/appointments'); return; }
        $db = new Database(); $conn = $db->getConnection();
        $conn->prepare('UPDATE prescriptions SET status = "dispensed" WHERE id = ?')->execute([(int)$prescriptionId]);
        $_SESSION['success'] = 'Đã đánh dấu phát thuốc.';
        header('Location: ' . APP_URL . '/appointments');
    }

    public function exportPdf($prescriptionId) {
        Auth::requireLogin();
        $db = new Database(); $conn = $db->getConnection();
        $st = $conn->prepare('SELECT * FROM prescriptions WHERE id = ?');
        $st->execute([(int)$prescriptionId]);
        $rx = $st->fetch(PDO::FETCH_ASSOC);
        if (!$rx) { $_SESSION['error']='Không tìm thấy đơn thuốc.'; header('Location: ' . APP_URL . '/appointments'); return; }

        // Load items
        $it = $conn->prepare('SELECT pi.*, m.name AS med_name, m.dosage_form AS med_form, m.strength AS med_strength FROM prescription_items pi LEFT JOIN medicines m ON m.id = pi.medicine_id WHERE pi.prescription_id = ? ORDER BY pi.id');
        $it->execute([(int)$prescriptionId]);
        $items = $it->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Load patient/doctor/appointment and diagnosis
        $patientName = $patientCode = $patientPhone = $patientEmail = $doctorName = $doctorSpec = $appointmentDate = $appointmentCode = $serviceName = $packageName = '';
        try {
            if (!empty($rx['appointment_id'])) {
                if (!class_exists('Appointment')) { require_once APP_PATH . '/Models/Appointment.php'; }
                $aptModel = new \Appointment();
                $apt = $aptModel->findById((int)$rx['appointment_id']);
                if ($apt) {
                    $patientName = $apt['patient_name'] ?? '';
                    $patientCode = $apt['patient_code'] ?? '';
                    $patientPhone = $apt['patient_phone'] ?? '';
                    $patientEmail = $apt['patient_email'] ?? '';
                    $doctorName = $apt['doctor_name'] ?? '';
                    $doctorSpec = $apt['specialization'] ?? '';
                    $appointmentDate = !empty($apt['appointment_date']) ? date('d/m/Y', strtotime($apt['appointment_date'])) : '';
                    $appointmentCode = $apt['appointment_code'] ?? '';
                    $serviceName = $apt['reason'] ?? '';
                    $packageName = $apt['package_name'] ?? '';
                }
            }
            // Fallbacks if appointment join missing or some fields empty
            if (empty($patientName) && !empty($rx['patient_id'])) {
                $sp = $conn->prepare('SELECT p.patient_code, u.full_name, u.phone, u.email FROM patients p LEFT JOIN users u ON u.id = p.user_id WHERE p.id = ?');
                $sp->execute([(int)$rx['patient_id']]);
                if ($pr = $sp->fetch(PDO::FETCH_ASSOC)) {
                    $patientName = $patientName ?: ($pr['full_name'] ?? '');
                    $patientCode = $patientCode ?: ($pr['patient_code'] ?? '');
                    $patientPhone = $patientPhone ?: ($pr['phone'] ?? '');
                    $patientEmail = $patientEmail ?: ($pr['email'] ?? '');
                }
            }
            if (empty($doctorName) && !empty($rx['doctor_id'])) {
                $sd = $conn->prepare('SELECT d.full_name, s.name AS spec, u.phone, u.email FROM doctors d LEFT JOIN specializations s ON s.id = d.specialization_id LEFT JOIN users u ON u.id = d.user_id WHERE d.id = ?');
                $sd->execute([(int)$rx['doctor_id']]);
                if ($dr = $sd->fetch(PDO::FETCH_ASSOC)) {
                    $doctorName = $doctorName ?: ($dr['full_name'] ?? '');
                    $doctorSpec = $doctorSpec ?: ($dr['spec'] ?? '');
                }
            }
            if (empty($packageName) && !empty($rx['package_appointment_id'])) {
                $spkg = $conn->prepare('SELECT hp.name AS package_name FROM package_appointments pa LEFT JOIN health_packages hp ON hp.id = pa.package_id WHERE pa.id = ?');
                $spkg->execute([(int)$rx['package_appointment_id']]);
                $rowPkg = $spkg->fetch(PDO::FETCH_ASSOC);
                $packageName = $packageName ?: ($rowPkg['package_name'] ?? '');
            }
            // If still missing patient fields, try from package_appointments
            if ((empty($patientName) || empty($patientCode)) && !empty($rx['package_appointment_id'])) {
                $spa = $conn->prepare('SELECT p.patient_code, u.full_name, u.phone, u.email
                                        FROM package_appointments pa
                                        LEFT JOIN patients p ON p.id = pa.patient_id
                                        LEFT JOIN users u ON u.id = p.user_id
                                        WHERE pa.id = ?');
                $spa->execute([(int)$rx['package_appointment_id']]);
                if ($paRow = $spa->fetch(PDO::FETCH_ASSOC)) {
                    $patientName = $patientName ?: ($paRow['full_name'] ?? '');
                    $patientCode = $patientCode ?: ($paRow['patient_code'] ?? '');
                    $patientPhone = $patientPhone ?: ($paRow['phone'] ?? '');
                    $patientEmail = $patientEmail ?: ($paRow['email'] ?? '');
                }
            }
        } catch (\Throwable $e) { /* ignore */ }
        $diagnosis = '';
        try {
            if (!empty($rx['appointment_id'])) {
                $stmDx = $conn->prepare('SELECT primary_section FROM diagnoses WHERE appointment_id = ? ORDER BY id DESC LIMIT 1');
                $stmDx->execute([(int)$rx['appointment_id']]);
                $rowDx = $stmDx->fetch(PDO::FETCH_ASSOC);
                $diagnosis = $rowDx['primary_section'] ?? '';
            }
        } catch (\Throwable $e) { /* ignore */ }

        $styles = '<style>
            body{ font-family: DejaVu Sans, sans-serif; font-size:12px; }
            .hdr{ text-align:center; margin-bottom:8px; }
            .meta{ margin-bottom:10px; }
            .meta td{ padding:4px 6px; }
            table{ border-collapse:collapse; width:100%; }
            th, td{ border:1px solid #444; padding:6px; }
            th{ background:#f2f2f2; }
        </style>';
        $html = $styles;
        $html .= '<div class="hdr"><h2>Đơn thuốc</h2></div>';
        $html .= '<table class="meta" width="100%">'
              .  '<tr><td><strong>Bệnh nhân:</strong> ' . htmlspecialchars($patientName) . ' (' . htmlspecialchars($patientCode) . ')</td><td><strong>Bác sĩ:</strong> ' . htmlspecialchars($doctorName) . (!empty($doctorSpec)?(' — ' . htmlspecialchars($doctorSpec)):'') . '</td></tr>'
              .  '<tr><td><strong>SĐT:</strong> ' . htmlspecialchars($patientPhone) . '</td><td><strong>Email:</strong> ' . htmlspecialchars($patientEmail) . '</td></tr>'
              .  '<tr><td><strong>Ngày khám:</strong> ' . htmlspecialchars($appointmentDate) . ' — <strong>Mã lịch:</strong> ' . htmlspecialchars($appointmentCode) . '</td><td><strong>Dịch vụ khám:</strong> ' . htmlspecialchars($serviceName) . (!empty($packageName)?(' — <strong>Thuộc gói:</strong> ' . htmlspecialchars($packageName)):'') . '</td></tr>'
              .  '<tr><td><strong>Chẩn đoán:</strong> ' . htmlspecialchars($diagnosis) . '</td><td><strong>Mã đơn:</strong> ' . htmlspecialchars($rx['prescription_code'] ?? ('RX-' . $rx['id'])) . ' — <strong>Trạng thái:</strong> ' . htmlspecialchars($rx['status'] ?? '') . '</td></tr>'
              .  '</table>';

        $html .= '<table>'
              .  '<tr><th>Thuốc</th><th>Liều</th><th>Tần suất</th><th>Thời gian</th><th>Bắt đầu</th><th>Kết thúc</th><th>Số lượng</th><th>Đường dùng</th><th>Dặn dò</th></tr>';
        foreach ($items as $row) {
            $drug = ($row['med_name'] ?? ('ID:' . (string)$row['medicine_id']))
                  . (!empty($row['med_strength']) ? (' ' . $row['med_strength']) : '')
                  . (!empty($row['med_form']) ? (' (' . $row['med_form'] . ')') : '');
            $html .= '<tr>'
                  .  '<td>' . htmlspecialchars($drug) . '</td>'
                  .  '<td>' . htmlspecialchars((string)($row['dosage'])) . '</td>'
                  .  '<td>' . htmlspecialchars((string)($row['frequency'])) . '</td>'
                  .  '<td>' . htmlspecialchars((string)($row['duration'])) . '</td>'
                  .  '<td>' . (!empty($row['start_date']) ? htmlspecialchars(date('d/m/Y', strtotime($row['start_date']))) : '') . '</td>'
                  .  '<td>' . (!empty($row['end_date']) ? htmlspecialchars(date('d/m/Y', strtotime($row['end_date']))) : '') . '</td>'
                  .  '<td>' . htmlspecialchars((string)($row['quantity'])) . '</td>'
                  .  '<td>' . htmlspecialchars((string)($row['route'])) . '</td>'
                  .  '<td>' . htmlspecialchars((string)($row['instructions'])) . '</td>'
                  .  '</tr>';
        }
        $html .= '</table>';
        $html .= '<div style="margin-top:30px; display:flex; justify-content:space-between;">'
              .  '<div><strong>Ngày in:</strong> ' . date('d/m/Y H:i') . '</div>'
              .  '<div style="text-align:right; min-width:220px;">Bác sĩ kê đơn<br><br><br>....................................</div>'
              .  '</div>';

        // Dompdf if available
        $ok = false; $pdfPath = null;
        try {
            if (class_exists('Dompdf\\Dompdf')) {
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $output = $dompdf->output();
                $dir = APP_PATH . '/storage/reports'; if (!is_dir($dir)) @mkdir($dir, 0777, true);
                $pdfPath = $dir . '/prescription_' . $prescriptionId . '.pdf';
                file_put_contents($pdfPath, $output);
                $ok = true;
            }
        } catch (\Throwable $e) { $ok = false; }

        if ($ok) {
            $conn->prepare('UPDATE prescriptions SET pdf_path = :p WHERE id = :id')->execute([':p'=>$pdfPath, ':id'=>(int)$prescriptionId]);
            $_SESSION['success'] = 'Đã tạo PDF đơn thuốc.';
            header('Location: ' . APP_URL . '/prescriptions/' . $prescriptionId . '/download-pdf');
        } else {
            header('Content-Type: text/html; charset=utf-8'); echo $html; return;
        }
    }

    public function downloadPdf($prescriptionId) {
        Auth::requireLogin();
        $db = new Database(); $conn = $db->getConnection();
        $st = $conn->prepare('SELECT p.*, pa.user_id as patient_user_id FROM prescriptions p LEFT JOIN patients pa ON pa.id = p.patient_id WHERE p.id = ?');
        $st->execute([(int)$prescriptionId]);
        $rx = $st->fetch(PDO::FETCH_ASSOC);
        if (!$rx) { http_response_code(404); echo 'Không tìm thấy đơn.'; return; }
        if (!Auth::isAdmin() && !Auth::isDoctor()) {
            if ((int)$rx['patient_user_id'] !== (int)Auth::id()) { http_response_code(403); echo 'Không có quyền'; return; }
        }
        $path = $rx['pdf_path'] ?? null;
        if (!$path || !is_file($path)) { $_SESSION['error']='Chưa có PDF'; header('Location: ' . APP_URL . '/appointments'); return; }
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="prescription_' . $prescriptionId . '.pdf"');
        header('Content-Length: ' . filesize($path));
        readfile($path); exit;
    }

    // Export consolidated prescriptions for a package appointment
    public function exportPackagePdf($packageAppointmentId) {
        Auth::requireLogin();
        $db = new Database(); $conn = $db->getConnection();
        // Get package, patient
        $pkg = null;
        try {
            $stp = $conn->prepare('SELECT pa.*, p.patient_code, u.full_name AS patient_name, u.phone AS patient_phone, u.email AS patient_email, hp.name AS package_name
                                FROM package_appointments pa
                                LEFT JOIN patients p ON p.id = pa.patient_id
                                LEFT JOIN users u ON u.id = p.user_id
                                LEFT JOIN health_packages hp ON hp.id = pa.package_id
                                WHERE pa.id = ?');
            $stp->execute([(int)$packageAppointmentId]);
            $pkg = $stp->fetch(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) { $pkg = null; }
        if (!$pkg) { $_SESSION['error'] = 'Không tìm thấy gói khám'; header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId); return; }

        // Load all prescriptions within this package
        $st = $conn->prepare('SELECT pr.*, d.full_name AS doctor_name FROM prescriptions pr LEFT JOIN doctors d ON d.id = pr.doctor_id WHERE pr.package_appointment_id = ? AND pr.total_items > 0 ORDER BY pr.id');
        $st->execute([(int)$packageAppointmentId]);
        $pres = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
        if (!$pres) { $_SESSION['warning'] = 'Chưa có đơn thuốc nào trong gói.'; header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId); return; }

        // Build HTML with sections per prescription
        $styles = '<style>
            body{ font-family: DejaVu Sans, sans-serif; font-size:12px; }
            h2{ margin-bottom:6px; }
            table{ border-collapse:collapse; width:100%; }
            th, td{ border:1px solid #444; padding:6px; }
            th{ background:#f2f2f2; }
            .meta td{ padding:4px 6px; border:none; }
        </style>';
        $html = $styles;
        $html .= '<h2 style="text-align:center">Đơn thuốc tổng hợp gói khám #' . htmlspecialchars((string)$packageAppointmentId) . '</h2>';
        $html .= '<table class="meta" width="100%">'
              .  '<tr><td><strong>Bệnh nhân:</strong> ' . htmlspecialchars($pkg['patient_name'] ?? '') . ' (' . htmlspecialchars($pkg['patient_code'] ?? '') . ')</td><td><strong>Ngày khám dự kiến:</strong> ' . (!empty($pkg['appointment_date']) ? htmlspecialchars(date('d/m/Y', strtotime($pkg['appointment_date']))) : '') . '</td></tr>'
              .  '<tr><td><strong>SĐT:</strong> ' . htmlspecialchars($pkg['patient_phone'] ?? '') . '</td><td><strong>Email:</strong> ' . htmlspecialchars($pkg['patient_email'] ?? '') . '</td></tr>'
              .  '<tr><td colspan="2"><strong>Gói khám:</strong> ' . htmlspecialchars($pkg['package_name'] ?? '') . '</td></tr>'
              .  '</table>';

        foreach ($pres as $rx) {
            // Items join medicines for display
            $it = $conn->prepare('SELECT pi.*, m.name AS med_name, m.dosage_form AS med_form, m.strength AS med_strength FROM prescription_items pi LEFT JOIN medicines m ON m.id = pi.medicine_id WHERE pi.prescription_id = ? ORDER BY pi.id');
            $it->execute([(int)$rx['id']]);
            $items = $it->fetchAll(PDO::FETCH_ASSOC) ?: [];
            if (!$items) continue;
            $html .= '<h3 style="margin-top:18px">Bác sĩ: ' . htmlspecialchars($rx['doctor_name'] ?? 'N/A') . ' — Mã đơn: ' . htmlspecialchars($rx['prescription_code'] ?? ('RX-' . $rx['id'])) . ' — Trạng thái: ' . htmlspecialchars($rx['status'] ?? '') . '</h3>';
            $html .= '<table>'
                 .  '<tr><th>Thuốc</th><th>Liều</th><th>Tần suất</th><th>Thời gian</th><th>Bắt đầu</th><th>Kết thúc</th><th>Số lượng</th><th>Đường dùng</th><th>Dặn dò</th></tr>';
            foreach ($items as $row) {
                $drug = ($row['med_name'] ?? ('ID:' . (string)$row['medicine_id']))
                      . (!empty($row['med_strength']) ? (' ' . $row['med_strength']) : '')
                      . (!empty($row['med_form']) ? (' (' . $row['med_form'] . ')') : '');
                $html .= '<tr>'
                      .  '<td>' . htmlspecialchars($drug) . '</td>'
                      .  '<td>' . htmlspecialchars((string)($row['dosage'])) . '</td>'
                      .  '<td>' . htmlspecialchars((string)($row['frequency'])) . '</td>'
                      .  '<td>' . htmlspecialchars((string)($row['duration'])) . '</td>'
                      .  '<td>' . (!empty($row['start_date']) ? htmlspecialchars(date('d/m/Y', strtotime($row['start_date']))) : '') . '</td>'
                      .  '<td>' . (!empty($row['end_date']) ? htmlspecialchars(date('d/m/Y', strtotime($row['end_date']))) : '') . '</td>'
                      .  '<td>' . htmlspecialchars((string)($row['quantity'])) . '</td>'
                      .  '<td>' . htmlspecialchars((string)($row['route'])) . '</td>'
                      .  '<td>' . htmlspecialchars((string)($row['instructions'])) . '</td>'
                      .  '</tr>';
            }
            $html .= '</table>';
        }

        // Generate PDF
        $ok = false; $pdfPath = null;
        try {
            if (class_exists('Dompdf\\Dompdf')) {
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $output = $dompdf->output();
                $dir = APP_PATH . '/storage/reports'; if (!is_dir($dir)) @mkdir($dir, 0777, true);
                $pdfPath = $dir . '/package_prescriptions_' . $packageAppointmentId . '.pdf';
                file_put_contents($pdfPath, $output);
                $ok = true;
            }
        } catch (\Throwable $e) { $ok = false; }

        if ($ok) {
            // Stream download
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="package_prescriptions_' . $packageAppointmentId . '.pdf"');
            header('Content-Length: ' . filesize($pdfPath));
            readfile($pdfPath); exit;
        } else {
            header('Content-Type: text/html; charset=utf-8'); echo $html; return;
        }
    }
}
