<?php 
$page_title = 'Thanh toán Hóa đơn';
ob_start(); 
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
            <i class="fas fa-credit-card mr-2"></i>Thanh toán Hóa đơn
        </h2>

        <!-- Invoice Info -->
        <div class="bg-purple-50 p-6 rounded-lg mb-6">
            <div class="text-center">
                <p class="text-gray-600 mb-2">Mã hóa đơn</p>
                <p class="text-2xl font-bold text-purple-600 mb-4"><?= htmlspecialchars($invoice['invoice_code']) ?></p>
                <p class="text-gray-600 mb-2">Số tiền cần thanh toán</p>
                <p class="text-4xl font-bold text-gray-900"><?= number_format($invoice['final_amount']) ?> VNĐ</p>
            </div>
        </div>

        <!-- Payment Methods -->
        <form id="paymentForm" method="POST" action="<?= APP_URL ?>/invoices/<?= $invoice['id'] ?>/process-payment">
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-4">Chọn phương thức thanh toán</label>
                <div class="space-y-3">
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition">
                        <input type="radio" name="payment_method" value="cash" checked class="mr-3">
                        <i class="fas fa-money-bill-wave text-green-600 text-2xl mr-3"></i>
                        <div>
                            <p class="font-semibold text-gray-900">Tiền mặt</p>
                            <p class="text-sm text-gray-500">Thanh toán trực tiếp tại quầy</p>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition">
                        <input type="radio" name="payment_method" value="momo" class="mr-3">
                        <i class="fab fa-cc-visa text-pink-600 text-2xl mr-3"></i>
                        <div>
                            <p class="font-semibold text-gray-900">Ví MoMo</p>
                            <p class="text-sm text-gray-500">Quét mã QR hoặc thanh toán qua app</p>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition">
                        <input type="radio" name="payment_method" value="zalopay" class="mr-3">
                        <i class="fas fa-wallet text-blue-500 text-2xl mr-3"></i>
                        <div>
                            <p class="font-semibold text-gray-900">Ví ZaloPay</p>
                            <p class="text-sm text-gray-500">Quét mã QR hoặc thanh toán qua app</p>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition">
                        <input type="radio" name="payment_method" value="vnpay" class="mr-3">
                        <i class="fas fa-university text-blue-600 text-2xl mr-3"></i>
                        <div>
                            <p class="font-semibold text-gray-900">VNPay</p>
                            <p class="text-sm text-gray-500">Thanh toán qua thẻ ATM/Visa/MasterCard</p>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition">
                        <input type="radio" name="payment_method" value="bank_transfer" class="mr-3">
                        <i class="fas fa-exchange-alt text-indigo-600 text-2xl mr-3"></i>
                        <div>
                            <p class="font-semibold text-gray-900">Chuyển khoản ngân hàng</p>
                            <p class="text-sm text-gray-500">Chuyển khoản trực tiếp</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex space-x-4">
                <a href="<?= APP_URL ?>/invoices/<?= $invoice['id'] ?>" 
                   class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center">
                    Hủy
                </a>
                <button type="submit" 
                        class="flex-1 px-6 py-3 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                    <i class="fas fa-check mr-2"></i>Xác nhận thanh toán
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
    
    if (selectedMethod === 'momo') {
        e.preventDefault();
        window.location.href = '<?= APP_URL ?>/momo-payment/pay/<?= $invoice['id'] ?>';
    } else if (selectedMethod === 'zalopay') {
        e.preventDefault();
        window.location.href = '<?= APP_URL ?>/zalopay-payment/pay/<?= $invoice['id'] ?>';
    } else if (selectedMethod === 'bank_transfer') {
        e.preventDefault();
        window.location.href = '<?= APP_URL ?>/bank-payment/pay/<?= $invoice['id'] ?>';
    }
});
</script>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
