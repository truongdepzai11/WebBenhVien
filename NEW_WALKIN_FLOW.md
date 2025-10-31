# ✅ FLOW MỚI - ĐĂNG KÝ WALK-IN (ĐÚNG YÊU CẦU)

## 🎯 FLOW HOÀN TOÀN MỚI

### **Bước 1: Trang chọn loại khám (TRANG ĐẦU TIÊN)**
```
URL: /schedule/select-type
Tiêu đề: "Đăng ký khám Walk-in"
```

**Màn hình:**
```
┌─────────────────────────────────────────┐
│ Đăng ký khám Walk-in                    │
├─────────────────────────────────────────┤
│ Chọn loại khám: *                       │
│                                         │
│ ┌─────────────────────────────────────┐ │
│ │ ● Khám thường                       │ │
│ │   Đăng ký khám bệnh thông thường    │ │
│ │   với bác sĩ chuyên khoa            │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ ┌─────────────────────────────────────┐ │
│ │ ○ Khám theo gói                     │ │
│ │   Đăng ký gói khám sức khỏe         │ │
│ │   tổng quát hoặc chuyên sâu         │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ [Quay lại] [Tiếp tục]                  │
└─────────────────────────────────────────┘

KHÔNG có lịch làm việc bác sĩ!
KHÔNG có form đăng ký!
CHỈ có 2 radio button chọn loại!
```

---

### **Bước 2a: Chọn "Khám thường" → Chuyển đến lịch bác sĩ**
```
URL: /schedule
```

**Màn hình:**
```
┌─────────────────────────────────────────┐
│ Đăng ký khám Walk-in - Khám thường      │
├─────────────────────────────────────────┤
│ Bác sĩ: [BS.Vanh Le - Mắt ▼]           │
│ Ngày: [31/10/2025]  [Hôm nay]          │
├─────────────────────────────────────────┤
│ BS.Vanh Le                           31 │
│ Mắt                       Tháng 10/2025 │
├─────────────────────────────────────────┤
│ 08:00  Đã qua (không thể đặt)           │
│ 09:00  Đã qua (không thể đặt)           │
│ 10:00  Slot trống                       │
│        [+ Thêm bệnh nhân] ← NÚT XANH   │
│ 11:00  Slot trống                       │
│        [+ Thêm bệnh nhân]               │
└─────────────────────────────────────────┘
```

**Click "Thêm bệnh nhân" → Form như CŨ (không có "Loại khám")**

---

### **Bước 2b: Chọn "Khám theo gói" → Chuyển đến form đăng ký gói**
```
URL: /schedule/register-package
```

**Màn hình:**
```
┌─────────────────────────────────────────┐
│ Đăng ký Gói khám Walk-in                │
├─────────────────────────────────────────┤
│ Chọn gói khám: *                        │
│ [Gói khám tổng quát - Nam ▼]           │
│                                         │
│ ┌─────────────────────────────────────┐ │
│ │ Gói khám sức khỏe tổng quát - Nam   │ │
│ │ ✓ Điện tim ECG                      │ │
│ │ ✓ Xét nghiệm máu                    │ │
│ │ ✓ Khám nội khoa                     │ │
│ │ Tổng: 6,180,000 đ                   │ │
│ └─────────────────────────────────────┘ │
├─────────────────────────────────────────┤
│ Loại bệnh nhân: *                       │
│ ● Bệnh nhân cũ  ○ Bệnh nhân mới        │
│                                         │
│ Chọn bệnh nhân: *                       │
│ [Nguyễn Văn A (BN001) ▼]               │
│                                         │
│ Ngày khám dự kiến: *                    │
│ [31/10/2025]                            │
│                                         │
│ Lý do khám / Ghi chú:                   │
│ [_________________________________]     │
│                                         │
│ [Hủy] [Xác nhận đăng ký]               │
└─────────────────────────────────────────┘

KHÔNG cần chọn bác sĩ!
KHÔNG cần chọn giờ!
Hệ thống sẽ phân công bác sĩ sau!
```

---

## 📁 FILES MỚI

### **1. select_type.php** (Trang chọn loại)
```
Path: app/Views/schedule/select_type.php
Chức năng:
- Hiển thị 2 radio button: Khám thường / Khám theo gói
- Click "Tiếp tục":
  + Nếu chọn "Khám thường" → /schedule
  + Nếu chọn "Khám theo gói" → /schedule/register-package
```

