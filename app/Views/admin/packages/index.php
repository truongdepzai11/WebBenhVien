<?php 
$page_title = 'Quản lý Gói khám';
ob_start(); 
?>

<div class="mb-6 flex items-center justify-between">
    <h3 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-box-open mr-2"></i>Quản lý Gói khám sức khỏe
    </h3>
    <a href="<?= APP_URL ?>/admin/packages/create" 
       class="px-6 py-2 gradient-bg text-white rounded-lg hover:opacity-90 transition">
        <i class="fas fa-plus mr-2"></i>Thêm gói khám
    </a>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <?php
    $totalPackages = count($packages);
    $activePackages = count(array_filter($packages, fn($p) => $p['is_active']));
    $totalServices = array_sum(array_column($packages, 'service_count'));
    $totalAppointments = array_sum(array_column($packages, 'appointment_count'));
    ?>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Tổng gói khám</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalPackages ?></p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-box-open text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Đang hoạt động</p>
                <p class="text-2xl font-bold text-green-600"><?= $activePackages ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Tổng dịch vụ</p>
                <p class="text-2xl font-bold text-blue-600"><?= $totalServices ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-list-check text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Lượt đặt khám</p>
                <p class="text-2xl font-bold text-orange-600"><?= $totalAppointments ?></p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                <i class="fas fa-calendar-check text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Packages Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (!empty($packages)): ?>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Gói khám
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Giá
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Độ tuổi
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Dịch vụ
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Lượt đặt
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Trạng thái
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Thao tác
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($packages as $package): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-box-open text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                <?= htmlspecialchars($package['name']) ?>
                            </div>
                            <div class="text-sm text-gray-500">
                                <?= htmlspecialchars($package['package_code']) ?>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm">
                    <?php
                    // Tính tổng giá từ dịch vụ
                    $totalPrice = 0;
                    if (isset($package['services'])) {
                        foreach ($package['services'] as $service) {
                            $totalPrice += $service['service_price'] ?? 0;
                        }
                    }
                    ?>
                    <div class="text-purple-600 font-bold">
                        <?= number_format($totalPrice) ?>đ
                    </div>
                    <div class="text-xs text-gray-500"><?= $package['service_count'] ?? 0 ?> dịch vụ</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    <?= $package['min_age'] ?> - <?= $package['max_age'] ?> tuổi
                </td>
                <td class="px-6 py-4 text-sm">
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                        <?= $package['service_count'] ?> dịch vụ
                    </span>
                </td>
                <td class="px-6 py-4 text-sm">
                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                        <?= $package['appointment_count'] ?> lượt
                    </span>
                </td>
                <td class="px-6 py-4 text-sm">
                    <form action="<?= APP_URL ?>/admin/packages/<?= $package['id'] ?>/toggle-status" method="POST" class="inline">
                        <button type="submit" class="focus:outline-none">
                            <?php if ($package['is_active']): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold cursor-pointer hover:bg-green-200">
                                    <i class="fas fa-check-circle mr-1"></i>Hoạt động
                                </span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold cursor-pointer hover:bg-gray-200">
                                    <i class="fas fa-times-circle mr-1"></i>Tạm dừng
                                </span>
                            <?php endif; ?>
                        </button>
                    </form>
                </td>
                <td class="px-6 py-4 text-sm">
                    <div class="flex items-center space-x-2">
                        <a href="<?= APP_URL ?>/packages/<?= $package['id'] ?>" 
                           class="text-purple-600 hover:text-purple-900" title="Xem chi tiết" target="_blank">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="<?= APP_URL ?>/admin/packages/<?= $package['id'] ?>/services" 
                           class="text-green-600 hover:text-green-900" title="Quản lý dịch vụ">
                            <i class="fas fa-list-check"></i>
                        </a>
                        <a href="<?= APP_URL ?>/admin/packages/<?= $package['id'] ?>/edit" 
                           class="text-blue-600 hover:text-blue-900" title="Sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="<?= APP_URL ?>/admin/packages/<?= $package['id'] ?>/delete" method="POST" 
                              class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa gói khám này? Tất cả dịch vụ liên quan sẽ bị xóa!')">
                            <button type="submit" class="text-red-600 hover:text-red-900" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="p-12 text-center">
        <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg mb-4">Chưa có gói khám nào</p>
        <a href="<?= APP_URL ?>/admin/packages/create" 
           class="inline-flex items-center px-6 py-3 gradient-bg text-white rounded-lg hover:opacity-90 transition">
            <i class="fas fa-plus mr-2"></i>Thêm gói khám đầu tiên
        </a>
    </div>
    <?php endif; ?>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
