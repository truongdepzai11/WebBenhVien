-- =====================================================
-- THÊM TÍNH NĂNG GÓI KHÁM SỨC KHỎE
-- =====================================================

-- 1. Bảng Gói khám (Health Packages)
CREATE TABLE IF NOT EXISTS health_packages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    package_code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price_male DECIMAL(10,2),
    price_female DECIMAL(10,2),
    gender_requirement ENUM('both', 'male', 'female') DEFAULT 'both',
    min_age INT DEFAULT 0,
    max_age INT DEFAULT 150,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active (is_active),
    INDEX idx_gender (gender_requirement)
);

-- 2. Bảng Danh mục xét nghiệm/dịch vụ trong gói
CREATE TABLE IF NOT EXISTS package_services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    package_id INT NOT NULL,
    service_name VARCHAR(255) NOT NULL,
    service_category ENUM('general', 'blood_test', 'urine_test', 'imaging', 'specialist', 'other') DEFAULT 'general',
    is_required BOOLEAN DEFAULT TRUE,
    gender_specific ENUM('both', 'male', 'female') DEFAULT 'both',
    notes TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (package_id) REFERENCES health_packages(id) ON DELETE CASCADE,
    INDEX idx_package (package_id),
    INDEX idx_category (service_category)
);

-- 3. Cập nhật bảng Appointments - thêm hỗ trợ gói khám
ALTER TABLE appointments 
ADD COLUMN IF NOT EXISTS package_id INT NULL AFTER doctor_id,
ADD COLUMN IF NOT EXISTS appointment_type ENUM('regular', 'package') DEFAULT 'regular' AFTER status;

ALTER TABLE appointments 
ADD FOREIGN KEY IF NOT EXISTS (package_id) REFERENCES health_packages(id) ON DELETE SET NULL;

ALTER TABLE appointments
ADD INDEX IF NOT EXISTS idx_package (package_id),
ADD INDEX IF NOT EXISTS idx_type (appointment_type);

-- 4. Bảng kết quả xét nghiệm theo gói
CREATE TABLE IF NOT EXISTS package_test_results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT NOT NULL,
    service_id INT NOT NULL,
    result_value VARCHAR(255),
    result_status ENUM('normal', 'abnormal', 'pending') DEFAULT 'pending',
    reference_range VARCHAR(100),
    notes TEXT,
    tested_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES package_services(id),
    INDEX idx_appointment (appointment_id),
    INDEX idx_status (result_status)
);

-- =====================================================
-- DỮ LIỆU MẪU: GÓI KHÁM SỨC KHỎE TỔNG QUÁT
-- =====================================================

-- Gói khám tổng quát Nam
INSERT INTO health_packages (package_code, name, description, price_male, price_female, gender_requirement, min_age, max_age) 
VALUES 
('PKG0001', 'Gói khám sức khỏe tổng quát - Nam', 'Gói khám toàn diện dành cho nam giới, bao gồm các xét nghiệm cơ bản và chuyên sâu', 3580000, NULL, 'male', 18, 150);

-- Gói khám tổng quát Nữ
INSERT INTO health_packages (package_code, name, description, price_male, price_female, gender_requirement, min_age, max_age) 
VALUES 
('PKG0002', 'Gói khám sức khỏe tổng quát - Nữ', 'Gói khám toàn diện dành cho nữ giới, bao gồm các xét nghiệm cơ bản và chuyên sâu', NULL, 4370000, 'female', 18, 150);

-- Dịch vụ cho gói Nam (PKG0001)
INSERT INTO package_services (package_id, service_name, service_category, gender_specific, display_order) VALUES
-- Chẩn đoán hình ảnh
(1, 'Chụp X quang ngực thẳng số hóa (1 phim)', 'imaging', 'both', 1),
(1, 'Siêu âm ổ bụng (gan mật, tụy, lách, thận, bàng quang)', 'imaging', 'both', 2),
(1, 'Siêu âm vú', 'imaging', 'male', 3),
(1, 'Siêu âm tuyến giáp', 'imaging', 'both', 4),
(1, 'Điện tim ECG', 'general', 'both', 5),

-- Khám phụ khoa
(1, 'Vị khuẩn nhuộm soi (huyết trắng)', 'specialist', 'both', 6),

-- Khám tổng quát
(1, 'Sinh hiệu (Mạch, huyết áp, chiều cao, cân nặng, chỉ số BMI)', 'general', 'both', 10),
(1, 'Khám Nội tổng quát (BS nội)', 'general', 'both', 11),
(1, 'Khám Phụ khoa (BS phụ khoa)', 'specialist', 'male', 12),

-- Xét nghiệm thường quy
(1, 'Tổng phân tích tế bào máu ngoại vi (máy laser)', 'blood_test', 'both', 20),

