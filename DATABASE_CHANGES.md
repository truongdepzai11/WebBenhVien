# 🔄 THAY ĐỔI DATABASE - PHIÊN BẢN MỚI

## ⚠️ QUAN TRỌNG: PHẢI IMPORT LẠI DATABASE

Database đã được **thiết kế lại hoàn toàn** theo chuẩn bệnh viện thực tế với **Foreign Keys** và **ràng buộc đầy đủ**.

---

## 📊 CÁC THAY ĐỔI CHÍNH

### 1. **Bảng `users` - Cải tiến**

**TRƯỚC:**
```sql
CREATE TABLE users (
    id, username, email, password, full_name, phone, role
)
```

**SAU (MỚI):**
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE,              -- ✨ MỚI
    gender ENUM('male', 'female', 'other'),  -- ✨ MỚI
    address TEXT,                     -- ✨ MỚI
    avatar VARCHAR(255),              -- ✨ MỚI
    role ENUM('admin', 'doctor', 'patient', 'staff'),
    is_active BOOLEAN DEFAULT TRUE,   -- ✨ MỚI
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX idx_role (role),            -- ✨ MỚI
    INDEX idx_email (email)           -- ✨ MỚI
);
```

**Lý do:** 
- Giới tính, ngày sinh nên ở bảng `users` (không chỉ ở `patients`)
- Tất cả user đều có thông tin cơ bản này
- Thêm index để tăng tốc query

---

### 2. **Bảng `specializations` - Đặt trước `doctors`**

**TRƯỚC:** Bảng này ở cuối file

**SAU (MỚI):** Đặt **TRƯỚC** bảng `doctors` để tạo Foreign Key

```sql
CREATE TABLE specializations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    min_age INT DEFAULT 0,
    max_age INT DEFAULT 150,
    gender_requirement ENUM('male', 'female', 'both'),
    icon VARCHAR(50) DEFAULT 'fa-stethoscope',  -- ✨ MỚI
    is_active BOOLEAN DEFAULT TRUE,              -- ✨ MỚI
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX idx_name (name)                        -- ✨ MỚI
);
```

---

### 3. **Bảng `doctors` - Foreign Key đến `specializations`**

**TRƯỚC:**
```sql
CREATE TABLE doctors (
    ...
    specialization VARCHAR(100) NOT NULL,  -- ❌ Lưu tên (string)
    ...
)
```

**SAU (MỚI):**
```sql
CREATE TABLE doctors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    doctor_code VARCHAR(20) UNIQUE NOT NULL,
    specialization_id INT NOT NULL,        -- ✅ Foreign Key (ID)
    license_number VARCHAR(50) UNIQUE NOT NULL,
    qualification TEXT,
    experience_years INT DEFAULT 0,
    consultation_fee DECIMAL(10,2),
    available_days VARCHAR(100),
    available_hours VARCHAR(50),
    bio TEXT,                              -- ✨ MỚI
    education TEXT,                        -- ✨ MỚI
    certifications TEXT,                   -- ✨ MỚI
    languages VARCHAR(255),                -- ✨ MỚI
    rating DECIMAL(3,2) DEFAULT 0,         -- ✨ MỚI
    total_patients INT DEFAULT 0,          -- ✨ MỚI
    is_available BOOLEAN DEFAULT TRUE,     -- ✨ MỚI
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (specialization_id) REFERENCES specializations(id) ON DELETE RESTRICT,  -- ✅ MỚI
    INDEX idx_doctor_code (doctor_code),
    INDEX idx_specialization (specialization_id)
);
```

**Lý do:**
- **Foreign Key** đảm bảo tính toàn vẹn dữ liệu
- Không thể xóa chuyên khoa nếu còn bác sĩ (`ON DELETE RESTRICT`)
- Thêm thông tin chi tiết: bio, education, rating...

---

### 4. **Bảng `patients` - Giảm trùng lặp**

**TRƯỚC:**
```sql
CREATE TABLE patients (
    ...
    date_of_birth DATE,    -- ❌ Trùng với users
    gender ENUM(...),      -- ❌ Trùng với users
    address TEXT,          -- ❌ Trùng với users
    ...
)
```

**SAU (MỚI):**
```sql
CREATE TABLE patients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    patient_code VARCHAR(20) UNIQUE NOT NULL,
    blood_type VARCHAR(5),
    allergies TEXT,
    medical_history TEXT,              -- ✨ MỚI
    emergency_contact VARCHAR(100),
    emergency_phone VARCHAR(20),
    insurance_number VARCHAR(50),      -- ✨ MỚI
    insurance_provider VARCHAR(100),   -- ✨ MỚI
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_patient_code (patient_code)
);
```

**Lý do:**
- Loại bỏ trùng lặp: `date_of_birth`, `gender`, `address` đã có ở `users`
- Chỉ lưu thông tin **đặc thù** của bệnh nhân: nhóm máu, dị ứng, bảo hiểm

---

### 5. **Bảng `appointments` - Cải tiến**

**SAU (MỚI):**
```sql
CREATE TABLE appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_code VARCHAR(20) UNIQUE NOT NULL,  -- ✨ MỚI
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    reason TEXT,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled', 'no_show'),  -- ✨ Thêm 'no_show'
    notes TEXT,
    confirmed_at TIMESTAMP NULL,           -- ✨ MỚI
    completed_at TIMESTAMP NULL,           -- ✨ MỚI
    cancelled_at TIMESTAMP NULL,           -- ✨ MỚI
    cancellation_reason TEXT,              -- ✨ MỚI
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    INDEX idx_appointment_date (appointment_date),
    INDEX idx_status (status),
    INDEX idx_patient (patient_id),
    INDEX idx_doctor (doctor_id)
);
```

**Lý do:**
- Thêm `appointment_code` để dễ tra cứu
- Lưu timestamp khi xác nhận/hoàn thành/hủy
- Lưu lý do hủy

---

### 6. **Bảng `medical_records` - Cải tiến**

**SAU (MỚI):**
```sql
CREATE TABLE medical_records (
    id INT PRIMARY KEY AUTO_INCREMENT,
    record_code VARCHAR(20) UNIQUE NOT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_id INT,
    visit_date DATE NOT NULL,
    chief_complaint TEXT,              -- ✨ MỚI (Lý do khám)
    symptoms TEXT,
    diagnosis TEXT NOT NULL,
    treatment_plan TEXT,               -- ✨ MỚI
    notes TEXT,
    follow_up_date DATE,               -- ✨ MỚI
    vital_signs JSON,                  -- ✨ MỚI (Huyết áp, nhịp tim...)
    attachments TEXT,                  -- ✨ MỚI (File đính kèm)
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
    INDEX idx_record_code (record_code),
    INDEX idx_patient (patient_id),
    INDEX idx_doctor (doctor_id),
    INDEX idx_visit_date (visit_date)
);
```

**Lý do:**
- Thêm `chief_complaint` (lý do khám chính)
- `vital_signs` dạng JSON để lưu: huyết áp, nhịp tim, nhiệt độ...
- `follow_up_date` để hẹn tái khám

---

### 7. **Bảng `medicines` - Cải tiến**

**SAU (MỚI):**
```sql
CREATE TABLE medicines (
    id INT PRIMARY KEY AUTO_INCREMENT,
    medicine_code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    generic_name VARCHAR(100),         -- ✨ MỚI (Tên hoạt chất)
    description TEXT,
    category VARCHAR(50),              -- ✨ MỚI
    unit VARCHAR(20),
    dosage_form VARCHAR(50),           -- ✨ MỚI (Viên nén, viên nang...)
    strength VARCHAR(50),              -- ✨ MỚI (500mg, 1g...)
    manufacturer VARCHAR(100),         -- ✨ MỚI
    price DECIMAL(10,2),
    stock_quantity INT DEFAULT 0,
    min_stock_level INT DEFAULT 10,    -- ✨ MỚI
    expiry_alert_days INT DEFAULT 30,  -- ✨ MỚI
    requires_prescription BOOLEAN DEFAULT TRUE,  -- ✨ MỚI
    side_effects TEXT,                 -- ✨ MỚI
    contraindications TEXT,            -- ✨ MỚI (Chống chỉ định)
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX idx_medicine_code (medicine_code),
    INDEX idx_name (name),
    INDEX idx_category (category)
);
```

**Lý do:**
- Thông tin đầy đủ như nhà thuốc thực tế
- `generic_name`: Tên hoạt chất (VD: Paracetamol)
- `dosage_form`: Dạng bào chế (viên nén, viên nang, siro...)
- `side_effects`, `contraindications`: Tác dụng phụ, chống chỉ định

---

### 8. **Bảng `prescriptions` - Cải tiến**

**SAU (MỚI):**
```sql
CREATE TABLE prescriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    prescription_code VARCHAR(20) UNIQUE NOT NULL,  -- ✨ MỚI
    medical_record_id INT NOT NULL,
    medicine_id INT NOT NULL,
    quantity INT NOT NULL,             -- ✨ MỚI
    dosage VARCHAR(100) NOT NULL,
    frequency VARCHAR(100) NOT NULL,
    duration VARCHAR(50) NOT NULL,
    instructions TEXT,
    route VARCHAR(50),                 -- ✨ MỚI (Đường dùng: uống, tiêm...)
    start_date DATE,                   -- ✨ MỚI
    end_date DATE,                     -- ✨ MỚI
    refills_allowed INT DEFAULT 0,     -- ✨ MỚI (Số lần kê lại)
    refills_remaining INT DEFAULT 0,   -- ✨ MỚI
    status ENUM('active', 'completed', 'cancelled'),  -- ✨ MỚI
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (medical_record_id) REFERENCES medical_records(id) ON DELETE CASCADE,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE RESTRICT,  -- ✅ MỚI
    INDEX idx_medical_record (medical_record_id),
    INDEX idx_medicine (medicine_id),
    INDEX idx_status (status)
);
```

**Lý do:**
- `prescription_code` để tra cứu đơn thuốc
- `quantity`: Số lượng thuốc
- `route`: Đường dùng (uống, tiêm, bôi...)
- `refills`: Cho phép kê lại đơn thuốc

---

## 🔗 SƠ ĐỒ QUAN HỆ

```
users (1) ----< (N) patients
users (1) ----< (N) doctors
specializations (1) ----< (N) doctors  ✅ FOREIGN KEY MỚI

