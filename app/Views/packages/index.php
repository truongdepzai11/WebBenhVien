<?php 
$page_title = 'Gói khám sức khỏe';
ob_start(); 
?>

<!-- Hero Section (chỉ hiện khi chưa đăng nhập) -->
<?php if (!isset($_SESSION['user_id'])): ?>
<div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-16">
    <div class="container mx-auto px-4">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-4">
                <i class="fas fa-heartbeat mr-3"></i>Gói khám sức khỏe tổng quát
            </h1>
            <p class="text-xl text-purple-100">
                Chăm sóc sức khỏe toàn diện - Phát hiện sớm nguy cơ bệnh lý
            </p>
        </div>
    </div>
</div>
<?php else: ?>
<!-- Header cho user đã đăng nhập -->
<div class="mb-6">
    <h3 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-box-open mr-2"></i>Gói khám sức khỏe
    </h3>
    <p class="text-gray-600 mt-2">Chọn gói khám phù hợp với bạn</p>
</div>
<?php endif; ?>

<!-- Packages Grid -->
<div class="<?= isset($_SESSION['user_id']) ? '' : 'container mx-auto' ?> px-4 py-12">
    
    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-filter mr-2"></i>Lọc gói khám phù hợp
        </h3>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Giới tính</label>
                <select name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Tất cả</option>
                    <option value="male" <?= ($_GET['gender'] ?? '') == 'male' ? 'selected' : '' ?>>Nam</option>
                    <option value="female" <?= ($_GET['gender'] ?? '') == 'female' ? 'selected' : '' ?>>Nữ</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Độ tuổi</label>
                <input type="number" name="age" value="<?= $_GET['age'] ?? '' ?>" 
                       placeholder="Nhập tuổi" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full gradient-bg text-white py-2 px-6 rounded-lg hover:opacity-90">
                    <i class="fas fa-search mr-2"></i>Tìm kiếm
                </button>
            </div>
        </form>
    </div>

    <!-- Packages List -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <?php foreach ($packages as $package): ?>
        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
            <!-- Package Header -->
            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white p-6">
                <h3 class="text-2xl font-bold mb-2"><?= htmlspecialchars($package['name']) ?></h3>
                <p class="text-purple-100"><?= htmlspecialchars($package['description']) ?></p>
            </div>

            <!-- Package Body -->
            <div class="p-6">
                <!-- Price -->
                <div class="mb-6">
                    <?php
                    // Tính tổng giá từ dịch vụ
                    $totalPrice = 0;
                    if (isset($package['services'])) {
                        foreach ($package['services'] as $service) {
                            $totalPrice += $service['service_price'] ?? 0;
                        }
                    }
                    ?>
                    <div class="text-center">
                        <span class="text-3xl font-bold text-purple-600">
                            <?= number_format($totalPrice) ?> VNĐ
                        </span>
                        <p class="text-xs text-gray-500 mt-1">Tổng <?= count($package['services'] ?? []) ?> dịch vụ</p>
                    </div>
                </div>

                <!-- Age Range -->
                <div class="mb-4 text-sm text-gray-600">
                    <i class="fas fa-user-clock mr-2"></i>
                    Độ tuổi: <?= $package['min_age'] ?> - <?= $package['max_age'] ?> tuổi
                </div>

                <!-- Services Preview -->
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-800 mb-3">Danh mục khám:</h4>
                    <div class="space-y-2">
                        <?php 
                        $services = $package['services'] ?? [];
                        $displayServices = array_slice($services, 0, 5);
                        foreach ($displayServices as $service): 
                        ?>
                        <div class="flex items-start text-sm text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                            <span><?= htmlspecialchars($service['service_name']) ?></span>
                        </div>
                        <?php endforeach; ?>
                        
                        <?php if (count($services) > 5): ?>
                        <div class="text-sm text-purple-600 font-medium">
                            + <?= count($services) - 5 ?> dịch vụ khác...
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <a href="<?= APP_URL ?>/packages/<?= $package['id'] ?>" 
                       class="flex-1 text-center px-4 py-2 border border-purple-600 text-purple-600 rounded-lg hover:bg-purple-50 transition">
                        <i class="fas fa-info-circle mr-2"></i>Chi tiết
                    </a>
                    
                    <?php 
                    // Chỉ bệnh nhân mới được đặt lịch
                    $userRole = $_SESSION['role'] ?? null;
                    if ($userRole === 'patient' || !isset($_SESSION['user_id'])): 
                    ?>
                    <a href="<?= APP_URL ?>/appointments/create?package_id=<?= $package['id'] ?>" 
                       class="flex-1 text-center px-4 py-2 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                        <i class="fas fa-calendar-plus mr-2"></i>Đặt lịch
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($packages)): ?>
    <div class="text-center py-12">
        <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg">Không tìm thấy gói khám phù hợp</p>
    </div>
    <?php endif; ?>
</div>

<!-- Benefits Section (chỉ hiện khi chưa đăng nhập) -->
<?php if (!isset($_SESSION['user_id'])): ?>
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <h3 class="text-2xl font-bold text-center text-gray-800 mb-8">
            Lợi ích khi khám theo gói
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-dollar-sign text-3xl text-purple-600"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">Tiết kiệm chi phí</h4>
                <p class="text-sm text-gray-600">Giá ưu đãi hơn khám lẻ</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clipboard-check text-3xl text-blue-600"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">Toàn diện</h4>
                <p class="text-sm text-gray-600">Kiểm tra đầy đủ các chỉ số</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-md text-3xl text-green-600"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">Chuyên nghiệp</h4>
                <p class="text-sm text-gray-600">Đội ngũ bác sĩ giàu kinh nghiệm</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clock text-3xl text-red-600"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">Nhanh chóng</h4>
                <p class="text-sm text-gray-600">Kết quả trong ngày</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php 
$content = ob_get_clean();

// Nếu user đã đăng nhập thì dùng layout main, chưa thì dùng landing
if (isset($_SESSION['user_id'])) {
    require_once APP_PATH . '/Views/layouts/main.php';
} else {
    require_once APP_PATH . '/Views/layouts/landing.php';
}
?>
