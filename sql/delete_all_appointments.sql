-- =====================================================
-- XÓA TẤT CẢ APPOINTMENTS ĐỂ TEST LẠI TỪ ĐẦU
-- =====================================================

-- Tắt foreign key checks tạm thời
SET FOREIGN_KEY_CHECKS = 0;

-- Xóa tất cả appointments
DELETE FROM appointments;

-- Reset AUTO_INCREMENT về 1
ALTER TABLE appointments AUTO_INCREMENT = 1;

-- Bật lại foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Kiểm tra kết quả
SELECT COUNT(*) as total_appointments FROM appointments;

-- =====================================================
-- HOÀN THÀNH!
-- Tất cả appointments đã được xóa
-- =====================================================
