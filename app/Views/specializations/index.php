<?php 
$page_title = 'Các Chuyên khoa';
ob_start(); 
?>

<div class="mb-6">
    <h3 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-stethoscope mr-2"></i>Các Chuyên khoa
    </h3>
    <p class="text-gray-600 mt-2">Khám phá các chuyên khoa y tế tại bệnh viện</p>
</div>

<!-- Specializations Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($specializations as $spec): ?>
    <div class="bg-white rounded-lg shadow-md overflow-hidden card-hover">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-stethoscope text-3xl"></i>
                </div>
                <span class="text-4xl font-bold opacity-50"><?= $spec['id'] ?></span>
            </div>
            <h3 class="text-2xl font-bold"><?= htmlspecialchars($spec['name']) ?></h3>
        </div>
        
        <div class="p-6">
            <p class="text-gray-600 mb-4 line-clamp-3">
                <?= htmlspecialchars($spec['description']) ?>
            </p>
            
            <div class="space-y-3 mb-4">
                <div class="flex items-center text-sm">
                    <i class="fas fa-user-clock text-blue-600 w-5 mr-2"></i>
                    <span class="text-gray-700">
                        Độ tuổi: <strong><?= $spec['min_age'] ?>-<?= $spec['max_age'] ?> tuổi</strong>
                    </span>
                </div>
                
                <div class="flex items-center text-sm">
                    <i class="fas fa-venus-mars text-purple-600 w-5 mr-2"></i>
                    <span class="text-gray-700">
                        Giới tính: 
                        <strong>
                            <?php
                            $genderLabels = [
                                'both' => 'Cả hai',
                                'male' => 'Nam',
                                'female' => 'Nữ'
                            ];
                            echo $genderLabels[$spec['gender_requirement']];
                            ?>
                        </strong>
                    </span>
                </div>
                
                <div class="flex items-center text-sm">
                    <i class="fas fa-user-md text-green-600 w-5 mr-2"></i>
                    <span class="text-gray-700">
                        <strong><?= $spec['doctor_count'] ?></strong> bác sĩ
                    </span>
                </div>
            </div>
            
            <a href="<?= APP_URL ?>/specializations/<?= $spec['id'] ?>" 
               class="block w-full text-center px-4 py-3 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                <i class="fas fa-arrow-right mr-2"></i>Xem chi tiết
            </a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
