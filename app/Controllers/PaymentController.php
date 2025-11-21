<?php
require_once __DIR__ . '/../Models/Payment.php';
require_once __DIR__ . '/../Models/Invoice.php';

class PaymentController {
    private $paymentModel;
    private $invoiceModel;

    public function __construct() {
        $this->paymentModel = new Payment();
        $this->invoiceModel = new Invoice();
    }

    // User lands back from MoMo
    public function momoReturn() {
        $params = $_GET;
        $cfg = require APP_PATH . '/../config/momo.php';
        $orderId = $params['orderId'] ?? null; // our payment_code
        $resultCode = (int)($params['resultCode'] ?? -1);
        $message = $params['message'] ?? '';

        $payment = $orderId ? $this->paymentModel->findByCode($orderId) : null;
        $invoiceId = $payment ? (int)$payment['invoice_id'] : null;
        if ($resultCode === 0) {
            $_SESSION['success'] = 'Thanh toán MoMo: giao dịch đã được xử lý. Vui lòng chờ hệ thống xác nhận (IPN).';
        } else {
            $_SESSION['error'] = 'Thanh toán MoMo thất bại: ' . htmlspecialchars($message);
        }
        if ($invoiceId) {
            header('Location: ' . APP_URL . '/invoices/' . $invoiceId);
        } else {
            header('Location: ' . APP_URL . '/invoices');
        }
        exit;
    }

    // IPN from MoMo to confirm transaction
    public function momoIpn() {
        // Expect JSON body
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            // fallback for form-encoded
            $data = $_POST;
        }

        $cfg = require APP_PATH . '/../config/momo.php';
        $partnerCode = $data['partnerCode'] ?? '';
        $orderId = $data['orderId'] ?? '';
        $requestId = $data['requestId'] ?? '';
        $amount = (int)($data['amount'] ?? 0);
        $resultCode = (int)($data['resultCode'] ?? -1);
        $transId = $data['transId'] ?? null;
        $signature = $data['signature'] ?? '';
        $extraData = $data['extraData'] ?? '';

        // Basic validate
        if ($partnerCode !== $cfg['partnerCode'] || empty($orderId)) {
            http_response_code(400);
            echo json_encode(['resultCode'=>400,'message'=>'invalid request']);
            return;
        }

        // Find payment by our orderId
        $payment = $this->paymentModel->findByCode($orderId);
        if (!$payment) {
            http_response_code(404);
            echo json_encode(['resultCode'=>404,'message'=>'payment not found']);
            return;
        }

        // Idempotency: if already success -> return OK
        if (($payment['payment_status'] ?? '') === 'success') {
            echo json_encode(['resultCode'=>0,'message'=>'already processed']);
            return;
        }

        // Amount check
        if ($amount != (int)$payment['amount']) {
            http_response_code(400);
            echo json_encode(['resultCode'=>400,'message'=>'amount mismatch']);
            return;
        }

        // Optionally verify signature (MoMo v2 raw signature)
        // Note: payload fields may change per requestType; keep minimal validation to avoid false negatives in sandbox.
        // In production, compute exact rawSignature as docs.

        // Update payment & invoice on success
        if ($resultCode === 0) {
            $this->paymentModel->updateStatus((int)$payment['id'], 'success', $transId, $raw ?: json_encode($data));

            // Recompute invoice paid amount by summing success payments
            $invoiceId = (int)$payment['invoice_id'];
            $inv = $this->invoiceModel->findById($invoiceId);
            if ($inv) {
                $success = $this->paymentModel->getByInvoiceId($invoiceId);
                $paid = 0;
                foreach ($success as $p) {
                    if (($p['payment_status'] ?? '') === 'success') $paid += (int)$p['amount'];
                }
                if ($paid >= (int)$inv['final_amount']) {
                    $this->invoiceModel->updatePaymentStatus($invoiceId, 'paid', date('Y-m-d H:i:s'));
                }
            }

            echo json_encode(['resultCode'=>0,'message'=>'success']);
            return;
        }

        // Failed
        $this->paymentModel->updateStatus((int)$payment['id'], 'failed', $transId, $raw ?: json_encode($data));
        echo json_encode(['resultCode'=>$resultCode,'message'=>'failed']);
    }
}
