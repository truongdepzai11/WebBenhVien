<?php
require_once __DIR__ . '/config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "<h2>Full payments table structure:</h2>";
$query = "DESCRIBE payments";
$stmt = $conn->prepare($query);
$stmt->execute();

$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<table border='1'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
foreach ($columns as $column) {
    echo "<tr>";
    echo "<td>" . $column['Field'] . "</td>";
    echo "<td>" . $column['Type'] . "</td>";
    echo "<td>" . $column['Null'] . "</td>";
    echo "<td>" . $column['Key'] . "</td>";
    echo "<td>" . ($column['Default'] ?: 'NULL') . "</td>";
    echo "<td>" . $column['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Test manual insert exactly like cash
echo "<h2>Test manual insert (like cash):</h2>";
$testQuery = "INSERT INTO payments (invoice_id, amount, payment_method, payment_status, transaction_id, gateway_response, payment_date) VALUES (1, 50000, 'momo', 'success', NULL, NULL, NOW())";
try {
    $stmt = $conn->prepare($testQuery);
    $result = $stmt->execute();
    if ($result) {
        $id = $conn->lastInsertId();
        echo "<p style='color: green;'>Manual insert successful! ID: $id</p>";
        
        // Check if it appears in getByInvoiceId
        $checkQuery = "SELECT * FROM payments WHERE invoice_id = 1 AND payment_method = 'momo' ORDER BY id DESC LIMIT 1";
        $stmt = $conn->prepare($checkQuery);
        $stmt->execute();
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($payment) {
            echo "<p style='color: green;'>Payment found in database!</p>";
            echo "<pre>" . print_r($payment, true) . "</pre>";
        } else {
            echo "<p style='color: red;'>Payment NOT found after insert!</p>";
        }
        
        // Clean up
        $deleteQuery = "DELETE FROM payments WHERE id = $id";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->execute();
    } else {
        echo "<p style='color: red;'>Manual insert failed!</p>";
        echo "<p>Error: " . print_r($stmt->errorInfo(), true) . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Exception: " . $e->getMessage() . "</p>";
}
?>
