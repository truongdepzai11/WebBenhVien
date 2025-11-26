<?php

require_once __DIR__ . '/../../config/database.php';

class AppointmentPackageService {
    private $conn;
    private $table = 'appointment_package_services';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Tìm APS theo package_appointment_id (từ appointments.summary) và tên dịch vụ (reason)
    public function findByPackageAppointmentAndServiceName($packageAppointmentId, $serviceName) {
        $query = "SELECT aps.*
                  FROM appointment_package_services aps
                  JOIN appointments a ON a.id = aps.appointment_id
                  JOIN package_services ps ON ps.id = aps.service_id
                  WHERE a.package_appointment_id = :pkg_id
                    AND TRIM(LOWER(ps.service_name)) = TRIM(LOWER(:svc))
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pkg_id', $packageAppointmentId);
        $stmt->bindParam(':svc', $serviceName);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateResultStateById($apsId, $state) {
        $query = "UPDATE " . $this->table . " SET result_state = :st, tested_at = IF(:st='submitted', NOW(), tested_at) WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':st', $state);
        $stmt->bindParam(':id', $apsId);
        return $stmt->execute();
    }
}
