<?php
require_once __DIR__ . '/../../config/database.php';

class ConsultationMessage {
    private $conn;
    private $table = 'consultation_messages';

    public $id;
    public $consultation_id;
    public $sender_user_id;
    public $message_text;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $q = "INSERT INTO {$this->table} (consultation_id, sender_user_id, message_text) VALUES (:cid, :uid, :msg)";
        $st = $this->conn->prepare($q);
        $st->bindParam(':cid', $this->consultation_id);
        $st->bindParam(':uid', $this->sender_user_id);
        $st->bindParam(':msg', $this->message_text);
        if ($st->execute()) { $this->id = $this->conn->lastInsertId(); return true; }
        return false;
    }

    public function listByConsultation($consultation_id) {
        $q = "SELECT m.*, u.full_name AS sender_name
              FROM {$this->table} m
              LEFT JOIN users u ON m.sender_user_id = u.id
              WHERE m.consultation_id = :cid ORDER BY m.created_at ASC";
        $st = $this->conn->prepare($q);
        $st->bindParam(':cid', $consultation_id);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $q = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $st = $this->conn->prepare($q);
        $st->bindParam(':id', $id);
        $st->execute();
        return $st->fetch(PDO::FETCH_ASSOC);
    }
}
