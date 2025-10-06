<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Hospital Management System' ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .scroll-smooth {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-gray-50 scroll-smooth">
    
    <!-- Navigation -->
    <nav class="bg-white shadow-md fixed w-full top-0 z-50">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 gradient-bg rounded-lg flex items-center justify-center">
                        <i class="fas fa-hospital text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">HMS</h1>
                        <p class="text-xs text-gray-600">Hospital Management</p>
                    </div>
                </div>
                
                <!-- Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-gray-700 hover:text-purple-600 font-medium transition">Trang chủ</a>
                    <a href="#specializations" class="text-gray-700 hover:text-purple-600 font-medium transition">Chuyên khoa</a>
                    <a href="#doctors" class="text-gray-700 hover:text-purple-600 font-medium transition">Bác sĩ</a>
                    <a href="#services" class="text-gray-700 hover:text-purple-600 font-medium transition">Dịch vụ</a>
                    <a href="#contact" class="text-gray-700 hover:text-purple-600 font-medium transition">Liên hệ</a>
                </div>
                
                <!-- Auth Buttons -->
                <div class="flex items-center space-x-4">
                    <a href="<?= APP_URL ?>/auth/login" 
                       class="px-6 py-2 border-2 border-purple-600 text-purple-600 rounded-lg hover:bg-purple-50 transition font-medium">
                        Đăng nhập
                    </a>
                    <a href="<?= APP_URL ?>/auth/register" 
                       class="px-6 py-2 gradient-bg text-white rounded-lg hover:opacity-90 transition font-medium">
                        Đăng ký
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="pt-20">
        <?= $content ?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-20">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- About -->
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 gradient-bg rounded-lg flex items-center justify-center">
                            <i class="fas fa-hospital text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold">HMS</h3>
                    </div>
                    <p class="text-gray-400 text-sm">
                        Hệ thống quản lý bệnh viện hiện đại, mang đến dịch vụ y tế chất lượng cao cho cộng đồng.
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-bold mb-4">Liên kết nhanh</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#specializations" class="text-gray-400 hover:text-white transition">Chuyên khoa</a></li>
                        <li><a href="#doctors" class="text-gray-400 hover:text-white transition">Đội ngũ bác sĩ</a></li>
                        <li><a href="#services" class="text-gray-400 hover:text-white transition">Dịch vụ</a></li>
                        <li><a href="<?= APP_URL ?>/auth/register" class="text-gray-400 hover:text-white transition">Đăng ký tài khoản</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h4 class="text-lg font-bold mb-4">Liên hệ</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><i class="fas fa-map-marker-alt mr-2"></i>57 Lê Lợi, Quận Gò VấpTP.HCM</li>
                        <li><i class="fas fa-phone mr-2"></i>0973436483</li>
                        <li><i class="fas fa-envelope mr-2"></i>truongpham12032003@gmail.com</li>
                    </ul>
                </div>
                
                <!-- Social -->
                <div>
                    <h4 class="text-lg font-bold mb-4">Theo dõi chúng tôi</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-purple-600 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-purple-600 transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-purple-600 transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-purple-600 transition">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; 2025 Hospital Management System. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
</body>
</html>
