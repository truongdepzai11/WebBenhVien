-- KIỂM TRA VÀ FIX GIÁ DỊCH VỤ

-- 1. XEM TẤT CẢ DỊCH VỤ HIỆN TẠI
SELECT id, service_name, service_category, service_price, is_required
FROM package_services
ORDER BY service_category, service_name;

-- 2. CẬP NHẬT GIÁ CHO TỪNG DỊCH VỤ CỤ THỂ (theo tên chính xác)
UPDATE package_services SET service_price = 200000 WHERE service_name = 'HCL-C không tồn tại (chỉ có HDL-C)';
UPDATE package_services SET service_price = 200000 WHERE service_name = 'Đo nồng độ HDL-C';
UPDATE package_services SET service_price = 200000 WHERE service_name = 'HDL-C';
UPDATE package_services SET service_price = 50000 WHERE service_name = 'Đo nồng độ LDL-C';
UPDATE package_services SET service_price = 50000 WHERE service_name = 'LDL-C';
UPDATE package_services SET service_price = 50000 WHERE service_name = 'Triglycerid';
UPDATE package_services SET service_price = 50000 WHERE service_name = 'Đo Triglycerid';
UPDATE package_services SET service_price = 50000 WHERE service_name = 'Tổng phân tích lắng máu (nếu lmay là lắng)';
UPDATE package_services SET service_price = 50000 WHERE service_name = 'Tổng phân tích nước tiểu (máy tự động)';

-- 3. CẬP NHẬT THEO PATTERN (backup)
UPDATE package_services SET service_price = 200000 WHERE service_name LIKE '%HDL%';
UPDATE package_services SET service_price = 50000 WHERE service_name LIKE '%LDL%';
UPDATE package_services SET service_price = 50000 WHERE service_name LIKE '%Triglycerid%';
UPDATE package_services SET service_price = 50000 WHERE service_name LIKE '%Tổng phân tích%';

-- 4. CẬP NHẬT GIÁ GÓI KHÁM (giảm xuống cho phù hợp)
-- Tổng thực tế: 200k + 50k + 50k + 50k + 50k = 400k (5 dịch vụ)
-- Giá gói = 500k (bao gồm phí quản lý)

UPDATE health_packages SET 
    price_male = 500000,
    price_female = 750000
WHERE name LIKE '%Gói khám sức khỏe tổng quát%';

-- 5. KIỂM TRA LẠI
SELECT 
    hp.name AS package_name,
    hp.price_male,
    hp.price_female,
    COUNT(ps.id) AS total_services,
    SUM(ps.service_price) AS total_service_price
FROM health_packages hp
LEFT JOIN package_services ps ON ps.package_id = hp.id
WHERE hp.name LIKE '%Gói khám%'
GROUP BY hp.id;

COMMIT;
