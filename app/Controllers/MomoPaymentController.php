<?php
require_once __DIR__ . '/../Models/Invoice.php';
require_once __DIR__ . '/../Models/Payment.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class MomoPaymentController {
    private $invoiceModel;
    private $paymentModel;

    public function __construct() {
        $this->invoiceModel = new Invoice();
        $this->paymentModel = new Payment();
    }

    // Hiển thị trang thanh toán MoMo với QR code
    public function showPaymentPage($invoiceId) {
        Auth::requireLogin();
        
        $invoice = $this->invoiceModel->findById($invoiceId);
        if (!$invoice) {
            $_SESSION['error'] = 'Không tìm thấy hóa đơn';
            header('Location: ' . APP_URL . '/invoices');
            exit;
        }

        // Kiểm tra quyền truy cập
        if (Auth::isPatient()) {
            require_once __DIR__ . '/../Models/Patient.php';
            $patientModel = new Patient();
            $patient = $patientModel->findByUserId(Auth::id());
            if ($invoice['patient_id'] != $patient['id']) {
                $_SESSION['error'] = 'Bạn không có quyền xem hóa đơn này';
                header('Location: ' . APP_URL . '/invoices');
                exit;
            }
        }

        // Nếu hóa đơn đã được thanh toán
        if ($invoice['payment_status'] === 'paid') {
            $_SESSION['info'] = 'Hóa đơn này đã được thanh toán';
            header('Location: ' . APP_URL . '/invoices/' . $invoiceId);
            exit;
        }

        // Lấy thông tin QR code MoMo (cá nhân)
        $momoConfig = $this->getMomoQrConfig($invoice);
        
        require_once __DIR__ . '/../Views/momo_payment/show.php';
    }

    // Lấy cấu hình QR MoMo cá nhân
    private function getMomoQrConfig($invoice) {
        return [
            'qr_code' => 'assets/images/momo-qr.png', // Đặt QR code của bạn ở đây
            'phone_number' => '0973436483', // Số điện thoại MoMo của bạn
            'account_name' => 'Phạm Quang Trường', // Tên tài khoản MoMo
            'instructions' => [
                '1. Mở ứng dụng MoMo',
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
        $this->paymentModel->payment_method = 'momo';
        $this->paymentModel->payment_status = 'success'; // Admin xác nhận ngay
        $this->paymentModel->transaction_id = null;
        $this->paymentModel->gateway_response = null;
        $this->paymentModel->payment_date = date('Y-m-d H:i:s');
        
        if ($this->paymentModel->create()) {
            // Cập nhật trạng thái hóa đơn
            $this->invoiceModel->updatePaymentStatus($invoiceId, 'paid', date('Y-m-d H:i:s'));
            
            $_SESSION['success'] = 'Đã xác nhận thanh toán MoMo cho hóa đơn #' . $invoice['invoice_code'];
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xác nhận thanh toán MoMo';
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
        $this->paymentModel->payment_method = 'momo';
        $this->paymentModel->payment_status = 'pending'; // Chờ admin xác nhận
        $this->paymentModel->transaction_id = null;
        $this->paymentModel->gateway_response = null;
        $this->paymentModel->payment_date = null;
        
        if ($this->paymentModel->create()) {
            $_SESSION['success'] = 'Bạn đã thanh toán MoMo, vui lòng chờ vài phút để nhân viên xác nhận!';
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
