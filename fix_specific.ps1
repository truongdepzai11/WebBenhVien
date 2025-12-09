$content = Get-Content "c:\xampp\htdocs\WebBenhvien\hospital-management-system\app\Controllers\AppointmentController.php"
# Fix line 56 in saveResults
$content = $content -replace "        `$aps = `$apsModel->findFirstByPackageAppointmentId(`$apt\['package_appointment_id'\]);", "        `$aps = `$apsModel->findByAppointmentId(`$apt['id']);"
# Fix line 156 in submitResults  
$content = $content -replace "        `$aps = `$apsModel->findFirstByPackageAppointmentId(`$apt\['package_appointment_id'\]);", "        `$aps = `$apsModel->findByAppointmentId(`$apt['id']);"
# Fix line 1041 in display
$content = $content -replace "            `$aps = `$apsModel->findFirstByPackageAppointmentId(`$appointment\['package_appointment_id'\]);", "            `$aps = `$apsModel->findByAppointmentId(`$appointment['id']);"
Set-Content "c:\xampp\htdocs\WebBenhvien\hospital-management-system\app\Controllers\AppointmentController.php" $content
Write-Host "Fixed specific lines!"
