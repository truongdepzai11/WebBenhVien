-- Whitelist thuốc theo dịch vụ trong gói
-- Run in phpMyAdmin (database: hospital_management)
START TRANSACTION;

CREATE TABLE IF NOT EXISTS `service_allowed_medicines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_service_medicine` (`service_id`,`medicine_id`),
  KEY `idx_sam_service` (`service_id`),
  KEY `idx_sam_medicine` (`medicine_id`),
  CONSTRAINT `fk_sam_service` FOREIGN KEY (`service_id`) REFERENCES `package_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_sam_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
