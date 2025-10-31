# ✅ FIX HOÀN TẤT - 2 VẤN ĐỀ

## 🎯 VẤN ĐỀ 1: XÓA "LOẠI KHÁM" TRONG FORM KHÁM THƯỜNG

### **Trước (Sai):**
```
Form "Thêm Bệnh nhân Walk-in" (khám thường):
┌─────────────────────────┐
│ Loại khám: *            │  ← SAI! Thừa!
│ ● Khám thường           │
│ ○ Khám theo gói         │
├─────────────────────────┤
│ Loại bệnh nhân: *       │
│ ● Bệnh nhân cũ          │
└─────────────────────────┘
```

### **Sau (Đúng):**
```
Form "Thêm Bệnh nhân Walk-in" (khám thường):
┌─────────────────────────┐
│ Loại bệnh nhân: *       │  ← ĐÚNG!
│ ● Bệnh nhân cũ          │
│ ○ Bệnh nhân mới         │
├─────────────────────────┤
│ Chọn bệnh nhân: [▼]    │
│ Lý do khám: [_____]    │
└─────────────────────────┘

KHÔNG có "Loại khám" ✅
```

### **Đã sửa:**
- File: `app/Views/schedule/add_patient.php`
- Xóa: Phần radio button "Loại khám" (dòng 39-58)
- Xóa: Phần chọn gói khám (dòng 60-94)
- Xóa: Div wrapper `patientTypeSelection` thừa

---

## 🎯 VẤN ĐỀ 2: FORM ĐĂNG KÝ GÓI - THÊM OPTION BỆNH NHÂN CŨ/MỚI

### **Trước (Sai):**
```
Form "Đăng ký Gói khám Walk-in":
┌─────────────────────────┐
│ Chọn gói khám: [▼]     │
├─────────────────────────┤
│ Chọn bệnh nhân: [▼]    │  ← SAI! Không có option cũ/mới
│ (Lỗi: Undefined array)  │
└─────────────────────────┘
```

### **Sau (Đúng):**
```
Form "Đăng ký Gói khám Walk-in":
┌─────────────────────────┐
│ Chọn gói khám: [▼]     │
├─────────────────────────┤
│ Loại bệnh nhân: *       │  ← ĐÚNG!
│ ● Bệnh nhân cũ          │
│ ○ Bệnh nhân mới         │
├─────────────────────────┤
│ Chọn bệnh nhân: [▼]    │  ← Hiện khi chọn "cũ"
│                         │
│ HOẶC                    │
│                         │
│ Thông tin bệnh nhân mới │  ← Hiện khi chọn "mới"
│ Họ tên: [_____]        │
│ Ngày sinh: [_____]     │
│ SĐT: [_____]           │
│ Giới tính: [▼]         │
│ Địa chỉ: [_____]       │
└─────────────────────────┘
```

### **Đã sửa:**
- File: `app/Views/schedule/index.php`
- Thêm: Radio button "Loại bệnh nhân" (cũ/mới)
- Thêm: Form chọn bệnh nhân cũ (dropdown)
- Thêm: Form nhập thông tin bệnh nhân mới (5 fields)
- Thêm: JavaScript `togglePatientFormPkg()` để ẩn/hiện

---

## 📊 SO SÁNH

### **Form khám thường (add_patient.php):**
```
✅ Loại bệnh nhân (cũ/mới)
✅ Chọn bệnh nhân / Nhập thông tin mới
✅ Lý do khám
❌ KHÔNG có "Loại khám"
❌ KHÔNG có "Chọn gói"
```

### **Form khám gói (trong index.php):**
```
✅ Chọn gói khám
✅ Loại bệnh nhân (cũ/mới)
✅ Chọn bệnh nhân / Nhập thông tin mới
✅ Ngày khám
✅ Lý do khám
```

---

## 🔧 CHI TIẾT KỸ THUẬT

### **1. File: add_patient.php**
**Xóa:**
```php
<!-- Chọn loại khám -->
<div class="mb-6">
    <input type="radio" name="appointment_type" value="regular">
    <input type="radio" name="appointment_type" value="package">
</div>

<!-- Chọn gói khám -->
<div id="packageSelection" class="hidden">
    <select name="package_id">...</select>
</div>
```

**Giữ lại:**
```php
<!-- Chọn loại bệnh nhân -->
<div class="mb-6">
    <input type="radio" name="patient_type" value="existing">
    <input type="radio" name="patient_type" value="new">
</div>
```

---

### **2. File: index.php (Form gói)**
**Thêm:**
```php
<!-- Loại bệnh nhân -->
<div class="mb-6">
    <input type="radio" name="patient_type_pkg" value="existing" checked>
    <input type="radio" name="patient_type_pkg" value="new">
</div>

<!-- Form bệnh nhân cũ -->
<div id="existingPatientFormPkg">
    <select name="patient_id">...</select>
</div>

<!-- Form bệnh nhân mới -->
<div id="newPatientFormPkg" class="hidden">
    <input name="new_patient_name">
    <input name="new_patient_dob">
    <input name="new_patient_phone">
    <select name="new_patient_gender">
    <input name="new_patient_address">
</div>
```

**JavaScript:**
```javascript
function togglePatientFormPkg() {
    if (type === 'existing') {
        existingPatientFormPkg.show();
        newPatientFormPkg.hide();
    } else {
        existingPatientFormPkg.hide();
        newPatientFormPkg.show();
    }
}
```

---

## 🚀 TEST

### **Test 1: Form khám thường**
```
1. Vào /schedule
2. Click "Khám thường"
3. Chọn bác sĩ + ngày
4. Click "Thêm bệnh nhân"
5. Kết quả:
   ✅ KHÔNG thấy "Loại khám"
   ✅ Thấy "Loại bệnh nhân"
   ✅ Chọn "Bệnh nhân mới" → Hiện form nhập
```

### **Test 2: Form khám gói**
```
1. Vào /schedule
2. Click "Khám theo gói"
3. Chọn gói khám
4. Kết quả:
   ✅ Thấy "Loại bệnh nhân"
   ✅ Chọn "Bệnh nhân cũ" → Hiện dropdown
   ✅ Chọn "Bệnh nhân mới" → Hiện form nhập
   ✅ KHÔNG có lỗi "Undefined array"
```

---

## 📁 FILES ĐÃ SỬA

1. ✅ `app/Views/schedule/add_patient.php`
   - Xóa: "Loại khám"
   - Xóa: "Chọn gói"
   - Giữ: "Loại bệnh nhân"

2. ✅ `app/Views/schedule/index.php`
   - Thêm: "Loại bệnh nhân" cho form gói
   - Thêm: Form bệnh nhân cũ/mới
   - Thêm: JavaScript toggle

---

**Hard refresh (Ctrl+Shift+R) và test!** 🚀

Kết quả mong đợi:
- ✅ Form khám thường: KHÔNG có "Loại khám"
- ✅ Form khám gói: CÓ "Loại bệnh nhân" + form nhập
- ✅ KHÔNG có lỗi
