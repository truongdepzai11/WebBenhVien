-- FIX GIÁ CHÍNH XÁC CHO CÁC DỊCH VỤ ĐANG HIỂN THỊ

-- Đặt tất cả về 50,000đ trước (giá cơ bản)
UPDATE package_services SET service_price = 50000;

-- Chỉ 1 dịch vụ đầu tiên (bắt buộc) là 200,000đ
-- Giả sử dịch vụ đầu tiên có is_required = 1
UPDATE package_services 
SET service_price = 200000 
WHERE is_required = 1 
LIMIT 1;

-- HOẶC cập nhật theo thứ tự (dịch vụ đầu tiên)
UPDATE package_services ps1
SET ps1.service_price = 200000
WHERE ps1.id = (
    SELECT MIN(ps2.id) 
    FROM (SELECT id FROM package_services) ps2
);

-- Cập nhật giá gói = tổng dịch vụ
-- Tổng = 200k + 50k + 50k + 50k + 50k = 400k
-- Giá gói = 500k (thêm 100k phí quản lý)

UPDATE health_packages 
SET price_male = 500000,
    price_female = 500000
WHERE id IN (SELECT DISTINCT package_id FROM package_services);

-- Kiểm tra kết quả
SELECT 
    ps.id,
    ps.service_name,
    ps.service_price,
    ps.is_required,
    hp.name AS package_name,
    hp.price_male
FROM package_services ps
JOIN health_packages hp ON ps.package_id = hp.id
ORDER BY ps.display_order, ps.id;

COMMIT;
