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
                               pattern="[0-9]{10,11}"
                               maxlength="11"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                               placeholder="Ví dụ: 0912345678">
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
                            <option value="<?= $spec['id'] ?>"
                                    <?= (($_SESSION['old']['specialization'] ?? '') == $spec['id']) ? 'selected' : '' ?>>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Giờ làm việc
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="work_start_time" class="block text-xs text-gray-600 mb-1">Từ giờ</label>
                                <input type="time" id="work_start_time" name="work_start_time"
                                       value="<?= $_SESSION['old']['work_start_time'] ?? '08:00' ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label for="work_end_time" class="block text-xs text-gray-600 mb-1">Đến giờ</label>
                                <input type="time" id="work_end_time" name="work_end_time"
                                       value="<?= $_SESSION['old']['work_end_time'] ?? '17:00' ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>
                        <input type="hidden" id="available_hours" name="available_hours" value="">
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

<script>
// Real-time validation
const usernameInput = document.getElementById('username');
const emailInput = document.getElementById('email');
const phoneInput = document.getElementById('phone');
const submitBtn = document.querySelector('button[type="submit"]');

let validationStatus = {
    username: false,
    email: false,
    phone: false
};

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Validate username
const validateUsername = debounce(async function() {
    const username = usernameInput.value.trim();
    if (!username) {
        showValidation(usernameInput, '', '');
        validationStatus.username = false;
        return;
    }
    
    try {
        const response = await fetch(`<?= APP_URL ?>/api/validate/username?username=${encodeURIComponent(username)}`);
        const data = await response.json();
        
        if (data.available) {
            showValidation(usernameInput, 'success', data.message);
            validationStatus.username = true;
        } else {
            showValidation(usernameInput, 'error', data.message);
            validationStatus.username = false;
        }
    } catch (error) {
        console.error('Validation error:', error);
    }
    updateSubmitButton();
}, 500);

// Validate email
const validateEmail = debounce(async function() {
    const email = emailInput.value.trim();
    if (!email) {
        showValidation(emailInput, '', '');
        validationStatus.email = false;
        return;
    }
    
    try {
        const response = await fetch(`<?= APP_URL ?>/api/validate/email?email=${encodeURIComponent(email)}`);
        const data = await response.json();
        
        if (data.available) {
            showValidation(emailInput, 'success', data.message);
            validationStatus.email = true;
        } else {
            showValidation(emailInput, 'error', data.message);
            validationStatus.email = false;
        }
    } catch (error) {
        console.error('Validation error:', error);
    }
    updateSubmitButton();
}, 500);

// Validate phone
const validatePhone = debounce(async function() {
    let phone = phoneInput.value.trim();
    
    // Remove non-numeric characters
    phone = phone.replace(/\D/g, '');
    phoneInput.value = phone;
    
    if (!phone) {
        showValidation(phoneInput, '', '');
        validationStatus.phone = false;
        updateSubmitButton();
        return;
    }
    
    // Validate format: 10-11 số, bắt đầu bằng 03, 07, 09
    const phoneRegex = /^(03|07|09)\d{8,9}$/;
    if (!phoneRegex.test(phone)) {
        let errorMsg = '';
        if (!/^(03|07|09)/.test(phone)) {
            errorMsg = 'Số điện thoại phải bắt đầu bằng 03, 07 hoặc 09';
        } else if (phone.length < 10) {
            errorMsg = 'Số điện thoại phải có 10-11 số (hiện tại: ' + phone.length + ' số)';
        } else if (phone.length > 11) {
            errorMsg = 'Số điện thoại không được quá 11 số (hiện tại: ' + phone.length + ' số)';
        } else {
            errorMsg = 'Số điện thoại không hợp lệ';
        }
        showValidation(phoneInput, 'error', errorMsg);
        validationStatus.phone = false;
        updateSubmitButton();
        return;
    }
    
    // Check duplicate in database
    try {
        const response = await fetch(`<?= APP_URL ?>/api/validate/phone?phone=${encodeURIComponent(phone)}`);
        
        if (!response.ok) {
            throw new Error('API error: ' + response.status);
        }
        
        const data = await response.json();
        
        if (data.available) {
            showValidation(phoneInput, 'success', '✓ Số điện thoại hợp lệ');
            validationStatus.phone = true;
        } else {
            showValidation(phoneInput, 'error', 'Số điện thoại đã được sử dụng. Vui lòng chọn số khác');
            validationStatus.phone = false;
        }
    } catch (error) {
        console.error('Phone validation error:', error);
        // Nếu API lỗi, vẫn cho phép submit (chỉ validate format)
        showValidation(phoneInput, 'success', '✓ Format hợp lệ (chưa kiểm tra trùng)');
        validationStatus.phone = true;
    }
    updateSubmitButton();
}, 500);

function showValidation(input, type, message) {
    // Remove existing validation message
    const existingMsg = input.parentElement.querySelector('.validation-message');
    if (existingMsg) existingMsg.remove();
    
    // Reset border
    input.classList.remove('border-green-500', 'border-red-500');
    
    if (!type) return;
    
    // Add new validation message
    const msgDiv = document.createElement('div');
    msgDiv.className = 'validation-message text-sm mt-1';
    
    if (type === 'success') {
        msgDiv.className += ' text-green-600';
        msgDiv.innerHTML = `<i class="fas fa-check-circle mr-1"></i>${message}`;
        input.classList.add('border-green-500');
    } else if (type === 'error') {
        msgDiv.className += ' text-red-600';
        msgDiv.innerHTML = `<i class="fas fa-times-circle mr-1"></i>${message}`;
        input.classList.add('border-red-500');
    }
    
    input.parentElement.appendChild(msgDiv);
}

function updateSubmitButton() {
    const allValid = validationStatus.username && validationStatus.email && validationStatus.phone;
    submitBtn.disabled = !allValid;
    if (!allValid) {
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

// Event listeners
usernameInput.addEventListener('input', validateUsername);
emailInput.addEventListener('input', validateEmail);
phoneInput.addEventListener('input', validatePhone);

// Combine work hours before submit
document.querySelector('form').addEventListener('submit', function(e) {
    const startTime = document.getElementById('work_start_time').value;
    const endTime = document.getElementById('work_end_time').value;
    
    if (startTime && endTime) {
        document.getElementById('available_hours').value = startTime + '-' + endTime;
    }
});

// Initial state
updateSubmitButton();
</script>

<?php 
unset($_SESSION['old']);
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
