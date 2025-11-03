# ✅ FIX: LỖI FOREIGN KEY KHI TẠO APPOINTMENT

## 🎯 VẤN ĐỀ

**Lỗi:**
```
SQLSTATE[23000]: Integrity constraint violation: 1452 
Cannot add or update a child row: a foreign key constraint fails 
(`hospital_management`.`appointments`, 
CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`package_id`) 
REFERENCES `health_packages` (`id`) ON DELETE SET NULL)
```

**Nguyên nhân:**
- Đang cố tạo appointment với `package_id` không tồn tại trong bảng `health_packages`
- Hoặc `package_id` = NULL nhưng foreign key không cho phép

---

## ✅ GIẢI PHÁP

### **Thêm validation kiểm tra `package_id`:**

```php
// AppointmentController::store()

// TRƯỚC (SAI):
$is_package = !empty($_POST['package_id']);
$this->appointmentModel->package_id = $_POST['package_id'] ?? null;

// SAU (ĐÚNG):
$is_package = !empty($_POST['package_id']);

// Kiểm tra package_id có tồn tại không
if ($is_package) {
    $package = $this->packageModel->findById($_POST['package_id']);
    if (!$package) {
        $_SESSION['error'] = 'Gói khám không tồn tại';
        header('Location: /appointments/create');
        exit;
    }
    $this->appointmentModel->package_id = $_POST['package_id'];
} else {
    $this->appointmentModel->package_id = null;
}
```

---

## 🔍 NGUYÊN NHÂN CHI TIẾT

### **1. Foreign Key Constraint:**

```sql
ALTER TABLE `appointments` 
ADD CONSTRAINT `appointments_ibfk_3` 
FOREIGN KEY (`package_id`) 
REFERENCES `health_packages` (`id`) 
ON DELETE SET NULL;
```

**Ý nghĩa:**
- `package_id` trong `appointments` phải tồn tại trong `health_packages.id`
- Hoặc `package_id` = NULL

---

### **2. Trường hợp gây lỗi:**

#### **Case 1: package_id không tồn tại**
```php
$_POST['package_id'] = 999; // Không tồn tại trong health_packages
$this->appointmentModel->package_id = 999;
$this->appointmentModel->create(); // ❌ LỖI!
```

#### **Case 2: package_id = "" (empty string)**
```php
$_POST['package_id'] = ""; // Empty string
$this->appointmentModel->package_id = ""; // ❌ LỖI! (Phải là NULL)
```

---

## ✅ CÁCH FIX

### **1. Kiểm tra tồn tại:**
```php
if ($is_package) {
    $package = $this->packageModel->findById($_POST['package_id']);
    if (!$package) {
        // Gói khám không tồn tại → Báo lỗi
        $_SESSION['error'] = 'Gói khám không tồn tại';
        exit;
    }
}
```

### **2. Set NULL đúng cách:**
```php
if ($is_package) {
    $this->appointmentModel->package_id = $_POST['package_id'];
} else {
    $this->appointmentModel->package_id = null; // NULL, không phải ""
}
```

---

## 🚀 TEST

### **Test 1: Đặt lịch khám thường**
```
1. Vào: /appointments/create
2. Chọn: Khám thường
3. Điền form (KHÔNG chọn gói khám)
4. Submit
5. Kết quả: ✅ Tạo thành công với package_id = NULL
```

### **Test 2: Đặt lịch khám theo gói (gói tồn tại)**
```
1. Vào: /appointments/create?package_id=1
2. Chọn: Khám theo gói
3. Chọn gói: "Gói khám tổng quát - Nam" (ID = 1)
4. Submit
5. Kết quả: ✅ Tạo thành công với package_id = 1
```

### **Test 3: Đặt lịch khám theo gói (gói KHÔNG tồn tại)**
```
1. Vào: /appointments/create?package_id=999
2. Chọn: Khám theo gói
3. Submit
4. Kết quả: ❌ "Gói khám không tồn tại"
```

---

## 📊 SO SÁNH

### **TRƯỚC (LỖI):**
```php
// Không kiểm tra
$this->appointmentModel->package_id = $_POST['package_id'] ?? null;
$this->appointmentModel->create(); // ❌ LỖI nếu package_id không tồn tại
```

### **SAU (ĐÚNG):**
```php
// Kiểm tra trước khi lưu
if ($is_package) {
    $package = $this->packageModel->findById($_POST['package_id']);
    if (!$package) {
        $_SESSION['error'] = 'Gói khám không tồn tại';
        exit;
    }
    $this->appointmentModel->package_id = $_POST['package_id'];
} else {
    $this->appointmentModel->package_id = null;
}
$this->appointmentModel->create(); // ✅ OK
```

---

## 🔧 CÁC TRƯỜNG HỢP KHÁC

### **1. Nếu muốn cho phép package_id không tồn tại:**

Sửa foreign key:
```sql
ALTER TABLE `appointments` 
DROP FOREIGN KEY `appointments_ibfk_3`;

-- Không thêm lại foreign key
-- Hoặc thêm với ON DELETE CASCADE
```

### **2. Nếu muốn tự động set NULL:**

```sql
ALTER TABLE `appointments` 
ADD CONSTRAINT `appointments_ibfk_3` 
FOREIGN KEY (`package_id`) 
REFERENCES `health_packages` (`id`) 
ON DELETE SET NULL
ON UPDATE CASCADE;
```

---

## ✅ ĐÃ SỬA

1. ✅ Thêm validation kiểm tra `package_id` tồn tại
2. ✅ Set `package_id = null` đúng cách cho khám thường
3. ✅ Báo lỗi rõ ràng nếu gói khám không tồn tại

---

## 📁 FILES ĐÃ SỬA

1. ✅ `AppointmentController.php` - Method `store()`

---

**REFRESH VÀ TEST LẠI!** 🎉
