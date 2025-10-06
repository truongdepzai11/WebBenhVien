<?php 
$page_title = $specialization['name'];
ob_start(); 
?>

<div class="mb-6">
    <a href="<?= APP_URL ?>/specializations" class="text-purple-600 hover:text-purple-700 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách
    </a>
    
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-8 text-white shadow-lg">
        <div class="flex items-center space-x-4">
            <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-stethoscope text-4xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-bold"><?= htmlspecialchars($specialization['name']) ?></h1>
                <p class="text-blue-100 mt-2"><?= htmlspecialchars($specialization['description']) ?></p>
            </div>
        </div>
        
        <div class="grid grid-cols-3 gap-4 mt-6">
            <div class="bg-white bg-opacity-10 rounded-lg p-4">
                <i class="fas fa-user-clock text-2xl mb-2"></i>
                <p class="text-sm opacity-75">Độ tuổi</p>
                <p class="text-xl font-bold"><?= $specialization['min_age'] ?>-<?= $specialization['max_age'] ?> tuổi</p>
            </div>
            
            <div class="bg-white bg-opacity-10 rounded-lg p-4">
                <i class="fas fa-venus-mars text-2xl mb-2"></i>
                <p class="text-sm opacity-75">Giới tính</p>
                <p class="text-xl font-bold">
                    <?php
                    $genderLabels = ['both' => 'Cả hai', 'male' => 'Nam', 'female' => 'Nữ'];
                    echo $genderLabels[$specialization['gender_requirement']];
                    ?>
                </p>
            </div>
            
            <div class="bg-white bg-opacity-10 rounded-lg p-4">
                <i class="fas fa-user-md text-2xl mb-2"></i>
                <p class="text-sm opacity-75">Số bác sĩ</p>
                <p class="text-xl font-bold"><?= count($doctors) ?> bác sĩ</p>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách bác sĩ -->
<div class="mb-6">
    <h3 class="text-2xl font-bold text-gray-800 mb-4">
        <i class="fas fa-user-md mr-2"></i>Đội ngũ bác sĩ
    </h3>
</div>

<?php if (empty($doctors)): ?>
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <i class="fas fa-user-md text-6xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg">Chưa có bác sĩ nào trong chuyên khoa này</p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($doctors as $doctor): ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden card-hover">
            <div class="gradient-bg p-6 text-white">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-md text-4xl text-purple-600"></i>
                </div>
                <h3 class="text-xl font-bold text-center"><?= htmlspecialchars($doctor['full_name']) ?></h3>
                <p class="text-center text-purple-100 text-sm mt-1"><?= htmlspecialchars($doctor['doctor_code']) ?></p>
            </div>
            
            <div class="p-6">
                <div class="space-y-3 mb-4">
                    <div class="flex items-start">
                        <i class="fas fa-graduation-cap text-purple-600 mt-1 mr-3 w-5"></i>
                        <div>
                            <p class="text-xs text-gray-500">Trình độ</p>
                            <p class="font-semibold text-gray-800"><?= htmlspecialchars($doctor['qualification']) ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <i class="fas fa-award text-purple-600 mt-1 mr-3 w-5"></i>
                        <div>
                            <p class="text-xs text-gray-500">Kinh nghiệm</p>
                            <p class="font-semibold text-gray-800"><?= $doctor['experience_years'] ?> năm</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <i class="fas fa-dollar-sign text-purple-600 mt-1 mr-3 w-5"></i>
                        <div>
                            <p class="text-xs text-gray-500">Phí khám</p>
                            <p class="font-semibold text-gray-800"><?= number_format($doctor['consultation_fee']) ?> VNĐ</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <i class="fas fa-calendar text-purple-600 mt-1 mr-3 w-5"></i>
                        <div>
                            <p class="text-xs text-gray-500">Lịch làm việc</p>
                            <p class="font-semibold text-gray-800 text-sm"><?= htmlspecialchars($doctor['available_days']) ?></p>
                            <p class="text-xs text-gray-600"><?= htmlspecialchars($doctor['available_hours']) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <a href="<?= APP_URL ?>/doctors/<?= $doctor['id'] ?>" 
                       class="flex-1 text-center px-4 py-2 border border-purple-600 text-purple-600 rounded-lg hover:bg-purple-50 transition">
                        <i class="fas fa-info-circle mr-1"></i>Chi tiết
                    </a>
                    <?php if (Auth::isPatient()): ?>
                    <a href="<?= APP_URL ?>/appointments/create?doctor_id=<?= $doctor['id'] ?>" 
                       class="flex-1 text-center px-4 py-2 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                        <i class="fas fa-calendar-plus mr-1"></i>Đặt lịch
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
