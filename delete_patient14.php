<?php
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

echo "🗑️  Deleting all records for patient 14...\n";
$conn->exec("DELETE FROM appointments WHERE package_appointment_id IN (SELECT id FROM package_appointments WHERE patient_id = 14)");
$conn->exec("DELETE FROM package_appointments WHERE patient_id = 14");

echo "✅ Deleted!\n\n";

echo "Verifying (should be empty):\n";
$result = $conn->query("SELECT COUNT(*) as count FROM package_appointments WHERE patient_id = 14")->fetch();
echo "Patient 14 package appointments: " . $result['count'] . "\n";

?>
