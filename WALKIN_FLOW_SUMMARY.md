# ✅ FLOW ĐĂNG KÝ WALK-IN - GIẢI THÍCH RÕ RÀNG

## 🎯 FLOW ĐÚNG

### **Bước 1: Vào trang "Đăng ký khám Walk-in"**
```
URL: /schedule
Tiêu đề: "Đăng ký khám Walk-in" (nếu là Receptionist)
```

**Màn hình:**
```
┌─────────────────────────────────┐
│ Đăng ký khám Walk-in            │
├─────────────────────────────────┤
│ Bác sĩ: [BS.Vanh Le - Mắt ▼]   │
│ Ngày: [31/10/2025]              │
│ [Hôm nay]                       │
├─────────────────────────────────┤
│ BS.Vanh Le                   31 │
│ Mắt                  Tháng 10   │
├─────────────────────────────────┤
│ 08:00  Đã qua (không thể đặt)   │
│ 09:00  Đã qua (không thể đặt)   │
│ 10:00  Đã qua (không thể đặt)   │
│ 11:00  Đã qua (không thể đặt)   │
│ 12:00  Slot trống               │
│        [+ Thêm bệnh nhân] ← NÚT XANH LÁ
└─────────────────────────────────┘
```

### **Bước 2: Click nút "Thêm bệnh nhân" (màu xanh lá)**
```
Click vào nút xanh lá ở slot trống
→ Chuyển đến trang form
```

### **Bước 3: Form "Thêm Bệnh nhân Walk-in"**
```
URL: /schedule/add-patient?doctor_id=X&date=2025-10-31&time=12:00:00
```

**Màn hình:**
```
┌─────────────────────────────────┐
│ Thêm Bệnh nhân Walk-in          │
├─────────────────────────────────┤
│ Bác sĩ: BS.Vanh Le - Mắt        │
│ Thời gian: 31/10/2025 - 12:00   │
├─────────────────────────────────┤
│ Loại khám: *                    │
│ ● Khám thường                   │
│ ○ Khám theo gói                 │
├─────────────────────────────────┤
│ ... (form tiếp theo)            │
└─────────────────────────────────┘
```

### **Bước 4a: Nếu chọn "Khám thường"**
```
┌─────────────────────────────────┐
│ Loại khám: ● Khám thường        │
├─────────────────────────────────┤
│ Loại bệnh nhân: *               │
│ ● Bệnh nhân cũ                  │
│ ○ Bệnh nhân mới                 │
├─────────────────────────────────┤
│ Chọn bệnh nhân: [Dropdown ▼]   │
│ Lý do khám: [____________]      │
│ [Hủy] [Xác nhận thêm]          │
└─────────────────────────────────┘
```

### **Bước 4b: Nếu chọn "Khám theo gói"**
```
┌─────────────────────────────────┐
│ Loại khám: ● Khám theo gói      │
├─────────────────────────────────┤
│ Chọn gói khám: *                │
│ [Gói khám tổng quát - Nam ▼]   │
│                                 │
│ ┌─────────────────────────────┐ │
│ │ Gói khám tổng quát - Nam    │ │
│ │ ✓ Điện tim ECG              │ │
│ │ ✓ Xét nghiệm máu            │ │
│ │ Tổng: 6,180,000 đ           │ │
│ └─────────────────────────────┘ │
├─────────────────────────────────┤
│ Lý do khám: [____________]      │
│ [Hủy] [Xác nhận thêm]          │
└─────────────────────────────────┘

KHÔNG có "Loại bệnh nhân" ✅
KHÔNG có "Chọn bệnh nhân" ✅
```

---

## ⚠️ VẤN ĐỀ HIỆN TẠI

### **Vấn đề 1: Không thấy nút "Thêm bệnh nhân"**

**Nguyên nhân:**
- Chưa login với role Receptionist
- Hoặc đang xem slot đã qua (isPast = true)

**Fix:**
```
1. Login với role Receptionist
2. Chọn ngày hôm nay hoặc tương lai
3. Nút "Thêm bệnh nhân" sẽ hiện ở slot trống
```

### **Vấn đề 2: Form không có "Loại khám"**

**Nguyên nhân:**
- Cache trình duyệt chưa reload file mới
- Hoặc đang xem file cũ

**Fix:**
```
1. Hard refresh: Ctrl + Shift + R
2. Hoặc xóa cache: F12 → Application → Clear storage
```

---

## 📸 SO SÁNH

### **Ảnh 1 (Trang chọn bác sĩ):**
```
Đây là trang /schedule
- Chọn bác sĩ
- Chọn ngày
- Xem lịch làm việc
- Click "Thêm bệnh nhân" → Chuyển sang form
```

### **Ảnh 2 (Trang form - THIẾU "Loại khám"):**
```
Đây là trang /schedule/add-patient
- THIẾU: Loại khám (Thường/Gói)
- Chỉ có: Bác sĩ, Ngày, Lịch làm việc

→ File cũ chưa có "Loại khám"!
→ Cần hard refresh!
```

---

## ✅ CHECKLIST

### **Kiểm tra trang /schedule:**
- [ ] Login với role Receptionist
- [ ] Tiêu đề hiện "Đăng ký khám Walk-in"
- [ ] Chọn bác sĩ + ngày
- [ ] Thấy nút "Thêm bệnh nhân" (màu xanh lá) ở slot trống

### **Kiểm tra trang /schedule/add-patient:**
- [ ] Click "Thêm bệnh nhân"
- [ ] Form hiện tiêu đề "Thêm Bệnh nhân Walk-in"
- [ ] Thấy "Loại khám: ● Khám thường ○ Khám theo gói"
- [ ] Click "Khám theo gói" → Ẩn "Chọn bệnh nhân", hiện "Chọn gói"

---

## 🚀 HÀNH ĐỘNG

### **1. Kiểm tra role:**
```php
// Trong trang /schedule
var_dump($_SESSION['role']); // Phải ra: "receptionist"
```

### **2. Hard refresh:**
```
Ctrl + Shift + R
```

### **3. Kiểm tra file:**
```
Mở: app/Views/schedule/add_patient.php
Tìm: Loại khám
→ Phải có radio button "Khám thường" / "Khám theo gói"
```

### **4. Test:**
```
1. Vào /schedule
2. Chọn bác sĩ + ngày
3. Click "Thêm bệnh nhân" (nút xanh lá)
4. Kiểm tra form có "Loại khám" không
5. Click "Khám theo gói"
6. Kiểm tra "Chọn bệnh nhân" có ẨN không
```

---

## 📁 FILES

1. ✅ `app/Views/schedule/index.php` - Trang chọn bác sĩ (có nút "Thêm bệnh nhân")
2. ✅ `app/Views/schedule/add_patient.php` - Form đăng ký (có "Loại khám")

---

**Hard refresh và test theo checklist!** 🚀

Nếu vẫn không thấy nút "Thêm bệnh nhân", chụp màn hình Console (F12) cho tôi xem!
