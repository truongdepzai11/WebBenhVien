-- Hospital Management System Database Schema

CREATE DATABASE IF NOT EXISTS hospital_management;
USE hospital_management;

-- Bảng người dùng (Users) - Cải tiến với giới tính, ngày sinh
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other') DEFAULT 'other',
    address TEXT,
    avatar VARCHAR(255),
    role ENUM('admin', 'doctor', 'patient', 'staff') DEFAULT 'patient',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_email (email)
);

-- Bảng chuyên khoa (Specializations) - Đặt trước để doctors tham chiếu
CREATE TABLE specializations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    min_age INT DEFAULT 0,
    max_age INT DEFAULT 150,
    gender_requirement ENUM('male', 'female', 'both') DEFAULT 'both',
    icon VARCHAR(50) DEFAULT 'fa-stethoscope',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
);

-- Bảng bệnh nhân (Patients) - Giảm trùng lặp với users
CREATE TABLE patients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    patient_code VARCHAR(20) UNIQUE NOT NULL,
    blood_type VARCHAR(5),
    allergies TEXT,
    medical_history TEXT,
    emergency_contact VARCHAR(100),
    emergency_phone VARCHAR(20),
    insurance_number VARCHAR(50),
    insurance_provider VARCHAR(100),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_patient_code (patient_code)
);

-- Bảng bác sĩ (Doctors) - Với Foreign Key đến specializations
CREATE TABLE doctors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    doctor_code VARCHAR(20) UNIQUE NOT NULL,
    specialization_id INT NOT NULL,
    license_number VARCHAR(50) UNIQUE NOT NULL,
    qualification TEXT,
    experience_years INT DEFAULT 0,
    consultation_fee DECIMAL(10,2) DEFAULT 0,
    available_days VARCHAR(100),
    available_hours VARCHAR(50),
    bio TEXT,
    education TEXT,
    certifications TEXT,
    languages VARCHAR(255),
    rating DECIMAL(3,2) DEFAULT 0,
    total_patients INT DEFAULT 0,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (specialization_id) REFERENCES specializations(id) ON DELETE RESTRICT,
    INDEX idx_doctor_code (doctor_code),
    INDEX idx_specialization (specialization_id)
);

-- Bảng lịch hẹn (Appointments) - Cải tiến
CREATE TABLE appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_code VARCHAR(20) UNIQUE NOT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    reason TEXT,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled', 'no_show') DEFAULT 'pending',
    notes TEXT,
    confirmed_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    cancellation_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    INDEX idx_appointment_date (appointment_date),
    INDEX idx_status (status),
    INDEX idx_patient (patient_id),
    INDEX idx_doctor (doctor_id)
);

-- Bảng hồ sơ bệnh án (Medical Records) - Cải tiến
CREATE TABLE medical_records (
    id INT PRIMARY KEY AUTO_INCREMENT,
    record_code VARCHAR(20) UNIQUE NOT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_id INT,
    visit_date DATE NOT NULL,
    chief_complaint TEXT,
    symptoms TEXT,
    diagnosis TEXT NOT NULL,
    treatment_plan TEXT,
    notes TEXT,
    follow_up_date DATE,
    vital_signs JSON,
    attachments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
    INDEX idx_record_code (record_code),
    INDEX idx_patient (patient_id),
    INDEX idx_doctor (doctor_id),
    INDEX idx_visit_date (visit_date)
);

-- Bảng thuốc (Medicines) - Cải tiến
CREATE TABLE medicines (
    id INT PRIMARY KEY AUTO_INCREMENT,
    medicine_code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    generic_name VARCHAR(100),
    description TEXT,
    category VARCHAR(50),
    unit VARCHAR(20),
    dosage_form VARCHAR(50),
    strength VARCHAR(50),
    manufacturer VARCHAR(100),
    price DECIMAL(10,2) DEFAULT 0,
    stock_quantity INT DEFAULT 0,
    min_stock_level INT DEFAULT 10,
    expiry_alert_days INT DEFAULT 30,
    requires_prescription BOOLEAN DEFAULT TRUE,
    side_effects TEXT,
    contraindications TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_medicine_code (medicine_code),
    INDEX idx_name (name),
    INDEX idx_category (category)
);

