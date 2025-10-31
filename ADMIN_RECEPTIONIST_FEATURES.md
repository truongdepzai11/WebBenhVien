# ✅ PHÂN QUYỀN & TÍNH NĂNG ADMIN/LỄ TÂN

## 🎯 YÊU CẦU

### **1. Admin/Doctor/Lễ tân KHÔNG đặt lịch cho chính mình**
- ❌ Ẩn nút "Đặt lịch" ở trang gói khám
- ✅ Chỉ xem chi tiết gói

### **2. Admin quản lý dịch vụ trong gói**
- ✅ Thêm/Xóa dịch vụ
- ✅ Sửa giá dịch vụ (inline)
- ✅ Toggle "Bắt buộc" ↔ "Không bắt buộc" (checkbox)

### **3. Lễ tân đăng ký gói khám cho bệnh nhân walk-in**
- ✅ Trang đăng ký walk-in
- ✅ Chọn gói khám cho bệnh nhân offline
- ✅ Phân công bác sĩ

---

## ✅ ĐÃ FIX

### **FIX 1: Ẩn nút "Đặt lịch" cho Admin/Doctor/Receptionist**

**File:** `app/Views/packages/index.php`

```php
<?php 
// Chỉ bệnh nhân mới được đặt lịch
$userRole = $_SESSION['role'] ?? null;
if ($userRole === 'patient' || !isset($_SESSION['user_id'])): 
?>
<a href="<?= APP_URL ?>/appointments/create?package_id=<?= $package['id'] ?>">
    <i class="fas fa-calendar-plus mr-2"></i>Đặt lịch
</a>
<?php endif; ?>
```

**Kết quả:**
- ✅ Bệnh nhân: Thấy nút "Đặt lịch"
- ✅ Guest: Thấy nút "Đặt lịch"
- ❌ Admin/Doctor/Receptionist: KHÔNG thấy nút "Đặt lịch"

---

### **FIX 2: Thêm checkbox toggle "Bắt buộc"**

**File:** `app/Views/admin/packages/services.php`

```php
<!-- Toggle bắt buộc -->
<form action="<?= APP_URL ?>/admin/packages/{package_id}/services/{service_id}/toggle-required" 
      method="POST">
    <label class="flex items-center cursor-pointer">
        <input type="checkbox" name="is_required" value="1" 
               <?= $service['is_required'] ? 'checked' : '' ?>
               onchange="this.form.submit()">
        <span class="ml-2">Bắt buộc</span>
    </label>
</form>
```

**Kết quả:**
- ✅ Admin click checkbox → Tự động submit
- ✅ Chuyển "Bắt buộc" ↔ "Không bắt buộc"

---

### **FIX 3: Route toggle-required (CẦN THÊM)**

**File:** `app/Controllers/PackageController.php`

```php
// Toggle trạng thái bắt buộc của dịch vụ
public function toggleServiceRequired($packageId, $serviceId) {
    Auth::requireRole(['admin']);
    
    // Lấy trạng thái hiện tại
    $stmt = $this->conn->prepare(
        "SELECT is_required FROM package_services WHERE id = ? AND package_id = ?"
    );
    $stmt->execute([$serviceId, $packageId]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$service) {
        $_SESSION['error'] = 'Không tìm thấy dịch vụ!';
        header("Location: " . APP_URL . "/admin/packages/$packageId/services");
        exit;
    }
    
    // Toggle trạng thái
    $newStatus = $service['is_required'] ? 0 : 1;
    
    $stmt = $this->conn->prepare(
        "UPDATE package_services SET is_required = ? WHERE id = ? AND package_id = ?"
    );
    $stmt->execute([$newStatus, $serviceId, $packageId]);
    
    $_SESSION['success'] = $newStatus 
        ? 'Đã đặt dịch vụ là bắt buộc!' 
        : 'Đã bỏ yêu cầu bắt buộc!';
    
    header("Location: " . APP_URL . "/admin/packages/$packageId/services");
    exit;
}
```

**Route:** `public/index.php`
```php
// Toggle dịch vụ bắt buộc
} elseif (preg_match('#^/admin/packages/(\d+)/services/(\d+)/toggle-required$#', $path, $matches)) {
    $controller->toggleServiceRequired($matches[1], $matches[2]);
```

---

## 🔄 FLOW

### **Admin quản lý dịch vụ:**
```
1. Vào /admin/packages/{id}/services
2. Thấy danh sách dịch vụ
3. Mỗi dịch vụ có:
   - Input giá (sửa inline)
   - Checkbox "Bắt buộc"
   - Nút xóa
4. Click checkbox → Tự động toggle
5. Sửa giá → Enter → Tự động lưu
```

