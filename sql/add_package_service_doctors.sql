-- Create mapping table for allowed doctors per package service
-- Run this in phpMyAdmin on database `hospital_management`

START TRANSACTION;

-- 1) Safety: create table only if it does not exist
CREATE TABLE IF NOT EXISTS `package_service_doctors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_service_doctor` (`service_id`,`doctor_id`),
  KEY `idx_psd_service` (`service_id`),
  KEY `idx_psd_doctor` (`doctor_id`),
  CONSTRAINT `fk_psd_service`
    FOREIGN KEY (`service_id`) REFERENCES `package_services` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_psd_doctor`
    FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

-- Optional quick checks:
-- SELECT * FROM package_service_doctors ORDER BY service_id, doctor_id;
-- Example insert: INSERT INTO package_service_doctors(service_id, doctor_id) VALUES (1, 3), (1, 5);
-- To rollback: DROP TABLE IF EXISTS package_service_doctors;
