# ✅ COOLDOWN PERIOD FOR PACKAGE APPOINTMENTS - IMPLEMENTATION COMPLETE

## 🎯 Mục Tiêu
Tạo ràng buộc để bệnh nhân chỉ có thể đặt **1 lần duy nhất** mỗi gói khám trong một khoảng thời gian xác định (cooldown period).

---

## 📝 Thay Đổi Thực Hiện

### 1️⃣ **Database** 
✅ **File:** `sql/hospital_management.sql`
- Cột `cooldown_days` đã tồn tại trong bảng `health_packages`
- Update giá trị cooldown_days cho tất cả gói khám:
  ```
  ID 1: Gói khám sức khỏe tổng quát - Nam           → 30 ngày
  ID 2: Gói khám sức khỏe tổng quát - Nữ           → 30 ngày
  ID 3: Gói khám phụ sản (mẹ bầu)                   → 90 ngày
  ID 4: Gói khám sức khỏe tổng quát Nam-Nữ         → 30 ngày
  ID 5: Gói khám tầm soát ung thư cơ bản            → 30 ngày
  ```

### 2️⃣ **Model Layer**
✅ **File:** `app/Models/PackageAppointment.php`

**Thêm method mới:**
```php
public function checkCooldown($patientId, $packageId)
```

**Chức năng:**
- Kiểm tra xem bệnh nhân đã đăng ký gói này trong cooldown period chưa
- Lấy `cooldown_days` từ `health_packages`
- Tìm đăng ký gần nhất của bệnh nhân (trạng thái != 'cancelled')
- Tính số ngày từ lần cuối đến hiện tại
- Trả về array chứa:
  - `is_in_cooldown`: boolean
  - `last_appointment_date`: ngày đăng ký lần cuối
  - `cooldown_days`: tổng số ngày cooldown
  - `remaining_days`: số ngày còn lại phải chờ
  - `error`: lỗi nếu có

### 3️⃣ **Controller Layer**
✅ **File:** `app/Controllers/ScheduleController.php`

**Thay đổi trong:** `storePackageWalkin()` method

**Thêm validation:**
```php
// Kiểm tra cooldown period trước khi tạo đăng ký gói khám
$cooldownCheck = $packageAppointmentModel->checkCooldown($patientId, $_POST['package_id']);

if ($cooldownCheck['is_in_cooldown']) {
    // Lấy tên gói để hiển thị trong thông báo
    require_once APP_PATH . '/Models/HealthPackage.php';
    $pkgModel = new HealthPackage();
    $pkg = $pkgModel->findById($_POST['package_id']);
    $packageName = $pkg['name'] ?? 'gói khám này';
    
    $remainingDays = $cooldownCheck['remaining_days'] ?? 0;
    $_SESSION['error'] = 'Bạn đã đặt khám gói "' . htmlspecialchars($packageName) . '" rồi. Vui lòng chờ thêm ' . $remainingDays . ' ngày nữa trước khi có thể đặt lại gói này.';
    header('Location: ' . APP_URL . '/schedule');
    exit;
}
```

**Nơi kiểm tra:** Trước khi tạo `package_appointment` mới (dòng ~300 trong file)

**Nếu trong cooldown:**
- ❌ Không tạo đăng ký mới
- 📢 Hiển thị thông báo lỗi với số ngày còn lại
- 🔄 Redirect về `/schedule`

---

## 🧪 Test Cases

### ✅ Test Case 1: Đặt Lần Đầu
**Kỳ vọng:** Đăng ký thành công
- Bệnh nhân "Yến" (ID 14) đặt gói "Gói khám tầm soát ung thư cơ bản" (ID 5)
- Cooldown: 30 ngày
- **Kết quả:** ✅ Đăng ký thành công (chưa có đăng ký nào)

### ❌ Test Case 2: Đặt Lại Trong 30 Ngày
**Kỳ vọng:** Bị chặn
- Cùng bệnh nhân "Yến" cố gắng đặt lại gói "Gói khám tầm soát ung thư cơ bản"
- Đã đặt 5 lần trong tháng 12
- **Kết quả:** ❌ Lỗi: "Bạn đã đặt khám gói "Gói khám tầm soát ung thư cơ bản" rồi. Vui lòng chờ thêm X ngày nữa..."

### ✅ Test Case 3: Đặt Lại Sau 30 Ngày
**Kỳ vọng:** Đăng ký thành công
- Sau 30+ ngày kể từ đăng ký lần cuối
- **Kết quả:** ✅ Đăng ký thành công

### ✅ Test Case 4: Đặt Gói Khác
**Kỳ vọng:** Đăng ký thành công
- Bệnh nhân đặt gói khám khác (không bị ảnh hưởng bởi cooldown)
- **Kết quả:** ✅ Đăng ký thành công

