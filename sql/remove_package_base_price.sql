-- XÓA CỘT GIÁ GỐC TRONG HEALTH_PACKAGES
-- Giá gói sẽ tính tự động = SUM(giá dịch vụ)

ALTER TABLE health_packages 
DROP COLUMN IF EXISTS price_male,
DROP COLUMN IF EXISTS price_female;

-- Thêm view để tính giá tự động
CREATE OR REPLACE VIEW package_prices AS
SELECT 
    hp.id AS package_id,
    hp.name AS package_name,
    hp.gender_requirement,
    COUNT(ps.id) AS total_services,
    SUM(ps.service_price) AS total_price,
    SUM(CASE WHEN ps.is_required = 1 THEN ps.service_price ELSE 0 END) AS required_price
FROM health_packages hp
LEFT JOIN package_services ps ON ps.package_id = hp.id
GROUP BY hp.id;

COMMIT;
