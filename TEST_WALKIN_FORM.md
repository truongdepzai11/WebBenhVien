# 🔍 TEST FORM WALK-IN

## ✅ KIỂM TRA

### **1. Hard Refresh trình duyệt**
```
Windows: Ctrl + Shift + R
Mac: Cmd + Shift + R
```

### **2. Kiểm tra Console**
```
F12 → Console tab
Xem có lỗi JavaScript không?
```

### **3. Test từng bước**

#### **Bước 1: Vào trang**
```
URL: /schedule/add-patient?doctor_id=X&date=2025-10-31&time=12:00:00
```

#### **Bước 2: Kiểm tra HTML**
```
F12 → Elements tab
Tìm: <div id="packageSelection" class="hidden">
→ Phải có element này!
```

#### **Bước 3: Click radio "Khám theo gói"**
```
Click vào: ○ Khám theo gói
→ Phải hiện dropdown "Chọn gói khám"
```

#### **Bước 4: Kiểm tra JavaScript**
```
F12 → Console
Gõ: toggleAppointmentType()
→ Xem có lỗi không?
```

---

## 🐛 NẾU VẪN LỖI

### **Lỗi 1: Không hiện dropdown gói**
**Nguyên nhân:** JavaScript không chạy

**Fix:**
```javascript
// Kiểm tra function có tồn tại không
console.log(typeof toggleAppointmentType); // Phải ra "function"

// Test thủ công
document.getElementById('packageSelection').classList.remove('hidden');
```

### **Lỗi 2: Dropdown rỗng**
**Nguyên nhân:** Không load được gói khám

**Fix:**
```php
// Kiểm tra trong add_patient.php
<?php
var_dump($packages); // Xem có data không
?>
```

### **Lỗi 3: API không hoạt động**
**Nguyên nhân:** Endpoint `/api/package-services/{id}` chưa có

**Fix tạm:**
```javascript
// Bỏ qua load API, chỉ hiện form
function loadPackageInfo(packageId) {
    if (!packageId) return;
    
    document.getElementById('packageInfo').classList.remove('hidden');
    document.getElementById('packageName').textContent = 'Gói khám đã chọn';
}
```

---

## 📸 SCREENSHOT DEBUG

### **Trước khi click:**
```
Loại khám:
● Khám thường
○ Khám theo gói

[Không thấy dropdown gói]
```

### **Sau khi click "Khám theo gói":**
```
Loại khám:
○ Khám thường
● Khám theo gói

Chọn gói khám: [Dropdown ▼]  ← PHẢI HIỆN!
```

---

## 🔧 DEBUG NHANH

### **Test 1: Kiểm tra element**
```javascript
// F12 Console
console.log(document.getElementById('packageSelection')); 
// Phải ra: <div id="packageSelection" class="hidden">...</div>
```

### **Test 2: Hiện thủ công**
```javascript
// F12 Console
document.getElementById('packageSelection').classList.remove('hidden');
// Dropdown phải hiện ngay!
```

### **Test 3: Kiểm tra event**
```javascript
// F12 Console
document.querySelector('input[name="appointment_type"][value="package"]').click();
// Dropdown phải tự động hiện
```

---

## ✅ KẾT QUẢ MONG ĐỢI

### **Form đầy đủ:**
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
│ Loại bệnh nhân: *               │
│ ● Bệnh nhân cũ                  │
│ ○ Bệnh nhân mới                 │
├─────────────────────────────────┤
│ Chọn bệnh nhân: [Dropdown ▼]   │
│ Lý do khám: [____________]      │
│ [Hủy] [Xác nhận thêm]          │
└─────────────────────────────────┘
```

---

**HARD REFRESH (Ctrl+Shift+R) và test lại!** 🚀
