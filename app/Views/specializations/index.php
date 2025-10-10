<?php 
$page_title = 'Các Chuyên khoa';
ob_start(); 
?>

<!-- Hero Section -->
<section id="specializations" class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Các Chuyên khoa</h2>
            <p class="text-xl text-gray-600">Đa dạng chuyên khoa với đội ngũ bác sĩ chuyên môn cao</p>
        </div>

<!-- Specializations Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <?php 
    // Phân trang: 6 items/trang
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 6;
    $offset = ($page - 1) * $perPage;
    $totalSpecs = count($specializations);
    $totalPages = ceil($totalSpecs / $perPage);
    $pagedSpecs = array_slice($specializations, $offset, $perPage);
    
    foreach ($pagedSpecs as $spec): 
    ?>
    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden group">
        <!-- Header với icon lớn -->
        <div class="relative bg-gradient-to-br from-purple-500 to-indigo-600 p-8 text-white">
            <div class="flex items-center space-x-4">
                <div class="w-20 h-20 bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas <?= $spec['icon'] ?? 'fa-stethoscope' ?> text-4xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold mb-1"><?= htmlspecialchars($spec['name']) ?></h3>
                    <p class="text-purple-100 text-sm"><?= $spec['doctor_count'] ?> bác sĩ</p>
                </div>
            </div>
        </div>
        
        <!-- Body -->
        <div class="p-6">
            <p class="text-gray-600 mb-6 leading-relaxed line-clamp-2">
                <?= htmlspecialchars($spec['description']) ?>
            </p>
            
            <!-- Info Grid -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-blue-50 rounded-lg p-3">
                    <div class="flex items-center text-blue-600 mb-1">
                        <i class="fas fa-user-clock text-sm mr-2"></i>
                        <span class="text-xs font-semibold">Độ tuổi</span>
                    </div>
                    <p class="text-gray-800 font-bold"><?= $spec['min_age'] ?>-<?= $spec['max_age'] ?> tuổi</p>
                </div>
                
                <div class="bg-purple-50 rounded-lg p-3">
                    <div class="flex items-center text-purple-600 mb-1">
                        <i class="fas fa-venus-mars text-sm mr-2"></i>
                        <span class="text-xs font-semibold">Giới tính</span>
                    </div>
                    <p class="text-gray-800 font-bold">
                        <?php
                        $genderLabels = [
                            'both' => 'Cả hai',
                            'male' => 'Nam',
                            'female' => 'Nữ'
                        ];
                        echo $genderLabels[$spec['gender_requirement']];
                        ?>
                    </p>
                </div>
            </div>
            
            <!-- Button -->
            <a href="<?= APP_URL ?>/specializations/<?= $spec['id'] ?>" 
               class="block w-full text-center px-4 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all font-semibold group">
                <span>Xem chi tiết</span>
                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform inline-block"></i>
            </a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="flex justify-center items-center space-x-2">
    <!-- Previous Button -->
    <?php if ($page > 1): ?>
    <a href="?page=<?= $page - 1 ?>" 
       class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
        <i class="fas fa-chevron-left"></i>
    </a>
    <?php else: ?>
    <span class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">
        <i class="fas fa-chevron-left"></i>
    </span>
    <?php endif; ?>
    
    <!-- Page Numbers -->
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php if ($i == $page): ?>
        <span class="px-4 py-2 gradient-bg text-white rounded-lg font-semibold">
            <?= $i ?>
        </span>
        <?php else: ?>
        <a href="?page=<?= $i ?>" 
           class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            <?= $i ?>
        </a>
        <?php endif; ?>
    <?php endfor; ?>
    
    <!-- Next Button -->
    <?php if ($page < $totalPages): ?>
    <a href="?page=<?= $page + 1 ?>" 
       class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
        <i class="fas fa-chevron-right"></i>
    </a>
    <?php else: ?>
    <span class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">
        <i class="fas fa-chevron-right"></i>
    </span>
    <?php endif; ?>
</div>

<!-- Page Info -->
<div class="text-center mt-4 text-gray-600">
    Trang <?= $page ?> / <?= $totalPages ?> (Tổng <?= $totalSpecs ?> chuyên khoa)
</div>
<?php endif; ?>

    </div>
</section>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/landing.php';
?>
