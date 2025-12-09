<?php 
$page_title = 'Lịch làm việc Bác sĩ';
ob_start(); 
?>

<div class="mb-6 flex items-center justify-between">
    <h3 class="text-2xl font-bold text-gray-800">
        <?php if (Auth::isReceptionist()): ?>
            <i class="fas fa-user-plus mr-2"></i>Đăng ký khám Walk-in
        <?php else: ?>
            <i class="fas fa-calendar-alt mr-2"></i>Lịch làm việc Bác sĩ
        <?php endif; ?>
    </h3>
    
    <!-- View Toggle (chỉ cho Admin/Doctor) -->
    <?php if (!Auth::isReceptionist()): ?>
    <div class="flex bg-gray-100 rounded-lg p-1">
        <a href="?view=week<?= isset($_GET['doctor_id']) ? '&doctor_id=' . $_GET['doctor_id'] : '' ?>" 
           class="px-4 py-2 rounded-lg transition <?= (!isset($_GET['view']) || $_GET['view'] == 'week') ? 'bg-white shadow text-purple-600 font-semibold' : 'text-gray-600 hover:text-gray-800' ?>">
            <i class="fas fa-calendar-week mr-2"></i>Lịch tuần
        </a>
        <a href="?view=day<?= isset($_GET['doctor_id']) ? '&doctor_id=' . $_GET['doctor_id'] : '' ?><?= isset($_GET['date']) ? '&date=' . $_GET['date'] : '' ?>" 
           class="px-4 py-2 rounded-lg transition <?= (isset($_GET['view']) && $_GET['view'] == 'day') ? 'bg-white shadow text-purple-600 font-semibold' : 'text-gray-600 hover:text-gray-800' ?>">
            <i class="fas fa-calendar-day mr-2"></i>Lịch ngày
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- WRAPPER CHO RECEPTIONIST -->
<?php if (Auth::isReceptionist()): ?>
<!-- CHỌN LOẠI KHÁM -->
<div id="appointmentTypeSelector" class="mb-6 bg-white rounded-lg shadow-md p-6">
    <label class="block text-lg font-medium text-gray-700 mb-4">
        Chọn loại khám <span class="text-red-500">*</span>
    </label>
    
    <div class="grid grid-cols-2 gap-4">
    <!-- Khám thường -->
    <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-purple-500 transition cursor-pointer"
         onclick="selectAppointmentType('regular')" id="regularCard">
        <label class="flex items-start cursor-pointer">
            <input type="radio" name="walkin_type" value="regular" id="typeRegular"
                   onchange="selectAppointmentType('regular')"
                   class="mt-1 mr-3 w-5 h-5 text-purple-600">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <i class="fas fa-stethoscope text-blue-600 text-xl mr-2"></i>
                    <h3 class="text-lg font-bold text-gray-800">Khám thường</h3>
                </div>
                <p class="text-sm text-gray-600">
                    Đăng ký khám bệnh với bác sĩ chuyên khoa
                </p>
            </div>
        </label>
    </div>

    <!-- Khám theo gói -->
    <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-purple-500 transition cursor-pointer"
         onclick="selectAppointmentType('package')" id="packageCard">
        <label class="flex items-start cursor-pointer">
            <input type="radio" name="walkin_type" value="package" id="typePackage"
                   onchange="selectAppointmentType('package')"
                   class="mt-1 mr-3 w-5 h-5 text-purple-600">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <i class="fas fa-box-open text-green-600 text-xl mr-2"></i>
                    <h3 class="text-lg font-bold text-gray-800">Khám theo gói</h3>
                </div>
                <p class="text-sm text-gray-600">
                    Đăng ký gói khám sức khỏe tổng quát
                </p>
            </div>
        </label>
    </div>
</div>
</div>
<!-- End appointmentTypeSelector -->
<?php endif; ?>

<!-- LỊCH BÁC SĨ -->
<div id="scheduleSection" class="<?= Auth::isReceptionist() ? 'hidden' : '' ?>">

<?php 
$viewMode = $_GET['view'] ?? 'week';
?>

