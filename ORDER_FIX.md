# ✅ FIX THỨ TỰ & LỖI GÓI KHÁM

## 🎯 VẤN ĐỀ

### **1. Thứ tự sai:**
```
Trước:
1. Chọn gói khám ← SAI!
2. Chọn bệnh nhân

→ Không thể lọc gói theo giới tính!
```

### **2. Lỗi "Undefined array key 'price'":**
```
Warning: Undefined array key "price" in ...
→ Dropdown gói khám bị lỗi
```

---

## ✅ GIẢI PHÁP

### **1. Đổi thứ tự:**
```
Sau:
1. Chọn bệnh nhân (cũ/mới) ← ĐÚNG!
   → Nhập thông tin (có giới tính)
2. Chọn gói khám
   → Lọc theo giới tính bệnh nhân
```

### **2. Fix lỗi price:**
```php
// Trước (Sai):
<option><?= $p['name'] ?> - <?= number_format($p['price']) ?></option>

// Sau (Đúng):
<?php $price = isset($p['price']) ? $p['price'] : 0; ?>
<option><?= $p['name'] ?> - <?= number_format($price) ?></option>
```

---

## 📊 FLOW MỚI

### **Form "Đăng ký Gói khám Walk-in":**

```
┌─────────────────────────────────┐
│ 1. LOẠI BỆNH NHÂN *             │
│    ● Bệnh nhân cũ               │
│    ○ Bệnh nhân mới              │
├─────────────────────────────────┤
│ 2a. NẾU CHỌN "CŨ":              │
│     Chọn bệnh nhân: [▼]         │
│     → Lấy giới tính từ DB       │
│                                 │
│ 2b. NẾU CHỌN "MỚI":             │
│     Họ tên: [_____]            │
│     Ngày sinh: [_____]         │
│     SĐT: [_____]               │
│     Giới tính: [Nam ▼] ← QUAN TRỌNG!
│     Địa chỉ: [_____]           │
├─────────────────────────────────┤
│ 3. CHỌN GÓI KHÁM *              │
│    [Gói khám tổng quát - Nam ▼]│
│    → Lọc theo giới tính ở bước 2│
│                                 │
│    ┌───────────────────────────┐│
│    │ Gói khám tổng quát - Nam  ││
│    │ ✓ Điện tim ECG            ││
│    │ ✓ Xét nghiệm máu          ││
│    │ Tổng: 6,180,000 đ         ││
│    └───────────────────────────┘│
├─────────────────────────────────┤
│ 4. NGÀY KHÁM *                  │
│    [31/10/2025]                 │
├─────────────────────────────────┤
│ 5. LÝ DO KHÁM / GHI CHÚ         │
│    [_____________________]      │
├─────────────────────────────────┤
│ [Hủy] [Xác nhận đăng ký]       │
└─────────────────────────────────┘
```

---

## 🔧 CHI TIẾT KỸ THUẬT

### **1. Đổi thứ tự trong form:**

**Trước:**
```html
<form>
    <!-- 1. Chọn gói khám -->
    <select name="package_id">...</select>
    
    <!-- 2. Chọn bệnh nhân -->
    <select name="patient_id">...</select>
</form>
```

**Sau:**
```html
<form>
    <!-- 1. Loại bệnh nhân -->
    <input type="radio" name="patient_type_pkg" value="existing">
    <input type="radio" name="patient_type_pkg" value="new">
    
    <!-- 2a. Chọn bệnh nhân cũ -->
    <select name="patient_id">...</select>
    
    <!-- 2b. Nhập bệnh nhân mới -->
    <input name="new_patient_name">
    <input name="new_patient_dob">
    <input name="new_patient_phone">
    <select name="new_patient_gender"> ← QUAN TRỌNG!
        <option value="male">Nam</option>
        <option value="female">Nữ</option>
    </select>
    <input name="new_patient_address">
    
    <!-- 3. Chọn gói khám (SAU) -->
    <select name="package_id" data-gender="...">...</select>
</form>
```

---

