$content = Get-Content "c:\xampp\htdocs\WebBenhvien\hospital-management-system\app\Controllers\AppointmentController.php"
$content = $content -replace "findFirstByPackageAppointmentId(`$apt\['package_appointment_id'\])", "findByAppointmentId(`$apt['id'])"
$content = $content -replace "findFirstByPackageAppointmentId(`$appointment\['package_appointment_id'\])", "findByAppointmentId(`$appointment['id'])"
Set-Content "c:\xampp\htdocs\WebBenhvien\hospital-management-system\app\Controllers\AppointmentController.php" $content
Write-Host "Fixed manually!"
