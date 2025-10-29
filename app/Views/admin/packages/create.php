<?php 
$page_title = 'Thêm Gói khám';
ob_start(); 
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-plus-circle mr-2"></i>Thêm Gói khám mới
        </h3>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <form action="<?= APP_URL ?>/admin/packages/store" method="POST" class="space-y-6">
            
            <!-- Tên gói khám -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Tên gói khám *
                </label>
                <input type="text" id="name" name="name" required
                       value="<?= $_SESSION['old']['name'] ?? '' ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                       placeholder="Ví dụ: Gói khám sức khỏe tổng quát">
            </div>

            <!-- Mô tả -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Mô tả
                </label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                          placeholder="Mô tả chi tiết về gói khám..."><?= $_SESSION['old']['description'] ?? '' ?></textarea>
            </div>

            <!-- Yêu cầu giới tính -->
            <div>
                <label for="gender_requirement" class="block text-sm font-medium text-gray-700 mb-2">
                    Yêu cầu giới tính *
                </label>
                <select id="gender_requirement" name="gender_requirement" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="both" <?= (($_SESSION['old']['gender_requirement'] ?? 'both') === 'both') ? 'selected' : '' ?>>
                        Cả hai giới
                    </option>
                    <option value="male" <?= (($_SESSION['old']['gender_requirement'] ?? '') === 'male') ? 'selected' : '' ?>>
                        Chỉ nam
                    </option>
                    <option value="female" <?= (($_SESSION['old']['gender_requirement'] ?? '') === 'female') ? 'selected' : '' ?>>
                        Chỉ nữ
                    </option>
                </select>
            </div>

            <!-- Thông báo về giá -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                    <div class="text-sm text-gray-700">
                        <strong>Lưu ý:</strong> Giá gói khám sẽ được tính tự động dựa trên tổng giá các dịch vụ bạn thêm vào sau khi tạo gói.
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
                           value="<?= $_SESSION['old']['min_age'] ?? '18' ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label for="max_age" class="block text-sm font-medium text-gray-700 mb-2">
                        Độ tuổi tối đa *
                    </label>
                    <input type="number" id="max_age" name="max_age" required min="0" max="150"
                           value="<?= $_SESSION['old']['max_age'] ?? '150' ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
            </div>

            <!-- Trạng thái -->
            <div class="flex items-center">
                <input type="checkbox" id="is_active" name="is_active" value="1" checked
                       class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                <label for="is_active" class="ml-2 text-sm text-gray-700">
                    Kích hoạt gói khám ngay
                </label>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <div class="flex">
                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                    <div>
                        <h4 class="font-semibold text-blue-800 mb-2">Lưu ý:</h4>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• Sau khi tạo gói khám, bạn cần thêm các dịch vụ/xét nghiệm vào gói</li>
                            <li>• Giá gói khám nên thấp hơn tổng giá các dịch vụ lẻ để khuyến khích khách hàng</li>
                            <li>• Độ tuổi phù hợp giúp hệ thống gợi ý gói khám chính xác cho bệnh nhân</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 gradient-bg text-white font-semibold py-3 px-6 rounded-lg hover:opacity-90 transition">
                    <i class="fas fa-save mr-2"></i>Lưu và thêm dịch vụ
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
