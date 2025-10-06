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
        $patients = $this->patientModel->getAll();

        // Nếu có appointment_id từ URL
        $appointmentId = $_GET['appointment_id'] ?? null;
        $selectedPatient = null;
        
        if ($appointmentId) {
            require_once __DIR__ . '/../Models/Appointment.php';
            $appointment = (new Appointment())->findById($appointmentId);
            if ($appointment) {
                $selectedPatient = $this->patientModel->findById($appointment['patient_id']);
            }
        }

        require_once APP_PATH . '/Views/medical-records/create.php';
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

        // Lấy doctor_id
        if (Auth::isDoctor()) {
            require_once __DIR__ . '/../Models/Doctor.php';
            $doctor = (new Doctor())->findByUserId(Auth::id());
            $doctorId = $doctor['id'];
        } else {
            $doctorId = $_POST['doctor_id'];
        }

        $this->medicalRecordModel->patient_id = $_POST['patient_id'];
        $this->medicalRecordModel->doctor_id = $doctorId;
        $this->medicalRecordModel->appointment_id = $_POST['appointment_id'] ?? null;
        $this->medicalRecordModel->diagnosis = $_POST['diagnosis'];
        $this->medicalRecordModel->symptoms = $_POST['symptoms'];
        $this->medicalRecordModel->treatment = $_POST['treatment'];
        $this->medicalRecordModel->notes = $_POST['notes'] ?? '';
        $this->medicalRecordModel->visit_date = $_POST['visit_date'];

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
