# ✅ FIX GIÁ GÓI KHÁM VÀ PHÂN CÔNG BÁC SĨ

## 🐛 VẤN ĐỀ ĐÃ PHÁT HIỆN

### **1. Giá không khớp**
- ❌ Giá gốc gói: **3,580,000đ**
- ❌ Tổng dịch vụ: **6,830,000đ** (5 dịch vụ x 200k = 1,000,000đ ???)
- ❌ Chênh lệch quá lớn!

### **2. Chọn bác sĩ không đúng**
- ❌ Form có "Chọn bác sĩ" ở dưới
- ❌ Không rõ bác sĩ này khám gì
- ❌ Gói khám cần **nhiều bác sĩ** cho từng dịch vụ

---

## ✅ GIẢI PHÁP ĐÃ TRIỂN KHAI

### **1. Fix Giá Dịch Vụ Thực Tế**

**Giá cũ (SAI):**
```
Tất cả dịch vụ = 200,000đ
→ Không thực tế!
```

**Giá mới (ĐÚNG):**
```sql
-- Khám tổng quát
Khám tổng quát:      200,000đ
Khám nội khoa:       150,000đ
Khám tai mũi họng:   150,000đ

-- Xét nghiệm máu (giá thấp)
Công thức máu:        80,000đ
Glucose:              60,000đ
Lipid:               120,000đ
Chức năng gan:       100,000đ
Chức năng thận:      100,000đ
HbA1c:               150,000đ
Acid uric:            90,000đ

-- Xét nghiệm nước tiểu
Tổng phân tích:       50,000đ

-- Chẩn đoán hình ảnh
Siêu âm:             400,000đ
X-quang:             350,000đ
Điện tim:            300,000đ

-- Khám chuyên khoa
Khám phụ khoa:       250,000đ
Khám tiết niệu:      250,000đ
Khám mắt:            200,000đ
```

**Tổng dịch vụ Nam:** ~2,160,000đ  
**Tổng dịch vụ Nữ:** ~2,410,000đ (thêm phụ khoa)  
**Giá gói (+ 10% phí):** 2,400,000đ (Nam) / 2,650,000đ (Nữ)

---

### **2. Phân Biệt Bác Sĩ**

#### **A. Khám Thường:**
```
- Chọn 1 bác sĩ (BẮT BUỘC)
- Bác sĩ này khám trực tiếp
- Lưu vào: appointments.doctor_id
```

#### **B. Khám Theo Gói:**
```
- Chọn bác sĩ điều phối (TÙY CHỌN)
- Bác sĩ này chỉ điều phối, không khám
- Lưu vào: appointments.coordinator_doctor_id
- Bác sĩ cho từng dịch vụ phân công sau
- Lưu vào: appointment_package_services.doctor_id
```

---

## 📊 DATABASE SCHEMA

### **Bảng `appointments`:**
```sql
appointments
├── doctor_id (NULL cho gói khám)
├── coordinator_doctor_id (Bác sĩ điều phối - gói khám)
├── package_id
├── appointment_type (regular/package)
└── total_price (Giá thực tế đã chọn)
```

### **Bảng `appointment_package_services`:**
```sql
appointment_package_services
├── appointment_id
├── service_id
├── service_price (Giá lúc đặt)
├── doctor_id (Bác sĩ phụ trách dịch vụ này)
├── status (pending/completed/cancelled)
└── result_value (Kết quả xét nghiệm)
```

---

## 🎯 FLOW HOẠT ĐỘNG

### **Bệnh nhân đặt gói khám:**
```
1. Chọn "Khám theo gói"
2. Chọn gói: "Gói khám tổng quát - Nam"
3. Thấy giá gốc: 2,400,000đ
4. Thấy danh sách dịch vụ:
   [x] Khám tổng quát      200,000đ [Bắt buộc]
   [x] Công thức máu        80,000đ [Tùy chọn]
   [x] Glucose              60,000đ [Tùy chọn]
   [ ] Siêu âm             400,000đ [Tùy chọn]
   [ ] X-quang             350,000đ [Tùy chọn]
   
5. Bỏ chọn Siêu âm + X-quang
6. Tổng giá: 340,000đ
7. Chọn bác sĩ điều phối (tùy chọn): Dr. Nguyễn Văn A
8. Submit
```

### **Admin/Lễ tân phân công bác sĩ:**
```
1. Vào chi tiết appointment
2. Thấy danh sách dịch vụ đã chọn:
   ├─ Khám tổng quát → [Chọn BS] Dr. Nguyễn Văn A
   ├─ Công thức máu  → [Chọn BS] Dr. Trần Thị B (XN)
   └─ Glucose        → [Chọn BS] Dr. Trần Thị B (XN)
3. Lưu → Cập nhật doctor_id cho từng dịch vụ
```

### **Bác sĩ nhập kết quả:**
```
1. Dr. Nguyễn Văn A login
2. Thấy danh sách dịch vụ được phân công
3. Nhập kết quả "Khám tổng quát"
4. Lưu vào appointment_package_services
```

---

## 🎨 UI/UX

### **Form đặt lịch - Khám thường:**
```
┌────────────────────────────────────┐
│ [•] Khám thường                    │
├────────────────────────────────────┤
│ Chọn chuyên khoa: Nội khoa         │
│ Chọn bác sĩ: Dr. Nguyễn Văn A *   │
│ (Bác sĩ khám chính)                │
└────────────────────────────────────┘
```

