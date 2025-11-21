<?php

require_once __DIR__ . '/../../config/database.php';

class HealthPackage {
    private $conn;
    private $table = 'health_packages';

    public $id;
    public $package_code;
    public $name;
    public $description;
    public $price_male;
    public $price_female;
    public $gender_requirement;
    public $min_age;
    public $max_age;
    public $is_active;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Lấy tất cả gói khám đang hoạt động
    public function getAllActive() {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE is_active = 1 
                  ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy các dịch vụ đã chọn theo appointment tổng hợp (từ bảng appointment_package_services)
    public function getSelectedServicesByAppointmentId($appointmentId) {
        $query = "SELECT ps.*
                  FROM appointment_package_services aps
                  INNER JOIN package_services ps ON aps.service_id = ps.id
                  WHERE aps.appointment_id = :appointment_id
                  ORDER BY ps.display_order, ps.service_category, ps.service_name";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':appointment_id', $appointmentId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy gói khám theo ID
    public function findById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy gói khám phù hợp với bệnh nhân
    public function getPackagesForPatient($gender, $age) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE is_active = 1 
                  AND (gender_requirement = 'both' OR gender_requirement = :gender)
                  AND min_age <= :age 
                  AND max_age >= :age
                  ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':age', $age);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách dịch vụ trong gói
    public function getServices($package_id) {
        $query = "SELECT * FROM package_services 
                  WHERE package_id = :package_id 
                  ORDER BY service_category, service_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':package_id', $package_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tạo gói khám mới (Admin)
    public function create() {
        if (empty($this->package_code)) {
            $this->package_code = $this->generatePackageCode();
        }

        $query = "INSERT INTO " . $this->table . " 
                  (package_code, name, description, gender_requirement, min_age, max_age, is_active) 
                  VALUES (:package_code, :name, :description, :gender_requirement, :min_age, :max_age, :is_active)";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':package_code', $this->package_code);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':gender_requirement', $this->gender_requirement);
        $stmt->bindParam(':min_age', $this->min_age);
        $stmt->bindParam(':max_age', $this->max_age);
        $stmt->bindParam(':is_active', $this->is_active);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Generate mã gói khám
    private function generatePackageCode() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $number = $row['total'] + 1;
        return 'PKG' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // Cập nhật gói khám
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET name = :name,
                      description = :description,
                      gender_requirement = :gender_requirement,
                      min_age = :min_age,
                      max_age = :max_age,
                      is_active = :is_active
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':gender_requirement', $this->gender_requirement);
        $stmt->bindParam(':min_age', $this->min_age);
        $stmt->bindParam(':max_age', $this->max_age);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Lấy danh sách dịch vụ trong gói
    public function getPackageServices($packageId) {
        $query = "SELECT * 
                  FROM package_services 
                  WHERE package_id = :package_id
                  ORDER BY display_order, service_category, service_name";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':package_id', $packageId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
