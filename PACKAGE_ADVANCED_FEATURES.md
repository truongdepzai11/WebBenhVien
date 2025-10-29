# 🎯 TÍNH NĂNG NÂNG CAO - GÓI KHÁM

## ✅ ĐÃ TRIỂN KHAI

### **1. Chọn/Bỏ Dịch Vụ Không Bắt Buộc** ✅
- Bệnh nhân có thể **bỏ chọn** dịch vụ không bắt buộc
- Dịch vụ **bắt buộc** (is_required=1) không thể bỏ
- Giá tự động **giảm** khi bỏ dịch vụ

### **2. Hiển Thị Giá Chi Tiết** ✅
- Mỗi dịch vụ hiển thị **giá riêng**
- Tổng giá **tính động** theo dịch vụ được chọn
- So sánh với giá gốc của gói

### **3. Database Schema Mới** ✅

**Bảng `package_services` - Thêm cột:**
```sql
service_price DECIMAL(10,2) -- Giá từng dịch vụ
```

**Bảng `appointment_package_services` - MỚI:**
```sql
CREATE TABLE appointment_package_services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT,
    service_id INT,
    service_price DECIMAL(10,2),
    doctor_id INT NULL,  -- Bác sĩ phụ trách dịch vụ này
    status ENUM('pending', 'completed', 'cancelled'),
    result_value TEXT,
    result_status ENUM('normal', 'abnormal', 'pending'),
    notes TEXT,
    tested_at DATETIME
)
```

**Bảng `appointments` - Thêm cột:**
```sql
total_price DECIMAL(10,2),  -- Tổng giá thực tế
coordinator_doctor_id INT   -- Bác sĩ phụ trách chính
```

---

## 🔧 FLOW HOẠT ĐỘNG

### **A. Bệnh nhân đặt lịch:**
```
1. Chọn "Khám theo gói"
2. Chọn gói khám
3. Hệ thống load danh sách dịch vụ (AJAX)
4. Hiển thị:
   ├─ [x] Khám tổng quát (150,000đ) [Bắt buộc]
   ├─ [x] Xét nghiệm máu (200,000đ) [Tùy chọn]
   ├─ [ ] Siêu âm (500,000đ) [Tùy chọn]
   └─ Tổng: 350,000đ
5. Bệnh nhân bỏ chọn "Siêu âm"
6. Tổng giá tự động giảm xuống 350,000đ
7. Submit → Lưu:
   - appointments.total_price = 350,000
   - appointment_package_services (chỉ 2 dịch vụ được chọn)
```

### **B. Lễ tân đăng ký Walk-in:**
```
1. Vào /schedule (trang Walk-in)
2. Chọn "Đặt theo gói"
3. Chọn bệnh nhân
4. Chọn gói khám
5. Chọn/bỏ dịch vụ
6. Chọn bác sĩ phụ trách chính
7. Submit → Tạo appointment ngay
```

### **C. Phân công bác sĩ:**
```
Admin/Lễ tân:
1. Vào chi tiết appointment (gói khám)
2. Thấy danh sách dịch vụ:
   ├─ Khám tổng quát → [Chọn BS] Dr. Nguyễn Văn A
   ├─ Xét nghiệm máu → [Chọn BS] Dr. Trần Thị B
   └─ Siêu âm → [Chọn BS] Dr. Lê Văn C
3. Lưu → Cập nhật appointment_package_services.doctor_id
```

---

## 📊 UI/UX

### **Form đặt lịch:**
```
┌────────────────────────────────────────┐
│ Loại khám: [•] Khám theo gói           │
├────────────────────────────────────────┤
│ Chọn gói: Gói khám tổng quát - Nam     │
│ Giá gốc: 3,580,000đ                    │
├────────────────────────────────────────┤
│ Chọn dịch vụ khám:                     │
│                                        │
│ [x] Khám tổng quát       150,000đ     │
│     (Bắt buộc)                         │
│                                        │
│ [x] Xét nghiệm máu       200,000đ     │
│     (Tùy chọn)                         │
│                                        │
│ [ ] Siêu âm              500,000đ     │
│     (Tùy chọn)                         │
│                                        │
│ ┌──────────────────────────────────┐  │
│ │ Tổng chi phí: 350,000đ           │  │
│ └──────────────────────────────────┘  │
├────────────────────────────────────────┤
│ Chọn bác sĩ: Dr. Nguyễn Văn A         │
│ Ngày khám: 30/10/2025                 │
│ ...                                    │
└────────────────────────────────────────┘
```

