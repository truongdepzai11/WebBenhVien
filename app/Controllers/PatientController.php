<?php

require_once __DIR__ . '/../Models/Patient.php';
require_once __DIR__ . '/../Models/MedicalRecord.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class PatientController {
    private $patientModel;
    private $medicalRecordModel;

    public function __construct() {
        $this->patientModel = new Patient();
        $this->medicalRecordModel = new MedicalRecord();
    }

    // Danh sách bệnh nhân
    public function index() {
        Auth::requireLogin();

        $patients = $this->patientModel->getAll();
        require_once APP_PATH . '/Views/patients/index.php';
    }

    // Chi tiết bệnh nhân
    public function show($id) {
        Auth::requireLogin();

        $patient = $this->patientModel->findById($id);
        
        if (!$patient) {
            $_SESSION['error'] = 'Không tìm thấy bệnh nhân';
            header('Location: ' . APP_URL . '/patients');
            exit;
        }

        // Kiểm tra quyền truy cập
        if (Auth::isPatient() && $patient['user_id'] != Auth::id()) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập';
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        $medical_records = $this->medicalRecordModel->getByPatientId($id);
        require_once APP_PATH . '/Views/patients/show.php';
    }

    // Tìm kiếm bệnh nhân
    public function search() {
        Auth::requireLogin();

        $keyword = $_GET['keyword'] ?? '';
        
        if (empty($keyword)) {
            header('Location: ' . APP_URL . '/patients');
            exit;
        }

        $patients = $this->patientModel->search($keyword);
        require_once APP_PATH . '/Views/patients/index.php';
    }
}
