# ✅ FIX: HIỂN THỊ BÁC SĨ ĐÃ PHÂN CÔNG

## 🎯 VẤN ĐỀ

**Theo hình ảnh:**
1. ❌ Lịch hẹn có 19 appointments với bác sĩ "BS. Trần Thị B"
2. ❌ Nhưng trong chi tiết gói khám vẫn hiện "Chưa phân công bác sĩ"
3. ❌ Số đếm sai: "0/28 đã phân công" thay vì "19/28"

## 🔍 NGUYÊN NHÂN

### **1. Logic mapping không khớp:**

```php
// Code cũ (SAI):
$appointmentMap[$apt['reason']] = $apt;
$hasAppointment = isset($appointmentMap[$service['service_name']]);
```

**Vấn đề:**
- Service name: "Chụp X quang ngực thẳng số hóa II (phim)"
- Appointment reason: "Chụp X quang ngực thẳng số hóa II (phim)"
- Phải khớp 100% → Nếu có khoảng trắng thừa, ký tự đặc biệt → KHÔNG khớp!

---

### **2. Appointments không được load:**

Có thể `getByPackageAppointmentId()` trả về rỗng vì:
- `package_appointment_id` không được set khi tạo
- Hoặc query sai

---

## ✅ GIẢI PHÁP

### **1. Match linh hoạt hơn:**

```php
// Tạo map với nhiều cách match
$appointmentMap = [];
foreach ($appointments as $apt) {
    // Match chính xác
    $appointmentMap[$apt['reason']] = $apt;
    
    // Match lowercase + trim
    $cleanReason = strtolower(trim($apt['reason']));
    $appointmentMap[$cleanReason] = $apt;
}

// Thử match theo thứ tự
foreach ($packageServices as $service) {
    // 1. Match chính xác
    $hasAppointment = isset($appointmentMap[$service['service_name']]);
    
    // 2. Match lowercase
    if (!$hasAppointment) {
        $cleanServiceName = strtolower(trim($service['service_name']));
        $hasAppointment = isset($appointmentMap[$cleanServiceName]);
    }
    
    // 3. Match chứa chuỗi (LIKE)
    if (!$hasAppointment) {
        foreach ($appointments as $apt) {
            if (stripos($apt['reason'], $service['service_name']) !== false) {
                $hasAppointment = true;
                $appointment = $apt;
                break;
            }
        }
    }
}
```

---

### **2. Debug appointments:**

```php
// Thêm log để kiểm tra
error_log("Package Appointment ID: " . $id);
error_log("Total appointments found: " . count($appointments));

// Kiểm tra từng appointment
foreach ($appointments as $apt) {
    error_log("Appointment reason: " . $apt['reason']);
}
```

---

### **3. Kiểm tra database:**

```sql
-- Xem appointments của gói khám #1
SELECT 
    a.id,
    a.reason,
    a.package_appointment_id,
    u.full_name as doctor_name
FROM appointments a
LEFT JOIN doctors d ON a.doctor_id = d.id
LEFT JOIN users u ON d.user_id = u.id
WHERE a.package_appointment_id = 1;
```

**Kết quả mong đợi:** 19 rows

---

## 🚀 CÁCH TEST

### **Bước 1: Refresh trang chi tiết**
```
http://localhost/.../package-appointments/1
```

### **Bước 2: Kiểm tra console log**
```
F12 → Console → Xem error log
```

### **Bước 3: Kiểm tra PHP error log**
```
c:\xampp\apache\logs\error.log
```

Tìm dòng:
```
Package Appointment ID: 1
Total appointments found: 19
```

---

## 💡 GIẢI PHÁP DÀI HẠN

### **Thêm cột `service_id` vào bảng `appointments`:**

```sql
ALTER TABLE `appointments` 
ADD COLUMN `service_id` INT(11) NULL AFTER `package_appointment_id`,
ADD KEY `fk_appointments_service` (`service_id`);
```

**Lợi ích:**
- Match chính xác 100%
- Không phụ thuộc vào string
- Dễ query, dễ maintain

**Khi tạo appointment:**
```php
$this->appointmentModel->service_id = $service['id']; // ← Link trực tiếp
$this->appointmentModel->reason = $service['service_name'];
```

**Khi hiển thị:**
```php
// Match theo service_id thay vì reason
$appointmentMap = [];
foreach ($appointments as $apt) {
    if ($apt['service_id']) {
        $appointmentMap[$apt['service_id']] = $apt;
    }
}

foreach ($packageServices as $service) {
    $hasAppointment = isset($appointmentMap[$service['id']]);
}
```

---

## ✅ ĐÃ SỬA

1. ✅ Thêm logic match linh hoạt (3 cách)
2. ✅ Thêm debug log
3. ✅ Cải thiện hiển thị

---

## 📊 KẾT QUẢ MONG ĐỢI

**Sau khi refresh:**

```
Danh sách dịch vụ & lịch khám (19/28 đã phân công)

✅ 1. Chụp X quang ngực...
   Đã phân công
   Bác sĩ: BS. Trần Thị B
   Ngày: 01/11/2025
   Giờ: 08:00

✅ 2. Siêu âm ổ bụng...
   Đã phân công
   Bác sĩ: BS. Trần Thị B
   ...

⏳ 20. Điện tim...
   Chưa phân công bác sĩ
```

---

**REFRESH VÀ KIỂM TRA LOG!** 🚀
