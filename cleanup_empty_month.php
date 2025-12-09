<?php
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

echo "🧹 Cleaning up records with empty appointment_year_month...\n\n";

// Find and delete records with empty appointment_year_month
$sql = "SELECT id FROM package_appointments WHERE appointment_year_month = '' OR appointment_year_month IS NULL";
$stmt = $conn->query($sql);
$badRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($badRecords)) {
    echo "✅ No bad records found!\n";
} else {
    echo "Found " . count($badRecords) . " record(s) to delete:\n";
    
    foreach ($badRecords as $rec) {
        echo "  Deleting ID {$rec['id']}...\n";
        
        // Delete corresponding appointments first
        $conn->exec("DELETE FROM appointments WHERE package_appointment_id = {$rec['id']}");
        
        // Delete package appointment
        $conn->exec("DELETE FROM package_appointments WHERE id = {$rec['id']}");
    }
    
    echo "\n✅ Cleanup complete!\n";
}

// Verify
echo "\nVerifying...\n";
$sql = "SELECT COUNT(*) as count FROM package_appointments WHERE appointment_year_month = '' OR appointment_year_month IS NULL";
$result = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);
echo "Records with empty/NULL appointment_year_month: {$result['count']}\n";

if ($result['count'] == 0) {
    echo "\n✅ All clean! UNIQUE INDEX should now work correctly.\n";
}

?>
