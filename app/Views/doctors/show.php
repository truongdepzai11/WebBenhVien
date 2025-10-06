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
                <i class="fas fa-calendar-alt mr-2"></i>Lịch làm việc
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-purple-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Ngày làm việc</p>
                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($doctor['available_days']) ?></p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Giờ làm việc</p>
                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($doctor['available_hours']) ?></p>
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