# ✅ BƯỚC 2: TẠO CONTROLLER & ROUTES

## 🎯 MỤC ĐÍCH

Tạo hệ thống quản lý đăng ký gói khám với các chức năng:
- Xem danh sách đăng ký
- Chi tiết đăng ký & phân công bác sĩ
- Tự động phân công bác sĩ thông minh
- Phân công thủ công

---

## 📁 FILES ĐÃ TẠO

### **1. Controller:**
✅ `app/Controllers/PackageAppointmentController.php`

**Các method:**
- `index()` - Danh sách đăng ký gói khám
- `show($id)` - Chi tiết đăng ký
- `autoAssignDoctors($id)` - Phân công tự động
- `assignDoctor()` - Phân công thủ công
- `cancel($id)` - Hủy đăng ký
- `findSuitableDoctor()` - Tìm bác sĩ phù hợp (private)

---

## 🛣️ ROUTES ĐÃ THÊM

| Method | URL | Action | Description |
|--------|-----|--------|-------------|
| GET | `/package-appointments` | `index()` | Danh sách đăng ký |
| GET | `/package-appointments/{id}` | `show($id)` | Chi tiết đăng ký |
| POST | `/package-appointments/{id}/auto-assign` | `autoAssignDoctors($id)` | Phân công tự động |
| POST | `/package-appointments/assign-doctor` | `assignDoctor()` | Phân công thủ công |
| POST | `/package-appointments/{id}/cancel` | `cancel($id)` | Hủy đăng ký |

---

## 🔧 MODELS ĐÃ CẬP NHẬT

### **1. PackageAppointment.php**
✅ Thêm method: `getByPatientId($patientId)`

### **2. Appointment.php**
✅ Thêm method: `getByPackageAppointmentId($packageAppointmentId)`
✅ Thêm method: `isDoctorAvailable($doctorId, $date, $time)`

### **3. HealthPackage.php**
✅ Thêm method: `getPackageServices($packageId)`

---

## 💡 LOGIC PHÂN CÔNG TỰ ĐỘNG

### **Thuật toán:**

```
1. Lấy danh sách dịch vụ trong gói (15 dịch vụ)
2. Ngày bắt đầu = appointment_date
3. Thời gian bắt đầu = 08:00

4. Với mỗi dịch vụ:
   a. Tìm bác sĩ rảnh vào thời gian hiện tại
   b. Nếu không có → Chuyển sang ngày hôm sau, 08:00
   c. Tạo appointment với:
      - doctor_id: Bác sĩ được chọn
      - appointment_date: Ngày hiện tại
      - appointment_time: Thời gian hiện tại
      - package_appointment_id: ID gói khám
      - appointment_type: 'package'
   d. Tăng thời gian lên 30 phút
   e. Nếu > 17:00 → Chuyển sang ngày hôm sau, 08:00

5. Kết quả: 15 appointments được tạo tự động
```

### **Ví dụ:**

**Gói khám đăng ký ngày 05/11/2025:**

| # | Dịch vụ | Bác sĩ | Ngày | Giờ |
|---|---------|--------|------|-----|
| 1 | Khám nội tổng quát | BS. Nguyễn Văn A | 05/11 | 08:00 |
| 2 | Khám tim mạch | BS. Trần Thị B | 05/11 | 08:30 |
| 3 | Khám mắt | BS. Lê Văn C | 05/11 | 09:00 |
| ... | ... | ... | ... | ... |
| 10 | Xét nghiệm máu | BS. Phạm Thị D | 05/11 | 12:30 |
| 11 | Siêu âm bụng | BS. Hoàng Văn E | 05/11 | 13:00 |
| ... | ... | ... | ... | ... |
| 15 | X-quang phổi | BS. Vũ Thị F | 06/11 | 08:00 |

---

## 🔐 PHÂN QUYỀN

### **Admin/Receptionist:**
- ✅ Xem tất cả đăng ký
- ✅ Phân công bác sĩ (tự động/thủ công)
- ✅ Hủy đăng ký

### **Bệnh nhân:**
- ✅ Xem đăng ký của mình
- ❌ Không phân công bác sĩ
- ❌ Không hủy (phải liên hệ lễ tân)

### **Bác sĩ:**
- ✅ Xem lịch được phân công
- ❌ Không xem đăng ký gói khám

---

## 📊 FLOW HOẠT ĐỘNG

### **1. Bệnh nhân đăng ký gói khám:**
```
Receptionist → Form đăng ký → POST /schedule/store-package-walkin
    ↓
Tạo package_appointment (status: scheduled)
    ↓
Chưa có appointments (chờ phân công)
```

### **2. Admin phân công bác sĩ:**
```
Admin → /package-appointments/{id} → Click "Phân công tự động"
    ↓
POST /package-appointments/{id}/auto-assign
    ↓
Tạo 15 appointments với package_appointment_id
    ↓
Mỗi appointment có doctor_id, date, time
```

### **3. Bệnh nhân xem lịch:**
```
Bệnh nhân → /package-appointments
    ↓
Thấy danh sách gói đã đăng ký
    ↓
Click chi tiết → Thấy 15 lịch khám đã được phân công
```

---

## ✅ HOÀN THÀNH

- ✅ Tạo PackageAppointmentController với 6 methods
- ✅ Thêm 5 routes mới
- ✅ Cập nhật 3 models với methods mới
- ✅ Logic phân công tự động hoàn chỉnh

---

## 🚀 BƯỚC TIẾP THEO

**Bước 3:** Tạo Views
- `package_appointments/index.php` - Danh sách
- `package_appointments/show.php` - Chi tiết & phân công

Sẵn sàng cho bước 3? 🎯
