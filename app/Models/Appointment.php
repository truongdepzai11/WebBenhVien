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
    public $package_appointment_id;
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

    // Lấy lịch tổng hợp của gói (reason có chứa ":")
    public function getSummaryByPackageAppointmentId($packageAppointmentId) {
        $query = "SELECT a.*, 
                         u.full_name as doctor_name, 
                         s.name as specialization,
                         p.id as patient_id,
                         pu.full_name as patient_name
                  FROM " . $this->table . " a
                  LEFT JOIN doctors d ON a.doctor_id = d.id
                  LEFT JOIN users u ON d.user_id = u.id
                  LEFT JOIN specializations s ON d.specialization_id = s.id
                  LEFT JOIN patients p ON a.patient_id = p.id
                  LEFT JOIN users pu ON p.user_id = pu.id
                  WHERE a.package_appointment_id = :pid
                    AND a.reason LIKE '%:%'
                  ORDER BY a.created_at DESC
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pid', $packageAppointmentId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Đếm số lịch dịch vụ đã được phân công (có doctor_id) cho gói
    // Loại trừ lịch tổng hợp gói (reason có ":") và đếm theo DISTINCT reason để không bị trùng
    public function countAssignedByPackageAppointmentId($packageAppointmentId) {
        $query = "SELECT COUNT(DISTINCT LOWER(TRIM(reason))) AS c FROM " . $this->table . "
                  WHERE package_appointment_id = :pkg_id
                    AND doctor_id IS NOT NULL
                    AND reason IS NOT NULL
                    AND reason NOT LIKE '%:%'
                    AND status IN ('pending','confirmed','completed')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pkg_id', $packageAppointmentId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['c'] ?? 0);
    }

    public function getChildCompletionStats($packageAppointmentId) {
        $query = "SELECT COUNT(*) AS total_children,
                         SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_children
                  FROM " . $this->table . "
                  WHERE package_appointment_id = :pkg_id
                    AND doctor_id IS NOT NULL
                    AND reason NOT LIKE '%:%'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pkg_id', $packageAppointmentId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        return [
            'total' => (int)($row['total_children'] ?? 0),
            'completed' => (int)($row['completed_children'] ?? 0),
        ];
    }

    // Tạo lịch hẹn mới
    public function create() {
        $this->appointment_code = $this->generateAppointmentCode();

        $query = "INSERT INTO " . $this->table . " 
                  (appointment_code, patient_id, doctor_id, coordinator_doctor_id, package_id, package_appointment_id, appointment_type, total_price, appointment_date, appointment_time, reason, status, notes) 
                  VALUES (:appointment_code, :patient_id, :doctor_id, :coordinator_doctor_id, :package_id, :package_appointment_id, :appointment_type, :total_price, :appointment_date, :appointment_time, :reason, :status, :notes)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':appointment_code', $this->appointment_code);
        $stmt->bindParam(':patient_id', $this->patient_id);
        $stmt->bindParam(':doctor_id', $this->doctor_id);
        $stmt->bindParam(':coordinator_doctor_id', $this->coordinator_doctor_id);
        $stmt->bindParam(':package_id', $this->package_id);
        $stmt->bindParam(':package_appointment_id', $this->package_appointment_id);
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

    // Lấy các lịch hẹn giữa bác sĩ và bệnh nhân (đã xác nhận/hoàn thành)
    public function getByDoctorAndPatient($doctor_id, $patient_id) {
        $query = "SELECT id, appointment_date, appointment_time, status
                  FROM " . $this->table . "
                  WHERE doctor_id = :doctor_id
                    AND patient_id = :patient_id
                    AND status IN ('confirmed','completed')
                  ORDER BY appointment_date DESC, appointment_time DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctor_id', $doctor_id);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tìm lịch hẹn theo ID
    public function findById($id) {
        $query = "SELECT a.*, 
                         p.patient_code, pu.full_name as patient_name, pu.phone as patient_phone,
                         d.doctor_code, du.full_name as doctor_name, d.consultation_fee,
                         s.name as specialization,
                         hp.name as package_name, hp.package_code
                  FROM " . $this->table . " a
                  LEFT JOIN patients p ON a.patient_id = p.id
                  LEFT JOIN users pu ON p.user_id = pu.id
                  LEFT JOIN doctors d ON a.doctor_id = d.id
                  LEFT JOIN users du ON d.user_id = du.id
                  LEFT JOIN specializations s ON d.specialization_id = s.id
                  LEFT JOIN health_packages hp ON a.package_id = hp.id
                  WHERE a.id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tìm appointment tổng hợp của gói (được tạo khi đặt gói)
    public function findSummaryByPackageAppointmentId($packageAppointmentId) {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE package_appointment_id = :pkg_id AND appointment_type = 'package'
                  ORDER BY id ASC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pkg_id', $packageAppointmentId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật một số field cho appointment theo id
    public function updateFields($id, $data) {
        $allowed = ['doctor_id','appointment_date','appointment_time','reason','status','notes'];
        $sets = [];
        $params = [':id' => $id];
        foreach ($data as $k => $v) {
            if (in_array($k, $allowed, true)) {
                $sets[] = "$k = :$k";
                $params[":$k"] = $v;
            }
        }
        if (empty($sets)) return false;
        $query = "UPDATE " . $this->table . " SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        foreach ($params as $p => $val) {
            $stmt->bindValue($p, $val);
        }
        return $stmt->execute();
    }

    // Lấy danh sách bác sĩ đã được phân công cho gói (từ các appointment chi tiết)
    public function getAssignedDoctorsByPackageAppointmentId($packageAppointmentId) {
        $query = "SELECT du.full_name AS doctor_name, s.name AS specialization
                  FROM " . $this->table . " a
                  LEFT JOIN doctors d ON a.doctor_id = d.id
                  LEFT JOIN users du ON d.user_id = du.id
                  LEFT JOIN specializations s ON d.specialization_id = s.id
                  WHERE a.package_appointment_id = :pkg_id AND a.doctor_id IS NOT NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pkg_id', $packageAppointmentId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    // Cập nhật trạng thái các lịch con (dịch vụ) trong gói theo package_appointment_id
    // Chỉ áp dụng cho các lịch KHÔNG phải tổng hợp (reason không chứa ':') và đã có doctor_id
    public function updateChildrenStatusByPackageAppointmentId($packageAppointmentId, array $fromStatuses, $toStatus) {
        if (empty($fromStatuses)) return false;
        // Build named placeholders for IN list: :st0, :st1, ...
        $inNames = [];
        foreach (array_values($fromStatuses) as $idx => $st) {
            $inNames[] = ":st{$idx}";
        }
        $in = implode(',', $inNames);

        $query = "UPDATE " . $this->table . " SET status = :toStatus
                  WHERE package_appointment_id = :pkg
                    AND doctor_id IS NOT NULL
                    AND reason NOT LIKE '%:%'
                    AND status IN (" . $in . ")";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':toStatus', $toStatus);
        $stmt->bindValue(':pkg', $packageAppointmentId);
        foreach (array_values($fromStatuses) as $idx => $st) {
            $stmt->bindValue(":st{$idx}", $st);
        }
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

    // Lấy danh sách các ngày khám khác nhau trong gói
    public function getAppointmentDatesByPackageAppointmentId($packageAppointmentId) {
        $query = "SELECT DISTINCT appointment_date 
                  FROM " . $this->table . " 
                  WHERE package_appointment_id = :package_appointment_id 
                  AND doctor_id IS NOT NULL
                  ORDER BY appointment_date";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':package_appointment_id', $packageAppointmentId);
        $stmt->execute();

        $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $dates ?: [];
    }
}
