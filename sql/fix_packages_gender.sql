-- FIX GÓI KHÁM THEO GIỚI TÍNH

-- BƯỚC 1: Xem tất cả gói hiện tại
SELECT 
    id,
    name,
    package_code,
    gender_requirement,
    min_age,
    max_age,
    is_active,
    (SELECT COUNT(*) FROM package_services WHERE package_id = health_packages.id) as service_count,
    (SELECT COUNT(*) FROM appointments WHERE package_id = health_packages.id) as appointment_count
FROM health_packages
ORDER BY name, gender_requirement;

-- BƯỚC 2: Tìm gói trùng tên
SELECT 
    name,
    COUNT(*) as duplicate_count,
    GROUP_CONCAT(id ORDER BY id) as package_ids,
    GROUP_CONCAT(gender_requirement ORDER BY id) as genders
FROM health_packages
GROUP BY name
HAVING COUNT(*) > 1;

-- BƯỚC 3: Xóa gói trùng (CẢNH BÁO: Chỉ xóa nếu chắc chắn!)
-- Ví dụ: Nếu có 2 gói "Gói khám tổng quát - Nam" với ID 1 và 2
-- Giữ lại ID nhỏ hơn, xóa ID lớn hơn

-- Kiểm tra gói có appointment không
-- SELECT id, name, 
--        (SELECT COUNT(*) FROM appointments WHERE package_id = health_packages.id) as appt_count
-- FROM health_packages 
-- WHERE id IN (2, 4, 6); -- Thay bằng ID gói muốn xóa

-- Nếu appointment_count = 0 → An toàn để xóa
-- DELETE FROM package_services WHERE package_id IN (2);
-- DELETE FROM health_packages WHERE id IN (2);

-- Nếu appointment_count > 0 → Chỉ ẩn đi
-- UPDATE health_packages SET is_active = 0 WHERE id IN (2);

-- BƯỚC 4: Đảm bảo tên gói rõ ràng
-- Gói Nam
UPDATE health_packages 
SET name = CASE 
    WHEN gender_requirement = 'male' AND name NOT LIKE '% - Nam' 
    THEN CONCAT(name, ' - Nam')
    ELSE name
END
WHERE gender_requirement = 'male';

-- Gói Nữ
UPDATE health_packages 
SET name = CASE 
    WHEN gender_requirement = 'female' AND name NOT LIKE '% - Nữ' 
    THEN CONCAT(name, ' - Nữ')
    ELSE name
END
WHERE gender_requirement = 'female';

-- BƯỚC 5: Kiểm tra kết quả
SELECT 
    id,
    name,
    gender_requirement,
    is_active
FROM health_packages
ORDER BY name, gender_requirement;

COMMIT;
