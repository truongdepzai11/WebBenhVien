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

    // Lưu nháp kết quả chỉ số cho lịch con (dịch vụ trong gói)
    public function saveResults($id) {
        Auth::requireLogin();
        if (!Auth::isDoctor()) { header('Location: ' . APP_URL . '/appointments/' . $id); exit; }

        $apt = $this->appointmentModel->findById($id);
        if (!$apt || empty($apt['package_appointment_id']) || empty($apt['doctor_id'])) {
            $_SESSION['error'] = 'Chỉ áp dụng cho lịch dịch vụ trong gói đã phân công.';
            header('Location: ' . APP_URL . '/appointments/' . $id); exit;
        }

        // Chỉ bác sĩ được phân công mới được nhập
        $doctor = $this->doctorModel->findByUserId(Auth::id());
        if (!$doctor || (int)$doctor['id'] !== (int)$apt['doctor_id']) {
            $_SESSION['error'] = 'Bạn không có quyền thao tác kết quả cho lịch này.';
            header('Location: ' . APP_URL . '/appointments/' . $id); exit;
        }

        // Tìm appointment tổng hợp của gói và hàng APS tương ứng theo service name (reason)
        require_once APP_PATH . '/Models/AppointmentPackageService.php';
        $summary = $this->appointmentModel->findSummaryByPackageAppointmentId($apt['package_appointment_id']);
        $apsModel = new AppointmentPackageService();
        $aps = $apsModel->findByPackageAppointmentAndServiceName($apt['package_appointment_id'], $apt['reason']);
        if (!$summary || !$aps) {
            $_SESSION['error'] = 'Không xác định được dịch vụ trong gói để lưu kết quả.';
            header('Location: ' . APP_URL . '/appointments/' . $id); exit;
        }

        // Lưu các dòng chỉ số vào package_test_results (xóa cũ rồi chèn lại đơn giản)
        $metrics = $_POST['metric_name'] ?? [];
        $values  = $_POST['result_value'] ?? [];
        $units   = $_POST['unit'] ?? [];
        $ranges  = $_POST['reference_range'] ?? [];
        $flags   = $_POST['flag'] ?? [];
        $notes   = $_POST['note'] ?? [];

        $db = new Database(); $conn = $db->getConnection();
        $conn->beginTransaction();
        try {
            $del = $conn->prepare('DELETE FROM package_test_results WHERE appointment_id = ? AND service_id = ?');
            $del->execute([(int)$summary['id'], (int)$aps['service_id']]);

            $ins = $conn->prepare('INSERT INTO package_test_results (appointment_id, service_id, metric_name, result_value, result_status, reference_range, notes) VALUES (?,?,?,?,?,?,?)');
            $count = max(count($metrics), count($values));
            for ($i=0; $i<$count; $i++) {
                $m = trim($metrics[$i] ?? '');
                $v = trim($values[$i] ?? '');
                if ($m === '' && $v === '') continue; // bỏ dòng trống
                $ins->execute([
                    (int)$summary['id'],
                    (int)$aps['service_id'],
                    $m,
                    $v,
                    $_POST['result_status'][$i] ?? null,
                    $ranges[$i] ?? null,
                    $_POST['notes'][$i] ?? null,
                ]);
            }
            // Cập nhật trạng thái nháp
            $apsModel->updateResultStateById($aps['id'], 'draft');
            $conn->commit();
            $_SESSION['success'] = 'Đã lưu nháp kết quả.';
        } catch (\Throwable $e) {
            $conn->rollBack();
            $_SESSION['error'] = 'Lỗi lưu kết quả: ' . $e->getMessage();
        }
        header('Location: ' . APP_URL . '/appointments/' . $id); exit;
    }

    // Nộp kết quả (khóa bác sĩ chỉnh) → chuyển result_state = submitted
    public function submitResults($id) {
        Auth::requireLogin();
        if (!Auth::isDoctor()) { header('Location: ' . APP_URL . '/appointments/' . $id); exit; }

        $apt = $this->appointmentModel->findById($id);
        if (!$apt || empty($apt['package_appointment_id']) || empty($apt['doctor_id'])) {
            $_SESSION['error'] = 'Chỉ áp dụng cho lịch dịch vụ trong gói đã phân công.';
            header('Location: ' . APP_URL . '/appointments/' . $id); exit;
        }
        $doctor = $this->doctorModel->findByUserId(Auth::id());
        if (!$doctor || (int)$doctor['id'] !== (int)$apt['doctor_id']) {
            $_SESSION['error'] = 'Bạn không có quyền thao tác kết quả cho lịch này.';
            header('Location: ' . APP_URL . '/appointments/' . $id); exit;
        }

        require_once APP_PATH . '/Models/AppointmentPackageService.php';
        $summary = $this->appointmentModel->findSummaryByPackageAppointmentId($apt['package_appointment_id']);
        $apsModel = new AppointmentPackageService();
        $aps = $apsModel->findByPackageAppointmentAndServiceName($apt['package_appointment_id'], $apt['reason']);
        if (!$summary || !$aps) {
            $_SESSION['error'] = 'Không xác định được dịch vụ trong gói để nộp kết quả.';
            header('Location: ' . APP_URL . '/appointments/' . $id); exit;
        }

        // Lưu dữ liệu giống như saveResults rồi đặt state=approved (bỏ duyệt admin)
        $metrics = $_POST['metric_name'] ?? [];
        $values  = $_POST['result_value'] ?? [];
        $units   = $_POST['unit'] ?? [];
        $ranges  = $_POST['reference_range'] ?? [];
        $flags   = $_POST['flag'] ?? [];
        $notes   = $_POST['note'] ?? [];

        $db = new Database(); $conn = $db->getConnection();
        $conn->beginTransaction();
        try {
            $del = $conn->prepare('DELETE FROM package_test_results WHERE appointment_id = ? AND service_id = ?');
            $del->execute([(int)$summary['id'], (int)$aps['service_id']]);

            $ins = $conn->prepare('INSERT INTO package_test_results (appointment_id, service_id, metric_name, result_value, result_status, reference_range, notes) VALUES (?,?,?,?,?,?,?)');
            $count = max(count($metrics), count($values));
            for ($i=0; $i<$count; $i++) {
                $m = trim($metrics[$i] ?? '');
                $v = trim($values[$i] ?? '');
                if ($m === '' && $v === '') continue;
                $ins->execute([
                    (int)$summary['id'],
                    (int)$aps['service_id'],
                    $m,
                    $v,
                    $_POST['result_status'][$i] ?? null,
                    $ranges[$i] ?? null,
                    $_POST['notes'][$i] ?? null,
                ]);
            }
            // Cập nhật result_json (findings/conclusion) nếu có
            $findings = trim($_POST['findings'] ?? '');
            $conclusion = trim($_POST['conclusion'] ?? '');
            if ($findings !== '' || $conclusion !== '') {
                $json = [
                    'findings' => $findings,
                    'conclusion' => $conclusion,
                ];
                $up = $conn->prepare('UPDATE appointment_package_services SET result_json = :js WHERE id = :id');
                $up->execute([':js'=>json_encode($json, JSON_UNESCAPED_UNICODE), ':id'=>(int)$aps['id']]);
            }
            $apsModel->updateResultStateById($aps['id'], 'approved');

            // Recalc final_status của package_appointments ngay sau khi approve một dịch vụ
            // Đếm trạng thái các dịch vụ thuộc summary appointment
            $st = $conn->prepare('SELECT result_state FROM appointment_package_services WHERE appointment_id = ?');
            $st->execute([(int)$summary['id']]);
            $rows = $st->fetchAll(PDO::FETCH_COLUMN) ?: [];
            $allApproved = !empty($rows) && count(array_filter($rows, function($s){ return $s === 'approved'; })) === count($rows);
            $final = $allApproved ? 'approved' : 'in_progress';
            $paUpdSql = 'UPDATE package_appointments SET final_status = :fs';
            $params = [':fs'=>$final, ':id'=>$apt['package_appointment_id']];
            if ($final === 'approved') {
                $paUpdSql .= ', approved_by = :ab, approved_at = NOW()';
                $params[':ab'] = Auth::id();
            }
            $paUpdSql .= ' WHERE id = :id';
            $paUpd = $conn->prepare($paUpdSql);
            $paUpd->execute($params);

            $conn->commit();
            $_SESSION['success'] = 'Đã gửi và phê duyệt kết quả cho bệnh nhân.';
        } catch (\Throwable $e) {
            $conn->rollBack();
            $_SESSION['error'] = 'Lỗi nộp kết quả: ' . $e->getMessage();
        }
        header('Location: ' . APP_URL . '/appointments/' . $id); exit;
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
        
        // Áp dụng lọc gộp (summary) cho BỆNH NHÂN và ADMIN để ẩn các lịch chi tiết dịch vụ trong gói
        if ($role === 'patient' || $role === 'admin') {
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
            // Bổ sung thông tin hiển thị cho lịch gói (dùng cho bảng của patient/admin)
            $regularAppointments = array_map(function($apt) {
                if (!empty($apt['package_appointment_id'])) {
                    $assigned = $this->appointmentModel->getAssignedDoctorsByPackageAppointmentId($apt['package_appointment_id']);
                    $apt['assigned_doctors'] = $assigned ?: [];
                    // Đếm số đã phân công và tổng số dịch vụ trong gói
                    $apt['assigned_count'] = $this->appointmentModel->countAssignedByPackageAppointmentId($apt['package_appointment_id']);
                    if (!isset($this->packageModel)) {
                        require_once APP_PATH . '/Models/HealthPackage.php';
                        $this->packageModel = new HealthPackage();
                    }
                    $services = $this->packageModel->getPackageServices($apt['package_id']);
                    $apt['total_services'] = is_array($services) ? count($services) : 0;
                    // Lấy danh sách các ngày khám khác nhau
                    $apt['appointment_dates'] = $this->appointmentModel->getAppointmentDatesByPackageAppointmentId($apt['package_appointment_id']);
                }
                return $apt;
            }, array_values($regularAppointments));
            
            // Chỉ sử dụng danh sách đã lọc cho view (ẩn các lịch chi tiết dịch vụ)
            $appointments = array_values($regularAppointments);
            $regularAppointments = [];
            $packageAppointments = [];
        }

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
            
            // Tính danh sách dịch vụ được chọn: dịch vụ bắt buộc + tùy chọn đã tick
            $allServices = $this->packageModel->getPackageServices($_POST['package_id']);
            $requiredIds = array_map(function($s){ return (int)$s['id']; }, array_filter($allServices, function($s){ return !empty($s['is_required']); }));
            $optionalSelected = array_map('intval', $_POST['selected_services'] ?? []);
            $selectedServiceIds = array_values(array_unique(array_merge($requiredIds, $optionalSelected)));

            // Tính tổng giá theo dịch vụ được chọn
            $selectedPriceMap = [];
            foreach ($allServices as $svc) {
                $selectedPriceMap[(int)$svc['id']] = (float)($svc['service_price'] ?? 0);
            }
            $totalSelected = 0;
            foreach ($selectedServiceIds as $sid) { $totalSelected += ($selectedPriceMap[$sid] ?? 0); }

            // 1. Tạo package_appointment
            require_once APP_PATH . '/Models/PackageAppointment.php';
            $packageAppointmentModel = new PackageAppointment();
            
            $packageAppointmentModel->patient_id = $patient_id;
            $packageAppointmentModel->package_id = $_POST['package_id'];
            $packageAppointmentModel->appointment_date = $_POST['appointment_date'];
            $packageAppointmentModel->status = 'scheduled';
            $packageAppointmentModel->notes = $_POST['notes'] ?? null;
            $packageAppointmentModel->total_price = $totalSelected;
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
            $this->appointmentModel->total_price = $totalSelected;
            
            if ($this->appointmentModel->create()) {
                // Lưu mapping các dịch vụ đã chọn để dùng về sau (hiển thị, tính tiền)
                if (!empty($selectedServiceIds)) {
                    $this->saveAppointmentServices($this->appointmentModel->id, $selectedServiceIds);
                }
                $_SESSION['success'] = 'Đăng ký gói khám thành công! Vui lòng chờ admin phân công bác sĩ.';

                // Tạo thông báo cho bệnh nhân khi đặt gói khám
                try {
                    require_once APP_PATH . '/Models/Notification.php';
                    require_once APP_PATH . '/Helpers/Mailer.php';
                    $notif = new Notification();
                    $apt = $this->appointmentModel->findById($this->appointmentModel->id);
                    if ($apt) {
                        $patient = $this->patientModel->findById($apt['patient_id']);
                        if ($patient) {
                            $userId = (int)$patient['user_id'];
                            $dateStr = !empty($apt['appointment_date']) ? date('d/m/Y', strtotime($apt['appointment_date'])) : 'N/A';
                            $title = 'Đăng ký gói khám thành công';
                            $message = 'Bạn đã đăng ký gói khám ' . ($apt['package_name'] ?? '') . ' vào ngày ' . $dateStr . '. Vui lòng chờ phân công bác sĩ.';
                            $link = '/package-appointments/' . $packageAppointmentModel->id;
                            $notif->create($userId, $title, $message, $link, 'system');

                            $mailer = new Mailer();
                            $email = $patient['email'] ?? null;
                            $fullName = $patient['full_name'] ?? '';
                            if (!empty($email)) {
                                $html = '<p>Chào ' . htmlspecialchars($fullName) . ',</p>'
                                      . '<p>Bạn đã đăng ký gói khám <strong>' . htmlspecialchars($apt['package_name'] ?? '') . '</strong> ngày <strong>' . $dateStr . '</strong>.</p>'
                                      . '<p>Xem chi tiết: <a href="' . APP_URL . $link . '">' . APP_URL . $link . '</a></p>';
                                $mailer->send($email, $title, $html);
                            }
                        }
                    }
                } catch (\Throwable $e) { /* silent */ }
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

            // Tạo thông báo cho bệnh nhân khi đặt lịch khám thường
            try {
                require_once APP_PATH . '/Models/Notification.php';
                require_once APP_PATH . '/Helpers/Mailer.php';
                $notif = new Notification();
                // Lấy lại appointment kèm join để có tên bác sĩ
                $apt = $this->appointmentModel->findById($this->appointmentModel->id);
                if ($apt) {
                    $patient = $this->patientModel->findById($apt['patient_id']);
                    if ($patient) {
                        $userId = (int)$patient['user_id'];
                        $dateStr = !empty($apt['appointment_date']) ? date('d/m/Y', strtotime($apt['appointment_date'])) : 'N/A';
                        $timeStr = !empty($apt['appointment_time']) ? date('H:i', strtotime($apt['appointment_time'])) : 'không rõ giờ';
                        $doctorName = $apt['doctor_name'] ?? '';
                        $title = 'Đặt lịch khám thành công';
                        $message = 'Bạn đã đặt lịch khám ngày ' . $dateStr . ' lúc ' . $timeStr . ' với bác sĩ ' . $doctorName . '. Vui lòng chờ xác nhận.';
                        $link = '/appointments/' . $this->appointmentModel->id;
                        $notif->create($userId, $title, $message, $link, 'system');

                        $mailer = new Mailer();
                        $email = $patient['email'] ?? null;
                        $fullName = $patient['full_name'] ?? '';
                        if (!empty($email)) {
                            $html = '<p>Chào ' . htmlspecialchars($fullName) . ',</p>'
                                  . '<p>Bạn đã đặt lịch khám với <strong>' . htmlspecialchars($doctorName) . '</strong> vào <strong>' . $dateStr . ' ' . $timeStr . '</strong>. Vui lòng chờ xác nhận.</p>'
                                  . '<p>Xem chi tiết: <a href="' . APP_URL . $link . '">' . APP_URL . $link . '</a></p>';
                            $mailer->send($email, $title, $html);
                        }

                        // Thông báo cho bác sĩ có lịch hẹn mới (chờ xác nhận)
                        if (!empty($apt['doctor_id'])) {
                            $doctor = $this->doctorModel->findById($apt['doctor_id']);
                            if ($doctor) {
                                $docUserId = (int)$doctor['user_id'];
                                $docTitle = 'Bạn có lịch hẹn mới cần xác nhận';
                                $docMsg = 'Bệnh nhân ' . ($apt['patient_name'] ?? '') . ' đặt lịch ngày ' . $dateStr . ' lúc ' . $timeStr . '. Vui lòng kiểm tra và xác nhận.';
                                $notif->create($docUserId, $docTitle, $docMsg, $link, 'system');

                                $docEmail = $doctor['email'] ?? null;
                                $docName = $doctor['full_name'] ?? '';
                                if (!empty($docEmail)) {
                                    $docHtml = '<p>Chào ' . htmlspecialchars($docName) . ',</p>'
                                             . '<p>Bạn có lịch hẹn mới với bệnh nhân <strong>' . htmlspecialchars($apt['patient_name'] ?? '') . '</strong> vào <strong>' . $dateStr . ' ' . $timeStr . '</strong>. Vui lòng đăng nhập để xác nhận.</p>'
                                             . '<p>Xem chi tiết: <a href="' . APP_URL . $link . '">' . APP_URL . $link . '</a></p>';
                                    $mailer->send($docEmail, $docTitle, $docHtml);
                                }
                            }
                        }
                    }
                }
            } catch (\Throwable $e) { /* silent */ }

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

        // Nếu xác nhận lịch gói khám, kiểm tra đã phân công hết dịch vụ chưa
        if ($status === 'confirmed') {
            $appointment = $this->appointmentModel->findById($id);
            
            if ($appointment && !empty($appointment['package_id']) && !empty($appointment['package_appointment_id'])) {
                $reason = $appointment['reason'] ?? '';
                
                // Kiểm tra xem đây có phải là lịch tổng hợp gói không (có chứa dấu ":")
                if (strpos($reason, ':') !== false) {
                    // Lấy tổng số dịch vụ trong gói
                    require_once APP_PATH . '/Models/HealthPackage.php';
                    $packageModel = new HealthPackage();
                    $services = $packageModel->getPackageServices($appointment['package_id']);
                    $totalServices = is_array($services) ? count($services) : 0;
                    
                    // Đếm số dịch vụ đã phân công
                    $assignedCount = $this->appointmentModel->countAssignedByPackageAppointmentId($appointment['package_appointment_id']);
                    
                    if ($assignedCount < $totalServices) {
                        $_SESSION['error'] = "Vui lòng phân công hết dịch vụ trong gói trước khi xác nhận. Hiện tại: {$assignedCount}/{$totalServices} dịch vụ đã phân công.";
                        header('Location: ' . APP_URL . '/appointments');
                        exit;
                    }
                }
            }
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

            // Nếu cập nhật sang 'confirmed' từ form chung -> tạo thông báo + gửi email cho bệnh nhân
            if ($status === 'confirmed') {
                try {
                    require_once APP_PATH . '/Models/Notification.php';
                    require_once APP_PATH . '/Helpers/Mailer.php';
                    $notificationModel = new Notification();
                    $apt = $this->appointmentModel->findById($id);
                    if ($apt) {
                        // Nếu đây là lịch TỔNG HỢP của gói (summary) thì tự động xác nhận các lịch con còn pending
                        $isPackageSummary = !empty($apt['package_id']) && !empty($apt['package_appointment_id'])
                                            && !empty($apt['reason']) && strpos($apt['reason'], ':') !== false
                                            && empty($apt['doctor_id']);
                        if ($isPackageSummary) {
                            try {
                                $this->appointmentModel->updateChildrenStatusByPackageAppointmentId(
                                    $apt['package_appointment_id'], ['pending'], 'confirmed'
                                );
                            } catch (\Throwable $e) { /* ignore child update errors */ }
                        }
                        $patient = $this->patientModel->findById($apt['patient_id']);
                        if ($patient) {
                            $userId = (int)$patient['user_id'];
                            // define email/name early for both confirmation and reminder blocks
                            $email = $patient['email'] ?? null;
                            $fullName = $patient['full_name'] ?? '';
                            $aptDate = !empty($apt['appointment_date']) ? date('d/m/Y', strtotime($apt['appointment_date'])) : 'N/A';
                            $aptTime = !empty($apt['appointment_time']) ? date('H:i', strtotime($apt['appointment_time'])) : null;
                            $doctorName = $apt['doctor_name'] ?? '';
                            $title = 'Lịch hẹn đã được xác nhận';
                            $link = '/appointments/' . $id;

                            // Nếu là lịch theo gói khám -> chỉ hiển thị ngày, bỏ giờ/BS
                            $isPackage = !empty($apt['package_id']) && !empty($apt['package_appointment_id']);
                            if ($isPackage) {
                                $message = 'Lịch hẹn theo gói khám của bạn vào ' . $aptDate . ' đã được xác nhận.';
                            } else {
                                $timeText = $aptTime ? (' lúc ' . $aptTime) : '';
                                $doctorText = $doctorName ? (' với bác sĩ ' . $doctorName) : '';
                                $message = 'Lịch hẹn của bạn vào ' . $aptDate . $timeText . $doctorText . ' đã được xác nhận.';
                            }
                            $notificationModel->create($userId, $title, $message, $link, 'system');

                            // Tạo nhắc lịch ngay nếu lịch là hôm nay hoặc ngày mai
                            try {
                                require_once APP_PATH . '/Models/PackageAppointment.php';
                                $pkgAptModel = new PackageAppointment();
                                $aptDateStr = $apt['appointment_date'];
                                if ((empty($aptDateStr) || $aptDateStr==='0000-00-00') && !empty($apt['package_appointment_id'])) {
                                    $pkgA = $pkgAptModel->findById($apt['package_appointment_id']);
                                    if ($pkgA && !empty($pkgA['appointment_date'])) {
                                        $aptDateStr = $pkgA['appointment_date'];
                                    }
                                }
                                if (!empty($aptDateStr)) {
                                    $now = time();
                                    $ts  = strtotime($aptDateStr . ' ' . ($apt['appointment_time'] ?? '00:00:00'));
                                    if ($ts) {
                                        $sameDay = date('Y-m-d',$ts)===date('Y-m-d',$now);
                                        $tomorrow = date('Y-m-d',$ts)===date('Y-m-d', strtotime('+1 day',$now));
                                        if ($sameDay || $tomorrow) {
                                            $aptDateDisp = date('d/m/Y',$ts);
                                            $aptTimeDisp = !empty($apt['appointment_time']) ? date('H:i', strtotime($apt['appointment_time'])) : null;
                                            if ($isPackage) {
                                                $remMsg = 'Bạn có lịch hẹn theo gói vào ' . $aptDateDisp . ($aptTimeDisp ? (' lúc ' . $aptTimeDisp) : '') . '. Vui lòng đến đúng giờ.';
                                            } else {
                                                $remMsg = 'Bạn có lịch hẹn vào ' . $aptDateDisp . ($aptTimeDisp ? (' lúc ' . $aptTimeDisp) : '') . ($doctorName ? (' với bác sĩ ' . $doctorName) : '') . '. Vui lòng đến đúng giờ.';
                                            }
                                            $exists = $notificationModel->existsByUserLinkType($userId, $link, 'reminder');
                                            if (!$exists) {
                                                $notificationModel->create($userId, 'Nhắc lịch khám sắp tới', $remMsg, $link, 'reminder');
                                                // Gửi email nhắc nhở
                                                if (!empty($email)) {
                                                    $mailer2 = new Mailer();
                                                    $html2 = '<p>Chào ' . htmlspecialchars($fullName) . ',</p>'
                                                          . '<p>' . htmlspecialchars($remMsg) . '</p>'
                                                          . '<p>Xem chi tiết: <a href="' . APP_URL . $link . '">' . APP_URL . $link . '</a></p>';
                                                    $mailer2->send($email, 'Nhắc lịch khám sắp tới', $html2);
                                                }
                                            }
                                        }
                                    }
                                }
                            } catch (\Throwable $e) { /* ignore */ }

                            $mailer = new Mailer();
                            if (!empty($email)) {
                                if ($isPackage) {
                                    $html = '<p>Chào ' . htmlspecialchars($fullName) . ',</p>'
                                          . '<p>Lịch hẹn theo gói khám của bạn đã được xác nhận.</p>'
                                          . '<ul>'
                                          . '<li>Ngày: <strong>' . $aptDate . '</strong></li>'
                                          . '</ul>'
                                          . '<p>Xem chi tiết: <a href="' . APP_URL . $link . '">' . APP_URL . $link . '</a></p>';
                                } else {
                                    $html = '<p>Chào ' . htmlspecialchars($fullName) . ',</p>'
                                          . '<p>Lịch hẹn của bạn đã được xác nhận:</p>'
                                          . '<ul>'
                                          . '<li>Ngày: <strong>' . $aptDate . '</strong></li>'
                                          . ($aptTime ? ('<li>Giờ: <strong>' . $aptTime . '</strong></li>') : '')
                                          . ($doctorName ? ('<li>Bác sĩ: <strong>' . htmlspecialchars($doctorName) . '</strong></li>') : '')
                                          . '</ul>'
                                          . '<p>Xem chi tiết: <a href="' . APP_URL . $link . '">' . APP_URL . $link . '</a></p>';
                                }
                                $mailer->send($email, $title, $html);
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // bỏ qua lỗi gửi thông báo/email để không chặn luồng
                }
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

        // Bổ sung giá dịch vụ nếu là lịch thuộc gói mà chưa có total_price
        if (!empty($appointment['package_id'])) {
            $priceIsMissing = !isset($appointment['total_price']) || $appointment['total_price'] === null;
            if ($priceIsMissing) {
                require_once APP_PATH . '/Models/HealthPackage.php';
                $pkgModel = new HealthPackage();
                $services = $pkgModel->getPackageServices($appointment['package_id']);
                $reason = strtolower(trim($appointment['reason'] ?? ''));
                // 1) So khop chinh xac
                foreach ($services as $svc) {
                    if (strtolower(trim($svc['service_name'])) === $reason) {
                        $appointment['total_price'] = (float)$svc['service_price'];
                        break;
                    }
                }
                // 2) Neu chua tim thay, so khop gan dung (chua/bi chua)
                if (!isset($appointment['total_price']) || $appointment['total_price'] === null) {
                    foreach ($services as $svc) {
                        $name = strtolower(trim($svc['service_name']));
                        if ($name !== '' && ($name === $reason || strpos($name, $reason) !== false || strpos($reason, $name) !== false)) {
                            $appointment['total_price'] = (float)$svc['service_price'];
                            break;
                        }
                    }
                }
            }
        }

        // Nếu là lịch dịch vụ thuộc gói: nạp kết quả chỉ số cho view bác sĩ
        $serviceMetrics = [];
        $resultState = null;
        $reviewNote = null;
        $serviceCategory = null;
        $resultJson = null;
        if (!empty($appointment['package_appointment_id']) && !empty($appointment['doctor_id'])) {
            require_once APP_PATH . '/Models/AppointmentPackageService.php';
            $summary = $this->appointmentModel->findSummaryByPackageAppointmentId($appointment['package_appointment_id']);
            $apsModel = new AppointmentPackageService();
            $aps = $apsModel->findByPackageAppointmentAndServiceName($appointment['package_appointment_id'], $appointment['reason']);
            if ($summary && $aps) {
                $resultState = $aps['result_state'] ?? null;
                $reviewNote = $aps['review_note'] ?? null;
                $resultJson = $aps['result_json'] ?? null;
                // lấy category từ package_services
                try {
                    $db = new Database(); $conn = $db->getConnection();
                    $sc = $conn->prepare('SELECT service_category FROM package_services WHERE id = ?');
                    $sc->execute([(int)$aps['service_id']]);
                    $serviceCategory = $sc->fetchColumn() ?: null;
                } catch (\Throwable $e) { $serviceCategory = null; }
                $db = new Database(); $conn = $db->getConnection();
                $stmt = $conn->prepare('SELECT metric_name, result_value, result_status, reference_range, notes FROM package_test_results WHERE appointment_id = ? AND service_id = ? ORDER BY id');
                $stmt->execute([(int)$summary['id'], (int)$aps['service_id']]);
                $serviceMetrics = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
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

        // Lấy thông tin lịch hẹn
        $appointment = $this->appointmentModel->findById($id);
        if (!$appointment) {
            $_SESSION['error'] = 'Không tìm thấy lịch hẹn';
            header('Location: ' . APP_URL . '/appointments');
            exit;
        }

        // Kiểm tra nếu là lịch gói khám: phải phân công hết dịch vụ mới được xác nhận
        if (!empty($appointment['package_id']) && !empty($appointment['package_appointment_id'])) {
            $reason = $appointment['reason'] ?? '';
            
            // Kiểm tra xem đây có phải là lịch tổng hợp gói không (có chứa dấu ":")
            if (strpos($reason, ':') !== false) {
                // Lấy tổng số dịch vụ trong gói
                require_once APP_PATH . '/Models/HealthPackage.php';
                $packageModel = new HealthPackage();
                $services = $packageModel->getPackageServices($appointment['package_id']);
                $totalServices = is_array($services) ? count($services) : 0;
                
                // Đếm số dịch vụ đã phân công
                $assignedCount = $this->appointmentModel->countAssignedByPackageAppointmentId($appointment['package_appointment_id']);
                
                if ($assignedCount < $totalServices) {
                    $_SESSION['error'] = "Vui lòng phân công hết dịch vụ trong gói trước khi xác nhận. Hiện tại: {$assignedCount}/{$totalServices} dịch vụ đã phân công.";
                    header('Location: ' . APP_URL . '/appointments/' . $id);
                    exit;
                }
            }
        }

        if ($this->appointmentModel->updateStatus($id, 'confirmed')) {
            $_SESSION['success'] = 'Đã xác nhận lịch hẹn thành công';

            // Tạo thông báo cho bệnh nhân + gửi email (nếu cấu hình)
            try {
                require_once APP_PATH . '/Models/Notification.php';
                require_once APP_PATH . '/Helpers/Mailer.php';
                $notificationModel = new Notification();

                // Lấy lại thông tin lịch hẹn để build nội dung
                $apt = $this->appointmentModel->findById($id);
                if ($apt) {
                    // user_id của bệnh nhân lấy từ bảng patients
                    $patient = $this->patientModel->findById($apt['patient_id']);
                    if ($patient) {
                        // Lấy email + tên từ users qua findById đã join
                        $userId = (int)$patient['user_id'];
                        $aptDate = !empty($apt['appointment_date']) ? date('d/m/Y', strtotime($apt['appointment_date'])) : 'N/A';
                        $aptTime = !empty($apt['appointment_time']) ? date('H:i', strtotime($apt['appointment_time'])) : 'không rõ giờ';
                        $doctorName = $apt['doctor_name'] ?? '';
                        $title = 'Lịch hẹn đã được xác nhận';
                        $message = 'Lịch hẹn của bạn vào ' . $aptDate . ' lúc ' . $aptTime . ' với bác sĩ ' . $doctorName . ' đã được xác nhận.';
                        $link = '/appointments/' . $id;
                        $notificationModel->create($userId, $title, $message, $link, 'reminder');

                        // Gửi email nếu có
                        $mailer = new Mailer();
                        $email = $patient['email'] ?? null; // do findById join users
                        $fullName = $patient['full_name'] ?? '';
                        if (!empty($email)) {
                            $html = '<p>Chào ' . htmlspecialchars($fullName) . ',</p>'
                                  . '<p>Lịch hẹn của bạn đã được xác nhận:</p>'
                                  . '<ul>'
                                  . '<li>Ngày: <strong>' . $aptDate . '</strong></li>'
                                  . '<li>Giờ: <strong>' . $aptTime . '</strong></li>'
                                  . '<li>Bác sĩ: <strong>' . htmlspecialchars($doctorName) . '</strong></li>'
                                  . '</ul>'
                                  . '<p>Xem chi tiết: <a href="' . APP_URL . $link . '">' . APP_URL . $link . '</a></p>';
                            $mailer->send($email, $title, $html);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // silent fail to avoid blocking confirmation
            }
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

        // Tính phí hủy (an toàn với lịch thiếu ngày/giờ)
        $cancellationFee = 0;
        $cancellationStatus = 'cancelled';
        if (!empty($appointment['appointment_date']) && !empty($appointment['appointment_time'])) {
            $appointmentDateTime = strtotime($appointment['appointment_date'] . ' ' . $appointment['appointment_time']);
            $now = time();
            $hoursUntilAppointment = ($appointmentDateTime - $now) / 3600;
            if ($hoursUntilAppointment < 1) {
                // Hủy trong vòng 1h → Phạt 30%
                $base = (float)($appointment['consultation_fee'] ?? 0);
                $cancellationFee = $base * 0.3;
                $cancellationStatus = 'late_cancelled';
            }
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

        // Tính phí hủy (an toàn với lịch thiếu ngày/giờ)
        $cancellationFee = 0;
        $cancellationStatus = 'cancelled';
        if (!empty($appointment['appointment_date']) && !empty($appointment['appointment_time'])) {
            $appointmentDateTime = strtotime($appointment['appointment_date'] . ' ' . $appointment['appointment_time']);
            $now = time();
            $hoursUntilAppointment = ($appointmentDateTime - $now) / 3600;
            if ($hoursUntilAppointment < 1) {
                // Hủy trong vòng 1h → Phạt 30%
                $base = (float)($appointment['consultation_fee'] ?? 0);
                $cancellationFee = $base * 0.3;
                $cancellationStatus = 'late_cancelled';
            }
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
