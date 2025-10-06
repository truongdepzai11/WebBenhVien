-- Migration: Thêm cột date_of_birth, gender, address vào bảng patients
-- Ngày: 2025-10-04

USE hospital_management;

-- Kiểm tra và thêm cột date_of_birth
ALTER TABLE patients 
ADD COLUMN IF NOT EXISTS date_of_birth DATE NULL AFTER patient_code;

-- Kiểm tra và thêm cột gender
ALTER TABLE patients 
ADD COLUMN IF NOT EXISTS gender ENUM('male', 'female', 'other') NULL AFTER date_of_birth;

-- Kiểm tra và thêm cột address
ALTER TABLE patients 
ADD COLUMN IF NOT EXISTS address TEXT NULL AFTER gender;

-- Hoàn thành!
SELECT 'Migration completed: Added date_of_birth, gender, address to patients table' as message;
