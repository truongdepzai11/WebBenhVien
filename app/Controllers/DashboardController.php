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
    private $packageModel; // avoid dynamic property creation

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
            // Chỉ giữ lịch thường hoặc lịch tổng hợp gói (nhận diện có dấu ":")
            $displayAppointments = array_values(array_filter($allAppointments, function($apt){
                if (empty($apt['package_appointment_id'])) return true;
                $reason = $apt['reason'] ?? '';
                return strpos($reason, ':') !== false;
            }));
            // Bổ sung tiến độ phân công và danh sách ngày cho lịch tổng hợp gói
            $displayAppointments = array_map(function($apt){
                if (!empty($apt['package_appointment_id'])) {
                    $apt['assigned_count'] = $this->appointmentModel->countAssignedByPackageAppointmentId($apt['package_appointment_id']);
                    if (!isset($this->packageModel)) {
                        require_once __DIR__ . '/../Models/HealthPackage.php';
                        $this->packageModel = new HealthPackage();
                    }
                    // Lấy tổng số dịch vụ đã chọn trong gói
                    $summaryAppointment = $this->appointmentModel->getSummaryByPackageAppointmentId($apt['package_appointment_id']);
                    if ($summaryAppointment) {
                        $services = $this->packageModel->getSelectedServicesByAppointmentId($summaryAppointment['id']);
                    } else {
                        $services = [];
                    }
                    
                    // Fallback: nếu không có dịch vụ đã chọn, lấy tất cả dịch vụ
                    if (empty($services)) {
                        $services = $this->packageModel->getPackageServices($apt['package_id']);
                    }
                    
                    $apt['total_services'] = is_array($services) ? count($services) : 0;
                    if (method_exists($this->appointmentModel, 'getAppointmentDatesByPackageAppointmentId')) {
                        $apt['appointment_dates'] = $this->appointmentModel->getAppointmentDatesByPackageAppointmentId($apt['package_appointment_id']);
                    } else {
                        $apt['appointment_dates'] = [];
                    }
                }
                return $apt;
            }, $displayAppointments);
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
            // Lấy toàn bộ lịch mà bác sĩ được phân công (bao gồm cả lịch dịch vụ thuộc gói)
            $displayAppointments = $this->appointmentModel->getByDoctorId($doctor['id']);
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
                return strpos($reason, ':') !== false;
            }));
            $displayAppointments = array_map(function($apt){
                if (!empty($apt['package_appointment_id'])) {
                    $apt['assigned_count'] = $this->appointmentModel->countAssignedByPackageAppointmentId($apt['package_appointment_id']);
                    if (!isset($this->packageModel)) {
                        require_once __DIR__ . '/../Models/HealthPackage.php';
                        $this->packageModel = new HealthPackage();
                    }
                    // Lấy tổng số dịch vụ đã chọn trong gói
                    $summaryAppointment = $this->appointmentModel->getSummaryByPackageAppointmentId($apt['package_appointment_id']);
                    if ($summaryAppointment) {
                        $services = $this->packageModel->getSelectedServicesByAppointmentId($summaryAppointment['id']);
                    } else {
                        $services = [];
                    }
                    
                    // Fallback: nếu không có dịch vụ đã chọn, lấy tất cả dịch vụ
                    if (empty($services)) {
                        $services = $this->packageModel->getPackageServices($apt['package_id']);
                    }
                    
                    $apt['total_services'] = is_array($services) ? count($services) : 0;
                    $apt['appointment_dates'] = $this->appointmentModel->getAppointmentDatesByPackageAppointmentId($apt['package_appointment_id']);
                }
                return $apt;
            }, $displayAppointments);
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
