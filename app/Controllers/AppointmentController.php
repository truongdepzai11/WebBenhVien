<?php

require_once __DIR__ . '/../Models/Appointment.php';
require_once __DIR__ . '/../Models/Patient.php';
require_once __DIR__ . '/../Models/Doctor.php';
require_once __DIR__ . '/../Models/Specialization.php';
require_once __DIR__ . '/../Models/Invoice.php';
require_once __DIR__ . '/../Models/HealthPackage.php';
require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/Validator.php';
require_once __DIR__ . '/../../config/database.php';

class AppointmentController {
    private $appointmentModel;
    private $patientModel;
    private $doctorModel;
    private $specializationModel;
    private $invoiceModel;
    private $packageModel;

    public function __construct() {
        $this->appointmentModel = new Appointment();
        $this->patientModel = new Patient();
        $this->doctorModel = new Doctor();
        $this->specializationModel = new Specialization();
        $this->invoiceModel = new Invoice();
        $this->packageModel = new HealthPackage();
    }

    // Danh sách lịch hẹn
    public function index() {
        Auth::requireLogin();

        $role = Auth::role();
        
        if ($role === 'doctor') {
            $doctor = $this->doctorModel->findByUserId(Auth::id());
            $appointments = $this->appointmentModel->getByDoctorId($doctor['id']);
        } elseif ($role === 'patient') {
            $patient = $this->patientModel->findByUserId(Auth::id());
            $appointments = $this->appointmentModel->getByPatientId($patient['id']);
        } else {
            $appointments = $this->appointmentModel->getAll();
        }
        
        // Lọc appointments để chỉ hiển thị 1 dòng cho gói khám (summary)
        // - Giữ: Khám thường (package_appointment_id = NULL)
        // - Giữ: Appointment tổng hợp gói khám (reason bắt đầu bằng 'Khám theo gói')
        // - Bỏ: Các appointment dịch vụ trong gói (reason không phải 'Khám theo gói')
        $regularAppointments = array_filter($appointments, function($apt) {
            // Khám thường
            if (empty($apt['package_appointment_id'])) {
                return true;
            }
            // Appointment tổng hợp gói khám
            $reason = $apt['reason'] ?? '';
            if (stripos($reason, 'Khám theo gói') === 0) {
                return true;
            }
            return false;
        });
        
        // Không cần lấy packageAppointments nữa vì đã có appointment tổng hợp
        $packageAppointments = [];

        require_once APP_PATH . '/Views/appointments/index.php';
    }

    // Danh sách lịch hẹn theo gói khám
    public function indexByPackage($packageAppointmentId) {
        Auth::requireLogin();

        // Lấy thông tin gói khám
        require_once APP_PATH . '/Models/PackageAppointment.php';
        $packageAppointmentModel = new PackageAppointment();
        $packageAppointment = $packageAppointmentModel->findById($packageAppointmentId);
        
        if (!$packageAppointment) {
            $_SESSION['error'] = 'Không tìm thấy gói khám';
            header('Location: ' . APP_URL . '/appointments');
            exit;
        }

        // Lấy tất cả appointments của gói này
        $appointments = $this->appointmentModel->getByPackageAppointmentId($packageAppointmentId);
        
        // Không có regularAppointments và packageAppointments
        $regularAppointments = [];
        $packageAppointments = [];
        
        // Đặt title
        $pageTitle = 'Lịch hẹn - ' . $packageAppointment['package_name'];

        require_once APP_PATH . '/Views/appointments/index.php';
    }

    // Hiển thị form tạo lịch hẹn
    public function create() {
        Auth::requireLogin();

        $doctors = $this->doctorModel->getAll();
        $specializations = $this->specializationModel->getAll();
        
        // Kiểm tra nếu đặt theo gói khám
        $package_id = $_GET['package_id'] ?? null;
        $selected_package = null;
        
        if ($package_id) {
            $selected_package = $this->packageModel->findById($package_id);
        }
        
        // Nếu là bệnh nhân, lấy thông tin của họ
        if (Auth::isPatient()) {
            $patient = $this->patientModel->findByUserId(Auth::id());
            
            // Tính tuổi
            if ($patient && $patient['date_of_birth']) {
                $dob = new DateTime($patient['date_of_birth']);
                $now = new DateTime();
                $patient_age = $now->diff($dob)->y;
                $patient_gender = $patient['gender'];
                
                // Lấy chuyên khoa phù hợp
                $eligible_specializations = $this->specializationModel->getEligibleForPatient($patient_age, $patient_gender);
                
                // Lấy gói khám phù hợp
                $eligible_packages = $this->packageModel->getPackagesForPatient($patient_gender, $patient_age);
            }
        } else {
            // Admin/Receptionist có thể xem tất cả gói
            $eligible_packages = $this->packageModel->getAllActive();
        }

        require_once APP_PATH . '/Views/appointments/create.php';
    }

