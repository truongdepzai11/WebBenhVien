<?php 
$page_title = $patient['full_name'];
ob_start(); 
?>

<div class="mb-6">
    <a href="<?= APP_URL ?>/patients" class="text-purple-600 hover:text-purple-700 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách
    </a>
</div>

<!-- Patient Profile Card -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <div class="gradient-bg p-8 text-white">
        <div class="flex items-center space-x-6">
            <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center">
                <i class="fas fa-user text-6xl text-purple-600"></i>
            </div>
            <div class="flex-1">
                <h1 class="text-4xl font-bold mb-2"><?= htmlspecialchars($patient['full_name']) ?></h1>
                <p class="text-xl text-purple-100 mb-2">Mã bệnh nhân: <?= htmlspecialchars($patient['patient_code']) ?></p>
                <div class="flex items-center space-x-4 text-purple-200">
                    <span>
                        <i class="fas fa-birthday-cake mr-2"></i>
                        <?php 
                        if ($patient['date_of_birth']) {
                            $dob = new DateTime($patient['date_of_birth']);
                            $now = new DateTime();
                            $age = $now->diff($dob)->y;
                            echo date('d/m/Y', strtotime($patient['date_of_birth'])) . ' (' . $age . ' tuổi)';
                        } else {
                            echo 'Chưa cập nhật';
                        }
                        ?>
                    </span>
                    <span>
                        <i class="fas fa-venus-mars mr-2"></i>
                        <?php
                        $genderLabels = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                        echo $genderLabels[$patient['gender']] ?? 'Chưa cập nhật';
                        ?>
                    </span>
                </div>
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
                        <span class="text-gray-700"><?= htmlspecialchars($patient['email']) ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-phone text-purple-600 w-6 mr-3"></i>
                        <span class="text-gray-700"><?= htmlspecialchars($patient['phone']) ?></span>
                    </div>
                    <?php if (!empty($patient['address'])): ?>
                    <div class="flex items-start">
                        <i class="fas fa-map-marker-alt text-purple-600 w-6 mr-3 mt-1"></i>
                        <span class="text-gray-700"><?= htmlspecialchars($patient['address']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Thông tin y tế -->
            <div>
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-heartbeat mr-2"></i>Thông tin y tế
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-tint text-purple-600 w-6 mr-3"></i>
                        <span class="text-gray-700">Nhóm máu: <strong><?= htmlspecialchars($patient['blood_type'] ?? 'Chưa cập nhật') ?></strong></span>
                    </div>
                    <?php if (!empty($patient['allergies'])): ?>
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-red-600 w-6 mr-3 mt-1"></i>
                        <div>
                            <p class="text-sm text-gray-500">Dị ứng</p>
                            <p class="text-gray-700"><?= nl2br(htmlspecialchars($patient['allergies'])) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($patient['medical_history'])): ?>
                    <div class="flex items-start">
                        <i class="fas fa-file-medical text-purple-600 w-6 mr-3 mt-1"></i>
                        <div>
                            <p class="text-sm text-gray-500">Tiền sử bệnh</p>
                            <p class="text-gray-700"><?= nl2br(htmlspecialchars($patient['medical_history'])) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Liên hệ khẩn cấp -->
        <?php if (!empty($patient['emergency_contact'])): ?>
        <div class="mt-6 pt-6 border-t">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-phone-alt mr-2"></i>Liên hệ khẩn cấp
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-red-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Người liên hệ</p>
                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($patient['emergency_contact']) ?></p>
                </div>
                <?php if (!empty($patient['emergency_phone'])): ?>
                <div class="bg-red-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Số điện thoại</p>
                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($patient['emergency_phone']) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Bảo hiểm -->
        <?php if (!empty($patient['insurance_number'])): ?>
        <div class="mt-6 pt-6 border-t">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-id-card mr-2"></i>Bảo hiểm y tế
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Số thẻ BHYT</p>
                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($patient['insurance_number']) ?></p>
                </div>
                <?php if (!empty($patient['insurance_provider'])): ?>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Nơi cấp</p>
                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($patient['insurance_provider']) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>