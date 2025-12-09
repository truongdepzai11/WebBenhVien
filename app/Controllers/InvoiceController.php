<?php

require_once __DIR__ . '/../Models/Invoice.php';
require_once __DIR__ . '/../Models/InvoiceItem.php';
require_once __DIR__ . '/../Models/Payment.php';
require_once __DIR__ . '/../Models/Patient.php';
require_once __DIR__ . '/../Models/Appointment.php';
require_once __DIR__ . '/../Models/Doctor.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class InvoiceController {
    private $invoiceModel;
    private $invoiceItemModel;
    private $paymentModel;
    private $patientModel;
    private $appointmentModel;
    private $doctorModel;

    public function __construct() {
        $this->invoiceModel = new Invoice();
        $this->invoiceItemModel = new InvoiceItem();
        $this->paymentModel = new Payment();
        $this->patientModel = new Patient();
        $this->appointmentModel = new Appointment();
        $this->doctorModel = new Doctor();
    }

    // Danh sách hóa đơn
    public function index() {
        Auth::requireLogin();

        if (Auth::isPatient()) {
            // Bệnh nhân chỉ xem hóa đơn của mình
            $patient = $this->patientModel->findByUserId(Auth::id());
            $invoices = $this->invoiceModel->getByPatientId($patient['id']);
        } elseif (Auth::isDoctor()) {
            // Bác sĩ chỉ xem hóa đơn của bệnh nhân đã khám với mình
            $doctor = $this->doctorModel->findByUserId(Auth::id());
            $appointments = $this->appointmentModel->getByDoctorId($doctor['id']);
            
            // Lấy danh sách patient_id từ lịch hẹn của bác sĩ
            $patientIds = array_unique(array_column($appointments, 'patient_id'));
            
            // Lấy hóa đơn của các bệnh nhân này
            $allInvoices = $this->invoiceModel->getAll();
            $invoices = array_filter($allInvoices, function($invoice) use ($patientIds) {
                return in_array($invoice['patient_id'], $patientIds);
            });
        } else {
            // Admin/Lễ tân xem tất cả
            $invoices = $this->invoiceModel->getAll();
        }

        require_once APP_PATH . '/Views/invoices/index.php';
    }

    // Chi tiết hóa đơn
    public function show($id) {
        Auth::requireLogin();

        $invoice = $this->invoiceModel->findById($id);
        
        if (!$invoice) {
            $_SESSION['error'] = 'Không tìm thấy hóa đơn';
            header('Location: ' . APP_URL . '/invoices');
            exit;
        }

        // Kiểm tra quyền xem
        if (Auth::isPatient()) {
            $patient = $this->patientModel->findByUserId(Auth::id());
            if ($invoice['patient_id'] != $patient['id']) {
                $_SESSION['error'] = 'Bạn không có quyền xem hóa đơn này';
                header('Location: ' . APP_URL . '/invoices');
                exit;
            }
        }

        // Lấy chi tiết items
        $items = $this->invoiceItemModel->getByInvoiceId($id);
        
        // Lấy lịch sử thanh toán
        $payments = $this->paymentModel->getByInvoiceId($id);
        
        // Lấy thông tin bác sĩ từ appointment (nếu có)
        $doctor_name = null;
        $packageServiceDetails = [];
        $packageTotal = 0;
        if ($invoice['appointment_id']) {
            $appointment = $this->appointmentModel->findById($invoice['appointment_id']);
            if ($appointment) {
                $doctor_name = $appointment['doctor_name'];

                // Nếu là lịch tổng hợp gói: gom đầy đủ dịch vụ trong gói (BS, ngày, giờ, giá)
                if (!empty($appointment['package_id']) && !empty($appointment['package_appointment_id'])) {
                    $reason = $appointment['reason'] ?? '';
                    if (strpos($reason, ':') !== false) {
                        require_once __DIR__ . '/../Models/HealthPackage.php';
                        $hpModel = new HealthPackage();
                        // Map giá dịch vụ theo tên
                        $services = $hpModel->getPackageServices($appointment['package_id']);
                        $priceMap = [];
                        if (is_array($services)) {
                            foreach ($services as $sv) {
                                $priceMap[$sv['service_name']] = (float)($sv['service_price'] ?? 0);
                                $packageTotal += (float)($sv['service_price'] ?? 0);
                            }
                        }

                        // Lấy tất cả lịch con theo gói
                        $childAppointments = $this->appointmentModel->getByPackageAppointmentId($appointment['package_appointment_id']);
                        foreach ($childAppointments as $apt) {
                            // Bỏ qua chính lịch tổng hợp (trùng id), còn lại coi là dịch vụ đơn lẻ
                            if (!empty($appointment['id']) && (int)$apt['id'] === (int)$appointment['id']) { continue; }
                            $serviceName = $apt['reason'] ?? '';
                            $packageServiceDetails[] = [
                                'service_name' => $serviceName,
                                'doctor_name' => $apt['doctor_name'] ?? null,
                                'appointment_date' => $apt['appointment_date'] ?? null,
                                'appointment_time' => $apt['appointment_time'] ?? null,
                                'price' => $priceMap[$serviceName] ?? 0,
                            ];
                        }
                    }
                }
            }
        }

        require_once APP_PATH . '/Views/invoices/show.php';
    }

    // Form tạo hóa đơn mới
    public function create() {
        Auth::requireLogin();
        
        if (!Auth::isAdmin() && !Auth::isDoctor() && !Auth::isReceptionist()) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập';
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        // Nếu là bác sĩ, chỉ lấy lịch hẹn của bác sĩ đó
        if (Auth::isDoctor()) {
            $doctor = $this->doctorModel->findByUserId(Auth::id());
            $allAppointments = $this->appointmentModel->getByDoctorId($doctor['id']);
        } else {
            // Admin/Lễ tân lấy tất cả
            $allAppointments = $this->appointmentModel->getAll();
        }
        
        // Lấy danh sách bệnh nhân ĐÃ KHÁM (có lịch hẹn completed)
        $patientsWithCompletedAppointments = [];
        
        foreach ($allAppointments as $apt) {
            if ($apt['status'] === 'completed') {
                $patientId = $apt['patient_id'];
                if (!isset($patientsWithCompletedAppointments[$patientId])) {
                    $patient = $this->patientModel->findById($patientId);
                    if ($patient) {
                        $patientsWithCompletedAppointments[$patientId] = $patient;
                    }
                }
            }
        }
        
        $patients = array_values($patientsWithCompletedAppointments);
        
        // Lấy danh sách lịch hẹn đã hoàn thành
        $appointments = array_filter($allAppointments, function($apt) {
            return $apt['status'] === 'completed';
        });

        require_once APP_PATH . '/Views/invoices/create.php';
    }

    // Tạo hóa đơn từ lịch hẹn (TỰ ĐỘNG ĐIỀN THÔNG TIN)
    public function createFromAppointment($appointment_id) {
        Auth::requireLogin();
        
        if (!Auth::isAdmin() && !Auth::isDoctor() && !Auth::isReceptionist()) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập';
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        $appointment = $this->appointmentModel->findById($appointment_id);
        
        if (!$appointment) {
            $_SESSION['error'] = 'Không tìm thấy lịch hẹn';
            header('Location: ' . APP_URL . '/appointments');
            exit;
        }

        // Kiểm tra đã có hóa đơn chưa
        $existingInvoice = null;
        $allInvoices = $this->invoiceModel->getAll();
        foreach ($allInvoices as $inv) {
            if ($inv['appointment_id'] == $appointment_id) {
                $existingInvoice = $inv;
                break;
            }
        }

        if ($existingInvoice) {
            $_SESSION['error'] = 'Lịch hẹn này đã có hóa đơn';
            header('Location: ' . APP_URL . '/invoices/' . $existingInvoice['id']);
            exit;
        }

        // Nếu là lịch tổng hợp gói: tự động điền tổng giá gói làm mặc định
        if (!empty($appointment['package_id']) && !empty($appointment['package_appointment_id'])) {
            $reason = $appointment['reason'] ?? '';
            if (strpos($reason, ':') !== false) {
                require_once __DIR__ . '/../Models/HealthPackage.php';
                $hpModel = new HealthPackage();
                $services = $hpModel->getPackageServices($appointment['package_id']);
                $totalPackage = 0;
                $priceMap = [];
                if (is_array($services)) {
                    foreach ($services as $sv) {
                        $price = (float)($sv['service_price'] ?? 0);
                        $priceMap[$sv['service_name']] = $price;
                        $totalPackage += $price;
                    }
                }
                // Lấy tất cả lịch con theo gói để prefill từng dịch vụ
                $packageServiceDetails = [];
                $childAppointments = $this->appointmentModel->getByPackageAppointmentId($appointment['package_appointment_id']);
                foreach ($childAppointments as $apt) {
                    // Bỏ qua dòng tổng hợp
                    if (strpos($apt['reason'] ?? '', ':') !== false) { continue; }
                    $serviceName = $apt['reason'] ?? '';
                    $packageServiceDetails[] = [
                        'service_name' => $serviceName,
                        'doctor_name' => $apt['doctor_name'] ?? null,
                        'appointment_date' => $apt['appointment_date'] ?? null,
                        'appointment_time' => $apt['appointment_time'] ?? null,
                        'price' => $priceMap[$serviceName] ?? 0,
                    ];
                }

                // Dùng các field sẵn có trong view
                $appointment['consultation_fee'] = $totalPackage; // tổng gói (fallback)
                $pkgName = $appointment['package_name'] ?? 'Gói khám';
                $appointment['specialization'] = 'Gói khám: ' . $pkgName;
            }
        }

        // Lấy danh sách bệnh nhân (để hiển thị trong form)
        $patients = $this->patientModel->getAll();
        $appointments = $this->appointmentModel->getAll();

        require_once APP_PATH . '/Views/invoices/create_from_appointment.php';
    }

    // Lưu hóa đơn mới
    public function store() {
        Auth::requireLogin();
        
        if (!Auth::isAdmin() && !Auth::isDoctor() && !Auth::isReceptionist()) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập';
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/invoices/create');
            exit;
        }

        // Tạo hóa đơn
        $this->invoiceModel->appointment_id = $_POST['appointment_id'] ?? null;
        $this->invoiceModel->patient_id = $_POST['patient_id'];
        $this->invoiceModel->total_amount = $_POST['total_amount'];
        $this->invoiceModel->discount_amount = $_POST['discount_amount'] ?? 0;
        $this->invoiceModel->tax_amount = $_POST['tax_amount'] ?? 0;
        $this->invoiceModel->final_amount = $_POST['final_amount'];
        $this->invoiceModel->status = 'pending';
        $this->invoiceModel->payment_method = $_POST['payment_method'] ?? 'cash';
        $this->invoiceModel->payment_status = 'unpaid';
        $this->invoiceModel->notes = $_POST['notes'] ?? null;
        $this->invoiceModel->due_date = $_POST['due_date'] ?? date('Y-m-d', strtotime('+7 days'));

        if ($this->invoiceModel->create()) {
            $invoice_id = $this->invoiceModel->id;

            // Thêm các items
            if (isset($_POST['items']) && is_array($_POST['items'])) {
                foreach ($_POST['items'] as $item) {
                    if (!empty($item['description'])) {
                        $this->invoiceItemModel->invoice_id = $invoice_id;
                        $this->invoiceItemModel->item_type = $item['type'];
                        $this->invoiceItemModel->item_id = $item['item_id'] ?? null;
                        $this->invoiceItemModel->description = $item['description'];
                        $this->invoiceItemModel->quantity = $item['quantity'];
                        $this->invoiceItemModel->unit_price = $item['unit_price'];
                        $this->invoiceItemModel->total_price = $item['total_price'];
                        $this->invoiceItemModel->create();
                    }
                }
            }

            $_SESSION['success'] = 'Tạo hóa đơn thành công';
            header('Location: ' . APP_URL . '/invoices/' . $invoice_id);
            exit;
        } else {
            $_SESSION['error'] = 'Tạo hóa đơn thất bại';
            header('Location: ' . APP_URL . '/invoices/create');
            exit;
        }
    }

    // Thanh toán hóa đơn
    public function pay($id) {
        Auth::requireLogin();

        $invoice = $this->invoiceModel->findById($id);
        
        if (!$invoice) {
            $_SESSION['error'] = 'Không tìm thấy hóa đơn';
            header('Location: ' . APP_URL . '/invoices');
            exit;
        }

        // Kiểm tra quyền
        if (Auth::isPatient()) {
            $patient = $this->patientModel->findByUserId(Auth::id());
            if ($invoice['patient_id'] != $patient['id']) {
                $_SESSION['error'] = 'Bạn không có quyền thanh toán hóa đơn này';
                header('Location: ' . APP_URL . '/invoices');
                exit;
            }
        }

        require_once APP_PATH . '/Views/invoices/pay.php';
    }

    // Xử lý thanh toán
    public function processPayment($id) {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/invoices/' . $id);
            exit;
        }

        $invoice = $this->invoiceModel->findById($id);
        
        if (!$invoice) {
            $_SESSION['error'] = 'Không tìm thấy hóa đơn';
            header('Location: ' . APP_URL . '/invoices');
            exit;
        }

        $payment_method = $_POST['payment_method'];

        // Tạo payment record
        $this->paymentModel->invoice_id = $id;
        $this->paymentModel->amount = $invoice['final_amount'];
        $this->paymentModel->payment_method = $payment_method;
        $this->paymentModel->payment_status = 'pending';
        $this->paymentModel->transaction_id = null;
        $this->paymentModel->gateway_response = null;
        $this->paymentModel->payment_date = null;

        if ($payment_method === 'cash') {
            // Thanh toán tiền mặt
            if (Auth::isAdmin() || Auth::isReceptionist()) {
                // Admin/Lễ tân xác nhận thanh toán ngay
                $this->paymentModel->payment_status = 'success';
                $this->paymentModel->payment_date = date('Y-m-d H:i:s');
                
                if ($this->paymentModel->create()) {
                    // Cập nhật trạng thái hóa đơn
                    $this->invoiceModel->updatePaymentStatus($id, 'paid', date('Y-m-d H:i:s'));
                    
                    $_SESSION['success'] = 'Xác nhận thanh toán thành công';
                    header('Location: ' . APP_URL . '/invoices/' . $id);
                    exit;
                }
            } else {
                // Bệnh nhân chỉ tạo yêu cầu thanh toán
                $this->paymentModel->payment_status = 'pending';
                
                if ($this->paymentModel->create()) {
                    $_SESSION['success'] = 'Đã gửi yêu cầu thanh toán. Vui lòng thanh toán tại quầy thu ngân.';
                    header('Location: ' . APP_URL . '/invoices/' . $id);
                    exit;
                }
            }
        } elseif ($payment_method === 'momo') {
            // Chuyển đến trang thanh toán MoMo
            $this->paymentModel->create();
            header('Location: ' . APP_URL . '/invoices/' . $id . '/momo');
            exit;
        } elseif ($payment_method === 'zalopay') {
            // Chuyển đến trang thanh toán ZaloPay
            $this->paymentModel->create();
            header('Location: ' . APP_URL . '/invoices/' . $id . '/zalopay');
            exit;
        } elseif ($payment_method === 'vnpay') {
            // Chuyển đến trang thanh toán VNPay
            $this->paymentModel->create();
            header('Location: ' . APP_URL . '/invoices/' . $id . '/vnpay');
            exit;
        }

        $_SESSION['error'] = 'Thanh toán thất bại';
        header('Location: ' . APP_URL . '/invoices/' . $id);
        exit;
    }

    // In hóa đơn
    public function print($id) {
        Auth::requireLogin();

        $invoice = $this->invoiceModel->findById($id);
        
        if (!$invoice) {
            $_SESSION['error'] = 'Không tìm thấy hóa đơn';
            header('Location: ' . APP_URL . '/invoices');
            exit;
        }

        // Kiểm tra quyền
        if (Auth::isPatient()) {
            $patient = $this->patientModel->findByUserId(Auth::id());
            if ($invoice['patient_id'] != $patient['id']) {
                $_SESSION['error'] = 'Bạn không có quyền in hóa đơn này';
                header('Location: ' . APP_URL . '/invoices');
                exit;
            }
        }

        $items = $this->invoiceItemModel->getByInvoiceId($id);

        require_once APP_PATH . '/Views/invoices/print.php';
    }

    // Xóa hóa đơn
    public function delete($id) {
        Auth::requireAdmin();

        if ($this->invoiceModel->delete($id)) {
            $_SESSION['success'] = 'Xóa hóa đơn thành công';
        } else {
            $_SESSION['error'] = 'Xóa hóa đơn thất bại';
        }

        header('Location: ' . APP_URL . '/invoices');
        exit;
    }

    // Thanh toán MoMo
    public function momo($id) {
        Auth::requireLogin();

        $invoice = $this->invoiceModel->findById($id);
        
        if (!$invoice) {
            $_SESSION['error'] = 'Không tìm thấy hóa đơn';
            header('Location: ' . APP_URL . '/invoices');
            exit;
        }

        // Tính số tiền còn phải thanh toán
        $payments = $this->paymentModel->getByInvoiceId($id);
        $paid = 0;
        foreach ($payments as $p) {
            if (($p['payment_status'] ?? '') === 'success') {
                $paid += (int)$p['amount'];
            }
        }
        $final = (int)$invoice['final_amount'];
        $amount = max(0, $final - $paid);
        if ($amount <= 0) {
            $_SESSION['warning'] = 'Hóa đơn đã được thanh toán đủ.';
            header('Location: ' . APP_URL . '/invoices/' . $id);
            exit;
        }

        // Tạo bản ghi payment pending (momo)
        $this->paymentModel->invoice_id = $id;
        $this->paymentModel->amount = $amount;
        $this->paymentModel->payment_method = 'momo';
        $this->paymentModel->payment_status = 'pending';
        $this->paymentModel->transaction_id = null;
        $this->paymentModel->gateway_response = null;
        $this->paymentModel->payment_date = null;
        $this->paymentModel->create();

        // Dùng payment_code làm orderId để đối soát idempotent
        $orderId = $this->paymentModel->payment_code;

        // Đọc config MoMo
        $cfg = require APP_PATH . '/../config/momo.php';
        $endpoint = $cfg['endpoint'];
        $partnerCode = $cfg['partnerCode'];
        $accessKey = $cfg['accessKey'];
        $secretKey = $cfg['secretKey'];
        $orderInfo = $cfg['orderInfo'] . ' #' . ($invoice['invoice_code'] ?? $id);
        $returnUrl = $cfg['returnUrl'];
        $ipnUrl = $cfg['ipnUrl'];
        $requestId = $orderId; // dùng cùng giá trị
        $extraData = base64_encode(json_encode(['invoice_id'=>$id, 'payment_code'=>$orderId]));
        $requestType = $cfg['requestType'];
        $lang = $cfg['lang'] ?? 'vi';

        // Validate config
        if (!$partnerCode || strpos($partnerCode, 'YOUR_') === 0 || !$accessKey || strpos($accessKey, 'YOUR_') === 0 || !$secretKey || strpos($secretKey, 'YOUR_') === 0) {
            $_SESSION['error'] = 'Chưa cấu hình MoMo Sandbox (partnerCode/accessKey/secretKey). Vui lòng điền trong config/momo.php.';
            header('Location: ' . APP_URL . '/invoices/' . $id);
            exit;
        }

        $raw = "accessKey={$accessKey}&amount={$amount}&extraData={$extraData}&ipnUrl={$ipnUrl}&orderId={$orderId}&orderInfo={$orderInfo}&partnerCode={$partnerCode}&redirectUrl={$returnUrl}&requestId={$requestId}&requestType={$requestType}";
        $signature = hash_hmac('sha256', $raw, $secretKey);

        $payload = [
            'partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $requestId,
            'amount' => (string)$amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $returnUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => $lang,
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
        ];

        if (!function_exists('curl_init')) {
            $_SESSION['error'] = 'Máy chủ chưa bật cURL PHP nên không thể gọi MoMo. Vui lòng bật php_curl.';
            header('Location: ' . APP_URL . '/invoices/' . $id);
            exit;
        }

        $jsonPayload = json_encode($payload);
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json' ]);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        $res = @json_decode($result, true);
        if ($httpCode === 200 && isset($res['payUrl'])) {
            header('Location: ' . $res['payUrl']);
            exit;
        }

        // Ghi log để debug
        @mkdir(BASE_PATH . '/storage/logs', 0777, true);
        @file_put_contents(BASE_PATH . '/storage/logs/momo.log', date('c') . "\nREQ: " . $jsonPayload . "\nRES({$httpCode}): " . $result . "\nERR: " . $curlErr . "\n\n", FILE_APPEND);

        $errMsg = 'Không khởi tạo được thanh toán MoMo.';
        if (is_array($res) && isset($res['message'])) { $errMsg .= ' ' . $res['message']; }
        $_SESSION['error'] = $errMsg;
        header('Location: ' . APP_URL . '/invoices/' . $id);
        exit;
    }

    // Thanh toán VNPay
    public function vnpay($id) {
        Auth::requireLogin();

        $invoice = $this->invoiceModel->findById($id);
        
        if (!$invoice) {
            $_SESSION['error'] = 'Không tìm thấy hóa đơn';
            header('Location: ' . APP_URL . '/invoices');
            exit;
        }

        // TODO: Tích hợp VNPay API
        // Cần: Terminal ID, Secret Key từ VNPay
        
        require_once APP_PATH . '/Views/invoices/vnpay.php';
    }
}