    // Lưu lịch hẹn mới
    public function store() {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/appointments/create');
            exit;
        }

        // Validate
        $is_package = !empty($_POST['package_id']);
        
        $validator = new Validator($_POST);
        
        // Chỉ bắt buộc doctor_id và appointment_time khi đặt khám THƯỜNG
        if (!$is_package) {
            $validator->required('doctor_id', 'Vui lòng chọn bác sĩ')
                      ->required('appointment_time', 'Vui lòng chọn giờ khám');
        }
        
        // Các field bắt buộc cho cả 2 loại
        $validator->required('appointment_date', 'Vui lòng chọn ngày khám')
                  ->date('appointment_date', 'Ngày khám không hợp lệ')
                  ->required('reason', 'Vui lòng nhập lý do khám');

        if ($validator->fails()) {
            $_SESSION['error'] = $validator->firstError();
            $_SESSION['old'] = $_POST;
            header('Location: ' . APP_URL . '/appointments/create');
            exit;
        }

        // Lấy patient_id
        if (Auth::isPatient()) {
            $patient = $this->patientModel->findByUserId(Auth::id());
            $patient_id = $patient['id'];
            
            // Kiểm tra điều kiện chuyên khoa nếu có chọn
            if (!empty($_POST['specialization'])) {
                $doctor = $this->doctorModel->findById($_POST['doctor_id']);
                
                if ($doctor && $patient['date_of_birth'] && $patient['gender']) {
                    $dob = new DateTime($patient['date_of_birth']);
                    $now = new DateTime();
                    $patient_age = $now->diff($dob)->y;
                    
                    $eligibility = $this->specializationModel->checkPatientEligibility(
                        $doctor['specialization'],
                        $patient_age,
                        $patient['gender']
                    );
                    
                    if (!$eligibility['eligible']) {
                        $_SESSION['error'] = $eligibility['reason'];
                        $_SESSION['old'] = $_POST;
                        header('Location: ' . APP_URL . '/appointments/create');
                        exit;
                    }
                }
            }
        } else {
            $patient_id = $_POST['patient_id'] ?? null;
            if (!$patient_id) {
                $_SESSION['error'] = 'Vui lòng chọn bệnh nhân';
                header('Location: ' . APP_URL . '/appointments/create');
                exit;
            }
        }

        // Kiểm tra loại khám
        $is_package = !empty($_POST['package_id']);
        
        // Kiểm tra thời gian không được trong quá khứ (CHỈ cho khám thường)
        if (!$is_package && !empty($_POST['appointment_time'])) {
            $appointmentDateTime = strtotime($_POST['appointment_date'] . ' ' . $_POST['appointment_time']);
            $currentDateTime = time();
            
            if ($appointmentDateTime <= $currentDateTime) {
                $_SESSION['error'] = 'Không thể đặt lịch khám trong quá khứ. Vui lòng chọn thời gian sau ' . date('d/m/Y H:i', $currentDateTime);
                $_SESSION['old'] = $_POST;
                header('Location: ' . APP_URL . '/appointments/create');
                exit;
            }
        }
        
        // Kiểm tra xung đột lịch (CHỈ cho khám thường)
        if (!$is_package && $this->appointmentModel->checkConflict($_POST['doctor_id'], $_POST['appointment_date'], $_POST['appointment_time'])) {
            $_SESSION['error'] = 'Thời gian này đã có lịch hẹn khác. Vui lòng chọn thời gian khác.';
            $_SESSION['old'] = $_POST;
            header('Location: ' . APP_URL . '/appointments/create');
            exit;
        }
        
