<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hospital Management System</title>
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
    <div class="max-w-md w-full">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full mb-4">
                <i class="fas fa-hospital text-3xl text-purple-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Hospital Management System</h1>
            <p class="text-purple-200">Hệ thống quản lý bệnh viện</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Đăng nhập</h2>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        <p><?= $_SESSION['error'] ?></p>
                    </div>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3"></i>
                        <p><?= $_SESSION['success'] ?></p>
                    </div>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <form action="<?= APP_URL ?>/auth/login" method="POST" class="space-y-6">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Tên đăng nhập
                    </label>
                    <input type="text" id="username" name="username" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                           placeholder="Nhập tên đăng nhập">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Mật khẩu
                    </label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                           placeholder="Nhập mật khẩu">
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full gradient-bg text-white font-semibold py-3 px-4 rounded-lg hover:opacity-90 transition duration-200 flex items-center justify-center">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Đăng nhập
                </button>
            </form>

            <!-- Register Link -->
            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Chưa có tài khoản? 
                    <a href="<?= APP_URL ?>/auth/register" class="text-purple-600 hover:text-purple-700 font-semibold">
                        Đăng ký ngay
                    </a>
                </p>
            </div>

            <!-- Demo Accounts -->
            <!-- <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-xs font-semibold text-gray-700 mb-2">Tài khoản demo:</p>
                <div class="text-xs text-gray-600 space-y-1">
                    <p><strong>Admin:</strong> admin / password</p>
                    <p><strong>Bác sĩ:</strong> dr.nguyen / password</p>
                    <p><strong>Bệnh nhân:</strong> patient1 / password</p>
                </div>
            </div> -->
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-white text-sm">
            <p>&copy; 2025 Hospital Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
