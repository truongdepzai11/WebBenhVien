<?php

require_once __DIR__ . '/../../config/database.php';

class Appointment {
    private $conn;
    private $table = 'appointments';

    public $id;
    public $appointment_code;
    public $patient_id;
    public $doctor_id;
    public $coordinator_doctor_id;
    public $package_id;
    public $appointment_type;
    public $total_price;
    public $appointment_date;
    public $appointment_time;
    public $reason;
    public $status;
    public $notes;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Tạo lịch hẹn mới
    public function create() {
        $this->appointment_code = $this->generateAppointmentCode();

        $query = "INSERT INTO " . $this->table . " 
                  (appointment_code, patient_id, doctor_id, coordinator_doctor_id, package_id, appointment_type, total_price, appointment_date, appointment_time, reason, status, notes) 
                  VALUES (:appointment_code, :patient_id, :doctor_id, :coordinator_doctor_id, :package_id, :appointment_type, :total_price, :appointment_date, :appointment_time, :reason, :status, :notes)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':appointment_code', $this->appointment_code);
        $stmt->bindParam(':patient_id', $this->patient_id);
        $stmt->bindParam(':doctor_id', $this->doctor_id);
        $stmt->bindParam(':coordinator_doctor_id', $this->coordinator_doctor_id);
        $stmt->bindParam(':package_id', $this->package_id);
        $stmt->bindParam(':appointment_type', $this->appointment_type);
        $stmt->bindParam(':total_price', $this->total_price);
        $stmt->bindParam(':appointment_date', $this->appointment_date);
        $stmt->bindParam(':appointment_time', $this->appointment_time);
        $stmt->bindParam(':reason', $this->reason);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':notes', $this->notes);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Tạo mã lịch hẹn
    private function generateAppointmentCode() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $number = $row['total'] + 1;
        return 'APT' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Lấy tất cả lịch hẹn
    public function getAll() {
        $query = "SELECT a.*, 
                         p.patient_code, pu.full_name as patient_name,
                         d.doctor_code, du.full_name as doctor_name, s.name as specialization
                  FROM " . $this->table . " a
                  LEFT JOIN patients p ON a.patient_id = p.id
                  LEFT JOIN users pu ON p.user_id = pu.id
                  LEFT JOIN doctors d ON a.doctor_id = d.id
                  LEFT JOIN users du ON d.user_id = du.id
                  LEFT JOIN specializations s ON d.specialization_id = s.id
                  ORDER BY a.appointment_date DESC, a.appointment_time DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tìm lịch hẹn theo ID
    public function findById($id) {
        $query = "SELECT a.*, 
                         p.patient_code, pu.full_name as patient_name, pu.phone as patient_phone,
                         d.doctor_code, du.full_name as doctor_name, d.consultation_fee,
                         s.name as specialization
                  FROM " . $this->table . " a
                  LEFT JOIN patients p ON a.patient_id = p.id
                  LEFT JOIN users pu ON p.user_id = pu.id
                  LEFT JOIN doctors d ON a.doctor_id = d.id
                  LEFT JOIN users du ON d.user_id = du.id
                  LEFT JOIN specializations s ON d.specialization_id = s.id
                  WHERE a.id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy lịch hẹn theo bệnh nhân
    public function getByPatientId($patient_id) {
        $query = "SELECT a.*, 
                         d.doctor_code, du.full_name as doctor_name, s.name as specialization
                  FROM " . $this->table . " a
                  LEFT JOIN doctors d ON a.doctor_id = d.id
                  LEFT JOIN users du ON d.user_id = du.id
                  LEFT JOIN specializations s ON d.specialization_id = s.id
                  WHERE a.patient_id = :patient_id
                  ORDER BY a.appointment_date DESC, a.appointment_time DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy lịch hẹn theo bác sĩ
    public function getByDoctorId($doctor_id) {
        $query = "SELECT a.*, 
                         p.patient_code, pu.full_name as patient_name, pu.phone as patient_phone
                  FROM " . $this->table . " a
                  LEFT JOIN patients p ON a.patient_id = p.id
                  LEFT JOIN users pu ON p.user_id = pu.id
                  WHERE a.doctor_id = :doctor_id
                  ORDER BY a.appointment_date DESC, a.appointment_time DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctor_id', $doctor_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật lịch hẹn
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET appointment_date = :appointment_date,
                      appointment_time = :appointment_time,
                      reason = :reason,
                      status = :status,
                      notes = :notes
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':appointment_date', $this->appointment_date);
        $stmt->bindParam(':appointment_time', $this->appointment_time);
        $stmt->bindParam(':reason', $this->reason);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':notes', $this->notes);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Cập nhật trạng thái
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    // Cập nhật thông tin hủy lịch
    public function updateCancellation($id, $status, $reason, $fee) {
        $query = "UPDATE " . $this->table . " 
                  SET status = :status, 
                      cancellation_reason = :reason,
                      cancellation_fee = :fee,
                      cancelled_at = NOW()
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':reason', $reason);
        $stmt->bindParam(':fee', $fee);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // Kiểm tra lịch hẹn trùng
    public function checkConflict($doctor_id, $date, $time, $exclude_id = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE doctor_id = :doctor_id 
                  AND appointment_date = :date 
                  AND appointment_time = :time 
                  AND status != 'cancelled'";
        
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctor_id', $doctor_id);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['count'] > 0;
    }

    // Xóa lịch hẹn
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    // Lấy appointments theo package_appointment_id
    public function getByPackageAppointmentId($packageAppointmentId) {
        $query = "SELECT a.*, 
                         u.full_name as doctor_name, 
                         s.name as specialization
                  FROM " . $this->table . " a
                  LEFT JOIN doctors d ON a.doctor_id = d.id
                  LEFT JOIN users u ON d.user_id = u.id
                  LEFT JOIN specializations s ON d.specialization_id = s.id
                  WHERE a.package_appointment_id = :package_appointment_id
                  ORDER BY a.appointment_date, a.appointment_time";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':package_appointment_id', $packageAppointmentId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Kiểm tra bác sĩ có rảnh không
    public function isDoctorAvailable($doctorId, $date, $time) {
        $query = "SELECT COUNT(*) as count 
                  FROM " . $this->table . " 
                  WHERE doctor_id = :doctor_id 
                  AND appointment_date = :date 
                  AND appointment_time = :time
                  AND status NOT IN ('cancelled', 'late_cancelled')";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] == 0; // True nếu không có lịch trùng
    }
}
