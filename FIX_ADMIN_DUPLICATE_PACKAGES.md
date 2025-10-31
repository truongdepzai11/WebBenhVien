# ✅ FIX: ADMIN THẤY 2 GÓI "NAM" GIỐNG NHAU

## 🐛 VẤN ĐỀ

**Admin/Doctor thấy 2 gói "Gói khám sức khỏe tổng quát - Nam" giống hệt nhau**

### **Nguyên nhân:**
- Database có 2 gói trùng tên
- Code đã đúng (Admin thấy tất cả gói)
- Cần xóa gói trùng trong database

---

## ✅ GIẢI PHÁP

### **CÁCH 1: Tự động (Khuyên dùng)**

```sql
-- Vào phpMyAdmin → SQL tab
-- Copy toàn bộ file: sql/auto_fix_duplicate_packages.sql
-- Paste và Run
```

### **CÁCH 2: Thủ công (An toàn hơn)**

#### **Bước 1: Xem gói trùng**

```sql
SELECT 
    id,
    package_code,
    name,
    gender_requirement,
    (SELECT COUNT(*) FROM appointments WHERE package_id = health_packages.id) as appointments
FROM health_packages
WHERE name LIKE '%tổng quát%'
ORDER BY name, id;
```

**Kết quả ví dụ:**
```
id | package_code | name                              | gender | appointments
---+--------------+-----------------------------------+--------+-------------
1  | PKG001       | Gói khám...tổng quát - Nam       | male   | 5
2  | PKG002       | Gói khám...tổng quát - Nam       | male   | 0  ← TRÙNG!
3  | PKG003       | Gói khám...tổng quát - Nữ        | female | 3
```

→ **Gói ID=2 là gói trùng, cần xóa**

---

#### **Bước 2: Xóa gói trùng**

**Nếu gói KHÔNG có appointment (appointments = 0):**

```sql
-- Xóa dịch vụ
DELETE FROM package_services WHERE package_id = 2;

-- Xóa gói
DELETE FROM health_packages WHERE id = 2;
```

**Nếu gói CÓ appointment (appointments > 0):**

```sql
-- Chỉ ẨN gói, không xóa
UPDATE health_packages SET is_active = 0 WHERE id = 2;
```

---

#### **Bước 3: Kiểm tra kết quả**

```sql
SELECT 
    id,
    package_code,
    name,
    gender_requirement,
    is_active
FROM health_packages
WHERE name LIKE '%tổng quát%'
ORDER BY name, gender_requirement;
```

**Kết quả mong đợi:**
```
id | package_code | name                              | gender | is_active
---+--------------+-----------------------------------+--------+----------
1  | PKG001       | Gói khám...tổng quát - Nam       | male   | 1
3  | PKG003       | Gói khám...tổng quát - Nữ        | female | 1
```

→ **Chỉ còn 1 gói Nam, 1 gói Nữ** ✅

---

## 🎯 TẠI SAO CÓ GÓI TRÙNG?

### **Nguyên nhân có thể:**

1. **Tạo gói 2 lần:**
   - Admin tạo gói "Nam"
   - Sau đó tạo lại gói "Nam" (quên đã tạo)

2. **Import data:**
   - Import SQL có dữ liệu trùng

3. **Bug trong code cũ:**
   - Code cũ không kiểm tra trùng lặp

---

## 🚀 PHÒNG TRÁNH GÓI TRÙNG

### **1. Thêm UNIQUE constraint:**

```sql
-- Đảm bảo không tạo trùng
ALTER TABLE health_packages 
ADD UNIQUE KEY unique_package (name, gender_requirement);
```

### **2. Kiểm tra trước khi tạo:**

```php
// Trong PackageController::store()
$existing = $this->packageModel->findByNameAndGender(
    $_POST['name'], 
    $_POST['gender_requirement']
);

if ($existing) {
    $_SESSION['error'] = 'Gói khám này đã tồn tại!';
    return;
}
```

---

## 📊 SO SÁNH

### **Trước (Lỗi):**
```
Admin thấy:
✓ Gói khám tổng quát - Nam
✓ Gói khám tổng quát - Nam  ← TRÙNG!
✓ Gói khám tổng quát - Nữ
```

### **Sau (Đúng):**
```
Admin thấy:
✓ Gói khám tổng quát - Nam
✓ Gói khám tổng quát - Nữ
```

---

## ⚠️ LƯU Ý QUAN TRỌNG

### **1. KHÔNG xóa gói có appointment**
```sql
-- Kiểm tra trước
SELECT COUNT(*) FROM appointments WHERE package_id = 2;

-- Nếu > 0 → Chỉ ẨN, đừng xóa
UPDATE health_packages SET is_active = 0 WHERE id = 2;
```

### **2. Backup trước khi xóa**
```sql
-- Backup bảng
CREATE TABLE health_packages_backup AS SELECT * FROM health_packages;
CREATE TABLE package_services_backup AS SELECT * FROM package_services;
```

### **3. Kiểm tra kỹ ID gói**
```sql
-- Xem chi tiết gói trước khi xóa
SELECT * FROM health_packages WHERE id = 2;
SELECT * FROM package_services WHERE package_id = 2;
```

---

## 🔧 SCRIPT SQL

### **File 1: `sql/remove_duplicate_packages.sql`**
- Hướng dẫn thủ công từng bước
- An toàn, kiểm soát được

### **File 2: `sql/auto_fix_duplicate_packages.sql`**
- Tự động xóa gói trùng
- Nhanh, tiện lợi

---

## 🚀 HÀNH ĐỘNG NGAY

### **Bước 1: Chạy SQL**
```
1. Mở phpMyAdmin
2. Chọn database: hospital_management
3. Tab SQL
4. Copy file: sql/auto_fix_duplicate_packages.sql
5. Paste và Run
```

### **Bước 2: Reload trang**
```
1. Vào /admin/packages
2. F5 reload
3. Kiểm tra: Chỉ còn 1 gói Nam, 1 gói Nữ
```

### **Bước 3: Test**
```
1. Login Admin → Thấy 2 gói (Nam + Nữ) ✅
2. Login Doctor → Thấy 2 gói (Nam + Nữ) ✅
3. Login Patient Nam → Thấy 1 gói (Nam) ✅
4. Login Patient Nữ → Thấy 1 gói (Nữ) ✅
```

---

## 📄 KẾT QUẢ MONG ĐỢI

### **Database:**
```sql
SELECT id, name, gender_requirement FROM health_packages;

-- Kết quả:
-- 1 | Gói khám sức khỏe tổng quát - Nam | male
-- 3 | Gói khám sức khỏe tổng quát - Nữ  | female
```

### **Admin thấy:**
```
┌─────────────────────────────────────┐
│ Quản lý Gói khám                    │
├─────────────────────────────────────┤
│ ✓ Gói khám tổng quát - Nam          │
│ ✓ Gói khám tổng quát - Nữ           │
└─────────────────────────────────────┘
```

### **Bệnh nhân Nam thấy:**
```
┌─────────────────────────────────────┐
│ Gói khám sức khỏe                   │
├─────────────────────────────────────┤
│ ✓ Gói khám tổng quát - Nam          │
└─────────────────────────────────────┘
```

### **Bệnh nhân Nữ thấy:**
```
┌─────────────────────────────────────┐
│ Gói khám sức khỏe                   │
├─────────────────────────────────────┤
│ ✓ Gói khám tổng quát - Nữ           │
└─────────────────────────────────────┘
```

---

**🎉 CHẠY SQL VÀ RELOAD TRANG - VẤN ĐỀ SẼ BIẾN MẤT!**
