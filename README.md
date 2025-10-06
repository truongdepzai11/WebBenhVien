# Hospital Management System

Hệ thống quản lý bệnh viện - Đồ án CNTT

## Tính năng chính

### 1. Quản lý người dùng
- Đăng ký tài khoản bệnh nhân
- Đăng nhập và xác thực
- Phân quyền: Admin, Bác sĩ, Bệnh nhân
- **Quản lý thông tin cá nhân**
- **Đổi mật khẩu**

### 2. Quản lý bệnh nhân
- Xem danh sách bệnh nhân
- Xem thông tin chi tiết bệnh nhân
- **Cập nhật thông tin y tế (nhóm máu, dị ứng, liên hệ khẩn cấp)**
- Tìm kiếm bệnh nhân
- Xem hồ sơ bệnh án

### 3. Quản lý bác sĩ
- Danh sách bác sĩ theo chuyên khoa
- Thông tin chi tiết bác sĩ
- Lịch làm việc và phí khám
- **Hiển thị kinh nghiệm và trình độ**

### 4. Quản lý chuyên khoa
- **Danh sách chuyên khoa với điều kiện**
- **Kiểm tra độ tuổi phù hợp (Nhi khoa: 0-15 tuổi, Lão khoa: 60+ tuổi)**
- **Kiểm tra giới tính (Sản phụ khoa: Nữ, Nam khoa: Nam)**
- **Lọc bác sĩ theo chuyên khoa phù hợp**

### 5. Quản lý lịch hẹn
- Đặt lịch khám online
- **Chọn chuyên khoa phù hợp với bệnh nhân**
- **Kiểm tra điều kiện chuyên khoa tự động**
- Xem lịch hẹn của bản thân
- Xác nhận/Hủy lịch hẹn
- Cập nhật trạng thái lịch hẹn
- **Kiểm tra xung đột lịch**

### 6. Hồ sơ bệnh án
- Ghi nhận chẩn đoán
- Quản lý đơn thuốc
- Lưu trữ kết quả xét nghiệm

## Công nghệ sử dụng

- **Backend**: PHP 7.4+ (Pure PHP, không framework)
- **Database**: MySQL
- **Frontend**: 
  - HTML5, CSS3
  - Tailwind CSS (UI Framework)
  - Font Awesome (Icons)
  - JavaScript (Vanilla)
- **Architecture**: MVC Pattern

## Cài đặt

### Yêu cầu hệ thống

- PHP >= 7.4
- MySQL >= 5.7
- Apache/Nginx web server
- XAMPP/WAMP (khuyến nghị cho Windows)

### Hướng dẫn cài đặt

1. **Clone project vào thư mục htdocs của XAMPP**
   ```bash
   cd C:\xampp\htdocs\WebBenhvien
   ```

2. **Tạo database**
   - Mở phpMyAdmin: `http://localhost/phpmyadmin`
   - Import file `sql/schema.sql`
   - Hoặc chạy các lệnh SQL trong file đó

3. **Cấu hình database**
   - Mở file `config/database.php`
   - Cập nhật thông tin kết nối nếu cần:
   ```php
   private $host = 'localhost';
   private $db_name = 'hospital_management';
   private $username = 'root';
   private $password = '';
   ```

4. **Cấu hình URL**
   - Mở file `config/config.php`
   - Kiểm tra APP_URL:
   ```php
   define('APP_URL', 'http://localhost/WebBenhvien/hospital-management-system/public');
   ```

5. **Truy cập ứng dụng**
   ```
   http://localhost/WebBenhvien/hospital-management-system/public
   ```

## Tài khoản demo

### Admin
- Username: `admin`
- Password: `password`

### Bác sĩ
- Username: `dr.nguyen`
- Password: `password`

- Username: `dr.tran`
- Password: `password`

### Bệnh nhân
- Username: `patient1`
- Password: `password`

- Username: `patient2`
- Password: `password`

## Cấu trúc thư mục

```
hospital-management-system/
├── app/
│   ├── Controllers/      # Các controller xử lý logic
│   ├── Models/           # Các model tương tác database
│   ├── Views/            # Giao diện người dùng
│   └── Helpers/          # Các helper function
├── config/               # File cấu hình
├── public/               # Thư mục public (entry point)
│   ├── assets/           # CSS, JS, Images
│   ├── index.php         # File khởi động
│   └── .htaccess         # URL rewrite
├── routes/               # Định nghĩa routes
├── sql/                  # Database schema
└── tests/                # Unit tests
```

## Tính năng theo vai trò

### Admin
- Xem tất cả thống kê hệ thống
- Quản lý bệnh nhân, bác sĩ
- Quản lý tất cả lịch hẹn
- Xem hồ sơ bệnh án

### Bác sĩ
- Xem danh sách bệnh nhân của mình
- Quản lý lịch hẹn
- Xác nhận/Hoàn thành lịch khám
- Tạo và quản lý hồ sơ bệnh án

### Bệnh nhân
- Xem danh sách bác sĩ
- Đặt lịch khám
- Xem lịch hẹn của mình
- Hủy lịch hẹn
- Xem hồ sơ bệnh án của bản thân

## Bảo mật

- Mật khẩu được mã hóa bằng bcrypt
- Session-based authentication
- Phân quyền truy cập theo vai trò
- Validation dữ liệu input
- Protection khỏi SQL Injection (PDO Prepared Statements)
- XSS protection (htmlspecialchars)

## Ghi chú

- Đây là đồ án học tập, phù hợp cho môn học CNTT
- Sử dụng Pure PHP không framework để hiểu rõ cơ chế hoạt động
- Giao diện responsive, thân thiện với người dùng
- Code được tổ chức theo mô hình MVC

## Liên hệ

Nếu có thắc mắc hoặc cần hỗ trợ, vui lòng liên hệ qua email hoặc tạo issue trên GitHub.

## License

MIT License - Tự do sử dụng cho mục đích học tập.
