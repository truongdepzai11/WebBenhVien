<?php

require_once __DIR__ . '/../../config/database.php';

class Doctor {
    private $conn;
    private $table = 'doctors';

    public $id;
    public $user_id;
    public $doctor_code;
    public $specialization;
    public $license_number;
    public $qualification;
    public $experience_years;
    public $consultation_fee;
    public $available_days;
    public $available_hours;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Đếm số lượng bác sĩ
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Tạo bác sĩ mới
    public function create() {
        $this->doctor_code = $this->generateDoctorCode();

        $query = "INSERT INTO " . $this->table . " 
                  (user_id, doctor_code, specialization, license_number, qualification, experience_years, consultation_fee, available_days, available_hours) 
                  VALUES (:user_id, :doctor_code, :specialization, :license_number, :qualification, :experience_years, :consultation_fee, :available_days, :available_hours)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':doctor_code', $this->doctor_code);
        $stmt->bindParam(':specialization', $this->specialization);
        $stmt->bindParam(':license_number', $this->license_number);
        $stmt->bindParam(':qualification', $this->qualification);
        $stmt->bindParam(':experience_years', $this->experience_years);
        $stmt->bindParam(':consultation_fee', $this->consultation_fee);
        $stmt->bindParam(':available_days', $this->available_days);
        $stmt->bindParam(':available_hours', $this->available_hours);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Tạo mã bác sĩ
    private function generateDoctorCode() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $number = $row['total'] + 1;
        return 'DOC' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Lấy tất cả bác sĩ
    public function getAll() {
        $query = "SELECT d.*, u.full_name, u.email, u.phone, s.name as specialization
                  FROM " . $this->table . " d
                  LEFT JOIN users u ON d.user_id = u.id
                  LEFT JOIN specializations s ON d.specialization_id = s.id
                  ORDER BY d.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tìm bác sĩ theo ID
    public function findById($id) {
        $query = "SELECT d.*, u.full_name, u.email, u.phone, u.username, s.name as specialization, s.id as specialization_id
                  FROM " . $this->table . " d
                  LEFT JOIN users u ON d.user_id = u.id
                  LEFT JOIN specializations s ON d.specialization_id = s.id
                  WHERE d.id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tìm bác sĩ theo user_id
    public function findByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tìm bác sĩ theo chuyên khoa
    public function findBySpecialization($specialization) {
        $query = "SELECT d.*, u.full_name, u.email, u.phone, s.name as specialization
                  FROM " . $this->table . " d
                  LEFT JOIN users u ON d.user_id = u.id
                  LEFT JOIN specializations s ON d.specialization_id = s.id
                  WHERE s.name = :specialization
                  ORDER BY d.experience_years DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':specialization', $specialization);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Alias cho getBySpecialization
    public function getBySpecialization($specialization) {
        return $this->findBySpecialization($specialization);
    }

    // Cập nhật thông tin bác sĩ
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET specialization = :specialization,
                      license_number = :license_number,
                      qualification = :qualification,
                      experience_years = :experience_years,
                      consultation_fee = :consultation_fee,
                      available_days = :available_days,
                      available_hours = :available_hours
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':specialization', $this->specialization);
        $stmt->bindParam(':license_number', $this->license_number);
        $stmt->bindParam(':qualification', $this->qualification);
        $stmt->bindParam(':experience_years', $this->experience_years);
        $stmt->bindParam(':consultation_fee', $this->consultation_fee);
        $stmt->bindParam(':available_days', $this->available_days);
        $stmt->bindParam(':available_hours', $this->available_hours);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Tìm kiếm bác sĩ
    public function search($keyword) {
        $query = "SELECT d.*, u.full_name, u.email, u.phone, s.name as specialization
                  FROM " . $this->table . " d
                  LEFT JOIN users u ON d.user_id = u.id
                  LEFT JOIN specializations s ON d.specialization_id = s.id
                  WHERE d.doctor_code LIKE :keyword 
                     OR u.full_name LIKE :keyword
                     OR s.name LIKE :keyword
                  ORDER BY d.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $search = "%{$keyword}%";
        $stmt->bindParam(':keyword', $search);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách chuyên khoa
    public function getSpecializations() {
        $query = "SELECT DISTINCT s.name 
                  FROM " . $this->table . " d
                  LEFT JOIN specializations s ON d.specialization_id = s.id
                  WHERE s.name IS NOT NULL
                  ORDER BY s.name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
