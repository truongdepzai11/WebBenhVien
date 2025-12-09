<?php
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

echo "🔍 Checking database status...\n\n";

// Check patient 14's package appointments
echo "Step 1: Patient 14's appointments:\n";
$sql = "SELECT id, package_id, created_at, appointment_year_month, status 
        FROM package_appointments 
        WHERE patient_id = 14 
        ORDER BY package_id, created_at DESC";
$stmt = $conn->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $row) {
    echo "  ID {$row['id']}: Package {$row['package_id']}, Created {$row['created_at']}, Year-Month: {$row['appointment_year_month']}, Status: {$row['status']}\n";
}

echo "\n\nStep 2: Checking UNIQUE INDEX status:\n";
$sql = "SELECT INDEX_NAME, COLUMN_NAME, SEQ_IN_INDEX 
        FROM INFORMATION_SCHEMA.STATISTICS 
        WHERE TABLE_NAME = 'package_appointments' 
        AND INDEX_NAME = 'unique_patient_package_month'
        ORDER BY SEQ_IN_INDEX";
$stmt = $conn->query($sql);
$indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($indexes)) {
    echo "  ❌ UNIQUE INDEX 'unique_patient_package_month' NOT FOUND!\n";
} else {
    echo "  ✅ UNIQUE INDEX exists:\n";
    foreach ($indexes as $idx) {
        echo "    - Column: {$idx['COLUMN_NAME']}, Sequence: {$idx['SEQ_IN_INDEX']}\n";
    }
}

echo "\n\nStep 3: Check for NULL appointment_year_month values:\n";
$sql = "SELECT COUNT(*) as null_count FROM package_appointments WHERE appointment_year_month IS NULL";
$result = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);
echo "  Records with NULL appointment_year_month: {$result['null_count']}\n";

echo "\n\nStep 4: Check table structure:\n";
$sql = "SHOW COLUMNS FROM package_appointments";
$stmt = $conn->query($sql);
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns as $col) {
    echo "  - {$col['Field']}: {$col['Type']} {$col['Null']} {$col['Key']}\n";
}

?>
