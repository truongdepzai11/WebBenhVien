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

<?php 
// Receptionist luôn xem daily view (walk-in form)
if (Auth::isReceptionist()) {
    $viewMode = 'day';
} else {
    $viewMode = $_GET['view'] ?? 'week';
}
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
        <label class="text-sm font-medium text-gray-700">Chọn bác sĩ:</label>
        <select name="doctor_id" onchange="this.form.submit()" 
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            <?php foreach ($doctors as $doc): ?>
            <option value="<?= $doc['id'] ?>" <?= $doc['id'] == $selectedDoctorId ? 'selected' : '' ?>>
                <?= htmlspecialchars($doc['full_name']) ?> - <?= htmlspecialchars($doc['specialization']) ?>
            </option>
            <?php endforeach; ?>
        </select>
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
        </div>
        <?php endif; ?>

        <!-- Chọn ngày -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Ngày</label>
            <input type="date" name="date" value="<?= $selectedDate ?>" onchange="this.form.submit()"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>

        <!-- Nút hôm nay -->
        <div class="flex items-end">
            <button type="button" onclick="window.location.href='<?= APP_URL ?>/schedule?doctor_id=<?= $selectedDoctorId ?>&date=<?= date('Y-m-d') ?>'"
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

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
