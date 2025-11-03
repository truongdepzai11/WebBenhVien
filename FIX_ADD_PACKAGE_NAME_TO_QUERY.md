# ✅ FIX: THÊM package_name VÀO QUERY

## 🎯 VẤN ĐỀ

**Lỗi:**
```
Undefined array key "package_name"
```

**Nguyên nhân:**
- View cần hiển thị `$appointment['package_name']`
- Nhưng query KHÔNG JOIN với bảng `health_packages`
- Kết quả không có field `package_name`

---

## ✅ GIẢI PHÁP

### **Thêm JOIN với bảng health_packages:**

```php
// Appointment::findById()

// TRƯỚC (SAI):
SELECT a.*, 
       p.patient_code, pu.full_name as patient_name,
       d.doctor_code, du.full_name as doctor_name,
       s.name as specialization
FROM appointments a
LEFT JOIN patients p ON a.patient_id = p.id
LEFT JOIN doctors d ON a.doctor_id = d.id
LEFT JOIN specializations s ON d.specialization_id = s.id
WHERE a.id = :id

// SAU (ĐÚNG):
SELECT a.*, 
       p.patient_code, pu.full_name as patient_name,
       d.doctor_code, du.full_name as doctor_name,
       s.name as specialization,
       hp.name as package_name, hp.price as package_price ← THÊM MỚI
FROM appointments a
LEFT JOIN patients p ON a.patient_id = p.id
LEFT JOIN doctors d ON a.doctor_id = d.id
LEFT JOIN specializations s ON d.specialization_id = s.id
LEFT JOIN health_packages hp ON a.package_id = hp.id ← THÊM MỚI
WHERE a.id = :id
```

---

## 📊 KẾT QUẢ

### **Khám thường (package_id = NULL):**
```php
$appointment = [
    'id' => 100,
    'patient_name' => 'Nguyễn Văn A',
    'doctor_name' => 'BS. Trần',
    'package_name' => NULL, // ← NULL vì không có gói
    'package_price' => NULL
];
```

### **Khám theo gói (package_id = 1):**
```php
$appointment = [
    'id' => 262,
    'patient_name' => 'huy le',
    'doctor_name' => NULL,
    'package_name' => 'Gói khám sức khỏe tổng quát - Nam', // ← CÓ GIÁ TRỊ
    'package_price' => 6680000
];
```

---

## 💡 SỬ DỤNG TRONG VIEW

```php
// show.php

<?php if ($appointment['appointment_type'] === 'package'): ?>
    <p><strong>Gói khám:</strong> 
        <?= htmlspecialchars($appointment['package_name'] ?? 'Gói khám sức khỏe') ?>
    </p>
    <p><strong>Tổng giá trị:</strong> 
        <?= number_format($appointment['total_price']) ?> VNĐ
    </p>
<?php endif; ?>
```

---

## ✅ ĐÃ SỬA

1. ✅ Thêm JOIN với `health_packages`
2. ✅ SELECT `hp.name as package_name`
3. ✅ SELECT `hp.price as package_price`

---

## 📁 FILES ĐÃ SỬA

1. ✅ `app/Models/Appointment.php` - Method `findById()`

---

## 🚀 TEST

### **Test 1: Xem chi tiết khám thường**
```
1. Vào /appointments/100
2. Kết quả:
   - ✅ Hiển thị thông tin bác sĩ
   - ✅ package_name = NULL (không lỗi)
```

### **Test 2: Xem chi tiết khám gói**
```
1. Vào /appointments/262
2. Kết quả:
   - ✅ Hiển thị thông tin gói khám
   - ✅ package_name = "Gói khám sức khỏe tổng quát - Nam"
   - ✅ KHÔNG lỗi "Undefined array key"
```

---

**REFRESH VÀ XEM!** 🎉
