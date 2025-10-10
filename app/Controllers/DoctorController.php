<?php

require_once __DIR__ . '/../Models/Doctor.php';
require_once __DIR__ . '/../Models/Appointment.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class DoctorController {
    private $doctorModel;
    private $appointmentModel;

    public function __construct() {
        $this->doctorModel = new Doctor();
        $this->appointmentModel = new Appointment();
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

        // Lấy appointments của bác sĩ trong tuần được chọn
        $currentWeek = isset($_GET['week']) ? $_GET['week'] : date('Y-\WW');
        $weekStart = new DateTime();
        $weekStart->setISODate(substr($currentWeek, 0, 4), substr($currentWeek, 6, 2));
        $weekEnd = clone $weekStart;
        $weekEnd->modify('+6 days');
        
        $allAppointments = $this->appointmentModel->getByDoctorId($id);
        $weeklyAppointments = [];
        
        foreach ($allAppointments as $apt) {
            $aptDate = new DateTime($apt['appointment_date']);
            if ($aptDate >= $weekStart && $aptDate <= $weekEnd && 
                !in_array($apt['status'], ['cancelled', 'late_cancelled'])) {
                $dateKey = $apt['appointment_date'];
                if (!isset($weeklyAppointments[$dateKey])) {
                    $weeklyAppointments[$dateKey] = [];
                }
                $weeklyAppointments[$dateKey][] = $apt;
            }
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
