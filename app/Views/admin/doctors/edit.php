<?php 
$page_title = 'Sửa thông tin Bác sĩ';
ob_start(); 
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-edit mr-2"></i>Sửa thông tin Bác sĩ
        </h3>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <form action="<?= APP_URL ?>/admin/doctors/<?= $doctor['id'] ?>/update" method="POST" class="space-y-6">
            <!-- Thông tin tài khoản -->
            <div class="border-b pb-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Thông tin tài khoản</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tên đăng nhập
                        </label>
                        <input type="text" value="<?= htmlspecialchars($doctor['username']) ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100" disabled>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email *
                        </label>
                        <input type="email" id="email" name="email" required
                               value="<?= htmlspecialchars($doctor['email']) ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Họ và tên *
                        </label>
                        <input type="text" id="full_name" name="full_name" required
                               value="<?= htmlspecialchars($doctor['full_name']) ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Số điện thoại *
                        </label>
                        <input type="tel" id="phone" name="phone" required
                               value="<?= htmlspecialchars($doctor['phone']) ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
            </div>

            <!-- Thông tin bác sĩ -->
            <div class="border-b pb-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Thông tin chuyên môn</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Mã bác sĩ
                        </label>
                        <input type="text" value="<?= htmlspecialchars($doctor['doctor_code']) ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100" disabled>
                    </div>

                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">
                            Chuyên khoa *
                        </label>
                        <select id="specialization" name="specialization" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <?php foreach ($specializations as $spec): ?>
                            <option value="<?= htmlspecialchars($spec['name']) ?>"
                                    <?= ($doctor['specialization'] === $spec['name']) ? 'selected' : '' ?>>
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
                               value="<?= htmlspecialchars($doctor['license_number']) ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label for="qualification" class="block text-sm font-medium text-gray-700 mb-2">
                            Trình độ
                        </label>
                        <input type="text" id="qualification" name="qualification"
                               value="<?= htmlspecialchars($doctor['qualification']) ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-2">
                            Số năm kinh nghiệm
                        </label>
                        <input type="number" id="experience_years" name="experience_years" min="0"
                               value="<?= $doctor['experience_years'] ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label for="consultation_fee" class="block text-sm font-medium text-gray-700 mb-2">
                            Phí khám (VNĐ)
                        </label>
                        <input type="number" id="consultation_fee" name="consultation_fee" min="0"
                               value="<?= $doctor['consultation_fee'] ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label for="available_days" class="block text-sm font-medium text-gray-700 mb-2">
                            Ngày làm việc
                        </label>
                        <input type="text" id="available_days" name="available_days"
                               value="<?= htmlspecialchars($doctor['available_days']) ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label for="available_hours" class="block text-sm font-medium text-gray-700 mb-2">
                            Giờ làm việc
                        </label>
                        <input type="text" id="available_hours" name="available_hours"
                               value="<?= htmlspecialchars($doctor['available_hours']) ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 gradient-bg text-white font-semibold py-3 px-6 rounded-lg hover:opacity-90 transition">
                    <i class="fas fa-save mr-2"></i>Cập nhật
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
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
