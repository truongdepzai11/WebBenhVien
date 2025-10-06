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
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="0912345678">
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
    <?php unset($_SESSION['old']); ?>
</body>
</html>
