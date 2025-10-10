<?php 
$page_title = 'Hệ thống Quản lý Bệnh viện';
ob_start(); 
?>

<!-- Hero Section -->
<section id="home" class="hero-gradient text-white py-20">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-5xl font-bold mb-6 leading-tight">
                    Chăm sóc sức khỏe<br>
                    <span class="text-purple-200">Chuyên nghiệp & Tận tâm</span>
                </h1>
                <p class="text-xl text-purple-100 mb-8">
                    Hệ thống quản lý bệnh viện hiện đại với đội ngũ bác sĩ giàu kinh nghiệm, 
                    trang thiết bị y tế tiên tiến, mang đến dịch vụ chăm sóc sức khỏe tốt nhất.
                </p>
                <div class="flex space-x-4">
                    <a href="<?= APP_URL ?>/auth/register" 
                       class="px-8 py-4 bg-white text-purple-600 rounded-lg hover:bg-gray-100 transition font-bold text-lg">
                        <i class="fas fa-user-plus mr-2"></i>Đăng ký ngay
                    </a>
                    <a href="#specializations" 
                       class="px-8 py-4 border-2 border-white text-white rounded-lg hover:bg-white hover:text-purple-600 transition font-bold text-lg">
                        <i class="fas fa-stethoscope mr-2"></i>Xem chuyên khoa
                    </a>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=600&h=400&fit=crop" 
                         alt="Hospital" 
                         class="w-full h-96 object-cover rounded-3xl shadow-2xl">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-12 bg-white shadow-md">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold text-purple-600 mb-2">
                    <i class="fas fa-user-md"></i>
                </div>
                <p class="text-3xl font-bold text-gray-800"><?= count($featured_doctors) ?>+</p>
                <p class="text-gray-600">Bác sĩ</p>
            </div>
            <div>
                <div class="text-4xl font-bold text-purple-600 mb-2">
                    <i class="fas fa-stethoscope"></i>
                </div>
                <p class="text-3xl font-bold text-gray-800"><?= count($specializations) ?>+</p>
                <p class="text-gray-600">Chuyên khoa</p>
            </div>
            <div>
                <div class="text-4xl font-bold text-purple-600 mb-2">
                    <i class="fas fa-hospital-user"></i>
                </div>
                <p class="text-3xl font-bold text-gray-800">1000+</p>
                <p class="text-gray-600">Bệnh nhân</p>
            </div>
            <div>
                <div class="text-4xl font-bold text-purple-600 mb-2">
                    <i class="fas fa-award"></i>
                </div>
                <p class="text-3xl font-bold text-gray-800">15+</p>
                <p class="text-gray-600">Năm kinh nghiệm</p>
            </div>
        </div>
    </div>
</section>

<!-- Specializations Section -->
<section id="specializations" class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Các Chuyên khoa</h2>
            <p class="text-xl text-gray-600">Đa dạng chuyên khoa với đội ngũ bác sĩ chuyên môn cao</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php 
            // Phân trang: 6 chuyên khoa/trang
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = 6;
            $offset = ($page - 1) * $perPage;
            $totalSpecs = count($specializations);
            $totalPages = ceil($totalSpecs / $perPage);
            $pagedSpecs = array_slice($specializations, $offset, $perPage);
            
            foreach ($pagedSpecs as $spec): 
            ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover">
                <div class="gradient-bg p-6 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <i class="fas <?= $spec['icon'] ?> text-3xl"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold"><?= htmlspecialchars($spec['name']) ?></h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4"><?= htmlspecialchars($spec['description']) ?></p>
                    <div class="flex items-center text-sm text-gray-500 mb-2">
                        <i class="fas fa-user-clock mr-2"></i>
                        Độ tuổi: <?= $spec['min_age'] ?>-<?= $spec['max_age'] ?> tuổi
                    </div>
                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <i class="fas fa-venus-mars mr-2"></i>
                        <?php
                        $genderLabels = ['both' => 'Cả hai', 'male' => 'Nam', 'female' => 'Nữ'];
                        echo $genderLabels[$spec['gender_requirement']];
                        ?>
                    </div>
                    <a href="<?= APP_URL ?>/specializations/<?= $spec['id'] ?>" 
                       class="block w-full text-center px-4 py-2 border-2 border-purple-600 text-purple-600 rounded-lg hover:bg-purple-50 transition font-semibold">
                        Xem chi tiết
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="flex justify-center items-center space-x-2">
            <!-- Previous -->
            <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>#specializations" 
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
                <a href="?page=<?= $i ?>#specializations" 
                   class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    <?= $i ?>
                </a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <!-- Next -->
            <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>#specializations" 
               class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-chevron-right"></i>
            </a>
            <?php else: ?>
            <span class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">
                <i class="fas fa-chevron-right"></i>
            </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Doctors Section -->
