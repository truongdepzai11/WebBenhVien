<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Hospital Management System' ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .sidebar-link {
            transition: all 0.2s ease;
        }
        
        .sidebar-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            padding-left: 1.5rem;
        }
        
        .sidebar-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            border-left: 4px solid white;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php if (Auth::check()): ?>
        <!-- Layout với Sidebar -->
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <aside class="w-64 gradient-bg text-white flex-shrink-0 overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center space-x-2 mb-8">
                        <i class="fas fa-hospital text-3xl"></i>
                        <div>
                            <h1 class="text-xl font-bold">HMS</h1>
                            <p class="text-xs opacity-75">Hospital Management</p>
                        </div>
                    </div>
                    
                    <!-- User Info -->
                    <div class="bg-white bg-opacity-10 rounded-lg p-4 mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-purple-600 font-bold">
                                <?= strtoupper(substr(Auth::user()['full_name'], 0, 1)) ?>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-sm"><?= htmlspecialchars(Auth::user()['full_name']) ?></p>
                                <p class="text-xs opacity-75"><?= ucfirst(Auth::role()) ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Navigation -->
                    <nav class="space-y-2">
                        <a href="<?= APP_URL ?>/dashboard" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false && strpos($_SERVER['REQUEST_URI'], '/admin') === false) ? 'active' : '' ?>">
                            <i class="fas fa-home w-5"></i>
                            <span>Dashboard</span>
                        </a>
                        
                        <?php if (Auth::isAdmin()): ?>
                        <!-- Admin Menu -->
                        <div class="pt-4 pb-2 px-4">
                            <p class="text-xs font-semibold text-purple-200 uppercase">Quản trị</p>
                        </div>
                        
                        <a href="<?= APP_URL ?>/admin" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/admin') !== false && strpos($_SERVER['REQUEST_URI'], '/admin/') === false) ? 'active' : '' ?>">
                            <i class="fas fa-cogs w-5"></i>
                            <span>Quản trị hệ thống</span>
                        </a>
                        
                        <a href="<?= APP_URL ?>/admin/doctors" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/admin/doctors') !== false) ? 'active' : '' ?>">
                            <i class="fas fa-user-md w-5"></i>
                            <span>QL Bác sĩ</span>
                        </a>
                        
                        <a href="<?= APP_URL ?>/admin/specializations" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/admin/specializations') !== false) ? 'active' : '' ?>">
                            <i class="fas fa-stethoscope w-5"></i>
                            <span>QL Chuyên khoa</span>
                        </a>
                        
                        <a href="<?= APP_URL ?>/admin/packages" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/admin/packages') !== false) ? 'active' : '' ?>">
                            <i class="fas fa-box-open w-5"></i>
                            <span>QL Gói khám</span>
                        </a>
                        
                        <a href="<?= APP_URL ?>/admin/users" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false) ? 'active' : '' ?>">
                            <i class="fas fa-users-cog w-5"></i>
                            <span>QL Users</span>
                        </a>
                        
                        <div class="pt-4 pb-2 px-4">
                            <p class="text-xs font-semibold text-purple-200 uppercase">Chung</p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (Auth::isAdmin() || Auth::isDoctor() || Auth::isReceptionist()): ?>
                        <a href="<?= APP_URL ?>/patients" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/patients') !== false) ? 'active' : '' ?>">
                            <i class="fas fa-hospital-user w-5"></i>
                            <span>Bệnh nhân</span>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!Auth::isDoctor()): ?>
                        <a href="<?= APP_URL ?>/doctors" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/doctors') !== false && strpos($_SERVER['REQUEST_URI'], '/admin') === false) ? 'active' : '' ?>">
                            <i class="fas fa-user-md w-5"></i>
                            <span>Bác sĩ</span>
                        </a>
                        <?php endif; ?>
                        
                        <a href="<?= APP_URL ?>/packages" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/packages') !== false && strpos($_SERVER['REQUEST_URI'], '/admin') === false) ? 'active' : '' ?>">
                            <i class="fas fa-box-open w-5"></i>
                            <span>Gói khám</span>
                        </a>
                        
                        <a href="<?= APP_URL ?>/appointments" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/appointments') !== false && strpos($_SERVER['REQUEST_URI'], '/package-appointments') === false) ? 'active' : '' ?>">
                            <i class="fas fa-calendar-check w-5"></i>
                            <span>Lịch hẹn</span>
                        </a>
                        
                        <a href="<?= APP_URL ?>/package-appointments" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/package-appointments') !== false) ? 'active' : '' ?>">
                            <i class="fas fa-clipboard-list w-5"></i>
                            <span>Quản lý Gói khám</span>
                        </a>

                        <?php if (Auth::isPatient()): ?>
                        <a href="<?= APP_URL ?>/my-results" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/my-results') !== false) ? 'active' : '' ?>">
                            <i class="fas fa-vial w-5"></i>
                            <span>Kết quả của tôi</span>
                        </a>
                        <?php endif; ?>

                        <?php if (Auth::isPatient()): ?>
                        <a href="<?= APP_URL ?>/consultations" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/consultations') !== false && strpos($_SERVER['REQUEST_URI'], '/doctor/consultations') === false) ? 'active' : '' ?>">
                            <i class="fas fa-comments w-5"></i>
                            <span>Tư vấn sức khỏe</span>
                        </a>
                        <a href="javascript:void(0)" onclick="toggleChatBot()" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-robot w-5"></i>
                            <span>Tư vấn AI</span>
                        </a>
                        <?php endif; ?>

                        <?php if (Auth::isDoctor()): ?>
                        <a href="<?= APP_URL ?>/doctor/consultations" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/doctor/consultations') !== false) ? 'active' : '' ?>">
                            <i class="fas fa-inbox w-5"></i>
                            <span>Hộp thư tư vấn</span>
                        </a>
                        <?php endif; ?>

                        <?php if (Auth::isReceptionist()): ?>
                        <!-- Menu nổi bật cho Lễ tân - Đăng ký Walk-in -->
                        <a href="<?= APP_URL ?>/schedule" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg bg-gradient-to-r from-green-500 to-emerald-600 text-white hover:from-green-600 hover:to-emerald-700 shadow-lg">
                            <i class="fas fa-user-plus w-5"></i>
                            <span class="font-semibold">Đăng ký khám Walk-in</span>
                        </a>
                        <?php elseif (Auth::isAdmin() || Auth::isDoctor()): ?>
                        <a href="<?= APP_URL ?>/schedule" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/schedule') !== false) ? 'active' : '' ?>">
                            <i class="fas fa-calendar-alt w-5"></i>
                            <span>Lịch làm việc Bác sĩ</span>
                        </a>
                        <?php endif; ?>

                        <a href="<?= APP_URL ?>/medical-records" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/medical-records') !== false) ? 'active' : '' ?>">
                            <i class="fas fa-file-medical w-5"></i>
                            <span>Hồ sơ bệnh án</span>
                        </a>

                        <a href="<?= APP_URL ?>/invoices" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/invoices') !== false) ? 'active' : '' ?>">
                            <i class="fas fa-file-invoice-dollar w-5"></i>
                            <span>Hóa đơn</span>
                        </a>
                        
                        <a href="<?= APP_URL ?>/profile" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg <?= (strpos($_SERVER['REQUEST_URI'], '/profile') !== false) ? 'active' : '' ?>">
                            <i class="fas fa-user-circle w-5"></i>
                            <span>Thông tin cá nhân</span>
                        </a>
                        
                        <a href="<?= APP_URL ?>/auth/logout" class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mt-8">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span>Đăng xuất</span>
                        </a>
                    </nav>
                </div>
            </aside>
            
            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Header -->
                <header class="bg-white shadow-sm">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-800"><?= $page_title ?? 'Dashboard' ?></h2>
                        <div class="flex items-center space-x-4">
                            <?php if (Auth::isPatient() || Auth::isDoctor() || Auth::isAdmin()): ?>
                            <a href="<?= APP_URL ?>/notifications" class="relative inline-flex items-center text-gray-600 hover:text-purple-700">
                                <i class="fas fa-bell text-xl"></i>
                                <span id="notifBadge" class="hidden absolute -top-2 -right-2 px-1.5 py-0.5 text-[10px] rounded-full bg-red-600 text-white">0</span>
                            </a>
                            <script>
                                (function(){
                                  const badge = document.addEventListener ? document.getElementById('notifBadge') : null;
                                  async function fetchCount(){
                                    try{
                                      const res = await fetch('<?= APP_URL ?>/api/notifications/unread-count', {cache:'no-store'});
                                      const data = await res.json();
                                      if(!badge) return;
                                      const c = data.count||0;
                                      if(c>0){ badge.textContent = c>99? '99+': c; badge.classList.remove('hidden'); }
                                      else { badge.classList.add('hidden'); }
                                    }catch(e){ /* silent */ }
                                  }
                                  fetchCount();
                                  setInterval(fetchCount, 60000);
                                })();
                            </script>
                            <?php endif; ?>
                            <span class="text-sm text-gray-600"><?= date('d/m/Y H:i') ?></span>
                        </div>
                    </div>
                </header>
                
                <!-- Content Area -->
                <main class="flex-1 overflow-y-auto p-6">
                    <!-- Flash Messages -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-3"></i>
                                <p><?= $_SESSION['success'] ?></p>
                            </div>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['warning'])): ?>
                        <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 mb-6 rounded" role="alert">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle mr-3"></i>
                                <p><?= $_SESSION['warning'] ?></p>
                            </div>
                        </div>
                        <?php unset($_SESSION['warning']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle mr-3"></i>
                                <p><?= $_SESSION['error'] ?></p>
                            </div>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    
                    <!-- Page Content -->
                    <?php echo $content ?? ''; ?>
                </main>
            </div>
        </div>
        <?php if (Auth::isPatient()): ?>
        <a href="javascript:void(0)" onclick="toggleChatBot()" class="fixed bottom-6 right-6 inline-flex items-center gap-2 px-4 py-3 rounded-full shadow-lg bg-green-600 hover:bg-green-700 text-white z-40">
            <i class="fas fa-robot"></i>
            <span class="hidden sm:inline">Tư vấn AI</span>
        </a>
        
        <!-- Chat Bot Popup -->
        <div id="chatBotPopup" class="fixed bottom-24 right-6 w-96 h-[500px] bg-white rounded-lg shadow-2xl z-50 hidden flex flex-col">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 text-white p-4 rounded-t-lg flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                        <i class="fas fa-robot text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold">Trợ lý AI Sức khỏe</h3>
                        <p class="text-xs opacity-90">Luôn sẵn sàng tư vấn</p>
                    </div>
                </div>
                <button onclick="toggleChatBot()" class="text-white hover:bg-white/20 rounded p-2">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Messages Area -->
            <div id="chatMessages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
                <div class="flex items-start space-x-2">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-robot text-green-600 text-sm"></i>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow-sm max-w-[80%]">
                        <p class="text-sm">Xin chào! Tôi là trợ lý AI sức khỏe. Tôi có thể giúp gì cho bạn hôm nay?</p>
                    </div>
                </div>
            </div>
            
            <!-- Input Area -->
            <div class="p-4 border-t bg-white rounded-b-lg">
                <div class="flex items-center space-x-2">
                    <input type="text" id="chatInput" placeholder="Nhập câu hỏi của bạn..." 
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <button onclick="sendMessage()" class="bg-green-600 hover:bg-green-700 text-white p-2 rounded-full">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    
    <!-- Chat Bot JavaScript -->
    <script>
        function toggleChatBot() {
            const popup = document.getElementById('chatBotPopup');
            if (popup.classList.contains('hidden')) {
                popup.classList.remove('hidden');
                document.getElementById('chatInput').focus();
            } else {
                popup.classList.add('hidden');
            }
        }
        
        function sendMessage() {
            const input = document.getElementById('chatInput');
            const message = input.value.trim();
            
            if (!message) return;
            
            const messagesContainer = document.getElementById('chatMessages');
            
            // Add user message
            const userMessageHtml = `
                <div class="flex items-start space-x-2 justify-end">
                    <div class="bg-blue-600 text-white p-3 rounded-lg shadow-sm max-w-[80%]">
                        <p class="text-sm">${message}</p>
                        <span class="text-xs opacity-75 mt-1">${new Date().toLocaleTimeString()}</span>
                    </div>
                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-gray-600 text-sm"></i>
                    </div>
                </div>
            `;
            messagesContainer.innerHTML += userMessageHtml;
            
            // Clear input
            input.value = '';
            
            // Scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            // Simulate AI response
            setTimeout(() => {
                const aiResponse = getAIResponse(message);
                const botMessageHtml = `
                    <div class="flex items-start space-x-2">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-robot text-green-600 text-sm"></i>
                        </div>
                        <div class="bg-white p-3 rounded-lg shadow-sm max-w-[80%]">
                            <p class="text-sm">${aiResponse}</p>
                            <span class="text-xs text-gray-500 mt-1">${new Date().toLocaleTimeString()}</span>
                        </div>
                    </div>
                `;
                messagesContainer.innerHTML += botMessageHtml;
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }, 1000);
        }
        
        function getAIResponse(message) {
            // Simple AI responses for demo
            const responses = {
                'đau đầu': 'Đau đầu có thể do nhiều nguyên nhân: stress, thiếu ngủ, hoặc vấn đề về huyết áp. Bạn nên nghỉ ngơi và uống đủ nước. Nếu kéo dài, hãy đến khám bác sĩ.',
                'huyết áp': 'Huyết áp bình thường là 120/80. Nếu trên 140/90 thì bạn nên giảm muối, tập thể dục và theo dõi thường xuyên.',
                'sốt': 'Nếu sốt dưới 38.5°C, bạn có thể nghỉ ngơi và uống paracetamol. Nếu trên 38.5°C hoặc kéo dài hơn 2 ngày, hãy đến khám.',
                'ho': 'Ho thường do virus hoặc dị ứng. Bạn nên uống nhiều nước, súc miệng bằng nước muối. Nếu có khó thở hoặc ho kéo dài, hãy đến khám.',
                'đau bụng': 'Đau bụng có thể do khó tiêu, viêm dạ dày hoặc ăn uống. Bạn nên ăn nhẹ, uống nhiều nước. Nếu đau dữ dội, hãy đến khám ngay.'
            };
            
            message = message.toLowerCase();
            for (let key in responses) {
                if (message.includes(key)) {
                    return responses[key];
                }
            }
            
            return 'Cảm ơn câu hỏi của bạn. Tôi đang học hỏi để trả lời tốt hơn. Bạn có thể mô tả chi tiết hơn về triệu chứng không?';
        }
        
        // Enter key to send
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('chatInput');
            if (input) {
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        sendMessage();
                    }
                });
            }
        });
    </script>
    <?php else: ?>
        <!-- Layout cho trang đăng nhập/đăng ký -->
        <?php echo $content ?? ''; ?>
    <?php endif; ?>
</body>
</html>
