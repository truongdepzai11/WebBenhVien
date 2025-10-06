<?php

require_once __DIR__ . '/../../config/database.php';

class InvoiceItem {
    private $conn;
    private $table = 'invoice_items';

    public $id;
    public $invoice_id;
    public $item_type;
    public $item_id;
    public $description;
    public $quantity;
    public $unit_price;
    public $total_price;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Tạo item mới
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (invoice_id, item_type, item_id, description, quantity, unit_price, total_price) 
                  VALUES (:invoice_id, :item_type, :item_id, :description, :quantity, :unit_price, :total_price)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':invoice_id', $this->invoice_id);
        $stmt->bindParam(':item_type', $this->item_type);
        $stmt->bindParam(':item_id', $this->item_id);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':total_price', $this->total_price);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Lấy items theo invoice_id
    public function getByInvoiceId($invoice_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE invoice_id = :invoice_id 
                  ORDER BY created_at ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':invoice_id', $invoice_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Xóa items theo invoice_id
    public function deleteByInvoiceId($invoice_id) {
        $query = "DELETE FROM " . $this->table . " WHERE invoice_id = :invoice_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':invoice_id', $invoice_id);

        return $stmt->execute();
    }
}