### **Lễ tân đăng ký walk-in:**
```
1. Vào /schedule (Lịch làm việc Bác sĩ)
2. Click "Thêm lịch khám" ở khung giờ
3. Chọn:
   - Loại: Khám thường / Khám gói
   - Nếu gói: Chọn gói khám
   - Thông tin bệnh nhân
4. Đăng ký → Tạo appointment
```

---

## 📊 SO SÁNH

### **Trước:**
```
Admin vào /packages:
✓ Xem gói
✓ Đặt lịch cho chính mình ← SAI!

Admin quản lý dịch vụ:
✓ Sửa giá
❌ Không thể toggle "Bắt buộc"
```

### **Sau:**
```
Admin vào /packages:
✓ Xem gói
✓ Xem chi tiết
❌ KHÔNG có nút "Đặt lịch" ← ĐÚNG!

Admin quản lý dịch vụ:
✓ Sửa giá (inline)
✓ Toggle "Bắt buộc" (checkbox) ← MỚI!
✓ Xóa dịch vụ
```

---

## 🎯 PHÂN QUYỀN

### **Bệnh nhân (Patient):**
- ✅ Xem gói khám
- ✅ Đặt lịch khám gói
- ✅ Đặt lịch khám thường
- ❌ KHÔNG quản lý gói/dịch vụ

### **Admin:**
- ✅ Xem gói khám
- ✅ Thêm/Sửa/Xóa gói
- ✅ Thêm/Sửa/Xóa dịch vụ
- ✅ Sửa giá dịch vụ
- ✅ Toggle "Bắt buộc"
- ❌ KHÔNG đặt lịch cho chính mình

### **Doctor:**
- ✅ Xem gói khám
- ✅ Xem chi tiết
- ❌ KHÔNG quản lý gói/dịch vụ
- ❌ KHÔNG đặt lịch cho chính mình

### **Receptionist (Lễ tân):**
- ✅ Xem gói khám
- ✅ Đăng ký gói cho bệnh nhân walk-in
- ✅ Phân công bác sĩ
- ❌ KHÔNG quản lý gói/dịch vụ
- ❌ KHÔNG đặt lịch cho chính mình

---

## 📝 FILES CẦN SỬA

### **Đã sửa:**
1. ✅ `app/Views/packages/index.php` - Ẩn nút "Đặt lịch"
2. ✅ `app/Views/admin/packages/services.php` - Thêm checkbox toggle

### **Cần thêm:**
3. ⏳ `app/Controllers/PackageController.php` - Thêm method `toggleServiceRequired()`
4. ⏳ `public/index.php` - Thêm route `/admin/packages/{id}/services/{sid}/toggle-required`

---

## 🚀 HÀNH ĐỘNG

### **Bước 1: Test nút "Đặt lịch"**
```
1. Login Admin → Vào /packages
2. Kiểm tra: KHÔNG thấy nút "Đặt lịch" ✅

3. Login Patient → Vào /packages
4. Kiểm tra: Thấy nút "Đặt lịch" ✅
```

### **Bước 2: Thêm route toggle-required**
```
1. Mở app/Controllers/PackageController.php
2. Thêm method toggleServiceRequired()
3. Mở public/index.php
4. Thêm route mới
5. Test: Click checkbox → Toggle thành công
```

### **Bước 3: Test quản lý dịch vụ**
```
1. Login Admin
2. Vào /admin/packages/{id}/services
3. Click checkbox "Bắt buộc"
4. Kiểm tra: Trạng thái đổi ngay ✅
```

---

## 💡 GHI CHÚ

### **Tại sao Admin không đặt lịch?**
- Admin quản lý hệ thống, không phải bệnh nhân
- Nếu Admin cần khám → Tạo tài khoản bệnh nhân riêng

### **Tại sao cần toggle "Bắt buộc"?**
- Một số dịch vụ có thể không bắt buộc (tùy chọn)
- Ví dụ: Xét nghiệm nâng cao, Chụp X-quang,...
- Bệnh nhân có thể bỏ chọn để giảm giá

### **Lễ tân đăng ký walk-in:**
- Bệnh nhân đến trực tiếp (không đặt online)
- Lễ tân nhập thông tin và đăng ký gói
- Hệ thống tạo appointment tự động

---

**Thêm route và test ngay!** 🚀
