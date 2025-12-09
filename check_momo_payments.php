<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/Models/Payment.php';

$database = new Database();
$conn = $database->getConnection();

// Kiểm tra payments table
$query = "SELECT * FROM payments WHERE payment_method = 'momo' ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->execute();

$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Recent MoMo Payments:</h2>";
if (empty($payments)) {
    echo "<p>No MoMo payments found</p>";
} else {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Payment Code</th><th>Invoice ID</th><th>Amount</th><th>Method</th><th>Status</th><th>Payment Date</th></tr>";
    foreach ($payments as $payment) {
        echo "<tr>";
        echo "<td>" . $payment['id'] . "</td>";
        echo "<td>" . $payment['payment_code'] . "</td>";
        echo "<td>" . $payment['invoice_id'] . "</td>";
        echo "<td>" . number_format($payment['amount']) . "</td>";
        echo "<td>" . $payment['payment_method'] . "</td>";
        echo "<td>" . $payment['payment_status'] . "</td>";
        echo "<td>" . $payment['payment_date'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Kiểm tra tất cả payments
echo "<h2>All Recent Payments:</h2>";
$query = "SELECT * FROM payments ORDER BY created_at DESC LIMIT 10";
$stmt = $conn->prepare($query);
$stmt->execute();

$allPayments = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($allPayments)) {
    echo "<p>No payments found at all</p>";
} else {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Payment Code</th><th>Invoice ID</th><th>Amount</th><th>Method</th><th>Status</th><th>Payment Date</th></tr>";
    foreach ($allPayments as $payment) {
        echo "<tr>";
        echo "<td>" . $payment['id'] . "</td>";
        echo "<td>" . $payment['payment_code'] . "</td>";
        echo "<td>" . $payment['invoice_id'] . "</td>";
        echo "<td>" . number_format($payment['amount']) . "</td>";
        echo "<td>" . $payment['payment_method'] . "</td>";
        echo "<td>" . $payment['payment_status'] . "</td>";
        echo "<td>" . $payment['payment_date'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>
