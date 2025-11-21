<?php

require_once __DIR__ . '/../../config/database.php';

class Payment {
    private $conn;
    private $table = 'payments';

    public $id;
    public $payment_code;
    public $invoice_id;
    public $amount;
    public $payment_method;
    public $payment_status;
    public $transaction_id;
    public $gateway_response;
    public $payment_date;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Tạo mã thanh toán tự động
    private function generatePaymentCode() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $number = $row['total'] + 1;
        return 'PAY' . date('Ymd') . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // Tạo payment mới
    public function create() {
        $this->payment_code = $this->generatePaymentCode();

        $query = "INSERT INTO " . $this->table . " 
                  (payment_code, invoice_id, amount, payment_method, payment_status, 
                   transaction_id, gateway_response, payment_date) 
                  VALUES (:payment_code, :invoice_id, :amount, :payment_method, :payment_status, 
                          :transaction_id, :gateway_response, :payment_date)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':payment_code', $this->payment_code);
        $stmt->bindParam(':invoice_id', $this->invoice_id);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':payment_method', $this->payment_method);
        $stmt->bindParam(':payment_status', $this->payment_status);
        $stmt->bindParam(':transaction_id', $this->transaction_id);
        $stmt->bindParam(':gateway_response', $this->gateway_response);
        $stmt->bindParam(':payment_date', $this->payment_date);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Lấy payments theo invoice_id
    public function getByInvoiceId($invoice_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE invoice_id = :invoice_id 
                  ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':invoice_id', $invoice_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật trạng thái thanh toán
    public function updateStatus($id, $status, $transaction_id = null, $gateway_response = null) {
        $query = "UPDATE " . $this->table . " 
                  SET payment_status = :status, 
                      transaction_id = :transaction_id,
                      gateway_response = :gateway_response,
                      payment_date = CASE WHEN :status = 'success' THEN NOW() ELSE payment_date END
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':transaction_id', $transaction_id);
        $stmt->bindParam(':gateway_response', $gateway_response);

        return $stmt->execute();
    }

    // Tìm payment theo mã payment_code (dùng làm orderId với MoMo)
    public function findByCode($payment_code) {
        $query = "SELECT * FROM " . $this->table . " WHERE payment_code = :code LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $payment_code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
