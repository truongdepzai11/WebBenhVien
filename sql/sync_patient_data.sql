-- Script: Đồng bộ dữ liệu date_of_birth, gender, address từ users sang patients
-- Ngày: 2025-10-04

USE hospital_management;

-- Đồng bộ từ users sang patients (users là nguồn chính)
UPDATE patients p
INNER JOIN users u ON p.user_id = u.id
SET p.date_of_birth = u.date_of_birth,
    p.gender = u.gender,
    p.address = u.address
WHERE u.date_of_birth IS NOT NULL OR u.gender IS NOT NULL OR u.address IS NOT NULL;

-- Kiểm tra kết quả
SELECT 'Đã đồng bộ dữ liệu từ users sang patients' as message,
       COUNT(*) as total_synced
FROM patients p
INNER JOIN users u ON p.user_id = u.id
WHERE p.date_of_birth = u.date_of_birth;
