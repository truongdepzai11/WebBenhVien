<?php 
$page_title = 'Thanh toán Ngân hàng';
ob_start(); 
?>

<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                <i class="fas fa-university text-green-500 text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Chuyển khoản ngân hàng</h1>
            <p class="text-gray-600">Quét mã QR để thanh toán hóa đơn của bạn</p>
        </div>

        <!-- Invoice Info -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-lg mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600">Hóa đơn: <?= htmlspecialchars($invoice['invoice_code']) ?></p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Số tiền cần thanh toán</p>
                    <p class="text-3xl font-bold text-green-600"><?= number_format($invoice['final_amount'], 0, ',', '.') ?> VNĐ</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- QR Code Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center">
                    <i class="fas fa-qrcode text-green-500 mr-2"></i>Quét mã QR để thanh toán
                </h2>
                
                <div class="flex justify-center mb-4">
                    <div class="relative">
                        <img src="<?= APP_URL ?>/assets/images/bank.png" 
                             alt="Bank QR Code" 
                             class="w-80 h-80 border-2 border-gray-200 rounded-lg">
                        <div class="absolute -bottom-2 -right-2 bg-green-500 text-white rounded-full p-2">
                            <i class="fas fa-university text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="text-center text-sm text-gray-600">
                    <p><i class="fas fa-university mr-1"></i>Ngân hàng: <?= htmlspecialchars($bankConfig['bank_name']) ?></p>
                    <p><i class="fas fa-user mr-1"></i>Chủ tài khoản: <?= htmlspecialchars($bankConfig['account_name']) ?></p>
                </div>
            </div>

            <!-- Instructions Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-list-ol text-green-500 mr-2"></i>Hướng dẫn thanh toán
                </h2>
                
                <div class="space-y-3">
                    <?php foreach ($bankConfig['instructions'] as $instruction): ?>
                        <div class="flex items-start">
                            <span class="text-green-500 font-semibold mr-3"><?= htmlspecialchars($instruction) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-6 p-4 bg-green-50 rounded-lg">
                    <h3 class="font-semibold text-gray-800 mb-2">
                        <i class="fas fa-info-circle text-green-500 mr-2"></i>Thông tin tài khoản
                    </h3>
                    <div class="space-y-2 text-sm">
                        <p><strong>Ngân hàng:</strong> <?= htmlspecialchars($bankConfig['bank_name']) ?></p>
                        <p><strong>Số tài khoản:</strong> <?= htmlspecialchars($bankConfig['account_number']) ?></p>
                        <p><strong>Chủ tài khoản:</strong> <?= htmlspecialchars($bankConfig['account_name']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex justify-center space-x-4">
            <form method="POST" action="<?= APP_URL ?>/bank-payment/patient-confirm/<?= $invoice['id'] ?>" class="inline">
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-check mr-2"></i>Đã thanh toán
                </button>
            </form>
            
            <a href="<?= APP_URL ?>/bank-payment/cancel/<?= $invoice['id'] ?>" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                <i class="fas fa-times mr-2"></i>Hủy
            </a>
        </div>

        <!-- Note -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <p><i class="fas fa-exclamation-triangle mr-1"></i>Vui lòng giữ lại biên lai chuyển tiền để làm bằng chứng thanh toán</p>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
