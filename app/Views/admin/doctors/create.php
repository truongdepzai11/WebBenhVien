<?php 
$page_title = 'Thêm Bác sĩ';
ob_start(); 
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-user-plus mr-2"></i>Thêm Bác sĩ mới
        </h3>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <form action="<?= APP_URL ?>/admin/doctors/store" method="POST" class="space-y-6">
            <!-- Thông tin tài khoản -->
            <div class="border-b pb-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Thông tin tài khoản</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            Tên đăng nhập *
                        </label>
                        <input type="text" id="username" name="username" required
                               value="<?= $_SESSION['old']['username'] ?? '' ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email *
                        </label>
                        <input type="email" id="email" name="email" required
                               value="<?= $_SESSION['old']['email'] ?? '' ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Mật khẩu *
                        </label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                               placeholder="Ít nhất 6 ký tự">
                    </div>

                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Họ và tên *
                        </label>
                        <input type="text" id="full_name" name="full_name" required
                               value="<?= $_SESSION['old']['full_name'] ?? '' ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Số điện thoại *
                        </label>
                        <input type="tel" id="phone" name="phone" required
                               value="<?= $_SESSION['old']['phone'] ?? '' ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
            </div>

            <!-- Thông tin bác sĩ -->
            <div class="border-b pb-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Thông tin chuyên môn</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">
                            Chuyên khoa *
                        </label>
                        <select id="specialization" name="specialization" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="">-- Chọn chuyên khoa --</option>
                            <?php foreach ($specializations as $spec): ?>
                            <option value="<?= htmlspecialchars($spec['name']) ?>"
                                    <?= (($_SESSION['old']['specialization'] ?? '') === $spec['name']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($spec['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Số giấy phép hành nghề *
                        </label>
                        <input type="text" id="license_number" name="license_number" required
                               value="<?= $_SESSION['old']['license_number'] ?? '' ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label for="qualification" class="block text-sm font-medium text-gray-700 mb-2">
                            Trình độ
                        </label>
                        <input type="text" id="qualification" name="qualification"
                               value="<?= $_SESSION['old']['qualification'] ?? '' ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                               placeholder="Ví dụ: Bác sĩ chuyên khoa II">
                    </div>

                    <div>
                        <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-2">
                            Số năm kinh nghiệm
                        </label>
                        <input type="number" id="experience_years" name="experience_years" min="0"
                               value="<?= $_SESSION['old']['experience_years'] ?? '0' ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label for="consultation_fee" class="block text-sm font-medium text-gray-700 mb-2">
                            Phí khám (VNĐ)
                        </label>
                        <input type="number" id="consultation_fee" name="consultation_fee" min="0"
                               value="<?= $_SESSION['old']['consultation_fee'] ?? '0' ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label for="available_days" class="block text-sm font-medium text-gray-700 mb-2">
                            Ngày làm việc
                        </label>
                        <input type="text" id="available_days" name="available_days"
                               value="<?= $_SESSION['old']['available_days'] ?? '' ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                               placeholder="Ví dụ: Thứ 2,Thứ 4,Thứ 6">
                    </div>

                    <div>
                        <label for="available_hours" class="block text-sm font-medium text-gray-700 mb-2">
                            Giờ làm việc
                        </label>
                        <input type="text" id="available_hours" name="available_hours"
                               value="<?= $_SESSION['old']['available_hours'] ?? '' ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                               placeholder="Ví dụ: 08:00-17:00">
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 gradient-bg text-white font-semibold py-3 px-6 rounded-lg hover:opacity-90 transition">
                    <i class="fas fa-save mr-2"></i>Lưu bác sĩ
                </button>
                <a href="<?= APP_URL ?>/admin/doctors" 
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
