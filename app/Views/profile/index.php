<?php 
$page_title = 'Thông tin cá nhân';
ob_start(); 
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h3 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-user-circle mr-2"></i>Thông tin cá nhân
        </h3>
        <a href="<?= APP_URL ?>/profile/change-password" 
           class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-key mr-2"></i>Đổi mật khẩu
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <form action="<?= APP_URL ?>/profile/update" method="POST" class="space-y-6">
            <!-- Thông tin tài khoản -->
            <div class="border-b pb-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-id-card mr-2"></i>Thông tin tài khoản
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tên đăng nhập
                        </label>
                        <input type="text" value="<?= htmlspecialchars($userDetails['username']) ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" 
                               disabled>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Vai trò
                        </label>
                        <input type="text" value="<?= ucfirst($userDetails['role']) ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" 
                               disabled>
                    </div>

                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Họ và tên *
                        </label>
                        <input type="text" id="full_name" name="full_name" required
                               value="<?= htmlspecialchars($userDetails['full_name']) ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email *
                        </label>
                        <input type="email" id="email" name="email" required
                               value="<?= htmlspecialchars($userDetails['email']) ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Số điện thoại *
                        </label>
                        <input type="tel" id="phone" name="phone" required
                               value="<?= htmlspecialchars($userDetails['phone']) ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
            </div>

            <?php if (Auth::isPatient() && isset($profile)): ?>
            <!-- Thông tin bệnh nhân -->
            <div class="border-b pb-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-notes-medical mr-2"></i>Thông tin y tế
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Mã bệnh nhân
                        </label>
                        <input type="text" value="<?= htmlspecialchars($profile['patient_code']) ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" 
                               disabled>
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                            Ngày sinh
                        </label>
                        <input type="date" id="date_of_birth" name="date_of_birth"
                               value="<?= $profile['date_of_birth'] ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                            Giới tính
                        </label>
                        <select id="gender" name="gender"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="">Chọn giới tính</option>
                            <option value="male" <?= $profile['gender'] === 'male' ? 'selected' : '' ?>>Nam</option>
                            <option value="female" <?= $profile['gender'] === 'female' ? 'selected' : '' ?>>Nữ</option>
                            <option value="other" <?= $profile['gender'] === 'other' ? 'selected' : '' ?>>Khác</option>
                        </select>
                    </div>

                    <div>
                        <label for="blood_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Nhóm máu
                        </label>
                        <select id="blood_type" name="blood_type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="">Chọn nhóm máu</option>
                            <option value="A+" <?= $profile['blood_type'] === 'A+' ? 'selected' : '' ?>>A+</option>
                            <option value="A-" <?= $profile['blood_type'] === 'A-' ? 'selected' : '' ?>>A-</option>
                            <option value="B+" <?= $profile['blood_type'] === 'B+' ? 'selected' : '' ?>>B+</option>
                            <option value="B-" <?= $profile['blood_type'] === 'B-' ? 'selected' : '' ?>>B-</option>
                            <option value="AB+" <?= $profile['blood_type'] === 'AB+' ? 'selected' : '' ?>>AB+</option>
                            <option value="AB-" <?= $profile['blood_type'] === 'AB-' ? 'selected' : '' ?>>AB-</option>
                            <option value="O+" <?= $profile['blood_type'] === 'O+' ? 'selected' : '' ?>>O+</option>
                            <option value="O-" <?= $profile['blood_type'] === 'O-' ? 'selected' : '' ?>>O-</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Địa chỉ
                        </label>
                        <textarea id="address" name="address" rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"><?= htmlspecialchars($profile['address'] ?? '') ?></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">
                            Dị ứng (nếu có)
                        </label>
                        <textarea id="allergies" name="allergies" rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                  placeholder="Ví dụ: Dị ứng penicillin, hải sản..."><?= htmlspecialchars($profile['allergies'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label for="emergency_contact" class="block text-sm font-medium text-gray-700 mb-2">
                            Người liên hệ khẩn cấp
                        </label>
                        <input type="text" id="emergency_contact" name="emergency_contact"
                               value="<?= htmlspecialchars($profile['emergency_contact'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label for="emergency_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            SĐT người liên hệ khẩn cấp
                        </label>
                        <input type="tel" id="emergency_phone" name="emergency_phone"
                               value="<?= htmlspecialchars($profile['emergency_phone'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Buttons -->
            <div class="flex gap-4 pt-4">
                <button type="submit" 
                        class="flex-1 gradient-bg text-white font-semibold py-3 px-6 rounded-lg hover:opacity-90 transition">
                    <i class="fas fa-save mr-2"></i>Lưu thay đổi
                </button>
                <a href="<?= APP_URL ?>/dashboard" 
                   class="flex-1 text-center bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i>Hủy
                </a>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
