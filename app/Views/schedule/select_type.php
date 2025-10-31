<?php 
$page_title = 'Đăng ký khám Walk-in';
ob_start(); 
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-user-plus mr-2"></i>Đăng ký khám Walk-in
        </h2>

        <div class="mb-8">
            <label class="block text-lg font-medium text-gray-700 mb-4">
                Chọn loại khám <span class="text-red-500">*</span>
            </label>
            
            <div class="space-y-4">
                <!-- Khám thường -->
                <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-purple-500 transition cursor-pointer"
                     onclick="selectType('regular')">
                    <label class="flex items-start cursor-pointer">
                        <input type="radio" name="appointment_type" value="regular" id="typeRegular"
                               class="mt-1 mr-4 w-5 h-5 text-purple-600">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-stethoscope text-blue-600 text-2xl mr-3"></i>
                                <h3 class="text-xl font-bold text-gray-800">Khám thường</h3>
                            </div>
                            <p class="text-gray-600">
                                Đăng ký khám bệnh thông thường với bác sĩ chuyên khoa. 
                                Chọn bác sĩ, thời gian và đặt lịch hẹn.
                            </p>
                            <div class="mt-3 flex items-center text-sm text-gray-500">
                                <i class="fas fa-clock mr-2"></i>
                                <span>Thời gian: Theo lịch bác sĩ</span>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Khám theo gói -->
                <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-purple-500 transition cursor-pointer"
                     onclick="selectType('package')">
                    <label class="flex items-start cursor-pointer">
                        <input type="radio" name="appointment_type" value="package" id="typePackage"
                               class="mt-1 mr-4 w-5 h-5 text-purple-600">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-box-open text-green-600 text-2xl mr-3"></i>
                                <h3 class="text-xl font-bold text-gray-800">Khám theo gói</h3>
                            </div>
                            <p class="text-gray-600">
                                Đăng ký gói khám sức khỏe tổng quát hoặc chuyên sâu. 
                                Bao gồm nhiều dịch vụ khám và xét nghiệm.
                            </p>
                            <div class="mt-3 flex items-center text-sm text-gray-500">
                                <i class="fas fa-calendar-check mr-2"></i>
                                <span>Thời gian: Linh hoạt theo gói</span>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Ghi chú -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
                <div>
                    <p class="font-semibold text-blue-800 mb-1">Lưu ý:</p>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• <strong>Khám thường:</strong> Chọn bác sĩ và thời gian cụ thể</li>
                        <li>• <strong>Khám theo gói:</strong> Chọn gói khám, hệ thống sẽ phân công bác sĩ sau</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex space-x-4">
            <a href="<?= APP_URL ?>/schedule" 
               class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại
            </a>
            <button type="button" id="btnContinue" disabled
                    class="flex-1 px-6 py-3 gradient-bg text-white rounded-lg hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-arrow-right mr-2"></i>Tiếp tục
            </button>
        </div>
    </div>
</div>

<script>
function selectType(type) {
    // Check radio button
    if (type === 'regular') {
        document.getElementById('typeRegular').checked = true;
    } else {
        document.getElementById('typePackage').checked = true;
    }
    
    // Enable continue button
    document.getElementById('btnContinue').disabled = false;
    
    // Add visual feedback
    document.querySelectorAll('.border-2').forEach(el => {
        el.classList.remove('border-purple-500', 'bg-purple-50');
        el.classList.add('border-gray-200');
    });
    event.currentTarget.classList.remove('border-gray-200');
    event.currentTarget.classList.add('border-purple-500', 'bg-purple-50');
}

// Continue button click
document.getElementById('btnContinue').addEventListener('click', function() {
    const selectedType = document.querySelector('input[name="appointment_type"]:checked');
    
    if (!selectedType) {
        alert('Vui lòng chọn loại khám!');
        return;
    }
    
    if (selectedType.value === 'regular') {
        // Chuyển đến trang lịch bác sĩ
        window.location.href = '<?= APP_URL ?>/schedule';
    } else {
        // Chuyển đến trang đăng ký gói
        window.location.href = '<?= APP_URL ?>/schedule/register-package';
    }
});

// Allow clicking on the card to select
document.querySelectorAll('.border-2').forEach(card => {
    card.addEventListener('click', function() {
        const radio = this.querySelector('input[type="radio"]');
        radio.checked = true;
        selectType(radio.value);
    });
});
</script>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
