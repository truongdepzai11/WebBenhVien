<?php 
$page_title = 'Thanh toán ZaloPay';
ob_start(); 
?>

<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                <i class="fas fa-wallet text-blue-500 text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Thanh toán qua ZaloPay</h1>
            <p class="text-gray-600">Quét mã QR để thanh toán hóa đơn của bạn</p>
        </div>

        <!-- Invoice Info -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600">Hóa đơn: <?= htmlspecialchars($invoice['invoice_code']) ?></p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Số tiền cần thanh toán</p>
                    <p class="text-3xl font-bold text-blue-600"><?= number_format($invoice['final_amount'], 0, ',', '.') ?> VNĐ</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- QR Code Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center">
                    <i class="fas fa-qrcode text-blue-500 mr-2"></i>Quét mã QR để thanh toán
                </h2>
                
                <div class="flex justify-center mb-4">
                    <div class="relative">
                        <img src="<?= APP_URL ?>/assets/images/zalopay-qr.png" 
                             alt="ZaloPay QR Code" 
                             class="w-80 h-80 border-2 border-gray-200 rounded-lg">
                        <div class="absolute -bottom-2 -right-2 bg-blue-500 text-white rounded-full p-2">
                            <i class="fas fa-mobile-alt text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="text-center text-sm text-gray-600">
                    <p><i class="fas fa-phone mr-1"></i>Số điện thoại: <?= htmlspecialchars($zaloConfig['phone_number']) ?></p>
                    <p><i class="fas fa-user mr-1"></i>Chủ tài khoản: <?= htmlspecialchars($zaloConfig['account_name']) ?></p>
                </div>
            </div>

            <!-- Instructions Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-list-ol text-blue-500 mr-2"></i>Hướng dẫn thanh toán
                </h2>
                
                <div class="space-y-3">
                    <?php foreach ($zaloConfig['instructions'] as $instruction): ?>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">
                            <?= explode('.', $instruction)[0] ?>
                        </div>
                        <p class="text-gray-700"><?= substr($instruction, strpos($instruction, ' ') + 1) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2 mt-1"></i>
                        <div>
                            <p class="text-sm font-semibold text-yellow-800">Lưu ý quan trọng:</p>
                            <p class="text-sm text-yellow-700 mt-1">Sau khi thanh toán thành công, vui lòng chụp màn hình và đưa cho lễ tân để xác nhận.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex gap-4">
            <a href="<?= APP_URL ?>/invoices/<?= $invoice['id'] ?>" 
               class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại
            </a>
            <button onclick="confirmPayment()" 
                    class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-check mr-2"></i>Đã thanh toán
            </button>
        </div>
    </div>
</div>

<script>
        function confirmPayment() {
            if (confirm('Bạn đã thanh toán thành công qua ZaloPay?')) {
                // Hiển thị thông báo chờ xác nhận
                const button = event.target;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang chờ lễ tân xác nhận...';
                button.disabled = true;
                button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                button.classList.add('bg-yellow-500');
                
                // Gọi API để lưu payment
                window.location.href = '<?= APP_URL ?>/zalopay-payment/patient-confirm/<?= $invoice['id'] ?>';
            }
        }
    </script>
</body>
</html>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
