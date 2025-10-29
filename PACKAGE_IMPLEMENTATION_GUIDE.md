# 📦 HƯỚNG DẪN TRIỂN KHAI HỆ THỐNG GÓI KHÁM

## ✅ ĐÃ HOÀN THÀNH

### 1. **Database**
- ✅ Bảng `health_packages` - Lưu thông tin gói khám
- ✅ Bảng `package_services` - Danh sách dịch vụ trong gói
- ✅ Bảng `package_test_results` - Kết quả xét nghiệm
- ✅ Cập nhật bảng `appointments` - Thêm `package_id`, `appointment_type`
- ✅ Dữ liệu mẫu: 2 gói khám (Nam/Nữ) với đầy đủ dịch vụ

**File SQL:** `sql/add_health_packages.sql`

---

### 2. **Models**
- ✅ `app/Models/HealthPackage.php`
  - `getAllActive()` - Lấy gói đang hoạt động
  - `findById($id)` - Chi tiết gói
  - `getPackagesForPatient($gender, $age)` - Gói phù hợp
  - `getServices($package_id)` - Dịch vụ trong gói
  - `create()`, `update()` - CRUD operations

- ✅ Cập nhật `app/Models/Appointment.php`
  - Thêm properties: `package_id`, `appointment_type`
  - Cập nhật method `create()` để lưu package

---

### 3. **Controllers**
- ✅ `app/Controllers/PackageController.php`
  - **Public:**
    - `index()` - Danh sách gói khám (có filter)
    - `show($id)` - Chi tiết gói
  - **Admin:**
    - `adminIndex()` - Quản lý gói khám
    - `create()`, `store()` - Thêm gói mới
    - `edit($id)`, `update($id)` - Sửa gói
    - `delete($id)` - Xóa gói
    - `toggleStatus($id)` - Bật/tắt gói
    - `manageServices($id)` - Quản lý dịch vụ
    - `addService()`, `deleteService()` - CRUD dịch vụ

- ✅ Cập nhật `app/Controllers/AppointmentController.php`
  - Thêm `packageModel`
  - Method `create()`: Lấy danh sách gói phù hợp
  - Method `store()`: Lưu `package_id` và `appointment_type`

---

### 4. **Views**

#### **Public Views:**
- ✅ `app/Views/packages/index.php`
  - Danh sách gói khám với filter (giới tính, tuổi)
  - Card hiển thị giá, dịch vụ preview
  - Button "Đặt lịch" và "Chi tiết"

- ✅ `app/Views/packages/show.php`
  - Chi tiết đầy đủ gói khám
  - Danh sách dịch vụ nhóm theo category
  - Sidebar đặt lịch + thông tin liên hệ

#### **Admin Views:**
- ✅ `app/Views/admin/packages/index.php`
  - Bảng quản lý gói khám
  - Stats cards (tổng gói, dịch vụ, lượt đặt)
  - Actions: Xem, Sửa, Xóa, Toggle status

- ✅ `app/Views/admin/packages/create.php`
  - Form thêm gói mới
  - Dynamic price fields theo giới tính
  - Validation

- ✅ `app/Views/admin/packages/edit.php`
  - Form sửa gói
  - Pre-fill dữ liệu

- ✅ `app/Views/admin/packages/services.php`
  - Form thêm dịch vụ (sidebar)
  - Danh sách dịch vụ nhóm theo category
  - Xóa dịch vụ

---

### 5. **Routes**
✅ Đã thêm vào `routes/web.php`:

```php
// Public
GET  /packages                          → Danh sách gói khám
GET  /packages/{id}                     → Chi tiết gói

// Admin
GET  /admin/packages                    → Quản lý gói khám
GET  /admin/packages/create             → Form thêm
POST /admin/packages/store              → Lưu gói mới
GET  /admin/packages/{id}/edit          → Form sửa
POST /admin/packages/{id}/update        → Cập nhật
POST /admin/packages/{id}/delete        → Xóa
POST /admin/packages/{id}/toggle-status → Bật/tắt

// Services
GET  /admin/packages/{id}/services                      → Quản lý dịch vụ
POST /admin/packages/{package_id}/services/add          → Thêm dịch vụ
POST /admin/packages/{package_id}/services/{id}/delete  → Xóa dịch vụ
```

---

## 🔄 CẦN LÀM TIẾP

### 1. **Cập nhật Menu/Navigation**

Thêm link vào sidebar (`app/Views/layouts/main.php`):

