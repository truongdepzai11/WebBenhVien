<?php

require_once __DIR__ . '/../Models/PackageAppointment.php';
require_once __DIR__ . '/../Models/Appointment.php';
require_once __DIR__ . '/../Models/HealthPackage.php';
require_once __DIR__ . '/../Models/Patient.php';
require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../../config/database.php';

class ResultsController {
    private $packageAppointmentModel;
    private $appointmentModel;
    private $packageModel;
    private $patientModel;

    public function __construct() {
        $this->packageAppointmentModel = new PackageAppointment();
        $this->appointmentModel = new Appointment();
        $this->packageModel = new HealthPackage();
        $this->patientModel = new Patient();
    }

    // Danh sách kết quả của tôi (bệnh nhân)
    public function index() {
        Auth::requireLogin();
        if (!Auth::isPatient()) {
            header('Location: ' . APP_URL . '/');
            exit;
        }

        $patient = $this->patientModel->findByUserId(Auth::id());
        if (!$patient) { header('Location: ' . APP_URL . '/'); exit; }

        // Các gói đã có kết quả (ít nhất 1 dịch vụ approved) hoặc final_status approved
        $db = new Database(); $conn = $db->getConnection();
        $stmt = $conn->prepare('SELECT pa.*, hp.name AS package_name
                                FROM package_appointments pa
                                JOIN health_packages hp ON hp.id = pa.package_id
                                WHERE pa.patient_id = ?
                                ORDER BY pa.created_at DESC');
        $stmt->execute([(int)$patient['id']]);
        $packages = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Đếm số dịch vụ approved cho mỗi gói
        foreach ($packages as &$pa) {
            $summary = $this->appointmentModel->getSummaryByPackageAppointmentId($pa['id']);
            $approvedCount = 0; $total = 0;
            if ($summary) {
                $st = $conn->prepare('SELECT result_state FROM appointment_package_services WHERE appointment_id = ?');
                $st->execute([(int)$summary['id']]);
                $rows = $st->fetchAll(PDO::FETCH_COLUMN) ?: [];
                $total = count($rows);
                foreach ($rows as $s) { if ($s === 'approved') $approvedCount++; }
            }

            $pa['approved_count'] = $approvedCount;
            $pa['total_services'] = $total;
        }
        unset($pa);

        // Kết quả khám thường (đã hoàn thành, có chẩn đoán/đơn thuốc)
        $regularResults = [];
        try {
            $stRegular = $conn->prepare('SELECT a.id, a.appointment_code, a.appointment_date, a.appointment_time,
                                                du.full_name AS doctor_name
                                         FROM appointments a
                                         LEFT JOIN doctors d ON d.id = a.doctor_id
                                         LEFT JOIN users du ON du.id = d.user_id
                                         WHERE a.patient_id = ?
                                           AND (a.package_appointment_id IS NULL OR a.package_appointment_id = 0)
                                           AND a.status = "completed"
                                         ORDER BY a.appointment_date DESC, a.appointment_time DESC, a.id DESC');
            $stRegular->execute([(int)$patient['id']]);
            $regularRows = $stRegular->fetchAll(PDO::FETCH_ASSOC) ?: [];

            if (!empty($regularRows)) {
                $appointmentIds = array_map('intval', array_column($regularRows, 'id'));
                if (!empty($appointmentIds)) {
                    $placeholders = implode(',', array_fill(0, count($appointmentIds), '?'));

                    // Latest diagnosis per appointment
                    $diagnosisByAppointment = [];
                    $sqlDx = "SELECT d.* FROM diagnoses d
                               JOIN (
                                   SELECT appointment_id, MAX(id) AS max_id
                                   FROM diagnoses
                                   WHERE appointment_id IN ($placeholders)
                                   GROUP BY appointment_id
                               ) t ON t.max_id = d.id";
                    $stDx = $conn->prepare($sqlDx);
                    $stDx->execute($appointmentIds);
                    foreach ($stDx->fetchAll(PDO::FETCH_ASSOC) ?: [] as $rowDx) {
                        $diagnosisByAppointment[(int)$rowDx['appointment_id']] = $rowDx;
                    }

                    // Latest prescription per appointment
                    $prescriptionByAppointment = [];
                    $prescriptionItemsByRxId = [];
                    $sqlRx = "SELECT p.* FROM prescriptions p
                               JOIN (
                                   SELECT appointment_id, MAX(id) AS max_id
                                   FROM prescriptions
                                   WHERE appointment_id IN ($placeholders)
                                   GROUP BY appointment_id
                               ) t ON t.max_id = p.id";
                    $stRx = $conn->prepare($sqlRx);
                    $stRx->execute($appointmentIds);
                    $rxRows = $stRx->fetchAll(PDO::FETCH_ASSOC) ?: [];
                    if (!empty($rxRows)) {
                        $rxIds = [];
                        foreach ($rxRows as $rowRx) {
                            $prescriptionByAppointment[(int)$rowRx['appointment_id']] = $rowRx;
                            if (!empty($rowRx['id'])) {
                                $rxIds[] = (int)$rowRx['id'];
                            }
                        }
                        $rxIds = array_values(array_unique(array_filter($rxIds)));
                        if (!empty($rxIds)) {
                            $phRxItems = implode(',', array_fill(0, count($rxIds), '?'));
                            $sqlRxItems = "SELECT pi.*, COALESCE(pi.drug_name, m.name) AS drug_label, m.dosage_form, m.strength
                                           FROM prescription_items pi
                                           LEFT JOIN medicines m ON m.id = pi.medicine_id
                                           WHERE pi.prescription_id IN ($phRxItems)
                                           ORDER BY pi.id";
                            $stRxItems = $conn->prepare($sqlRxItems);
                            $stRxItems->execute($rxIds);
                            foreach ($stRxItems->fetchAll(PDO::FETCH_ASSOC) ?: [] as $piRow) {
                                $pid = (int)$piRow['prescription_id'];
                                if (!isset($prescriptionItemsByRxId[$pid])) { $prescriptionItemsByRxId[$pid] = []; }
                                $prescriptionItemsByRxId[$pid][] = $piRow;
                            }
                        }
                    }

                    // Regular result headers and items
                    $resultByAppointment = [];
                    $itemsByResultId = [];
                    $sqlResult = "SELECT * FROM appointment_results WHERE appointment_id IN ($placeholders)";
                    $stResult = $conn->prepare($sqlResult);
                    $stResult->execute($appointmentIds);
                    $resultRows = $stResult->fetchAll(PDO::FETCH_ASSOC) ?: [];
                    if (!empty($resultRows)) {
                        $resultIds = [];
                        foreach ($resultRows as $rowRes) {
                            $aptId = (int)$rowRes['appointment_id'];
                            $resultByAppointment[$aptId] = $rowRes;
                            $resultIds[] = (int)$rowRes['id'];
                        }
                        $resultIds = array_values(array_unique(array_filter($resultIds)));
                        if (!empty($resultIds)) {
                            $phResultItems = implode(',', array_fill(0, count($resultIds), '?'));
                            $sqlItems = "SELECT * FROM appointment_result_items WHERE result_id IN ($phResultItems) ORDER BY id";
                            $stItems = $conn->prepare($sqlItems);
                            $stItems->execute($resultIds);
                            foreach ($stItems->fetchAll(PDO::FETCH_ASSOC) ?: [] as $itemRow) {
                                $rid = (int)$itemRow['result_id'];
                                if (!isset($itemsByResultId[$rid])) { $itemsByResultId[$rid] = []; }
                                $itemsByResultId[$rid][] = $itemRow;
                            }
                        }
                    }

                    foreach ($regularRows as $row) {
                        $aptId = (int)$row['id'];
                        $diag = $diagnosisByAppointment[$aptId] ?? null;
                        $rx = $prescriptionByAppointment[$aptId] ?? null;
                        if ($rx && ($rx['status'] ?? '') === 'draft') { $rx = null; }

                        $resultHeader = $resultByAppointment[$aptId] ?? null;
                        $resultItems = [];
                        $resultStatus = null;
                        $resultNote = null;
                        if ($resultHeader && ($resultHeader['status'] ?? '') === 'submitted') {
                            $rid = (int)$resultHeader['id'];
                            $resultItems = $itemsByResultId[$rid] ?? [];
                            if (!empty($resultItems)) {
                                $resultStatus = 'submitted';
                                $resultNote = $resultHeader['review_note'] ?? null;
                            }
                        }

                        if (!$diag && !$rx && !$resultStatus) {
                            continue; // Bỏ qua lịch chưa có dữ liệu cho bệnh nhân
                        }

                        $rxItemsPayload = [];
                        if ($rx && !empty($rx['id'])) {
                            $rxItemsPayload = $prescriptionItemsByRxId[(int)$rx['id']] ?? [];
                        }

                        $regularResults[] = [
                            'appointment_id' => $aptId,
                            'appointment_code' => $row['appointment_code'],
                            'appointment_date' => $row['appointment_date'],
                            'appointment_time' => $row['appointment_time'],
                            'doctor_name' => $row['doctor_name'],
                            'diagnosis_status' => $diag['status'] ?? null,
                            'diagnosis_primary' => $diag['primary_icd10'] ?? null,
                            'prescription_status' => $rx['status'] ?? null,
                            'prescription_code' => $rx['prescription_code'] ?? null,
                            'prescription_items' => $rxItemsPayload,
                            'result_status' => $resultStatus,
                            'result_note' => $resultNote,
                            'result_items' => $resultItems,
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            $regularResults = [];
        }

        require_once APP_PATH . '/Views/results/index.php';
    }

    // Chi tiết kết quả 1 gói cho bệnh nhân
    public function package($packageAppointmentId) {
        Auth::requireLogin();
        if (!Auth::isPatient()) { header('Location: ' . APP_URL . '/'); exit; }

        $patient = $this->patientModel->findByUserId(Auth::id());
        $pa = $this->packageAppointmentModel->findById($packageAppointmentId);
        if (!$pa || (int)$pa['patient_id'] !== (int)$patient['id']) {
            header('Location: ' . APP_URL . '/my-results'); exit;
        }

        $summary = $this->appointmentModel->getSummaryByPackageAppointmentId($packageAppointmentId);
        $db = new Database(); $conn = $db->getConnection();

        $apsRows = [];
        $metricsByServiceId = [];
        if ($summary) {
            // Lấy dịch vụ và trạng thái (chỉ hiển thị những cái approved, hoặc tất cả nếu final_status=approved)
            $st = $conn->prepare('SELECT aps.*, ps.service_name, ps.service_category
                                   FROM appointment_package_services aps
                                   JOIN package_services ps ON ps.id = aps.service_id
                                   WHERE aps.appointment_id = ?
                                   ORDER BY ps.display_order, aps.id');
            $st->execute([(int)$summary['id']]);
            $apsRows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];

            // Gắn appointment_id của lịch con cho từng dịch vụ (service_appointment_id)
            // để dùng lấy chẩn đoán/đơn thuốc chính xác theo từng lịch dịch vụ
            if (!empty($apsRows)) {
                foreach ($apsRows as &$row) {
                    try {
                        $q = $conn->prepare('SELECT id FROM appointments 
                                             WHERE package_appointment_id = ? 
                                               AND doctor_id IS NOT NULL 
                                               AND LOWER(TRIM(reason)) = LOWER(TRIM(?))
                                             ORDER BY appointment_date, appointment_time LIMIT 1');
                        $q->execute([(int)$packageAppointmentId, (string)($row['service_name'] ?? '')]);
                        $row['service_appointment_id'] = (int)($q->fetchColumn() ?: 0);
                    } catch (\Throwable $e) {
                        $row['service_appointment_id'] = 0;
                    }
                }
                unset($row);
            }

            // Load metrics
            $st2 = $conn->prepare('SELECT service_id, metric_name, result_value, result_status, reference_range, notes
                                    FROM package_test_results
                                    WHERE appointment_id = ?
                                    ORDER BY service_id, id');
            $st2->execute([(int)$summary['id']]);
            foreach ($st2->fetchAll(PDO::FETCH_ASSOC) ?: [] as $m) {
                $sid = (int)$m['service_id'];
                if (!isset($metricsByServiceId[$sid])) $metricsByServiceId[$sid] = [];
                $metricsByServiceId[$sid][] = $m;
            }

            // Collect appointment_ids of services (child appointments)
            $serviceAppointmentIds = [];
            foreach ($apsRows as $r) {
                $sid = (int)($r['service_appointment_id'] ?? 0);
                if ($sid > 0) { $serviceAppointmentIds[] = $sid; }
            }
            $serviceAppointmentIds = array_values(array_unique(array_filter($serviceAppointmentIds)));

            // Load latest approved diagnosis per service appointment
            $diagnosisByAppointmentId = [];
            if (!empty($serviceAppointmentIds)) {
                $in = implode(',', array_fill(0, count($serviceAppointmentIds), '?'));
                $sqlDx = "SELECT d.* FROM diagnoses d
                          JOIN (
                             SELECT appointment_id, MAX(id) AS max_id
                             FROM diagnoses
                             WHERE appointment_id IN ($in) AND status='approved'
                             GROUP BY appointment_id
                          ) t ON t.max_id = d.id";
                $stDx = $conn->prepare($sqlDx);
                $stDx->execute($serviceAppointmentIds);
                foreach ($stDx->fetchAll(PDO::FETCH_ASSOC) ?: [] as $rowDx) {
                    $diagnosisByAppointmentId[(int)$rowDx['appointment_id']] = $rowDx;
                }
            }

            // Load latest non-draft prescription per service appointment + their items
            $prescriptionByAppointmentId = []; $prescriptionItemsByRxId = [];
            if (!empty($serviceAppointmentIds)) {
                $in = implode(',', array_fill(0, count($serviceAppointmentIds), '?'));
                $sqlRx = "SELECT p.* FROM prescriptions p
                          JOIN (
                            SELECT appointment_id, MAX(id) AS max_id
                            FROM prescriptions
                            WHERE appointment_id IN ($in) AND status IN ('approved','dispensed')
                            GROUP BY appointment_id
                          ) t ON t.max_id = p.id";
                $stRx = $conn->prepare($sqlRx);
                $stRx->execute($serviceAppointmentIds);
                $rxRows = $stRx->fetchAll(PDO::FETCH_ASSOC) ?: [];
                if (!empty($rxRows)) {
                    $rxIds = [];
                    foreach ($rxRows as $rx) {
                        $prescriptionByAppointmentId[(int)$rx['appointment_id']] = $rx;
                        $rxIds[] = (int)$rx['id'];
                    }
                    if (!empty($rxIds)) {
                        $in2 = implode(',', array_fill(0, count($rxIds), '?'));
                        $sti = $conn->prepare("SELECT pi.*, COALESCE(pi.drug_name, m.name) AS drug_name
                                               FROM prescription_items pi
                                               LEFT JOIN medicines m ON m.id = pi.medicine_id
                                               WHERE pi.prescription_id IN ($in2)
                                               ORDER BY pi.id");
                        $sti->execute($rxIds);
                        foreach ($sti->fetchAll(PDO::FETCH_ASSOC) ?: [] as $it) {
                            $pid = (int)$it['prescription_id'];
                            if (!isset($prescriptionItemsByRxId[$pid])) $prescriptionItemsByRxId[$pid] = [];
                            $prescriptionItemsByRxId[$pid][] = $it;
                        }
                    }
                }
            }
        } else {
            $diagnosisByAppointmentId = [];
            $prescriptionByAppointmentId = [];
            $prescriptionItemsByRxId = [];
        }

        // Per-service maps prepared above only

        require_once APP_PATH . '/Views/results/package.php';
    }
}
