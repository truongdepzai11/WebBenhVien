-- FIX: Add database-level protection against duplicate package appointments in cooldown period
-- This uses a TRIGGER to prevent duplicate appointments atomically at database level

-- 1. Add a helper column to store the year-month for easier filtering
ALTER TABLE package_appointments 
ADD COLUMN `appointment_year_month` VARCHAR(7) DEFAULT NULL COMMENT 'YYYY-MM format for cooldown checking';

-- 2. Update existing records
UPDATE package_appointments 
SET appointment_year_month = DATE_FORMAT(created_at, '%Y-%m')
WHERE appointment_year_month IS NULL;

-- 3. Create UNIQUE index on patient+package+month for added protection
-- This will prevent the same patient from having 2 records for same package in same month
ALTER TABLE package_appointments 
ADD UNIQUE KEY `unique_patient_package_month` (patient_id, package_id, appointment_year_month);

-- 4. Create BEFORE INSERT trigger to set the appointment_year_month and validate cooldown
DELIMITER $$

DROP TRIGGER IF EXISTS `before_insert_package_appointments` $$

CREATE TRIGGER `before_insert_package_appointments`
BEFORE INSERT ON `package_appointments`
FOR EACH ROW
BEGIN
    DECLARE last_month_count INT DEFAULT 0;
    DECLARE cooldown_days INT DEFAULT 0;
    
    -- Set the year-month field
    SET NEW.appointment_year_month = DATE_FORMAT(NEW.created_at, '%Y-%m');
    
    -- Get cooldown days for this package
    SELECT cooldown_days INTO cooldown_days 
    FROM health_packages 
    WHERE id = NEW.package_id 
    LIMIT 1;
    
    -- If cooldown_days > 0, check if patient already has an appointment this month
    IF cooldown_days > 0 THEN
        SELECT COUNT(*) INTO last_month_count
        FROM package_appointments
        WHERE patient_id = NEW.patient_id
          AND package_id = NEW.package_id
          AND appointment_year_month = DATE_FORMAT(NEW.created_at, '%Y-%m')
          AND status IN ('pending', 'scheduled', 'in_progress', 'completed')
          AND id != NEW.id;
        
        -- If found, reject the insert
        IF last_month_count > 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'COOLDOWN_VIOLATION: Patient already has an appointment for this package this month';
        END IF;
    END IF;
END$$

DELIMITER ;

