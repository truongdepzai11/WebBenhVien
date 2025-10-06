<?php

require_once __DIR__ . '/../Models/Doctor.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class DoctorController {
    private $doctorModel;

    public function __construct() {
        $this->doctorModel = new Doctor();
    }

    // Danh sách bác sĩ
    public function index() {
        Auth::requireLogin();

        $doctors = $this->doctorModel->getAll();
        $specializations = $this->doctorModel->getSpecializations();
        require_once APP_PATH . '/Views/doctors/index.php';
    }

    // Chi tiết bác sĩ
    public function show($id) {
        Auth::requireLogin();

        $doctor = $this->doctorModel->findById($id);
        
        if (!$doctor) {
            $_SESSION['error'] = 'Không tìm thấy bác sĩ';
            header('Location: ' . APP_URL . '/doctors');
            exit;
        }

        require_once APP_PATH . '/Views/doctors/show.php';
    }

    // Tìm kiếm bác sĩ
    public function search() {
        Auth::requireLogin();

        $keyword = $_GET['keyword'] ?? '';
        
        if (empty($keyword)) {
            header('Location: ' . APP_URL . '/doctors');
            exit;
        }

        $doctors = $this->doctorModel->search($keyword);
        $specializations = $this->doctorModel->getSpecializations();
        require_once APP_PATH . '/Views/doctors/index.php';
    }
}
