# ✅ FIX GIÁ GÓI & LỌC THEO GIỚI TÍNH

## 🎯 VẤN ĐỀ

### **1. Giá gói hiện 0 VNĐ:**
```
Dropdown: "Gói khám sức khỏe tổng quát - Nam - 0 VNĐ"
→ SAI! Phải hiện giá thật
```

### **2. Hiện cả gói Nữ khi chọn Nam:**
```
Giới tính: Nam
Dropdown gói:
- Gói khám Nam ✓
- Gói khám Nữ ← SAI! Phải ẨN
- Gói khám Chung ✓
```

---

## ✅ GIẢI PHÁP

### **1. Tính giá từ tổng dịch vụ:**

**Trước (Sai):**
```php
$price = isset($p['price']) ? $p['price'] : 0;
// → Luôn = 0 vì không có field 'price'
```

**Sau (Đúng):**
```php
// Tính tổng giá từ bảng health_package_services
$query = "SELECT SUM(service_price) as total_price 
          FROM health_package_services 
          WHERE package_id = :package_id";
$stmt = $pkgModel->conn->prepare($query);
$stmt->execute();
$totalPrice = $stmt->fetch()['total_price'] ?? 0;
```

---

### **2. Lọc gói theo giới tính:**

**JavaScript:**
```javascript
function filterPackagesByGender(gender) {
    const options = packageSelect.querySelectorAll('option');
    
    options.forEach(option => {
        const pkgGender = option.dataset.gender;
        
        // Hiển thị nếu gói phù hợp
        if (pkgGender === 'both' || pkgGender === gender) {
            option.style.display = ''; // Hiện
        } else {
            option.style.display = 'none'; // Ẩn
        }
    });
}

// Lắng nghe thay đổi giới tính
document.getElementById('newPatientGenderPkg').addEventListener('change', function() {
    filterPackagesByGender(this.value);
});
```

---

## 📊 FLOW LỌC GÓI

### **Bước 1: Chọn giới tính**
```
Bệnh nhân mới:
Giới tính: [Nam ▼]
         ↓
    Trigger event
```

### **Bước 2: Lọc dropdown gói**
```
Giới tính = "male"
         ↓
Loop qua tất cả <option>:
- data-gender="male" → Hiện ✓
- data-gender="female" → Ẩn ❌
- data-gender="both" → Hiện ✓
```

### **Bước 3: Kết quả**
```
Dropdown chỉ hiện:
- Gói khám tổng quát - Nam
- Gói khám sức khỏe - Chung
(KHÔNG hiện gói Nữ)
```

---

## 🔧 CHI TIẾT KỸ THUẬT

### **1. Cấu trúc option với metadata:**

```html
<option value="1" 
        data-gender="male" 
        data-price="6180000">
    Gói khám tổng quát - Nam - 6,180,000 VNĐ
</option>

<option value="2" 
        data-gender="female" 
        data-price="6480000">
    Gói khám tổng quát - Nữ - 6,480,000 VNĐ
</option>

<option value="3" 
        data-gender="both" 
        data-price="5000000">
    Gói khám cơ bản - Chung - 5,000,000 VNĐ
</option>
```

---

### **2. Mapping giới tính:**

```
Database (gender_requirement):
- "male" → Nam
- "female" → Nữ
- "both" → Cả 2 giới

Form (new_patient_gender):
- "male" → Nam
- "female" → Nữ
- "other" → Khác (hiện gói "both")
```

---

### **3. Logic lọc:**

```javascript
if (pkgGender === 'both') {
    // Luôn hiện gói "Chung"
    show();
} else if (pkgGender === patientGender) {
    // Hiện nếu khớp giới tính
    show();
} else {
    // Ẩn nếu không khớp
    hide();
}
```

---

## 📸 KẾT QUẢ

### **Trước (Sai):**
```
Giới tính: Nam
Dropdown:
┌─────────────────────────────────┐
│ Gói khám Nam - 0 VNĐ           │ ← SAI! Giá 0
│ Gói khám Nữ - 0 VNĐ            │ ← SAI! Hiện gói Nữ
│ Gói khám Chung - 0 VNĐ         │ ← SAI! Giá 0
└─────────────────────────────────┘
```

### **Sau (Đúng):**
```
Giới tính: Nam
Dropdown:
┌─────────────────────────────────┐
│ Gói khám Nam - 6,180,000 VNĐ   │ ✓ Giá đúng
│ Gói khám Chung - 5,000,000 VNĐ │ ✓ Giá đúng
└─────────────────────────────────┘
(Gói Nữ đã ẨN)
```

---

## 🧪 TEST

### **Test 1: Giá gói đúng**
```
1. Vào /schedule
2. Click "Khám theo gói"
3. Chọn "Bệnh nhân mới"
4. Mở dropdown "Chọn gói khám"
5. Kết quả:
   ✅ Tất cả gói hiện giá > 0
   ✅ Giá = tổng dịch vụ
```

### **Test 2: Lọc theo giới tính Nam**
```
1. Chọn "Bệnh nhân mới"
2. Giới tính: Nam
3. Mở dropdown "Chọn gói khám"
4. Kết quả:
   ✅ Hiện: Gói Nam + Gói Chung
   ❌ ẨN: Gói Nữ
```

### **Test 3: Lọc theo giới tính Nữ**
```
1. Chọn "Bệnh nhân mới"
2. Giới tính: Nữ
3. Mở dropdown "Chọn gói khám"
4. Kết quả:
   ✅ Hiện: Gói Nữ + Gói Chung
   ❌ ẨN: Gói Nam
```

### **Test 4: Đổi giới tính**
```
1. Giới tính: Nam → Chọn gói Nam
2. Đổi giới tính: Nữ
3. Kết quả:
   ✅ Dropdown reset về "-- Chọn gói khám --"
   ✅ Gói Nam bị ẨN
   ✅ Gói Nữ hiện ra
```

---

## 📁 FILES ĐÃ SỬA

1. ✅ `app/Views/schedule/index.php`
   - Tính giá từ SUM(service_price)
   - Thêm data-gender attribute
   - Thêm JavaScript filterPackagesByGender()
   - Thêm event listener cho giới tính

---

## 💡 LƯU Ý

### **Tối ưu hiệu suất:**
```php
// Hiện tại: Query trong loop (N queries)
foreach ($pkgs as $p) {
    $query = "SELECT SUM(service_price) ...";
    // → Chậm nếu nhiều gói
}

// Tối ưu: 1 query duy nhất
$query = "SELECT p.*, SUM(s.service_price) as total_price
          FROM health_packages p
          LEFT JOIN health_package_services s ON p.id = s.package_id
          WHERE p.is_active = 1
          GROUP BY p.id";
```

### **Xử lý bệnh nhân cũ:**
```javascript
// TODO: Thêm giới tính vào dropdown bệnh nhân
<option value="1" data-gender="male">
    Nguyễn Văn A (BN001) - Nam
</option>

// Sau đó lọc gói khi chọn bệnh nhân
patientSelect.addEventListener('change', function() {
    const gender = this.options[this.selectedIndex].dataset.gender;
    filterPackagesByGender(gender);
});
```

---

**Hard refresh (Ctrl+Shift+R) và test!** 🚀

Kết quả mong đợi:
- ✅ Giá gói hiện đúng (> 0)
- ✅ Chọn Nam → Chỉ hiện gói Nam + Chung
- ✅ Chọn Nữ → Chỉ hiện gói Nữ + Chung
- ✅ Đổi giới tính → Dropdown tự động lọc
