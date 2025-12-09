<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/Models/PackageAppointment.php';

$db = new Database();
$conn = $db->getConnection();

echo "🔍 Testing hasBookingThisMonth()...\n\n";

$packageAppointmentModel = new PackageAppointment();

// Check patient 14, package 3
$patientId = 14;
$packageId = 3;

echo "Step 1: Check database for patient $patientId, package $packageId this month:\n";
$month = date('Y-m');
$sql = "SELECT id, appointment_year_month, status FROM package_appointments 
        WHERE patient_id = $patientId AND package_id = $packageId AND appointment_year_month = '$month'";
echo "  Query: $sql\n";
$result = $conn->query($sql)->fetchAll();
echo "  Records found: " . count($result) . "\n";
foreach ($result as $rec) {
    echo "    - ID {$rec['id']}: month={$rec['appointment_year_month']}, status={$rec['status']}\n";
}

echo "\nStep 2: Call hasBookingThisMonth():\n";
$hasBooking = $packageAppointmentModel->hasBookingThisMonth($patientId, $packageId);
echo "  Result: " . ($hasBooking ? "TRUE (booking exists)" : "FALSE (no booking)") . "\n";

if ($hasBooking) {
    echo "\n✅ hasBookingThisMonth() correctly detected duplicate!\n";
} else {
    echo "\n❌ hasBookingThisMonth() FAILED to detect duplicate!\n";
}

?>
