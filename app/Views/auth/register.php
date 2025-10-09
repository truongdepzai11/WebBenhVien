<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Hospital Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full mb-4">
                <i class="fas fa-hospital text-3xl text-purple-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Hospital Management System</h1>
            <p class="text-purple-200">Tạo tài khoản mới</p>
        </div>

        <!-- Register Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Đăng ký tài khoản</h2>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        <p><?= $_SESSION['error'] ?></p>
                    </div>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="<?= APP_URL ?>/auth/register" method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2"></i>Tên đăng nhập *
                        </label>
                        <input type="text" id="username" name="username" required
                               value="<?= $_SESSION['old']['username'] ?? '' ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Tên đăng nhập">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2"></i>Email *
                        </label>
                        <input type="email" id="email" name="email" required
                               value="<?= $_SESSION['old']['email'] ?? '' ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="email@example.com">
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card mr-2"></i>Họ và tên *
                        </label>
                        <input type="text" id="full_name" name="full_name" required
                               value="<?= $_SESSION['old']['full_name'] ?? '' ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Nguyễn Văn A">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone mr-2"></i>Số điện thoại *
                        </label>
                        <input type="tel" id="phone" name="phone" required
                               value="<?= $_SESSION['old']['phone'] ?? '' ?>"
                               pattern="[0-9]{10,11}"
                               maxlength="11"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Ví dụ: 0912345678">
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2"></i>Mật khẩu *
                        </label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Ít nhất 6 ký tự">
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2"></i>Xác nhận mật khẩu *
                        </label>
                        <input type="password" id="password_confirm" name="password_confirm" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Nhập lại mật khẩu">
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-2"></i>Ngày sinh
                        </label>
                        <input type="date" id="date_of_birth" name="date_of_birth"
                               value="<?= $_SESSION['old']['date_of_birth'] ?? '' ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-venus-mars mr-2"></i>Giới tính
                        </label>
                        <select id="gender" name="gender"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="">Chọn giới tính</option>
                            <option value="male" <?= (($_SESSION['old']['gender'] ?? '') === 'male') ? 'selected' : '' ?>>Nam</option>
                            <option value="female" <?= (($_SESSION['old']['gender'] ?? '') === 'female') ? 'selected' : '' ?>>Nữ</option>
                            <option value="other" <?= (($_SESSION['old']['gender'] ?? '') === 'other') ? 'selected' : '' ?>>Khác</option>
                        </select>
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2"></i>Địa chỉ
                    </label>
                    <textarea id="address" name="address" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                              placeholder="Địa chỉ của bạn"><?= $_SESSION['old']['address'] ?? '' ?></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full gradient-bg text-white font-semibold py-3 px-4 rounded-lg hover:opacity-90 transition duration-200 flex items-center justify-center">
                    <i class="fas fa-user-plus mr-2"></i>
                    Đăng ký
                </button>
            </form>

            <!-- Login Link -->
            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Đã có tài khoản? 
                    <a href="<?= APP_URL ?>/auth/login" class="text-purple-600 hover:text-purple-700 font-semibold">
                        Đăng nhập ngay
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-white text-sm">
            <p>&copy; 2025 Hospital Management System. All rights reserved.</p>
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
            showValidation(phoneInput, 'success', '✓ Format hợp lệ (chưa kiểm tra trùng)');
            validationStatus.phone = true;
        }
        updateSubmitButton();
    }, 500);

    function showValidation(input, type, message) {
        const existingMsg = input.parentElement.querySelector('.validation-message');
        if (existingMsg) existingMsg.remove();
        
        input.classList.remove('border-green-500', 'border-red-500');
        
        if (!type) return;
        
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

    // Initial state
    updateSubmitButton();
    </script>
    
    <?php unset($_SESSION['old']); ?>
</body>
</html>