<?php if ($viewMode == 'week' && !Auth::isReceptionist()): ?>
<!-- WEEKLY VIEW -->
<?php 
// Lấy tuần hiện tại
$currentWeek = isset($_GET['week']) ? $_GET['week'] : date('Y-\WW');
$weekStart = new DateTime();
$weekStart->setISODate(substr($currentWeek, 0, 4), substr($currentWeek, 6, 2));
$weekDays = [];
for ($i = 0; $i < 7; $i++) {
    $weekDays[] = clone $weekStart;
    $weekStart->modify('+1 day');
}
?>

<!-- Doctor Selector for Weekly View -->
<?php if (Auth::isAdmin()): ?>
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form method="GET" class="flex items-center gap-4">
        <input type="hidden" name="view" value="week">
        <input type="hidden" name="walkin_type" value="<?= $_GET['walkin_type'] ?? 'regular' ?>">
        <label class="text-sm font-medium text-gray-700">Chọn bác sĩ:</label>
        <select name="doctor_id" onchange="this.form.submit()" 
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            <?php foreach ($doctors as $doc): ?>
            <option value="<?= $doc['id'] ?>" <?= $doc['id'] == $selectedDoctorId ? 'selected' : '' ?>>
                <?= htmlspecialchars($doc['full_name']) ?> - <?= htmlspecialchars($doc['specialization']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="refresh" value="1">
    </form>
</div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h4 class="text-lg font-bold text-gray-800">
            Tuần <?= substr($currentWeek, 6, 2) ?>, <?= substr($currentWeek, 0, 4) ?>
        </h4>
        <div class="flex space-x-2">
            <a href="?view=week&week=<?= date('Y-\WW', strtotime('-1 week', strtotime($currentWeek))) ?><?= isset($_GET['doctor_id']) ? '&doctor_id=' . $_GET['doctor_id'] : '' ?>" 
               class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 transition">
                <i class="fas fa-chevron-left"></i>
            </a>
            <a href="?view=week<?= isset($_GET['doctor_id']) ? '&doctor_id=' . $_GET['doctor_id'] : '' ?>" 
               class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 transition">
                Tuần này
            </a>
            <a href="?view=week&week=<?= date('Y-\WW', strtotime('+1 week', strtotime($currentWeek))) ?><?= isset($_GET['doctor_id']) ? '&doctor_id=' . $_GET['doctor_id'] : '' ?>" 
               class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 transition">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>
    
    <!-- Calendar Table -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gradient-to-r from-purple-500 to-indigo-600">
                    <th class="border border-purple-400 p-3 text-white font-semibold text-sm min-w-[80px]">Ca làm</th>
                    <?php foreach ($weekDays as $day): ?>
                    <th class="border border-purple-400 p-3 text-white font-semibold text-sm min-w-[120px]">
                        <div><?= $day->format('l') == 'Monday' ? 'Thứ 2' : ($day->format('l') == 'Tuesday' ? 'Thứ 3' : ($day->format('l') == 'Wednesday' ? 'Thứ 4' : ($day->format('l') == 'Thursday' ? 'Thứ 5' : ($day->format('l') == 'Friday' ? 'Thứ 6' : ($day->format('l') == 'Saturday' ? 'Thứ 7' : 'CN'))))) ?></div>
                        <div class="text-xs text-purple-100"><?= $day->format('d/m/Y') ?></div>
                    </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <!-- Ca Sáng -->
                <tr>
                    <td class="border border-gray-300 p-3 bg-yellow-50 font-semibold text-gray-700">
                        <i class="fas fa-sun text-yellow-500 mr-2"></i>Sáng
                    </td>
                    <?php foreach ($weekDays as $day): 
                        $dateKey = $day->format('Y-m-d');
                        $morningApts = [];
                        if (isset($weeklyAppointments[$dateKey])) {
                            foreach ($weeklyAppointments[$dateKey] as $apt) {
                                $hour = (int)substr($apt['appointment_time'], 0, 2);
                                if ($hour >= 8 && $hour < 12) {
                                    $morningApts[] = $apt;
                                }
                            }
                        }
                    ?>
                    <td class="border border-gray-300 p-2 align-top">
                        <?php if (!empty($morningApts)): ?>
                            <?php foreach ($morningApts as $apt): ?>
                            <div class="bg-green-100 border-l-4 border-green-500 p-2 rounded text-xs mb-1">
                                <p class="text-gray-600 mb-1">
                                    <i class="fas fa-clock text-green-600 mr-1"></i>
                                    <?= substr($apt['appointment_time'], 0, 5) ?>
                                </p>
                                <p class="font-semibold text-green-700">
                                    <i class="fas fa-user mr-1"></i>
                                    <?= htmlspecialchars($apt['patient_name']) ?>
                                </p>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <div class="text-center text-gray-400 py-4">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                
                <!-- Ca Chiều -->
                <tr>
                    <td class="border border-gray-300 p-3 bg-orange-50 font-semibold text-gray-700">
                        <i class="fas fa-cloud-sun text-orange-500 mr-2"></i>Chiều
                    </td>
                    <?php foreach ($weekDays as $day): 
                        $dateKey = $day->format('Y-m-d');
                        $afternoonApts = [];
                        if (isset($weeklyAppointments[$dateKey])) {
                            foreach ($weeklyAppointments[$dateKey] as $apt) {
                                $hour = (int)substr($apt['appointment_time'], 0, 2);
                                if ($hour >= 12 && $hour < 18) {
                                    $afternoonApts[] = $apt;
                                }
                            }
                        }
                    ?>
                    <td class="border border-gray-300 p-2 align-top">
                        <?php if (!empty($afternoonApts)): ?>
                            <?php foreach ($afternoonApts as $apt): ?>
                            <div class="bg-blue-100 border-l-4 border-blue-500 p-2 rounded text-xs mb-1">
                                <p class="text-gray-600 mb-1">
                                    <i class="fas fa-clock text-blue-600 mr-1"></i>
                                    <?= substr($apt['appointment_time'], 0, 5) ?>
                                </p>
                                <p class="font-semibold text-blue-700">
                                    <i class="fas fa-user mr-1"></i>
                                    <?= htmlspecialchars($apt['patient_name']) ?>
                                </p>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <div class="text-center text-gray-400 py-4">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                
                <!-- Ca Tối -->
                <tr>
                    <td class="border border-gray-300 p-3 bg-indigo-50 font-semibold text-gray-700">
                        <i class="fas fa-moon text-indigo-500 mr-2"></i>Tối
                    </td>
                    <?php foreach ($weekDays as $day): 
                        $dateKey = $day->format('Y-m-d');
                        $eveningApts = [];
                        if (isset($weeklyAppointments[$dateKey])) {
                            foreach ($weeklyAppointments[$dateKey] as $apt) {
                                $hour = (int)substr($apt['appointment_time'], 0, 2);
                                if ($hour >= 18) {
                                    $eveningApts[] = $apt;
                                }
                            }
                        }
                    ?>
                    <td class="border border-gray-300 p-2 bg-gray-50 align-top">
                        <?php if (!empty($eveningApts)): ?>
                            <?php foreach ($eveningApts as $apt): ?>
                            <div class="bg-indigo-100 border-l-4 border-indigo-500 p-2 rounded text-xs mb-1">
                                <p class="text-gray-600 mb-1">
                                    <i class="fas fa-clock text-indigo-600 mr-1"></i>
                                    <?= substr($apt['appointment_time'], 0, 5) ?>
                                </p>
                                <p class="font-semibold text-indigo-700">
                                    <i class="fas fa-user mr-1"></i>
                                    <?= htmlspecialchars($apt['patient_name']) ?>
                                </p>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <div class="text-center text-gray-400 py-4">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php else: ?>
<!-- DAILY VIEW (Original) -->
<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" action="<?= APP_URL ?>/schedule" class="grid grid-cols-1 md:grid-cols-<?= (Auth::isAdmin() || Auth::isReceptionist()) ? '3' : '2' ?> gap-4">
        <input type="hidden" name="view" value="day">
        <input type="hidden" name="walkin_type" value="<?= $_GET['walkin_type'] ?? 'regular' ?>">
        <!-- Chọn bác sĩ - ADMIN/RECEPTIONIST -->
        <?php if (Auth::isAdmin() || Auth::isReceptionist()): ?>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Bác sĩ</label>
            <select name="doctor_id" onchange="this.form.submit()" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                <?php foreach ($doctors as $doc): ?>
                <option value="<?= $doc['id'] ?>" <?= $doc['id'] == $selectedDoctorId ? 'selected' : '' ?>>
                    <?= htmlspecialchars($doc['full_name']) ?> - <?= htmlspecialchars($doc['specialization']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="refresh" value="1">
        </div>
        <?php endif; ?>

        <!-- Chọn ngày -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Ngày</label>
            <input type="date" name="date" value="<?= $selectedDate ?>" onchange="this.form.submit()"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            <input type="hidden" name="refresh" value="1">
        </div>

        <!-- Nút hôm nay -->
        <div class="flex items-end">
            <button type="button" onclick="window.location.href='<?= APP_URL ?>/schedule?doctor_id=<?= $selectedDoctorId ?>&date=<?= date('Y-m-d') ?>&refresh=1&walkin_type=<?= urlencode($_GET['walkin_type'] ?? 'regular') ?>'"
                    class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-calendar-day mr-2"></i>Hôm nay
            </button>
        </div>
    </form>
</div>

<!-- Schedule Table -->
<?php if ($selectedDoctor): ?>
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="bg-gradient-to-r from-purple-600 to-blue-600 text-white p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold"><?= htmlspecialchars($selectedDoctor['full_name']) ?></h2>
                <p class="text-purple-100"><?= htmlspecialchars($selectedDoctor['specialization']) ?></p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold"><?= date('d', strtotime($selectedDate)) ?></p>
                <p class="text-purple-100">Tháng <?= date('m/Y', strtotime($selectedDate)) ?></p>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 gap-3">
            <?php 
            $currentDateTime = time();
            foreach ($timeSlots as $time => $appointment): 
                $slotDateTime = strtotime($selectedDate . ' ' . $time);
                $isPast = $slotDateTime <= $currentDateTime;
                // Chỉ hiển thị mờ slot quá khứ cho Lễ tân (để họ không đặt)
                $showPastStyle = Auth::isReceptionist() && $isPast;
            ?>
            <div class="flex items-center border rounded-lg overflow-hidden hover:shadow-md transition <?= $showPastStyle ? 'opacity-50 bg-gray-50' : '' ?>">
                <!-- Time -->
                <div class="w-24 bg-gray-100 p-4 text-center border-r">
                    <p class="text-2xl font-bold text-gray-800"><?= date('H:i', strtotime($time)) ?></p>
                </div>

                <!-- Content -->
                <div class="flex-1 p-4">
                    <?php if ($appointment): ?>
                        <!-- Đã có lịch -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 flex-1">
                                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center shadow-md">
                                    <i class="fas fa-user text-white text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <p class="font-bold text-gray-900 text-lg"><?= htmlspecialchars($appointment['patient_name']) ?></p>
                                        <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs rounded-full font-medium">
                                            <?= htmlspecialchars($appointment['patient_code']) ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                                        <span>
                                            <i class="fas fa-phone mr-1 text-gray-400"></i>
                                            <?= htmlspecialchars($appointment['patient_phone']) ?>
                                        </span>
                                        <span>
                                            <i class="fas fa-notes-medical mr-1 text-gray-400"></i>
                                            <?= htmlspecialchars($appointment['reason']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'confirmed' => 'bg-blue-100 text-blue-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'no_show' => 'bg-red-100 text-red-800'
                                ];
                                $statusLabels = [
                                    'pending' => 'Chờ xác nhận',
                                    'confirmed' => 'Đã xác nhận',
                                    'completed' => 'Hoàn thành',
                                    'no_show' => 'Vắng mặt'
                                ];
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusColors[$appointment['status']] ?? 'bg-gray-100 text-gray-800' ?>">
                                    <?= $statusLabels[$appointment['status']] ?? $appointment['status'] ?>
                                </span>
                                <a href="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>" 
                                   class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 transition text-sm">
                                    Chi tiết
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Slot trống -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-calendar-plus text-gray-400 text-xl"></i>
                                </div>
                                <?php if (Auth::isReceptionist() && $isPast): ?>
                                <p class="text-gray-400 italic">Đã qua (không thể đặt)</p>
                                <?php else: ?>
                                <p class="text-gray-400 italic">Slot trống</p>
                                <?php endif; ?>
                            </div>
                            <?php if (Auth::isReceptionist() && !$isPast): ?>
                            <a href="<?= APP_URL ?>/schedule/add-patient?doctor_id=<?= $selectedDoctorId ?>&date=<?= $selectedDate ?>&time=<?= $time ?>" 
                               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-plus mr-2"></i>Thêm bệnh nhân
                            </a>
                            <?php elseif (Auth::isReceptionist() && $isPast): ?>
                            <span class="px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                                <i class="fas fa-ban mr-2"></i>Đã qua
                            </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php else: ?>
<!-- No doctor selected in daily view -->
<div class="bg-white rounded-lg shadow-md p-12 text-center">
    <i class="fas fa-user-md text-6xl text-gray-300 mb-4"></i>
    <p class="text-gray-500 text-lg">Chọn bác sĩ để xem lịch làm việc</p>
</div>
<?php endif; ?> <!-- End selectedDoctor check -->
<?php endif; ?> <!-- End view mode check -->

    </div>
    <!-- End scheduleSection -->

<!-- FORM ĐĂNG KÝ GÓI (CHỈ CHO RECEPTIONIST) -->
<?php if (Auth::isReceptionist()): ?>
<div id="packageSection" class="hidden">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">
                <i class="fas fa-box-open mr-2"></i>Đăng ký Gói khám Walk-in
            </h2>
            <p class="text-gray-600 mb-6">Đăng ký gói khám sức khỏe cho bệnh nhân tại quầy</p>

            <form method="POST" action="<?= APP_URL ?>/schedule/store-package-walkin">
                <!-- Chọn loại bệnh nhân (ĐẦU TIÊN) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Loại bệnh nhân <span class="text-red-500">*</span>
                    </label>
                    <div class="flex space-x-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="patient_type_pkg" value="existing" checked
                                   onchange="togglePatientFormPkg()"
                                   class="mr-2">
                            <span>Bệnh nhân cũ (đã có hồ sơ)</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="patient_type_pkg" value="new"
                                   onchange="togglePatientFormPkg()"
                                   class="mr-2">
                            <span>Bệnh nhân mới (lần đầu khám)</span>
                        </label>
                    </div>
                </div>

                <!-- Form chọn bệnh nhân cũ -->
                <div id="existingPatientFormPkg" class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Chọn bệnh nhân <span class="text-red-500">*</span>
                    </label>
                    <select name="patient_id" id="patientSelectPkg"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">-- Chọn bệnh nhân --</option>
                        <?php
                        require_once APP_PATH . '/Models/Patient.php';
                        $ptModel = new Patient();
                        $pts = $ptModel->getAll();
                        foreach ($pts as $pt):
                        ?>
                        <option value="<?= $pt['id'] ?>">
                            <?= htmlspecialchars($pt['full_name']) ?> (<?= $pt['patient_code'] ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Form tạo bệnh nhân mới -->
                <div id="newPatientFormPkg" class="hidden mb-6 p-6 bg-blue-50 rounded-lg border-2 border-blue-200">
                    <h3 class="font-bold text-gray-800 mb-4">
                        <i class="fas fa-user-plus mr-2"></i>Thông tin bệnh nhân mới
                    </h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Họ tên <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="new_patient_name" id="newPatientNamePkg"
                                   placeholder="Nguyễn Văn A"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Ngày sinh <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="new_patient_dob" id="newPatientDobPkg"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Số điện thoại <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="new_patient_phone" id="newPatientPhonePkg"
                                   placeholder="0912345678"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Giới tính <span class="text-red-500">*</span>
                            </label>
                            <select name="new_patient_gender" id="newPatientGenderPkg"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Địa chỉ <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="new_patient_address" id="newPatientAddressPkg"
                                   placeholder="123 Đường ABC, Quận 1, TP.HCM"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>
                </div>

                <!-- Chọn gói khám (SAU KHI ĐÃ CHỌN BỆNH NHÂN) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Chọn gói khám <span class="text-red-500">*</span>
                    </label>
                    <select name="package_id" id="packageSelectWalkin" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                            onchange="loadPackageInfoWalkin(this.value)">
                        <option value="">-- Chọn gói khám --</option>
                        <?php
                        require_once APP_PATH . '/Models/HealthPackage.php';
                        
                        $pkgModel = new HealthPackage();
                        $pkgs = $pkgModel->getAllActive();
                        
                        // Tạo kết nối riêng để tính giá (Database đã được load trong public/index.php)
                        $database = new Database();
                        $conn = $database->getConnection();
                        
                        foreach ($pkgs as $p):
                            // Tính giá từ tổng dịch vụ
                            $query = "SELECT SUM(service_price) as total_price FROM package_services WHERE package_id = :package_id";
                            $stmt = $conn->prepare($query);
                            $stmt->bindParam(':package_id', $p['id']);
                            $stmt->execute();
                            $priceData = $stmt->fetch(PDO::FETCH_ASSOC);
                            $totalPrice = $priceData['total_price'] ?? 0;
                            
                            $genderReq = $p['gender_requirement'] ?? 'both';
                        ?>
                        <option value="<?= $p['id'] ?>" 
                                data-gender="<?= $genderReq ?>"
                                data-price="<?= $totalPrice ?>">
                            <?= htmlspecialchars($p['name']) ?> - <?= number_format($totalPrice) ?> VNĐ
                        </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <div id="packageInfoWalkin" class="hidden mt-4 p-4 bg-purple-50 rounded-lg">
                        <h4 class="font-semibold mb-2" id="packageNameWalkin"></h4>
                        <div id="packageServicesWalkin" class="text-sm"></div>
                        <div class="mt-3 pt-3 border-t">
                            <span class="font-bold text-purple-600" id="packagePriceWalkin">0 đ</span>
                        </div>
                    </div>
                </div>

                <!-- Ngày khám -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Ngày khám dự kiến <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="appointment_date" required min="<?= date('Y-m-d') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>

                <!-- Lý do -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lý do khám / Ghi chú</label>
                    <textarea name="reason" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
                </div>

                <!-- Actions -->
                <div class="flex space-x-4">
                    <button type="button" onclick="selectAppointmentType('regular')"
                            class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center">
                        Hủy
                    </button>
                    <button type="submit" 
                            class="flex-1 px-6 py-3 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                        <i class="fas fa-check mr-2"></i>Xác nhận đăng ký
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
    <!-- End packageSection -->
<?php endif; ?>

<script>
function selectAppointmentType(type) {
    const regularCard = document.getElementById('regularCard');
    const packageCard = document.getElementById('packageCard');
    const scheduleSection = document.getElementById('scheduleSection');
    const packageSection = document.getElementById('packageSection');
    const typeRegular = document.getElementById('typeRegular');
    const typePackage = document.getElementById('typePackage');
    
    // Reset borders
    regularCard.classList.remove('border-purple-500', 'bg-purple-50');
    packageCard.classList.remove('border-purple-500', 'bg-purple-50');
    regularCard.classList.add('border-gray-200');
    packageCard.classList.add('border-gray-200');
    
    if (type === 'regular') {
        // Check radio
        typeRegular.checked = true;
        
        // Highlight card
        regularCard.classList.remove('border-gray-200');
        regularCard.classList.add('border-purple-500', 'bg-purple-50');
        
        // Show schedule, hide package
        scheduleSection.classList.remove('hidden');
        packageSection.classList.add('hidden');
    } else {
        // Check radio
        typePackage.checked = true;
        
        // Highlight card
        packageCard.classList.remove('border-gray-200');
        packageCard.classList.add('border-purple-500', 'bg-purple-50');
        
        // Hide schedule, show package
        scheduleSection.classList.add('hidden');
        packageSection.classList.remove('hidden');
    }
}

// Load thông tin gói khám cho walk-in
async function loadPackageInfoWalkin(packageId) {
    const packageInfo = document.getElementById('packageInfoWalkin');
    const packageName = document.getElementById('packageNameWalkin');
    const packageServices = document.getElementById('packageServicesWalkin');
    const packagePrice = document.getElementById('packagePriceWalkin');
    
    if (!packageId) {
        packageInfo.classList.add('hidden');
        return;
    }
    
    try {
        const response = await fetch(`<?= APP_URL ?>/api/package-services/${packageId}`);
        const data = await response.json();
        
        if (data.success) {
            const selectedOption = document.querySelector(`#packageSelectWalkin option[value="${packageId}"]`);
            packageName.textContent = selectedOption.textContent.split(' - ')[0];
            
            packageServices.innerHTML = '';
            let totalPrice = 0;
            
            data.services.forEach(service => {
                const price = parseInt(service.service_price) || 0;
                totalPrice += price;
                
                const serviceDiv = document.createElement('div');
                serviceDiv.className = 'flex items-center mb-1';
                serviceDiv.innerHTML = `
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span>${service.service_name}</span>
                `;
                packageServices.appendChild(serviceDiv);
            });
            
            packagePrice.textContent = new Intl.NumberFormat('vi-VN').format(totalPrice) + ' đ';
            packageInfo.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Lỗi load gói khám:', error);
    }
}

// Toggle form bệnh nhân (cũ/mới) cho gói khám
function togglePatientFormPkg() {
    const patientType = document.querySelector('input[name="patient_type_pkg"]:checked').value;
    const existingForm = document.getElementById('existingPatientFormPkg');
    const newForm = document.getElementById('newPatientFormPkg');
    const patientSelect = document.getElementById('patientSelectPkg');
    
    if (patientType === 'existing') {
        existingForm.classList.remove('hidden');
        newForm.classList.add('hidden');
        patientSelect.required = true;
        
        // Bỏ required cho form mới
        document.getElementById('newPatientNamePkg').required = false;
        document.getElementById('newPatientDobPkg').required = false;
        document.getElementById('newPatientPhonePkg').required = false;
        document.getElementById('newPatientAddressPkg').required = false;
    } else {
        existingForm.classList.add('hidden');
        newForm.classList.remove('hidden');
        patientSelect.required = false;
        
        // Thêm required cho form mới
        document.getElementById('newPatientNamePkg').required = true;
        document.getElementById('newPatientDobPkg').required = true;
        document.getElementById('newPatientPhonePkg').required = true;
        document.getElementById('newPatientAddressPkg').required = true;
    }
}

// Lọc gói khám theo giới tính
function filterPackagesByGender(gender) {
    const packageSelect = document.getElementById('packageSelectWalkin');
    const options = packageSelect.querySelectorAll('option');
    
    options.forEach(option => {
        if (option.value === '') return; // Giữ option "-- Chọn gói khám --"
        
        const pkgGender = option.dataset.gender;
        
        // Hiển thị nếu:
        // - Gói dành cho cả 2 giới (both)
        // - Hoặc gói khớp với giới tính đã chọn
        if (pkgGender === 'both' || pkgGender === gender) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    });
    
    // Reset selection nếu option hiện tại bị ẩn
    const selectedOption = packageSelect.options[packageSelect.selectedIndex];
    if (selectedOption && selectedOption.style.display === 'none') {
        packageSelect.value = '';
        document.getElementById('packageInfoWalkin').classList.add('hidden');
    }
}

// Lắng nghe thay đổi giới tính (bệnh nhân mới)
document.addEventListener('DOMContentLoaded', function() {
    // Tự động chọn loại khám từ URL
    const urlParams = new URLSearchParams(window.location.search);
    const walkinType = urlParams.get('walkin_type') || 'regular';
    if (walkinType) {
        selectAppointmentType(walkinType);
    }
    
    // Chỉ reload khi có tham số refresh=1 và không có dữ liệu
    if (urlParams.get('refresh') === '1') {
        setTimeout(function() {
            // Kiểm tra xem có dữ liệu lịch hẹn không
            const scheduleTable = document.querySelector('.bg-white.rounded-lg.shadow-md.overflow-hidden');
            if (scheduleTable && !scheduleTable.querySelector('table')) {
                // Nếu không có bảng lịch, reload lại trang một lần (không còn refresh=1)
                const newUrl = window.location.pathname + window.location.search.replace(/[?&]refresh=1/g, '');
                window.location.href = newUrl;
            }
        }, 500);
    }
    
    const genderSelect = document.getElementById('newPatientGenderPkg');
    if (genderSelect) {
        genderSelect.addEventListener('change', function() {
            filterPackagesByGender(this.value);
        });
    }
    
    // Lắng nghe thay đổi bệnh nhân cũ
    const patientSelect = document.getElementById('patientSelectPkg');
    if (patientSelect) {
        patientSelect.addEventListener('change', function() {
            if (this.value) {
                // Lấy giới tính từ option đã chọn
                const selectedOption = this.options[this.selectedIndex];
                const patientData = selectedOption.textContent;
                
                // TODO: Fetch giới tính từ API hoặc lưu trong data attribute
                // Tạm thời không lọc cho bệnh nhân cũ
            }
        });
    }
});
</script>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
