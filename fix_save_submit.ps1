$content = Get-Content "c:\xampp\htdocs\WebBenhvien\hospital-management-system\app\Controllers\AppointmentController.php"
# Fix saveResults method
$content = $content -replace "findFirstByPackageAppointmentId(`$apt\['package_appointment_id'\])", "findByAppointmentId(`$apt['id'])"
Set-Content "c:\xampp\htdocs\WebBenhvien\hospital-management-system\app\Controllers\AppointmentController.php" $content
Write-Host "Fixed save and submit methods!"
