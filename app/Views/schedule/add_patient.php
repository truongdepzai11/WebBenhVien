<?php 
$page_title = 'Thêm Bệnh nhân Walk-in';
ob_start(); 
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="<?= APP_URL ?>/schedule?doctor_id=<?= $doctor['id'] ?>&date=<?= $date ?>" class="text-purple-600 hover:text-purple-700">
            <i class="fas fa-arrow-left mr-2"></i>Quay lại lịch làm việc
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-user-plus mr-2"></i>Thêm Bệnh nhân Walk-in
        </h2>

        <!-- Thông tin slot -->
        <div class="bg-purple-50 p-6 rounded-lg mb-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Bác sĩ</p>
                    <p class="font-bold text-gray-900"><?= htmlspecialchars($doctor['full_name']) ?></p>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($doctor['specialization']) ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Thời gian</p>
                    <p class="font-bold text-gray-900"><?= date('d/m/Y', strtotime($date)) ?></p>
                    <p class="text-sm text-gray-500"><?= date('H:i', strtotime($time)) ?></p>
                </div>
            </div>
        </div>

        <form method="POST" action="<?= APP_URL ?>/schedule/store-walk-in">
            <input type="hidden" name="doctor_id" value="<?= $doctor['id'] ?>">
            <input type="hidden" name="appointment_date" value="<?= $date ?>">
            <input type="hidden" name="appointment_time" value="<?= $time ?>">

            <!-- Chọn loại bệnh nhân -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Loại bệnh nhân <span class="text-red-500">*</span>
                </label>
                <div class="flex space-x-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="patient_type" value="existing" checked
                               onchange="togglePatientForm()"
                               class="mr-2">
                        <span>Bệnh nhân cũ (đã có hồ sơ)</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="patient_type" value="new"
                               onchange="togglePatientForm()"
                               class="mr-2">
                        <span>Bệnh nhân mới (lần đầu khám)</span>
                    </label>
                </div>
            </div>

            <!-- Form chọn bệnh nhân cũ -->
            <div id="existingPatientForm" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Chọn bệnh nhân <span class="text-red-500">*</span>
                </label>
                <select name="patient_id" id="patientSelect"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="">-- Chọn bệnh nhân --</option>
                    <?php foreach ($patients as $patient): ?>
                    <option value="<?= $patient['id'] ?>">
                        <?= htmlspecialchars($patient['full_name']) ?> 
                        (<?= htmlspecialchars($patient['patient_code']) ?>) - 
                        <?= htmlspecialchars($patient['phone']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Form tạo bệnh nhân mới -->
            <div id="newPatientForm" class="hidden mb-6 p-6 bg-blue-50 rounded-lg border-2 border-blue-200">
                <h3 class="font-bold text-gray-800 mb-4">
                    <i class="fas fa-user-plus mr-2"></i>Thông tin bệnh nhân mới
                </h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Họ tên <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="new_patient_name" id="newPatientName"
                               placeholder="Nguyễn Văn A"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Ngày sinh <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="new_patient_dob" id="newPatientDob"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" name="new_patient_email" id="newPatientEmail"
                               placeholder="example@gmail.com (không bắt buộc)"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <p class="text-xs text-gray-500 mt-1">Nếu có email, bệnh nhân có thể đăng nhập sau</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Số điện thoại <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="new_patient_phone" id="newPatientPhone"
                               placeholder="0912345678"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Giới tính <span class="text-red-500">*</span>
                        </label>
                        <select name="new_patient_gender" id="newPatientGender"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="male">Nam</option>
                            <option value="female">Nữ</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Địa chỉ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="new_patient_address" id="newPatientAddress"
                               placeholder="123 Đường ABC, Quận 1, TP.HCM"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
            </div>

            <!-- Lý do khám -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Lý do khám <span class="text-red-500">*</span>
                </label>
                <input type="text" name="reason" required placeholder="Ví dụ: Khám tổng quát, Đau đầu..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>

            <!-- Triệu chứng -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Triệu chứng
                </label>
                <textarea name="symptoms" rows="3" placeholder="Mô tả triệu chứng của bệnh nhân..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <!-- Ghi chú -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
                    <div>
                        <p class="font-semibold text-blue-800 mb-1">Lưu ý về Walk-in:</p>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• Lịch hẹn sẽ tự động được <strong>xác nhận</strong></li>
                            <li>• Phí khám: <strong><?= number_format($doctor['consultation_fee']) ?> VNĐ</strong></li>
                            <li>• Sau khám, vui lòng tạo hóa đơn cho bệnh nhân</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex space-x-4">
                <a href="<?= APP_URL ?>/schedule?doctor_id=<?= $doctor['id'] ?>&date=<?= $date ?>" 
                   class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center">
                    Hủy
                </a>
                <button type="submit" 
                        class="flex-1 px-6 py-3 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                    <i class="fas fa-check mr-2"></i>Xác nhận thêm
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePatientForm() {
    const patientType = document.querySelector('input[name="patient_type"]:checked').value;
    const existingForm = document.getElementById('existingPatientForm');
    const newForm = document.getElementById('newPatientForm');
    const patientSelect = document.getElementById('patientSelect');
    
    if (patientType === 'existing') {
        existingForm.classList.remove('hidden');
        newForm.classList.add('hidden');
        patientSelect.required = true;
        
        // Bỏ required cho form mới
        document.getElementById('newPatientName').required = false;
        document.getElementById('newPatientDob').required = false;
        document.getElementById('newPatientPhone').required = false;
        document.getElementById('newPatientAddress').required = false;
    } else {
        existingForm.classList.add('hidden');
        newForm.classList.remove('hidden');
        patientSelect.required = false;
        
        // Thêm required cho form mới
        document.getElementById('newPatientName').required = true;
        document.getElementById('newPatientDob').required = true;
        document.getElementById('newPatientPhone').required = true;
        document.getElementById('newPatientAddress').required = true;
    }
}
</script>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
