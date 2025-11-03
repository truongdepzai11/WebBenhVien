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
            
            <!-- Chọn loại khám -->
            <div class="border-b pb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    <i class="fas fa-clipboard-list mr-2"></i>Loại khám *
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-purple-500 transition <?= empty($selected_package) ? 'border-purple-500 bg-purple-50' : 'border-gray-300' ?>">
                        <input type="radio" name="appointment_type_choice" value="regular" 
                               <?= empty($selected_package) ? 'checked' : '' ?>
                               onchange="toggleAppointmentType('regular')"
                               class="mr-3">
                        <div>
                            <div class="font-semibold text-gray-800">
                                <i class="fas fa-stethoscope mr-2"></i>Khám thường
                            </div>
                            <div class="text-sm text-gray-500">Khám bệnh theo triệu chứng</div>
                        </div>
                    </label>
                    
                    <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-purple-500 transition <?= !empty($selected_package) ? 'border-purple-500 bg-purple-50' : 'border-gray-300' ?>">
                        <input type="radio" name="appointment_type_choice" value="package"
                               <?= !empty($selected_package) ? 'checked' : '' ?>
                               onchange="toggleAppointmentType('package')"
                               class="mr-3">
                        <div>
                            <div class="font-semibold text-gray-800">
                                <i class="fas fa-box-open mr-2"></i>Khám theo gói
                            </div>
                            <div class="text-sm text-gray-500">Khám sức khỏe tổng quát</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Chọn gói khám (ẩn nếu chọn khám thường) -->
            <div id="package_selection" style="display: <?= !empty($selected_package) ? 'block' : 'none' ?>">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-box-open mr-2"></i>Chọn gói khám *
                </label>
                <select name="package_id" id="package_id" 
                        onchange="updatePackageInfo(this.value)"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="">-- Chọn gói khám --</option>
                    <?php if (isset($eligible_packages)): ?>
                        <?php foreach ($eligible_packages as $pkg): ?>
                        <option value="<?= $pkg['id'] ?>" 
                                data-name="<?= htmlspecialchars($pkg['name']) ?>"
                                <?= ($selected_package && $selected_package['id'] == $pkg['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pkg['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                
                <!-- Hiển thị thông tin gói -->
                <div id="package_info" class="mt-4 p-4 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg border border-purple-200" style="display:<?= !empty($selected_package) ? 'block' : 'none' ?>">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800 mb-2" id="package_name">
                                <?= $selected_package['name'] ?? '' ?>
                            </h4>
                            <div class="text-sm text-gray-600 mb-3" id="package_price">
                                <span class="text-xs text-gray-500">Giá sẽ hiển thị sau khi chọn dịch vụ</span>
                            </div>
                        </div>
                        <a href="#" id="package_detail_link" target="_blank" class="text-purple-600 hover:text-purple-800 text-sm">
                            <i class="fas fa-external-link-alt mr-1"></i>Xem chi tiết
                        </a>
                    </div>
                </div>
                
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle"></i> Gói khám bao gồm nhiều dịch vụ xét nghiệm và khám chuyên khoa
                </p>
                
                <!-- Danh sách dịch vụ trong gói (cho phép chọn/bỏ) -->
                <div id="package_services_list" class="mt-6" style="display:none">
                    <h4 class="font-semibold text-gray-800 mb-3">
                        <i class="fas fa-list-check mr-2"></i>Chọn dịch vụ khám
                    </h4>
                    <div id="services_container" class="space-y-2 max-h-96 overflow-y-auto">
                        <!-- Services will be loaded here via AJAX -->
                    </div>
                    
                    <!-- Tổng giá -->
                    <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-700">Tổng chi phí:</span>
                            <span class="text-2xl font-bold text-green-600" id="total_price_display">0 đ</span>
                        </div>
                        <input type="hidden" name="total_price" id="total_price_input" value="0">
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle"></i> Giá đã bao gồm tất cả dịch vụ được chọn
                        </p>
                    </div>
                </div>
            </div>

            <!-- Chọn chuyên khoa (chỉ hiện khi khám thường) -->
            <div id="specialization_selection" style="display: <?= empty($selected_package) ? 'block' : 'none' ?>">
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
            </div>

            <!-- Chọn bác sĩ (CHỈ cho khám thường, ẨN khi đặt gói) -->
            <div id="doctor_selection" style="display: <?= empty($selected_package) ? 'block' : 'none' ?>">
                <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user-md mr-2"></i>Chọn bác sĩ <span id="doctor_required_label">*</span>
                </label>
                <select id="doctor_id" name="doctor_id"
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
                <p class="text-xs text-gray-500 mt-1" id="doctor_note">
                    <i class="fas fa-info-circle"></i> <span id="doctor_note_text">Chọn bác sĩ khám chính</span>
                </p>
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

            <!-- Giờ khám (CHỈ cho khám thường, ẨN khi đặt gói) -->
            <div id="time_selection" style="display: <?= empty($selected_package) ? 'block' : 'none' ?>">
                <label for="appointment_time" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-clock mr-2"></i>Giờ khám <span id="time_required_label">*</span>
                </label>
                <select id="appointment_time" name="appointment_time"
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
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle"></i> Giờ khám sẽ được sắp xếp bởi lễ tân khi đặt gói
                </p>
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
// Toggle giữa khám thường và khám theo gói
function toggleAppointmentType(type) {
    const packageSelection = document.getElementById('package_selection');
    const specializationSelection = document.getElementById('specialization_selection');
    const doctorSelection = document.getElementById('doctor_selection');
    const timeSelection = document.getElementById('time_selection');
    const packageIdInput = document.getElementById('package_id');
    const doctorSelect = document.getElementById('doctor_id');
    const timeSelect = document.getElementById('appointment_time');
    const timeRequired = document.getElementById('time_required_label');
    
    if (type === 'package') {
        // Hiện: Chọn gói
        packageSelection.style.display = 'block';
        
        // Ẩn: Chuyên khoa, Bác sĩ, Giờ khám
        specializationSelection.style.display = 'none';
        doctorSelection.style.display = 'none';
        timeSelection.style.display = 'none';
        
        // Bỏ required
        doctorSelect.removeAttribute('required');
        timeSelect.removeAttribute('required');
        
        // Reset giá trị
        doctorSelect.value = '';
        timeSelect.value = '';
    } else {
        // Ẩn: Chọn gói
        packageSelection.style.display = 'none';
        packageIdInput.value = '';
        document.getElementById('package_info').style.display = 'none';
        
        // Hiện: Chuyên khoa, Bác sĩ, Giờ khám
        specializationSelection.style.display = 'block';
        doctorSelection.style.display = 'block';
        timeSelection.style.display = 'block';
        
        // Thêm required
        doctorSelect.setAttribute('required', 'required');
        timeSelect.setAttribute('required', 'required');
    }
}

// Cập nhật thông tin gói khám và load dịch vụ
function updatePackageInfo(packageId) {
    const packageInfo = document.getElementById('package_info');
    const packageName = document.getElementById('package_name');
    const packagePrice = document.getElementById('package_price');
    const packageLink = document.getElementById('package_detail_link');
    const servicesList = document.getElementById('package_services_list');
    
    if (!packageId) {
        packageInfo.style.display = 'none';
        servicesList.style.display = 'none';
        return;
    }
    
    const select = document.getElementById('package_id');
    const option = select.options[select.selectedIndex];
    
    const name = option.dataset.name;
    
    packageName.textContent = name;
    packagePrice.innerHTML = '<span class="text-xs text-gray-500">Đang tải dịch vụ...</span>';
    packageLink.href = '<?= APP_URL ?>/packages/' + packageId;
    packageInfo.style.display = 'block';
    
    // Load danh sách dịch vụ
    loadPackageServices(packageId);
}

// Load danh sách dịch vụ trong gói
function loadPackageServices(packageId) {
    const servicesContainer = document.getElementById('services_container');
    const servicesList = document.getElementById('package_services_list');
    
    // Show loading
    servicesContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-purple-600"></i> Đang tải dịch vụ...</div>';
    servicesList.style.display = 'block';
    
    // Fetch services via AJAX
    fetch('<?= APP_URL ?>/api/package-services/' + packageId)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.services.length > 0) {
                renderServices(data.services);
            } else {
                servicesContainer.innerHTML = '<div class="text-center py-4 text-gray-500">Không có dịch vụ nào</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            servicesContainer.innerHTML = '<div class="text-center py-4 text-red-500">Lỗi tải dịch vụ</div>';
        });
}

