# HƯỚNG DẪN SỬ DỤNG HỆ THỐNG

## 🚀 CÀI ĐẶT

### 1. Import Database
```sql
-- Mở phpMyAdmin: http://localhost/phpmyadmin
-- Xóa database cũ (nếu có):
DROP DATABASE IF EXISTS hospital_management;

-- Import file: sql/schema.sql
```

### 2. Truy cập hệ thống
```
http://localhost/WebBenhvien/hospital-management-system/public
```

## 👥 TÀI KHOẢN DEMO

### Admin (Quản trị viên)
- **Username:** `admin`
- **Password:** `password`
- **Quyền:** Toàn quyền quản lý hệ thống

### Bác sĩ
- **Username:** `dr.nguyen` / `dr.tran` / `dr.le`
- **Password:** `password`
- **Quyền:** Quản lý bệnh nhân, lịch hẹn

### Bệnh nhân
- **Username:** `patient1` / `patient2`
- **Password:** `password`
- **Quyền:** Đặt lịch khám, xem thông tin cá nhân

## 📋 TÍNH NĂNG THEO VAI TRÒ

### 🔴 ADMIN - Quản trị viên

#### Menu Quản trị:
1. **Quản trị hệ thống** (`/admin`)
   - Xem thống kê tổng quan
   - Truy cập nhanh các chức năng

2. **QL Bác sĩ** (`/admin/doctors`)
   - ✅ Xem danh sách bác sĩ
   - ✅ **Thêm bác sĩ mới** (không cần vào phpMyAdmin)
   - ✅ **Sửa thông tin bác sĩ**
   - ✅ **Xóa bác sĩ**
   - Chọn chuyên khoa từ danh sách có sẵn

3. **QL Chuyên khoa** (`/admin/specializations`)
   - ✅ Xem danh sách chuyên khoa
   - ✅ **Thêm chuyên khoa mới**
   - ✅ Cấu hình độ tuổi (min_age, max_age)
   - ✅ Cấu hình giới tính (male/female/both)
   - ✅ **Xóa chuyên khoa**

4. **QL Users** (`/admin/users`)
   - Xem tất cả người dùng
   - Xem vai trò, ngày tạo

#### Menu Chung:
- **Bệnh nhân** - Xem danh sách bệnh nhân
- **Bác sĩ** - Xem danh sách bác sĩ (view công khai)
- **Lịch hẹn** - Quản lý tất cả lịch hẹn
- **Thông tin cá nhân** - Cập nhật profile, đổi mật khẩu

### 🟢 BÁC SĨ

1. **Dashboard** - Thống kê lịch hẹn của mình
2. **Bệnh nhân** - Xem danh sách bệnh nhân
3. **Bác sĩ** - Xem thông tin đồng nghiệp
4. **Lịch hẹn** - Quản lý lịch hẹn của mình
   - Xác nhận lịch hẹn
   - Hoàn thành khám
   - Hủy lịch
5. **Thông tin cá nhân** - Cập nhật thông tin

### 🔵 BỆNH NHÂN

1. **Dashboard** - Thống kê lịch hẹn
2. **Bác sĩ** - Xem danh sách bác sĩ, đặt lịch
3. **Lịch hẹn** - Xem lịch hẹn của mình
   - ✅ **Đặt lịch khám mới**
   - ✅ **Chọn chuyên khoa phù hợp** (tự động lọc theo tuổi/giới tính)
   - Hủy lịch hẹn
4. **Thông tin cá nhân**
   - ✅ **Cập nhật thông tin y tế** (ngày sinh, giới tính, nhóm máu, dị ứng)
   - ✅ **Đổi mật khẩu**

## 🎯 HƯỚNG DẪN SỬ DỤNG CHI TIẾT

### A. ADMIN - Thêm Bác sĩ mới

1. Đăng nhập với tài khoản `admin`
2. Click **QL Bác sĩ** trong sidebar
3. Click nút **"Thêm bác sĩ"**
4. Điền thông tin:
   - **Tài khoản:** username, email, password, họ tên, SĐT
   - **Chuyên môn:** chọn chuyên khoa, số giấy phép, trình độ, kinh nghiệm, phí khám
   - **Lịch làm việc:** ngày làm việc, giờ làm việc
5. Click **"Lưu bác sĩ"**

✅ **Bác sĩ được tạo ngay lập tức, không cần vào phpMyAdmin!**

### B. ADMIN - Thêm Chuyên khoa mới

1. Click **QL Chuyên khoa**
2. Click **"Thêm chuyên khoa"**
3. Điền thông tin:
   - **Tên:** Ví dụ: "Thần kinh"
   - **Mô tả:** Mô tả về chuyên khoa
   - **Độ tuổi tối thiểu:** 0
   - **Độ tuổi tối đa:** 150
   - **Giới tính:** Cả hai / Chỉ nam / Chỉ nữ
4. Click **"Lưu chuyên khoa"**

**Ví dụ thực tế:**
- **Nhi khoa:** 0-15 tuổi, Cả hai giới
- **Lão khoa:** 60-150 tuổi, Cả hai giới
- **Sản phụ khoa:** 15-60 tuổi, Chỉ nữ
- **Nam khoa:** 18-150 tuổi, Chỉ nam

