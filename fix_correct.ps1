$content = Get-Content "c:\xampp\htdocs\WebBenhvien\hospital-management-system\app\Controllers\AppointmentController.php"
# Fix saveResults method - line around 56
$content = $content -replace "        `$aps = `$apsModel->findByPackageAppointmentAndServiceName(`$apt\['package_appointment_id'\], `$apt\['reason'\]);", "        `$aps = `$apsModel->findByAppointmentId(`$apt['id']);"
# Fix submitResults method - line around 156
$content = $content -replace "        `$aps = `$apsModel->findByPackageAppointmentAndServiceName(`$apt\['package_appointment_id'\], `$apt\['reason'\]);", "        `$aps = `$apsModel->findByAppointmentId(`$apt['id']);"
# Fix display section - line around 1041
$content = $content -replace "            `$aps = `$apsModel->findByPackageAppointmentAndServiceName(`$appointment\['package_appointment_id'\], `$appointment\['reason'\]);", "            `$aps = `$apsModel->findByAppointmentId(`$appointment['id']);"
Set-Content "c:\xampp\htdocs\WebBenhvien\hospital-management-system\app\Controllers\AppointmentController.php" $content
Write-Host "Fixed correct methods!"
