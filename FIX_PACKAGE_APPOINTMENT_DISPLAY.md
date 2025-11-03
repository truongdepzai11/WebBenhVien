# ✅ FIX: HIỂN THỊ GÓI KHÁM TRONG LỊCH HẸN

## 🎯 VẤN ĐỀ

**Trước khi fix:**
- ❌ Hiện 19 dòng riêng lẻ cho 19 dịch vụ trong gói khám
- ❌ Không phân biệt được "Khám thường" vs "Khám theo gói"
- ❌ Danh sách lịch hẹn quá dài, khó quản lý

**Yêu cầu:**
- ✅ Chỉ hiện **1 dòng** cho mỗi gói khám
- ✅ Hiện "Khám theo gói" để phân biệt
- ✅ Click vào → Xem chi tiết tất cả dịch vụ trong gói

---

## ✅ GIẢI PHÁP

### **1. Lọc appointments trong Controller:**

```php
// AppointmentController::index()

// Lọc ra chỉ appointments KHÔNG thuộc gói khám
$regularAppointments = array_filter($appointments, function($apt) {
    return empty($apt['package_appointment_id']);
});

// Lấy danh sách gói khám
$packageAppointmentModel = new PackageAppointment();
$packageAppointments = $packageAppointmentModel->getAll();
```

---

### **2. Hiển thị 2 loại trong View:**

```php
<!-- Hiển thị gói khám trước (màu tím) -->
<?php foreach ($packageAppointments as $pkg): ?>
<tr class="bg-purple-50">
    <td>#PKG<?= $pkg['id'] ?></td>
    <td><?= $pkg['patient_name'] ?></td>
    <td>
        <i class="fas fa-box-open"></i> Khám theo gói
        <br><?= $pkg['package_name'] ?>
    </td>
    <td><?= $pkg['appointment_date'] ?></td>
    <td>Nhiều dịch vụ</td>
    <td><?= $pkg['status'] ?></td>
    <td>
        <a href="/package-appointments/<?= $pkg['id'] ?>">
            <i class="fas fa-eye"></i>
        </a>
    </td>
</tr>
<?php endforeach; ?>

<!-- Hiển thị appointments thường -->
<?php foreach ($regularAppointments as $apt): ?>
<tr>
    <td><?= $apt['appointment_code'] ?></td>
    <td><?= $apt['patient_name'] ?></td>
    <td><?= $apt['doctor_name'] ?></td>
    <td><?= $apt['appointment_date'] ?></td>
    <td><?= $apt['appointment_time'] ?></td>
    <td><?= $apt['reason'] ?></td>
    <td><?= $apt['status'] ?></td>
</tr>
<?php endforeach; ?>
```

---

## 📊 KẾT QUẢ

### **Trước:**
```
Quản lý Lịch hẹn
┌─────────────────────────────────────────┐
│ APT00245 | huy le | BS. Trần | 13/11   │ ← Gói khám dịch vụ 1
│ APT00244 | huy le | BS. Trần | 13/11   │ ← Gói khám dịch vụ 2
│ APT00243 | huy le | BS. Trần | 13/11   │ ← Gói khám dịch vụ 3
│ ... (16 dòng nữa)                       │
│ APT00001 | Nguyễn A | BS. X | 05/11    │ ← Khám thường
└─────────────────────────────────────────┘
```

### **Sau:**
```
Quản lý Lịch hẹn
┌─────────────────────────────────────────┐
│ #PKG1 | huy le | 📦 Khám theo gói      │ ← 1 dòng cho cả gói
│       | Gói khám tổng quát - Nam        │
│       | 13/11 | Nhiều dịch vụ          │
├─────────────────────────────────────────┤
│ APT00001 | Nguyễn A | BS. X | 05/11    │ ← Khám thường
│ APT00002 | Trần B | BS. Y | 06/11      │ ← Khám thường
└─────────────────────────────────────────┘
```

---

## 🎨 THIẾT KẾ

### **Gói khám (màu tím):**
- Background: `bg-purple-50`
- Icon: `fas fa-box-open`
- Text: "Khám theo gói"
- Link: `/package-appointments/{id}`

### **Khám thường (màu trắng):**
- Background: `bg-white`
- Hiển thị bình thường
- Link: `/appointments/{id}`

---

## 🔗 FLOW

### **1. Xem danh sách:**
```
User → /appointments
    ↓
Thấy:
- 1 dòng gói khám (tím)
- N dòng khám thường (trắng)
```

### **2. Click vào gói khám:**
```
User → Click #PKG1
    ↓
Redirect: /package-appointments/1
    ↓
Thấy:
- Thông tin gói khám
- 28 dịch vụ chi tiết
- 19/28 đã phân công bác sĩ
```

### **3. Click vào khám thường:**
```
User → Click APT00001
    ↓
Redirect: /appointments/1
    ↓
Thấy:
- Chi tiết 1 lịch hẹn
- Thông tin bác sĩ, bệnh nhân
```

---

## 💡 LỢI ÍCH

✅ **Gọn gàng:** 1 dòng thay vì 19 dòng
✅ **Rõ ràng:** Phân biệt được loại khám
✅ **Dễ quản lý:** Không bị rối khi có nhiều gói khám
✅ **UX tốt:** Click vào mới xem chi tiết

---

## 📁 FILES ĐÃ SỬA

1. ✅ `AppointmentController.php` - Lọc appointments + Load gói khám
2. ✅ `appointments/index.php` - Hiển thị 2 loại riêng biệt

---

## 🚀 TEST

**Bước 1:** Vào `/appointments`

**Kết quả mong đợi:**
- ✅ Thấy 1 dòng màu tím: "#PKG1 | huy le | 📦 Khám theo gói"
- ✅ Thấy các dòng khám thường bình thường
- ✅ KHÔNG thấy 19 dòng riêng lẻ nữa

**Bước 2:** Click vào #PKG1

**Kết quả:**
- ✅ Redirect đến `/package-appointments/1`
- ✅ Thấy chi tiết 28 dịch vụ
- ✅ Thấy 19/28 đã phân công

---

**REFRESH VÀ TEST NGAY!** 🎉
