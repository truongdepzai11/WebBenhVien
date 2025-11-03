-- =====================================================
-- THÊM CỘT total_price VÀO BẢNG package_appointments
-- =====================================================

USE hospital_management;

-- Thêm cột total_price
ALTER TABLE package_appointments 
ADD COLUMN total_price DECIMAL(10,2) DEFAULT 0.00 AFTER notes;

-- Kiểm tra kết quả
DESCRIBE package_appointments;

-- =====================================================
-- HOÀN THÀNH!
-- =====================================================
