<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán MoMo - Hóa đơn <?= htmlspecialchars($invoice['invoice_code']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Thanh toán MoMo</h1>
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
                        <i class="fas fa-qrcode text-pink-500 mr-2"></i>Quét mã QR để thanh toán
                    </h2>
                    
                    <div class="flex justify-center mb-4">
                        <div class="relative">
                            <img src="<?= APP_URL ?>/assets/images/momo-qr.png" 
                                 alt="MoMo QR Code" 
                                 class="w-80 h-80 border-2 border-gray-200 rounded-lg">
                            <div class="absolute -bottom-2 -right-2 bg-pink-500 text-white rounded-full p-2">
                                <i class="fas fa-mobile-alt text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="text-center text-sm text-gray-600">
                        <p><i class="fas fa-phone mr-1"></i>Số điện thoại: <?= htmlspecialchars($momoConfig['phone_number']) ?></p>
                        <p><i class="fas fa-user mr-1"></i>Chủ tài khoản: <?= htmlspecialchars($momoConfig['account_name']) ?></p>
                    </div>
                </div>

                <!-- Instructions Section -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-list-ol text-blue-500 mr-2"></i>Hướng dẫn thanh toán
                    </h2>
                    
                    <div class="space-y-3">
                        <?php foreach ($momoConfig['instructions'] as $index => $instruction): ?>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-semibold text-sm">
                                <?= $index + 1 ?>
                            </div>
                            <p class="ml-3 text-gray-700"><?= $instruction ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Lưu ý:</strong> Sau khi thanh toán thành công, vui lòng chụp màn hình giao dịch và đưa cho lễ tân để xác nhận.
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 space-y-3">
                        <button onclick="confirmPayment()" class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition font-semibold">
                            <i class="fas fa-check-circle mr-2"></i>Đã thanh toán - Chờ xác nhận
                        </button>
                        
                        <a href="<?= APP_URL ?>/momo-payment/cancel/<?= $invoice['id'] ?>" 
                           class="block w-full bg-gray-500 text-white py-3 px-4 rounded-lg hover:bg-gray-600 transition text-center font-semibold">
                            <i class="fas fa-times-circle mr-2"></i>Hủy thanh toán
                        </a>
                    </div>
                </div>
            </div>

            <!-- Invoice Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-file-invoice text-purple-500 mr-2"></i>Thông tin hóa đơn
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Mã hóa đơn</p>
                        <p class="font-semibold"><?= htmlspecialchars($invoice['invoice_code']) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Ngày phát hành</p>
                        <p class="font-semibold"><?= date('d/m/Y', strtotime($invoice['issued_date'])) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tổng tiền</p>
                        <p class="font-semibold"><?= number_format($invoice['total_amount'], 0, ',', '.') ?> VNĐ</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Thành tiền</p>
                        <p class="font-semibold text-green-600"><?= number_format($invoice['final_amount'], 0, ',', '.') ?> VNĐ</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmPayment() {
            if (confirm('Bạn đã thanh toán thành công qua MoMo?')) {
                // Hiển thị thông báo chờ xác nhận
                const button = event.target;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang chờ lễ tân xác nhận...';
                button.disabled = true;
                button.classList.remove('bg-green-600', 'hover:bg-green-700');
                button.classList.add('bg-yellow-500');
                
                // Gọi API để lưu payment
                window.location.href = '<?= APP_URL ?>/momo-payment/patient-confirm/<?= $invoice['id'] ?>';
            }
        }

        // Auto refresh trang mỗi 30 giây để kiểm tra trạng thái
        // Tạm thời disable để test QR code
        /*
        setInterval(() => {
            fetch('<?= APP_URL ?>/api/invoices/<?= $invoice['id'] ?>/status')
                .then(response => response.json())
                .then(data => {
                    if (data.payment_status === 'paid') {
                        alert('Hóa đơn đã được thanh toán và xác nhận!');
                        window.location.href = '<?= APP_URL ?>/invoices/<?= $invoice['id'] ?>';
                    }
                })
                .catch(error => console.log('Error checking status:', error));
        }, 30000);
        */
    </script>
</body>
</html>
