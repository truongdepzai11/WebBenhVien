# ✅ FINAL: LƯU 2 BẢNG KHI ĐẶT GÓI KHÁM

## 🎯 YÊU CẦU CUỐI CÙNG

Khi đặt gói khám, cần lưu vào **2 BẢNG**:

1. ✅ **`package_appointments`** → Hiện trong "Quản lý Gói khám"
2. ✅ **`appointments`** → Hiện trong "Quản lý Lịch hẹn" với loại "Khám theo gói"

---

## ✅ GIẢI PHÁP

### **Khi user đặt gói khám:**

```php
// 1. Tạo package_appointment
$packageAppointment = new PackageAppointment();
$packageAppointment->patient_id = 10;
$packageAppointment->package_id = 1;
$packageAppointment->appointment_date = '2025-11-05';
$packageAppointment->status = 'scheduled';
$packageAppointment->total_price = 6680000;
$packageAppointment->create();
// → ID = 5

// 2. Tạo appointment "tổng hợp"
$appointment = new Appointment();
$appointment->patient_id = 10;
$appointment->doctor_id = null; // Chưa có bác sĩ
$appointment->package_id = 1;
$appointment->package_appointment_id = 5; // Link đến package_appointment
$appointment->appointment_date = '2025-11-05';
$appointment->appointment_time = null; // Chưa có giờ
$appointment->reason = 'Khám theo gói: Gói tổng quát - Nam';
$appointment->appointment_type = 'package';
$appointment->status = 'pending';
$appointment->total_price = 6680000;
$appointment->create();
```

---

## 📊 KẾT QUẢ

### **Bảng `package_appointments`:**
```sql
id | patient_id | package_id | appointment_date | status    | total_price
5  | 10         | 1          | 2025-11-05       | scheduled | 6680000
```

### **Bảng `appointments`:**
```sql
id  | patient_id | doctor_id | package_id | package_appointment_id | appointment_type | reason
262 | 10         | NULL      | 1          | 5                      | package          | Khám theo gói: ...
```

---

## 🎨 GIAO DIỆN

### **Trang "Quản lý Lịch hẹn" (`/appointments`):**
```
┌──────────────────────────────────────────────────────────────┐
│ MÃ LỊCH  | BỆNH NHÂN | BÁC SĨ          | NGÀY       | LOẠI KHÁM       │
├──────────────────────────────────────────────────────────────┤
│ APT00262 | huy le    | Chưa phân công  | 05/11/2025 | 📦 Khám theo gói│ ← Màu vàng
│ APT00001 | Nguyễn A  | BS. Trần        | 05/11/2025 | 👨‍⚕️ Khám thường │
└──────────────────────────────────────────────────────────────┘
```

### **Trang "Quản lý Gói khám" (`/package-appointments`):**
```
┌──────────────────────────────────────────────────────────────┐
│ MÃ GÓI | BỆNH NHÂN | GÓI KHÁM              | NGÀY       | PHÂN CÔNG    │
├──────────────────────────────────────────────────────────────┤
│ #PKG5  | huy le    | Gói tổng quát - Nam   | 05/11/2025 | 0/28 đã PC   │
└──────────────────────────────────────────────────────────────┘
```

---

## 🔄 FLOW HOÀN CHỈNH

### **Bước 1: User đặt gói khám**
```
User → /appointments/create
  ↓ Chọn "Khám theo gói"
  ↓ Chọn gói: "Gói tổng quát - Nam"
  ↓ Chọn ngày: 05/11/2025
  ↓ Submit
  ↓
Controller tạo 2 records:
  1. package_appointments (id=5)
  2. appointments (id=262, package_appointment_id=5)
  ↓
Redirect → /package-appointments/5
```

### **Bước 2: Xem danh sách**

**Quản lý Lịch hẹn:**
```
/appointments
→ Thấy APT00262 (màu vàng)
→ Loại khám: "Khám theo gói" ✅
→ Bác sĩ: "Chưa phân công"
→ Giờ: "Chưa xác định"
```

