<?php

require_once __DIR__ . '/../Models/Patient.php';
require_once __DIR__ . '/../Models/Doctor.php';
require_once __DIR__ . '/../Models/Appointment.php';
require_once __DIR__ . '/../Models/MedicalRecord.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class DashboardController {
    private $patientModel;
    private $doctorModel;
    private $appointmentModel;
    private $medicalRecordModel;

    public function __construct() {
        $this->patientModel = new Patient();
        $this->doctorModel = new Doctor();
        $this->appointmentModel = new Appointment();
        $this->medicalRecordModel = new MedicalRecord();
    }

    public function index() {
        Auth::requireLogin();

        $user = Auth::user();
        $role = Auth::role();

        // Thống kê theo vai trò
        if ($role === 'admin' || $role === 'receptionist') {
            $allAppointments = $this->appointmentModel->getAll();
            // Chỉ giữ lịch khám thường hoặc lịch tổng hợp gói (reason bắt đầu bằng 'Khám theo gói')
            $displayAppointments = array_values(array_filter($allAppointments, function($apt){
                if (empty($apt['package_appointment_id'])) return true;
                $reason = $apt['reason'] ?? '';
                return stripos($reason, 'Khám theo gói') === 0;
            }));
            $stats = [
                'total_patients' => count($this->patientModel->getAll()),
                'total_doctors' => count($this->doctorModel->getAll()),
                'total_appointments' => count($displayAppointments),
                'pending_appointments' => count(array_filter($displayAppointments, fn($a) => $a['status'] === 'pending')),
                'completed_appointments' => count(array_filter($displayAppointments, fn($a) => $a['status'] === 'completed')),
                'total_records' => count($this->medicalRecordModel->getAll())
            ];
            $recent_appointments = array_slice($displayAppointments, 0, 5);
        } elseif ($role === 'doctor') {
            $doctor = $this->doctorModel->findByUserId(Auth::id());
            $appointments = $this->appointmentModel->getByDoctorId($doctor['id']);
            $displayAppointments = array_values(array_filter($appointments, function($apt){
                if (empty($apt['package_appointment_id'])) return true;
                $reason = $apt['reason'] ?? '';
                return stripos($reason, 'Khám theo gói') === 0;
            }));
            $stats = [
                'total_appointments' => count($displayAppointments),
                'pending_appointments' => count(array_filter($displayAppointments, fn($a) => $a['status'] === 'pending')),
                'completed_appointments' => count(array_filter($displayAppointments, fn($a) => $a['status'] === 'completed')),
                'total_patients' => count($this->medicalRecordModel->getByDoctorId($doctor['id']))
            ];
            $recent_appointments = array_slice($displayAppointments, 0, 5);
        } else { // patient
            $patient = $this->patientModel->findByUserId(Auth::id());
            $appointments = $this->appointmentModel->getByPatientId($patient['id']);
            $displayAppointments = array_values(array_filter($appointments, function($apt){
                if (empty($apt['package_appointment_id'])) return true;
                $reason = $apt['reason'] ?? '';
                return stripos($reason, 'Khám theo gói') === 0;
            }));
            $stats = [
                'total_appointments' => count($displayAppointments),
                'pending_appointments' => count(array_filter($displayAppointments, fn($a) => $a['status'] === 'pending')),
                'completed_appointments' => count(array_filter($displayAppointments, fn($a) => $a['status'] === 'completed')),
                'total_records' => count($this->medicalRecordModel->getByPatientId($patient['id']))
            ];
            $recent_appointments = array_slice($displayAppointments, 0, 5);
        }

        require_once APP_PATH . '/Views/dashboard/index.php';
    }
}