### **Form đặt lịch - Khám theo gói:**
```
┌────────────────────────────────────┐
│ [•] Khám theo gói                  │
├────────────────────────────────────┤
│ Chọn gói: Gói tổng quát - Nam      │
│ Giá gốc: 2,400,000đ                │
├────────────────────────────────────┤
│ Chọn dịch vụ:                      │
│ [x] Khám tổng quát    200,000đ    │
│ [x] Công thức máu      80,000đ    │
│ [ ] Siêu âm           400,000đ    │
│                                    │
│ Tổng: 280,000đ                     │
├────────────────────────────────────┤
│ Chọn bác sĩ điều phối (tùy chọn)  │
│ Dr. Nguyễn Văn A                   │
│ (Bác sĩ cho từng dịch vụ sẽ được  │
│  phân công sau)                    │
└────────────────────────────────────┘
```

---

## 📝 FILES ĐÃ SỬA

### **SQL:**
1. ✅ `sql/update_package_services_price.sql`
   - Cập nhật giá dịch vụ thực tế
   - Cập nhật giá gói cho đúng
   - Thêm cột `coordinator_doctor_id`

### **Controllers:**
2. ✅ `AppointmentController.php`
   - Phân biệt `doctor_id` vs `coordinator_doctor_id`
   - Logic lưu khác nhau cho khám thường vs gói

### **Models:**
3. ✅ `Appointment.php`
   - Thêm property `coordinator_doctor_id`
   - Cập nhật query INSERT

### **Views:**
4. ✅ `appointments/create.php`
   - Thay đổi label bác sĩ động
   - Bỏ required khi chọn gói
   - JavaScript toggle

---

## 🚀 HƯỚNG DẪN SỬ DỤNG

### **BƯỚC 1: Chạy SQL**
```bash
# Vào phpMyAdmin
SOURCE sql/update_package_services_price.sql;
```

### **BƯỚC 2: Test**
```
1. Login bệnh nhân
2. Đặt lịch → Chọn "Khám theo gói"
3. Chọn gói → Xem giá mới
4. Chọn/bỏ dịch vụ → Xem tổng giá
5. Xem label bác sĩ: "Bác sĩ điều phối (tùy chọn)"
6. Submit
```

### **BƯỚC 3: Kiểm tra DB**
```sql
-- Xem appointment
SELECT * FROM appointments WHERE id = [last_id];
-- coordinator_doctor_id có giá trị
-- doctor_id = NULL (với gói khám)

-- Xem dịch vụ đã chọn
SELECT * FROM appointment_package_services 
WHERE appointment_id = [last_id];
-- doctor_id = NULL (chưa phân công)
```

---

## 🎯 TÍNH NĂNG TIẾP THEO

### **1. Trang phân công bác sĩ**
**File:** `app/Views/appointments/assign-doctors.php`

```
Appointment #APT-001 - Gói khám tổng quát

Dịch vụ đã chọn:
┌────────────────────────────────────────┐
│ Khám tổng quát (200,000đ)             │
│ Bác sĩ: [Dropdown chọn BS] → Dr. A    │
├────────────────────────────────────────┤
│ Công thức máu (80,000đ)               │
│ Bác sĩ: [Dropdown chọn BS] → Dr. B    │
├────────────────────────────────────────┤
│ Glucose (60,000đ)                     │
│ Bác sĩ: [Dropdown chọn BS] → Dr. B    │
└────────────────────────────────────────┘

[Lưu phân công]
```

### **2. Trang nhập kết quả**
**File:** `app/Views/appointments/package-results.php`

```
Dịch vụ của tôi (Dr. Nguyễn Văn A):

Appointment #APT-001:
┌────────────────────────────────────────┐
│ Khám tổng quát                         │
│ Kết quả: [Textarea]                    │
│ Trạng thái: [Normal/Abnormal]         │
│ [Lưu kết quả]                          │
└────────────────────────────────────────┘
```

---

## 📊 SO SÁNH TRƯỚC VÀ SAU

### **TRƯỚC (Lỗi):**
```
Giá gói: 3,580,000đ
Tổng dịch vụ: 6,830,000đ
→ Chênh lệch 3,250,000đ ❌

Chọn bác sĩ: Bắt buộc
→ Không rõ bác sĩ này làm gì ❌
```

### **SAU (Đúng):**
```
Giá gói: 2,400,000đ
Tổng dịch vụ: ~2,160,000đ
→ Gói = tổng + 10% phí ✅

Chọn bác sĩ điều phối: Tùy chọn
→ Bác sĩ cho từng dịch vụ phân công sau ✅
```

---

## ⚠️ LƯU Ý

1. **Giá gói phải >= tổng dịch vụ bắt buộc**
2. **Bệnh nhân có thể bỏ dịch vụ tùy chọn** → Giá giảm
3. **Admin phân công bác sĩ sau** khi bệnh nhân đặt lịch
4. **Mỗi dịch vụ có thể có bác sĩ khác nhau**
5. **Coordinator doctor** chỉ điều phối, không khám

---

**🎉 HOÀN THÀNH! Chạy SQL và reload trang!** 🚀
