# Hướng dẫn tích hợp Lễ tân (Receptionist)

## 📋 Tổng quan

Đã thêm vai trò **Lễ tân (Receptionist)** vào hệ thống để đăng ký lịch khám offline cho bệnh nhân đến trực tiếp tại bệnh viện.

---

## 🔧 Các thay đổi đã thực hiện

### 1. **Database Schema**
- ✅ Thêm role `receptionist` vào bảng `users`
- File: `sql/schema.sql` (dòng 18)

### 2. **Auth Helper**
- ✅ Thêm method `isReceptionist()`
- ✅ Thêm method `isAdminOrReceptionist()`
- File: `app/Helpers/Auth.php`

### 3. **Controllers đã cập nhật**

#### **ScheduleController**
- ✅ Lễ tân có quyền xem lịch làm việc tất cả bác sĩ
- ✅ Lễ tân có quyền thêm bệnh nhân walk-in
- File: `app/Controllers/ScheduleController.php`

#### **InvoiceController**
- ✅ Lễ tân có quyền tạo hóa đơn
- ✅ Lễ tân có quyền xác nhận thanh toán tiền mặt
- File: `app/Controllers/InvoiceController.php`

#### **DashboardController**
- ✅ Lễ tân xem dashboard giống Admin
- File: `app/Controllers/DashboardController.php`

### 4. **Views đã cập nhật**

#### **Sidebar Menu**
- ✅ Lễ tân thấy menu: Bệnh nhân, Lịch làm việc, Hóa đơn
- File: `app/Views/layouts/main.php`

#### **Invoice Show**
- ✅ Lễ tân thấy nút "Xác nhận thanh toán"
- File: `app/Views/invoices/show.php`

---

## 🚀 Cách triển khai

### **Bước 1: Cập nhật Database**

```sql
-- Chạy lệnh này trong phpMyAdmin hoặc MySQL CLI
ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'doctor', 'patient', 'staff', 'receptionist') DEFAULT 'patient';
```

### **Bước 2: Thêm tài khoản Lễ tân**

```bash
# Chạy file SQL
mysql -u root -p hospital_management < sql/add_receptionist.sql
```

Hoặc chạy trực tiếp trong phpMyAdmin:

```sql
INSERT INTO users (username, email, password, full_name, phone, role, is_active) 
VALUES (
    'receptionist1',
    'receptionist@hospital.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Nguyễn Thị Lễ Tân',
    '0901234567',
    'receptionist',
    TRUE
);
```

**Thông tin đăng nhập:**
- Username: `receptionist1`
- Password: `password`

---

## 📱 Chức năng Lễ tân

### **1. Xem lịch làm việc bác sĩ**
- Truy cập: `/schedule`
- Chọn bác sĩ và ngày
- Xem các slot trống/đã đặt

### **2. Đăng ký lịch khám Walk-in**
- Click vào slot trống
- Chọn:
  - **Bệnh nhân cũ**: Tìm trong danh sách
  - **Bệnh nhân mới**: Nhập thông tin mới
- Điền lý do khám, triệu chứng
- Lưu → Tự động xác nhận

### **3. Quản lý bệnh nhân**
- Truy cập: `/patients`
- Xem danh sách bệnh nhân
- Tìm kiếm bệnh nhân

### **4. Xử lý thanh toán**
- Truy cập: `/invoices`
- Xem hóa đơn chưa thanh toán
- Click "Xác nhận thanh toán" cho hóa đơn tiền mặt
- Hóa đơn chuyển sang "Đã thanh toán"

---

## 🔒 Phân quyền

| Chức năng | Admin | Doctor | Receptionist | Patient |
|-----------|-------|--------|--------------|---------|
| Xem lịch làm việc | ✅ | ✅ (của mình) | ✅ | ❌ |
| Thêm lịch walk-in | ✅ | ✅ | ✅ | ❌ |
| Tạo hóa đơn | ✅ | ✅ | ✅ | ❌ |
| Xác nhận thanh toán | ✅ | ❌ | ✅ | ❌ |
| Quản lý users | ✅ | ❌ | ❌ | ❌ |
| Quản lý bác sĩ | ✅ | ❌ | ❌ | ❌ |
| Quản lý chuyên khoa | ✅ | ❌ | ❌ | ❌ |

---

## 🧪 Test Cases

### **Test 1: Đăng nhập Lễ tân**
1. Đăng nhập với `receptionist1` / `password`
2. Kiểm tra dashboard hiển thị đúng
3. Kiểm tra sidebar có đủ menu

### **Test 2: Đăng ký Walk-in bệnh nhân mới**
1. Vào `/schedule`
2. Chọn bác sĩ và ngày
3. Click slot trống
4. Chọn "Bệnh nhân mới"
5. Nhập thông tin đầy đủ
6. Lưu và kiểm tra lịch hẹn xuất hiện

### **Test 3: Đăng ký Walk-in bệnh nhân cũ**
1. Vào `/schedule`
2. Click slot trống
3. Chọn "Bệnh nhân cũ"
4. Tìm và chọn bệnh nhân
5. Lưu và kiểm tra

### **Test 4: Xác nhận thanh toán**
1. Vào `/invoices`
2. Click vào hóa đơn chưa thanh toán
3. Click "Xác nhận thanh toán"
4. Chọn "Tiền mặt"
5. Xác nhận → Kiểm tra trạng thái chuyển "Đã thanh toán"

---

## 📝 Lưu ý

1. **Bệnh nhân mới từ walk-in:**
   - Username tự động: `patient_<timestamp>`
   - Email tạm: `patient_<timestamp>@walkin.local`
   - Password mặc định: `walkin123`
   - Nên hướng dẫn bệnh nhân đổi password sau

2. **Thanh toán:**
   - Lễ tân chỉ xác nhận thanh toán tiền mặt
   - MoMo/VNPay bệnh nhân tự thanh toán online

3. **Quyền hạn:**
   - Lễ tân KHÔNG có quyền:
     - Quản lý users
     - Quản lý bác sĩ
     - Quản lý chuyên khoa
     - Xóa dữ liệu

---

## 🔄 Rollback (nếu cần)

```sql
-- Xóa role receptionist
DELETE FROM users WHERE role = 'receptionist';

-- Rollback schema
ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'doctor', 'patient', 'staff') DEFAULT 'patient';
```

---

## 📞 Hỗ trợ

Nếu có vấn đề, kiểm tra:
1. Database đã update schema chưa?
2. File Auth.php đã có method `isReceptionist()` chưa?
3. Clear cache trình duyệt
4. Kiểm tra log PHP: `xampp/apache/logs/error.log`

---

**Hoàn thành!** 🎉
