<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Patient.php';
require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/Validator.php';

class AuthController {
    private $userModel;
    private $patientModel;

    public function __construct() {
        $this->userModel = new User();
        $this->patientModel = new Patient();
    }

    // Hiển thị trang đăng nhập
    public function showLogin() {
        if (Auth::check()) {
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }
        require_once APP_PATH . '/Views/auth/login.php';
    }

    // Xử lý đăng nhập
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/auth/login');
            exit;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Validate
        $validator = new Validator($_POST);
        $validator->required('username', 'Tên đăng nhập không được để trống')
                  ->required('password', 'Mật khẩu không được để trống');

        if ($validator->fails()) {
            $_SESSION['error'] = $validator->firstError();
            header('Location: ' . APP_URL . '/auth/login');
            exit;
        }

        // Xác thực
        $user = $this->userModel->authenticate($username, $password);

        if ($user) {
            Auth::login($user);
            $_SESSION['success'] = 'Chào mừng, ' . $user['full_name'] . '!';
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        } else {
            $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng';
            header('Location: ' . APP_URL . '/auth/login');
            exit;
        }
    }

    // Hiển thị trang đăng ký
    public function showRegister() {
        if (Auth::check()) {
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }
        require_once APP_PATH . '/Views/auth/register.php';
    }

    // Xử lý đăng ký
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/auth/register');
            exit;
        }

        // Validate
        $validator = new Validator($_POST);
        $validator->required('username', 'Tên đăng nhập không được để trống')
                  ->min('username', 3, 'Tên đăng nhập phải có ít nhất 3 ký tự')
                  ->unique('username', $this->userModel, 'username', 'Tên đăng nhập đã tồn tại')
                  ->required('email', 'Email không được để trống')
                  ->email('email', 'Email không hợp lệ')
                  ->unique('email', $this->userModel, 'email', 'Email đã tồn tại')
                  ->required('password', 'Mật khẩu không được để trống')
                  ->min('password', 6, 'Mật khẩu phải có ít nhất 6 ký tự')
                  ->match('password', 'password_confirm', 'Xác nhận mật khẩu không khớp')
                  ->required('full_name', 'Họ tên không được để trống')
                  ->required('phone', 'Số điện thoại không được để trống')
                  ->phone('phone', 'Số điện thoại không hợp lệ');

        if ($validator->fails()) {
            $_SESSION['error'] = $validator->firstError();
            $_SESSION['old'] = $_POST;
            header('Location: ' . APP_URL . '/auth/register');
            exit;
        }

        // Tạo user với đầy đủ thông tin
        $this->userModel->username = $_POST['username'];
        $this->userModel->email = $_POST['email'];
        $this->userModel->password = $_POST['password'];
        $this->userModel->full_name = $_POST['full_name'];
        $this->userModel->phone = $_POST['phone'];
        $this->userModel->date_of_birth = $_POST['date_of_birth'] ?? null;
        $this->userModel->gender = $_POST['gender'] ?? null;
        $this->userModel->address = $_POST['address'] ?? null;
        $this->userModel->role = 'patient';

        if ($this->userModel->create()) {
            // Tạo patient profile
            $this->patientModel->user_id = $this->userModel->id;
            $this->patientModel->create();

            $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
            header('Location: ' . APP_URL . '/auth/login');
            exit;
        } else {
            $_SESSION['error'] = 'Đăng ký thất bại. Vui lòng thử lại.';
            header('Location: ' . APP_URL . '/auth/register');
            exit;
        }
    }

    // Đăng xuất
    public function logout() {
        Auth::logout();
        $_SESSION['success'] = 'Đã đăng xuất thành công';
        header('Location: ' . APP_URL . '/auth/login');
        exit;
    }
}
