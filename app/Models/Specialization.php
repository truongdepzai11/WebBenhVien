<?php

require_once __DIR__ . '/../../config/database.php';

class Specialization {
    private $conn;
    private $table = 'specializations';

    public $id;
    public $name;
    public $description;
    public $min_age;
    public $max_age;
    public $gender_requirement;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Lấy tất cả chuyên khoa
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tìm chuyên khoa theo ID
    public function findById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tìm chuyên khoa theo tên
    public function findByName($name) {
        $query = "SELECT * FROM " . $this->table . " WHERE name = :name LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Kiểm tra bệnh nhân có đủ điều kiện khám chuyên khoa không
    public function checkPatientEligibility($specialization_name, $patient_age, $patient_gender) {
        $specialization = $this->findByName($specialization_name);
        
        if (!$specialization) {
            return ['eligible' => false, 'reason' => 'Chuyên khoa không tồn tại'];
        }

        // Kiểm tra độ tuổi
        if ($patient_age < $specialization['min_age']) {
            return [
                'eligible' => false, 
                'reason' => "Chuyên khoa này yêu cầu độ tuổi tối thiểu {$specialization['min_age']} tuổi"
            ];
        }

        if ($patient_age > $specialization['max_age']) {
            return [
                'eligible' => false, 
                'reason' => "Chuyên khoa này chỉ dành cho người dưới {$specialization['max_age']} tuổi"
            ];
        }

        // Kiểm tra giới tính
        if ($specialization['gender_requirement'] !== 'both') {
            if ($patient_gender !== $specialization['gender_requirement']) {
                $gender_text = $specialization['gender_requirement'] === 'male' ? 'nam' : 'nữ';
                return [
                    'eligible' => false, 
                    'reason' => "Chuyên khoa này chỉ dành cho giới tính {$gender_text}"
                ];
            }
        }

        return ['eligible' => true, 'reason' => ''];
    }

    // Lấy chuyên khoa phù hợp với bệnh nhân
    public function getEligibleForPatient($patient_age, $patient_gender) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE :age BETWEEN min_age AND max_age 
                  AND (gender_requirement = 'both' OR gender_requirement = :gender)
                  ORDER BY name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':age', $patient_age);
        $stmt->bindParam(':gender', $patient_gender);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
