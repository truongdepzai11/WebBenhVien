# ✅ FIX CUỐI CÙNG - FORM WALK-IN

## 🎯 YÊU CẦU CUỐI CÙNG

### **Khi chọn "Khám thường":**
```
Loại khám: ● Khám thường ○ Khám theo gói

↓ Hiện:

Loại bệnh nhân: ● Bệnh nhân cũ ○ Bệnh nhân mới
Chọn bệnh nhân: [Dropdown ▼]
Lý do khám: [____________]
```

### **Khi chọn "Khám theo gói":**
```
Loại khám: ○ Khám thường ● Khám theo gói

↓ Hiện:

Chọn gói khám: [Dropdown ▼]
Lý do khám: [____________]

↓ ẨN:

Loại bệnh nhân (KHÔNG hiện)
Chọn bệnh nhân (KHÔNG hiện)
```

---

## ✅ ĐÃ FIX

### **1. Wrap "Loại bệnh nhân" trong div**
```php
<div id="patientTypeSelection">
    <!-- Loại bệnh nhân -->
    <!-- Chọn bệnh nhân cũ -->
    <!-- Form bệnh nhân mới -->
</div>
```

### **2. JavaScript toggle**
```javascript
if (appointmentType === 'package') {
    // Hiện gói, ẨN bệnh nhân
    packageSelection.classList.remove('hidden');
    patientTypeSelection.classList.add('hidden');
} else {
    // ẨN gói, Hiện bệnh nhân
    packageSelection.classList.add('hidden');
    patientTypeSelection.classList.remove('hidden');
}
```

---

## 📊 FLOW ĐÚNG

### **Flow 1: Khám thường**
```
1. Click "Thêm bệnh nhân" (nút xanh lá)
2. Form hiện:
   ● Khám thường
   ○ Khám theo gói
   
3. Hiện:
   ● Bệnh nhân cũ ○ Bệnh nhân mới
   [Chọn bệnh nhân ▼]
   [Lý do khám ___]
   
4. Submit → Tạo appointment thường
```

### **Flow 2: Khám theo gói**
```
1. Click "Thêm bệnh nhân" (nút xanh lá)
2. Form hiện:
   ○ Khám thường
   ● Khám theo gói  ← Click vào đây
   
3. Hiện:
   [Chọn gói khám ▼]
   [Lý do khám ___]
   
4. Chọn gói → Xem dịch vụ + giá
5. Submit → Tạo appointment gói
```

---

## 📸 SCREENSHOT MONG ĐỢI

### **Khám thường:**
```
┌─────────────────────────────────┐
│ Thêm Bệnh nhân Walk-in          │
├─────────────────────────────────┤
│ Bác sĩ: BS.Vanh Le - Mắt        │
│ Thời gian: 31/10/2025 - 12:00   │
├─────────────────────────────────┤
│ Loại khám: *                    │
│ ● Khám thường                   │
│ ○ Khám theo gói                 │
├─────────────────────────────────┤
│ Loại bệnh nhân: *               │
│ ● Bệnh nhân cũ                  │
│ ○ Bệnh nhân mới                 │
├─────────────────────────────────┤
│ Chọn bệnh nhân: [Dropdown ▼]   │
│ Lý do khám: [____________]      │
│ [Hủy] [Xác nhận thêm]          │
└─────────────────────────────────┘
```

### **Khám theo gói:**
```
┌─────────────────────────────────┐
│ Thêm Bệnh nhân Walk-in          │
├─────────────────────────────────┤
│ Bác sĩ: BS.Vanh Le - Mắt        │
│ Thời gian: 31/10/2025 - 12:00   │
├─────────────────────────────────┤
│ Loại khám: *                    │
│ ○ Khám thường                   │
│ ● Khám theo gói                 │
├─────────────────────────────────┤
│ Chọn gói khám: *                │
│ [Gói khám tổng quát - Nam ▼]   │
│                                 │
│ ┌─────────────────────────────┐ │
│ │ Gói khám tổng quát - Nam    │ │
│ │ ✓ Điện tim ECG   100,000 đ  │ │
│ │ ✓ Xét nghiệm     150,000 đ  │ │
│ │ Tổng: 6,180,000 đ           │ │
│ └─────────────────────────────┘ │
├─────────────────────────────────┤
│ Lý do khám: [____________]      │
│ [Hủy] [Xác nhận thêm]          │
└─────────────────────────────────┘

KHÔNG có "Loại bệnh nhân" ✅
KHÔNG có "Chọn bệnh nhân" ✅
```

---

## 🔄 SO SÁNH

### **Trước (Sai):**
```
Khám theo gói:
✓ Chọn gói
✓ Chọn bệnh nhân  ← SAI! Không cần
✓ Lý do
```

### **Sau (Đúng):**
```
Khám theo gói:
✓ Chọn gói
✓ Lý do
❌ KHÔNG có chọn bệnh nhân
```

---

## 💡 LÝ DO

### **Tại sao không chọn bệnh nhân khi đặt gói?**
- Gói khám thường được **đăng ký trước** (online)
- Bệnh nhân đã có tài khoản
- Hệ thống tự động lấy thông tin từ user đã login

### **Walk-in gói khám:**
- Bệnh nhân đến trực tiếp
- Lễ tân chỉ cần chọn gói
- Thông tin bệnh nhân sẽ nhập ở bước sau (hoặc tạo appointment rồi assign bệnh nhân)

---

## ⚠️ LƯU Ý BACKEND

### **Khi submit form:**
```php
$appointmentType = $_POST['appointment_type'];

if ($appointmentType === 'package') {
    $packageId = $_POST['package_id'];
    // Không có patient_id từ form
    // Cần xử lý: Tạo appointment với package_id
    // Sau đó admin/lễ tân assign bệnh nhân
} else {
    $patientId = $_POST['patient_id'];
    // Xử lý như cũ
}
```

---

## 📝 FILES ĐÃ SỬA

1. ✅ `app/Views/schedule/add_patient.php`
   - Wrap "Loại bệnh nhân" trong `<div id="patientTypeSelection">`
   - JavaScript toggle ẩn/hiện đúng

---

**Hard refresh (Ctrl+Shift+R) và test!** 🚀

### **Test checklist:**
- [ ] Chọn "Khám thường" → Hiện "Chọn bệnh nhân"
- [ ] Chọn "Khám theo gói" → ẨN "Chọn bệnh nhân", hiện "Chọn gói"
- [ ] Chọn gói → Hiện thông tin dịch vụ + giá
- [ ] Submit form → Backend xử lý đúng
