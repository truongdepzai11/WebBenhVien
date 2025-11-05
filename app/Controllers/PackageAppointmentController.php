<?php

require_once __DIR__ . '/../Models/PackageAppointment.php';
require_once __DIR__ . '/../Models/Patient.php';
require_once __DIR__ . '/../Models/HealthPackage.php';
require_once __DIR__ . '/../Models/Appointment.php';
require_once __DIR__ . '/../Models/Doctor.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class PackageAppointmentController {
    private $packageAppointmentModel;
    private $patientModel;
    private $packageModel;
    private $appointmentModel;
    private $doctorModel;

    public function __construct() {
        $this->packageAppointmentModel = new PackageAppointment();
        $this->patientModel = new Patient();
        $this->packageModel = new HealthPackage();
        $this->appointmentModel = new Appointment();
        $this->doctorModel = new Doctor();
    }

    // Danh sách đăng ký gói khám
    public function index() {
        Auth::requireLogin();

        $packageAppointments = [];

        if (Auth::isPatient()) {
            // Bệnh nhân chỉ xem của mình
            $patient = $this->patientModel->findByUserId(Auth::id());
            $packageAppointments = $this->packageAppointmentModel->getByPatientId($patient['id']);
        } else {
            // Admin/Receptionist xem tất cả
            $packageAppointments = $this->packageAppointmentModel->getAll();
        }

        require_once APP_PATH . '/Views/package_appointments/index.php';
    }

    // Chi tiết đăng ký gói khám
    public function show($id) {
        Auth::requireLogin();

        $packageAppointment = $this->packageAppointmentModel->findById($id);

        if (!$packageAppointment) {
            $_SESSION['error'] = 'Không tìm thấy đăng ký gói khám';
            header('Location: ' . APP_URL . '/package-appointments');
            exit;
        }

        // Kiểm tra quyền: Bệnh nhân chỉ xem của mình
        if (Auth::isPatient()) {
            $patient = $this->patientModel->findByUserId(Auth::id());
            if ($packageAppointment['patient_id'] != $patient['id']) {
                $_SESSION['error'] = 'Bạn không có quyền xem đăng ký này';
                header('Location: ' . APP_URL . '/package-appointments');
                exit;
            }
        }

        // Lấy danh sách dịch vụ trong gói
        $packageServices = $this->packageModel->getPackageServices($packageAppointment['package_id']);

        // Lấy tất cả appointments thuộc gói
        $appointments = $this->appointmentModel->getByPackageAppointmentId($id);
        // Chỉ tính các lịch có bác sĩ (bất kể appointment_type) → tránh bỏ sót dữ liệu cũ
        $serviceAppointments = array_values(array_filter($appointments, function($a){
            return !empty($a['doctor_id']);
        }));
        // Đếm theo dịch vụ duy nhất dựa trên reason (tên dịch vụ)
        $uniqueReasons = [];
        foreach ($serviceAppointments as $apt) {
            $key = strtolower(trim($apt['reason'] ?? ''));
            if ($key !== '' && !in_array($key, $uniqueReasons, true)) {
                $uniqueReasons[] = $key;
            }
        }
        $assignedCount = count($uniqueReasons);

        // Lấy danh sách bác sĩ (cho phân công)
        $doctors = $this->doctorModel->getAll();

        require_once APP_PATH . '/Views/package_appointments/show.php';
    }

    // Phân công bác sĩ tự động
    public function autoAssignDoctors($packageAppointmentId) {
        Auth::requireLogin();

        // Chỉ Admin/Receptionist mới được phân công
        if (!Auth::isAdmin() && !Auth::isReceptionist()) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện chức năng này';
            header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId);
            exit;
        }

        $packageAppointment = $this->packageAppointmentModel->findById($packageAppointmentId);
        // Lấy giá dịch vụ từ package_services theo service_id
        $servicePrice = null;
        $services = $this->packageModel->getPackageServices($packageAppointment['package_id']);
        foreach ($services as $svc) {
            if ((string)$svc['id'] === (string)$serviceId) { $servicePrice = $svc['service_price']; break; }
        }

        if (!$packageAppointment) {
            $_SESSION['error'] = 'Không tìm thấy đăng ký gói khám';
            header('Location: ' . APP_URL . '/package-appointments');
            exit;
        }

        // Lấy danh sách dịch vụ trong gói
        $packageServices = $this->packageModel->getPackageServices($packageAppointment['package_id']);

        if (empty($packageServices)) {
            $_SESSION['error'] = 'Gói khám không có dịch vụ nào';
            header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId);
            exit;
        }

        // Ngày bắt đầu khám
        $startDate = new DateTime($packageAppointment['appointment_date']);
        $currentTime = new DateTime('08:00:00'); // Bắt đầu từ 8h sáng
        $appointmentsCreated = 0;
        $failedServices = [];

        foreach ($packageServices as $service) {
            // Tìm bác sĩ phù hợp với dịch vụ
            $doctor = $this->findSuitableDoctor($service, $startDate, $currentTime);

            if (!$doctor) {
                // Nếu không tìm được bác sĩ, thử ngày hôm sau
                $startDate->modify('+1 day');
                $currentTime = new DateTime('08:00:00');
                $doctor = $this->findSuitableDoctor($service, $startDate, $currentTime);
            }

            if ($doctor) {
                // Tạo appointment
                $this->appointmentModel->appointment_code = 'APT' . date('YmdHis') . rand(100, 999);
                $this->appointmentModel->patient_id = $packageAppointment['patient_id'];
                $this->appointmentModel->doctor_id = $doctor['id'];
                $this->appointmentModel->package_id = $packageAppointment['package_id'];
                $this->appointmentModel->package_appointment_id = $packageAppointmentId;
                $this->appointmentModel->appointment_date = $startDate->format('Y-m-d');
                $this->appointmentModel->appointment_time = $currentTime->format('H:i:s');
                $this->appointmentModel->appointment_type = 'package_detail';
                $this->appointmentModel->reason = $service['service_name'];
                $this->appointmentModel->total_price = $service['service_price'] ?? null;
                $this->appointmentModel->status = 'pending';
                $this->appointmentModel->notes = 'Tự động phân công - Gói khám: ' . $packageAppointment['package_name'];

                if ($this->appointmentModel->create()) {
                    $appointmentsCreated++;
                } else {
                    $failedServices[] = $service['service_name'];
                }

                // Tăng thời gian lên 30 phút cho dịch vụ tiếp theo
                $currentTime->modify('+30 minutes');

                // Nếu quá 17h, chuyển sang ngày hôm sau
                if ($currentTime->format('H') >= 17) {
                    $startDate->modify('+1 day');
                    $currentTime = new DateTime('08:00:00');
                }
            } else {
                // Không tìm được bác sĩ
                $failedServices[] = $service['service_name'];
            }
        }

        // Thông báo kết quả
        $totalServices = count($packageServices);
        
        if ($appointmentsCreated > 0) {
            if ($appointmentsCreated == $totalServices) {
                $_SESSION['success'] = "✅ Đã phân công thành công {$appointmentsCreated}/{$totalServices} lịch khám";
            } else {
                $_SESSION['warning'] = "⚠️ Đã phân công {$appointmentsCreated}/{$totalServices} lịch khám. " . 
                                       count($failedServices) . " dịch vụ chưa phân công được bác sĩ.";
            }
        } else {
            $_SESSION['error'] = '❌ Không thể phân công bác sĩ. Vui lòng kiểm tra lại lịch làm việc của bác sĩ.';
        }

        header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId);
        exit;
    }

    // Mapping dịch vụ → chuyên khoa
    private function getServiceSpecializationMap() {
        return [
            // Khám lâm sàng
            'Khám nội tổng quát' => 'Nội khoa',
            'Khám tim mạch' => 'Tim mạch',
            'Khám hô hấp' => 'Hô hấp',
            'Khám tiêu hóa' => 'Tiêu hóa',
            'Khám thần kinh' => 'Thần kinh',
            'Khám mắt' => 'Mắt',
            'Khám tai mũi họng' => 'Tai Mũi Họng',
            'Khám răng hàm mặt' => 'Răng Hàm Mặt',
            'Khám da liễu' => 'Da liễu',
            'Khám cơ xương khớp' => 'Cơ Xương Khớp',
            'Khám sản phụ khoa' => 'Sản Phụ khoa',
            'Khám tiết niệu' => 'Tiết niệu',
            
            // Xét nghiệm & Chẩn đoán hình ảnh
            'Xét nghiệm' => 'Xét nghiệm',
            'Siêu âm' => 'Chẩn đoán hình ảnh',
            'X-quang' => 'Chẩn đoán hình ảnh',
            'Điện tim' => 'Tim mạch',
            'Nội soi' => 'Tiêu hóa',
        ];
    }

    // Tìm chuyên khoa phù hợp cho dịch vụ
    private function findSpecializationForService($serviceName) {
        $map = $this->getServiceSpecializationMap();
        
        // Tìm khớp chính xác
        if (isset($map[$serviceName])) {
            return $map[$serviceName];
        }
        
        // Tìm khớp một phần (chứa từ khóa)
        foreach ($map as $keyword => $specialization) {
            if (stripos($serviceName, $keyword) !== false) {
                return $specialization;
            }
        }
        
        // Mặc định: Nội khoa (bác sĩ đa khoa)
        return 'Nội khoa';
    }

    // Tìm bác sĩ phù hợp cho dịch vụ (theo chuyên môn)
    private function findSuitableDoctor($service, $date, $time) {
        $serviceName = $service['service_name'];
        $requiredSpecialization = $this->findSpecializationForService($serviceName);
        
        // Query tìm bác sĩ theo chuyên khoa
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT d.*, u.full_name, s.name as specialization_name
                  FROM doctors d
                  LEFT JOIN users u ON d.user_id = u.id
                  LEFT JOIN specializations s ON d.specialization_id = s.id
                  WHERE s.name = :specialization
                  AND d.is_available = 1
                  ORDER BY d.total_patients ASC, d.rating DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':specialization', $requiredSpecialization);
        $stmt->execute();
        
        $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Kiểm tra từng bác sĩ xem có rảnh không
        foreach ($doctors as $doctor) {
            $isAvailable = $this->appointmentModel->isDoctorAvailable(
                $doctor['id'],
                $date->format('Y-m-d'),
                $time->format('H:i:s')
            );

            if ($isAvailable) {
                return $doctor;
            }
        }
        
        // Nếu không tìm được bác sĩ chuyên khoa, tìm bác sĩ Nội khoa
        if ($requiredSpecialization !== 'Nội khoa') {
            $query = "SELECT d.*, u.full_name, s.name as specialization_name
                      FROM doctors d
                      LEFT JOIN users u ON d.user_id = u.id
                      LEFT JOIN specializations s ON d.specialization_id = s.id
                      WHERE s.name = 'Nội khoa'
                      AND d.is_available = 1
                      ORDER BY d.total_patients ASC";
            
            $stmt = $conn->prepare($query);
            $stmt->execute();
            
            $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($doctors as $doctor) {
                $isAvailable = $this->appointmentModel->isDoctorAvailable(
                    $doctor['id'],
                    $date->format('Y-m-d'),
                    $time->format('H:i:s')
                );

                if ($isAvailable) {
                    return $doctor;
                }
            }
        }

        return null;
    }
    
    // Phân loại dịch vụ theo nhóm
    private function categorizeServices($services) {
        $clinical = [];      // Khám lâm sàng
        $laboratory = [];    // Xét nghiệm
        $imaging = [];       // Chẩn đoán hình ảnh
        
        foreach ($services as $service) {
            $name = $service['service_name'];
            
            if (stripos($name, 'Xét nghiệm') !== false) {
                $laboratory[] = $service;
            } elseif (stripos($name, 'Siêu âm') !== false || 
                      stripos($name, 'X-quang') !== false ||
                      stripos($name, 'CT') !== false ||
                      stripos($name, 'MRI') !== false) {
                $imaging[] = $service;
            } else {
                $clinical[] = $service;
            }
        }
        
        return [$clinical, $laboratory, $imaging];
    }

    // Phân công bác sĩ thủ công
    public function assignDoctor() {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/package-appointments');
            exit;
        }

        $packageAppointmentId = $_POST['package_appointment_id'];
        $serviceId = $_POST['service_id'];
        $doctorId = $_POST['doctor_id'];
        $appointmentDate = $_POST['appointment_date'];
        $appointmentTime = $_POST['appointment_time'];

        $packageAppointment = $this->packageAppointmentModel->findById($packageAppointmentId);

        // Tạo appointment CHI TIẾT cho dịch vụ (để có thể có nhiều bác sĩ cho 1 gói)
        $this->appointmentModel->appointment_code = 'APT' . date('YmdHis') . rand(100, 999);
        $this->appointmentModel->patient_id = $packageAppointment['patient_id'];
        $this->appointmentModel->doctor_id = $doctorId;
        $this->appointmentModel->package_id = $packageAppointment['package_id'];
        $this->appointmentModel->package_appointment_id = $packageAppointmentId;
        $this->appointmentModel->appointment_date = $appointmentDate;
        $this->appointmentModel->appointment_time = $appointmentTime;
        $this->appointmentModel->appointment_type = 'package_detail';
        $this->appointmentModel->reason = $_POST['service_name'];
        $this->appointmentModel->status = 'pending';
        $this->appointmentModel->notes = 'Phân công thủ công - Gói khám: ' . $packageAppointment['package_name'];

        if ($this->appointmentModel->create()) {
            $_SESSION['success'] = 'Đã phân công bác sĩ cho dịch vụ trong gói.';
        } else {
            $_SESSION['error'] = 'Không thể tạo lịch hẹn cho dịch vụ.';
        }

        header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId);
        exit;
    }

    // Hủy đăng ký gói khám
    public function cancel($id) {
        Auth::requireLogin();

        $packageAppointment = $this->packageAppointmentModel->findById($id);

        if (!$packageAppointment) {
            $_SESSION['error'] = 'Không tìm thấy đăng ký gói khám';
            header('Location: ' . APP_URL . '/package-appointments');
            exit;
        }

        // Cập nhật trạng thái
        if ($this->packageAppointmentModel->updateStatus($id, 'cancelled')) {
            $_SESSION['success'] = 'Đã hủy đăng ký gói khám';
        } else {
            $_SESSION['error'] = 'Hủy đăng ký thất bại';
        }

        header('Location: ' . APP_URL . '/package-appointments');
        exit;
    }
}
