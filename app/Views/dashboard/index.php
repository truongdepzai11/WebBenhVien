<?php 
$page_title = 'Dashboard';
ob_start(); 
?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php if (Auth::isAdmin()): ?>
        <div class="bg-white rounded-lg shadow-md p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Tổng bệnh nhân</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?= $stats['total_patients'] ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Tổng bác sĩ</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?= $stats['total_doctors'] ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-md text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Tổng lịch hẹn</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?= $stats['total_appointments'] ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-check text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Hồ sơ bệnh án</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?= $stats['total_records'] ?></p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-file-medical text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-md p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Tổng lịch hẹn</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?= $stats['total_appointments'] ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Đang chờ</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?= $stats['pending_appointments'] ?></p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Đã hoàn thành</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?= $stats['completed_appointments'] ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium"><?= Auth::isDoctor() ? 'Bệnh nhân' : 'Hồ sơ' ?></p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?= Auth::isDoctor() ? $stats['total_patients'] : $stats['total_records'] ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-file-medical text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Recent Appointments -->
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-gray-800">
            <i class="fas fa-calendar-alt mr-2"></i>Lịch hẹn gần đây
        </h3>
        <a href="<?= APP_URL ?>/appointments" class="text-purple-600 hover:text-purple-700 font-semibold text-sm">
            Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    <?php if (empty($recent_appointments)): ?>
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-calendar-times text-4xl mb-3"></i>
            <p>Chưa có lịch hẹn nào</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã</th>
                        <?php if (!Auth::isPatient()): ?>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bệnh nhân</th>
                        <?php endif; ?>
                        <?php if (!Auth::isDoctor()): ?>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bác sĩ</th>
                        <?php endif; ?>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày khám</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giờ</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($recent_appointments as $apt): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900"><?= htmlspecialchars($apt['appointment_code']) ?></td>
                        <?php if (!Auth::isPatient()): ?>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($apt['patient_name']) ?></td>
                        <?php endif; ?>
                        <?php if (!Auth::isDoctor()): ?>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($apt['doctor_name']) ?></td>
                        <?php endif; ?>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= date('d/m/Y', strtotime($apt['appointment_date'])) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700"><?= date('H:i', strtotime($apt['appointment_time'])) ?></td>
                        <td class="px-4 py-3 text-sm">
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
                            <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $statusColors[$apt['status']] ?>">
                                <?= $statusLabels[$apt['status']] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Quick Actions -->
<?php if (Auth::isPatient()): ?>
<div class="mt-6">
    <a href="<?= APP_URL ?>/appointments/create" 
       class="inline-flex items-center px-6 py-3 gradient-bg text-white font-semibold rounded-lg hover:opacity-90 transition">
        <i class="fas fa-plus mr-2"></i>
        Đặt lịch khám mới
    </a>
</div>
<?php endif; ?>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