patients (1) ----< (N) appointments
doctors (1) ----< (N) appointments

patients (1) ----< (N) medical_records
doctors (1) ----< (N) medical_records
appointments (1) ----< (1) medical_records

medical_records (1) ----< (N) prescriptions
medicines (1) ----< (N) prescriptions  ✅ FOREIGN KEY MỚI
```

---

## 📝 DỮ LIỆU MẪU

### Users
- **Admin**: `admin` / `password`
- **Doctors**: 7 bác sĩ với đầy đủ giới tính, ngày sinh
- **Patients**: 2 bệnh nhân với thông tin đầy đủ

### Specializations
- 10 chuyên khoa với icon riêng
- Có điều kiện độ tuổi, giới tính

### Medicines
- 5 loại thuốc mẫu với thông tin đầy đủ
- Có giá, tồn kho, nhà sản xuất

---

## 🚀 HƯỚNG DẪN IMPORT

1. **Mở phpMyAdmin**: `http://localhost/phpmyadmin`

2. **Xóa database cũ**:
   ```sql
   DROP DATABASE IF EXISTS hospital_management;
   ```

3. **Import file mới**: `sql/schema.sql`

4. **Kiểm tra**:
   ```sql
   SHOW TABLES;
   SELECT * FROM specializations;
   SELECT * FROM doctors;
   ```

