<?php
// Debug POST data khi save results
echo "<pre>";
echo "=== POST Data ===\n";
print_r($_POST);
echo "\n=== SESSION Messages ===\n";
if (isset($_SESSION)) {
    print_r($_SESSION);
}
echo "</pre>";
?>
