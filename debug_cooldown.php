<?php
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

echo "🔍 DETAILED DEBUG: Testing checkCooldown() logic\n\n";

// Patient 14, Package 3
$patientId = 14;
$packageId = 3;

echo "Testing: Patient $patientId, Package $packageId\n\n";

// Step 1: Check what's in database
echo "Step 1: Current records in database:\n";
$sql = "SELECT id, patient_id, package_id, created_at, appointment_year_month, status 
        FROM package_appointments 
        WHERE patient_id = $patientId AND package_id = $packageId
        ORDER BY created_at DESC";
$stmt = $conn->query($sql);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($records as $rec) {
    echo "  - ID {$rec['id']}: created_at={$rec['created_at']}, year_month={$rec['appointment_year_month']}, status={$rec['status']}\n";
}

// Step 2: Test the exact query that checkCooldown uses
echo "\n\nStep 2: Simulating checkCooldown() query:\n";
$cooldownDays = 90; // Package 3 is 90 days
$queryLastAppointment = "SELECT id, created_at
                          FROM package_appointments 
                          WHERE patient_id = $patientId 
                            AND package_id = $packageId 
                            AND status IN ('pending', 'scheduled', 'in_progress', 'completed')
                          ORDER BY created_at DESC
                          LIMIT 1";

echo "  Query: $queryLastAppointment\n";
$stmtTest = $conn->query($queryLastAppointment);
$lastAppointmentData = $stmtTest->fetch(PDO::FETCH_ASSOC);

if ($lastAppointmentData) {
    echo "  Result: ID {$lastAppointmentData['id']}, created_at={$lastAppointmentData['created_at']}\n";
    
    $lastDate = new DateTime($lastAppointmentData['created_at']);
    $currentDate = new DateTime('now');
    $interval = $currentDate->diff($lastDate);
    $daysDiff = (int)$interval->days;
    
    echo "  Days since last appointment: $daysDiff days\n";
    echo "  Cooldown period required: $cooldownDays days\n";
    
    if ($daysDiff < $cooldownDays) {
        $remainingDays = $cooldownDays - $daysDiff;
        echo "  ⚠️  IN COOLDOWN! Remaining: $remainingDays days\n";
        echo "  checkCooldown() should return: is_in_cooldown=true\n";
    } else {
        echo "  ✅ NOT in cooldown (can book)\n";
        echo "  checkCooldown() should return: is_in_cooldown=false\n";
    }
} else {
    echo "  Result: NO RECORD FOUND\n";
    echo "  ⚠️  This is wrong! Record exists in database!\n";
}

// Step 3: Actually call checkCooldown()
echo "\n\nStep 3: Actually calling checkCooldown():\n";
require_once __DIR__ . '/app/Models/PackageAppointment.php';
$packageAppointmentModel = new PackageAppointment();
$result = $packageAppointmentModel->checkCooldown($patientId, $packageId);
echo "  Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";

if (!$result['is_in_cooldown']) {
    echo "  ⚠️  PROBLEM! checkCooldown() says NOT in cooldown, but database has records!\n";
} else {
    echo "  ✅ checkCooldown() correctly detected cooldown\n";
}

?>
