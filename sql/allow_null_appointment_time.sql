-- =====================================================
-- CHO PHÉP doctor_id VÀ appointment_time = NULL
-- (Cần thiết cho đặt lịch theo gói khám)
-- =====================================================

USE hospital_management;

-- Sửa cột doctor_id cho phép NULL
-- (Khi đặt gói khám, bác sĩ sẽ được phân công sau)
ALTER TABLE appointments 
MODIFY COLUMN doctor_id INT(11) NULL;

-- Sửa cột appointment_time cho phép NULL
-- (Khi đặt gói khám, giờ khám sẽ được phân công sau)
ALTER TABLE appointments 
MODIFY COLUMN appointment_time TIME NULL;

-- Kiểm tra kết quả
DESCRIBE appointments;

-- =====================================================
-- HOÀN THÀNH!
-- Bây giờ có thể đặt gói khám mà không cần chọn bác sĩ và giờ
-- =====================================================
