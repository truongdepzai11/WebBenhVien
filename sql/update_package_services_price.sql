-- Thêm cột giá cho từng dịch vụ trong gói (nếu chưa có)
ALTER TABLE package_services 
ADD COLUMN IF NOT EXISTS service_price DECIMAL(10,2) DEFAULT 0 AFTER service_name;

-- XÓA HẾT GIÁ CŨ VÀ CẬP NHẬT LẠI
UPDATE package_services SET service_price = 0;

-- Cập nhật giá THỰC TẾ cho từng dịch vụ
-- Dùng CASE để cập nhật chính xác
UPDATE package_services SET service_price = CASE 
    -- Khám tổng quát (200k)
    WHEN service_name = 'Khám tổng quát' THEN 200000
    WHEN service_name = 'Khám nội khoa' THEN 150000
    WHEN service_name = 'Khám tai mũi họng' THEN 150000
    
    -- Xét nghiệm máu (60k-150k)
    WHEN service_name LIKE '%Công thức máu%' OR service_name LIKE '%CBC%' THEN 80000
    WHEN service_name LIKE '%Glucose%' OR service_name LIKE '%Đường huyết%' THEN 60000
    WHEN service_name LIKE '%Lipid%' OR service_name LIKE '%Mỡ máu%' THEN 120000
    WHEN service_name LIKE '%Chức năng gan%' OR service_name LIKE '%SGOT%' OR service_name LIKE '%SGPT%' THEN 100000
    WHEN service_name LIKE '%Chức năng thận%' OR service_name LIKE '%Creatinine%' OR service_name LIKE '%Urea%' THEN 100000
    WHEN service_name LIKE '%HbA1c%' THEN 150000
    WHEN service_name LIKE '%Acid uric%' THEN 90000
    
    -- Xét nghiệm nước tiểu (50k)
    WHEN service_name LIKE '%nước tiểu%' OR service_name LIKE '%Tổng phân tích%' THEN 50000
    
    -- Chẩn đoán hình ảnh (300k-500k)
    WHEN service_name LIKE '%Siêu âm%' THEN 400000
    WHEN service_name LIKE '%X-quang%' OR service_name LIKE '%Chụp X%' THEN 500000
    WHEN service_name LIKE '%Điện tim%' OR service_name LIKE '%ECG%' THEN 400000
    
    -- Khám chuyên khoa (200k-250k)
    WHEN service_name LIKE '%phụ khoa%' THEN 250000
    WHEN service_name LIKE '%tiết niệu%' THEN 250000
    WHEN service_name LIKE '%Khám mắt%' THEN 200000
    
    ELSE service_price
END;

-- Tính lại giá gói dựa trên tổng dịch vụ
-- Gói Nam: 200k + 150k + 80k + 60k + 120k + 100k + 100k + 50k + 400k + 350k + 300k + 250k = 2,160,000
-- Gói Nữ: thêm Khám phụ khoa 250k = 2,410,000
-- Giá gói = tổng dịch vụ + 10% (phí quản lý)

UPDATE health_packages SET 
    price_male = 2400000,
    price_female = 2650000
WHERE name LIKE '%Gói khám sức khỏe tổng quát%' AND gender_requirement = 'both';

UPDATE health_packages SET 
    price_male = 2400000
WHERE name LIKE '%Gói khám sức khỏe tổng quát%' AND gender_requirement = 'male';

UPDATE health_packages SET 
    price_female = 2650000
WHERE name LIKE '%Gói khám sức khỏe tổng quát%' AND gender_requirement = 'female';

-- Tạo bảng lưu dịch vụ được chọn cho mỗi appointment
CREATE TABLE IF NOT EXISTS appointment_package_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    service_id INT NOT NULL,
    service_price DECIMAL(10,2) NOT NULL,
    doctor_id INT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    result_value TEXT NULL,
    result_status ENUM('normal', 'abnormal', 'pending') DEFAULT 'pending',
    notes TEXT NULL,
    tested_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES package_services(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE SET NULL,
    INDEX idx_appointment (appointment_id),
    INDEX idx_service (service_id),
    INDEX idx_doctor (doctor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm cột total_price vào appointments để lưu tổng giá thực tế
ALTER TABLE appointments 
ADD COLUMN total_price DECIMAL(10,2) DEFAULT 0 AFTER package_id;

-- Thêm cột coordinator_doctor_id (bác sĩ phụ trách chính)
ALTER TABLE appointments 
ADD COLUMN coordinator_doctor_id INT NULL AFTER doctor_id,
ADD FOREIGN KEY (coordinator_doctor_id) REFERENCES doctors(id) ON DELETE SET NULL;

COMMIT;
