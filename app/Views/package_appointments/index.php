<?php 
$page_title = 'Quản lý Gói khám';
ob_start(); 
?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-box-open mr-2"></i>Quản lý Gói khám
        </h3>
        <p class="text-gray-600 mt-1">Danh sách đăng ký gói khám sức khỏe</p>
    </div>
    
    <?php if (Auth::isReceptionist()): ?>
    <a href="<?= APP_URL ?>/schedule" 
       class="px-6 py-3 gradient-bg text-white rounded-lg hover:opacity-90 transition">
        <i class="fas fa-plus mr-2"></i>Đăng ký gói mới
    </a>
    <?php endif; ?>
</div>

<!-- Thống kê tổng quan -->
<?php if (!Auth::isPatient()): ?>
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Tổng đăng ký</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">
                    <?= count($packageAppointments) ?>
                </p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Chờ phân công</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">
                    <?= count(array_filter($packageAppointments, fn($p) => $p['status'] == 'scheduled')) ?>
                </p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Đang thực hiện</p>
                <p class="text-2xl font-bold text-purple-600 mt-1">
                    <?= count(array_filter($packageAppointments, fn($p) => $p['status'] == 'in_progress')) ?>
                </p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-spinner text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Hoàn thành</p>
                <p class="text-2xl font-bold text-green-600 mt-1">
                    <?= count(array_filter($packageAppointments, fn($p) => $p['status'] == 'completed')) ?>
                </p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Danh sách đăng ký -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (empty($packageAppointments)): ?>
    <div class="p-12 text-center">
        <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg">Chưa có đăng ký gói khám nào</p>
        <?php if (Auth::isReceptionist()): ?>
        <a href="<?= APP_URL ?>/schedule" 
           class="inline-block mt-4 px-6 py-3 gradient-bg text-white rounded-lg hover:opacity-90">
            Đăng ký gói khám mới
        </a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Mã ĐK
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Bệnh nhân
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Gói khám
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ngày khám
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Trạng thái
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ngày đăng ký
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Thao tác
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($packageAppointments as $pa): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-900">
                            #<?= $pa['id'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-purple-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($pa['patient_name']) ?>
                                </p>
                                <p class="text-sm text-gray-500">
                                    <?= htmlspecialchars($pa['patient_code']) ?>
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 font-medium">
                            <?= htmlspecialchars($pa['package_name']) ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            <i class="fas fa-calendar mr-1 text-gray-400"></i>
                            <?= date('d/m/Y', strtotime($pa['appointment_date'])) ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php
                        $as = (int)($pa['assigned_count'] ?? 0);
                        $ts = (int)($pa['total_services'] ?? 0);
                        
                        if ($ts > 0) {
                            if ($as === 0) {
                                $colorClass = 'bg-yellow-100 text-yellow-800';
                                $label = 'Chưa phân công';
                            } elseif ($as < $ts) {
                                $colorClass = 'bg-orange-100 text-orange-800';
                                $label = "Đã phân công {$as}/{$ts} dịch vụ";
                            } else {
                                $colorClass = 'bg-green-100 text-green-800';
                                $label = "Đã phân công đầy đủ {$as}/{$ts} dịch vụ";
                            }
                        } else {
                            $colorClass = 'bg-gray-100 text-gray-800';
                            $label = 'Không có dịch vụ';
                        }
                        ?>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $colorClass ?>">
                            <?= $label ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= date('d/m/Y H:i', strtotime($pa['created_at'])) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="<?= APP_URL ?>/package-appointments/<?= $pa['id'] ?>" 
                           class="text-purple-600 hover:text-purple-900 mr-3"
                           title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </a>
                        
                        <?php if (Auth::isAdmin() || Auth::isReceptionist()): ?>
                            <?php if ($pa['status'] == 'scheduled'): ?>
                            <form action="<?= APP_URL ?>/package-appointments/<?= $pa['id'] ?>/cancel" 
                                  method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('Bạn có chắc muốn hủy đăng ký này?')">
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900"
                                        title="Hủy đăng ký">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        <?php endif; ?>
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
