# ✅ TÓM TẮT: LỌC GÓI KHÁM THEO GIỚI TÍNH

## 🎯 YÊU CẦU

1. **Bệnh nhân** → Chỉ thấy gói phù hợp giới tính
2. **Admin/Doctor** → Thấy TẤT CẢ gói
3. **Không hiển thị gói trùng lặp**

---

## ✅ ĐÃ FIX

### **1. PackageController.php**
```php
// Admin/Doctor/Receptionist
if (in_array($role, ['admin', 'doctor', 'receptionist'])) {
    $packages = getAllActive(); // TẤT CẢ
}

// Bệnh nhân
else if ($role === 'patient') {
    $patient = getPatientInfo();
    $packages = getPackagesForPatient($patient['gender'], $age);
    // Lọc: gender = 'both' OR gender = patient.gender
}

// Guest
else {
    $packages = getAllActive(); // TẤT CẢ
}
```

---

## 📊 LOGIC LỌC

### **Bảng quyết định:**

| User          | Giới tính | Gói hiển thị                    |
|---------------|-----------|----------------------------------|
| Admin         | -         | Nam + Nữ + Both                  |
| Doctor        | -         | Nam + Nữ + Both                  |
| Receptionist  | -         | Nam + Nữ + Both                  |
| Patient       | Nam       | Nam + Both                       |
| Patient       | Nữ        | Nữ + Both                        |
| Guest         | -         | Nam + Nữ + Both                  |

---

## 🔧 FIX GÓI TRÙNG

### **Kiểm tra:**
```sql
SELECT name, COUNT(*) as count
FROM health_packages
GROUP BY name
HAVING COUNT(*) > 1;
```

### **Xóa trùng:**
```sql
-- Xem chi tiết
SELECT * FROM health_packages WHERE name = 'Gói khám tổng quát - Nam';

-- Xóa (nếu không có appointment)
DELETE FROM health_packages WHERE id = [ID_TRÙNG];
```

---

## 🚀 TEST

### **Test 1: Bệnh nhân Nam**
```
Login: patient (gender = male)
Vào: /packages
Kết quả: Chỉ thấy gói Nam + Both
```

### **Test 2: Bệnh nhân Nữ**
```
Login: patient (gender = female)
Vào: /packages
Kết quả: Chỉ thấy gói Nữ + Both
```

### **Test 3: Admin**
```
Login: admin
Vào: /packages
Kết quả: Thấy TẤT CẢ gói
```

---

## 📝 FILES

1. ✅ `app/Controllers/PackageController.php` - Logic lọc
2. ✅ `sql/check_duplicate_packages.sql` - Kiểm tra trùng
3. ✅ `sql/fix_packages_gender.sql` - Fix tự động
4. ✅ `FIX_DUPLICATE_PACKAGES.md` - Hướng dẫn chi tiết

---

## ⚠️ LƯU Ý

1. **Tên gói phải rõ ràng:**
   - ✅ "Gói khám tổng quát - Nam"
   - ✅ "Gói khám tổng quát - Nữ"
   - ❌ "Gói khám tổng quát" (không rõ)

2. **gender_requirement phải đúng:**
   - `male` → Chỉ nam
   - `female` → Chỉ nữ
   - `both` → Cả 2 giới

3. **Không xóa gói có appointment:**
   ```sql
   -- Kiểm tra trước
   SELECT COUNT(*) FROM appointments WHERE package_id = X;
   -- Nếu > 0 → Chỉ ẩn: is_active = 0
   ```

---

**Chạy SQL và reload trang!** 🚀
