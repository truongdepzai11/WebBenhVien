$content = Get-Content "c:\xampp\htdocs\WebBenhvien\hospital-management-system\app\Controllers\AppointmentController.php"
# Replace all occurrences
$content = $content -replace "findFirstByPackageAppointmentId\(`$.*\['package_appointment_id'\]\)", "findByAppointmentId(`$1['id'])"
Set-Content "c:\xampp\htdocs\WebBenhvien\hospital-management-system\app\Controllers\AppointmentController.php" $content
Write-Host "Fixed all occurrences!"
