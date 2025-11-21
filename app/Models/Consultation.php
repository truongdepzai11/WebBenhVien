<?php
require_once __DIR__ . '/../../config/database.php';

class Consultation {
    private $conn;
    private $table = 'consultations';

    public $id;
    public $code;
    public $patient_id;
    public $doctor_id; // nullable
    public $subject;
    public $status; // open, answered, closed
    public $last_message_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    private function generateCode() {
        $q = "SELECT COUNT(*) AS c FROM {$this->table}";
        $stmt = $this->conn->prepare($q);
        $stmt->execute();
        $n = ((int)$stmt->fetch(PDO::FETCH_ASSOC)['c']) + 1;
        return 'CST' . str_pad($n, 6, '0', STR_PAD_LEFT);
    }

    public function create() {
        if (empty($this->code)) $this->code = $this->generateCode();
        $q = "INSERT INTO {$this->table} (code, patient_id, doctor_id, subject, status, last_message_at)
              VALUES (:code, :patient_id, :doctor_id, :subject, :status, NOW())";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(':code', $this->code);
        $stmt->bindParam(':patient_id', $this->patient_id);
        $stmt->bindParam(':doctor_id', $this->doctor_id);
        $stmt->bindParam(':subject', $this->subject);
        $status = $this->status ?: 'open';
        $stmt->bindParam(':status', $status);
        if ($stmt->execute()) { $this->id = $this->conn->lastInsertId(); return true; }
        return false;
    }

    public function touch() {
        $q = "UPDATE {$this->table} SET last_message_at = NOW() WHERE id = :id";
        $st = $this->conn->prepare($q);
        $st->bindParam(':id', $this->id);
        return $st->execute();
    }

    public function setStatus($status) {
        $q = "UPDATE {$this->table} SET status = :s WHERE id = :id";
        $st = $this->conn->prepare($q);
        $st->bindParam(':s', $status);
        $st->bindParam(':id', $this->id);
        return $st->execute();
    }

    public function findById($id) {
        $q = "SELECT c.*, u.full_name AS patient_name, du.full_name AS doctor_name
              FROM {$this->table} c
              LEFT JOIN patients p ON c.patient_id = p.id
              LEFT JOIN users u ON p.user_id = u.id
              LEFT JOIN doctors d ON c.doctor_id = d.id
              LEFT JOIN users du ON d.user_id = du.id
              WHERE c.id = :id LIMIT 1";
        $st = $this->conn->prepare($q);
        $st->bindParam(':id', $id);
        $st->execute();
        return $st->fetch(PDO::FETCH_ASSOC);
    }

    public function listForPatient($patient_id, $limit = 100) {
        $q = "SELECT * FROM {$this->table} WHERE patient_id = :pid ORDER BY last_message_at DESC LIMIT :lim";
        $st = $this->conn->prepare($q);
        $st->bindParam(':pid', $patient_id, PDO::PARAM_INT);
        $st->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listForDoctorUser($doctor_user_id, $status = null, $limit = 100) {
        $q = "SELECT c.*, u.full_name AS patient_name
              FROM {$this->table} c
              LEFT JOIN doctors d ON c.doctor_id = d.id
              LEFT JOIN patients p ON c.patient_id = p.id
              LEFT JOIN users u ON p.user_id = u.id
              WHERE d.user_id = :uid" . ($status ? " AND c.status = :st" : "") . "
              ORDER BY c.last_message_at DESC LIMIT :lim";
        $st = $this->conn->prepare($q);
        $st->bindParam(':uid', $doctor_user_id, PDO::PARAM_INT);
        if ($status) $st->bindParam(':st', $status);
        $st->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}
