<?php 
$page_title = 'Quản lý Dịch vụ - ' . $package['name'];
ob_start(); 

$categoryNames = [
    'general' => 'Khám tổng quát',
    'blood_test' => 'Xét nghiệm máu',
    'urine_test' => 'Xét nghiệm nước tiểu',
    'imaging' => 'Chẩn đoán hình ảnh',
    'specialist' => 'Khám chuyên khoa',
    'other' => 'Khác'
];
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-list-check mr-2"></i>Quản lý Dịch vụ
            </h3>
            <p class="text-gray-600 mt-1">
                <?= htmlspecialchars($package['name']) ?> (<?= $package['package_code'] ?>)
            </p>
        </div>
        <a href="<?= APP_URL ?>/admin/packages" 
           class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Quay lại
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Form thêm dịch vụ -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
            <h4 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-plus-circle mr-2"></i>Thêm dịch vụ mới
            </h4>
            
            <form action="<?= APP_URL ?>/admin/packages/<?= $package['id'] ?>/services/add" method="POST" class="space-y-4">
                
                <div>
                    <label for="service_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Tên dịch vụ *
                    </label>
                    <input type="text" id="service_name" name="service_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm"
                           placeholder="Ví dụ: Xét nghiệm máu tổng quát">
                </div>

                <div>
                    <label for="service_category" class="block text-sm font-medium text-gray-700 mb-2">
                        Danh mục *
                    </label>
                    <select id="service_category" name="service_category" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                        <?php foreach ($categoryNames as $key => $name): ?>
                        <option value="<?= $key ?>"><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="gender_specific" class="block text-sm font-medium text-gray-700 mb-2">
                        Dành cho giới tính
                    </label>
                    <select id="gender_specific" name="gender_specific"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                        <option value="both">Cả hai giới</option>
                        <option value="male">Chỉ nam</option>
                        <option value="female">Chỉ nữ</option>
                    </select>
                </div>

                <div>
                    <label for="service_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Giá dịch vụ (VNĐ) *
                    </label>
                    <input type="number" id="service_price" name="service_price" required min="0" step="1000" value="50000"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm"
                           placeholder="50000">
                    <p class="text-xs text-gray-500 mt-1">Giá tiền cho dịch vụ này</p>
                </div>

                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                        Thời lượng khám (phút) *
                    </label>
                    <input type="number" id="duration_minutes" name="duration_minutes" required min="5" step="5" value="30"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm"
                           placeholder="30">
                    <p class="text-xs text-gray-500 mt-1">Thời gian dự kiến thực hiện dịch vụ</p>
                </div>

                <div>
                    <label for="display_order" class="block text-sm font-medium text-gray-700 mb-2">
                        Thứ tự hiển thị
                    </label>
                    <input type="number" id="display_order" name="display_order" value="0" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Ghi chú
                    </label>
                    <textarea id="notes" name="notes" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm"
                              placeholder="Ghi chú về dịch vụ..."></textarea>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_required" name="is_required" value="1" checked
                           class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <label for="is_required" class="ml-2 text-sm text-gray-700">
                        Dịch vụ bắt buộc
                    </label>
                </div>

                <button type="submit" 
                        class="w-full gradient-bg text-white py-2 px-4 rounded-lg hover:opacity-90 transition text-sm font-semibold">
                    <i class="fas fa-plus mr-2"></i>Thêm dịch vụ
                </button>
            </form>

            <!-- Stats -->
            <div class="mt-6 pt-6 border-t space-y-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600"><?= count($services) ?></div>
                    <div class="text-sm text-gray-600">Tổng số dịch vụ</div>
                </div>
                
                <?php 
                $totalPrice = 0;
                $requiredPrice = 0;
                foreach ($services as $service) {
                    $price = $service['service_price'] ?? 0;
                    $totalPrice += $price;
                    if ($service['is_required']) {
                        $requiredPrice += $price;
                    }
                }
                ?>
                
                <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600"><?= number_format($totalPrice) ?> đ</div>
                        <div class="text-xs text-gray-600 mt-1">Tổng giá gói khám</div>
                    </div>
                    <?php if ($requiredPrice > 0): ?>
                    <div class="text-center mt-2 pt-2 border-t border-green-200">
                        <div class="text-sm text-gray-700">Dịch vụ bắt buộc: <span class="font-bold"><?= number_format($requiredPrice) ?> đ</span></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách dịch vụ -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <?php if (!empty($services)): ?>
                <?php 
                    // Tổng thời lượng toàn gói
                    $totalDuration = 0; 
                    foreach ($services as $svc) { 
                        $totalDuration += (int)($svc['duration_minutes'] ?? 30); 
                    }
                    // Hàm format giờ:phút
                    $formatDuration = function($mins) {
                        $mins = (int)$mins;
                        if ($mins < 60) return $mins . ' phút';
                        $h = intdiv($mins, 60);
                        $m = $mins % 60;
                        return $h . ' giờ' . ($m > 0 ? (' ' . $m . ' phút') : '');
                    };
                ?>
                <div class="px-6 pt-4">
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs font-semibold">
                        ⏱ Tổng thời lượng gói: <?= $formatDuration($totalDuration) ?>
                    </span>
                </div>
                <?php
                // Nhóm dịch vụ theo category
                $servicesByCategory = [];
                foreach ($services as $service) {
                    $category = $service['service_category'];
                    if (!isset($servicesByCategory[$category])) {
                        $servicesByCategory[$category] = [];
                    }
                    $servicesByCategory[$category][] = $service;
                }
                ?>

                <div class="divide-y divide-gray-200">
                    <?php foreach ($servicesByCategory as $category => $categoryServices): ?>
                    <div class="p-6">
                        <?php 
                            $categoryTotalDuration = 0; 
                            foreach ($categoryServices as $svc) { 
                                $categoryTotalDuration += (int)($svc['duration_minutes'] ?? 30); 
                            }
                        ?>
                        <div class="flex items-center flex-wrap gap-2 mb-4">
                            <h4 class="text-lg font-bold text-gray-800">
                                <?= $categoryNames[$category] ?? 'Khác' ?>
                            </h4>
                            <span class="ml-1 px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">
                                <?= count($categoryServices) ?> dịch vụ
                            </span>
                            <span class="ml-1 px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-semibold">
                                ⏱ Tổng thời lượng: <?= $formatDuration($categoryTotalDuration) ?>
                            </span>
                        </div>

                        <div class="space-y-2">
                            <?php foreach ($categoryServices as $service): ?>
                            <div class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-medium text-gray-800">
                                            <?= htmlspecialchars($service['service_name']) ?>
                                        </span>
                                        <?php if ($service['is_required']): ?>
                                        <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded">Bắt buộc</span>
                                        <?php endif; ?>
                                        <?php if ($service['gender_specific'] != 'both'): ?>
                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">
                                            <?= $service['gender_specific'] == 'male' ? 'Nam' : 'Nữ' ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Giá dịch vụ, Thời lượng & Bắt buộc - Có thể sửa inline -->
                                    <div class="flex items-center gap-4 mt-2">
                                        <!-- Sửa giá -->
                                        <form action="<?= APP_URL ?>/admin/packages/<?= $package['id'] ?>/services/<?= $service['id'] ?>/update-price" 
                                              method="POST" class="flex items-center gap-2">
                                            <label class="text-sm text-gray-600">Giá:</label>
                                            <input type="number" name="service_price" 
                                                   value="<?= $service['service_price'] ?? 0 ?>" 
                                                   min="0" step="1000"
                                                   class="w-32 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500"
                                                   onchange="this.form.submit()">
                                            <span class="text-sm text-gray-600">đ</span>
                                        </form>

                                        <!-- Sửa thời lượng -->
                                        <form action="<?= APP_URL ?>/admin/packages/<?= $package['id'] ?>/services/<?= $service['id'] ?>/update-duration" 
                                              method="POST" class="flex items-center gap-2">
                                            <label class="text-sm text-gray-600">Thời lượng:</label>
                                            <input type="number" name="duration_minutes" 
                                                   value="<?= (int)($service['duration_minutes'] ?? 30) ?>" 
                                                   min="5" step="5"
                                                   class="w-24 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500"
                                                   onchange="this.form.submit()">
                                            <span class="text-sm text-gray-600">phút</span>
                                        </form>
                                        
                                        <!-- Toggle bắt buộc -->
                                        <form action="<?= APP_URL ?>/admin/packages/<?= $package['id'] ?>/services/<?= $service['id'] ?>/toggle-required" 
                                              method="POST" class="flex items-center gap-2">
                                            <label class="flex items-center cursor-pointer">
                                                <input type="checkbox" name="is_required" value="1" 
                                                       <?= $service['is_required'] ? 'checked' : '' ?>
                                                       onchange="this.form.submit()"
                                                       class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                                <span class="ml-2 text-sm text-gray-600">Bắt buộc</span>
                                            </label>
                                        </form>
                                    </div>
                                    
                                    <?php if ($service['notes']): ?>
                                    <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($service['notes']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <form action="<?= APP_URL ?>/admin/packages/<?= $package['id'] ?>/services/<?= $service['id'] ?>/delete" 
                                      method="POST" class="ml-3"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa dịch vụ này?')">
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
            <div class="p-12 text-center">
                <i class="fas fa-list-check text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">Chưa có dịch vụ nào trong gói khám</p>
                <p class="text-gray-400 text-sm mt-2">Sử dụng form bên trái để thêm dịch vụ</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