```php
<!-- Sau menu Bác sĩ -->
<a href="<?= APP_URL ?>/packages" class="sidebar-link...">
    <i class="fas fa-box-open w-5"></i>
    <span>Gói khám</span>
</a>

<!-- Admin menu -->
<?php if (Auth::isAdmin()): ?>
<a href="<?= APP_URL ?>/admin/packages" class="sidebar-link...">
    <i class="fas fa-box-open w-5"></i>
    <span>QL Gói khám</span>
</a>
<?php endif; ?>
```

---

### 2. **Cập nhật Form Đặt Lịch**

File: `app/Views/appointments/create.php`

Thêm section chọn loại khám:

```php
<!-- Chọn loại khám -->
<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-3">
        Loại khám *
    </label>
    <div class="grid grid-cols-2 gap-4">
        <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-purple-500 transition">
            <input type="radio" name="appointment_type" value="regular" 
                   <?= empty($selected_package) ? 'checked' : '' ?>
                   onchange="togglePackageSelection(false)"
                   class="mr-3">
            <div>
                <div class="font-semibold">Khám thường</div>
                <div class="text-sm text-gray-500">Khám bệnh theo triệu chứng</div>
            </div>
        </label>
        
        <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-purple-500 transition">
            <input type="radio" name="appointment_type" value="package"
                   <?= !empty($selected_package) ? 'checked' : '' ?>
                   onchange="togglePackageSelection(true)"
                   class="mr-3">
            <div>
                <div class="font-semibold">Khám theo gói</div>
                <div class="text-sm text-gray-500">Khám sức khỏe tổng quát</div>
            </div>
        </label>
    </div>
</div>

<!-- Chọn gói khám (ẩn nếu chọn khám thường) -->
<div id="package_selection" style="display: <?= !empty($selected_package) ? 'block' : 'none' ?>">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Chọn gói khám *
    </label>
    <select name="package_id" id="package_id" 
            onchange="updatePackagePrice(this.value)"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg">
        <option value="">-- Chọn gói khám --</option>
        <?php foreach ($eligible_packages ?? [] as $pkg): ?>
        <option value="<?= $pkg['id'] ?>" 
                data-price-male="<?= $pkg['price_male'] ?>"
                data-price-female="<?= $pkg['price_female'] ?>"
                <?= ($selected_package && $selected_package['id'] == $pkg['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($pkg['name']) ?>
        </option>
        <?php endforeach; ?>
    </select>
    
    <!-- Hiển thị giá gói -->
    <div id="package_price" class="mt-3 p-4 bg-purple-50 rounded-lg" style="display:none">
        <div class="text-sm text-gray-600">Giá gói khám:</div>
        <div class="text-2xl font-bold text-purple-600" id="price_display"></div>
    </div>
</div>

<script>
function togglePackageSelection(show) {
    document.getElementById('package_selection').style.display = show ? 'block' : 'none';
    if (!show) {
        document.getElementById('package_id').value = '';
        document.getElementById('package_price').style.display = 'none';
    }
}

function updatePackagePrice(packageId) {
    if (!packageId) {
        document.getElementById('package_price').style.display = 'none';
        return;
    }
    
    const select = document.getElementById('package_id');
    const option = select.options[select.selectedIndex];
    const priceMale = option.dataset.priceMale;
    const priceFemale = option.dataset.priceFemale;
    
    let priceHtml = '';
    if (priceMale && priceFemale) {
        priceHtml = `
            <div>Nam: ${parseInt(priceMale).toLocaleString('vi-VN')} đ</div>
            <div>Nữ: ${parseInt(priceFemale).toLocaleString('vi-VN')} đ</div>
        `;
    } else if (priceMale) {
        priceHtml = parseInt(priceMale).toLocaleString('vi-VN') + ' đ';
    } else if (priceFemale) {
        priceHtml = parseInt(priceFemale).toLocaleString('vi-VN') + ' đ';
    }
    
    document.getElementById('price_display').innerHTML = priceHtml;
    document.getElementById('package_price').style.display = 'block';
}

// Initialize if package is pre-selected
<?php if (!empty($selected_package)): ?>
updatePackagePrice(<?= $selected_package['id'] ?>);
<?php endif; ?>
</script>
```

---

### 3. **Hiển thị Gói khám trong Danh sách Lịch hẹn**

File: `app/Views/appointments/index.php`

Thêm cột "Loại khám" và badge:

