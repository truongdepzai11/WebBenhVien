# ✅ GIÁ GÓI KHÁM ĐỘNG - TÍNH TỰ ĐỘNG

## 🎯 LOGIC MỚI

### **Trước (SAI):**
```
health_packages.price_male = 3,580,000đ (cố định)
health_packages.price_female = 4,370,000đ (cố định)
→ Không linh hoạt, khó quản lý
```

### **Sau (ĐÚNG):**
```
Giá gói = SUM(giá các dịch vụ trong gói)
→ Admin sửa giá dịch vụ → Giá gói tự động thay đổi
→ Linh hoạt, dễ quản lý
```

---

## 📊 CÁCH HOẠT ĐỘNG

### **1. Admin thêm dịch vụ:**
```
Tên: Đo nồng độ HDL-C
Giá: 200,000đ
Bắt buộc: ✓
→ Lưu vào package_services
```

### **2. Admin sửa giá dịch vụ:**
```
Đo nồng độ HDL-C: 200,000đ → 150,000đ
→ Chỉ cần sửa 1 ô input
→ Tự động submit khi thay đổi
```

### **3. Hệ thống tính tổng:**
```php
$totalPrice = 0;
foreach ($services as $service) {
    $totalPrice += $service['service_price'];
}
// Giá gói = $totalPrice
```

### **4. Bệnh nhân đặt lịch:**
```
Chọn gói → Thấy tất cả dịch vụ + giá
Bỏ dịch vụ tùy chọn → Giá giảm
Tổng = SUM(dịch vụ được chọn)
```

---

## 🎨 GIAO DIỆN ADMIN

### **Trang quản lý dịch vụ:**
```
┌─────────────────────────────────────────┐
│ Quản lý Dịch vụ - Gói khám tổng quát    │
├─────────────────────────────────────────┤
│ [Sidebar]                               │
│ ┌─────────────────┐                     │
│ │ Thêm dịch vụ    │                     │
│ │ Tên: [____]     │                     │
│ │ Giá: [50000]    │ ← MỚI              │
│ │ [Thêm]          │                     │
│ ├─────────────────┤                     │
│ │ 28              │                     │
│ │ Tổng dịch vụ    │                     │
│ ├─────────────────┤                     │
│ │ 6,180,000đ      │ ← TỔNG TỰ ĐỘNG    │
│ │ Tổng giá gói    │                     │
│ └─────────────────┘                     │
│                                         │
│ [Danh sách dịch vụ]                     │
│ ┌─────────────────────────────────────┐ │
│ │ Xét nghiệm máu                      │ │
│ ├─────────────────────────────────────┤ │
│ │ Đo HDL-C                            │ │
│ │ Giá: [200000] đ ← CÓ THỂ SỬA       │ │
│ │                                     │ │
│ │ Đo LDL-C                            │ │
│ │ Giá: [50000] đ  ← CÓ THỂ SỬA       │ │
│ └─────────────────────────────────────┘ │
└─────────────────────────────────────────┘
```

---

## 📝 FILES ĐÃ SỬA

### **1. View - Admin:**
`app/Views/admin/packages/services.php`
- ✅ Thêm input "Giá dịch vụ" vào form thêm
- ✅ Hiển thị input giá cho mỗi dịch vụ (có thể sửa inline)
- ✅ Hiển thị tổng giá gói (tính tự động)
- ✅ Auto-submit khi thay đổi giá

### **2. Controller:**
`app/Controllers/PackageController.php`
- ✅ Method `addService()` - Lưu giá khi thêm
- ✅ Method `updateServicePrice()` - Cập nhật giá (MỚI)

### **3. Routes:**
`routes/web.php`
- ✅ Route mới: `POST /admin/packages/{id}/services/{sid}/update-price`

### **4. SQL:**
`sql/remove_package_base_price.sql`
- ✅ Xóa cột `price_male`, `price_female` (không cần nữa)
- ✅ Tạo VIEW `package_prices` để tính giá tự động

---

## 🚀 HƯỚNG DẪN SỬ DỤNG

### **BƯỚC 1: Chạy SQL (Tùy chọn)**
```sql
-- Nếu muốn xóa cột giá gốc
SOURCE sql/remove_package_base_price.sql;
```

### **BƯỚC 2: Test Admin**
```
1. Login admin
2. Vào: /admin/packages/{id}/services
3. Thấy sidebar bên trái:
   - Form thêm dịch vụ (có trường Giá)
   - Tổng giá gói (tự động)
4. Thêm dịch vụ mới với giá 50,000đ
5. Xem tổng giá tăng lên
6. Sửa giá dịch vụ → Tự động submit
7. Xem tổng giá thay đổi
```

### **BƯỚC 3: Test Bệnh nhân**
```
1. Login bệnh nhân
2. Đặt lịch → Chọn "Khám theo gói"
3. Chọn gói → Thấy danh sách dịch vụ + giá
4. Tổng giá = SUM(dịch vụ được chọn)
5. Bỏ dịch vụ tùy chọn → Giá giảm
```

---

## 💡 LỢI ÍCH

### **1. Linh hoạt:**
- Admin sửa giá dịch vụ dễ dàng
- Không cần sửa code
- Không cần tính toán thủ công

### **2. Chính xác:**
- Giá gói = SUM(dịch vụ)
- Không bao giờ sai lệch
- Tự động cập nhật

### **3. Dễ quản lý:**
- Thấy rõ giá từng dịch vụ
- Thấy rõ tổng giá gói
- Thấy rõ dịch vụ bắt buộc

---

## 📊 VÍ DỤ THỰC TẾ

### **Gói khám tổng quát - Nam:**
```
Dịch vụ:
├─ Đo HDL-C              200,000đ [Bắt buộc]
├─ Đo LDL-C               50,000đ [Tùy chọn]
├─ Triglycerid            50,000đ [Tùy chọn]
├─ Tổng phân tích         50,000đ [Tùy chọn]
└─ Điện tim ECG          400,000đ [Tùy chọn]
────────────────────────────────────────
Tổng giá gói:            750,000đ
```

### **Bệnh nhân chọn:**
```
[x] Đo HDL-C              200,000đ
[x] Đo LDL-C               50,000đ
[ ] Triglycerid            50,000đ (bỏ)
[ ] Tổng phân tích         50,000đ (bỏ)
[ ] Điện tim ECG          400,000đ (bỏ)
────────────────────────────────────────
Tổng thanh toán:         250,000đ ✅
```

---

## ⚠️ LƯU Ý

1. **Giá dịch vụ phải > 0**
2. **Dịch vụ bắt buộc không thể bỏ**
3. **Tổng giá tính real-time** (JavaScript)
4. **Admin có thể sửa giá bất cứ lúc nào**
5. **Giá lưu vào appointment khi đặt lịch** (không thay đổi sau)

---

## 🎉 KẾT QUẢ

### **Trước:**
- Giá cố định: 3,580,000đ
- Không linh hoạt
- Khó quản lý

### **Sau:**
- Giá động: SUM(dịch vụ)
- Linh hoạt 100%
- Dễ quản lý

**Admin chỉ cần sửa giá dịch vụ → Giá gói tự động thay đổi!** 🚀
