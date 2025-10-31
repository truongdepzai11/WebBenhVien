-- KIỂM TRA GÓI TRÙNG LẶP

-- 1. Xem tất cả gói khám
SELECT 
    id,
    name,
    package_code,
    gender_requirement,
    min_age,
    max_age,
    status
FROM health_packages
ORDER BY name, gender_requirement;

-- 2. Tìm gói trùng tên
SELECT 
    name,
    COUNT(*) as count,
    GROUP_CONCAT(id) as package_ids,
    GROUP_CONCAT(gender_requirement) as genders
FROM health_packages
GROUP BY name
HAVING COUNT(*) > 1;

-- 3. Kiểm tra gói "Gói khám sức khỏe tổng quát - Nam"
SELECT * FROM health_packages 
WHERE name LIKE '%tổng quát%' 
AND (gender_requirement = 'male' OR gender_requirement = 'both');

-- 4. XÓA GÓI TRÙNG (nếu cần)
-- DELETE FROM health_packages WHERE id = [ID_GÓI_TRÙNG];
