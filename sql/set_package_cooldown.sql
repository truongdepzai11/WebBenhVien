-- SET COOLDOWN DAYS FOR HEALTH PACKAGES
-- Mỗi bệnh nhân chỉ được đặt 1 lần duy nhất mỗi gói khám trong khoảng thời gian cooldown

UPDATE health_packages SET cooldown_days = 30 WHERE id = 1; -- Gói khám sức khỏe tổng quát - Nam (30 ngày)
UPDATE health_packages SET cooldown_days = 30 WHERE id = 2; -- Gói khám sức khỏe tổng quát - Nữ (30 ngày)
UPDATE health_packages SET cooldown_days = 90 WHERE id = 3; -- Gói khám phụ sản (90 ngày - định kỳ 3 tháng)
UPDATE health_packages SET cooldown_days = 30 WHERE id = 4; -- Gói khám sức khỏe tổng quát Nam-Nữ (30 ngày)
UPDATE health_packages SET cooldown_days = 30 WHERE id = 5; -- Gói khám tầm soát ung thư cơ bản (30 ngày)
