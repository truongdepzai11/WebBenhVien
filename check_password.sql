-- Kiểm tra mật khẩu đã lưu
SELECT id, username, email, password FROM users WHERE role = 'patient' ORDER BY id DESC LIMIT 5;
