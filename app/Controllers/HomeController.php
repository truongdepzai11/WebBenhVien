<?php

require_once __DIR__ . '/../Models/Specialization.php';
require_once __DIR__ . '/../Models/Doctor.php';

class HomeController {
    private $specializationModel;
    private $doctorModel;

    public function __construct() {
        $this->specializationModel = new Specialization();
        $this->doctorModel = new Doctor();
    }

    // Trang chủ (Landing Page)
    public function index() {
        // Lấy các chuyên khoa
        $specializations = $this->specializationModel->getAll();
        
        // Lấy bác sĩ nổi bật (top 6)
        $doctors = $this->doctorModel->getAll();
        $featured_doctors = array_slice($doctors, 0, 6);
        
        require_once APP_PATH . '/Views/home/index.php';
    }
}
