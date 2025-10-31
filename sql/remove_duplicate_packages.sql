-- XÓA GÓI KHÁM TRÙNG LẶP

-- BƯỚC 1: XEM CÁC GÓI TRÙNG
SELECT 
    id,
    package_code,
    name,
    gender_requirement,
    min_age,
    max_age,
    is_active,
    created_at,
    (SELECT COUNT(*) FROM package_services WHERE package_id = health_packages.id) as service_count,
    (SELECT COUNT(*) FROM appointments WHERE package_id = health_packages.id) as appointment_count
FROM health_packages
WHERE name LIKE '%tổng quát%'
ORDER BY name, id;

-- BƯỚC 2: TÌM GÓI TRÙNG CHÍNH XÁC
-- Nếu thấy 2 gói có cùng tên "Gói khám sức khỏe tổng quát - Nam"
-- Ví dụ kết quả:
-- id=1, name="Gói khám sức khỏe tổng quát - Nam", package_code="PKG001"
-- id=2, name="Gói khám sức khỏe tổng quát - Nam", package_code="PKG002" ← TRÙNG!

-- BƯỚC 3: XÓA GÓI TRÙNG (Giữ lại gói có ID nhỏ hơn)

-- 3.1. Kiểm tra gói nào có appointment
SELECT 
    id,
    name,
    package_code,
    (SELECT COUNT(*) FROM appointments WHERE package_id = health_packages.id) as has_appointments
FROM health_packages
WHERE name = 'Gói khám sức khỏe tổng quát - Nam'
ORDER BY id;

-- 3.2. Xóa gói KHÔNG có appointment (giả sử là PKG002)
-- CẢNH BÁO: Thay PKG002 bằng mã gói thực tế của bạn!

-- Xóa dịch vụ của gói trùng
DELETE FROM package_services 
WHERE package_id = (
    SELECT id FROM health_packages WHERE package_code = 'PKG002'
);

-- Xóa gói trùng
DELETE FROM health_packages 
WHERE package_code = 'PKG002';

-- HOẶC nếu gói có appointment, chỉ ẨN đi:
-- UPDATE health_packages 
-- SET is_active = 0 
-- WHERE package_code = 'PKG002';

-- BƯỚC 4: KIỂM TRA KẾT QUẢ
SELECT 
    id,
    package_code,
    name,
    gender_requirement,
    is_active
FROM health_packages
WHERE name LIKE '%tổng quát%'
ORDER BY name, gender_requirement;

-- Kết quả mong đợi:
-- id | package_code | name                                    | gender_requirement
-- ---+--------------+-----------------------------------------+-------------------
-- 1  | PKG001       | Gói khám sức khỏe tổng quát - Nam      | male
-- 3  | PKG003       | Gói khám sức khỏe tổng quát - Nữ       | female

COMMIT;
