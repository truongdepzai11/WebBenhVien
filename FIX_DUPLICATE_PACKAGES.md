# ✅ FIX GÓI KHÁM TRÙNG LẶP VÀ LỌC THEO GIỚI TÍNH

## 🐛 VẤN ĐỀ

### **1. Hiển thị 2 gói "Nam" giống nhau**
- Nguyên nhân: Database có 2 gói trùng tên
- Cần: Xóa gói trùng

### **2. Bệnh nhân NỮ thấy gói NAM**
- Nguyên nhân: Không lọc theo giới tính user
- Cần: Lọc gói phù hợp với giới tính bệnh nhân

### **3. Admin không thấy tất cả gói**
- Nguyên nhân: Bị lọc như bệnh nhân
- Cần: Admin/Doctor thấy TẤT CẢ gói

---

## ✅ GIẢI PHÁP ĐÃ TRIỂN KHAI

### **1. Fix Logic Lọc Gói (PackageController.php)**

```php
// Admin/Doctor/Receptionist → Thấy TẤT CẢ gói
if (in_array($userRole, ['admin', 'doctor', 'receptionist'])) {
    $packages = $this->packageModel->getAllActive();
}

// Bệnh nhân → Chỉ thấy gói phù hợp giới tính
else if ($userRole === 'patient') {
    $patient = $patientModel->findByUserId($_SESSION['user_id']);
    $gender = $patient['gender']; // 'male' hoặc 'female'
    $age = tính_tuổi($patient['date_of_birth']);
    
    // Lọc: gender_requirement = 'both' OR gender_requirement = $gender
    $packages = $this->packageModel->getPackagesForPatient($gender, $age);
}

// Guest → Hiện tất cả hoặc lọc theo query
else {
    $packages = $this->packageModel->getAllActive();
}
```

---

## 🔧 BƯỚC FIX

### **BƯỚC 1: Kiểm tra gói trùng**

```sql
-- Chạy trong phpMyAdmin
SELECT 
    id,
    name,
    package_code,
    gender_requirement,
    status
FROM health_packages
WHERE name LIKE '%tổng quát%'
ORDER BY name, gender_requirement;
```

**Kết quả mong đợi:**
```
id | name                                    | gender_requirement
---+-----------------------------------------+-------------------
1  | Gói khám sức khỏe tổng quát - Nam      | male
2  | Gói khám sức khỏe tổng quát - Nữ       | female
```

**Nếu thấy:**
```
1  | Gói khám sức khỏe tổng quát - Nam      | male
2  | Gói khám sức khỏe tổng quát - Nam      | male  ← TRÙNG!
3  | Gói khám sức khỏe tổng quát - Nữ       | female
```

→ **XÓA gói ID=2**

---

### **BƯỚC 2: Xóa gói trùng (nếu có)**

```sql
-- Xem chi tiết gói trùng
SELECT * FROM health_packages WHERE id = 2;

-- Xóa gói trùng
DELETE FROM health_packages WHERE id = 2;

-- Xóa dịch vụ của gói trùng
DELETE FROM package_services WHERE package_id = 2;
```

---

### **BƯỚC 3: Đảm bảo tên gói đúng**

```sql
-- Gói Nam
UPDATE health_packages 
SET name = 'Gói khám sức khỏe tổng quát - Nam',
    gender_requirement = 'male'
WHERE id = 1;

-- Gói Nữ
UPDATE health_packages 
SET name = 'Gói khám sức khỏe tổng quát - Nữ',
    gender_requirement = 'female'
WHERE id = 3;

-- Hoặc tạo gói BOTH (cho cả 2 giới)
INSERT INTO health_packages 
(package_code, name, gender_requirement, min_age, max_age, is_active)
VALUES 
('PKG003', 'Gói khám sức khỏe tổng quát', 'both', 18, 100, 1);
```

---

## 📊 LOGIC LỌC GÓI