---

## ✅ LỢI ÍCH CỦA DATABASE MỚI

1. **Tính toàn vẹn dữ liệu**
   - Foreign Keys đảm bảo không có dữ liệu "rác"
   - Không thể xóa chuyên khoa nếu còn bác sĩ
   - Không thể xóa thuốc nếu còn đơn thuốc

2. **Hiệu suất cao**
   - Có INDEX cho các cột thường query
   - Giảm trùng lặp dữ liệu

3. **Giống thực tế**
   - Cấu trúc giống hệ thống bệnh viện thật
   - Đầy đủ thông tin: bio, education, rating, insurance...
   - Có vital_signs (JSON) để lưu chỉ số sức khỏe

4. **Dễ mở rộng**
   - Có `is_active` để soft delete
   - Có `updated_at` để track thay đổi
   - Có các timestamp: confirmed_at, completed_at...

---

## ⚠️ LƯU Ý QUAN TRỌNG

1. **Code cũ sẽ BỊ LỖI** vì:
   - `doctors.specialization` → `doctors.specialization_id`
   - `patients.date_of_birth` → `users.date_of_birth`
   - `patients.gender` → `users.gender`

2. **Cần cập nhật**:
   - Models
   - Controllers
   - Views

3. **Tôi sẽ cập nhật code ngay sau khi bạn import database!**

---

**Hãy import database trước, sau đó tôi sẽ cập nhật toàn bộ code!** 🚀
