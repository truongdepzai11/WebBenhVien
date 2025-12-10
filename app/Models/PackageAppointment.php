<?php

require_once __DIR__ . '/../../config/database.php';

class PackageAppointment {
    private $conn;
    private $table = 'package_appointments';

    public $id;
    public $patient_id;
    public $package_id;
    public $appointment_date;
    public $status;
    public $notes;
    public $total_price;
    public $created_by;
    public $created_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Tạo đăng ký gói khám mới
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (patient_id, package_id, appointment_date, status, notes, total_price, created_by, appointment_year_month, created_at) 
                  VALUES 
                  (:patient_id, :package_id, :appointment_date, :status, :notes, :total_price, :created_by, DATE_FORMAT(NOW(), '%Y-%m'), NOW())";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':patient_id', $this->patient_id);
        $stmt->bindParam(':package_id', $this->package_id);
        $stmt->bindParam(':appointment_date', $this->appointment_date);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':notes', $this->notes);
        $stmt->bindParam(':total_price', $this->total_price);
        $stmt->bindParam(':created_by', $this->created_by);

        // execute() will throw PDOException if there's a UNIQUE constraint violation
        $stmt->execute();
        
        $this->id = $this->conn->lastInsertId();
        return true;
    }

    // Lấy tất cả đăng ký gói khám
    public function getAll() {
        $query = "SELECT pa.*, 
                         u_patient.full_name as patient_name, 
                         p.patient_code, 
                         hp.name as package_name, 
                         u_creator.full_name as created_by_name
                  FROM " . $this->table . " pa
                  LEFT JOIN patients p ON pa.patient_id = p.id
                  LEFT JOIN users u_patient ON p.user_id = u_patient.id
                  LEFT JOIN health_packages hp ON pa.package_id = hp.id
                  LEFT JOIN users u_creator ON pa.created_by = u_creator.id
                  ORDER BY pa.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy theo patient_id
    public function getByPatientId($patientId) {
        $query = "SELECT pa.*, 
                         u_patient.full_name as patient_name, 
                         p.patient_code, 
                         hp.name as package_name, 
                         u_creator.full_name as created_by_name
                  FROM " . $this->table . " pa
                  LEFT JOIN patients p ON pa.patient_id = p.id
                  LEFT JOIN users u_patient ON p.user_id = u_patient.id
                  LEFT JOIN health_packages hp ON pa.package_id = hp.id
                  LEFT JOIN users u_creator ON pa.created_by = u_creator.id
                  WHERE pa.patient_id = :patient_id
                  ORDER BY pa.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy theo ID
    public function findById($id) {
        $query = "SELECT pa.*, 
                         u_patient.full_name as patient_name, 
                         p.patient_code, 
                         hp.name as package_name
                  FROM " . $this->table . " pa
                  LEFT JOIN patients p ON pa.patient_id = p.id
                  LEFT JOIN users u_patient ON p.user_id = u_patient.id
                  LEFT JOIN health_packages hp ON pa.package_id = hp.id
                  WHERE pa.id = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật trạng thái
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " 
                  SET status = :status 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    // Kiểm tra xem bệnh nhân đã đăng ký gói này trong tháng hiện tại chưa
    public function hasBookingThisMonth($patientId, $packageId) {
        $month = date('Y-m');
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE patient_id = :patient_id 
                  AND package_id = :package_id 
                  AND appointment_year_month = :month
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->bindParam(':package_id', $packageId);
        $stmt->bindParam(':month', $month);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    // Lấy theo patient_id với thông tin chi tiết dịch vụ và bác sĩ
    public function getByPatientIdWithServices($patientId) {
        $query = "SELECT pa.*, 
                         u_patient.full_name as patient_name, 
                         p.patient_code, 
                         hp.name as package_name,
                         COUNT(ps.id) as services_count,
                         GROUP_CONCAT(DISTINCT ps.service_name ORDER BY ps.display_order) as service_names,
                         GROUP_CONCAT(DISTINCT du.full_name ORDER BY du.full_name) as doctor_names
                  FROM " . $this->table . " pa
                  LEFT JOIN patients p ON pa.patient_id = p.id
                  LEFT JOIN users u_patient ON p.user_id = u_patient.id
                  LEFT JOIN health_packages hp ON pa.package_id = hp.id
                  LEFT JOIN package_services ps ON hp.id = ps.package_id
                  LEFT JOIN package_service_doctors psd ON ps.id = psd.service_id
                  LEFT JOIN doctors d ON psd.doctor_id = d.id  
                  LEFT JOIN users du ON d.user_id = du.id
                  WHERE pa.patient_id = :patient_id
                  GROUP BY pa.id
                  ORDER BY pa.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Convert comma-separated strings to arrays
        foreach ($results as &$result) {
            $result['doctor_names'] = $result['doctor_names'] ? explode(',', $result['doctor_names']) : [];
            $result['appointment_times'] = [$result['appointment_time']]; // Single appointment time
        }
        
        return $results;
    }
}
