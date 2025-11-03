# ✅ FIX: FLOW TẠO GÓI KHÁM ĐÚNG

## 🎯 VẤN ĐỀ

### **1. Cột "LOẠI KHÁM" hiện sai:**
- Đặt gói khám → Hiện "Khám thường" ❌
- Nguyên nhân: Tạo appointment trực tiếp với `appointment_type = 'package'`

### **2. Bảng `package_appointments` trống:**
- Đặt gói khám → KHÔNG tạo record trong `package_appointments` ❌
- Trang "Quản lý Gói khám" trống trơn ❌

---

## ✅ GIẢI PHÁP

### **Flow ĐÚNG khi đặt gói khám:**

```
1. User đặt gói khám
   ↓
2. Tạo package_appointment (KHÔNG tạo appointment)
   ↓
3. Redirect → /package-appointments/{id}
   ↓
4. Admin phân công bác sĩ cho từng dịch vụ
   ↓
5. Tạo appointments (mỗi dịch vụ 1 appointment)
```

---

## 📊 SO SÁNH

### **TRƯỚC (SAI):**

**Đặt gói khám:**
```php
// AppointmentController::store()
$this->appointmentModel->package_id = 1;
$this->appointmentModel->appointment_type = 'package';
$this->appointmentModel->doctor_id = null;
$this->appointmentModel->create();
// → Tạo 1 appointment với package_id
// → KHÔNG tạo package_appointment
```

**Kết quả:**
- ✅ Bảng `appointments`: Có 1 record
- ❌ Bảng `package_appointments`: Trống
- ❌ Trang "Quản lý Gói khám": Trống
- ❌ Không thể phân công bác sĩ

---

### **SAU (ĐÚNG):**

**Đặt gói khám:**
```php
// AppointmentController::store()
if ($is_package) {
    // Tạo package_appointment
    $packageAppointmentModel = new PackageAppointment();
    $packageAppointmentModel->patient_id = $patient_id;
    $packageAppointmentModel->package_id = $_POST['package_id'];
    $packageAppointmentModel->appointment_date = $_POST['appointment_date'];
    $packageAppointmentModel->status = 'scheduled';
    $packageAppointmentModel->create();
    
    // Redirect đến trang chi tiết gói khám
    header('Location: /package-appointments/' . $id);
    exit;
}
```

**Kết quả:**
- ✅ Bảng `package_appointments`: Có 1 record
- ✅ Trang "Quản lý Gói khám": Hiện gói khám
- ✅ Admin có thể phân công bác sĩ
- ✅ Sau khi phân công → Tạo appointments

---

## 💻 CODE ĐÃ SỬA

### **AppointmentController::store():**

```php
public function store() {
    // ... validation ...
    
    // Kiểm tra loại khám
    $is_package = !empty($_POST['package_id']);
    
    // Nếu đặt GÓI KHÁM → Tạo package_appointment
    if ($is_package) {
        $package = $this->packageModel->findById($_POST['package_id']);
        
        // Tạo package_appointment
        $packageAppointmentModel = new PackageAppointment();
        $packageAppointmentModel->patient_id = $patient_id;
        $packageAppointmentModel->package_id = $_POST['package_id'];
        $packageAppointmentModel->appointment_date = $_POST['appointment_date'];
        $packageAppointmentModel->status = 'scheduled';
        $packageAppointmentModel->notes = $_POST['notes'] ?? null;
        $packageAppointmentModel->total_price = $package['price'];
        
        if ($packageAppointmentModel->create()) {
            $_SESSION['success'] = 'Đăng ký gói khám thành công! Vui lòng chờ admin phân công bác sĩ.';
            header('Location: /package-appointments/' . $packageAppointmentModel->id);
        }
        exit;
    }
    
    // Nếu đặt KHÁM THƯỜNG → Tạo appointment
    $this->appointmentModel->patient_id = $patient_id;
    $this->appointmentModel->doctor_id = $_POST['doctor_id'];
    $this->appointmentModel->appointment_date = $_POST['appointment_date'];
    $this->appointmentModel->appointment_time = $_POST['appointment_time'];
    $this->appointmentModel->appointment_type = 'regular';
    $this->appointmentModel->package_id = null;
    $this->appointmentModel->package_appointment_id = null;
    $this->appointmentModel->create();
}
```

