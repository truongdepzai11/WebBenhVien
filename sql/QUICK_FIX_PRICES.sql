-- ==========================================
-- QUICK FIX: ĐẶT GIÁ ĐƠN GIẢN
-- ==========================================

-- BƯỚC 1: Đặt TẤT CẢ dịch vụ = 50,000đ
UPDATE package_services SET service_price = 50000;

-- BƯỚC 2: Chỉ dịch vụ BẮT BUỘC = 200,000đ
UPDATE package_services SET service_price = 200000 WHERE is_required = 1;

-- BƯỚC 3: Đặt giá gói = 500,000đ (đủ cho 1 dịch vụ 200k + 4 dịch vụ 50k = 400k + phí)
UPDATE health_packages SET 
    price_male = 500000,
    price_female = 500000;

-- BƯỚC 4: Kiểm tra
SELECT 'DỊCH VỤ:' AS info;
SELECT service_name, service_price, is_required FROM package_services;

SELECT 'GÓI KHÁM:' AS info;
SELECT name, price_male, price_female FROM health_packages;

SELECT 'TỔNG DỊCH VỤ:' AS info;
SELECT 
    SUM(service_price) AS total_all_services,
    SUM(CASE WHEN is_required = 1 THEN service_price ELSE 0 END) AS total_required
FROM package_services;

COMMIT;
