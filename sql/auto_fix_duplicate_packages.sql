-- TỰ ĐỘNG XÓA GÓI TRÙNG (AN TOÀN)

-- BƯỚC 1: Tạo bảng tạm để lưu gói cần xóa
CREATE TEMPORARY TABLE IF NOT EXISTS packages_to_delete AS
SELECT 
    hp2.id,
    hp2.package_code,
    hp2.name,
    (SELECT COUNT(*) FROM appointments WHERE package_id = hp2.id) as appointment_count
FROM health_packages hp1
INNER JOIN health_packages hp2 
    ON hp1.name = hp2.name 
    AND hp1.gender_requirement = hp2.gender_requirement
    AND hp1.id < hp2.id  -- Giữ gói có ID nhỏ hơn
WHERE hp1.is_active = 1 AND hp2.is_active = 1;

-- BƯỚC 2: Xem danh sách gói sẽ bị xóa
SELECT 
    id,
    package_code,
    name,
    appointment_count,
    CASE 
        WHEN appointment_count = 0 THEN 'SẼ XÓA'
        ELSE 'CHỈ ẨN (có appointment)'
    END as action
FROM packages_to_delete;

-- BƯỚC 3: Xóa dịch vụ của gói trùng (không có appointment)
DELETE FROM package_services 
WHERE package_id IN (
    SELECT id FROM packages_to_delete WHERE appointment_count = 0
);

-- BƯỚC 4: Xóa gói trùng (không có appointment)
DELETE FROM health_packages 
WHERE id IN (
    SELECT id FROM packages_to_delete WHERE appointment_count = 0
);

-- BƯỚC 5: Ẩn gói trùng (có appointment)
UPDATE health_packages 
SET is_active = 0 
WHERE id IN (
    SELECT id FROM packages_to_delete WHERE appointment_count > 0
);

-- BƯỚC 6: Kiểm tra kết quả
SELECT 
    id,
    package_code,
    name,
    gender_requirement,
    is_active,
    (SELECT COUNT(*) FROM package_services WHERE package_id = health_packages.id) as services,
    (SELECT COUNT(*) FROM appointments WHERE package_id = health_packages.id) as appointments
FROM health_packages
ORDER BY name, gender_requirement, id;

-- BƯỚC 7: Dọn dẹp
DROP TEMPORARY TABLE IF EXISTS packages_to_delete;

COMMIT;