### **2. Fix lỗi "Undefined array key 'price'":**

**Trước (Lỗi):**
```php
<?php foreach ($pkgs as $p): ?>
    <option value="<?= $p['id'] ?>">
        <?= $p['name'] ?> - <?= number_format($p['price']) ?> VNĐ
        ↑ LỖI! $p['price'] có thể không tồn tại
    </option>
<?php endforeach; ?>
```

**Sau (Đúng):**
```php
<?php foreach ($pkgs as $p): 
    $price = isset($p['price']) ? $p['price'] : 0; ← Kiểm tra trước
?>
    <option value="<?= $p['id'] ?>" data-gender="<?= $p['gender'] ?? 'both' ?>">
        <?= htmlspecialchars($p['name']) ?> - <?= number_format($price) ?> VNĐ
        ↑ ĐÚNG! Luôn có giá trị
    </option>
<?php endforeach; ?>
```

---

## 💡 LÝ DO THAY ĐỔI

### **Tại sao chọn bệnh nhân TRƯỚC?**

1. **Lọc gói theo giới tính:**
   ```
   Bệnh nhân: Nam
   → Chỉ hiện: Gói khám Nam, Gói khám Chung
   → ẨN: Gói khám Nữ
   ```

2. **Tránh chọn sai:**
   ```
   Nếu chọn gói trước:
   - Chọn "Gói khám Nữ"
   - Sau đó chọn bệnh nhân Nam
   → SAI! Phải chọn lại gói
   ```

3. **UX tốt hơn:**
   ```
   Flow tự nhiên:
   1. Ai khám? (Bệnh nhân)
   2. Khám gì? (Gói khám phù hợp)
   3. Khi nào? (Ngày khám)
   ```

---

## 🚀 TÍNH NĂNG MỚI (TỐI ƯU)

### **Lọc gói theo giới tính (JavaScript):**

```javascript
// Khi chọn bệnh nhân cũ
document.getElementById('patientSelectPkg').addEventListener('change', function() {
    const patientId = this.value;
    // Fetch giới tính từ API
    // Lọc dropdown gói khám
});

// Khi chọn giới tính (bệnh nhân mới)
document.getElementById('newPatientGenderPkg').addEventListener('change', function() {
    const gender = this.value;
    filterPackagesByGender(gender);
});

function filterPackagesByGender(gender) {
    const options = document.querySelectorAll('#packageSelectWalkin option');
    options.forEach(option => {
        const pkgGender = option.dataset.gender;
        if (pkgGender === 'both' || pkgGender === gender) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
}
```

---

## 📁 FILES ĐÃ SỬA

1. ✅ `app/Views/schedule/index.php`
   - Đổi thứ tự: Bệnh nhân → Gói
   - Fix lỗi: `isset($p['price'])`
   - Thêm: `data-gender` attribute

---

## 🧪 TEST

### **Test 1: Thứ tự đúng**
```
1. Vào /schedule
2. Click "Khám theo gói"
3. Kết quả:
   ✅ Thấy "Loại bệnh nhân" ĐẦU TIÊN
   ✅ Thấy "Chọn gói khám" SAU
```

### **Test 2: Không lỗi price**
```
1. Click "Khám theo gói"
2. Mở dropdown "Chọn gói khám"
3. Kết quả:
   ✅ KHÔNG có lỗi "Undefined array key"
   ✅ Hiện đầy đủ tên + giá gói
```

### **Test 3: Lọc theo giới tính (Tương lai)**
```
1. Chọn "Bệnh nhân mới"
2. Chọn giới tính: "Nam"
3. Mở dropdown "Chọn gói khám"
4. Kết quả:
   ✅ Chỉ hiện: Gói Nam + Gói Chung
   ❌ ẨN: Gói Nữ
```

---

**Hard refresh (Ctrl+Shift+R) và test!** 🚀

Kết quả mong đợi:
- ✅ Thứ tự: Bệnh nhân → Gói → Ngày
- ✅ KHÔNG lỗi "Undefined array key"
- ✅ Form logic hợp lý
