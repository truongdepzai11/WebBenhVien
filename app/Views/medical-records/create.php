<?php 
$page_title = 'Tạo Hồ sơ bệnh án';
ob_start(); 
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="<?= APP_URL ?>/medical-records" class="text-purple-600 hover:text-purple-700">
            <i class="fas fa-arrow-left mr-2"></i>Quay lại
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-file-medical mr-2"></i>Tạo Hồ sơ bệnh án
        </h2>

        <form method="POST" action="<?= APP_URL ?>/medical-records/store">
            <!-- Chọn bệnh nhân -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Bệnh nhân <span class="text-red-500">*</span>
                </label>
                <select name="patient_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="">-- Chọn bệnh nhân --</option>
                    <?php foreach ($patients as $patient): ?>
                    <option value="<?= $patient['id'] ?>" <?= ($selectedPatient && $selectedPatient['id'] == $patient['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($patient['full_name']) ?> (<?= htmlspecialchars($patient['patient_code']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Ngày khám -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Ngày khám <span class="text-red-500">*</span>
                </label>
                <input type="date" name="visit_date" required value="<?= date('Y-m-d') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>

            <!-- Triệu chứng -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Triệu chứng <span class="text-red-500">*</span>
                </label>
                <textarea name="symptoms" required rows="3" placeholder="Mô tả triệu chứng của bệnh nhân..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <!-- Chẩn đoán -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Chẩn đoán <span class="text-red-500">*</span>
                </label>
                <textarea name="diagnosis" required rows="3" placeholder="Chẩn đoán bệnh..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <!-- Điều trị -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Phương pháp điều trị <span class="text-red-500">*</span>
                </label>
                <textarea name="treatment" required rows="4" placeholder="Kê đơn thuốc, chỉ định xét nghiệm..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <!-- Ghi chú -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Ghi chú
                </label>
                <textarea name="notes" rows="2" placeholder="Lời dặn, theo dõi..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <!-- Hidden field -->
            <?php if (isset($appointmentId)): ?>
            <input type="hidden" name="appointment_id" value="<?= $appointmentId ?>">
            <?php endif; ?>

            <!-- Actions -->
            <div class="flex space-x-4">
                <a href="<?= APP_URL ?>/medical-records" 
                   class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center">
                    Hủy
                </a>
                <button type="submit" 
                        class="flex-1 px-6 py-3 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                    <i class="fas fa-save mr-2"></i>Lưu hồ sơ
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
