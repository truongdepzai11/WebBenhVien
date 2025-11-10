<?php

require_once __DIR__ . '/../../config/database.php';

class Invoice {
    private $conn;
    private $table = 'invoices';

    public $id;
    public $invoice_code;
    public $appointment_id;
    public $patient_id;
    public $total_amount;
    public $discount_amount;
    public $tax_amount;
    public $final_amount;
    public $status;
    public $payment_method;
    public $payment_status;
    public $notes;
    public $issued_date;
    public $paid_date;
    public $due_date;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Tìm hóa đơn theo appointment_id (nếu tạo từ lịch khám)
    public function findByAppointmentId($appointment_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE appointment_id = :appointment_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':appointment_id', $appointment_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tạo mã hóa đơn tự động
    private function generateInvoiceCode() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $number = $row['total'] + 1;
        return 'INV' . date('Ymd') . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // Tạo hóa đơn mới
    public function create() {
        $this->invoice_code = $this->generateInvoiceCode();

        $query = "INSERT INTO " . $this->table . " 
                  (invoice_code, appointment_id, patient_id, total_amount, discount_amount, 
                   tax_amount, final_amount, status, payment_method, payment_status, notes, due_date) 
                  VALUES (:invoice_code, :appointment_id, :patient_id, :total_amount, :discount_amount, 
                          :tax_amount, :final_amount, :status, :payment_method, :payment_status, :notes, :due_date)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':invoice_code', $this->invoice_code);
        $stmt->bindParam(':appointment_id', $this->appointment_id);
        $stmt->bindParam(':patient_id', $this->patient_id);
        $stmt->bindParam(':total_amount', $this->total_amount);
        $stmt->bindParam(':discount_amount', $this->discount_amount);
        $stmt->bindParam(':tax_amount', $this->tax_amount);
        $stmt->bindParam(':final_amount', $this->final_amount);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':payment_method', $this->payment_method);
        $stmt->bindParam(':payment_status', $this->payment_status);
        $stmt->bindParam(':notes', $this->notes);
        $stmt->bindParam(':due_date', $this->due_date);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Lấy tất cả hóa đơn
    public function getAll() {
        $query = "SELECT i.*, 
                         p.patient_code, u.full_name as patient_name,
                         a.appointment_code
                  FROM " . $this->table . " i
                  LEFT JOIN patients p ON i.patient_id = p.id
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN appointments a ON i.appointment_id = a.id
                  ORDER BY i.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tìm hóa đơn theo ID
    public function findById($id) {
        $query = "SELECT i.*, 
                         p.patient_code, u.full_name as patient_name, u.phone as patient_phone,
                         a.appointment_code, a.appointment_date
                  FROM " . $this->table . " i
                  LEFT JOIN patients p ON i.patient_id = p.id
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN appointments a ON i.appointment_id = a.id
                  WHERE i.id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy hóa đơn theo bệnh nhân
    public function getByPatientId($patient_id) {
        $query = "SELECT i.*, a.appointment_code
                  FROM " . $this->table . " i
                  LEFT JOIN appointments a ON i.appointment_id = a.id
                  WHERE i.patient_id = :patient_id
                  ORDER BY i.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật trạng thái thanh toán
    public function updatePaymentStatus($id, $status, $paid_date = null) {
        $query = "UPDATE " . $this->table . " 
                  SET payment_status = :status, 
                      paid_date = :paid_date,
                      status = CASE WHEN :status = 'paid' THEN 'paid' ELSE status END
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':paid_date', $paid_date);

        return $stmt->execute();
    }

    // Xóa hóa đơn
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
}