-- Bảng đơn thuốc (Prescriptions) - Cải tiến
CREATE TABLE prescriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    prescription_code VARCHAR(20) UNIQUE NOT NULL,
    medical_record_id INT NOT NULL,
    medicine_id INT NOT NULL,
    quantity INT NOT NULL,
    dosage VARCHAR(100) NOT NULL,
    frequency VARCHAR(100) NOT NULL,
    duration VARCHAR(50) NOT NULL,
    instructions TEXT,
    route VARCHAR(50),
    start_date DATE,
    end_date DATE,
    refills_allowed INT DEFAULT 0,
    refills_remaining INT DEFAULT 0,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (medical_record_id) REFERENCES medical_records(id) ON DELETE CASCADE,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE RESTRICT,
    INDEX idx_medical_record (medical_record_id),
    INDEX idx_medicine (medicine_id),
    INDEX idx_status (status)
);

-- Bảng hóa đơn (Invoices)
CREATE TABLE invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_code VARCHAR(20) UNIQUE NOT NULL,
    appointment_id INT,
    patient_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    final_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'paid', 'cancelled', 'refunded') DEFAULT 'pending',
    payment_method ENUM('cash', 'momo', 'vnpay', 'bank_transfer') DEFAULT 'cash',
    payment_status ENUM('unpaid', 'paid', 'partial', 'refunded') DEFAULT 'unpaid',
    notes TEXT,
    issued_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    paid_date TIMESTAMP NULL,
    due_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    INDEX idx_patient (patient_id),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status)
);

-- Bảng chi tiết hóa đơn (Invoice Items)
CREATE TABLE invoice_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    item_type ENUM('consultation', 'medicine', 'test', 'procedure', 'other') NOT NULL,
    item_id INT,
    description VARCHAR(255) NOT NULL,
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    INDEX idx_invoice (invoice_id)
);

-- Bảng thanh toán (Payments)
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    payment_code VARCHAR(20) UNIQUE NOT NULL,
    invoice_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'momo', 'vnpay', 'bank_transfer') NOT NULL,
    payment_status ENUM('pending', 'success', 'failed', 'refunded') DEFAULT 'pending',
    transaction_id VARCHAR(100),
    gateway_response TEXT,
    payment_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    INDEX idx_invoice (invoice_id),
    INDEX idx_status (payment_status),
    INDEX idx_transaction (transaction_id)
);

-- ==================== DỮ LIỆU MẪU ====================

-- 1. Chuyên khoa (phải insert trước vì doctors tham chiếu)
INSERT INTO specializations (name, description, min_age, max_age, gender_requirement, icon) VALUES
('Tim mạch', 'Chuyên khoa tim mạch, điều trị các bệnh về tim và mạch máu', 0, 150, 'both', 'fa-heartbeat'),
('Nội khoa', 'Chuyên khoa nội tổng quát', 0, 150, 'both', 'fa-stethoscope'),
('Nhi khoa', 'Chuyên khoa nhi, điều trị cho trẻ em', 0, 15, 'both', 'fa-baby'),
('Lão khoa', 'Chuyên khoa chăm sóc người cao tuổi', 60, 150, 'both', 'fa-user-clock'),
('Sản phụ khoa', 'Chuyên khoa phụ nữ và thai sản', 15, 60, 'female', 'fa-female'),
('Nam khoa', 'Chuyên khoa nam giới', 18, 150, 'male', 'fa-male'),
('Da liễu', 'Chuyên khoa da liễu', 0, 150, 'both', 'fa-hand-sparkles'),
('Tai mũi họng', 'Chuyên khoa tai mũi họng', 0, 150, 'both', 'fa-head-side-cough'),
('Mắt', 'Chuyên khoa mắt', 0, 150, 'both', 'fa-eye'),
('Răng hàm mặt', 'Chuyên khoa răng hàm mặt', 0, 150, 'both', 'fa-tooth');

