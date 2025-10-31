# ✅ FIX CUỐI CÙNG - ĐÚNG YÊU CẦU!

## 🎯 FLOW MỚI (ĐÚNG 100%)

### **Trang /schedule (Đăng ký khám Walk-in)**

**MẶC ĐỊNH (Khi vào trang):**
```
┌─────────────────────────────────────────┐
│ Đăng ký khám Walk-in                    │
├─────────────────────────────────────────┤
│ Chọn loại khám: *                       │
│                                         │
│ ┌─────────────────────────────────────┐ │
│ │ ○ Khám thường                       │ │
│ │   Đăng ký khám bệnh với bác sĩ      │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ ┌─────────────────────────────────────┐ │
│ │ ○ Khám theo gói                     │ │
│ │   Đăng ký gói khám sức khỏe         │ │
│ └─────────────────────────────────────┘ │
└─────────────────────────────────────────┘

KHÔNG hiện lịch bác sĩ!
KHÔNG hiện form đăng ký!
CHỈ có 2 radio button!
```

---

### **Khi chọn "Khám thường"**
```
┌─────────────────────────────────────────┐
│ Đăng ký khám Walk-in                    │
├─────────────────────────────────────────┤
│ Chọn loại khám: *                       │
│ ● Khám thường  ○ Khám theo gói         │
├─────────────────────────────────────────┤
│ Bác sĩ: [BS.Vanh Le - Mắt ▼]           │
│ Ngày: [31/10/2025]  [Hôm nay]          │
├─────────────────────────────────────────┤
│ BS.Vanh Le                           31 │
│ Mắt                       Tháng 10/2025 │
├─────────────────────────────────────────┤
│ 08:00  Đã qua (không thể đặt)           │
│ 09:00  Đã qua (không thể đặt)           │
│ 10:00  Slot trống                       │
│        [+ Thêm bệnh nhân]               │
│ 11:00  Slot trống                       │
│        [+ Thêm bệnh nhân]               │
└─────────────────────────────────────────┘

✅ Hiện lịch bác sĩ
✅ Hiện nút "Thêm bệnh nhân"
```

---

### **Khi chọn "Khám theo gói"**
```
┌─────────────────────────────────────────┐
│ Đăng ký khám Walk-in                    │
├─────────────────────────────────────────┤
│ Chọn loại khám: *                       │
│ ○ Khám thường  ● Khám theo gói         │
├─────────────────────────────────────────┤
│ Chọn gói khám: *                        │
│ [Gói khám tổng quát - Nam ▼]           │
│                                         │
│ ┌─────────────────────────────────────┐ │
│ │ Gói khám sức khỏe tổng quát - Nam   │ │
│ │ ✓ Điện tim ECG                      │ │
│ │ ✓ Xét nghiệm máu                    │ │
│ │ ✓ Khám nội khoa                     │ │
│ │ Tổng: 6,180,000 đ                   │ │
│ └─────────────────────────────────────┘ │
├─────────────────────────────────────────┤
│ Loại bệnh nhân: *                       │
│ ● Bệnh nhân cũ  ○ Bệnh nhân mới        │
│                                         │
│ Chọn bệnh nhân: *                       │
│ [Nguyễn Văn A (BN001) ▼]               │
│                                         │
│ Ngày khám dự kiến: *                    │
│ [31/10/2025]                            │
│                                         │
│ Lý do khám / Ghi chú:                   │
│ [_________________________________]     │
│                                         │
│ [Hủy] [Xác nhận đăng ký]               │
└─────────────────────────────────────────┘

✅ Hiện form đăng ký gói
❌ ẨN lịch bác sĩ
```

---

## ✅ ĐÃ SỬA

### **1. File: `app/Views/schedule/index.php`**

