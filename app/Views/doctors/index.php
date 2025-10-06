<?php 
$page_title = 'Danh sách Bác sĩ';
ob_start(); 
?>

<div class="mb-6 flex items-center justify-between">
    <h3 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-user-md mr-2"></i>Danh sách Bác sĩ
    </h3>
</div>

<!-- Search -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="<?= APP_URL ?>/doctors/search" method="GET" class="flex gap-4">
        <div class="flex-1">
            <input type="text" name="keyword" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="Tìm kiếm bác sĩ theo tên, chuyên khoa...">
        </div>
        <button type="submit" class="px-6 py-2 gradient-bg text-white rounded-lg hover:opacity-90 transition">
            <i class="fas fa-search mr-2"></i>Tìm kiếm
        </button>
    </form>
</div>

<!-- Doctors Grid -->
<?php if (empty($doctors)): ?>
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <i class="fas fa-user-md text-6xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg">Không tìm thấy bác sĩ nào</p>
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
                        <i class="fas fa-stethoscope text-purple-600 mt-1 mr-3 w-5"></i>
                        <div>
                            <p class="text-xs text-gray-500">Chuyên khoa</p>
                            <p class="font-semibold text-gray-800"><?= htmlspecialchars($doctor['specialization']) ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <i class="fas fa-graduation-cap text-purple-600 mt-1 mr-3 w-5"></i>
                        <div>
                            <p class="text-xs text-gray-500">Kinh nghiệm</p>
                            <p class="font-semibold text-gray-800"><?= $doctor['experience_years'] ?> năm</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <i class="fas fa-phone text-purple-600 mt-1 mr-3 w-5"></i>
                        <div>
                            <p class="text-xs text-gray-500">Liên hệ</p>
                            <p class="font-semibold text-gray-800"><?= htmlspecialchars($doctor['phone']) ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <i class="fas fa-dollar-sign text-purple-600 mt-1 mr-3 w-5"></i>
                        <div>
                            <p class="text-xs text-gray-500">Phí khám</p>
                            <p class="font-semibold text-gray-800"><?= number_format($doctor['consultation_fee']) ?> VNĐ</p>
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