-- 2. Users (với giới tính, ngày sinh)
-- Admin
INSERT INTO users (username, email, password, full_name, phone, date_of_birth, gender, address, role) VALUES
('admin', 'admin@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '0123456789', '1980-01-01', 'male', '123 Admin St, TP.HCM', 'admin');

-- Doctors
INSERT INTO users (username, email, password, full_name, phone, date_of_birth, gender, address, role) VALUES
('dr.nguyen', 'nguyen@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'BS. Nguyễn Văn A', '0987654321', '1975-05-15', 'male', '456 Doctor St, TP.HCM', 'doctor'),
('dr.tran', 'tran@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'BS. Trần Thị B', '0987654322', '1980-08-20', 'female', '789 Doctor St, TP.HCM', 'doctor'),
('dr.le', 'le@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'BS. Lê Văn C', '0987654323', '1978-03-10', 'male', '321 Doctor St, TP.HCM', 'doctor'),
('dr.pham', 'pham@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'BS. Phạm Thị D', '0987654324', '1985-11-25', 'female', '654 Doctor St, TP.HCM', 'doctor'),
('dr.hoang', 'hoang@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'BS. Hoàng Văn E', '0987654325', '1982-07-30', 'male', '987 Doctor St, TP.HCM', 'doctor'),
('dr.vu', 'vu@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'BS. Vũ Thị F', '0987654326', '1988-02-14', 'female', '147 Doctor St, TP.HCM', 'doctor'),
('dr.dang', 'dang@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'BS. Đặng Văn G', '0987654327', '1979-12-05', 'male', '258 Doctor St, TP.HCM', 'doctor');

-- Patients
INSERT INTO users (username, email, password, full_name, phone, date_of_birth, gender, address, role) VALUES
('patient1', 'patient1@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Văn D', '0912345678', '1990-05-15', 'male', '123 Đường ABC, Quận 1, TP.HCM', 'patient'),
('patient2', 'patient2@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hoàng Thị E', '0912345679', '1985-08-20', 'female', '456 Đường XYZ, Quận 2, TP.HCM', 'patient');

-- 3. Doctors (với specialization_id)
INSERT INTO doctors (user_id, doctor_code, specialization_id, license_number, qualification, experience_years, consultation_fee, available_days, available_hours, bio) VALUES
(2, 'DOC001', 1, 'LIC001', 'Bác sĩ chuyên khoa II', 15, 200000, 'Thứ 2,Thứ 4,Thứ 6', '08:00-17:00', 'Chuyên gia tim mạch với 15 năm kinh nghiệm'),
(3, 'DOC002', 2, 'LIC002', 'Bác sĩ chuyên khoa I', 10, 150000, 'Thứ 3,Thứ 5,Thứ 7', '08:00-17:00', 'Bác sĩ nội khoa giàu kinh nghiệm'),
(4, 'DOC003', 3, 'LIC003', 'Bác sĩ chuyên khoa II', 12, 180000, 'Thứ 2,Thứ 3,Thứ 4,Thứ 5,Thứ 6', '08:00-16:00', 'Chuyên gia nhi khoa'),
(5, 'DOC004', 9, 'LIC004', 'Bác sĩ chuyên khoa I', 8, 170000, 'Thứ 2,Thứ 3,Thứ 5', '08:00-17:00', 'Bác sĩ chuyên khoa mắt'),
(6, 'DOC005', 8, 'LIC005', 'Bác sĩ chuyên khoa II', 14, 160000, 'Thứ 2,Thứ 4,Thứ 6', '08:00-16:00', 'Chuyên gia tai mũi họng'),
(7, 'DOC006', 7, 'LIC006', 'Bác sĩ chuyên khoa I', 9, 140000, 'Thứ 3,Thứ 5,Thứ 7', '08:00-17:00', 'Bác sĩ da liễu'),
(8, 'DOC007', 10, 'LIC007', 'Bác sĩ chuyên khoa II', 11, 190000, 'Thứ 2,Thứ 3,Thứ 4,Thứ 5,Thứ 6', '08:00-18:00', 'Chuyên gia răng hàm mặt');

-- 4. Patients
INSERT INTO patients (user_id, patient_code, blood_type, emergency_contact, emergency_phone) VALUES
(9, 'PAT001', 'O+', 'Phạm Thị F', '0923456789'),
(10, 'PAT002', 'A+', 'Hoàng Văn G', '0923456780');

-- 5. Medicines (Dữ liệu mẫu thuốc)
INSERT INTO medicines (medicine_code, name, generic_name, category, unit, dosage_form, strength, manufacturer, price, stock_quantity) VALUES
('MED001', 'Paracetamol 500mg', 'Paracetamol', 'Giảm đau, hạ sốt', 'Viên', 'Viên nén', '500mg', 'Công ty Dược A', 2000, 1000),
('MED002', 'Amoxicillin 500mg', 'Amoxicillin', 'Kháng sinh', 'Viên', 'Viên nang', '500mg', 'Công ty Dược B', 5000, 500),
('MED003', 'Vitamin C 1000mg', 'Ascorbic Acid', 'Vitamin', 'Viên', 'Viên sủi', '1000mg', 'Công ty Dược C', 3000, 800),
('MED004', 'Omeprazole 20mg', 'Omeprazole', 'Tiêu hóa', 'Viên', 'Viên nang', '20mg', 'Công ty Dược D', 4000, 600),
('MED005', 'Cetirizine 10mg', 'Cetirizine', 'Chống dị ứng', 'Viên', 'Viên nén', '10mg', 'Công ty Dược E', 1500, 700);