```php
<td class="px-6 py-4 text-sm">
    <?php if ($appointment['appointment_type'] == 'package'): ?>
        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">
            <i class="fas fa-box-open mr-1"></i>Gói khám
        </span>
        <?php if ($appointment['package_name']): ?>
        <div class="text-xs text-gray-500 mt-1">
            <?= htmlspecialchars($appointment['package_name']) ?>
        </div>
        <?php endif; ?>
    <?php else: ?>
        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">
            <i class="fas fa-stethoscope mr-1"></i>Khám thường
        </span>
    <?php endif; ?>
</td>
```

Cập nhật query trong `Appointment::getAll()`:

```php
$query = "SELECT a.*, 
          p.full_name as patient_name, p.phone as patient_phone,
          d.doctor_code, u.full_name as doctor_name,
          s.name as specialization,
          hp.name as package_name
          FROM appointments a
          LEFT JOIN patients p ON a.patient_id = p.id
          LEFT JOIN doctors d ON a.doctor_id = d.id
          LEFT JOIN users u ON d.user_id = u.id
          LEFT JOIN specializations s ON d.specialization_id = s.id
          LEFT JOIN health_packages hp ON a.package_id = hp.id
          ORDER BY a.appointment_date DESC, a.appointment_time DESC";
```

---

### 4. **Tính năng Nhập Kết quả Xét nghiệm** (Optional - Phase 2)

Tạo trang cho bác sĩ/admin nhập kết quả:

- `app/Views/appointments/package_results.php`
- Controller method: `viewPackageResults($appointment_id)`
- Form nhập từng dịch vụ với:
  - Giá trị kết quả
  - Trạng thái (normal/abnormal/pending)
  - Ghi chú

---

## 🚀 HƯỚNG DẪN SỬ DỤNG

### **Cho Admin:**
1. Vào `/admin/packages` → Quản lý gói khám
2. Click "Thêm gói khám" → Điền thông tin
3. Sau khi tạo → Tự động chuyển đến trang thêm dịch vụ
4. Thêm các dịch vụ/xét nghiệm vào gói
5. Toggle trạng thái để kích hoạt/tạm dừng gói

### **Cho Bệnh nhân:**
1. Vào `/packages` → Xem danh sách gói khám
2. Filter theo giới tính và tuổi
3. Click "Chi tiết" → Xem đầy đủ dịch vụ
4. Click "Đặt lịch" → Form đặt lịch với gói đã chọn
5. Hoàn tất đặt lịch

### **Flow đặt lịch theo gói:**
```
Trang gói khám → Chọn gói → Đặt lịch → Chọn bác sĩ & thời gian → Xác nhận
```

---

## 📊 DATABASE SCHEMA

```sql
health_packages
├── id (PK)
├── package_code (UNIQUE)
├── name
├── description
├── price_male
├── price_female
├── gender_requirement (both/male/female)
├── min_age
├── max_age
└── is_active

package_services
├── id (PK)
├── package_id (FK → health_packages)
├── service_name
├── service_category (general/blood_test/urine_test/imaging/specialist/other)
├── is_required
├── gender_specific
├── notes
└── display_order

appointments (UPDATED)
├── ... (existing columns)
├── package_id (FK → health_packages) [NEW]
└── appointment_type (regular/package) [NEW]

package_test_results
├── id (PK)
├── appointment_id (FK → appointments)
├── service_id (FK → package_services)
├── result_value
├── result_status (normal/abnormal/pending)
├── notes
└── tested_at
```

---

## ✅ CHECKLIST TRIỂN KHAI

- [x] Chạy SQL migration
- [x] Tạo Models
- [x] Tạo Controllers
- [x] Tạo Views (Public + Admin)
- [x] Thêm Routes
- [x] Cập nhật AppointmentController
- [x] Cập nhật Appointment Model
- [ ] Thêm menu links
- [ ] Cập nhật form đặt lịch
- [ ] Cập nhật danh sách lịch hẹn
- [ ] Test đầy đủ flow

---

## 🎯 KẾT QUẢ MONG ĐỢI

Sau khi hoàn thành, hệ thống sẽ có:

1. ✅ Trang public hiển thị gói khám đẹp mắt
2. ✅ Admin quản lý gói khám + dịch vụ dễ dàng
3. ✅ Bệnh nhân đặt lịch theo gói khám
4. ✅ Phân biệt rõ "Khám thường" vs "Khám theo gói"
5. ✅ Giá gói khám hiển thị theo giới tính
6. ✅ Filter gói khám phù hợp với tuổi/giới tính

**Phù hợp 100% với đề tài: "Ứng dụng chăm sóc sức khỏe và quản lý gói dịch vụ khám chữa bệnh tại bệnh viện tư"** 🎉