-- Tổng phân tích nước tiểu
(1, 'Tổng phân tích nước tiểu (máy tự động)', 'urine_test', 'both', 30),

-- Kiểm tra bộ mỡ
(1, 'Định lượng cholesterol toàn phần', 'blood_test', 'both', 40),
(1, 'Định lượng HDL-C', 'blood_test', 'both', 41),
(1, 'Định lượng LDL-C', 'blood_test', 'both', 42),
(1, 'Định lượng Triglycerid', 'blood_test', 'both', 43),

-- Kiểm tra men gan
(1, 'Đo hoạt độ ALT (GPT)', 'blood_test', 'both', 50),
(1, 'Đo hoạt độ AST (GOT)', 'blood_test', 'both', 51),
(1, 'Đo hoạt độ GGT', 'blood_test', 'both', 52),

-- Kiểm tra chức năng thận
(1, 'Định lượng Creatinin máu', 'blood_test', 'both', 60),
(1, 'Định lượng Ure máu', 'blood_test', 'both', 61),

-- Kiểm tra chức năng tuyến giáp
(1, 'Định lượng TSH máu', 'blood_test', 'both', 70),
(1, 'Định lượng FT4 máu', 'blood_test', 'both', 71),

-- Tầm soát bệnh Gout
(1, 'Định lượng Acid Uric máu', 'blood_test', 'both', 80),

-- Tầm soát tiểu đường
(1, 'Định lượng Glucose', 'blood_test', 'both', 90),
(1, 'Định lượng HbA1c', 'blood_test', 'both', 91),

-- Tầm soát viêm gan siêu vi B&C
(1, 'HbsAg miễn dịch tự động', 'blood_test', 'both', 100),
(1, 'HbsAb miễn dịch tự động', 'blood_test', 'both', 101),
(1, 'HCV Ab miễn dịch tự động', 'blood_test', 'both', 102);

-- Dịch vụ cho gói Nữ (PKG0002) - Tương tự nhưng có thêm khám phụ khoa
INSERT INTO package_services (package_id, service_name, service_category, gender_specific, display_order) VALUES
-- Chẩn đoán hình ảnh
(2, 'Chụp X quang ngực thẳng số hóa (1 phim)', 'imaging', 'both', 1),
(2, 'Siêu âm ổ bụng (gan mật, tụy, lách, thận, bàng quang)', 'imaging', 'both', 2),
(2, 'Siêu âm vú', 'imaging', 'female', 3),
(2, 'Siêu âm tuyến giáp', 'imaging', 'both', 4),
(2, 'Điện tim ECG', 'general', 'both', 5),
(2, 'Vị khuẩn nhuộm soi (huyết trắng)', 'specialist', 'female', 6),

-- Khám tổng quát (giống Nam)
(2, 'Sinh hiệu (Mạch, huyết áp, chiều cao, cân nặng, chỉ số BMI)', 'general', 'both', 10),
(2, 'Khám Nội tổng quát (BS nội)', 'general', 'both', 11),
(2, 'Khám Phụ khoa (BS phụ khoa)', 'specialist', 'female', 12),

-- Các xét nghiệm khác giống Nam
(2, 'Tổng phân tích tế bào máu ngoại vi (máy laser)', 'blood_test', 'both', 20),
(2, 'Tổng phân tích nước tiểu (máy tự động)', 'urine_test', 'both', 30),
(2, 'Định lượng cholesterol toàn phần', 'blood_test', 'both', 40),
(2, 'Định lượng HDL-C', 'blood_test', 'both', 41),
(2, 'Định lượng LDL-C', 'blood_test', 'both', 42),
(2, 'Định lượng Triglycerid', 'blood_test', 'both', 43),
(2, 'Đo hoạt độ ALT (GPT)', 'blood_test', 'both', 50),
(2, 'Đo hoạt độ AST (GOT)', 'blood_test', 'both', 51),
(2, 'Đo hoạt độ GGT', 'blood_test', 'both', 52),
(2, 'Định lượng Creatinin máu', 'blood_test', 'both', 60),
(2, 'Định lượng Ure máu', 'blood_test', 'both', 61),
(2, 'Định lượng TSH máu', 'blood_test', 'both', 70),
(2, 'Định lượng FT4 máu', 'blood_test', 'both', 71),
(2, 'Định lượng Acid Uric máu', 'blood_test', 'both', 80),
(2, 'Định lượng Glucose', 'blood_test', 'both', 90),
(2, 'Định lượng HbA1c', 'blood_test', 'both', 91),
(2, 'HbsAg miễn dịch tự động', 'blood_test', 'both', 100),
(2, 'HbsAb miễn dịch tự động', 'blood_test', 'both', 101),
(2, 'HCV Ab miễn dịch tự động', 'blood_test', 'both', 102);