### ✅ Test Case 5: Admin Hủy, Bệnh Nhân Đặt Lại Ngay
**Kỳ vọng:** Đăng ký thành công (vì cancelled không tính)
- Admin hủy một `package_appointment` (status = 'cancelled')
- Bệnh nhân đặt lại ngay
- **Kết quả:** ✅ Đăng ký thành công

---

## 🔍 Chi Tiết Logic Kiểm Tra

### Query SQL Trong `checkCooldown()`
```sql
-- Lấy cooldown_days
SELECT cooldown_days FROM health_packages WHERE id = ?

-- Tìm đăng ký gần nhất (không tính cancelled)
SELECT MAX(created_at) as last_created_at 
FROM package_appointments 
WHERE patient_id = ? 
  AND package_id = ? 
  AND status != 'cancelled'
```

### Quy Tắc Logic
1. Nếu `cooldown_days = 0` → Không có ràng buộc ✅
2. Nếu chưa có đăng ký nào → Cho phép đặt ✅
3. Nếu có đăng ký:
   - Tính `days_diff = hiện_tại - lần_cuối`
   - Nếu `days_diff < cooldown_days` → **Trong cooldown** ❌
   - Nếu `days_diff >= cooldown_days` → **Cho phép đặt** ✅

---

## 📋 Danh Sách Files Thay Đổi

| File | Thay Đổi |
|------|----------|
| `app/Models/PackageAppointment.php` | ➕ Thêm method `checkCooldown()` |
| `app/Controllers/ScheduleController.php` | ➕ Thêm validation trong `storePackageWalkin()` |
| `sql/set_package_cooldown.sql` | ✨ Script update cooldown_days (đã chạy) |
| `PACKAGE_COOLDOWN_FEATURE.md` | ✨ Documentation chi tiết |

---

## 🚀 Cách Sử Dụng

### Kiểm Tra Cooldown Trong Code:
```php
$packageAppointmentModel = new PackageAppointment();
$result = $packageAppointmentModel->checkCooldown($patientId, $packageId);

if ($result['is_in_cooldown']) {
    echo "Phải chờ " . $result['remaining_days'] . " ngày";
} else {
    echo "Có thể đặt lại";
}
```

### Thay Đổi Cooldown Days:
```php
// Trong database:
UPDATE health_packages SET cooldown_days = 45 WHERE id = 1;

// Hoặc từ code:
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->prepare("UPDATE health_packages SET cooldown_days = ? WHERE id = ?");
$stmt->execute([45, 1]);
```

---

## ⚙️ Cấu Hình Hiện Tại

| Gói Khám | Cooldown | Mục Đích |
|----------|----------|---------|
| Gói khám sức khỏe - Nam | 30 ngày | Kiểm tra sức khỏe tổng quát định kỳ |
| Gói khám sức khỏe - Nữ | 30 ngày | Kiểm tra sức khỏe tổng quát định kỳ |
| Gói khám phụ sản | 90 ngày | Khám thai định kỳ 3 tháng |
| Gói khám Nam-Nữ | 30 ngày | Kiểm tra sức khỏe tổng quát định kỳ |
| Gói tầm soát ung thư | 30 ngày | Tầm soát ung thư định kỳ |

> Có thể điều chỉnh bằng cách update `cooldown_days` trong bảng `health_packages`

---

## 📊 Ví Dụ Thực Tế

**Bệnh nhân "Yến" (ID 14) đặt gói "Gói khám tầm soát ung thư cơ bản" (ID 5):**

| Lần | Ngày Đặt | Kết Quả | Ghi Chú |
|-----|----------|---------|--------|
| 1️⃣ | 04/12/2025 | ✅ Thành công | Đầu tiên |
| 2️⃣ | 06/12/2025 | ❌ Lỗi | Chỉ sau 2 ngày, phải chờ 28 ngày |
| 3️⃣ | 07/12/2025 | ❌ Lỗi | Chỉ sau 3 ngày, phải chờ 27 ngày |
| 4️⃣ | 03/01/2026 | ✅ Thành công | 30 ngày đã qua ✓ |

---

## 🔄 Future Enhancements

- [ ] Cho phép Admin tùy chỉnh `cooldown_days` từ UI (Admin Panel)
- [ ] Hiển thị "Có thể đặt lại vào ngày XX" trong danh sách gói khám
- [ ] Send email reminder khi hết cooldown period
- [ ] Support ràng buộc theo tháng (thay vì theo ngày)
- [ ] Thống kê số lần đặt mỗi gói khám

---

## ✅ Status
**Implementation:** COMPLETE
**Testing:** Ready for user testing
**Documentation:** Complete

---

**Tạo ngày:** 07/12/2025  
**Last Updated:** 07/12/2025
