# 🔍 DEBUG FORM WALK-IN

## ⚠️ VẤN ĐỀ

**Khi chọn "Khám theo gói", vẫn thấy:**
- Loại bệnh nhân
- Chọn bệnh nhân

**Mong muốn: Phải ẨN hoàn toàn!**

---

## 🔧 CÁCH DEBUG

### **Bước 1: Hard Refresh**
```
Ctrl + Shift + R (Windows)
Cmd + Shift + R (Mac)
```

### **Bước 2: Mở Console**
```
F12 → Console tab
```

### **Bước 3: Click "Khám theo gói"**
```
Xem Console có log:
- Toggle appointment type: package
- patientTypeSelection element: <div>...</div>
- → Đã ẨN patientTypeSelection
```

### **Bước 4: Kiểm tra element**
```javascript
// Trong Console, gõ:
document.getElementById('patientTypeSelection')

// Phải ra: <div id="patientTypeSelection" class="hidden">...</div>
// Nếu KHÔNG có class="hidden" → JavaScript không chạy!
```

---

## 🐛 NGUYÊN NHÂN CÓ THỂ

### **1. Cache trình duyệt**
**Triệu chứng:** Code đã sửa nhưng trang không thay đổi

**Fix:**
```
1. Ctrl + Shift + R
2. Hoặc: F12 → Network → Disable cache (tick vào)
3. Reload lại
```

### **2. JavaScript bị lỗi**
**Triệu chứng:** Console có lỗi đỏ

**Fix:**
```
1. F12 → Console
2. Xem lỗi
3. Fix lỗi syntax
```

### **3. Element không tồn tại**
**Triệu chứng:** Console log `patientTypeSelection element: null`

**Fix:**
```
Kiểm tra HTML có:
<div id="patientTypeSelection">
...
</div>
```

---

## ✅ TEST THỦ CÔNG

### **Test 1: Kiểm tra element**
```javascript
// F12 Console
console.log(document.getElementById('patientTypeSelection'));
// Phải ra: <div id="patientTypeSelection">...</div>
```

### **Test 2: Ẩn thủ công**
```javascript
// F12 Console
document.getElementById('patientTypeSelection').classList.add('hidden');
// → "Loại bệnh nhân" phải biến mất ngay!
```

### **Test 3: Hiện lại**
```javascript
// F12 Console
document.getElementById('patientTypeSelection').classList.remove('hidden');
// → "Loại bệnh nhân" phải hiện lại!
```

---

## 📸 KIỂM TRA KẾT QUẢ

### **Khám thường (Đúng):**
```
✓ Loại khám: ● Khám thường
✓ Loại bệnh nhân: ● Bệnh nhân cũ
✓ Chọn bệnh nhân: [Dropdown]
✓ Lý do khám: [_______]
```

### **Khám theo gói (Đúng):**
```
✓ Loại khám: ● Khám theo gói
✓ Chọn gói khám: [Dropdown]
✓ Lý do khám: [_______]

❌ KHÔNG có "Loại bệnh nhân"
❌ KHÔNG có "Chọn bệnh nhân"
```

---

## 🔄 NẾU VẪN KHÔNG ĐƯỢC

### **Option 1: Xóa cache hoàn toàn**
```
1. F12 → Application
2. Clear storage
3. Clear site data
4. Reload
```

### **Option 2: Dùng Incognito**
```
Ctrl + Shift + N (Chrome)
→ Vào lại trang
→ Test
```

### **Option 3: Kiểm tra file có lưu không**
```
1. Mở file: app/Views/schedule/add_patient.php
2. Tìm: <div id="patientTypeSelection">
3. Kiểm tra có đúng vị trí không
```

---

## 📋 CHECKLIST

- [ ] Hard refresh (Ctrl+Shift+R)
- [ ] Mở Console (F12)
- [ ] Click "Khám theo gói"
- [ ] Xem Console log
- [ ] Kiểm tra element có class="hidden"
- [ ] Test thủ công bằng Console
- [ ] Nếu vẫn lỗi → Chụp Console gửi tôi

---

## 🎯 KẾT QUẢ MONG ĐỢI

### **Console log khi click "Khám theo gói":**
```
Toggle appointment type: package
patientTypeSelection element: <div id="patientTypeSelection">...</div>
→ Đã ẨN patientTypeSelection
```

### **HTML sau khi click:**
```html
<div id="patientTypeSelection" class="hidden">
    <!-- Loại bệnh nhân -->
    <!-- Chọn bệnh nhân -->
</div>
```

### **Màn hình:**
```
Chỉ thấy:
- Loại khám
- Chọn gói khám
- Lý do khám

KHÔNG thấy:
- Loại bệnh nhân ❌
- Chọn bệnh nhân ❌
```

---

**Làm theo checklist và báo kết quả!** 🚀