**Quản lý Gói khám:**
```
/package-appointments
→ Thấy #PKG5
→ Gói: "Gói tổng quát - Nam"
→ Phân công: "0/28 đã phân công"
```

### **Bước 3: Admin phân công bác sĩ**
```
Admin → /package-appointments/5
  ↓ Thấy 28 dịch vụ chưa phân công
  ↓ Dịch vụ 1: Chọn BS. A, ngày 05/11, giờ 10:00
  ↓ Submit
  ↓
Tạo appointment mới:
  - id = 263
  - patient_id = 10
  - doctor_id = 3 (BS. A)
  - package_id = 1
  - package_appointment_id = 5
  - appointment_time = '10:00:00'
  - reason = 'Khám nội khoa'
```

### **Bước 4: Kết quả cuối cùng**

**Bảng `appointments`:**
```sql
id  | doctor_id | package_appointment_id | appointment_time | reason
262 | NULL      | 5                      | NULL             | Khám theo gói: ... (Tổng hợp)
263 | 3         | 5                      | 10:00:00         | Khám nội khoa
264 | 7         | 5                      | 11:00:00         | Siêu âm
...
```

**Trang `/appointments`:**
```
APT00262 | huy le | Chưa phân công | 05/11 | -     | Khám theo gói (Tổng hợp)
APT00263 | huy le | BS. A          | 05/11 | 10:00 | Khám theo gói
APT00264 | huy le | BS. B          | 05/11 | 11:00 | Khám theo gói
```

---

## 💡 TẠI SAO CẦN 2 BẢNG?

### **Bảng `package_appointments`:**
- **Mục đích:** Quản lý đăng ký gói khám
- **Thông tin:** Bệnh nhân nào, gói nào, ngày nào, trạng thái
- **Dùng để:** Admin phân công bác sĩ, theo dõi tiến độ

### **Bảng `appointments`:**
- **Mục đích:** Quản lý lịch hẹn cụ thể
- **Thông tin:** Bác sĩ nào, giờ nào, dịch vụ gì
- **Dùng để:** Hiển thị lịch khám, check conflict, thống kê

### **Appointment "tổng hợp":**
- **Mục đích:** Hiện trong danh sách lịch hẹn
- **Đặc điểm:**
  - `doctor_id = NULL` (chưa phân công)
  - `appointment_time = NULL` (chưa xác định)
  - `appointment_type = 'package'`
  - `reason = 'Khám theo gói: ...'`
- **Lợi ích:**
  - User thấy gói khám trong danh sách lịch hẹn
  - Phân biệt với khám thường
  - Click vào → Redirect đến `/package-appointments/{id}`

---

## ✅ ĐÃ SỬA

### **Model:**
1. ✅ `PackageAppointment.php` - Thêm property `$total_price`
2. ✅ `PackageAppointment.php` - Thêm vào câu INSERT và bindParam

### **Controller:**
3. ✅ `AppointmentController.php` - Tạo CẢ 2 records khi đặt gói khám

---

## 📁 FILES ĐÃ SỬA

1. ✅ `app/Models/PackageAppointment.php`
2. ✅ `app/Controllers/AppointmentController.php`

---

## 🚀 TEST

### **Test 1: Đặt gói khám**
```
1. Chọn "Khám theo gói"
2. Chọn gói, ngày
3. Submit
4. Kết quả:
   - ✅ Bảng package_appointments: Có 1 record
   - ✅ Bảng appointments: Có 1 record (tổng hợp)
   - ✅ /package-appointments: Hiện #PKG5
   - ✅ /appointments: Hiện APT00262 (màu vàng, "Khám theo gói")
```

### **Test 2: Phân công bác sĩ**
```
1. Admin vào /package-appointments/5
2. Phân công bác sĩ cho dịch vụ
3. Kết quả:
   - ✅ Bảng appointments: Thêm 1 record mới (dịch vụ cụ thể)
   - ✅ /appointments: Hiện APT00263 (màu vàng, "Khám theo gói")
```

---

**HOÀN THÀNH! BÂY GIỜ CÓ ĐẦY ĐỦ 2 BẢNG!** 🎉
