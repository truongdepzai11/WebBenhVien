<?php 
$page_title = 'Sửa Chuyên khoa';
ob_start(); 
?>

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-edit mr-2"></i>Sửa Chuyên khoa
        </h3>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <form action="<?= APP_URL ?>/admin/specializations/<?= $specialization['id'] ?>/update" method="POST" class="space-y-6">
            <!-- Tên chuyên khoa -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Tên chuyên khoa *
                </label>
                <input type="text" id="name" name="name" required
                       value="<?= $_SESSION['old']['name'] ?? $specialization['name'] ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                       placeholder="Ví dụ: Tim mạch, Nội khoa...">
            </div>

            <!-- Mô tả -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Mô tả
                </label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                          placeholder="Mô tả về chuyên khoa..."><?= $_SESSION['old']['description'] ?? $specialization['description'] ?></textarea>
            </div>

            <!-- Độ tuổi -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="min_age" class="block text-sm font-medium text-gray-700 mb-2">
                        Độ tuổi tối thiểu *
                    </label>
                    <input type="number" id="min_age" name="min_age" required min="0" max="150"
                           value="<?= $_SESSION['old']['min_age'] ?? $specialization['min_age'] ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label for="max_age" class="block text-sm font-medium text-gray-700 mb-2">
                        Độ tuổi tối đa *
                    </label>
                    <input type="number" id="max_age" name="max_age" required min="0" max="150"
                           value="<?= $_SESSION['old']['max_age'] ?? $specialization['max_age'] ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
            </div>

            <!-- Yêu cầu giới tính -->
            <div>
                <label for="gender_requirement" class="block text-sm font-medium text-gray-700 mb-2">
                    Yêu cầu giới tính *
                </label>
                <select id="gender_requirement" name="gender_requirement" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="both" <?= (($_SESSION['old']['gender_requirement'] ?? $specialization['gender_requirement']) === 'both') ? 'selected' : '' ?>>
                        Cả hai giới
                    </option>
                    <option value="male" <?= (($_SESSION['old']['gender_requirement'] ?? $specialization['gender_requirement']) === 'male') ? 'selected' : '' ?>>
                        Chỉ nam
                    </option>
                    <option value="female" <?= (($_SESSION['old']['gender_requirement'] ?? $specialization['gender_requirement']) === 'female') ? 'selected' : '' ?>>
                        Chỉ nữ
                    </option>
                </select>
            </div>

            <!-- Ví dụ -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <div class="flex">
                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                    <div>
                        <h4 class="font-semibold text-blue-800 mb-2">Ví dụ:</h4>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• <strong>Nhi khoa:</strong> 0-15 tuổi, Cả hai giới</li>
                            <li>• <strong>Lão khoa:</strong> 60-150 tuổi, Cả hai giới</li>
                            <li>• <strong>Sản phụ khoa:</strong> 15-60 tuổi, Chỉ nữ</li>
                            <li>• <strong>Nam khoa:</strong> 18-150 tuổi, Chỉ nam</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 gradient-bg text-white font-semibold py-3 px-6 rounded-lg hover:opacity-90 transition">
                    <i class="fas fa-save mr-2"></i>Cập nhật
                </button>
                <a href="<?= APP_URL ?>/admin/specializations" 
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