        // Nếu đặt GÓI KHÁM → Tạo package_appointment VÀ appointment
        if ($is_package) {
            // Kiểm tra package_id tồn tại
            $package = $this->packageModel->findById($_POST['package_id']);
            if (!$package) {
                $_SESSION['error'] = 'Gói khám không tồn tại';
                $_SESSION['old'] = $_POST;
                header('Location: ' . APP_URL . '/appointments/create');
                exit;
            }
            
            // 1. Tạo package_appointment
            require_once APP_PATH . '/Models/PackageAppointment.php';
            $packageAppointmentModel = new PackageAppointment();
            
            $packageAppointmentModel->patient_id = $patient_id;
            $packageAppointmentModel->package_id = $_POST['package_id'];
            $packageAppointmentModel->appointment_date = $_POST['appointment_date'];
            $packageAppointmentModel->status = 'scheduled';
            $packageAppointmentModel->notes = $_POST['notes'] ?? null;
            $packageAppointmentModel->total_price = $package['price'];
            $packageAppointmentModel->created_by = Auth::id();
            
            if (!$packageAppointmentModel->create()) {
                $_SESSION['error'] = 'Đăng ký gói khám thất bại';
                $_SESSION['old'] = $_POST;
                header('Location: ' . APP_URL . '/appointments/create');
                exit;
            }
            
            // 2. Tạo appointment "tổng hợp" để hiện trong danh sách lịch hẹn
            $this->appointmentModel->patient_id = $patient_id;
            $this->appointmentModel->doctor_id = null;
            $this->appointmentModel->package_id = $_POST['package_id'];
            $this->appointmentModel->package_appointment_id = $packageAppointmentModel->id;
            $this->appointmentModel->appointment_date = $_POST['appointment_date'];
            $this->appointmentModel->appointment_time = null;
            $this->appointmentModel->reason = 'Khám theo gói: ' . $package['name'];
            $this->appointmentModel->status = 'pending';
            $this->appointmentModel->notes = $_POST['notes'] ?? null;
            $this->appointmentModel->appointment_type = 'package';
            $this->appointmentModel->coordinator_doctor_id = null;
            $this->appointmentModel->total_price = $package['price'];
            
            if ($this->appointmentModel->create()) {
                $_SESSION['success'] = 'Đăng ký gói khám thành công! Vui lòng chờ admin phân công bác sĩ.';
                header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentModel->id);
            } else {
                $_SESSION['error'] = 'Đăng ký gói khám thất bại';
                $_SESSION['old'] = $_POST;
                header('Location: ' . APP_URL . '/appointments/create');
            }
            exit;
        }
        
        // Nếu đặt KHÁM THƯỜNG → Tạo appointment
        $this->appointmentModel->patient_id = $patient_id;
        $this->appointmentModel->doctor_id = $_POST['doctor_id'];
        $this->appointmentModel->appointment_date = $_POST['appointment_date'];
        $this->appointmentModel->appointment_time = $_POST['appointment_time'];
        $this->appointmentModel->reason = $_POST['reason'];
        $this->appointmentModel->status = 'pending';
        $this->appointmentModel->notes = $_POST['notes'] ?? null;
        $this->appointmentModel->package_id = null;
        $this->appointmentModel->package_appointment_id = null;
        $this->appointmentModel->appointment_type = 'regular';
        $this->appointmentModel->coordinator_doctor_id = null;
        $this->appointmentModel->total_price = 0;

        if ($this->appointmentModel->create()) {
            $_SESSION['success'] = 'Đặt lịch hẹn thành công!';
            header('Location: ' . APP_URL . '/appointments');
            exit;
        } else {
            $_SESSION['error'] = 'Đặt lịch hẹn thất bại. Vui lòng thử lại.';
            header('Location: ' . APP_URL . '/appointments/create');
            exit;
        }
    }
    
    // Lưu dịch vụ đã chọn cho appointment
    private function saveAppointmentServices($appointment_id, $service_ids) {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Lấy thông tin dịch vụ
        $placeholders = str_repeat('?,', count($service_ids) - 1) . '?';
        $query = "SELECT id, service_price FROM package_services WHERE id IN ($placeholders)";
        $stmt = $conn->prepare($query);
        $stmt->execute($service_ids);
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Insert vào appointment_package_services
        $insertQuery = "INSERT INTO appointment_package_services 
                        (appointment_id, service_id, service_price, status) 
                        VALUES (?, ?, ?, 'pending')";
        $insertStmt = $conn->prepare($insertQuery);
        
        foreach ($services as $service) {
            $insertStmt->execute([
                $appointment_id,
                $service['id'],
                $service['service_price']
            ]);
        }
    }

    // Cập nhật trạng thái lịch hẹn
    public function updateStatus($id) {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/appointments');
            exit;
        }

        $status = $_POST['status'] ?? '';
        
        if (!in_array($status, ['pending', 'confirmed', 'completed', 'cancelled', 'late_cancelled', 'no_show'])) {
            $_SESSION['error'] = 'Trạng thái không hợp lệ';
            header('Location: ' . APP_URL . '/appointments');
            exit;
        }

        // Nếu hủy lịch, kiểm tra thời gian để áp dụng chính sách phí
        if ($status === 'cancelled') {
            $appointment = $this->appointmentModel->findById($id);
            
            if ($appointment) {
                $appointmentDateTime = strtotime($appointment['appointment_date'] . ' ' . $appointment['appointment_time']);
                $currentDateTime = time();
                $hoursDiff = ($appointmentDateTime - $currentDateTime) / 3600;
                
                // Nếu hủy trong vòng 1 giờ trước giờ khám → Phí 30%
                if ($hoursDiff < 1 && $hoursDiff >= 0) {
                    $status = 'late_cancelled'; // Hủy muộn - tính phí 30%
                    $_SESSION['warning'] = 'Lịch hẹn đã bị hủy. Do hủy muộn (dưới 1 giờ trước giờ khám), bạn sẽ bị tính phí 30% chi phí khám.';
                } else if ($hoursDiff < 0) {
                    // Đã qua giờ khám
                    $_SESSION['error'] = 'Không thể hủy lịch hẹn đã qua giờ khám';
                    header('Location: ' . APP_URL . '/appointments/' . $id);
                    exit;
                } else {
                    // Hủy trước 1 giờ → Miễn phí
                    $_SESSION['success'] = 'Hủy lịch hẹn thành công. Không tính phí.';
                }
            }
        }

        if ($this->appointmentModel->updateStatus($id, $status)) {
            if (!isset($_SESSION['success']) && !isset($_SESSION['warning'])) {
                $_SESSION['success'] = 'Cập nhật trạng thái thành công';
            }
        } else {
            $_SESSION['error'] = 'Cập nhật trạng thái thất bại';
        }

        header('Location: ' . APP_URL . '/appointments');
        exit;
    }

    // Chi tiết lịch hẹn
    public function show($id) {
        Auth::requireLogin();

        $appointment = $this->appointmentModel->findById($id);
        
        if (!$appointment) {
            $_SESSION['error'] = 'Không tìm thấy lịch hẹn';
            header('Location: ' . APP_URL . '/appointments');
            exit;
        }

        // Kiểm tra quyền xem
        if (Auth::isPatient()) {
            $patient = $this->patientModel->findByUserId(Auth::id());
            if ($appointment['patient_id'] != $patient['id']) {
                $_SESSION['error'] = 'Bạn không có quyền xem lịch hẹn này';
                header('Location: ' . APP_URL . '/appointments');
                exit;
            }
        }

        // Kiểm tra xem đã có hóa đơn chưa
        $invoice = null;
        $allInvoices = $this->invoiceModel->getAll();
        foreach ($allInvoices as $inv) {
            if ($inv['appointment_id'] == $id) {
                $invoice = $inv;
                break;
            }
        }

        require_once APP_PATH . '/Views/appointments/show.php';
    }

    // Xác nhận lịch hẹn
    public function confirm($id) {
        Auth::requireLogin();
        
        if (!Auth::isDoctor() && !Auth::isAdmin()) {
            $_SESSION['error'] = 'Bạn không có quyền xác nhận lịch hẹn';
            header('Location: ' . APP_URL . '/appointments');
            exit;
        }

        if ($this->appointmentModel->updateStatus($id, 'confirmed')) {
            $_SESSION['success'] = 'Xác nhận lịch hẹn thành công';
        } else {
            $_SESSION['error'] = 'Xác nhận lịch hẹn thất bại';
        }

        header('Location: ' . APP_URL . '/appointments/' . $id);
        exit;
    }

    // Hoàn thành lịch hẹn
    public function complete($id) {
        Auth::requireLogin();
        
        if (!Auth::isDoctor() && !Auth::isAdmin()) {
            $_SESSION['error'] = 'Bạn không có quyền hoàn thành lịch hẹn';
            header('Location: ' . APP_URL . '/appointments');
            exit;
        }

        if ($this->appointmentModel->updateStatus($id, 'completed')) {
            $_SESSION['success'] = 'Hoàn thành lịch hẹn thành công';
        } else {
            $_SESSION['error'] = 'Hoàn thành lịch hẹn thất bại';
        }

        header('Location: ' . APP_URL . '/appointments/' . $id);
        exit;
    }

    // Đánh dấu vắng mặt (No-show)
    public function markNoShow($id) {
        Auth::requireLogin();
        
        if (!Auth::isDoctor() && !Auth::isAdmin()) {
            $_SESSION['error'] = 'Bạn không có quyền đánh dấu vắng mặt';
            header('Location: ' . APP_URL . '/appointments');
            exit;
        }

        $appointment = $this->appointmentModel->findById($id);
        
        if (!$appointment) {
            $_SESSION['error'] = 'Không tìm thấy lịch hẹn';
            header('Location: ' . APP_URL . '/appointments');
            exit;
        }

        // Phí phạt 100% phí khám
        $noShowFee = $appointment['consultation_fee'];

        // Cập nhật trạng thái
        if ($this->appointmentModel->updateCancellation($id, 'no_show', 'Bệnh nhân không đến khám', $noShowFee)) {
            // Tạo hóa đơn phí phạt
            $this->invoiceModel->appointment_id = $id;
            $this->invoiceModel->patient_id = $appointment['patient_id'];
            $this->invoiceModel->total_amount = $noShowFee;
            $this->invoiceModel->discount_amount = 0;
            $this->invoiceModel->tax_amount = 0;
            $this->invoiceModel->final_amount = $noShowFee;
            $this->invoiceModel->status = 'pending';
            $this->invoiceModel->payment_method = 'cash';
            $this->invoiceModel->payment_status = 'unpaid';
            $this->invoiceModel->notes = 'Phí phạt không đến khám (No-show)';
            $this->invoiceModel->due_date = date('Y-m-d', strtotime('+7 days'));
            
            if ($this->invoiceModel->create()) {
                // Thêm item vào hóa đơn
                require_once __DIR__ . '/../Models/InvoiceItem.php';
                $invoiceItemModel = new InvoiceItem();
                $invoiceItemModel->invoice_id = $this->invoiceModel->id;
                $invoiceItemModel->item_type = 'other';
                $invoiceItemModel->item_id = null;
                $invoiceItemModel->description = 'Phí phạt không đến khám (No-show)';
                $invoiceItemModel->quantity = 1;
                $invoiceItemModel->unit_price = $noShowFee;
                $invoiceItemModel->total_price = $noShowFee;
                $invoiceItemModel->create();
            }

            $_SESSION['success'] = 'Đánh dấu vắng mặt thành công. Phí phạt: ' . number_format($noShowFee) . ' VNĐ';
        } else {
            $_SESSION['error'] = 'Đánh dấu vắng mặt thất bại';
        }

        header('Location: ' . APP_URL . '/appointments');
        exit;
    }

    // Hiển thị form hủy lịch
    public function showCancelForm($id) {
        Auth::requireLogin();

        $appointment = $this->appointmentModel->findById($id);
        
        if (!$appointment) {
            $_SESSION['error'] = 'Không tìm thấy lịch hẹn';
            header('Location: ' . APP_URL . '/appointments');
            exit;
        }

        // Kiểm tra quyền hủy
        if (Auth::isPatient()) {
            $patient = $this->patientModel->findByUserId(Auth::id());
            if ($appointment['patient_id'] != $patient['id']) {
                $_SESSION['error'] = 'Bạn không có quyền hủy lịch hẹn này';
                header('Location: ' . APP_URL . '/appointments');
                exit;
            }
        }

        // Tính phí hủy
        $appointmentDateTime = strtotime($appointment['appointment_date'] . ' ' . $appointment['appointment_time']);
        $now = time();
        $hoursUntilAppointment = ($appointmentDateTime - $now) / 3600;
        
        $cancellationFee = 0;
        $cancellationStatus = 'cancelled';
        
        if ($hoursUntilAppointment < 1) {
            // Hủy trong vòng 1h → Phạt 30%
            $cancellationFee = $appointment['consultation_fee'] * 0.3;
            $cancellationStatus = 'late_cancelled';
        }

        require_once APP_PATH . '/Views/appointments/cancel.php';
    }

    // Xử lý hủy lịch hẹn
    public function cancel($id) {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/appointments/' . $id . '/cancel');
            exit;
        }

        $appointment = $this->appointmentModel->findById($id);
        
        if (!$appointment) {
            $_SESSION['error'] = 'Không tìm thấy lịch hẹn';
            header('Location: ' . APP_URL . '/appointments');
            exit;
        }

        // Kiểm tra quyền hủy
        if (Auth::isPatient()) {
            $patient = $this->patientModel->findByUserId(Auth::id());
            if ($appointment['patient_id'] != $patient['id']) {
                $_SESSION['error'] = 'Bạn không có quyền hủy lịch hẹn này';
                header('Location: ' . APP_URL . '/appointments');
                exit;
            }
        }

        // Tính phí hủy
        $appointmentDateTime = strtotime($appointment['appointment_date'] . ' ' . $appointment['appointment_time']);
        $now = time();
        $hoursUntilAppointment = ($appointmentDateTime - $now) / 3600;
        
        $cancellationFee = 0;
        $cancellationStatus = 'cancelled';
        
        if ($hoursUntilAppointment < 1) {
            // Hủy trong vòng 1h → Phạt 30%
            $cancellationFee = $appointment['consultation_fee'] * 0.3;
            $cancellationStatus = 'late_cancelled';
        }

        $cancellationReason = $_POST['cancellation_reason'] ?? 'Không có lý do';

        // Cập nhật trạng thái hủy
        if ($this->appointmentModel->updateCancellation($id, $cancellationStatus, $cancellationReason, $cancellationFee)) {
            // Nếu có phí hủy → Tạo hóa đơn
            if ($cancellationFee > 0) {
                $this->invoiceModel->appointment_id = $id;
                $this->invoiceModel->patient_id = $appointment['patient_id'];
                $this->invoiceModel->total_amount = $cancellationFee;
                $this->invoiceModel->discount_amount = 0;
                $this->invoiceModel->tax_amount = 0;
                $this->invoiceModel->final_amount = $cancellationFee;
                $this->invoiceModel->status = 'pending';
                $this->invoiceModel->payment_method = 'cash';
                $this->invoiceModel->payment_status = 'unpaid';
                $this->invoiceModel->notes = 'Phí hủy lịch muộn (trong 24h)';
                $this->invoiceModel->due_date = date('Y-m-d', strtotime('+7 days'));
                
                if ($this->invoiceModel->create()) {
                    // Thêm item vào hóa đơn
                    require_once __DIR__ . '/../Models/InvoiceItem.php';
                    $invoiceItemModel = new InvoiceItem();
                    $invoiceItemModel->invoice_id = $this->invoiceModel->id;
                    $invoiceItemModel->item_type = 'other';
                    $invoiceItemModel->item_id = null;
                    $invoiceItemModel->description = 'Phí hủy lịch khám muộn';
                    $invoiceItemModel->quantity = 1;
                    $invoiceItemModel->unit_price = $cancellationFee;
                    $invoiceItemModel->total_price = $cancellationFee;
                    $invoiceItemModel->create();
                }
            }

            $_SESSION['success'] = $cancellationFee > 0 
                ? 'Hủy lịch hẹn thành công. Phí hủy: ' . number_format($cancellationFee) . ' VNĐ'
                : 'Hủy lịch hẹn thành công';
        } else {
            $_SESSION['error'] = 'Hủy lịch hẹn thất bại';
        }

        header('Location: ' . APP_URL . '/appointments');
        exit;
    }
}
