<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../../config/database.php';

class DiagnosisController {
    private function findLatestByAppointment($conn, $appointmentId) {
        $stm = $conn->prepare('SELECT * FROM diagnoses WHERE appointment_id = ? ORDER BY id DESC LIMIT 1');
        $stm->execute([(int)$appointmentId]);
        return $stm->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function ensureHeader($conn, $appointmentId) {
        $dx = $this->findLatestByAppointment($conn, $appointmentId);
        if ($dx) return $dx;
        $ins = $conn->prepare('INSERT INTO diagnoses (appointment_id, package_appointment_id, doctor_id, poster_id, primary_section, clinical_findings, official_findings, status, signed_at, created_at, updated_at) VALUES (?,?,?,?,?,?,?,"draft",NULL,NOW(),NOW())');
        $doctorId = null;
        try {
            require_once APP_PATH . '/Models/Appointment.php';
            require_once APP_PATH . '/Models/Doctor.php';
            $aptModel = new Appointment();
            $apt = $aptModel->findById($appointmentId);
            if ($apt && !empty($apt['doctor_id'])) { $doctorId = (int)$apt['doctor_id']; }
        } catch (\Throwable $e) { $doctorId = null; }
        $posterId = Auth::id();
        $ins->execute([(int)$appointmentId, null, $doctorId, $posterId, null, null, null]);
        $id = (int)$conn->lastInsertId();
        $get = $conn->prepare('SELECT * FROM diagnoses WHERE id = ?');
        $get->execute([$id]);
        return $get->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // POST: save draft for appointment
    public function saveForAppointment($appointmentId) {
        Auth::requireLogin();
        if (!Auth::isDoctor() && !Auth::isAdmin()) { $_SESSION['error'] = 'Không có quyền lưu chẩn đoán'; header('Location: ' . APP_URL . '/appointments/' . $appointmentId); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ' . APP_URL . '/appointments/' . $appointmentId); return; }

        $db = new Database(); $conn = $db->getConnection();
        $dx = $this->ensureHeader($conn, $appointmentId);
        if (!$dx) { $_SESSION['error'] = 'Không tạo được bản ghi chẩn đoán.'; header('Location: ' . APP_URL . '/appointments/' . $appointmentId); return; }

        $primary = trim($_POST['primary_section'] ?? '');
        $clinical = trim($_POST['clinical_findings'] ?? '');
        $official = trim($_POST['official_findings'] ?? '');

        $up = $conn->prepare('UPDATE diagnoses SET primary_section = ?, clinical_findings = ?, official_findings = ?, updated_at = NOW() WHERE id = ?');
        $up->execute([$primary ?: null, $clinical ?: null, $official ?: null, (int)$dx['id']]);

        if (!empty($_POST['submit_after'])) {
            $conn->prepare('UPDATE diagnoses SET status = "submitted", updated_at = NOW() WHERE id = ?')->execute([(int)$dx['id']]);
            $_SESSION['success'] = 'Đã lưu và nộp chẩn đoán.';
        } else {
            $_SESSION['success'] = 'Đã lưu chẩn đoán (nháp).';
        }
        header('Location: ' . APP_URL . '/appointments/' . $appointmentId);
    }

    // POST: submit diagnosis by id
    public function submit($diagnosisId) {
        Auth::requireLogin();
        if (!Auth::isDoctor() && !Auth::isAdmin()) { $_SESSION['error'] = 'Không có quyền nộp chẩn đoán'; header('Location: ' . APP_URL . '/appointments'); return; }
        $db = new Database(); $conn = $db->getConnection();
        $conn->prepare('UPDATE diagnoses SET status = "submitted", updated_at = NOW() WHERE id = ?')->execute([(int)$diagnosisId]);
        $_SESSION['success'] = 'Đã nộp chẩn đoán.';
        header('Location: ' . APP_URL . '/appointments');
    }

    // POST: approve diagnosis by id (admin/doctor)
    public function approve($diagnosisId) {
        Auth::requireLogin();
        if (!Auth::isAdmin() && !Auth::isDoctor()) { $_SESSION['error'] = 'Không có quyền duyệt chẩn đoán'; header('Location: ' . APP_URL . '/appointments'); return; }
        $db = new Database(); $conn = $db->getConnection();
        $conn->prepare('UPDATE diagnoses SET status = "approved", signed_at = NOW(), updated_at = NOW() WHERE id = ?')->execute([(int)$diagnosisId]);
        $_SESSION['success'] = 'Đã duyệt chẩn đoán.';
        header('Location: ' . APP_URL . '/appointments');
    }

    // GET: latest diagnosis json for an appointment (optional helper)
    public function latestJson($appointmentId) {
        Auth::requireLogin(); header('Content-Type: application/json; charset=utf-8');
        $db = new Database(); $conn = $db->getConnection();
        $dx = $this->findLatestByAppointment($conn, $appointmentId);
        echo json_encode(['diagnosis'=>$dx], JSON_UNESCAPED_UNICODE);
    }
}
