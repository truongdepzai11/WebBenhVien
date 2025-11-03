# ✅ FIX HOÀN CHỈNH: TẠO LỊCH HẸN GÓI KHÁM

## 🎯 TẤT CẢ LỖI ĐÃ SỬA

### **Lỗi 1: Foreign key `package_id`**
```
SQLSTATE[23000]: Integrity constraint violation: 1452 
Cannot add or update a child row: a foreign key constraint fails
```
**Fix:** Kiểm tra `package_id` tồn tại trước khi lưu

---

### **Lỗi 2: Thiếu `package_appointment_id` trong Model**
```
Column 'package_appointment_id' not found
```
**Fix:** 
- Thêm property `package_appointment_id`
- Thêm vào câu INSERT
- Thêm bindParam

---

### **Lỗi 3: `appointment_time` cannot be null**
```
Column 'appointment_time' cannot be null
```
**Fix:** Sửa cột cho phép NULL
```sql
ALTER TABLE appointments 
MODIFY COLUMN appointment_time TIME NULL;
```

---

### **Lỗi 4: `doctor_id` cannot be null**
```
Column 'doctor_id' cannot be null
```
**Fix:** Sửa cột cho phép NULL
```sql
ALTER TABLE appointments 
MODIFY COLUMN doctor_id INT(11) NULL;
```

---

## ✅ GIẢI PHÁP HOÀN CHỈNH

### **1. Database Schema:**

```sql
-- Cho phép NULL khi đặt gói khám
ALTER TABLE appointments 
MODIFY COLUMN doctor_id INT(11) NULL;

ALTER TABLE appointments 
MODIFY COLUMN appointment_time TIME NULL;
```

---

### **2. Model (Appointment.php):**

```php
class Appointment {
    // Properties
    public $id;
    public $appointment_code;
    public $patient_id;
    public $doctor_id;
    public $coordinator_doctor_id;
    public $package_id;
    public $package_appointment_id; // ← THÊM MỚI
    public $appointment_type;
    public $total_price;
    public $appointment_date;
    public $appointment_time;
    public $reason;
    public $status;
    public $notes;
    
    // Create method
    public function create() {
        $query = "INSERT INTO appointments 
                  (..., package_id, package_appointment_id, ...) 
                  VALUES (..., :package_id, :package_appointment_id, ...)";
        
        $stmt->bindParam(':package_id', $this->package_id);
        $stmt->bindParam(':package_appointment_id', $this->package_appointment_id); // ← THÊM MỚI
        // ...
    }
}
```

---

### **3. Controller (AppointmentController.php):**

```php
public function store() {
    // Validate
    $is_package = !empty($_POST['package_id']);
    
    $validator = new Validator($_POST);
    
    // Chỉ bắt buộc doctor_id và appointment_time khi đặt khám THƯỜNG
    if (!$is_package) {
        $validator->required('doctor_id', 'Vui lòng chọn bác sĩ')
                  ->required('appointment_time', 'Vui lòng chọn giờ khám');
    }
    
    // Kiểm tra package_id tồn tại
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
    
    // Set doctor_id
    if ($is_package) {
        $this->appointmentModel->doctor_id = null; // ← NULL cho gói khám
    } else {
        $this->appointmentModel->doctor_id = $_POST['doctor_id'];
    }
    
    // Create appointment
    $this->appointmentModel->create();
}
```

---

### **4. View (create.php):**

```javascript
function toggleAppointmentType(type) {
    if (type === 'package') {
        // Ẩn chọn bác sĩ và giờ khám
        doctorSelection.style.display = 'none';
        timeSelection.style.display = 'none';
        
        // Bỏ required
        doctorSelect.removeAttribute('required');
        timeSelect.removeAttribute('required');
        
        // Reset giá trị
        doctorSelect.value = '';
        timeSelect.value = '';
    } else {
        // Hiện chọn bác sĩ và giờ khám
        doctorSelection.style.display = 'block';
        timeSelection.style.display = 'block';
        
        // Thêm required
        doctorSelect.setAttribute('required', 'required');
        timeSelect.setAttribute('required', 'required');
    }
}
```