### **Bảng quyết định:**

| User Role       | Giới tính | Hiển thị gói                              |
|-----------------|-----------|-------------------------------------------|
| **Admin**       | -         | TẤT CẢ (Nam + Nữ + Both)                  |
| **Doctor**      | -         | TẤT CẢ (Nam + Nữ + Both)                  |
| **Receptionist**| -         | TẤT CẢ (Nam + Nữ + Both)                  |
| **Patient**     | Nam       | Gói Nam + Gói Both                        |
| **Patient**     | Nữ        | Gói Nữ + Gói Both                         |
| **Guest**       | -         | TẤT CẢ (hoặc lọc theo query)              |

---

## 🎯 VÍ DỤ

### **Database:**
```
id | name                          | gender_requirement
---+-------------------------------+-------------------
1  | Gói tổng quát - Nam           | male
2  | Gói tổng quát - Nữ            | female
3  | Gói cơ bản                    | both
4  | Gói tiền hôn nhân - Nữ        | female
```

### **Kết quả hiển thị:**

**Admin login:**
```
✓ Gói tổng quát - Nam
✓ Gói tổng quát - Nữ
✓ Gói cơ bản
✓ Gói tiền hôn nhân - Nữ
→ Thấy TẤT CẢ 4 gói
```

**Bệnh nhân NAM login:**
```
✓ Gói tổng quát - Nam       (male)
✓ Gói cơ bản                (both)
→ Thấy 2 gói
```

**Bệnh nhân NỮ login:**
```
✓ Gói tổng quát - Nữ        (female)
✓ Gói cơ bản                (both)
✓ Gói tiền hôn nhân - Nữ    (female)
→ Thấy 3 gói
```

---

## 🚀 TEST

### **Test 1: Admin**
```
1. Login admin
2. Vào /packages
3. Phải thấy TẤT CẢ gói (Nam + Nữ)
```

### **Test 2: Bệnh nhân Nam**
```
1. Login bệnh nhân giới tính Nam
2. Vào /packages
3. Chỉ thấy gói Nam + Both
4. KHÔNG thấy gói Nữ
```

### **Test 3: Bệnh nhân Nữ**
```
1. Login bệnh nhân giới tính Nữ
2. Vào /packages
3. Chỉ thấy gói Nữ + Both
4. KHÔNG thấy gói Nam
```

---

## 📝 FILES ĐÃ SỬA

1. ✅ `app/Controllers/PackageController.php`
   - Thêm logic lọc theo role
   - Admin/Doctor → Tất cả
   - Patient → Lọc theo giới tính

2. ✅ `sql/check_duplicate_packages.sql`
   - Script kiểm tra gói trùng

---

## ⚠️ LƯU Ý

1. **Không xóa gói có appointment**
   ```sql
   -- Kiểm tra trước khi xóa
   SELECT COUNT(*) FROM appointments WHERE package_id = 2;
   -- Nếu > 0 → Đừng xóa, chỉ set is_active = 0
   UPDATE health_packages SET is_active = 0 WHERE id = 2;
   ```

2. **Tên gói phải rõ ràng**
   - ✅ "Gói khám tổng quát - Nam"
   - ✅ "Gói khám tổng quát - Nữ"
   - ❌ "Gói khám tổng quát" (không rõ giới tính)

3. **gender_requirement phải đúng**
   - `male` → Chỉ nam
   - `female` → Chỉ nữ
   - `both` → Cả 2 giới

---

## 🎉 KẾT QUẢ

### **Trước:**
```
❌ Bệnh nhân Nữ thấy 2 gói Nam
❌ Admin không thấy đủ gói
❌ Gói trùng lặp
```

### **Sau:**
```
✅ Bệnh nhân chỉ thấy gói phù hợp
✅ Admin thấy TẤT CẢ gói
✅ Không còn trùng lặp
```

---

**Chạy SQL kiểm tra và reload trang!** 🚀
