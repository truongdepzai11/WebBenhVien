<?php
require_once __DIR__ . '/../../config/database.php';

class ConsultationAttachment {
    private $conn;
    private $table = 'consultation_attachments';

    public $id;
    public $message_id;
    public $file_path;
    public $file_name;
    public $file_size;
    public $mime_type;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $q = "INSERT INTO {$this->table} (message_id, file_path, file_name, file_size, mime_type)
              VALUES (:mid, :path, :name, :size, :mime)";
        $st = $this->conn->prepare($q);
        $st->bindParam(':mid', $this->message_id);
        $st->bindParam(':path', $this->file_path);
        $st->bindParam(':name', $this->file_name);
        $st->bindParam(':size', $this->file_size);
        $st->bindParam(':mime', $this->mime_type);
        if ($st->execute()) { $this->id = $this->conn->lastInsertId(); return true; }
        return false;
    }

    public function listByMessage($message_id) {
        $q = "SELECT * FROM {$this->table} WHERE message_id = :mid";
        $st = $this->conn->prepare($q);
        $st->bindParam(':mid', $message_id);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listByConsultation($consultation_id) {
        $q = "SELECT a.*
              FROM {$this->table} a
              INNER JOIN consultation_messages m ON a.message_id = m.id
              WHERE m.consultation_id = :cid";
        $st = $this->conn->prepare($q);
        $st->bindParam(':cid', $consultation_id);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}
