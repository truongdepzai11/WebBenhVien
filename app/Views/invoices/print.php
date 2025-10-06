<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn #<?= $invoice['invoice_code'] ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; padding: 20px; }
        .invoice { max-width: 800px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .header h1 { color: #667eea; font-size: 28px; margin-bottom: 10px; }
        .info-section { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .info-box { width: 48%; }
        .info-box h3 { font-size: 14px; color: #666; margin-bottom: 10px; text-transform: uppercase; }
        .info-box p { margin: 5px 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f3f4f6; padding: 12px; text-align: left; font-size: 12px; text-transform: uppercase; border: 1px solid #ddd; }
        td { padding: 10px; border: 1px solid #ddd; font-size: 14px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-section { margin-top: 20px; text-align: right; }
        .total-row { display: flex; justify-content: flex-end; margin: 5px 0; }
        .total-label { width: 200px; text-align: right; padding-right: 20px; }
        .total-value { width: 150px; text-align: right; font-weight: bold; }
        .grand-total { font-size: 20px; color: #667eea; border-top: 2px solid #333; padding-top: 10px; margin-top: 10px; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #ddd; padding-top: 20px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="invoice">
        <!-- Header -->
        <div class="header">
            <h1>HÓA ĐƠN THANH TOÁN</h1>
            <p style="font-size: 18px; font-weight: bold; margin-top: 10px;">
                #<?= htmlspecialchars($invoice['invoice_code']) ?>
            </p>
            <p style="font-size: 14px; color: #666; margin-top: 5px;">
                Ngày lập: <?= date('d/m/Y H:i', strtotime($invoice['issued_date'])) ?>
            </p>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-box">
                <h3>Thông tin bệnh viện</h3>
                <p><strong>Hospital Management System</strong></p>
                <p>123 Đường ABC, TP.HCM</p>
                <p>Điện thoại: 1900 xxxx</p>
                <p>Email: info@hospital.com</p>
            </div>
            <div class="info-box">
                <h3>Thông tin bệnh nhân</h3>
                <p><strong><?= htmlspecialchars($invoice['patient_name']) ?></strong></p>
                <p>Mã BN: <?= htmlspecialchars($invoice['patient_code']) ?></p>
                <p>SĐT: <?= htmlspecialchars($invoice['patient_phone']) ?></p>
                <?php if ($invoice['appointment_code']): ?>
                <p>Mã lịch khám: <?= htmlspecialchars($invoice['appointment_code']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Dịch vụ</th>
                    <th class="text-center">Số lượng</th>
                    <th class="text-right">Đơn giá</th>
                    <th class="text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php $stt = 1; foreach ($items as $item): ?>
                <tr>
                    <td class="text-center"><?= $stt++ ?></td>
                    <td><?= htmlspecialchars($item['description']) ?></td>
                    <td class="text-center"><?= $item['quantity'] ?></td>
                    <td class="text-right"><?= number_format($item['unit_price']) ?></td>
                    <td class="text-right"><?= number_format($item['total_price']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row">
                <div class="total-label">Tổng tiền:</div>
                <div class="total-value"><?= number_format($invoice['total_amount']) ?> VNĐ</div>
            </div>
            <?php if ($invoice['discount_amount'] > 0): ?>
            <div class="total-row">
                <div class="total-label">Giảm giá:</div>
                <div class="total-value" style="color: #ef4444;">-<?= number_format($invoice['discount_amount']) ?> VNĐ</div>
            </div>
            <?php endif; ?>
            <?php if ($invoice['tax_amount'] > 0): ?>
            <div class="total-row">
                <div class="total-label">Thuế VAT:</div>
                <div class="total-value"><?= number_format($invoice['tax_amount']) ?> VNĐ</div>
            </div>
            <?php endif; ?>
            <div class="total-row grand-total">
                <div class="total-label">TỔNG THANH TOÁN:</div>
                <div class="total-value"><?= number_format($invoice['final_amount']) ?> VNĐ</div>
            </div>
        </div>

        <!-- Payment Status -->
        <div style="margin-top: 30px; padding: 15px; background: #f3f4f6; border-radius: 8px;">
            <p style="font-size: 14px; margin-bottom: 5px;">
                <strong>Trạng thái thanh toán:</strong> 
                <?php
                $labels = ['unpaid' => 'Chưa thanh toán', 'paid' => 'Đã thanh toán', 'partial' => 'Thanh toán 1 phần'];
                echo $labels[$invoice['payment_status']];
                ?>
            </p>
            <?php if ($invoice['paid_date']): ?>
            <p style="font-size: 14px;">
                <strong>Ngày thanh toán:</strong> <?= date('d/m/Y H:i', strtotime($invoice['paid_date'])) ?>
            </p>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Cảm ơn quý khách đã sử dụng dịch vụ!</p>
            <p style="margin-top: 10px;">Hóa đơn này được in tự động từ hệ thống</p>
        </div>

        <!-- Print Button -->
        <div class="no-print" style="text-align: center; margin-top: 30px;">
            <button onclick="window.print()" 
                    style="padding: 12px 30px; background: #667eea; color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer;">
                <i class="fas fa-print"></i> In hóa đơn
            </button>
            <button onclick="window.close()" 
                    style="padding: 12px 30px; background: #6b7280; color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; margin-left: 10px;">
                Đóng
            </button>
        </div>
    </div>
</body>
</html>
