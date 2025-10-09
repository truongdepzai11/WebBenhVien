<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/User.php';

class ValidationController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // Check username đã tồn tại chưa
    public function checkUsername() {
        header('Content-Type: application/json');
        
        $username = $_GET['username'] ?? '';
        
        if (empty($username)) {
            echo json_encode(['available' => true]);
            exit;
        }

        $user = $this->userModel->findByUsername($username);
        
        echo json_encode([
            'available' => !$user,
            'message' => $user ? 'Tên đăng nhập đã tồn tại' : 'Tên đăng nhập khả dụng'
        ]);
        exit;
    }

    // Check email đã tồn tại chưa
    public function checkEmail() {
        header('Content-Type: application/json');
        
        $email = $_GET['email'] ?? '';
        
        if (empty($email)) {
            echo json_encode(['available' => true]);
            exit;
        }

        $user = $this->userModel->findByEmail($email);
        
        echo json_encode([
            'available' => !$user,
            'message' => $user ? 'Email đã được sử dụng' : 'Email khả dụng'
        ]);
        exit;
    }

    // Check phone đã tồn tại chưa
    public function checkPhone() {
        header('Content-Type: application/json');
        
        $phone = $_GET['phone'] ?? '';
        
        if (empty($phone)) {
            echo json_encode(['available' => true]);
            exit;
        }

        // Tạo connection riêng
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT id FROM users WHERE phone = :phone LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':phone', $phone);
        $stmt->execute();
        
        $exists = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'available' => !$exists,
            'message' => $exists ? 'Số điện thoại đã được sử dụng. Vui lòng chọn số khác' : '✓ Số điện thoại hợp lệ'
        ]);
        exit;
    }
}
