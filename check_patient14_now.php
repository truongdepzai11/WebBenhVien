<?php
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

echo "🔍 Checking what's actually in database right now:\n\n";

echo "Package Appointments for patient 14:\n";
$sql = "SELECT id, package_id, created_at, appointment_year_month, status FROM package_appointments WHERE patient_id = 14 ORDER BY created_at DESC";
$stmt = $conn->query($sql);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($records)) {
    echo "  ✅ EMPTY (good!)\n";
} else {
    foreach ($records as $rec) {
        echo "  ID {$rec['id']}: pkg={$rec['package_id']}, month={$rec['appointment_year_month']}, status={$rec['status']}, created={$rec['created_at']}\n";
    }
}

echo "\n\nAppointments for patient 14:\n";
$sql = "SELECT id, package_appointment_id, package_id, created_at FROM appointments WHERE patient_id = 14 ORDER BY created_at DESC";
$stmt = $conn->query($sql);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($records)) {
    echo "  ✅ EMPTY (good!)\n";
} else {
    foreach ($records as $rec) {
        echo "  ID {$rec['id']}: pkg_appt_id={$rec['package_appointment_id']}, pkg={$rec['package_id']}, created={$rec['created_at']}\n";
    }
}

?>
