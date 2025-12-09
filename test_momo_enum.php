<?php
require_once __DIR__ . '/config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Kiểm tra enum values
echo "<h2>Payment Method ENUM Values:</h2>";
$query = "SHOW COLUMNS FROM payments WHERE Field = 'payment_method'";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    echo "<p><strong>Type:</strong> " . $result['Type'] . "</p>";
    
    // Extract enum values
    $enumStr = $result['Type'];
    preg_match("/^enum\((.*)\)$/", $enumStr, $matches);
    if (isset($matches[1])) {
        $values = str_getcsv($matches[1], ",", "'");
        echo "<p><strong>Allowed values:</strong></p><ul>";
        foreach ($values as $value) {
            echo "<li>" . htmlspecialchars($value) . "</li>";
        }
        echo "</ul>";
        
        if (!in_array('momo', $values)) {
            echo "<p style='color: red; font-weight: bold;'>PROBLEM: 'momo' is NOT in the enum values!</p>";
            echo "<p>Need to add 'momo' to the enum. Run this SQL:</p>";
            echo "<code>ALTER TABLE payments MODIFY COLUMN payment_method ENUM('cash','momo','vnpay','bank_transfer') NOT NULL;</code>";
        } else {
            echo "<p style='color: green; font-weight: bold;'>GOOD: 'momo' is in the enum values!</p>";
        }
    }
} else {
    echo "<p style='color: red;'>payment_method column not found!</p>";
}

// Test insert with momo
echo "<h2>Test Insert with 'momo':</h2>";
$testQuery = "INSERT INTO payments (payment_code, invoice_id, amount, payment_method, payment_status, payment_date) VALUES ('TEST001', 1, 1000, 'momo', 'success', NOW())";
try {
    $stmt = $conn->prepare($testQuery);
    $result = $stmt->execute();
    if ($result) {
        echo "<p style='color: green;'>Insert successful!</p>";
        // Delete test record
        $deleteQuery = "DELETE FROM payments WHERE payment_code = 'TEST001'";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->execute();
    } else {
        echo "<p style='color: red;'>Insert failed!</p>";
        echo "<p>Error: " . print_r($stmt->errorInfo(), true) . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Exception: " . $e->getMessage() . "</p>";
}
?>
