<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Patient.php';
require_once __DIR__ . '/../Models/Doctor.php';
require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/Validator.php';

class ProfileController {
    private $userModel;
    private $patientModel;
    private $doctorModel;

    public function __construct() {
        $this->userModel = new User();
        $this->patientModel = new Patient();
        $this->doctorModel = new Doctor();
    }

    // Hiển thị trang profile
    public function index() {
        Auth::requireLogin();

        $user = Auth::user();
        $userDetails = $this->userModel->findById($user['id']);
        
        if (Auth::isPatient()) {
            $profile = $this->patientModel->findByUserId($user['id']);
        } elseif (Auth::isDoctor()) {
            $profile = $this->doctorModel->findByUserId($user['id']);
        }

        require_once APP_PATH . '/Views/profile/index.php';
    }

    // Cập nhật thông tin cá nhân
    public function update() {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/profile');
            exit;
        }

        $validator = new Validator($_POST);
        $validator->required('full_name', 'Họ tên không được để trống')
                  ->required('email', 'Email không được để trống')
                  ->email('email', 'Email không hợp lệ')
                  ->required('phone', 'Số điện thoại không được để trống')
                  ->phone('phone', 'Số điện thoại không hợp lệ');

        if ($validator->fails()) {
            $_SESSION['error'] = $validator->firstError();
            header('Location: ' . APP_URL . '/profile');
            exit;
        }

        // Cập nhật user
        $this->userModel->id = Auth::id();
        $this->userModel->full_name = $_POST['full_name'];
        $this->userModel->email = $_POST['email'];
        $this->userModel->phone = $_POST['phone'];

        if ($this->userModel->update()) {
            // Cập nhật session
            $_SESSION['full_name'] = $_POST['full_name'];
            $_SESSION['email'] = $_POST['email'];

            // Cập nhật thông tin bổ sung
            if (Auth::isPatient()) {
                $patient = $this->patientModel->findByUserId(Auth::id());
                if ($patient) {
                    $this->patientModel->id = $patient['id'];
                    $this->patientModel->date_of_birth = $_POST['date_of_birth'] ?? null;
                    $this->patientModel->gender = $_POST['gender'] ?? null;
                    $this->patientModel->address = $_POST['address'] ?? null;
                    $this->patientModel->blood_type = $_POST['blood_type'] ?? null;
                    $this->patientModel->allergies = $_POST['allergies'] ?? null;
                    $this->patientModel->emergency_contact = $_POST['emergency_contact'] ?? null;
                    $this->patientModel->emergency_phone = $_POST['emergency_phone'] ?? null;
                    $this->patientModel->update();
                }
            }

            $_SESSION['success'] = 'Cập nhật thông tin thành công!';
        } else {
            $_SESSION['error'] = 'Cập nhật thông tin thất bại!';
        }

        header('Location: ' . APP_URL . '/profile');
        exit;
    }

    // Hiển thị form đổi mật khẩu
    public function showChangePassword() {
        Auth::requireLogin();
        require_once APP_PATH . '/Views/profile/change-password.php';
    }

    // Xử lý đổi mật khẩu
    public function changePassword() {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/profile/change-password');
            exit;
        }

        $validator = new Validator($_POST);
        $validator->required('current_password', 'Mật khẩu hiện tại không được để trống')
                  ->required('new_password', 'Mật khẩu mới không được để trống')
                  ->min('new_password', 6, 'Mật khẩu mới phải có ít nhất 6 ký tự')
                  ->match('new_password', 'confirm_password', 'Xác nhận mật khẩu không khớp');

        if ($validator->fails()) {
            $_SESSION['error'] = $validator->firstError();
            header('Location: ' . APP_URL . '/profile/change-password');
            exit;
        }

        // Kiểm tra mật khẩu hiện tại
        $user = $this->userModel->findById(Auth::id());
        if (!password_verify($_POST['current_password'], $user['password'])) {
            $_SESSION['error'] = 'Mật khẩu hiện tại không đúng';
            header('Location: ' . APP_URL . '/profile/change-password');
            exit;
        }

        // Cập nhật mật khẩu mới
        $query = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->userModel->conn->prepare($query);
        $hashed_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':id', Auth::id());

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Đổi mật khẩu thành công!';
            header('Location: ' . APP_URL . '/profile');
        } else {
            $_SESSION['error'] = 'Đổi mật khẩu thất bại!';
            header('Location: ' . APP_URL . '/profile/change-password');
        }
        exit;
    }
}
