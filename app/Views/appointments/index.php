<?php 
$page_title = 'Quản lý Lịch hẹn';
ob_start(); 
?>

<div class="mb-6 flex items-center justify-between">
    <h3 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-calendar-check mr-2"></i>Quản lý Lịch hẹn
    </h3>
    <?php if (Auth::isPatient()): ?>
    <a href="<?= APP_URL ?>/appointments/create" 
       class="px-6 py-2 gradient-bg text-white rounded-lg hover:opacity-90 transition">
        <i class="fas fa-plus mr-2"></i>Đặt lịch mới
    </a>
    <?php endif; ?>
</div>

<!-- Appointments Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (empty($appointments)): ?>
        <div class="p-12 text-center">
            <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg mb-4">Chưa có lịch hẹn nào</p>
            <?php if (Auth::isPatient()): ?>
            <a href="<?= APP_URL ?>/appointments/create" 
               class="inline-flex items-center px-6 py-3 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                <i class="fas fa-plus mr-2"></i>Đặt lịch khám ngay
            </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã lịch</th>
                        <?php if (!Auth::isPatient()): ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bệnh nhân</th>
                        <?php endif; ?>
                        <?php if (!Auth::isDoctor()): ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bác sĩ</th>
                        <?php endif; ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày khám</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giờ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lý do</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($appointments as $apt): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="<?= APP_URL ?>/appointments/<?= $apt['id'] ?>" 
                               class="text-sm font-medium text-purple-600 hover:text-purple-900">
                                <?= htmlspecialchars($apt['appointment_code']) ?>
                            </a>
                        </td>
                        <?php if (!Auth::isPatient()): ?>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= htmlspecialchars($apt['patient_name']) ?></div>
                            <div class="text-xs text-gray-500"><?= htmlspecialchars($apt['patient_code']) ?></div>
                        </td>
                        <?php endif; ?>
                        <?php if (!Auth::isDoctor()): ?>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= htmlspecialchars($apt['doctor_name']) ?></div>
                            <div class="text-xs text-gray-500"><?= htmlspecialchars($apt['specialization']) ?></div>
                        </td>
                        <?php endif; ?>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <?= date('d/m/Y', strtotime($apt['appointment_date'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <?= date('H:i', strtotime($apt['appointment_time'])) ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-700 max-w-xs truncate"><?= htmlspecialchars($apt['reason']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'confirmed' => 'bg-blue-100 text-blue-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-gray-100 text-gray-800',
                                'late_cancelled' => 'bg-orange-100 text-orange-800',
                                'no_show' => 'bg-red-100 text-red-800'
                            ];
                            $statusLabels = [
                                'pending' => 'Chờ xác nhận',
                                'confirmed' => 'Đã xác nhận',
                                'completed' => 'Hoàn thành',
                                'cancelled' => 'Đã hủy',
                                'late_cancelled' => 'Hủy muộn',
                                'no_show' => 'Vắng mặt'
                            ];
                            ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusColors[$apt['status']] ?>">
                                <?= $statusLabels[$apt['status']] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-2">
                                <?php if ($apt['status'] === 'pending' && (Auth::isDoctor() || Auth::isAdmin())): ?>
                                <form action="<?= APP_URL ?>/appointments/<?= $apt['id'] ?>/update-status" method="POST" class="inline">
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="text-blue-600 hover:text-blue-900" title="Xác nhận">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                                
                                <?php if ($apt['status'] === 'confirmed' && (Auth::isDoctor() || Auth::isAdmin())): ?>
                                <form action="<?= APP_URL ?>/appointments/<?= $apt['id'] ?>/update-status" method="POST" class="inline">
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="text-green-600 hover:text-green-900" title="Hoàn thành">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                </form>
                                <a href="<?= APP_URL ?>/appointments/<?= $apt['id'] ?>/no-show" 
                                   onclick="return confirm('Xác nhận bệnh nhân không đến khám? Sẽ tạo hóa đơn phạt 100% phí khám.')"
                                   class="text-red-600 hover:text-red-900" title="Vắng mặt">
                                    <i class="fas fa-user-times"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (in_array($apt['status'], ['pending', 'confirmed'])): ?>
                                <a href="<?= APP_URL ?>/appointments/<?= $apt['id'] ?>/cancel" 
                                   class="text-orange-600 hover:text-orange-900" title="Hủy lịch">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
