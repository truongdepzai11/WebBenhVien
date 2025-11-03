# ✅ BƯỚC 1: THÊM CỘT PACKAGE_APPOINTMENT_ID

## 🎯 MỤC ĐÍCH

Liên kết các appointment (lịch hẹn từng dịch vụ) với package_appointment (đăng ký gói khám tổng thể).

---

## 📊 CẤU TRÚC DỮ LIỆU

### **Trước:**
```
package_appointments (Đăng ký gói)
    ↓ (không liên kết trực tiếp)
appointments (Lịch hẹn từng dịch vụ)
```

### **Sau:**
```
package_appointments (1)
    ↓ package_appointment_id
appointments (N)
```

---

## 🔧 THAY ĐỔI DATABASE

### **Bảng `appointments` - Cột mới:**

| Column | Type | Null | Description |
|--------|------|------|-------------|
| `package_appointment_id` | INT(11) | YES | ID của đăng ký gói khám (nếu appointment này thuộc 1 gói) |

### **Foreign Key:**
```sql
FOREIGN KEY (package_appointment_id) 
REFERENCES package_appointments(id) 
ON DELETE CASCADE
```

→ Khi xóa package_appointment, tất cả appointments liên quan cũng bị xóa.

---

## 💡 CÁCH SỬ DỤNG

### **Khi tạo đăng ký gói khám:**

```php
// 1. Tạo package_appointment
$packageAppointment = new PackageAppointment();
$packageAppointment->patient_id = $patientId;
$packageAppointment->package_id = $packageId;
$packageAppointment->create();

// 2. Lấy danh sách dịch vụ trong gói
$services = getPackageServices($packageId);

// 3. Tạo appointment cho từng dịch vụ
foreach ($services as $service) {
    $appointment = new Appointment();
    $appointment->patient_id = $patientId;
    $appointment->package_appointment_id = $packageAppointment->id; // ← LIÊN KẾT
    $appointment->doctor_id = assignDoctor($service);
    $appointment->appointment_type = 'package';
    $appointment->create();
}
```

---

## 📋 VÍ DỤ DỮ LIỆU

### **package_appointments:**
| id | patient_id | package_id | appointment_date | status |
|----|------------|------------|------------------|--------|
| 1 | 10 | 2 | 2025-11-05 | scheduled |

### **appointments (liên kết với package_appointment #1):**
| id | patient_id | doctor_id | package_appointment_id | appointment_type | reason |
|----|------------|-----------|------------------------|------------------|--------|
| 101 | 10 | 5 | **1** | package | Khám nội tổng quát |
| 102 | 10 | 6 | **1** | package | Khám mắt |
| 103 | 10 | 7 | **1** | package | Khám tai mũi họng |
| ... | ... | ... | **1** | package | ... |

→ Tất cả 15 dịch vụ trong gói đều có `package_appointment_id = 1`

---

## 🔍 QUERY HỮU ÍCH

### **Lấy tất cả appointments của 1 gói khám:**
```sql
SELECT a.*, d.full_name as doctor_name, s.service_name
FROM appointments a
LEFT JOIN doctors d ON a.doctor_id = d.id
LEFT JOIN package_services ps ON a.package_id = ps.package_id
LEFT JOIN services s ON ps.service_id = s.id
WHERE a.package_appointment_id = 1
ORDER BY a.appointment_date, a.appointment_time;
```

### **Thống kê tiến độ gói khám:**
```sql
SELECT 
    pa.id,
    COUNT(a.id) as total_appointments,
    SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN a.status = 'pending' THEN 1 ELSE 0 END) as pending
FROM package_appointments pa
LEFT JOIN appointments a ON pa.id = a.package_appointment_id
WHERE pa.id = 1
GROUP BY pa.id;
```

---

## ✅ HOÀN THÀNH

- ✅ Thêm cột `package_appointment_id` vào bảng `appointments`
- ✅ Tạo foreign key constraint
- ✅ Kiểm tra cấu trúc database

---

## 🚀 BƯỚC TIẾP THEO

**Bước 2:** Tạo Controller & Views để:
- Hiển thị danh sách đăng ký gói khám
- Chi tiết gói khám với danh sách appointments
- Phân công bác sĩ tự động

Sẵn sàng cho bước 2? 🎯