---

## 🔗 API ENDPOINTS

### **GET /api/package-services/{package_id}**
**Response:**
```json
{
  "success": true,
  "services": [
    {
      "id": 1,
      "service_name": "Khám tổng quát",
      "service_price": 150000,
      "is_required": 1,
      "notes": "Khám sức khỏe tổng quát"
    },
    {
      "id": 2,
      "service_name": "Xét nghiệm máu",
      "service_price": 200000,
      "is_required": 0,
      "notes": null
    }
  ]
}
```

---

## 📝 FILES ĐÃ TẠO/SỬA

### **SQL:**
- ✅ `sql/update_package_services_price.sql` - Migration

### **Controllers:**
- ✅ `PackageController::getServicesJson()` - API endpoint
- ✅ `AppointmentController::saveAppointmentServices()` - Lưu dịch vụ

### **Models:**
- ✅ `Appointment` - Thêm property `total_price`

### **Views:**
- ✅ `appointments/create.php` - Thêm section chọn dịch vụ

### **Routes:**
- ✅ `GET /api/package-services/{package_id}`

---

## 🚀 HƯỚNG DẪN SỬ DỤNG

### **BƯỚC 1: Chạy SQL Migration**
```sql
SOURCE sql/update_package_services_price.sql;
```

### **BƯỚC 2: Test Flow**
```
1. Login bệnh nhân
2. Vào "Đặt lịch khám"
3. Chọn "Khám theo gói"
4. Chọn gói → Thấy danh sách dịch vụ
5. Bỏ chọn dịch vụ tùy chọn
6. Xem tổng giá thay đổi
7. Submit
```

### **BƯỚC 3: Kiểm tra Database**
```sql
-- Xem appointment vừa tạo
SELECT * FROM appointments WHERE id = [last_id];

-- Xem dịch vụ đã chọn
SELECT * FROM appointment_package_services 
WHERE appointment_id = [last_id];
```

---

## 🎯 TÍNH NĂNG TIẾP THEO (Gợi ý)

### **1. Phân công bác sĩ cho từng dịch vụ**
- View: `appointments/assign-doctors.php`
- Dropdown chọn bác sĩ cho từng dịch vụ
- Lưu vào `appointment_package_services.doctor_id`

### **2. Nhập kết quả xét nghiệm**
- View: `appointments/package-results.php`
- Form nhập từng dịch vụ:
  - Giá trị kết quả
  - Trạng thái (normal/abnormal)
  - Ghi chú
- Lưu vào `appointment_package_services`

### **3. In phiếu kết quả gói khám**
- PDF export
- Bao gồm tất cả dịch vụ + kết quả
- Logo bệnh viện + chữ ký bác sĩ

### **4. Lễ tân Walk-in cho gói khám**
- Tích hợp vào `/schedule`
- Chọn gói + dịch vụ
- Tạo appointment ngay lập tức

### **5. Thống kê doanh thu theo gói**
- Dashboard admin
- Biểu đồ gói khám phổ biến
- Doanh thu theo từng gói

---

## ⚠️ LƯU Ý

1. **Dịch vụ bắt buộc:**
   - Không thể bỏ chọn
   - Checkbox disabled
   - Luôn tính vào tổng giá

2. **Giá động:**
   - JavaScript tính real-time
   - Lưu vào `total_price` khi submit
   - Không dùng giá gốc của gói

3. **Validation:**
   - Phải chọn ít nhất 1 dịch vụ
   - Tổng giá > 0
   - Dịch vụ phải thuộc gói đã chọn

---

## 🐛 TROUBLESHOOTING

### **Lỗi: Services không load**
```
- Check API endpoint: /api/package-services/{id}
- Check console log (F12)
- Verify package_id có services
```

### **Lỗi: Tổng giá = 0**
```
- Check JavaScript calculateTotalPrice()
- Verify service_price trong DB
- Check checkbox có data-price attribute
```

### **Lỗi: Không lưu được services**
```
- Check bảng appointment_package_services đã tạo
- Verify selected_services[] được submit
- Check AppointmentController::saveAppointmentServices()
```

---

**🎉 HỆ THỐNG GÓI KHÁM NÂNG CAO HOÀN THÀNH!**

Chạy SQL migration và test ngay! 🚀
