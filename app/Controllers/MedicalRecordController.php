<?php

require_once __DIR__ . '/../Models/MedicalRecord.php';
require_once __DIR__ . '/../Models/Patient.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class MedicalRecordController {
    private $medicalRecordModel;
    private $patientModel;

    public function __construct() {
        $this->medicalRecordModel = new MedicalRecord();
        $this->patientModel = new Patient();
    }

    // Danh sách hồ sơ bệnh án
    public function index() {
        Auth::requireLogin();

        if (Auth::isPatient()) {
            $patient = $this->patientModel->findByUserId(Auth::id());
            $records = $this->medicalRecordModel->getByPatientId($patient['id']);
        } elseif (Auth::isDoctor()) {
            $doctor = (new Doctor())->findByUserId(Auth::id());
            $records = $this->medicalRecordModel->getByDoctorId($doctor['id']);
        } else {
            $records = $this->medicalRecordModel->getAll();
        }

        require_once APP_PATH . '/Views/medical-records/index.php';
    }

    // Chi tiết hồ sơ bệnh án
    public function show($id) {
        Auth::requireLogin();

        $record = $this->medicalRecordModel->findById($id);
        
        if (!$record) {
            $_SESSION['error'] = 'Không tìm thấy hồ sơ bệnh án!';
            header('Location: ' . APP_URL . '/medical-records');
            exit;
        }

        // Kiểm tra quyền xem
        if (Auth::isPatient()) {
            $patient = $this->patientModel->findByUserId(Auth::id());
            if ($record['patient_id'] != $patient['id']) {
                $_SESSION['error'] = 'Bạn không có quyền xem hồ sơ này!';
                header('Location: ' . APP_URL . '/medical-records');
                exit;
            }
        }

        // Lấy đơn thuốc
        $prescriptions = $this->medicalRecordModel->getPrescriptions($id);

        // Lấy hóa đơn gắn với lịch hẹn (nếu có)
        $invoice = null;
        if (!empty($record['appointment_id'])) {
            require_once __DIR__ . '/../Models/Invoice.php';
            $invoice = (new Invoice())->findByAppointmentId($record['appointment_id']);
        }

        require_once APP_PATH . '/Views/medical-records/show.php';
    }

    // Form tạo hồ sơ bệnh án (CHỈ Bác sĩ/Admin)
    public function create() {
        Auth::requireLogin();

        if (!Auth::isDoctor() && !Auth::isAdmin()) {
            $_SESSION['error'] = 'Bạn không có quyền tạo hồ sơ bệnh án';
            header('Location: ' . APP_URL . '/medical-records');
            exit;
        }

        // Lấy danh sách bệnh nhân
        // Nếu là bác sĩ: chỉ hiển thị bệnh nhân đã từng khám với bác sĩ này (đã xác nhận/hoàn thành)
        $patients = [];
        $isDoctor = Auth::isDoctor();
        $doctorId = null;
        if ($isDoctor) {
            require_once __DIR__ . '/../Models/Doctor.php';
            $doctor = (new Doctor())->findByUserId(Auth::id());
            $doctorId = $doctor ? $doctor['id'] : null;
            if ($doctorId) {
                $patients = $this->patientModel->getByDoctorId($doctorId);
            }
        } else {
            $patients = $this->patientModel->getAll();
        }

        // Nếu có appointment_id từ URL
        $appointmentId = $_GET['appointment_id'] ?? null;
        $selectedPatient = null;
        $appointmentVisitDate = null;
        
        if ($appointmentId) {
            require_once __DIR__ . '/../Models/Appointment.php';
            $appointment = (new Appointment())->findById($appointmentId);
            if ($appointment) {
                $selectedPatient = $this->patientModel->findById($appointment['patient_id']);
                $appointmentVisitDate = $appointment['appointment_date'] ?? null;
            }
            // Nếu đã có hồ sơ cho lịch hẹn này, chuyển sang trang chi tiết để tránh tạo trùng
            $existing = $this->medicalRecordModel->findByAppointmentId($appointmentId);
            if ($existing) {
                $_SESSION['success'] = 'Hồ sơ bệnh án cho lịch hẹn này đã tồn tại.';
                header('Location: ' . APP_URL . '/medical-records/' . $existing['id']);
                exit;
            }
        }

        require_once APP_PATH . '/Views/medical-records/create.php';
    }

    // API: Lấy các lịch hẹn của bệnh nhân với bác sĩ hiện tại (đã xác nhận/hoàn thành)
    public function getPatientAppointmentsForDoctor($patient_id) {
        Auth::requireLogin();
        if (!Auth::isDoctor() && !Auth::isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            return;
        }

        require_once __DIR__ . '/../Models/Appointment.php';
        require_once __DIR__ . '/../Models/Doctor.php';
        $doctor = (new Doctor())->findByUserId(Auth::id());
        if (!$doctor) {
            echo json_encode([]);
            return;
        }
        $appointments = (new Appointment())->getByDoctorAndPatient($doctor['id'], $patient_id);
        header('Content-Type: application/json');
        echo json_encode($appointments);
    }

    // Lưu hồ sơ bệnh án
    public function store() {
        Auth::requireLogin();

        if (!Auth::isDoctor() && !Auth::isAdmin()) {
            $_SESSION['error'] = 'Bạn không có quyền tạo hồ sơ bệnh án';
            header('Location: ' . APP_URL . '/medical-records');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/medical-records');
            exit;
        }

        // Lấy doctor_id và ràng buộc theo lịch hẹn nếu là Bác sĩ
        if (Auth::isDoctor()) {
            require_once __DIR__ . '/../Models/Doctor.php';
            require_once __DIR__ . '/../Models/Appointment.php';
            $doctor = (new Doctor())->findByUserId(Auth::id());
            $doctorId = $doctor['id'];

            // Bắt buộc phải chọn lịch hẹn khi bác sĩ tạo hồ sơ
            $appointmentIdPost = $_POST['appointment_id'] ?? null;
            if (!$appointmentIdPost) {
                $_SESSION['error'] = 'Vui lòng chọn lịch hẹn của bệnh nhân với bạn để tạo hồ sơ.';
                header('Location: ' . APP_URL . '/medical-records/create');
                exit;
            }

            $appointment = (new Appointment())->findById($appointmentIdPost);
            if (!$appointment || (int)$appointment['doctor_id'] !== (int)$doctorId || (int)$appointment['patient_id'] !== (int)$_POST['patient_id']) {
                $_SESSION['error'] = 'Lịch hẹn không hợp lệ cho bác sĩ/bệnh nhân này.';
                header('Location: ' . APP_URL . '/medical-records/create');
                exit;
            }
        } else {
            $doctorId = $_POST['doctor_id'];
        }

        $this->medicalRecordModel->patient_id = $_POST['patient_id'];
        $this->medicalRecordModel->doctor_id = $doctorId;
        $this->medicalRecordModel->appointment_id = $_POST['appointment_id'] ?? null;
        $this->medicalRecordModel->diagnosis = $_POST['diagnosis'];
        $this->medicalRecordModel->symptoms = $_POST['symptoms'];
        $this->medicalRecordModel->treatment = $_POST['treatment'];
        $this->medicalRecordModel->prescription = $_POST['prescription'] ?? null;
        $this->medicalRecordModel->test_results = $_POST['test_results'] ?? null;
        $this->medicalRecordModel->notes = $_POST['notes'] ?? '';
        // Nếu là bác sĩ, bắt buộc dùng ngày từ lịch hẹn; Admin dùng theo input
        if (Auth::isDoctor()) {
            $this->medicalRecordModel->visit_date = $appointment['appointment_date'];
        } else {
            $this->medicalRecordModel->visit_date = $_POST['visit_date'];
        }

        if ($this->medicalRecordModel->create()) {
            $_SESSION['success'] = 'Tạo hồ sơ bệnh án thành công';
            header('Location: ' . APP_URL . '/medical-records/' . $this->medicalRecordModel->id);
            exit;
        } else {
            $_SESSION['error'] = 'Tạo hồ sơ bệnh án thất bại';
            header('Location: ' . APP_URL . '/medical-records/create');
            exit;
        }
    }
}
