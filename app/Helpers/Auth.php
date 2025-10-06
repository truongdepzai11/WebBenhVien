<?php

class Auth {
    // Kiểm tra đã đăng nhập
    public static function check() {
        return isset($_SESSION['user_id']);
    }

    // Lấy thông tin user hiện tại
    public static function user() {
        if (self::check()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'full_name' => $_SESSION['full_name'],
                'email' => $_SESSION['email'],
                'role' => $_SESSION['role']
            ];
        }
        return null;
    }

    // Lấy ID user
    public static function id() {
        return $_SESSION['user_id'] ?? null;
    }

    // Lấy role user
    public static function role() {
        return $_SESSION['role'] ?? null;
    }

    // Đăng nhập
    public static function login($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
    }

    // Đăng xuất
    public static function logout() {
        session_unset();
        session_destroy();
    }

    // Kiểm tra quyền admin
    public static function isAdmin() {
        return self::check() && $_SESSION['role'] === 'admin';
    }

    // Kiểm tra quyền doctor
    public static function isDoctor() {
        return self::check() && $_SESSION['role'] === 'doctor';
    }

    // Kiểm tra quyền patient
    public static function isPatient() {
        return self::check() && $_SESSION['role'] === 'patient';
    }

    // Yêu cầu đăng nhập
    public static function requireLogin() {
        if (!self::check()) {
            header('Location: ' . APP_URL . '/auth/login');
            exit;
        }
    }

    // Yêu cầu quyền admin
    public static function requireAdmin() {
        self::requireLogin();
        if (!self::isAdmin()) {
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }
    }

    // Yêu cầu quyền doctor
    public static function requireDoctor() {
        self::requireLogin();
        if (!self::isDoctor() && !self::isAdmin()) {
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }
    }
}
