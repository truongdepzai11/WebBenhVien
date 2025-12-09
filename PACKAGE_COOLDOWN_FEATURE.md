# PACKAGE APPOINTMENT COOLDOWN - FEATURE DOCUMENTATION

## 📋 Mô Tả Tính Năng

Tính năng **Cooldown Period** cho phép giới hạn số lần bệnh nhân có thể đặt cùng một gói khám trong một khoảng thời gian nhất định.

### ❌ Vấn Đề Trước Đây
- Bệnh nhân có thể đặt cùng một gói khám **nhiều lần** trong cùng một tháng
- Không thực tế vì một gói khám là một đợt khám sức khỏe toàn diện
- Bệnh nhân cần thời gian để hoàn thành và xử lý kết quả

### ✅ Giải Pháp
- Mỗi bệnh nhân chỉ được đặt **1 lần duy nhất** mỗi gói khám trong khoảng thời gian **cooldown**
- Nếu cố gắng đặt trong cooldown period → **Từ chối + Thông báo**
- Sau khi hết cooldown → **Cho phép đặt lại**

---

## 🔧 Thực Hiện

### 1. Database Schema
**Bảng:** `health_packages`

**Cột mới:** `cooldown_days` (INT, DEFAULT 0)
- Giá trị tính bằng **ngày**
- `0` = Không có ràng buộc (cho phép đặt nhiều lần)
- `>0` = Số ngày phải chờ trước khi có thể đặt lại

### 2. Cấu Hình Cooldown (Hiện Tại)
```
ID | Package Name                           | Cooldown Days
---+----------------------------------------+---------------
1  | Gói khám sức khỏe tổng quát - Nam      | 30 ngày
2  | Gói khám sức khỏe tổng quát - Nữ      | 30 ngày
3  | Gói khám phụ sản (mẹ bầu)              | 90 ngày (3 tháng)
4  | Gói khám sức khỏe tổng quát Nam-Nữ    | 30 ngày
5  | Gói khám tầm soát ung thư cơ bản       | 30 ngày
```

### 3. Logic Kiểm Tra

#### 3.1 Method trong `PackageAppointment` Model
```php
public function checkCooldown($patientId, $packageId)
```

**Input:**
- `$patientId`: ID bệnh nhân
- `$packageId`: ID gói khám

**Output:** Array chứa:
- `is_in_cooldown` (bool): Có đang trong cooldown period không
- `last_appointment_date` (string): Ngày đặt lần cuối cùng
- `cooldown_days` (int): Tổng số ngày cooldown
- `remaining_days` (int): Số ngày còn lại phải chờ
- `error` (string): Lỗi nếu có

**Quy Tắc:**
1. Lấy `cooldown_days` từ `health_packages`
2. Nếu `cooldown_days <= 0` → Không ràng buộc, cho phép đặt
3. Tìm `package_appointment` gần nhất của bệnh nhân cho gói này
4. Tính số ngày từ lần cuối đến hiện tại
5. Nếu `days_diff < cooldown_days` → **Trong cooldown**
6. Ngược lại → **Cho phép đặt**

**Trạng thái Kiểm Tra:** `pending`, `scheduled`, `in_progress`, `completed` (KHÔNG tính `cancelled`)

#### 3.2 Validation trong ScheduleController

**Nơi kiểm tra:** `storePackageWalkin()` method

**Thời điểm:** Trước khi tạo `package_appointment` mới

**Nếu trong cooldown:**
```
Thông báo: "Bạn đã đặt khám gói "[Package Name]" rồi. Vui lòng chờ thêm [X] ngày nữa trước khi có thể đặt lại gói này."
Hành động: Redirect về `/schedule` 
```

---

## 🧪 Testing

### Test Case 1: Đặt Lần Đầu
✅ **Kỳ Vọng:** Thành công
- **Bước:** Bệnh nhân đặt gói khám lần đầu
- **Kết Quả:** Đăng ký thành công, thông báo "Đặt lịch khám thành công"

### Test Case 2: Đặt Lại Trong Cooldown Period
❌ **Kỳ Vọng:** Bị chặn
- **Bước:** Bệnh nhân cố gắng đặt cùng gói khám trong vòng 30 ngày
- **Kết Quả:** Thông báo lỗi, không được tạo đăng ký mới

### Test Case 3: Đặt Lại Sau Hết Cooldown Period
✅ **Kỳ Vọng:** Thành công
- **Bước:** Sau ≥ 30 ngày, bệnh nhân đặt lại cùng gói khám
- **Kết Quả:** Đăng ký thành công

### Test Case 4: Admin Hủy Đăng Ký
✅ **Kỳ Vọng:** Cho phép đặt lại ngay
- **Bước:** Admin hủy (`cancelled`) một package_appointment, bệnh nhân đặt lại
- **Kết Quả:** Đăng ký thành công (vì `cancelled` không tính trong kiểm tra)

---

## 🚀 Cách Sử Dụng

### Để Kiểm Tra Cooldown:
```php
$packageAppointmentModel = new PackageAppointment();
$result = $packageAppointmentModel->checkCooldown($patientId, $packageId);

if ($result['is_in_cooldown']) {
    echo "Phải chờ " . $result['remaining_days'] . " ngày nữa";
} else {
    echo "Có thể đặt lại";
}
```

### Để Cấu Hình Cooldown Days:
```sql
UPDATE health_packages SET cooldown_days = 45 WHERE id = 1;
```

---

## 📝 Files Thay Đổi

1. **`app/Models/PackageAppointment.php`**
   - Thêm method `checkCooldown()`

2. **`app/Controllers/ScheduleController.php`**
   - Thêm validation trong `storePackageWalkin()`

3. **`sql/hospital_management.sql`**
   - Bảng `health_packages` có cột `cooldown_days` (đã tồn tại)

4. **`sql/set_package_cooldown.sql`**
   - Script cập nhật cooldown_days cho các gói (đã chạy)

---

## 📊 Ví Dụ Thực Tế

**Scenario:** Bệnh nhân "Yến" đặt gói khám tầm soát ung thư

| Ngày | Hành Động | Kết Quả |
|------|----------|---------|
| 01/12/2025 | Đặt gói | ✅ Thành công |
| 05/12/2025 | Cố gắng đặt lại | ❌ Lỗi: "Vui lòng chờ 26 ngày" |
| 31/12/2025 | Cố gắng đặt lại | ✅ Thành công (30 ngày đã qua) |

---

## 🔄 Future Improvements

- [ ] Cho phép Admin tùy chỉnh cooldown_days từ UI
- [ ] Hiển thị ngày có thể đặt lại trong danh sách gói khám
- [ ] Send reminder email khi hết cooldown period
- [ ] Support ràng buộc theo tháng (thay vì theo ngày)
