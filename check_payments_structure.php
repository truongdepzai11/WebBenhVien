<?php
require_once __DIR__ . '/config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Xem cấu trúc table payments
echo "<h2>Structure of payments table:</h2>";
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
    echo "<td>" . $column['Default'] . "</td>";
    echo "<td>" . $column['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Kiểm tra enum values cho payment_method
echo "<h2>ENUM values for payment_method:</h2>";
$query = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
          WHERE TABLE_NAME = 'payments' 
          AND COLUMN_NAME = 'payment_method' 
          AND TABLE_SCHEMA = DATABASE()";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<p>" . ($result['COLUMN_TYPE'] ?? 'Not found') . "</p>";
?>
