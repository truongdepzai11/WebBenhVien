<?php 
$page_title = 'Đăng ký Gói khám Walk-in';
ob_start(); 
?>

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="<?= APP_URL ?>/schedule/select-type" class="text-purple-600 hover:text-purple-700">
            <i class="fas fa-arrow-left mr-2"></i>Quay lại chọn loại khám
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">
            <i class="fas fa-box-open mr-2"></i>Đăng ký Gói khám Walk-in
        </h2>
        <p class="text-gray-600 mb-6">Đăng ký gói khám sức khỏe cho bệnh nhân tại quầy</p>

        <form method="POST" action="<?= APP_URL ?>/schedule/store-package-walkin">
            <!-- Chọn gói khám -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Chọn gói khám <span class="text-red-500">*</span>
                </label>
                <select name="package_id" id="packageSelect" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                        onchange="loadPackageInfo(this.value)">
                    <option value="">-- Chọn gói khám --</option>
                    <?php
                    // Lấy danh sách gói khám
                    require_once APP_PATH . '/Models/HealthPackage.php';
                    $packageModel = new HealthPackage();
                    $packages = $packageModel->getAllActive();
                    foreach ($packages as $pkg):
                    ?>
                    <option value="<?= $pkg['id'] ?>" 
                            data-name="<?= htmlspecialchars($pkg['name']) ?>"
                            data-price="<?= $pkg['price'] ?>">
                        <?= htmlspecialchars($pkg['name']) ?> - <?= number_format($pkg['price']) ?> VNĐ
                    </option>
                    <?php endforeach; ?>
                </select>
                
                <!-- Thông tin gói -->
                <div id="packageInfo" class="hidden mt-4 p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <h4 class="font-semibold text-gray-800 mb-3" id="packageName"></h4>
                    <div class="text-sm text-gray-600 mb-3">
                        <p class="font-medium mb-2">Danh sách dịch vụ:</p>
                        <div id="packageServices" class="space-y-1 ml-4"></div>
                    </div>
                    <div class="pt-3 border-t border-purple-200">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Tổng chi phí:</span>
                            <span class="text-xl font-bold text-purple-600" id="packagePrice">0 đ</span>
                        </div>
                    </div>
                </div>
            </div>

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
                    <?php
                    require_once APP_PATH . '/Models/Patient.php';
                    $patientModel = new Patient();
                    $patients = $patientModel->getAll();
                    foreach ($patients as $patient):
                    ?>
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
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Địa chỉ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="new_patient_address" id="newPatientAddress"
                               placeholder="123 Đường ABC, Quận 1, TP.HCM"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
            </div>

            <!-- Ngày khám -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Ngày khám dự kiến <span class="text-red-500">*</span>
                </label>
                <input type="date" name="appointment_date" required
                       min="<?= date('Y-m-d') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                <p class="text-xs text-gray-500 mt-1">Ngày bắt đầu thực hiện các dịch vụ trong gói</p>
            </div>

            <!-- Lý do khám -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Lý do khám / Ghi chú
                </label>
                <textarea name="reason" rows="3" placeholder="Ghi chú thêm về tình trạng sức khỏe..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <!-- Ghi chú -->
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-green-500 text-xl mr-3 mt-1"></i>
                    <div>
                        <p class="font-semibold text-green-800 mb-1">Lưu ý về Gói khám Walk-in:</p>
                        <ul class="text-sm text-green-700 space-y-1">
                            <li>• Lịch hẹn sẽ tự động được <strong>xác nhận</strong></li>
                            <li>• Bác sĩ sẽ được <strong>phân công</strong> cho từng dịch vụ sau</li>
                            <li>• Bệnh nhân có thể thực hiện các dịch vụ trong nhiều ngày</li>
                            <li>• Sau đăng ký, vui lòng tạo hóa đơn cho bệnh nhân</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex space-x-4">
                <a href="<?= APP_URL ?>/schedule/select-type" 
                   class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center">
                    Hủy
                </a>
                <button type="submit" 
                        class="flex-1 px-6 py-3 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                    <i class="fas fa-check mr-2"></i>Xác nhận đăng ký
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Load thông tin gói khám
async function loadPackageInfo(packageId) {
    const packageInfo = document.getElementById('packageInfo');
    const packageName = document.getElementById('packageName');
    const packageServices = document.getElementById('packageServices');
    const packagePrice = document.getElementById('packagePrice');
    
    if (!packageId) {
        packageInfo.classList.add('hidden');
        return;
    }
    
    try {
        const response = await fetch(`<?= APP_URL ?>/api/package-services/${packageId}`);
        const data = await response.json();
        
        if (data.success) {
            // Hiển thị tên gói
            const selectedOption = document.querySelector(`#packageSelect option[value="${packageId}"]`);
            packageName.textContent = selectedOption.dataset.name;
            
            // Hiển thị dịch vụ
            packageServices.innerHTML = '';
            let totalPrice = 0;
            
            data.services.forEach(service => {
                const price = parseInt(service.service_price) || 0;
                totalPrice += price;
                
                const serviceDiv = document.createElement('div');
                serviceDiv.className = 'flex items-center text-sm';
                serviceDiv.innerHTML = `
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span class="flex-1">${service.service_name}</span>
                    ${service.is_required ? '<span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded ml-2">Bắt buộc</span>' : ''}
                `;
                packageServices.appendChild(serviceDiv);
            });
            
            // Hiển thị tổng giá
            packagePrice.textContent = new Intl.NumberFormat('vi-VN').format(totalPrice) + ' đ';
            
            // Hiện thông tin gói
            packageInfo.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Lỗi load gói khám:', error);
    }
}

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
