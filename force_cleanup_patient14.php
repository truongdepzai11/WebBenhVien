<?php
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

echo "🔥 Force cleaning all duplicates for patient 14...\n\n";

// Get all package appointments for patient 14
$sql = "SELECT id, package_id, appointment_year_month 
        FROM package_appointments 
        WHERE patient_id = 14 
        ORDER BY package_id, created_at";
$stmt = $conn->query($sql);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Current records for patient 14:\n";
foreach ($records as $rec) {
    echo "  ID {$rec['id']}: Package {$rec['package_id']}, Month {$rec['appointment_year_month']}\n";
}

// Group by package_id and month
$groups = [];
foreach ($records as $rec) {
    $key = $rec['package_id'] . '_' . $rec['appointment_year_month'];
    if (!isset($groups[$key])) {
        $groups[$key] = [];
    }
    $groups[$key][] = $rec['id'];
}

echo "\n\nGrouping by package + month:\n";
$toDelete = [];
foreach ($groups as $key => $ids) {
    if (count($ids) > 1) {
        echo "  ⚠️  Package + Month '$key': " . count($ids) . " duplicates: " . implode(',', $ids) . "\n";
        // Keep first, delete rest
        $toDelete = array_merge($toDelete, array_slice($ids, 1));
    } else {
        echo "  ✅ Package + Month '$key': 1 record (OK)\n";
    }
}

if (!empty($toDelete)) {
    echo "\n\nDeleting IDs: " . implode(', ', $toDelete) . "\n";
    
    foreach ($toDelete as $id) {
        // Delete corresponding appointments
        $conn->exec("DELETE FROM appointments WHERE package_appointment_id = $id");
        // Delete package appointment
        $conn->exec("DELETE FROM package_appointments WHERE id = $id");
        echo "  ✅ Deleted ID $id\n";
    }
    
    echo "\n✅ Cleanup complete!\n";
} else {
    echo "\n✅ No duplicates found!\n";
}

?>
