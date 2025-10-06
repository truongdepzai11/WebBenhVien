<?php 
$page_title = 'Đổi mật khẩu';
ob_start(); 
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-key mr-2"></i>Đổi mật khẩu
        </h3>
        <p class="text-gray-600 mt-2">Vui lòng nhập mật khẩu hiện tại và mật khẩu mới</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <form action="<?= APP_URL ?>/profile/change-password" method="POST" class="space-y-6">
            <!-- Mật khẩu hiện tại -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2"></i>Mật khẩu hiện tại *
                </label>
                <input type="password" id="current_password" name="current_password" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="Nhập mật khẩu hiện tại">
            </div>

            <!-- Mật khẩu mới -->
            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-key mr-2"></i>Mật khẩu mới *
                </label>
                <input type="password" id="new_password" name="new_password" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="Nhập mật khẩu mới (ít nhất 6 ký tự)">
            </div>

            <!-- Xác nhận mật khẩu mới -->
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-check-circle mr-2"></i>Xác nhận mật khẩu mới *
                </label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="Nhập lại mật khẩu mới">
            </div>

            <!-- Lưu ý bảo mật -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <div class="flex">
                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                    <div>
                        <h4 class="font-semibold text-blue-800 mb-2">Lưu ý về mật khẩu:</h4>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• Mật khẩu phải có ít nhất 6 ký tự</li>
                            <li>• Nên sử dụng kết hợp chữ hoa, chữ thường, số và ký tự đặc biệt</li>
                            <li>• Không sử dụng mật khẩu quá đơn giản</li>
                            <li>• Không chia sẻ mật khẩu với người khác</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 gradient-bg text-white font-semibold py-3 px-6 rounded-lg hover:opacity-90 transition">
                    <i class="fas fa-check mr-2"></i>Đổi mật khẩu
                </button>
                <a href="<?= APP_URL ?>/profile" 
                   class="flex-1 text-center bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i>Hủy
                </a>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
