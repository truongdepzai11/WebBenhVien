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
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-calendar-alt mr-2"></i>Lịch làm việc trong tuần
            </h3>
            
            <?php
            // Parse available_days và available_hours
            $daysMap = [
                'Thứ 2' => 'T2',
                'Thứ 3' => 'T3',
                'Thứ 4' => 'T4',
                'Thứ 5' => 'T5',
                'Thứ 6' => 'T6',
                'Thứ 7' => 'T7',
                'Chủ nhật' => 'CN'
            ];
            
            $availableDays = array_map('trim', explode(',', $doctor['available_days']));
            $availableHours = $doctor['available_hours'];
            
            // Tất cả các ngày trong tuần
            $weekDays = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
            $weekDaysFull = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật'];
            ?>
            
            <!-- Calendar Grid -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gradient-to-r from-purple-500 to-indigo-600">
                            <th class="border border-purple-400 p-3 text-white font-semibold text-sm">Ca làm</th>
                            <?php foreach ($weekDays as $day): ?>
                            <th class="border border-purple-400 p-3 text-white font-semibold text-sm min-w-[100px]">
                                <?= $day ?>
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
                            <?php foreach ($weekDaysFull as $dayFull): ?>
                            <td class="border border-gray-300 p-2">
                                <?php if (in_array($dayFull, $availableDays)): ?>
                                <div class="bg-green-100 border-l-4 border-green-500 p-3 rounded">
                                    <p class="text-xs text-gray-600 mb-1">
                                        <i class="fas fa-clock text-green-600 mr-1"></i>
                                        <?= htmlspecialchars($availableHours) ?>
                                    </p>
                                    <p class="text-xs font-semibold text-green-700">
                                        <i class="fas fa-user-md mr-1"></i>
                                        <?= htmlspecialchars($doctor['full_name']) ?>
                                    </p>
                                </div>
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
                            <?php foreach ($weekDaysFull as $dayFull): ?>
                            <td class="border border-gray-300 p-2">
                                <?php if (in_array($dayFull, $availableDays)): ?>
                                <div class="bg-blue-100 border-l-4 border-blue-500 p-3 rounded">
                                    <p class="text-xs text-gray-600 mb-1">
                                        <i class="fas fa-clock text-blue-600 mr-1"></i>
                                        <?= htmlspecialchars($availableHours) ?>
                                    </p>
                                    <p class="text-xs font-semibold text-blue-700">
                                        <i class="fas fa-user-md mr-1"></i>
                                        <?= htmlspecialchars($doctor['full_name']) ?>
                                    </p>
                                </div>
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
                            <?php foreach ($weekDaysFull as $dayFull): ?>
                            <td class="border border-gray-300 p-2 bg-gray-50">
                                <div class="text-center text-gray-400 py-4">
                                    <i class="fas fa-times-circle"></i>
                                </div>
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
                    <span class="text-gray-600">Ca sáng</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>
                    <span class="text-gray-600">Ca chiều</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-times-circle text-gray-400 mr-2"></i>
                    <span class="text-gray-600">Không làm việc</span>
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