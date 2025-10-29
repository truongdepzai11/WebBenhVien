<?php 
$page_title = $package['name'];
ob_start(); 

// Mapping category names
$categoryNames = [
    'general' => 'Khám tổng quát',
    'blood_test' => 'Xét nghiệm máu',
    'urine_test' => 'Xét nghiệm nước tiểu',
    'imaging' => 'Chẩn đoán hình ảnh',
    'specialist' => 'Khám chuyên khoa',
    'other' => 'Khác'
];

$categoryIcons = [
    'general' => 'fa-stethoscope',
    'blood_test' => 'fa-vial',
    'urine_test' => 'fa-flask',
    'imaging' => 'fa-x-ray',
    'specialist' => 'fa-user-md',
    'other' => 'fa-notes-medical'
];
?>

<!-- Breadcrumb (chỉ hiện khi chưa đăng nhập) -->
<?php if (!isset($_SESSION['user_id'])): ?>
<div class="bg-gray-100 py-4">
    <div class="container mx-auto px-4">
        <nav class="text-sm">
            <a href="<?= APP_URL ?>/" class="text-purple-600 hover:text-purple-800">Trang chủ</a>
            <span class="mx-2 text-gray-500">/</span>
            <a href="<?= APP_URL ?>/packages" class="text-purple-600 hover:text-purple-800">Gói khám</a>
            <span class="mx-2 text-gray-500">/</span>
            <span class="text-gray-700"><?= htmlspecialchars($package['name']) ?></span>
        </nav>
    </div>
</div>
<?php endif; ?>

<!-- Package Detail -->
<div class="<?= isset($_SESSION['user_id']) ? '' : 'container mx-auto' ?> px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Package Header -->
            <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-4">
                    <?= htmlspecialchars($package['name']) ?>
                </h1>
                
                <?php if ($package['description']): ?>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    <?= nl2br(htmlspecialchars($package['description'])) ?>
                </p>
                <?php endif; ?>

                <!-- Package Info -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="text-sm text-gray-600 mb-1">Mã gói khám</div>
                        <div class="font-semibold text-gray-800"><?= htmlspecialchars($package['package_code']) ?></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="text-sm text-gray-600 mb-1">Độ tuổi phù hợp</div>
                        <div class="font-semibold text-gray-800"><?= $package['min_age'] ?> - <?= $package['max_age'] ?> tuổi</div>
                    </div>
                </div>

                <!-- Price (tính từ dịch vụ) -->
                <div class="border-t pt-6">
                    <?php
                    // Tính tổng giá từ dịch vụ
                    $totalPrice = 0;
                    $requiredPrice = 0;
                    if (isset($services)) {
                        foreach ($services as $service) {
                            $price = $service['service_price'] ?? 0;
                            $totalPrice += $price;
                            if ($service['is_required']) {
                                $requiredPrice += $price;
                            }
                        }
                    }
                    ?>
                    <div class="text-center p-8 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg border-2 border-purple-200">
                        <div class="text-lg text-gray-600 mb-3">
                            <?php if ($package['gender_requirement'] == 'male'): ?>
                                <i class="fas fa-male text-blue-600 mr-2"></i>Gói dành cho Nam giới
                            <?php elseif ($package['gender_requirement'] == 'female'): ?>
                                <i class="fas fa-female text-pink-600 mr-2"></i>Gói dành cho Nữ giới
                            <?php else: ?>
                                <i class="fas fa-users text-purple-600 mr-2"></i>Gói dành cho cả Nam và Nữ
                            <?php endif; ?>
                        </div>
                        <div class="text-5xl font-bold text-purple-600 mb-2">
                            <?= number_format($totalPrice) ?> đ
                        </div>
                        <div class="text-sm text-gray-500">
                            Tổng <?= count($services ?? []) ?> dịch vụ
                            <?php if ($requiredPrice > 0): ?>
                                • Bắt buộc: <?= number_format($requiredPrice) ?> đ
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services List -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-list-check mr-2 text-purple-600"></i>
                    Danh mục dịch vụ khám
                </h2>

                <?php foreach ($servicesByCategory as $category => $services): ?>
                <div class="mb-8 last:mb-0">
                    <div class="flex items-center mb-4 pb-2 border-b-2 border-purple-200">
                        <i class="fas <?= $categoryIcons[$category] ?? 'fa-notes-medical' ?> text-2xl text-purple-600 mr-3"></i>
                        <h3 class="text-xl font-bold text-gray-800">
                            <?= $categoryNames[$category] ?? 'Khác' ?>
                        </h3>
                        <span class="ml-auto bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-semibold">
                            <?= count($services) ?> dịch vụ
                        </span>
                    </div>

                    <div class="space-y-3">
                        <?php foreach ($services as $service): ?>
                        <div class="flex items-start p-3 hover:bg-gray-50 rounded-lg transition">
                            <i class="fas fa-check-circle text-green-500 mr-3 mt-1"></i>
                            <div class="flex-1">
                                <div class="font-medium text-gray-800">
                                    <?= htmlspecialchars($service['service_name']) ?>
                                </div>
                                <?php if ($service['notes']): ?>
                                <div class="text-sm text-gray-500 mt-1">
                                    <?= htmlspecialchars($service['notes']) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($service['is_required']): ?>
                            <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded">Bắt buộc</span>
                            <?php endif; ?>
                            <?php if ($service['gender_specific'] != 'both'): ?>
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded ml-2">
                                <?= $service['gender_specific'] == 'male' ? 'Nam' : 'Nữ' ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Booking Card -->
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-4">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    Đặt lịch khám
                </h3>

                <div class="mb-6">
                    <div class="text-sm text-gray-600 mb-2">Giá gói khám:</div>
                    <div class="text-2xl font-bold text-purple-600">
                        <?= number_format($totalPrice) ?> đ
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        <?= count($services ?? []) ?> dịch vụ
                    </div>
                </div>

                <a href="<?= APP_URL ?>/appointments/create?package_id=<?= $package['id'] ?>" 
                   class="block w-full text-center gradient-bg text-white py-3 px-6 rounded-lg hover:opacity-90 transition mb-3">
                    <i class="fas fa-calendar-plus mr-2"></i>Đặt lịch ngay
                </a>

                <a href="<?= APP_URL ?>/packages" 
                   class="block w-full text-center border border-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Xem gói khác
                </a>

                <!-- Contact Info -->
                <div class="mt-6 pt-6 border-t">
                    <h4 class="font-semibold text-gray-800 mb-3">Cần tư vấn?</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-phone text-purple-600 mr-2"></i>
                            <span>0973436483</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-envelope text-purple-600 mr-2"></i>
                            <span>support@hospital.com</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Benefits -->
            <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg p-6 mt-6">
                <h4 class="font-bold text-gray-800 mb-4">
                    <i class="fas fa-star text-yellow-500 mr-2"></i>
                    Ưu điểm nổi bật
                </h4>
                <ul class="space-y-3 text-sm text-gray-700">
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Tiết kiệm chi phí so với khám lẻ</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Kiểm tra toàn diện sức khỏe</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Phát hiện sớm nguy cơ bệnh lý</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Kết quả nhanh chóng, chính xác</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        <span>Đội ngũ bác sĩ chuyên môn cao</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();

// Nếu user đã đăng nhập thì dùng layout main, chưa thì dùng landing
if (isset($_SESSION['user_id'])) {
    require_once APP_PATH . '/Views/layouts/main.php';
} else {
    require_once APP_PATH . '/Views/layouts/landing.php';
}
?>