### C. BỆNH NHÂN - Đặt lịch khám

1. Đăng nhập với tài khoản bệnh nhân
2. Vào **Thông tin cá nhân** → Cập nhật **ngày sinh** và **giới tính** (quan trọng!)
3. Click **Lịch hẹn** → **"Đặt lịch mới"**
4. **Chọn chuyên khoa** (chỉ hiện các chuyên khoa phù hợp với bạn)
5. **Chọn bác sĩ** (tự động lọc theo chuyên khoa)
6. Chọn ngày, giờ khám
7. Nhập lý do khám
8. Click **"Xác nhận đặt lịch"**

**Logic kiểm tra:**
- Nếu bạn là **nam, 25 tuổi** → Không thể đặt Sản phụ khoa, Nhi khoa
- Nếu bạn là **nữ, 70 tuổi** → Không thể đặt Nhi khoa, Nam khoa
- Nếu bạn là **nữ, 8 tuổi** → Chỉ có thể đặt Nhi khoa

## 🔧 CẤU TRÚC DATABASE

### Bảng quan trọng:

**specializations** - Chuyên khoa
- `name` - Tên chuyên khoa
- `min_age` - Độ tuổi tối thiểu
- `max_age` - Độ tuổi tối đa
- `gender_requirement` - Yêu cầu giới tính (male/female/both)

**doctors** - Bác sĩ
- `specialization` - Liên kết với tên chuyên khoa
- `consultation_fee` - Phí khám
- `available_days` - Ngày làm việc
- `available_hours` - Giờ làm việc

**patients** - Bệnh nhân
- `date_of_birth` - Ngày sinh (dùng để tính tuổi)
- `gender` - Giới tính (dùng để kiểm tra chuyên khoa)
- `blood_type` - Nhóm máu
- `allergies` - Dị ứng

## 📊 CHUYÊN KHOA CÓ SẴN

Sau khi import database, hệ thống có 10 chuyên khoa:

1. **Tim mạch** - 0-150 tuổi, Cả hai
2. **Nội khoa** - 0-150 tuổi, Cả hai
3. **Nhi khoa** - 0-15 tuổi, Cả hai
4. **Lão khoa** - 60-150 tuổi, Cả hai
5. **Sản phụ khoa** - 15-60 tuổi, Chỉ nữ
6. **Nam khoa** - 18-150 tuổi, Chỉ nam
7. **Da liễu** - 0-150 tuổi, Cả hai
8. **Tai mũi họng** - 0-150 tuổi, Cả hai
9. **Mắt** - 0-150 tuổi, Cả hai
10. **Răng hàm mặt** - 0-150 tuổi, Cả hai

## 👨‍⚕️ BÁC SĨ CÓ SẴN

7 bác sĩ với đầy đủ chuyên khoa:

1. BS. Nguyễn Văn A - Tim mạch
2. BS. Trần Thị B - Nội khoa
3. BS. Lê Văn C - Nhi khoa
4. BS. Phạm Thị D - Mắt
5. BS. Hoàng Văn E - Tai mũi họng
6. BS. Vũ Thị F - Da liễu
7. BS. Đặng Văn G - Răng hàm mặt

## 🎨 GIAO DIỆN

- **Tailwind CSS** - Framework CSS hiện đại
- **Font Awesome** - Icons đẹp
- **Gradient Purple** - Màu chủ đạo
- **Responsive** - Tương thích mobile
- **Sidebar** - Menu dọc với phân quyền rõ ràng

## 🔐 BẢO MẬT

- Mật khẩu được mã hóa bằng **bcrypt**
- Session-based authentication
- Phân quyền chặt chẽ (Admin/Doctor/Patient)
- Kiểm tra quyền truy cập mọi trang
- XSS protection với `htmlspecialchars()`
- SQL Injection protection với PDO Prepared Statements

## 📝 LƯU Ý

1. **Phải cập nhật ngày sinh và giới tính** trước khi đặt lịch khám
2. **Admin có thể thêm bác sĩ và chuyên khoa** trực tiếp trên web
3. **Chuyên khoa tự động lọc** theo độ tuổi và giới tính
4. **Không thể đặt lịch** nếu không đủ điều kiện chuyên khoa
5. **Import lại database** nếu muốn reset dữ liệu

## 🆘 TROUBLESHOOTING

**Lỗi: Trang trắng**
- Kiểm tra Apache đã bật
- Kiểm tra URL đúng: `/public` ở cuối
- Xem error log: `xampp/apache/logs/error.log`

**Lỗi: Không kết nối database**
- Kiểm tra MySQL đã chạy
- Kiểm tra `config/database.php`
- Import lại `sql/schema.sql`

**Lỗi: Không thấy bác sĩ khi chọn chuyên khoa**
- Import lại database để có đầy đủ 7 bác sĩ
- Kiểm tra bác sĩ có chuyên khoa tương ứng

## 📞 HỖ TRỢ

Nếu gặp vấn đề, kiểm tra:
1. Apache và MySQL đã chạy
2. Database đã import đầy đủ
3. URL truy cập đúng
4. Đã đăng nhập đúng tài khoản

---

**Chúc bạn sử dụng hệ thống thành công! 🎉**
