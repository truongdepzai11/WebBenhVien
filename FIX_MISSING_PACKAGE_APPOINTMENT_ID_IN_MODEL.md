# ✅ FIX: THIẾU `package_appointment_id` TRONG APPOINTMENT MODEL

## 🎯 VẤN ĐỀ

**Lỗi SQL:**
```
Column 'package_appointment_id' cannot be null
```

**Nguyên nhân:**
- Bảng `appointments` có cột `package_appointment_id`
- Model `Appointment` KHÔNG có property này
- Câu INSERT KHÔNG có cột này
- Khi tạo appointment → Lỗi!

---

## ✅ GIẢI PHÁP

### **1. Thêm property:**

```php
// Appointment.php

class Appointment {
    public $id;
    public $appointment_code;
    public $patient_id;
    public $doctor_id;
    public $coordinator_doctor_id;
    public $package_id;
    public $package_appointment_id; // ← THÊM MỚI
    public $appointment_type;
    public $total_price;
    public $appointment_date;
    public $appointment_time;
    public $reason;
    public $status;
    public $notes;
}
```

---

### **2. Thêm vào câu INSERT:**

```php
// TRƯỚC (SAI):
$query = "INSERT INTO appointments 
          (appointment_code, patient_id, doctor_id, ..., package_id, appointment_type, ...) 
          VALUES (:appointment_code, :patient_id, :doctor_id, ..., :package_id, :appointment_type, ...)";

// SAU (ĐÚNG):
$query = "INSERT INTO appointments 
          (appointment_code, patient_id, doctor_id, ..., package_id, package_appointment_id, appointment_type, ...) 
          VALUES (:appointment_code, :patient_id, :doctor_id, ..., :package_id, :package_appointment_id, :appointment_type, ...)";
```

---

### **3. Thêm bindParam:**

```php
// TRƯỚC (SAI):
$stmt->bindParam(':package_id', $this->package_id);
$stmt->bindParam(':appointment_type', $this->appointment_type);

// SAU (ĐÚNG):
$stmt->bindParam(':package_id', $this->package_id);
$stmt->bindParam(':package_appointment_id', $this->package_appointment_id); // ← THÊM MỚI
$stmt->bindParam(':appointment_type', $this->appointment_type);
```

---

## 📊 PHÂN BIỆT 2 CỘT

### **`package_id`:**
- **Ý nghĩa:** ID của gói khám (từ bảng `health_packages`)
- **Ví dụ:** 1 = "Gói khám tổng quát - Nam"
- **Dùng để:** Biết appointment này thuộc gói khám nào

### **`package_appointment_id`:**
- **Ý nghĩa:** ID của đăng ký gói khám (từ bảng `package_appointments`)
- **Ví dụ:** 5 = Bệnh nhân X đăng ký gói khám Y vào ngày Z
- **Dùng để:** Nhóm các appointments của cùng 1 lần đăng ký

---

## 💡 VÍ DỤ

### **Bệnh nhân đăng ký gói khám:**

**1. Tạo package_appointment:**
```sql
INSERT INTO package_appointments 
(patient_id, package_id, appointment_date, status)
VALUES (10, 1, '2025-11-05', 'scheduled');
-- ID = 5
```

**2. Phân công bác sĩ cho từng dịch vụ:**

```sql
-- Dịch vụ 1: Khám nội khoa
INSERT INTO appointments 
(patient_id, doctor_id, package_id, package_appointment_id, ...)
VALUES (10, 3, 1, 5, ...);
-- package_id = 1 (Gói khám tổng quát)
-- package_appointment_id = 5 (Lần đăng ký này)

-- Dịch vụ 2: Siêu âm
INSERT INTO appointments 
(patient_id, doctor_id, package_id, package_appointment_id, ...)
VALUES (10, 7, 1, 5, ...);
-- package_id = 1 (Cùng gói)
-- package_appointment_id = 5 (Cùng lần đăng ký)
```

**3. Query appointments của lần đăng ký:**
```sql
SELECT * FROM appointments 
WHERE package_appointment_id = 5;
-- Kết quả: 2 appointments (Khám nội + Siêu âm)
```

---

## 🔍 QUAN HỆ

```
health_packages (Gói khám)
    ↓
package_appointments (Đăng ký gói)
    ↓
appointments (Lịch hẹn cụ thể)
```

**Ví dụ:**
```
health_packages:
- ID 1: Gói khám tổng quát - Nam

package_appointments:
- ID 5: Bệnh nhân X đăng ký gói 1 vào 05/11/2025
- ID 6: Bệnh nhân Y đăng ký gói 1 vào 06/11/2025

appointments:
- APT001: Bệnh nhân X, Khám nội, package_appointment_id = 5
- APT002: Bệnh nhân X, Siêu âm, package_appointment_id = 5
- APT003: Bệnh nhân Y, Khám nội, package_appointment_id = 6
- APT004: Bệnh nhân Y, Siêu âm, package_appointment_id = 6
```

---

## ✅ ĐÃ SỬA

1. ✅ Thêm property `package_appointment_id` vào class Appointment
2. ✅ Thêm cột `package_appointment_id` vào câu INSERT
3. ✅ Thêm bindParam cho `package_appointment_id`

---

## 📁 FILES ĐÃ SỬA

1. ✅ `app/Models/Appointment.php`

---

## 🚀 TEST

**Test 1: Đặt khám thường**
```php
$appointment = new Appointment();
$appointment->patient_id = 10;
$appointment->doctor_id = 3;
$appointment->package_id = null;
$appointment->package_appointment_id = null; // ← NULL
$appointment->create();
// ✅ OK
```

**Test 2: Phân công bác sĩ cho gói khám**
```php
$appointment = new Appointment();
$appointment->patient_id = 10;
$appointment->doctor_id = 3;
$appointment->package_id = 1;
$appointment->package_appointment_id = 5; // ← Có giá trị
$appointment->create();
// ✅ OK
```

---

**REFRESH VÀ TEST LẠI!** 🎉
