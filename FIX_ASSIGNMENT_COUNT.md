# ✅ FIX: HIỂN THỊ SỐ LỊCH PHÂN CÔNG CHÍNH XÁC

## 🎯 VẤN ĐỀ

**Theo hình ảnh:**
1. ❌ "Đã phân công thành công 23 lịch khám" → Số sai!
2. ✅ "Danh sách dịch vụ & lịch khám (0/28 đã phân công)" → Đúng nhưng không cập nhật

## 🔍 NGUYÊN NHÂN

1. **Thông báo không chính xác:**
   - Hardcode "23 lịch khám"
   - Không đếm số lịch thực tế được tạo

2. **Không tìm được bác sĩ:**
   - Có thể không có bác sĩ chuyên khoa phù hợp
   - Hoặc tất cả bác sĩ đều bận

## ✅ GIẢI PHÁP

### **1. Đếm chính xác số lịch được tạo:**

```php
$appointmentsCreated = 0;
$failedServices = [];

foreach ($packageServices as $service) {
    $doctor = $this->findSuitableDoctor($service, $startDate, $currentTime);
    
    if ($doctor) {
        if ($this->appointmentModel->create()) {
            $appointmentsCreated++; // ✅ Đếm thành công
        } else {
            $failedServices[] = $service['service_name']; // ❌ Lưu lỗi
        }
    } else {
        $failedServices[] = $service['service_name']; // ❌ Không tìm được BS
    }
}
```

---

### **2. Thông báo chi tiết:**

```php
$totalServices = count($packageServices);

if ($appointmentsCreated > 0) {
    if ($appointmentsCreated == $totalServices) {
        // ✅ Phân công đủ 100%
        $_SESSION['success'] = "✅ Đã phân công thành công {$appointmentsCreated}/{$totalServices} lịch khám";
    } else {
        // ⚠️ Phân công một phần
        $_SESSION['warning'] = "⚠️ Đã phân công {$appointmentsCreated}/{$totalServices} lịch khám. " . 
                               count($failedServices) . " dịch vụ chưa phân công được bác sĩ.";
    }
} else {
    // ❌ Không phân công được gì
    $_SESSION['error'] = '❌ Không thể phân công bác sĩ. Vui lòng kiểm tra lại lịch làm việc của bác sĩ.';
}
```

---

## 📊 KẾT QUẢ

### **Trường hợp 1: Phân công đủ 100%**
```
✅ Đã phân công thành công 28/28 lịch khám
```

### **Trường hợp 2: Phân công một phần**
```
⚠️ Đã phân công 23/28 lịch khám. 5 dịch vụ chưa phân công được bác sĩ.
```

### **Trường hợp 3: Không phân công được**
```
❌ Không thể phân công bác sĩ. Vui lòng kiểm tra lại lịch làm việc của bác sĩ.
```

---

## 🔧 NGUYÊN NHÂN KHÔNG TÌM ĐƯỢC BÁC SĨ

### **1. Không có bác sĩ chuyên khoa:**
```sql
-- Ví dụ: Tìm bác sĩ "Tai Mũi Họng" nhưng không có trong DB
SELECT * FROM doctors d
LEFT JOIN specializations s ON d.specialization_id = s.id
WHERE s.name = 'Tai Mũi Họng'
-- → Kết quả: 0 rows
```

**Giải pháp:** Fallback sang bác sĩ "Nội khoa"

---

### **2. Tất cả bác sĩ đều bận:**
```sql
-- Kiểm tra lịch trống
SELECT COUNT(*) FROM appointments
WHERE doctor_id = 5
AND appointment_date = '2025-11-05'
AND appointment_time = '08:00:00'
-- → Kết quả: 1 (Đã có lịch → Bận)
```

**Giải pháp:** Chuyển sang ngày hôm sau

---

## 🎯 CÁCH KIỂM TRA

### **Bước 1: Kiểm tra bác sĩ trong DB**
```sql
SELECT s.name, COUNT(d.id) as total_doctors
FROM specializations s
LEFT JOIN doctors d ON s.id = d.specialization_id
WHERE d.is_available = 1
GROUP BY s.id
ORDER BY total_doctors DESC;
```

**Kết quả mong đợi:**
| Chuyên khoa | Số bác sĩ |
|-------------|-----------|
| Nội khoa | 5 |
| Tim mạch | 3 |
| Mắt | 2 |
| ... | ... |

---

### **Bước 2: Kiểm tra lịch làm việc**
```sql
SELECT 
    d.doctor_code,
    u.full_name,
    COUNT(a.id) as total_appointments
FROM doctors d
LEFT JOIN users u ON d.user_id = u.id
LEFT JOIN appointments a ON d.id = a.doctor_id
WHERE a.appointment_date = '2025-11-05'
GROUP BY d.id
ORDER BY total_appointments ASC;
```

**Kết quả:** Bác sĩ nào có ít lịch nhất → Ưu tiên phân công

---

## 💡 GỢI Ý CẢI THIỆN

### **1. Thêm log chi tiết:**
```php
foreach ($packageServices as $service) {
    $doctor = $this->findSuitableDoctor($service, $startDate, $currentTime);
    
    if (!$doctor) {
        error_log("Không tìm được bác sĩ cho: " . $service['service_name']);
        error_log("Chuyên khoa yêu cầu: " . $this->findSpecializationForService($service['service_name']));
    }
}
```

---

### **2. Hiển thị danh sách dịch vụ thất bại:**
```php
if (count($failedServices) > 0) {
    $_SESSION['failed_services'] = $failedServices;
}

// Trong view:
<?php if (isset($_SESSION['failed_services'])): ?>
<div class="bg-yellow-50 p-4 rounded">
    <p class="font-semibold">Các dịch vụ chưa phân công:</p>
    <ul class="list-disc ml-6">
        <?php foreach ($_SESSION['failed_services'] as $service): ?>
        <li><?= $service ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php unset($_SESSION['failed_services']); ?>
<?php endif; ?>
```

---

## ✅ HOÀN THÀNH

- ✅ Đếm chính xác số lịch được tạo
- ✅ Thông báo chi tiết (success/warning/error)
- ✅ Lưu danh sách dịch vụ thất bại
- ✅ Fallback sang bác sĩ Nội khoa
- ✅ Thử ngày hôm sau nếu bận

---

**REFRESH VÀ TEST NGAY!** 🚀