---

## 🔄 FLOW HOÀN CHỈNH

### **1. Đặt khám THƯỜNG:**

```
User → /appointments/create
  ↓ Chọn "Khám thường"
  ↓ Chọn bác sĩ: BS. Nguyễn Văn A
  ↓ Chọn ngày: 05/11/2025
  ↓ Chọn giờ: 10:00
  ↓ Submit
  ↓
Tạo appointment:
  - appointment_type = 'regular'
  - doctor_id = 3
  - appointment_time = '10:00:00'
  - package_id = NULL
  - package_appointment_id = NULL
  ↓
Redirect → /appointments
```

---

### **2. Đặt khám THEO GÓI:**

```
User → /appointments/create
  ↓ Chọn "Khám theo gói"
  ↓ Chọn gói: "Gói tổng quát - Nam"
  ↓ Chọn ngày: 05/11/2025
  ↓ Submit
  ↓
Tạo package_appointment:
  - patient_id = 10
  - package_id = 1
  - appointment_date = '2025-11-05'
  - status = 'scheduled'
  ↓
Redirect → /package-appointments/5
  ↓
Admin thấy:
  - 28 dịch vụ chưa phân công
  - Nút "Phân công ngay"
  ↓
Admin phân công từng dịch vụ:
  - Dịch vụ 1: Chọn BS. A, ngày 05/11, giờ 10:00
  - Dịch vụ 2: Chọn BS. B, ngày 05/11, giờ 11:00
  - ...
  ↓
Tạo appointments:
  - APT001: doctor_id=3, package_appointment_id=5, time='10:00'
  - APT002: doctor_id=7, package_appointment_id=5, time='11:00'
  - ...
  ↓
Bây giờ vào /appointments:
  - Thấy APT001, APT002... (màu vàng)
  - Loại khám: "Khám theo gói" ✅
```

---

## ✅ ĐÃ SỬA

1. ✅ Đặt gói khám → Tạo `package_appointment` (KHÔNG tạo appointment)
2. ✅ Redirect đến `/package-appointments/{id}`
3. ✅ Admin phân công bác sĩ → Tạo appointments
4. ✅ Appointments có `package_appointment_id` → Hiện "Khám theo gói"
5. ✅ Bỏ check conflict và check thời gian quá khứ cho gói khám

---

## 📁 FILES ĐÃ SỬA

1. ✅ `app/Controllers/AppointmentController.php` - Method `store()`

---

## 🚀 TEST

### **Test 1: Đặt khám thường**
```
1. Chọn "Khám thường"
2. Chọn bác sĩ, ngày, giờ
3. Submit
4. Kết quả:
   - ✅ Tạo appointment
   - ✅ Hiện trong /appointments
   - ✅ Loại khám: "Khám thường"
```

### **Test 2: Đặt gói khám**
```
1. Chọn "Khám theo gói"
2. Chọn gói, ngày
3. Submit
4. Kết quả:
   - ✅ Tạo package_appointment
   - ✅ Redirect → /package-appointments/1
   - ✅ Hiện trong "Quản lý Gói khám"
   - ✅ Chưa có appointments
```

### **Test 3: Phân công bác sĩ**
```
1. Admin vào /package-appointments/1
2. Phân công bác sĩ cho dịch vụ
3. Kết quả:
   - ✅ Tạo appointment
   - ✅ Hiện trong /appointments (màu vàng)
   - ✅ Loại khám: "Khám theo gói" ✅
```

---

**REFRESH VÀ TEST NGAY!** 🎉
