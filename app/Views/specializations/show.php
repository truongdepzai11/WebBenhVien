<?php 
$page_title = $specialization['name'];
ob_start(); 
?>

<!-- Breadcrumb -->
<div class="bg-gray-50 py-4">
    <div class="container mx-auto px-6">
        <a href="<?= APP_URL ?>" class="text-purple-600 hover:text-purple-700 inline-flex items-center font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Quay lại trang chủ
        </a>
    </div>
</div>

<!-- Main Content -->
<div class="container mx-auto px-6 py-8">

<!-- Header Card -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
    <div class="bg-gradient-to-br from-purple-500 to-indigo-600 p-8 md:p-12">
        <div class="flex items-start space-x-6">
            <div class="w-24 h-24 bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0">
                <i class="fas <?= $specialization['icon'] ?? 'fa-stethoscope' ?> text-5xl text-white"></i>
            </div>
            <div class="flex-1">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-3"><?= htmlspecialchars($specialization['name']) ?></h1>
                <p class="text-purple-100 text-lg leading-relaxed"><?= htmlspecialchars($specialization['description']) ?></p>
            </div>
        </div>
    </div>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-200">
        <div class="p-6 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded-xl mb-3">
                <i class="fas fa-user-clock text-2xl text-blue-600"></i>
            </div>
            <p class="text-sm text-gray-600 mb-1">Độ tuổi phù hợp</p>
            <p class="text-2xl font-bold text-gray-800"><?= $specialization['min_age'] ?>-<?= $specialization['max_age'] ?> tuổi</p>
        </div>
        
        <div class="p-6 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-purple-100 rounded-xl mb-3">
                <i class="fas fa-venus-mars text-2xl text-purple-600"></i>
            </div>
            <p class="text-sm text-gray-600 mb-1">Giới tính</p>
            <p class="text-2xl font-bold text-gray-800">
                <?php
                $genderLabels = ['both' => 'Cả hai', 'male' => 'Nam', 'female' => 'Nữ'];
                echo $genderLabels[$specialization['gender_requirement']];
                ?>
            </p>
        </div>
        
        <div class="p-6 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-xl mb-3">
                <i class="fas fa-user-md text-2xl text-green-600"></i>
            </div>
            <p class="text-sm text-gray-600 mb-1">Đội ngũ bác sĩ</p>
            <p class="text-2xl font-bold text-gray-800"><?= count($doctors) ?> bác sĩ</p>
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

</div>
<!-- End Main Content -->

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/landing.php';
?>