// Render danh sách dịch vụ
function renderServices(services) {
    const servicesContainer = document.getElementById('services_container');
    let html = '';
    
    services.forEach(service => {
        const isRequired = service.is_required == 1;
        const price = parseFloat(service.service_price || 0);
        
        html += `
            <div class="flex items-start p-3 border rounded-lg hover:bg-gray-50 ${isRequired ? 'bg-blue-50 border-blue-200' : 'border-gray-200'}">
                <input type="checkbox" 
                       name="selected_services[]" 
                       value="${service.id}" 
                       data-price="${price}"
                       data-required="${isRequired ? '1' : '0'}"
                       ${isRequired ? 'checked disabled' : 'checked'}
                       onchange="calculateTotalPrice()"
                       class="mt-1 mr-3 w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                       id="service_${service.id}">
                <label for="service_${service.id}" class="flex-1 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="font-medium text-gray-800">${service.service_name}</div>
                            ${service.notes ? `<div class="text-xs text-gray-500 mt-1">${service.notes}</div>` : ''}
                        </div>
                        <div class="ml-4 text-right">
                            <div class="font-bold text-purple-600">${price.toLocaleString('vi-VN')} đ</div>
                            ${isRequired ? '<span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded">Bắt buộc</span>' : '<span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">Tùy chọn</span>'}
                        </div>
                    </div>
                </label>
            </div>
        `;
    });
    
    servicesContainer.innerHTML = html;
    calculateTotalPrice();
}

// Tính tổng giá
function calculateTotalPrice() {
    const checkboxes = document.querySelectorAll('input[name="selected_services[]"]:checked');
    let total = 0;
    
    checkboxes.forEach(checkbox => {
        total += parseFloat(checkbox.dataset.price || 0);
    });
    
    document.getElementById('total_price_display').textContent = total.toLocaleString('vi-VN') + ' đ';
    document.getElementById('total_price_input').value = total;
}

// Validate thời gian không được trong quá khứ
function validateDateTime() {
    const dateInput = document.getElementById('appointment_date');
    const timeInput = document.getElementById('appointment_time');
    
    // Nếu chưa chọn ngày/giờ, cho phép submit (server sẽ validate)
    if (!dateInput || !timeInput || !dateInput.value || !timeInput.value) {
        return true;
    }
    
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

// Initialize nếu có package được chọn từ URL
<?php if (!empty($selected_package)): ?>
document.addEventListener('DOMContentLoaded', function() {
    updatePackageInfo(<?= $selected_package['id'] ?>);
});
<?php endif; ?>
</script>

<?php 
unset($_SESSION['old']);
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
