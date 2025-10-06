-- Migration: Thêm tính năng Phí hủy lịch & Phí vắng mặt
-- Ngày: 2025-10-04

USE hospital_management;

-- 1. Thêm cột vào bảng appointments
ALTER TABLE appointments 
ADD COLUMN IF NOT EXISTS cancellation_reason TEXT AFTER notes,
ADD COLUMN IF NOT EXISTS cancellation_fee DECIMAL(10,2) DEFAULT 0 AFTER cancellation_reason,
ADD COLUMN IF NOT EXISTS cancelled_at TIMESTAMP NULL AFTER cancellation_fee;

-- 2. Cập nhật ENUM status (thêm trạng thái mới)
ALTER TABLE appointments 
MODIFY COLUMN status ENUM('pending', 'confirmed', 'completed', 'cancelled', 'late_cancelled', 'no_show') DEFAULT 'pending';

-- 3. Thêm index cho hiệu suất
ALTER TABLE appointments 
ADD INDEX idx_cancelled_at (cancelled_at);

-- Hoàn thành!
SELECT 'Migration completed successfully!' as message;
