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
        }

        // Load prescription for this package (latest, prefer approved)
        $prescription = null; $prescriptionItems = [];
        try {
            $stp = $conn->prepare("SELECT * FROM prescriptions WHERE package_appointment_id = ? ORDER BY (status='approved') DESC, id DESC LIMIT 1");
            $stp->execute([(int)$packageAppointmentId]);
            $prescription = $stp->fetch(PDO::FETCH_ASSOC) ?: null;
            if ($prescription) {
                $sti = $conn->prepare('SELECT * FROM prescription_items WHERE prescription_id = ? ORDER BY id');
                $sti->execute([(int)$prescription['id']]);
                $prescriptionItems = $sti->fetchAll(PDO::FETCH_ASSOC) ?: [];
            }
        } catch (\Throwable $e) { /* ignore */ }

        require_once APP_PATH . '/Views/results/package.php';
    }
}
