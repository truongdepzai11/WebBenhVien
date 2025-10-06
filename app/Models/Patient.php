<?php

require_once __DIR__ . '/../../config/database.php';

class Patient {
    private $conn;
    private $table = 'patients';

    public $id;
    public $user_id;
    public $patient_code;
    public $date_of_birth;
    public $gender;
    public $address;
    public $blood_type;
    public $allergies;
    public $emergency_contact;
    public $emergency_phone;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function create() {
        $this->patient_code = $this->generatePatientCode();

        $query = "INSERT INTO " . $this->table . " 
                  (user_id, patient_code, date_of_birth, gender, address, blood_type, allergies, emergency_contact, emergency_phone) 
                  VALUES (:user_id, :patient_code, :date_of_birth, :gender, :address, :blood_type, :allergies, :emergency_contact, :emergency_phone)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':patient_code', $this->patient_code);
        $stmt->bindParam(':date_of_birth', $this->date_of_birth);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':blood_type', $this->blood_type);
        $stmt->bindParam(':allergies', $this->allergies);
        $stmt->bindParam(':emergency_contact', $this->emergency_contact);
        $stmt->bindParam(':emergency_phone', $this->emergency_phone);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    private function generatePatientCode() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $number = $row['total'] + 1;
        return 'PAT' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function getAll() {
        $query = "SELECT p.id, p.patient_code, p.blood_type,
                         u.full_name, u.email, u.phone, u.date_of_birth, u.gender, u.address, u.created_at
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  ORDER BY u.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $query = "SELECT p.*, u.full_name, u.email, u.phone, u.username, u.date_of_birth, u.gender, u.address
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  WHERE p.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByUserId($user_id) {
        $query = "SELECT p.*, u.date_of_birth, u.gender, u.address FROM " . $this->table . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  WHERE p.user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByCode($code) {
        $query = "SELECT p.*, u.full_name, u.email, u.phone, u.date_of_birth, u.gender, u.address
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  WHERE p.patient_code = :code LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        // Cập nhật bảng patients
        $query = "UPDATE " . $this->table . " 
                  SET date_of_birth = :date_of_birth, gender = :gender, address = :address,
                      blood_type = :blood_type, allergies = :allergies,
                      emergency_contact = :emergency_contact, emergency_phone = :emergency_phone
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date_of_birth', $this->date_of_birth);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':blood_type', $this->blood_type);
        $stmt->bindParam(':allergies', $this->allergies);
        $stmt->bindParam(':emergency_contact', $this->emergency_contact);
        $stmt->bindParam(':emergency_phone', $this->emergency_phone);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            // Đồng bộ sang bảng users
            $queryUser = "UPDATE users SET date_of_birth = :date_of_birth, gender = :gender, address = :address
                          WHERE id = (SELECT user_id FROM patients WHERE id = :patient_id)";
            $stmtUser = $this->conn->prepare($queryUser);
            $stmtUser->bindParam(':date_of_birth', $this->date_of_birth);
            $stmtUser->bindParam(':gender', $this->gender);
            $stmtUser->bindParam(':address', $this->address);
            $stmtUser->bindParam(':patient_id', $this->id);
            $stmtUser->execute();
            return true;
        }
        return false;
    }

    public function search($keyword) {
        $query = "SELECT p.*, u.full_name, u.email, u.phone, u.date_of_birth, u.gender, u.address
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  WHERE p.patient_code LIKE :keyword OR u.full_name LIKE :keyword OR u.email LIKE :keyword
                  ORDER BY u.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $search = "%{$keyword}%";
        $stmt->bindParam(':keyword', $search);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
