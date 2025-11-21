<?php

require_once __DIR__ . '/../../config/database.php';

class MedicalRecord {
    private $conn;
    private $table = 'medical_records';

    public $id;
    public $record_code;
    public $patient_id;
    public $doctor_id;
    public $appointment_id;
    public $diagnosis;
    public $symptoms;
    public $treatment;
    public $prescription;
    public $test_results;
    public $notes;
    public $visit_date;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Tạo hồ sơ bệnh án mới
    public function create() {
        $this->record_code = $this->generateRecordCode();

        $query = "INSERT INTO " . $this->table . " 
                  (record_code, patient_id, doctor_id, appointment_id, diagnosis, symptoms, treatment, prescription, test_results, notes, visit_date) 
                  VALUES (:record_code, :patient_id, :doctor_id, :appointment_id, :diagnosis, :symptoms, :treatment, :prescription, :test_results, :notes, :visit_date)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':record_code', $this->record_code);
        $stmt->bindParam(':patient_id', $this->patient_id);
        $stmt->bindParam(':doctor_id', $this->doctor_id);
        $stmt->bindParam(':appointment_id', $this->appointment_id);
        $stmt->bindParam(':diagnosis', $this->diagnosis);
        $stmt->bindParam(':symptoms', $this->symptoms);
        $stmt->bindParam(':treatment', $this->treatment);
        $stmt->bindParam(':prescription', $this->prescription);
        $stmt->bindParam(':test_results', $this->test_results);
        $stmt->bindParam(':notes', $this->notes);
        $stmt->bindParam(':visit_date', $this->visit_date);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Tạo mã hồ sơ bệnh án
    private function generateRecordCode() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $number = $row['total'] + 1;
        return 'MR' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    // Lấy tất cả hồ sơ bệnh án
    public function getAll() {
        $query = "SELECT mr.*, 
                         p.patient_code, pu.full_name as patient_name,
                         d.doctor_code, du.full_name as doctor_name, s.name as specialization
                  FROM " . $this->table . " mr
                  LEFT JOIN patients p ON mr.patient_id = p.id
                  LEFT JOIN users pu ON p.user_id = pu.id
                  LEFT JOIN doctors d ON mr.doctor_id = d.id
                  LEFT JOIN users du ON d.user_id = du.id
                  LEFT JOIN specializations s ON d.specialization_id = s.id
                  ORDER BY mr.visit_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tìm hồ sơ bệnh án theo appointment_id (nếu được tạo từ lịch hẹn)
    public function findByAppointmentId($appointment_id) {
        $query = "SELECT mr.*, 
                         p.patient_code, pu.full_name as patient_name,
                         d.doctor_code, du.full_name as doctor_name, s.name as specialization
                  FROM " . $this->table . " mr
                  LEFT JOIN patients p ON mr.patient_id = p.id
                  LEFT JOIN users pu ON p.user_id = pu.id
                  LEFT JOIN doctors d ON mr.doctor_id = d.id
                  LEFT JOIN users du ON d.user_id = du.id
                  LEFT JOIN specializations s ON d.specialization_id = s.id
                  WHERE mr.appointment_id = :appointment_id
                  ORDER BY mr.visit_date DESC
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':appointment_id', $appointment_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tìm hồ sơ bệnh án theo ID
    public function findById($id) {
        $query = "SELECT mr.*, 
                         p.patient_code, pu.full_name as patient_name, p.date_of_birth, p.gender, p.blood_type,
                         d.doctor_code, du.full_name as doctor_name, s.name as specialization
                  FROM " . $this->table . " mr
                  LEFT JOIN patients p ON mr.patient_id = p.id
                  LEFT JOIN users pu ON p.user_id = pu.id
                  LEFT JOIN doctors d ON mr.doctor_id = d.id
                  LEFT JOIN users du ON d.user_id = du.id
                  LEFT JOIN specializations s ON d.specialization_id = s.id
                  WHERE mr.id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy hồ sơ bệnh án theo bệnh nhân
    public function getByPatientId($patient_id) {
        $query = "SELECT mr.*, 
                         p.patient_code, pu.full_name as patient_name,
                         d.doctor_code, du.full_name as doctor_name, s.name as specialization
                  FROM " . $this->table . " mr
                  LEFT JOIN patients p ON mr.patient_id = p.id
                  LEFT JOIN users pu ON p.user_id = pu.id
                  LEFT JOIN doctors d ON mr.doctor_id = d.id
                  LEFT JOIN users du ON d.user_id = du.id
                  LEFT JOIN specializations s ON d.specialization_id = s.id
                  WHERE mr.patient_id = :patient_id
                  ORDER BY mr.visit_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy hồ sơ bệnh án theo bác sĩ
    public function getByDoctorId($doctor_id) {
        $query = "SELECT mr.*, 
                         p.patient_code, pu.full_name as patient_name,
                         d.doctor_code, du.full_name as doctor_name, s.name as specialization
                  FROM " . $this->table . " mr
                  LEFT JOIN patients p ON mr.patient_id = p.id
                  LEFT JOIN users pu ON p.user_id = pu.id
                  LEFT JOIN doctors d ON mr.doctor_id = d.id
                  LEFT JOIN users du ON d.user_id = du.id
                  LEFT JOIN specializations s ON d.specialization_id = s.id
                  WHERE mr.doctor_id = :doctor_id
                  ORDER BY mr.visit_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctor_id', $doctor_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật hồ sơ bệnh án
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET diagnosis = :diagnosis,
                      symptoms = :symptoms,
                      treatment = :treatment,
                      prescription = :prescription,
                      test_results = :test_results,
                      notes = :notes,
                      visit_date = :visit_date
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':diagnosis', $this->diagnosis);
        $stmt->bindParam(':symptoms', $this->symptoms);
        $stmt->bindParam(':treatment', $this->treatment);
        $stmt->bindParam(':prescription', $this->prescription);
        $stmt->bindParam(':test_results', $this->test_results);
        $stmt->bindParam(':notes', $this->notes);
        $stmt->bindParam(':visit_date', $this->visit_date);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Xóa hồ sơ bệnh án
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    // Lấy đơn thuốc của hồ sơ bệnh án
    public function getPrescriptions($record_id) {
        $query = "SELECT p.*, m.name as medicine_name, m.unit, m.price
                  FROM prescriptions p
                  LEFT JOIN medicines m ON p.medicine_id = m.id
                  WHERE p.medical_record_id = :record_id
                  ORDER BY p.created_at";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':record_id', $record_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

