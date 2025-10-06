-- Migration: Thêm các cột còn thiếu vào bảng medical_records
-- Ngày: 2025-10-04

USE hospital_management;

-- Thêm cột treatment
ALTER TABLE medical_records 
ADD COLUMN IF NOT EXISTS treatment TEXT NULL AFTER symptoms;

-- Thêm cột prescription
ALTER TABLE medical_records 
ADD COLUMN IF NOT EXISTS prescription TEXT NULL AFTER treatment;

-- Thêm cột test_results
ALTER TABLE medical_records 
ADD COLUMN IF NOT EXISTS test_results TEXT NULL AFTER prescription;

-- Hoàn thành
SELECT 'Đã thêm các cột treatment, prescription, test_results vào bảng medical_records' as message;
