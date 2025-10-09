-- Script thêm tài khoản Lễ tân mẫu
-- Chạy script này sau khi đã update schema.sql

USE hospital_management;

-- Thêm user Lễ tân
INSERT INTO users (username, email, password, full_name, phone, role, is_active) 
VALUES (
    'receptionist1',
    'receptionist@hospital.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'Nguyễn Thị Lễ Tân',
    '0901234567',
    'receptionist',
    TRUE
);

-- Kiểm tra
SELECT id, username, full_name, email, role FROM users WHERE role = 'receptionist';
