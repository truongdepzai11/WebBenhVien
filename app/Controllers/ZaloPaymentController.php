<?php
require_once __DIR__ . '/../Models/Invoice.php';
require_once __DIR__ . '/../Models/Payment.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class ZaloPaymentController {
    private $invoiceModel;
    private $paymentModel;

    public function __construct() {
        $this->invoiceModel = new Invoice();
        $this->paymentModel = new Payment();
    }

    // Hiển thị trang thanh toán ZaloPay
    public function showPaymentPage($invoiceId) {
        Auth::requireLogin();
        
        $invoice = $this->invoiceModel->findById($invoiceId);
        if (!$invoice) {
            $_SESSION['error'] = 'Không tìm thấy hóa đơn';
            header('Location: ' . APP_URL . '/invoices');
            exit;
        }

        if ($invoice['payment_status'] === 'paid') {
            $_SESSION['info'] = 'Hóa đơn này đã được thanh toán';
            header('Location: ' . APP_URL . '/invoices/' . $invoiceId);
            exit;
        }

        // Lấy thông tin QR code ZaloPay (cá nhân)
        $zaloConfig = $this->getZaloQrConfig($invoice);
        
        require_once __DIR__ . '/../Views/zalo_payment/show.php';
    }

    // Lấy cấu hình QR ZaloPay cá nhân
    private function getZaloQrConfig($invoice) {
        return [
            'qr_code' => 'assets/images/zalopay-qr.png', // Đặt QR code của bạn ở đây
            'phone_number' => '0973436483', // Số điện thoại ZaloPay của bạn
            'account_name' => 'Phạm Quang Trường', // Tên tài khoản ZaloPay
            'instructions' => [
                '1. Mở ứng dụng ZaloPay',
                '2. Chọn "Quét mã"',
                '3. Quét mã QR ở trên',
                '4. Nhập số tiền cần thanh toán: ' . number_format($invoice['final_amount'], 0, ',', '.') . ' VNĐ',
                '5. Xác nhận thanh toán',
                '6. Chụp màn hình giao dịch thành công và đưa cho lễ tân xác nhận'
            ]
        ];
    }

    // Admin xác nhận thanh toán thủ công
    public function confirmPayment($invoiceId) {
        Auth::requireAdminOrStaff();
        
        $invoice = $this->invoiceModel->findById($invoiceId);
        if (!$invoice) {
            $_SESSION['error'] = 'Không tìm thấy hóa đơn';
            header('Location: ' . APP_URL . '/invoices');
            exit;
        }

        if ($invoice['payment_status'] === 'paid') {
            $_SESSION['info'] = 'Hóa đơn này đã được thanh toán';
            header('Location: ' . APP_URL . '/invoices/' . $invoiceId);
            exit;
        }

        // Tạo payment record - làm y hệt như tiền mặt
        $this->paymentModel->invoice_id = $invoiceId;
        $this->paymentModel->amount = $invoice['final_amount'];
        $this->paymentModel->payment_method = 'zalopay';
        $this->paymentModel->payment_status = 'success'; // Admin xác nhận ngay
        $this->paymentModel->transaction_id = null;
        $this->paymentModel->gateway_response = null;
        $this->paymentModel->payment_date = date('Y-m-d H:i:s');
        
        if ($this->paymentModel->create()) {
            // Cập nhật trạng thái hóa đơn
            $this->invoiceModel->updatePaymentStatus($invoiceId, 'paid', date('Y-m-d H:i:s'));
            
            $_SESSION['success'] = 'Đã xác nhận thanh toán ZaloPay cho hóa đơn #' . $invoice['invoice_code'];
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xác nhận thanh toán ZaloPay';
        }

        header('Location: ' . APP_URL . '/invoices/' . $invoiceId);
        exit;
    }

    // Patient báo đã thanh toán
    public function patientConfirmPayment($invoiceId) {
        Auth::requireLogin();
        
        $invoice = $this->invoiceModel->findById($invoiceId);
        if (!$invoice) {
            $_SESSION['error'] = 'Không tìm thấy hóa đơn';
            header('Location: ' . APP_URL . '/invoices');
            exit;
        }

        if ($invoice['payment_status'] === 'paid') {
            $_SESSION['info'] = 'Hóa đơn này đã được thanh toán';
            header('Location: ' . APP_URL . '/invoices/' . $invoiceId);
            exit;
        }

        // Tạo payment record với status pending
        $this->paymentModel->invoice_id = $invoiceId;
        $this->paymentModel->amount = $invoice['final_amount'];
        $this->paymentModel->payment_method = 'zalopay';
        $this->paymentModel->payment_status = 'pending'; // Chờ admin xác nhận
        $this->paymentModel->transaction_id = null;
        $this->paymentModel->gateway_response = null;
        $this->paymentModel->payment_date = null;
        
        if ($this->paymentModel->create()) {
            $_SESSION['success'] = 'Bạn đã thanh toán ZaloPay, vui lòng chờ vài phút để nhân viên xác nhận!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
        }

        header('Location: ' . APP_URL . '/invoices/' . $invoiceId);
        exit;
    }

    // Hủy thanh toán (quay lại trang hóa đơn)
    public function cancelPayment($invoiceId) {
        $_SESSION['info'] = 'Đã hủy thanh toán';
        header('Location: ' . APP_URL . '/invoices/' . $invoiceId);
        exit;
    }
}
