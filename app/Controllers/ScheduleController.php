<?php

require_once __DIR__ . '/../Models/Appointment.php';
require_once __DIR__ . '/../Models/Doctor.php';
require_once __DIR__ . '/../Models/Patient.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class ScheduleController {
    private $appointmentModel;
    private $doctorModel;
    private $patientModel;

    public function __construct() {
        $this->appointmentModel = new Appointment();
        $this->doctorModel = new Doctor();
        $this->patientModel = new Patient();
    }

    // Xem lịch làm việc bác sĩ
    public function index() {
        Auth::requireLogin();

        // Nếu là Doctor → Chỉ xem lịch của mình
        if (Auth::isDoctor()) {
            $currentDoctor = $this->doctorModel->findByUserId(Auth::id());
            $doctors = [$currentDoctor];
            $selectedDoctorId = $currentDoctor['id'];
        } else {
            // Admin → Xem tất cả bác sĩ
            $doctors = $this->doctorModel->getAll();
            $selectedDoctorId = $_GET['doctor_id'] ?? ($doctors[0]['id'] ?? null);
        }

        // Lấy tham số từ URL
        $selectedDate = $_GET['date'] ?? date('Y-m-d');

        // Lấy lịch hẹn của bác sĩ trong ngày
        $appointments = [];
        if ($selectedDoctorId) {
            $allAppointments = $this->appointmentModel->getByDoctorId($selectedDoctorId);
            foreach ($allAppointments as $apt) {
                if ($apt['appointment_date'] == $selectedDate && 
                    !in_array($apt['status'], ['cancelled', 'late_cancelled'])) {
                    $appointments[] = $apt;
                }
            }
        }

        // Tạo lịch theo giờ (8h - 17h)
        $timeSlots = [];
        for ($hour = 8; $hour <= 17; $hour++) {
            $time = sprintf('%02d:00:00', $hour);
            $timeSlots[$time] = null; // Mặc định trống
        }

        // Điền lịch hẹn vào slots
        foreach ($appointments as $apt) {
            $time = substr($apt['appointment_time'], 0, 8); // HH:MM:SS
            $timeSlots[$time] = $apt;
        }

        // Lấy thông tin bác sĩ được chọn
        $selectedDoctor = null;
        if ($selectedDoctorId) {
            $selectedDoctor = $this->doctorModel->findById($selectedDoctorId);
        }

        require_once APP_PATH . '/Views/schedule/index.php';
    }

    // Form thêm bệnh nhân vào slot
    public function addPatient() {
        Auth::requireLogin();

        if (!Auth::isAdmin() && !Auth::isDoctor()) {
            $_SESSION['error'] = 'Bạn không có quyền thêm lịch hẹn';
            header('Location: ' . APP_URL . '/schedule');
            exit;
        }

        $doctorId = $_GET['doctor_id'] ?? null;
        $date = $_GET['date'] ?? date('Y-m-d');
        $time = $_GET['time'] ?? null;

        if (!$doctorId || !$time) {
            $_SESSION['error'] = 'Thiếu thông tin';
            header('Location: ' . APP_URL . '/schedule');
            exit;
        }

        // Kiểm tra slot còn trống không
        if ($this->appointmentModel->checkConflict($doctorId, $date, $time)) {
            $_SESSION['error'] = 'Slot này đã có người đặt';
            header('Location: ' . APP_URL . '/schedule?doctor_id=' . $doctorId . '&date=' . $date);
            exit;
        }

        $doctor = $this->doctorModel->findById($doctorId);
        $patients = $this->patientModel->getAll();

        require_once APP_PATH . '/Views/schedule/add_patient.php';
    }

    // Lưu lịch hẹn Walk-in
    public function storeWalkIn() {
        Auth::requireLogin();

        if (!Auth::isAdmin() && !Auth::isDoctor()) {
            $_SESSION['error'] = 'Bạn không có quyền thêm lịch hẹn';
            header('Location: ' . APP_URL . '/schedule');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/schedule');
            exit;
        }

        $doctorId = $_POST['doctor_id'];
        $date = $_POST['appointment_date'];
        $time = $_POST['appointment_time'];
        $patientType = $_POST['patient_type'];

        // Xử lý bệnh nhân
        if ($patientType === 'new') {
            // Tạo bệnh nhân mới
            require_once __DIR__ . '/../Models/User.php';
            $userModel = new User();
            
            // Tạo user (không cần đăng nhập)
            $timestamp = time();
            $userModel->username = 'patient_' . $timestamp; // Username tự động
            $userModel->full_name = $_POST['new_patient_name'];
            // Nếu có email thật → dùng, không thì tạo email tạm
            $userModel->email = !empty($_POST['new_patient_email']) 
                ? $_POST['new_patient_email'] 
                : 'patient_' . $timestamp . '@walkin.local';
            $userModel->password = password_hash('walkin123', PASSWORD_DEFAULT); // Password mặc định
            $userModel->phone = $_POST['new_patient_phone'];
            $userModel->date_of_birth = $_POST['new_patient_dob'];
            $userModel->gender = $_POST['new_patient_gender'];
            $userModel->address = $_POST['new_patient_address'];
            $userModel->role = 'patient';
            
            if (!$userModel->create()) {
                $_SESSION['error'] = 'Tạo tài khoản bệnh nhân thất bại';
                header('Location: ' . APP_URL . '/schedule');
                exit;
            }
            
            // Tạo hồ sơ bệnh nhân
            $this->patientModel->user_id = $userModel->id;
            $this->patientModel->date_of_birth = $_POST['new_patient_dob'];
            $this->patientModel->gender = $_POST['new_patient_gender'];
            $this->patientModel->address = $_POST['new_patient_address'];
            $this->patientModel->emergency_contact = $_POST['new_patient_phone'];
            
            if (!$this->patientModel->create()) {
                $_SESSION['error'] = 'Tạo hồ sơ bệnh nhân thất bại';
                header('Location: ' . APP_URL . '/schedule');
                exit;
            }
            
            $patientId = $this->patientModel->id;
        } else {
            // Bệnh nhân cũ
            $patientId = $_POST['patient_id'];
        }

        // Kiểm tra trùng lịch
        if ($this->appointmentModel->checkConflict($doctorId, $date, $time)) {
            $_SESSION['error'] = 'Lịch hẹn bị trùng. Vui lòng chọn giờ khác.';
            header('Location: ' . APP_URL . '/schedule/add-patient?doctor_id=' . $doctorId . '&date=' . $date . '&time=' . $time);
            exit;
        }

        // Tạo lịch hẹn
        $this->appointmentModel->doctor_id = $doctorId;
        $this->appointmentModel->patient_id = $patientId;
        $this->appointmentModel->appointment_date = $date;
        $this->appointmentModel->appointment_time = $time;
        $this->appointmentModel->reason = $_POST['reason'] ?? 'Khám trực tiếp (Walk-in)';
        $this->appointmentModel->symptoms = $_POST['symptoms'] ?? '';
        $this->appointmentModel->status = 'confirmed'; // Walk-in tự động xác nhận
        $this->appointmentModel->notes = 'Walk-in - Đặt bởi ' . Auth::user()['full_name'];

        if ($this->appointmentModel->create()) {
            $_SESSION['success'] = 'Thêm lịch hẹn Walk-in thành công';
            header('Location: ' . APP_URL . '/schedule?doctor_id=' . $doctorId . '&date=' . $date);
            exit;
        } else {
            $_SESSION['error'] = 'Thêm lịch hẹn thất bại';
            header('Location: ' . APP_URL . '/schedule');
            exit;
        }
    }
}
