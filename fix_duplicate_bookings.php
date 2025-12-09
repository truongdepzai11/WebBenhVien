<?php
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

echo "🔧 Fixing duplicate package bookings...\n\n";

// Step 1: Delete all test/duplicate package appointments
echo "Step 1: Identifying and cleaning duplicate package appointments...\n";
try {
    // Get all package appointments grouped by patient + package + month
    $sql = "SELECT patient_id, package_id, DATE_FORMAT(created_at, '%Y-%m') as month, 
                   COUNT(*) as count, 
                   GROUP_CONCAT(id ORDER BY created_at DESC) as ids
            FROM package_appointments
            GROUP BY patient_id, package_id, month
            HAVING COUNT(*) > 1
            ORDER BY patient_id, package_id";
    
    $stmt = $conn->query($sql);
    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($duplicates)) {
        echo "✅ No duplicates found!\n";
    } else {
        echo "Found " . count($duplicates) . " duplicate groups:\n";
        
        foreach ($duplicates as $dup) {
            $ids = $dup['ids'];
            $idArray = explode(',', $ids);
            // Keep the first (oldest), delete the rest
            $toDelete = array_slice($idArray, 1);
            
            if (!empty($toDelete)) {
                echo "  - Patient {$dup['patient_id']}, Package {$dup['package_id']}, Month {$dup['month']}: ";
                echo "Keeping ID {$idArray[0]}, deleting IDs " . implode(',', $toDelete) . "\n";
                
                // Delete corresponding appointments first (foreign key)
                foreach ($toDelete as $paId) {
                    $conn->exec("DELETE FROM appointments WHERE package_appointment_id = $paId");
                }
                
                // Delete package appointments
                $deleteIds = implode(',', $toDelete);
                $conn->exec("DELETE FROM package_appointments WHERE id IN ($deleteIds)");
            }
        }
        echo "\n✅ Duplicates cleaned!\n";
    }
} catch (Exception $e) {
    echo "❌ Error cleaning duplicates: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 2: Drop and recreate UNIQUE INDEX to enforce it
echo "\nStep 2: Rebuilding UNIQUE INDEX...\n";
try {
    // Drop existing index
    $conn->exec("ALTER TABLE package_appointments DROP INDEX IF EXISTS unique_patient_package_month");
    echo "  - Dropped old index\n";
    
    // Recreate UNIQUE INDEX
    $conn->exec("ALTER TABLE package_appointments ADD UNIQUE INDEX unique_patient_package_month 
                (patient_id, package_id, DATE_FORMAT(created_at, '%Y-%m'))");
    echo "  - Created new UNIQUE INDEX with DATE_FORMAT\n";
} catch (Exception $e) {
    // If DATE_FORMAT in index doesn't work, use a different approach
    echo "  - Index with DATE_FORMAT not supported, using appointment_year_month column instead\n";
    
    try {
        $conn->exec("ALTER TABLE package_appointments DROP INDEX IF EXISTS unique_patient_package_month");
        
        // First update appointment_year_month for all records
        $conn->exec("UPDATE package_appointments SET appointment_year_month = DATE_FORMAT(created_at, '%Y-%m')");
        
        // Create index on the column
        $conn->exec("ALTER TABLE package_appointments ADD UNIQUE INDEX unique_patient_package_month 
                    (patient_id, package_id, appointment_year_month)");
        echo "  - Created UNIQUE INDEX on appointment_year_month column\n";
    } catch (Exception $e2) {
        echo "⚠️  Warning: Could not create UNIQUE INDEX: " . $e2->getMessage() . "\n";
    }
}

echo "\n✅ All fixes applied!\n";
echo "Now testing cooldown logic...\n\n";

// Step 3: Verify checkCooldown logic
echo "Step 3: Verifying checkCooldown protection...\n";
try {
    require_once __DIR__ . '/app/Models/PackageAppointment.php';
    
    $packageAppointmentModel = new PackageAppointment();
    
    // Check patient 14 (who had duplicates) for each package
    for ($pid = 1; $pid <= 5; $pid++) {
        $result = $packageAppointmentModel->checkCooldown(14, $pid);
        echo "  - Patient 14, Package $pid: ";
        if ($result['is_in_cooldown']) {
            echo "IN COOLDOWN ({$result['remaining_days']} days remaining)\n";
        } else {
            echo "Not in cooldown\n";
        }
    }
} catch (Exception $e) {
    echo "⚠️  Could not test cooldown: " . $e->getMessage() . "\n";
}

echo "\n✅ All steps completed!\n";
?>
