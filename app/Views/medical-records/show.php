<?php 
$page_title = 'Chi tiết Hồ sơ bệnh án';
ob_start(); 
?>

<div class="mb-6">
    <a href="<?= APP_URL ?>/medical-records" class="text-purple-600 hover:text-purple-700">
        <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách
    </a>
</div>

<!-- Medical Record Details -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <div class="gradient-bg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Hồ sơ bệnh án #MR<?= str_pad($record['id'], 5, '0', STR_PAD_LEFT) ?></h1>
                <p class="text-purple-100">Ngày khám: <?= date('d/m/Y', strtotime($record['visit_date'])) ?></p>
            </div>
        </div>
    </div>

    <div class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Thông tin bệnh nhân -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-user mr-2"></i>Thông tin bệnh nhân
                </h3>
                <div class="space-y-2">
                    <p><strong>Họ tên:</strong> <?= htmlspecialchars($record['patient_name']) ?></p>
                    <p><strong>Mã BN:</strong> <?= htmlspecialchars($record['patient_code']) ?></p>
                    <?php if (isset($record['date_of_birth'])): ?>
                    <p><strong>Ngày sinh:</strong> <?= date('d/m/Y', strtotime($record['date_of_birth'])) ?></p>
                    <?php endif; ?>
                    <?php if (isset($record['gender'])): ?>
                    <p><strong>Giới tính:</strong> 
                        <?php
                        $genderLabels = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                        echo $genderLabels[$record['gender']] ?? 'N/A';
                        ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Thông tin bác sĩ -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-user-md mr-2"></i>Bác sĩ khám
                </h3>
                <div class="space-y-2">
                    <p><strong>Họ tên:</strong> <?= htmlspecialchars($record['doctor_name']) ?></p>
                    <p><strong>Chuyên khoa:</strong> <?= htmlspecialchars($record['specialization']) ?></p>
                </div>
            </div>
        </div>

        <!-- Triệu chứng -->
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">
                <i class="fas fa-heartbeat mr-2"></i>Triệu chứng
            </h3>
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-gray-700"><?= nl2br(htmlspecialchars($record['symptoms'])) ?></p>
            </div>
        </div>

        <!-- Chẩn đoán -->
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">
                <i class="fas fa-stethoscope mr-2"></i>Chẩn đoán
            </h3>
            <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                <p class="text-gray-700 font-semibold"><?= nl2br(htmlspecialchars($record['diagnosis'])) ?></p>
            </div>
        </div>

        <!-- Điều trị -->
        <?php if (!empty($record['treatment'])): ?>
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">
                <i class="fas fa-pills mr-2"></i>Phương pháp điều trị
            </h3>
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-gray-700"><?= nl2br(htmlspecialchars($record['treatment'])) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Đơn thuốc -->
        <?php if (!empty($record['prescription'])): ?>
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">
                <i class="fas fa-prescription mr-2"></i>Đơn thuốc
            </h3>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <p class="text-gray-700"><?= nl2br(htmlspecialchars($record['prescription'])) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Kết quả xét nghiệm -->
        <?php if (!empty($record['test_results'])): ?>
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">
                <i class="fas fa-vial mr-2"></i>Kết quả xét nghiệm
            </h3>
            <div class="bg-purple-50 p-4 rounded-lg">
                <p class="text-gray-700"><?= nl2br(htmlspecialchars($record['test_results'])) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Ghi chú -->
        <?php if (!empty($record['notes'])): ?>
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">
                <i class="fas fa-sticky-note mr-2"></i>Ghi chú
            </h3>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-gray-700"><?= nl2br(htmlspecialchars($record['notes'])) ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
