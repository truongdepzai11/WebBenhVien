<?php
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

echo "🔧 Removing problematic trigger...\n";

try {
    $conn->exec("DROP TRIGGER IF EXISTS `before_insert_package_appointments`");
    echo "✅ Trigger dropped successfully!\n";
    echo "\nNote: Database protection is now via UNIQUE INDEX only.\n";
    echo "Application-level transaction protection is the primary safeguard.\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
