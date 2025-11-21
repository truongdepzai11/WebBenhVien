<?php 
$page_title = 'Quản trị hệ thống';
ob_start(); 
?>

<div class="mb-6">
    <h3 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-cogs mr-2"></i>Quản trị hệ thống
    </h3>
    <p class="text-gray-600 mt-2">Quản lý toàn bộ hệ thống bệnh viện</p>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium">Tổng người dùng</p>
                <p class="text-3xl font-bold mt-2"><?= $stats['total_users'] ?></p>
            </div>
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">Tổng bác sĩ</p>
                <p class="text-3xl font-bold mt-2"><?= $stats['total_doctors'] ?></p>
            </div>
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-user-md text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium">Tổng bệnh nhân</p>
                <p class="text-3xl font-bold mt-2"><?= $stats['total_patients'] ?></p>
            </div>
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-hospital-user text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm font-medium">Chuyên khoa</p>
                <p class="text-3xl font-bold mt-2"><?= $stats['total_specializations'] ?></p>
            </div>
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-stethoscope text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <a href="<?= APP_URL ?>/admin/doctors" class="bg-white rounded-lg shadow-md p-6 hover:shadow-xl transition">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user-md text-green-600 text-xl"></i>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800">Quản lý Bác sĩ</h4>
                <p class="text-sm text-gray-600">Thêm, sửa, xóa bác sĩ</p>
            </div>
        </div>
    </a>
    
    <a href="<?= APP_URL ?>/admin/specializations" class="bg-white rounded-lg shadow-md p-6 hover:shadow-xl transition">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-stethoscope text-blue-600 text-xl"></i>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800">Quản lý Chuyên khoa</h4>
                <p class="text-sm text-gray-600">Thêm, sửa, xóa chuyên khoa</p>
            </div>
        </div>
    </a>

    <a href="<?= APP_URL ?>/admin/users" class="bg-white rounded-lg shadow-md p-6 hover:shadow-xl transition">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-purple-600 text-xl"></i>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800">Quản lý Users</h4>
                <p class="text-sm text-gray-600">Xem danh sách người dùng</p>
            </div>
        </div>
    </a>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
