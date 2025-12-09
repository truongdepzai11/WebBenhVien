<?php
define('APP_PATH', __DIR__);
require_once APP_PATH . '/config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Check appointment 375
$stmt = $conn->prepare('SELECT * FROM appointments WHERE id = 375');
$stmt->execute();
$apt = $stmt->fetch(PDO::FETCH_ASSOC);

if ($apt) {
    echo "=== Appointment 375 EXISTS ===\n";
    echo "ID: " . $apt['id'] . ", Package ID: " . $apt['package_appointment_id'] . ", Reason: " . $apt['reason'] . "\n";
    
    // Check package services for this appointment
    $stmt2 = $conn->prepare('SELECT * FROM appointment_package_services WHERE appointment_id = 375');
    $stmt2->execute();
    $services = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n=== Package Services for 375 ===\n";
    foreach($services as $svc) {
        echo "Service ID: " . $svc['service_id'] . ", Result State: " . $svc['result_state'] . "\n";
    }
    
    // Check all appointments with same package_appointment_id
    if ($apt['package_appointment_id']) {
        $stmt3 = $conn->prepare('SELECT id, reason FROM appointments WHERE package_appointment_id = ?');
        $stmt3->execute([$apt['package_appointment_id']]);
        $allApts = $stmt3->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\n=== All appointments in package " . $apt['package_appointment_id'] . " ===\n";
        foreach($allApts as $a) {
            echo "ID: " . $a['id'] . ", Reason: " . $a['reason'] . "\n";
        }
    }
} else {
    echo "=== Appointment 375 NOT FOUND ===\n";
}
?>