### **2. register_package.php** (Form đăng ký gói)
```
Path: app/Views/schedule/register_package.php
Chức năng:
- Chọn gói khám
- Chọn bệnh nhân (cũ/mới)
- Chọn ngày khám
- Nhập lý do
- Submit → Tạo appointment gói
```

### **3. index.php** (Lịch bác sĩ - GIỮ NGUYÊN)
```
Path: app/Views/schedule/index.php
Chức năng:
- Chọn bác sĩ + ngày
- Xem lịch làm việc
- Click "Thêm bệnh nhân" → /schedule/add-patient
```

### **4. add_patient.php** (Form khám thường - XÓA "Loại khám")
```
Path: app/Views/schedule/add_patient.php
Chức năng:
- XÓA phần "Loại khám" (không cần nữa)
- Chỉ có: Chọn bệnh nhân + Lý do
- Submit → Tạo appointment thường
```

---

## 🔄 SO SÁNH

### **Trước (Sai):**
```
1. Vào /schedule → Thấy lịch bác sĩ
2. Click "Thêm bệnh nhân"
3. Form có "Loại khám" (Thường/Gói)
4. Chọn gói → Vẫn ở form này
```

### **Sau (Đúng):**
```
1. Vào /schedule/select-type
2. Chọn loại: Thường HOẶC Gói
3a. Nếu Thường → /schedule (lịch bác sĩ)
3b. Nếu Gói → /schedule/register-package (form gói)
```

---

## ⚙️ CẦN THÊM ROUTES

### **File: public/index.php**
```php
// Trang chọn loại khám
} elseif ($path === '/schedule/select-type') {
    Auth::requireRole(['receptionist']);
    require_once APP_PATH . '/Views/schedule/select_type.php';

// Trang đăng ký gói walk-in
} elseif ($path === '/schedule/register-package') {
    Auth::requireRole(['receptionist']);
    require_once APP_PATH . '/Views/schedule/register_package.php';

// Xử lý submit đăng ký gói walk-in
} elseif ($path === '/schedule/store-package-walkin' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireRole(['receptionist']);
    require_once APP_PATH . '/Controllers/ScheduleController.php';
    $controller = new ScheduleController();
    $controller->storePackageWalkin();
```

---

## 🔧 SỬA FILE CŨ

### **1. Sửa menu sidebar**
```php
// Thay đổi link "Đăng ký khám Walk-in"
<a href="<?= APP_URL ?>/schedule/select-type">
    Đăng ký khám Walk-in
</a>
```

### **2. Sửa add_patient.php**
```php
// XÓA phần này:
<!-- Chọn loại khám -->
<div class="mb-6">
    <label>Loại khám *</label>
    ● Khám thường
    ○ Khám theo gói
</div>

// GIỮ LẠI:
- Chọn bệnh nhân (cũ/mới)
- Lý do khám
- Triệu chứng
```

---

## 🚀 HÀNH ĐỘNG

### **Bước 1: Thêm routes**
```
Mở: public/index.php
Thêm 3 routes mới (xem phần trên)
```

### **Bước 2: Sửa menu**
```
Mở: app/Views/layouts/main.php
Sửa link "Đăng ký khám Walk-in" → /schedule/select-type
```

### **Bước 3: Sửa add_patient.php**
```
Mở: app/Views/schedule/add_patient.php
Xóa phần "Loại khám"
```

### **Bước 4: Test**
```
1. Vào /schedule/select-type
2. Thấy 2 option: Thường / Gói
3. Chọn Thường → Chuyển /schedule
4. Chọn Gói → Chuyển /schedule/register-package
```

---

## 📊 KẾT QUẢ MONG ĐỢI

### **Trang select-type:**
```
✓ 2 radio button lớn, đẹp
✓ Mô tả rõ ràng
✓ Click "Tiếp tục" → Chuyển trang đúng
```

### **Trang register-package:**
```
✓ Chọn gói → Hiện dịch vụ + giá
✓ Chọn bệnh nhân (cũ/mới)
✓ Chọn ngày khám
✓ Submit → Tạo appointment gói
```

---

**Thêm routes và test ngay!** 🚀
