<?php 
$page_title = 'Lịch làm việc Bác sĩ';
ob_start(); 
?>

<div class="mb-6">
    <h3 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-calendar-alt mr-2"></i>Lịch làm việc Bác sĩ
    </h3>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" action="<?= APP_URL ?>/schedule" class="grid grid-cols-1 md:grid-cols-<?= (Auth::isAdmin() || Auth::isReceptionist()) ? '3' : '2' ?> gap-4">
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
<div class="bg-white rounded-lg shadow-md p-12 text-center">
    <i class="fas fa-user-md text-6xl text-gray-300 mb-4"></i>
    <p class="text-gray-500 text-lg">Chọn bác sĩ để xem lịch làm việc</p>
</div>
<?php endif; ?>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
