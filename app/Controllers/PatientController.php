<?php

require_once __DIR__ . '/../Models/Patient.php';
require_once __DIR__ . '/../Models/MedicalRecord.php';
require_once __DIR__ . '/../Models/Doctor.php';
require_once __DIR__ . '/../Models/Appointment.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class PatientController {
    private $patientModel;
    private $medicalRecordModel;
    private $doctorModel;
    private $appointmentModel;

    public function __construct() {
        $this->patientModel = new Patient();
        $this->medicalRecordModel = new MedicalRecord();
        $this->doctorModel = new Doctor();
        $this->appointmentModel = new Appointment();
    }

    // Danh sách bệnh nhân
    public function index() {
        Auth::requireLogin();

        if (Auth::isDoctor()) {
            // Bác sĩ chỉ xem bệnh nhân đã khám với mình
            $doctor = $this->doctorModel->findByUserId(Auth::id());
            $appointments = $this->appointmentModel->getByDoctorId($doctor['id']);
            
            // Lấy danh sách patient_id duy nhất
            $patientIds = array_unique(array_column($appointments, 'patient_id'));
            
            // Lấy thông tin bệnh nhân
            $allPatients = $this->patientModel->getAll();
            $patients = array_filter($allPatients, function($patient) use ($patientIds) {
                return in_array($patient['id'], $patientIds);
            });
        } else {
            // Admin/Lễ tân xem tất cả
            $patients = $this->patientModel->getAll();
        }
        
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
        
        // Lấy lịch hẹn gần đây (chỉ regular appointments)
        $appointments = $this->appointmentModel->getByPatientId($id);
        $recent_appointments = array_slice($appointments, 0, 10);
        
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

        // Tìm kiếm trong tất cả bệnh nhân
        $allResults = $this->patientModel->search($keyword);
        
        // Nếu là bác sĩ, chỉ lấy bệnh nhân đã khám với mình
        if (Auth::isDoctor()) {
            $doctor = $this->doctorModel->findByUserId(Auth::id());
            $appointments = $this->appointmentModel->getByDoctorId($doctor['id']);
            
            // Lấy danh sách patient_id của bác sĩ
            $doctorPatientIds = array_unique(array_column($appointments, 'patient_id'));
            
            // Lọc kết quả tìm kiếm
            $patients = array_filter($allResults, function($patient) use ($doctorPatientIds) {
                return in_array($patient['id'], $doctorPatientIds);
            });
        } else {
            // Admin/Lễ tân xem tất cả kết quả
            $patients = $allResults;
        }
        
        require_once APP_PATH . '/Views/patients/index.php';
    }
}
