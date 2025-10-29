<?php 
$page_title = 'Sửa Gói khám';
ob_start(); 
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-edit mr-2"></i>Sửa Gói khám
        </h3>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <form action="<?= APP_URL ?>/admin/packages/<?= $package['id'] ?>/update" method="POST" class="space-y-6">
            
            <!-- Mã gói khám (readonly) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Mã gói khám
                </label>
                <input type="text" value="<?= htmlspecialchars($package['package_code']) ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100" disabled>
            </div>

            <!-- Tên gói khám -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Tên gói khám *
                </label>
                <input type="text" id="name" name="name" required
                       value="<?= $_SESSION['old']['name'] ?? $package['name'] ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>

            <!-- Mô tả -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Mô tả
                </label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"><?= $_SESSION['old']['description'] ?? $package['description'] ?></textarea>
            </div>

            <!-- Yêu cầu giới tính -->
            <div>
                <label for="gender_requirement" class="block text-sm font-medium text-gray-700 mb-2">
                    Yêu cầu giới tính *
                </label>
                <select id="gender_requirement" name="gender_requirement" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="both" <?= (($_SESSION['old']['gender_requirement'] ?? $package['gender_requirement']) === 'both') ? 'selected' : '' ?>>
                        Cả hai giới
                    </option>
                    <option value="male" <?= (($_SESSION['old']['gender_requirement'] ?? $package['gender_requirement']) === 'male') ? 'selected' : '' ?>>
                        Chỉ nam
                    </option>
                    <option value="female" <?= (($_SESSION['old']['gender_requirement'] ?? $package['gender_requirement']) === 'female') ? 'selected' : '' ?>>
                        Chỉ nữ
                    </option>
                </select>
            </div>

            <!-- Thông báo về giá -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                    <div class="text-sm text-gray-700">
                        <strong>Lưu ý:</strong> Giá gói khám được tính tự động từ tổng giá các dịch vụ. Vào quản lý dịch vụ để thay đổi giá.
                    </div>
                </div>
            </div>

            <!-- Độ tuổi -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="min_age" class="block text-sm font-medium text-gray-700 mb-2">
                        Độ tuổi tối thiểu *
                    </label>
                    <input type="number" id="min_age" name="min_age" required min="0" max="150"
                           value="<?= $_SESSION['old']['min_age'] ?? $package['min_age'] ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label for="max_age" class="block text-sm font-medium text-gray-700 mb-2">
                        Độ tuổi tối đa *
                    </label>
                    <input type="number" id="max_age" name="max_age" required min="0" max="150"
                           value="<?= $_SESSION['old']['max_age'] ?? $package['max_age'] ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
            </div>

            <!-- Trạng thái -->
            <div class="flex items-center">
                <input type="checkbox" id="is_active" name="is_active" value="1" 
                       <?= $package['is_active'] ? 'checked' : '' ?>
                       class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                <label for="is_active" class="ml-2 text-sm text-gray-700">
                    Gói khám đang hoạt động
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 gradient-bg text-white font-semibold py-3 px-6 rounded-lg hover:opacity-90 transition">
                    <i class="fas fa-save mr-2"></i>Cập nhật
                </button>
                <a href="<?= APP_URL ?>/admin/packages" 
                   class="flex-1 text-center bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i>Hủy
                </a>
            </div>
        </form>
    </div>
</div>


<?php 
unset($_SESSION['old']);
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
