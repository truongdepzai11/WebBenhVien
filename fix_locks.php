<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=hospital_management', 'root', '');
    $pdo->exec('SET innodb_lock_wait_timeout = 5');
    echo "Lock timeout set to 5 seconds\n";
    
    // Kill any sleeping connections
    $stmt = $pdo->query("SHOW PROCESSLIST");
    while ($row = $stmt->fetch()) {
        if ($row['State'] == 'Locked' || $row['Command'] == 'Sleep') {
            echo "Killing process ID: " . $row['Id'] . "\n";
            $pdo->exec("KILL " . $row['Id']);
        }
    }
    echo "Fixed locks\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
