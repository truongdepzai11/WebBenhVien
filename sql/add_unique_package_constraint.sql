-- ADD UNIQUE INDEX TO PREVENT DUPLICATE PACKAGE APPOINTMENTS IN SAME MONTH
-- This ensures a patient can only have 1 active/pending/scheduled appointment per package per month

ALTER TABLE package_appointments 
ADD UNIQUE KEY `unique_patient_package_month` (
    `patient_id`,
    `package_id`,
    YEAR(`created_at`),
    MONTH(`created_at`)
);
