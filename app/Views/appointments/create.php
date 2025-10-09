<?php 
$page_title = 'Đặt Lịch Khám';
ob_start(); 
?>

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-calendar-plus mr-2"></i>Đặt Lịch Khám Mới
        </h3>
        <p class="text-gray-600 mt-2">Vui lòng điền đầy đủ thông tin để đặt lịch khám</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <form action="<?= APP_URL ?>/appointments/store" method="POST" class="space-y-6">
            <!-- Chọn chuyên khoa -->
            <?php if (Auth::isPatient() && isset($eligible_specializations)): ?>
            <div>
                <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-stethoscope mr-2"></i>Chọn chuyên khoa
                </label>
                <select id="specialization" name="specialization" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        onchange="filterDoctors(this.value)">
                    <option value="">-- Tất cả chuyên khoa --</option>
                    <?php foreach ($eligible_specializations as $spec): ?>
                    <option value="<?= htmlspecialchars($spec['name']) ?>">
                        <?= htmlspecialchars($spec['name']) ?>
                        <?php if ($spec['min_age'] > 0 || $spec['max_age'] < 150): ?>
                            (<?= $spec['min_age'] ?>-<?= $spec['max_age'] ?> tuổi)
                        <?php endif; ?>
                        <?php if ($spec['gender_requirement'] !== 'both'): ?>
                            - <?= $spec['gender_requirement'] === 'male' ? 'Nam' : 'Nữ' ?>
                        <?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle"></i> Chỉ hiển thị các chuyên khoa phù hợp với độ tuổi và giới tính của bạn
                </p>
            </div>
            <?php endif; ?>

            <!-- Chọn bác sĩ -->
            <div>
                <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user-md mr-2"></i>Chọn bác sĩ *
                </label>
                <select id="doctor_id" name="doctor_id" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">-- Chọn bác sĩ --</option>
                    <?php foreach ($doctors as $doctor): ?>
                    <option value="<?= $doctor['id'] ?>" 
                            data-specialization="<?= htmlspecialchars($doctor['specialization']) ?>"
                            <?= (isset($_GET['doctor_id']) && $_GET['doctor_id'] == $doctor['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($doctor['full_name']) ?> - <?= htmlspecialchars($doctor['specialization']) ?>
                        (<?= number_format($doctor['consultation_fee']) ?> VNĐ)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <script>
            function filterDoctors(specialization) {
                const doctorSelect = document.getElementById('doctor_id');
                const options = doctorSelect.querySelectorAll('option');
                
                options.forEach(option => {
                    if (option.value === '') {
                        option.style.display = 'block';
                        return;
                    }
                    
                    const doctorSpec = option.getAttribute('data-specialization');
                    if (!specialization || doctorSpec === specialization) {
                        option.style.display = 'block';
                    } else {
                        option.style.display = 'none';
                    }
                });
                
                doctorSelect.value = '';
            }
            </script>

            <!-- Ngày khám -->
            <div>
                <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar mr-2"></i>Ngày khám *
                </label>
                <input type="date" id="appointment_date" name="appointment_date" required
                       min="<?= date('Y-m-d') ?>"
                       value="<?= $_SESSION['old']['appointment_date'] ?? '' ?>"
                       onchange="validateDateTime()"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle"></i> Chỉ được chọn ngày từ hôm nay trở đi
                </p>
            </div>

            <!-- Giờ khám -->
            <div>
                <label for="appointment_time" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-clock mr-2"></i>Giờ khám *
                </label>
                <select id="appointment_time" name="appointment_time" required
                        onchange="validateDateTime()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">-- Chọn giờ --</option>
                    <option value="08:00:00">08:00</option>
                    <option value="08:30:00">08:30</option>
                    <option value="09:00:00">09:00</option>
                    <option value="09:30:00">09:30</option>
                    <option value="10:00:00">10:00</option>
                    <option value="10:30:00">10:30</option>
                    <option value="11:00:00">11:00</option>
                    <option value="13:00:00">13:00</option>
                    <option value="13:30:00">13:30</option>
                    <option value="14:00:00">14:00</option>
                    <option value="14:30:00">14:30</option>
                    <option value="15:00:00">15:00</option>
                    <option value="15:30:00">15:30</option>
                    <option value="16:00:00">16:00</option>
                    <option value="16:30:00">16:30</option>
                </select>
            </div>

            <!-- Lý do khám -->
            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-notes-medical mr-2"></i>Lý do khám *
                </label>
                <textarea id="reason" name="reason" rows="4" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                          placeholder="Mô tả triệu chứng hoặc lý do khám bệnh..."><?= $_SESSION['old']['reason'] ?? '' ?></textarea>
            </div>

            <!-- Ghi chú -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-comment mr-2"></i>Ghi chú thêm (nếu có)
                </label>
                <textarea id="notes" name="notes" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                          placeholder="Thông tin bổ sung..."><?= $_SESSION['old']['notes'] ?? '' ?></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 gradient-bg text-white font-semibold py-3 px-6 rounded-lg hover:opacity-90 transition">
                    <i class="fas fa-check mr-2"></i>Xác nhận đặt lịch
                </button>
                <a href="<?= APP_URL ?>/appointments" 
                   class="flex-1 text-center bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i>Hủy
                </a>
            </div>
        </form>
    </div>

    <!-- Lưu ý -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <div class="flex">
            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
            <div>
                <h4 class="font-semibold text-blue-800 mb-2">Lưu ý:</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Vui lòng đến sớm 15 phút trước giờ hẹn</li>
                    <li>• Mang theo giấy tờ tùy thân và sổ khám bệnh (nếu có)</li>
                    <li>• Nếu cần hủy lịch, vui lòng thông báo trước 24 giờ</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Validate thời gian không được trong quá khứ
function validateDateTime() {
    const dateInput = document.getElementById('appointment_date');
    const timeInput = document.getElementById('appointment_time');
    
    if (!dateInput.value || !timeInput.value) return;
    
    const selectedDate = new Date(dateInput.value + ' ' + timeInput.value);
    const now = new Date();
    
    if (selectedDate <= now) {
        alert('Không thể đặt lịch khám trong quá khứ. Vui lòng chọn thời gian sau ' + now.toLocaleString('vi-VN'));
        timeInput.value = '';
        return false;
    }
    
    return true;
}

// Validate khi submit form
document.querySelector('form').addEventListener('submit', function(e) {
    if (!validateDateTime()) {
        e.preventDefault();
        return false;
    }
});
</script>

<?php 
unset($_SESSION['old']);
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
