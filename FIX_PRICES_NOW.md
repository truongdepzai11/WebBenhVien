# 🚨 FIX GIÁ NGAY - ĐƠN GIẢN NHẤT

## ❌ VẤN ĐỀ

Giá hiện tại: **6,180,000đ** (SAI!)  
Giá mong muốn: **~500,000đ** (ĐÚNG!)

---

## ✅ GIẢI PHÁP NHANH

### **Chạy file SQL này:**

```bash
# Vào phpMyAdmin → SQL tab
# Copy-paste nội dung file này:
sql/QUICK_FIX_PRICES.sql
```

### **Hoặc chạy từng lệnh:**

```sql
-- 1. Đặt TẤT CẢ dịch vụ = 50,000đ
UPDATE package_services SET service_price = 50000;

-- 2. Chỉ dịch vụ BẮT BUỘC = 200,000đ
UPDATE package_services SET service_price = 200000 WHERE is_required = 1;

-- 3. Đặt giá gói = 500,000đ
UPDATE health_packages SET 
    price_male = 500000,
    price_female = 500000;

COMMIT;
```

---

## 📊 KẾT QUẢ SAU KHI FIX

### **Dịch vụ:**
```
[x] Dịch vụ bắt buộc    200,000đ
[ ] Dịch vụ 2            50,000đ
[ ] Dịch vụ 3            50,000đ
[ ] Dịch vụ 4            50,000đ
[ ] Dịch vụ 5            50,000đ
```

### **Tổng giá:**
```
Tất cả dịch vụ: 200k + 50k + 50k + 50k + 50k = 400,000đ
Giá gói: 500,000đ (bao gồm phí quản lý)
```

### **Khi bệnh nhân chọn:**
```
Chọn tất cả:     400,000đ
Chỉ bắt buộc:    200,000đ
Bỏ 2 dịch vụ:    300,000đ
```

---

## 🎯 TEST NGAY

1. Chạy SQL
2. Reload trang: `/appointments/create?package_id=1`
3. Chọn "Khám theo gói"
4. Xem giá gói: **500,000đ** ✅
5. Xem tổng dịch vụ: **400,000đ** ✅

---

## 🔧 NẾU VẪN SAI

### **Kiểm tra database:**
```sql
-- Xem giá dịch vụ
SELECT service_name, service_price, is_required 
FROM package_services;

-- Xem giá gói
SELECT name, price_male, price_female 
FROM health_packages;
```

### **Nếu giá vẫn cao:**
```sql
-- Reset về 0
UPDATE package_services SET service_price = 0;

-- Set lại từng dịch vụ theo ID
UPDATE package_services SET service_price = 200000 WHERE id = 1;
UPDATE package_services SET service_price = 50000 WHERE id = 2;
UPDATE package_services SET service_price = 50000 WHERE id = 3;
UPDATE package_services SET service_price = 50000 WHERE id = 4;
UPDATE package_services SET service_price = 50000 WHERE id = 5;
```

---

## 📝 LƯU Ý

1. **Giá gói phải >= tổng dịch vụ bắt buộc**
2. **Dịch vụ bắt buộc (is_required=1) không thể bỏ**
3. **Bệnh nhân chỉ có thể bỏ dịch vụ tùy chọn**
4. **Tổng giá tính động theo dịch vụ được chọn**

---

## 🎉 HOÀN THÀNH

Sau khi chạy SQL:
- Giá gói: **500,000đ**
- Tổng dịch vụ: **400,000đ**
- Chênh lệch hợp lý: **100,000đ** (phí quản lý)

**Reload trang và test ngay!** 🚀
