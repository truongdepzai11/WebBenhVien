<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Specialization.php';
require_once __DIR__ . '/../Models/Doctor.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class SpecializationController {
    private $specializationModel;
    private $doctorModel;

    public function __construct() {
        $this->specializationModel = new Specialization();
        $this->doctorModel = new Doctor();
    }

    // Danh sách chuyên khoa (công khai)
    public function index() {
        $specializations = $this->specializationModel->getAll();
        
        // Đếm số bác sĩ cho mỗi chuyên khoa
        $database = new Database();
        $conn = $database->getConnection();
        
        foreach ($specializations as &$spec) {
            $query = "SELECT COUNT(*) as total FROM doctors WHERE specialization_id = :specialization_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':specialization_id', $spec['id']);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $spec['doctor_count'] = $result['total'];
        }
        
        require_once APP_PATH . '/Views/specializations/index.php';
    }

    // Chi tiết chuyên khoa
    public function show($id) {
        $specialization = $this->specializationModel->findById($id);
        
        if (!$specialization) {
            $_SESSION['error'] = 'Không tìm thấy chuyên khoa!';
            header('Location: ' . APP_URL . '/specializations');
            exit;
        }

        // Lấy danh sách bác sĩ của chuyên khoa
        $doctors = $this->doctorModel->getBySpecialization($specialization['name']);
        
        require_once APP_PATH . '/Views/specializations/show.php';
    }
}