**Thêm vào đầu (sau tiêu đề):**
```php
<!-- CHỌN LOẠI KHÁM (CHỈ CHO RECEPTIONIST) -->
<?php if (Auth::isReceptionist()): ?>
<div id="appointmentTypeSelector">
    <div class="grid grid-cols-2 gap-4">
        <!-- Khám thường -->
        <div onclick="selectAppointmentType('regular')">
            ● Khám thường
        </div>
        
        <!-- Khám theo gói -->
        <div onclick="selectAppointmentType('package')">
            ○ Khám theo gói
        </div>
    </div>
</div>
<?php endif; ?>
```

**Wrap lịch bác sĩ:**
```php
<!-- LỊCH BÁC SĨ (ẨN MẶC ĐỊNH CHO RECEPTIONIST) -->
<div id="scheduleSection" class="<?= Auth::isReceptionist() ? 'hidden' : '' ?>">
    <!-- Tất cả code lịch bác sĩ -->
</div>
```

**Thêm form đăng ký gói:**
```php
<!-- FORM ĐĂNG KÝ GÓI (ẨN MẶC ĐỊNH) -->
<?php if (Auth::isReceptionist()): ?>
<div id="packageSection" class="hidden">
    <?php require_once APP_PATH . '/Views/schedule/register_package.php'; ?>
</div>
<?php endif; ?>
```

**JavaScript toggle:**
```javascript
function selectAppointmentType(type) {
    if (type === 'regular') {
        scheduleSection.classList.remove('hidden');
        packageSection.classList.add('hidden');
    } else {
        scheduleSection.classList.add('hidden');
        packageSection.classList.remove('hidden');
    }
}
```

---

## 📊 SO SÁNH

### **Trước (Sai):**
```
Vào /schedule:
✓ Hiện lịch bác sĩ ngay ← SAI!
✓ Không có chọn loại ← SAI!
```

### **Sau (Đúng):**
```
Vào /schedule:
✓ Hiện 2 radio button chọn loại ← ĐÚNG!
✓ Lịch bác sĩ ẨN mặc định ← ĐÚNG!
✓ Click "Khám thường" → Hiện lịch ← ĐÚNG!
✓ Click "Khám theo gói" → Hiện form gói ← ĐÚNG!
```

---

## 🚀 TEST NGAY

### **Test 1: Vào trang**
```
1. Login Receptionist
2. Vào /schedule
3. Kết quả:
   ✅ Thấy 2 radio button
   ✅ KHÔNG thấy lịch bác sĩ
   ✅ KHÔNG thấy form đăng ký
```

### **Test 2: Chọn "Khám thường"**
```
1. Click radio "Khám thường"
2. Kết quả:
   ✅ Lịch bác sĩ hiện ra
   ✅ Form gói ẨN đi
```

### **Test 3: Chọn "Khám theo gói"**
```
1. Click radio "Khám theo gói"
2. Kết quả:
   ✅ Form đăng ký gói hiện ra
   ✅ Lịch bác sĩ ẨN đi
```

---

## 📁 FILES ĐÃ SỬA

1. ✅ `app/Views/schedule/index.php` - Thêm chọn loại + toggle
2. ✅ `app/Views/schedule/register_package.php` - Form đăng ký gói (đã tạo trước)

---

## 💡 LOGIC

### **Khi vào trang /schedule:**
```
if (Receptionist) {
    Hiện: 2 radio button
    Ẩn: Lịch bác sĩ
    Ẩn: Form gói
}
```

### **Khi click "Khám thường":**
```
Hiện: Lịch bác sĩ (scheduleSection)
Ẩn: Form gói (packageSection)
```

### **Khi click "Khám theo gói":**
```
Ẩn: Lịch bác sĩ (scheduleSection)
Hiện: Form gói (packageSection)
```

---

**Hard refresh (Ctrl+Shift+R) và test ngay!** 🚀

Kết quả mong đợi:
- ✅ Vào trang → Chỉ thấy 2 radio button
- ✅ Click "Khám thường" → Hiện lịch bác sĩ
- ✅ Click "Khám theo gói" → Hiện form đăng ký gói