---

## 📊 FLOW HOÀN CHỈNH

### **Đặt lịch KHÁM THƯỜNG:**

```
1. User chọn "Khám thường"
2. Chọn bác sĩ: BS. Nguyễn Văn A (REQUIRED)
3. Chọn ngày: 05/11/2025 (REQUIRED)
4. Chọn giờ: 10:00 (REQUIRED)
5. Nhập lý do: "Đau đầu" (REQUIRED)
6. Submit

→ Tạo appointment:
   - doctor_id = 3 (BS. Nguyễn Văn A)
   - appointment_date = '2025-11-05'
   - appointment_time = '10:00:00'
   - package_id = NULL
   - package_appointment_id = NULL
```

---

### **Đặt lịch KHÁM THEO GÓI:**

```
1. User chọn "Khám theo gói"
2. Chọn gói: "Gói tổng quát - Nam" (REQUIRED)
3. Chọn ngày: 05/11/2025 (REQUIRED)
4. Nhập lý do: "Khám định kỳ" (REQUIRED)
5. KHÔNG chọn bác sĩ (đã ẩn)
6. KHÔNG chọn giờ (đã ẩn)
7. Submit

→ Tạo package_appointment:
   - patient_id = 10
   - package_id = 1
   - appointment_date = '2025-11-05'
   - status = 'scheduled'
   
→ KHÔNG tạo appointments ngay
   (Sẽ tạo sau khi admin phân công)
```

---

### **Admin phân công bác sĩ:**

```
1. Admin vào /package-appointments/5
2. Thấy 28 dịch vụ chưa phân công
3. Dịch vụ 1: Khám nội khoa
   - Chọn bác sĩ: BS. Nguyễn Văn A
   - Chọn ngày: 05/11/2025
   - Chọn giờ: 10:00
4. Submit

→ Tạo appointment:
   - doctor_id = 3 (BS. Nguyễn Văn A)
   - appointment_date = '2025-11-05'
   - appointment_time = '10:00:00'
   - package_id = 1
   - package_appointment_id = 5
```

---

## ✅ TẤT CẢ ĐÃ SỬA

### **Database:**
1. ✅ `doctor_id` cho phép NULL
2. ✅ `appointment_time` cho phép NULL

### **Model:**
3. ✅ Thêm property `package_appointment_id`
4. ✅ Thêm vào câu INSERT
5. ✅ Thêm bindParam

### **Controller:**
6. ✅ Validation động (khám thường vs gói)
7. ✅ Kiểm tra `package_id` tồn tại
8. ✅ Set `doctor_id = NULL` cho gói khám

### **View:**
9. ✅ Ẩn/hiện bác sĩ và giờ khám
10. ✅ Bỏ/thêm required động
11. ✅ JavaScript validation đúng

---

## 📁 FILES ĐÃ SỬA

1. ✅ `sql/allow_null_appointment_time.sql` - Migration
2. ✅ `app/Models/Appointment.php` - Model
3. ✅ `app/Controllers/AppointmentController.php` - Controller
4. ✅ `app/Views/appointments/create.php` - View (đã có sẵn)

---

## 🚀 TEST CUỐI CÙNG

### **Test 1: Đặt khám thường**
```
✅ Bắt buộc chọn bác sĩ
✅ Bắt buộc chọn giờ
✅ Tạo appointment thành công
✅ doctor_id có giá trị
✅ appointment_time có giá trị
```

### **Test 2: Đặt gói khám**
```
✅ KHÔNG cần chọn bác sĩ
✅ KHÔNG cần chọn giờ
✅ Tạo package_appointment thành công
✅ doctor_id = NULL
✅ appointment_time = NULL
```

### **Test 3: Phân công bác sĩ**
```
✅ Admin vào /package-appointments/1
✅ Thấy danh sách dịch vụ
✅ Chọn bác sĩ + ngày + giờ
✅ Tạo appointment thành công
✅ doctor_id có giá trị
✅ appointment_time có giá trị
```

---

**HOÀN THÀNH! BÂY GIỜ CÓ THỂ ĐẶT GÓI KHÁM!** 🎉