<section id="doctors" class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Đội ngũ Bác sĩ</h2>
            <p class="text-xl text-gray-600">Bác sĩ giàu kinh nghiệm, tận tâm với nghề</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($featured_doctors as $doctor): ?>
            <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover">
                <div class="gradient-bg p-6 text-white text-center">
                    <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-md text-5xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-bold"><?= htmlspecialchars($doctor['full_name']) ?></h3>
                    <p class="text-purple-100 text-sm mt-1"><?= htmlspecialchars($doctor['doctor_code']) ?></p>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <span class="inline-block px-3 py-1 bg-purple-100 text-purple-600 rounded-full text-sm font-semibold">
                            <?= htmlspecialchars($doctor['specialization']) ?>
                        </span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex items-center">
                            <i class="fas fa-graduation-cap text-purple-600 w-5 mr-2"></i>
                            <?= htmlspecialchars($doctor['qualification']) ?>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-award text-purple-600 w-5 mr-2"></i>
                            <?= $doctor['experience_years'] ?> năm kinh nghiệm
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-dollar-sign text-purple-600 w-5 mr-2"></i>
                            <?= number_format($doctor['consultation_fee']) ?> VNĐ
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12">
            <a href="<?= APP_URL ?>/auth/register" 
               class="inline-block px-8 py-4 gradient-bg text-white rounded-lg hover:opacity-90 transition font-bold text-lg">
                Đăng ký để đặt lịch khám
            </a>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Dịch vụ của chúng tôi</h2>
            <p class="text-xl text-gray-600">Đa dạng dịch vụ y tế chất lượng cao</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Đặt lịch online -->
            <a href="<?= APP_URL ?>/auth/register" class="bg-white p-8 rounded-xl shadow-md text-center card-hover block">
                <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-check text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Đặt lịch online</h3>
                <p class="text-gray-600">Đặt lịch khám nhanh chóng, tiện lợi</p>
                <p class="text-purple-600 font-semibold mt-3">
                    <i class="fas fa-arrow-right mr-1"></i>Đăng ký ngay
                </p>
            </a>
            
            <!-- Hồ sơ bệnh án -->
            <a href="<?= APP_URL ?>/auth/login" class="bg-white p-8 rounded-xl shadow-md text-center card-hover block">
                <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-file-medical text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Hồ sơ bệnh án</h3>
                <p class="text-gray-600">Quản lý hồ sơ điện tử an toàn</p>
                <p class="text-purple-600 font-semibold mt-3">
                    <i class="fas fa-arrow-right mr-1"></i>Đăng nhập
                </p>
            </a>
            
            <!-- Đơn thuốc điện tử -->
            <a href="<?= APP_URL ?>/auth/login" class="bg-white p-8 rounded-xl shadow-md text-center card-hover block">
                <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-pills text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Đơn thuốc điện tử</h3>
                <p class="text-gray-600">Kê đơn và tra cứu thuốc dễ dàng</p>
                <p class="text-purple-600 font-semibold mt-3">
                    <i class="fas fa-arrow-right mr-1"></i>Đăng nhập
                </p>
            </a>
            
            <!-- Hỗ trợ 24/7 -->
            <div class="bg-white p-8 rounded-xl shadow-md text-center card-hover">
                <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Hỗ trợ 24/7</h3>
                <p class="text-gray-600">Tư vấn và hỗ trợ mọi lúc</p>
                <p class="text-gray-500 font-semibold mt-3">
                    <i class="fas fa-phone mr-1"></i>0973436483
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section id="contact" class="py-20 gradient-bg text-white">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-4xl font-bold mb-6">Sẵn sàng chăm sóc sức khỏe của bạn?</h2>
        <p class="text-xl text-purple-100 mb-8 max-w-2xl mx-auto">
            Đăng ký tài khoản ngay hôm nay để trải nghiệm dịch vụ y tế chất lượng cao
        </p>
        <div class="flex justify-center space-x-4">
            <a href="<?= APP_URL ?>/auth/register" 
               class="px-8 py-4 bg-white text-purple-600 rounded-lg hover:bg-gray-100 transition font-bold text-lg">
                <i class="fas fa-user-plus mr-2"></i>Đăng ký miễn phí
            </a>
            <a href="<?= APP_URL ?>/auth/login" 
               class="px-8 py-4 border-2 border-white text-white rounded-lg hover:bg-white hover:text-purple-600 transition font-bold text-lg">
                <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập
            </a>
        </div>
    </div>
</section>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/landing.php';
?>
