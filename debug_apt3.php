<?php
define('APP_PATH', __DIR__);
require_once APP_PATH . '/config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Check all package services for package 85
$stmt = $conn->prepare('SELECT aps.*, a.reason FROM appointment_package_services aps JOIN appointments a ON a.id = aps.appointment_id WHERE a.package_appointment_id = 85');
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "=== All Package Services for Package 85 ===\n";
foreach($services as $svc) {
    echo "Appointment ID: " . $svc['appointment_id'] . ", Service ID: " . $svc['service_id'] . ", Reason: " . $svc['reason'] . "\n";
}

// Check which appointment is the summary
$stmt2 = $conn->prepare('SELECT * FROM appointments WHERE id = 370');
$stmt2->execute();
$summary = $stmt2->fetch(PDO::FETCH_ASSOC);

echo "\n=== Summary Appointment (370) ===\n";
if ($summary) {
    echo "ID: " . $summary['id'] . ", Reason: " . $summary['reason'] . ", Is Summary: " . ($summary['is_summary'] ? 'Yes' : 'No') . "\n";
}
?>
