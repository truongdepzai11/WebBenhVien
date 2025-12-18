<?php
require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Kiểm tra appointment 552
$stmt = $conn->prepare('SELECT COUNT(*) as count FROM appointment_package_services WHERE appointment_id = 552');
$stmt->execute();
$result = $stmt->fetch();
echo 'Selected services count for appointment 552: ' . $result['count'] . PHP_EOL;

// Kiểm tra package appointment
$stmt2 = $conn->prepare('SELECT id, package_id FROM package_appointments WHERE id = 118');
$stmt2->execute();
$pa = $stmt2->fetch();
echo 'Package appointment 118: ' . print_r($pa, true) . PHP_EOL;

// Kiểm tra appointment summary
$stmt3 = $conn->prepare('SELECT id FROM appointments WHERE package_appointment_id = 118');
$stmt3->execute();
$summary = $stmt3->fetch();
echo 'Summary appointment: ' . print_r($summary, true) . PHP_EOL;
?>
