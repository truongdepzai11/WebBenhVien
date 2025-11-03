# 🗑️ HƯỚNG DẪN XÓA APPOINTMENTS

## 🎯 MỤC ĐÍCH

Xóa tất cả dữ liệu trong bảng `appointments` để test lại từ đầu.

---

## ⚠️ CẢNH BÁO

**XÓA DỮ LIỆU KHÔNG THỂ KHÔI PHỤC!**

Chỉ làm điều này khi:
- ✅ Đang trong môi trường development/test
- ✅ Muốn reset toàn bộ lịch hẹn
- ✅ Đã backup dữ liệu (nếu cần)

---

## 🔧 CÁCH 1: QUA PHPMYADMIN (ĐỀ XUẤT)

### **Bước 1:** Mở phpMyAdmin
```
http://localhost/phpmyadmin
```

### **Bước 2:** Chọn database
```
Click vào: hospital_management
```

### **Bước 3:** Chạy SQL
```
1. Click tab "SQL"
2. Copy paste đoạn SQL dưới đây:
```

```sql
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM appointments;
ALTER TABLE appointments AUTO_INCREMENT = 1;
SET FOREIGN_KEY_CHECKS = 1;
SELECT COUNT(*) as total_appointments FROM appointments;
```

```
3. Click "Go"
4. Kết quả: total_appointments = 0
```

---

## 🔧 CÁCH 2: QUA COMMAND LINE

### **Windows:**

```powershell
cd c:\xampp\htdocs\WebBenhvien\hospital-management-system
c:\xampp\mysql\bin\mysql.exe -u root hospital_management < sql\delete_all_appointments.sql
```

### **Hoặc trực tiếp:**

```powershell
c:\xampp\mysql\bin\mysql.exe -u root hospital_management -e "DELETE FROM appointments; ALTER TABLE appointments AUTO_INCREMENT = 1;"
```

---

## 🔧 CÁCH 3: QUA PHP SCRIPT

Tạo file `delete_appointments.php`:

```php
<?php
require_once __DIR__ . '/config/database.php';

$database = new Database();
$conn = $database->getConnection();

try {
    // Tắt foreign key checks
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Xóa tất cả appointments
    $stmt = $conn->prepare("DELETE FROM appointments");
    $stmt->execute();
    
    // Reset AUTO_INCREMENT
    $conn->exec("ALTER TABLE appointments AUTO_INCREMENT = 1");
    
    // Bật lại foreign key checks
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    // Kiểm tra
    $stmt = $conn->query("SELECT COUNT(*) as total FROM appointments");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "✅ Đã xóa thành công!\n";
    echo "Tổng appointments còn lại: " . $result['total'] . "\n";
    
} catch (PDOException $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
}
?>
```

**Chạy:**
```powershell
php delete_appointments.php
```

---

## 🛡️ TẠI SAO CẦN `SET FOREIGN_KEY_CHECKS = 0`?

Bảng `appointments` có foreign key liên kết với:
- `patients` (patient_id)
- `doctors` (doctor_id)
- `package_appointments` (package_appointment_id)

Nếu không tắt foreign key checks, MySQL sẽ báo lỗi khi xóa.

---

## ✅ KIỂM TRA SAU KHI XÓA

### **1. Kiểm tra số lượng:**
```sql
SELECT COUNT(*) FROM appointments;
-- Kết quả: 0
```

### **2. Kiểm tra AUTO_INCREMENT:**
```sql
SHOW TABLE STATUS LIKE 'appointments';
-- Auto_increment: 1
```

### **3. Kiểm tra trong app:**
```
Vào: /appointments
Kết quả: "Chưa có lịch hẹn nào"
```

---

## 🔄 TEST LẠI TỪ ĐẦU

### **1. Đặt lịch khám thường:**
```
/appointments/create
→ Điền form
→ Submit
→ Kiểm tra: /appointments
```

### **2. Đặt lịch khám theo gói:**
```
/schedule → Khám theo gói
→ Chọn bệnh nhân + gói khám
→ Submit
→ Vào: /package-appointments/1
→ Phân công bác sĩ thủ công
→ Kiểm tra: /appointments
```

---

## 🚨 LƯU Ý

### **KHÔNG xóa các bảng khác:**
- ❌ KHÔNG xóa `package_appointments` (đăng ký gói khám)
- ❌ KHÔNG xóa `patients` (bệnh nhân)
- ❌ KHÔNG xóa `doctors` (bác sĩ)
- ❌ KHÔNG xóa `health_packages` (gói khám)

### **Chỉ xóa `appointments`:**
- ✅ Xóa lịch hẹn cụ thể
- ✅ Giữ nguyên master data

---

## 📊 SO SÁNH

### **Trước khi xóa:**
```sql
SELECT COUNT(*) FROM appointments;
-- Kết quả: 19 (hoặc nhiều hơn)
```

### **Sau khi xóa:**
```sql
SELECT COUNT(*) FROM appointments;
-- Kết quả: 0
```

---

## 🎉 HOÀN THÀNH

Bây giờ bạn có thể:
- ✅ Test lại từ đầu
- ✅ Phân công bác sĩ thủ công
- ✅ Không bị tự động tạo appointments

---

**FILE SQL:** `sql/delete_all_appointments.sql`
