-- Thêm cột package_appointment_id vào bảng appointments
-- Để liên kết các appointment với đăng ký gói khám

ALTER TABLE `appointments` 
ADD COLUMN `package_appointment_id` INT(11) NULL DEFAULT NULL AFTER `package_id`,
ADD KEY `fk_appointments_package_appointment` (`package_appointment_id`),
ADD CONSTRAINT `fk_appointments_package_appointment` 
    FOREIGN KEY (`package_appointment_id`) 
    REFERENCES `package_appointments` (`id`) 
    ON DELETE CASCADE;

-- Thêm comment để giải thích
ALTER TABLE `appointments` 
MODIFY COLUMN `package_appointment_id` INT(11) NULL DEFAULT NULL 
COMMENT 'ID của đăng ký gói khám (nếu appointment này thuộc 1 gói khám)';
