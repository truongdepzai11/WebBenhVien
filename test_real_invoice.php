<?php
require_once __DIR__ . '/config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "<h2>Get a real invoice ID:</h2>";
$query = "SELECT id, invoice_code FROM invoices ORDER BY id DESC LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->execute();
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($invoices)) {
    echo "<p style='color: red;'>No invoices found!</p>";
} else {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Invoice Code</th></tr>";
    foreach ($invoices as $invoice) {
        echo "<tr><td>" . $invoice['id'] . "</td><td>" . $invoice['invoice_code'] . "</td></tr>";
    }
    echo "</table>";
    
    // Test with real invoice ID
    $realInvoiceId = $invoices[0]['id'];
    echo "<h2>Test with real invoice ID: $realInvoiceId</h2>";
    
    $testQuery = "INSERT INTO payments (invoice_id, amount, payment_method, payment_status, transaction_id, gateway_response, payment_date) VALUES (?, ?, 'momo', 'success', NULL, NULL, NOW())";
    try {
        $stmt = $conn->prepare($testQuery);
        $stmt->execute([$realInvoiceId, 50000]);
        $id = $conn->lastInsertId();
        echo "<p style='color: green;'>Insert successful! ID: $id</p>";
        
        // Check if it appears in getByInvoiceId
        $checkQuery = "SELECT * FROM payments WHERE invoice_id = ? AND payment_method = 'momo' ORDER BY id DESC LIMIT 1";
        $stmt = $conn->prepare($checkQuery);
        $stmt->execute([$realInvoiceId]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($payment) {
            echo "<p style='color: green;'>Payment found in database!</p>";
            echo "<pre>" . print_r($payment, true) . "</pre>";
        } else {
            echo "<p style='color: red;'>Payment NOT found after insert!</p>";
        }
        
        // Clean up
        $deleteQuery = "DELETE FROM payments WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->execute([$id]);
        echo "<p>Test record cleaned up.</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Exception: " . $e->getMessage() . "</p>";
    }
}
?>
