<?php 
$page_title = $doctor['full_name'];
ob_start(); 
?>

<div class="mb-6">
    <a href="<?= APP_URL ?>/doctors" class="text-purple-600 hover:text-purple-700 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách
    </a>
</div>

<!-- Doctor Profile Card -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <div class="gradient-bg p-8 text-white">
        <div class="flex items-center space-x-6">
            <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center">
                <i class="fas fa-user-md text-6xl text-purple-600"></i>
            </div>
            <div class="flex-1">
                <h1 class="text-4xl font-bold mb-2"><?= htmlspecialchars($doctor['full_name']) ?></h1>
                <p class="text-xl text-purple-100 mb-2"><?= htmlspecialchars($doctor['specialization']) ?></p>
                <p class="text-purple-200">
                    <i class="fas fa-id-badge mr-2"></i><?= htmlspecialchars($doctor['doctor_code']) ?>
                </p>
            </div>
        </div>
    </div>

    <div class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Thông tin liên hệ -->
            <div>
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-address-card mr-2"></i>Thông tin liên hệ
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-purple-600 w-6 mr-3"></i>
                        <span class="text-gray-700"><?= htmlspecialchars($doctor['email']) ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-phone text-purple-600 w-6 mr-3"></i>
                        <span class="text-gray-700"><?= htmlspecialchars($doctor['phone']) ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-certificate text-purple-600 w-6 mr-3"></i>
                        <span class="text-gray-700">Giấy phép: <?= htmlspecialchars($doctor['license_number']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Thông tin chuyên môn -->
            <div>
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-graduation-cap mr-2"></i>Thông tin chuyên môn
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-user-graduate text-purple-600 w-6 mr-3"></i>
                        <span class="text-gray-700"><?= htmlspecialchars($doctor['qualification']) ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-award text-purple-600 w-6 mr-3"></i>
                        <span class="text-gray-700">Kinh nghiệm: <?= $doctor['experience_years'] ?> năm</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-dollar-sign text-purple-600 w-6 mr-3"></i>
                        <span class="text-gray-700">Phí khám: <?= number_format($doctor['consultation_fee']) ?> VNĐ</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lịch làm việc -->
        <div class="mt-6 pt-6 border-t">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-calendar-alt mr-2"></i>Lịch làm việc trong tuần
                </h3>
                
                <!-- Week Navigation -->
                <?php
                $currentWeek = isset($_GET['week']) ? $_GET['week'] : date('Y-\WW');
                $weekStart = new DateTime();
                $weekStart->setISODate(substr($currentWeek, 0, 4), substr($currentWeek, 6, 2));
                $weekDays = [];
                for ($i = 0; $i < 7; $i++) {
                    $weekDays[] = clone $weekStart;
                    $weekStart->modify('+1 day');
                }
                ?>
                
                <div class="flex items-center space-x-2">
                    <a href="?week=<?= date('Y-\WW', strtotime('-1 week', strtotime($currentWeek))) ?>" 
                       class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 transition">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <a href="<?= APP_URL ?>/doctors/<?= $doctor['id'] ?>" 
                       class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 transition text-sm">
                        Tuần này
                    </a>
                    <a href="?week=<?= date('Y-\WW', strtotime('+1 week', strtotime($currentWeek))) ?>" 
                       class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 transition">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="text-sm text-gray-600 mb-4">
                Tuần <?= substr($currentWeek, 6, 2) ?>, <?= substr($currentWeek, 0, 4) ?>
            </div>
            
            <!-- Calendar Grid -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gradient-to-r from-purple-500 to-indigo-600">
                            <th class="border border-purple-400 p-3 text-white font-semibold text-sm">Ca làm</th>
                            <?php foreach ($weekDays as $day): ?>
                            <th class="border border-purple-400 p-3 text-white font-semibold text-sm min-w-[100px]">
                                <div><?= $day->format('l') == 'Monday' ? 'T2' : ($day->format('l') == 'Tuesday' ? 'T3' : ($day->format('l') == 'Wednesday' ? 'T4' : ($day->format('l') == 'Thursday' ? 'T5' : ($day->format('l') == 'Friday' ? 'T6' : ($day->format('l') == 'Saturday' ? 'T7' : 'CN'))))) ?></div>
                                <div class="text-xs text-purple-100"><?= $day->format('d/m') ?></div>
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
            
            <!-- Legend -->
            <div class="mt-4 flex flex-wrap gap-4 text-sm">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                    <span class="text-gray-600">Ca sáng (8h-12h)</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>
                    <span class="text-gray-600">Ca chiều (12h-18h)</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-indigo-500 rounded mr-2"></div>
                    <span class="text-gray-600">Ca tối (18h+)</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-times-circle text-gray-400 mr-2"></i>
                    <span class="text-gray-600">Không có lịch</span>
                </div>
            </div>
        </div>

        <?php if (!empty($doctor['bio'])): ?>
        <!-- Giới thiệu -->
        <div class="mt-6 pt-6 border-t">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-info-circle mr-2"></i>Giới thiệu
            </h3>
            <p class="text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($doctor['bio'])) ?></p>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <?php if (Auth::isPatient()): ?>
        <div class="mt-8 pt-6 border-t">
            <a href="<?= APP_URL ?>/appointments/create?doctor_id=<?= $doctor['id'] ?>" 
               class="inline-block px-8 py-3 gradient-bg text-white rounded-lg hover:opacity-90 transition text-lg">
                <i class="fas fa-calendar-plus mr-2"></i>Đặt lịch khám
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>