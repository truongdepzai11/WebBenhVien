<?php 
$page_title = 'Chi tiết Hóa đơn #' . $invoice['invoice_code'];
ob_start(); 
?>

<div class="mb-6">
    <a href="<?= APP_URL ?>/invoices" class="text-purple-600 hover:text-purple-700">
        <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách
    </a>
</div>

<!-- Invoice Header -->
<div class="bg-white rounded-lg shadow-md p-8 mb-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Hóa đơn #<?= htmlspecialchars($invoice['invoice_code']) ?></h1>
            <p class="text-gray-600">Ngày lập: <?= date('d/m/Y H:i', strtotime($invoice['issued_date'])) ?></p>
        </div>
        <div class="text-right">
            <?php
            $statusColors = [
                'pending' => 'bg-yellow-100 text-yellow-800',
                'paid' => 'bg-green-100 text-green-800',
                'cancelled' => 'bg-red-100 text-red-800'
            ];
            $statusLabels = [
                'pending' => 'Chờ xử lý',
                'paid' => 'Đã thanh toán',
                'cancelled' => 'Đã hủy'
            ];
            ?>
            <span class="px-4 py-2 rounded-full text-sm font-bold <?= $statusColors[$invoice['status']] ?>">
                <?= $statusLabels[$invoice['status']] ?>
            </span>
        </div>
    </div>

    <!-- Patient Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6 border-b">
        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-3">Thông tin bệnh nhân</h3>
            <div class="space-y-2 text-gray-700">
                <p><strong>Họ tên:</strong> <?= htmlspecialchars($invoice['patient_name']) ?></p>
                <p><strong>Mã BN:</strong> <?= htmlspecialchars($invoice['patient_code']) ?></p>
                <p><strong>SĐT:</strong> <?= htmlspecialchars($invoice['patient_phone']) ?></p>
            </div>
        </div>
        <?php if ($invoice['appointment_code']): ?>
        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-3">Thông tin lịch khám</h3>
            <div class="space-y-2 text-gray-700">
                <p><strong>Mã lịch:</strong> <?= htmlspecialchars($invoice['appointment_code']) ?></p>
                <p><strong>Ngày khám:</strong> <?= date('d/m/Y', strtotime($invoice['appointment_date'])) ?></p>
                <?php if (isset($doctor_name) && $doctor_name): ?>
                <p><strong>Bác sĩ:</strong> 
                    <span class="inline-flex items-center">
                        <i class="fas fa-user-md text-purple-600 mr-2"></i>
                        <?= htmlspecialchars($doctor_name) ?>
                    </span>
                </p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($packageServiceDetails)): ?>
    <!-- Package Services Breakdown -->
    <div class="mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Danh sách dịch vụ trong gói</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Dịch vụ</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Bác sĩ</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Ngày khám</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Giờ khám</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Giá</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($packageServiceDetails as $sv): ?>
                    <tr>
                        <td class="px-4 py-3 text-gray-900 font-medium"><?= htmlspecialchars($sv['service_name'] ?? '') ?></td>
                        <td class="px-4 py-3 text-gray-700"><?= htmlspecialchars($sv['doctor_name'] ?? '—') ?></td>
                        <td class="px-4 py-3 text-gray-700"><?= !empty($sv['appointment_date']) ? date('d/m/Y', strtotime($sv['appointment_date'])) : '—' ?></td>
                        <td class="px-4 py-3 text-gray-700"><?= !empty($sv['appointment_time']) ? date('H:i', strtotime($sv['appointment_time'])) : '—' ?></td>
                        <td class="px-4 py-3 text-right text-gray-900 font-semibold"><?= number_format((float)($sv['price'] ?? 0)) ?> VNĐ</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right font-semibold text-gray-800">Tổng giá gói</td>
                        <td class="px-4 py-3 text-right text-purple-600 font-bold text-lg"><?= number_format((float)($packageTotal ?? 0)) ?> VNĐ</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Invoice Items -->
    <div class="mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Chi tiết dịch vụ</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Dịch vụ</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Số lượng</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Đơn giá</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="px-4 py-3">
                            <div>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($item['description']) ?></p>
                                <?php
                                $typeLabels = [
                                    'consultation' => 'Khám bệnh',
                                    'medicine' => 'Thuốc',
                                    'test' => 'Xét nghiệm',
                                    'procedure' => 'Thủ thuật',
                                    'other' => 'Khác'
                                ];
                                ?>
                                <p class="text-sm text-gray-500"><?= $typeLabels[$item['item_type']] ?></p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700"><?= $item['quantity'] ?></td>
                        <td class="px-4 py-3 text-right text-gray-700"><?= number_format($item['unit_price']) ?> VNĐ</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900"><?= number_format($item['total_price']) ?> VNĐ</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Total -->
    <div class="border-t pt-6">
        <div class="flex justify-end">
            <div class="w-full md:w-1/2 space-y-3">
                <div class="flex justify-between text-gray-700">
                    <span>Tổng tiền:</span>
                    <span class="font-semibold"><?= number_format($invoice['total_amount']) ?> VNĐ</span>
                </div>
                <?php if ($invoice['discount_amount'] > 0): ?>
                <div class="flex justify-between text-gray-700">
                    <span>Giảm giá:</span>
                    <span class="font-semibold text-red-600">-<?= number_format($invoice['discount_amount']) ?> VNĐ</span>
                </div>
                <?php endif; ?>
                <?php if ($invoice['tax_amount'] > 0): ?>
                <div class="flex justify-between text-gray-700">
                    <span>Thuế VAT:</span>
                    <span class="font-semibold"><?= number_format($invoice['tax_amount']) ?> VNĐ</span>
                </div>
                <?php endif; ?>
                <div class="flex justify-between text-xl font-bold text-gray-900 pt-3 border-t">
                    <span>Tổng thanh toán:</span>
                    <span class="text-purple-600"><?= number_format($invoice['final_amount']) ?> VNĐ</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Status -->
    <div class="mt-6 pt-6 border-t">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Trạng thái thanh toán</h3>
                <?php
                $paymentColors = [
                    'unpaid' => 'bg-red-100 text-red-800',
                    'paid' => 'bg-green-100 text-green-800',
                    'partial' => 'bg-yellow-100 text-yellow-800'
                ];
                $paymentLabels = [
                    'unpaid' => 'Chưa thanh toán',
                    'paid' => 'Đã thanh toán',
                    'partial' => 'Thanh toán 1 phần'
                ];
                ?>
                <span class="px-4 py-2 rounded-full text-sm font-bold <?= $paymentColors[$invoice['payment_status']] ?>">
                    <?= $paymentLabels[$invoice['payment_status']] ?>
                </span>
                <?php if ($invoice['paid_date']): ?>
                <p class="text-sm text-gray-600 mt-2">Ngày thanh toán: <?= date('d/m/Y H:i', strtotime($invoice['paid_date'])) ?></p>
                <?php endif; ?>
            </div>
            <div class="flex space-x-3">
                <a href="<?= APP_URL ?>/invoices/<?= $invoice['id'] ?>/print" 
                   class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" target="_blank">
                    <i class="fas fa-print mr-2"></i>In hóa đơn
                </a>
                
                <?php if ($invoice['payment_status'] === 'unpaid'): ?>
                    <?php if (Auth::isPatient()): ?>
                    <a href="<?= APP_URL ?>/invoices/<?= $invoice['id'] ?>/pay" 
                       class="px-6 py-2 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                        <i class="fas fa-credit-card mr-2"></i>Thanh toán
                    </a>
                    <?php endif; ?>
                    
                    <?php if (Auth::isAdmin() || Auth::isReceptionist()): ?>
                    <a href="<?= APP_URL ?>/invoices/<?= $invoice['id'] ?>/pay" 
                       class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-check-circle mr-2"></i>Xác nhận thanh toán
                    </a>
                    
                    <a href="<?= APP_URL ?>/momo-payment/confirm/<?= $invoice['id'] ?>" 
                       onclick="return confirm('Xác nhận bệnh nhân đã thanh toán qua MoMo?')"
                       class="px-6 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition">
                        <i class="fas fa-qrcode mr-2"></i>Xác nhận MoMo
                    </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    <?php if (!empty($payments)): ?>
    <div class="mt-6 pt-6 border-t">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Lịch sử thanh toán</h3>
        <div class="space-y-3">
            <?php foreach ($payments as $payment): ?>
            <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg">
                <div>
                    <p class="font-semibold text-gray-900"><?= htmlspecialchars($payment['payment_code']) ?></p>
                    <p class="text-sm text-gray-600">
                        <?php
                        $methodLabels = ['cash' => 'Tiền mặt', 'momo' => 'MoMo', 'vnpay' => 'VNPay', 'bank_transfer' => 'Chuyển khoản', 'zalopay' => 'ZaloPay'];
                        echo $methodLabels[$payment['payment_method']];
                        ?>
                        <?php if ($payment['payment_date']): ?>
                        - <?= date('d/m/Y H:i', strtotime($payment['payment_date'])) ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-900"><?= number_format($payment['amount']) ?> VNĐ</p>
                    <?php
                    $statusColors = ['pending' => 'text-yellow-600', 'success' => 'text-green-600', 'failed' => 'text-red-600'];
                    $statusLabels = ['pending' => 'Đang xử lý', 'success' => 'Thành công', 'failed' => 'Thất bại'];
                    ?>
                    <p class="text-sm font-semibold <?= $statusColors[$payment['payment_status']] ?>">
                        <?= $statusLabels[$payment['payment_status']] ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
