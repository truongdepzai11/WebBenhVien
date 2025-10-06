<?php 
$page_title = 'Quản lý Hóa đơn';
ob_start(); 
?>

<div class="mb-6 flex items-center justify-between">
    <h3 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-file-invoice-dollar mr-2"></i>Quản lý Hóa đơn
    </h3>
    <?php if (Auth::isAdmin() || Auth::isDoctor()): ?>
    <a href="<?= APP_URL ?>/invoices/create" 
       class="px-6 py-2 gradient-bg text-white rounded-lg hover:opacity-90 transition">
        <i class="fas fa-plus mr-2"></i>Tạo hóa đơn mới
    </a>
    <?php endif; ?>
</div>

<!-- Invoices Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (empty($invoices)): ?>
        <div class="p-12 text-center">
            <i class="fas fa-file-invoice text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Chưa có hóa đơn nào</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã HĐ</th>
                        <?php if (!Auth::isPatient()): ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bệnh nhân</th>
                        <?php endif; ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày lập</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tổng tiền</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thanh toán</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($invoices as $invoice): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-semibold text-purple-600"><?= htmlspecialchars($invoice['invoice_code']) ?></span>
                        </td>
                        <?php if (!Auth::isPatient()): ?>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($invoice['patient_name']) ?></p>
                                <p class="text-sm text-gray-500"><?= htmlspecialchars($invoice['patient_code']) ?></p>
                            </div>
                        </td>
                        <?php endif; ?>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?= date('d/m/Y', strtotime($invoice['issued_date'])) ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-900"><?= number_format($invoice['final_amount']) ?> VNĐ</span>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'paid' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                'refunded' => 'bg-gray-100 text-gray-800'
                            ];
                            $statusLabels = [
                                'pending' => 'Chờ xử lý',
                                'paid' => 'Đã thanh toán',
                                'cancelled' => 'Đã hủy',
                                'refunded' => 'Đã hoàn tiền'
                            ];
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusColors[$invoice['status']] ?>">
                                <?= $statusLabels[$invoice['status']] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                            $paymentColors = [
                                'unpaid' => 'bg-red-100 text-red-800',
                                'paid' => 'bg-green-100 text-green-800',
                                'partial' => 'bg-yellow-100 text-yellow-800',
                                'refunded' => 'bg-gray-100 text-gray-800'
                            ];
                            $paymentLabels = [
                                'unpaid' => 'Chưa thanh toán',
                                'paid' => 'Đã thanh toán',
                                'partial' => 'Thanh toán 1 phần',
                                'refunded' => 'Đã hoàn tiền'
                            ];
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $paymentColors[$invoice['payment_status']] ?>">
                                <?= $paymentLabels[$invoice['payment_status']] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="<?= APP_URL ?>/invoices/<?= $invoice['id'] ?>" 
                                   class="text-purple-600 hover:text-purple-900" title="Chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= APP_URL ?>/invoices/<?= $invoice['id'] ?>/print" 
                                   class="text-blue-600 hover:text-blue-900" title="In" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                                <?php if ($invoice['payment_status'] === 'unpaid' && Auth::isPatient()): ?>
                                <a href="<?= APP_URL ?>/invoices/<?= $invoice['id'] ?>/pay" 
                                   class="text-green-600 hover:text-green-900" title="Thanh toán">
                                    <i class="fas fa-credit-card"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (Auth::isAdmin()): ?>
                                <button onclick="if(confirm('Xác nhận xóa?')) window.location.href='<?= APP_URL ?>/invoices/<?= $invoice['id'] ?>/delete'" 
                                        class="text-red-600 hover:text-red-900" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
