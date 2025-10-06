<?php

require_once __DIR__ . '/../Models/Invoice.php';
require_once __DIR__ . '/../Models/InvoiceItem.php';
require_once __DIR__ . '/../Models/Payment.php';
require_once __DIR__ . '/../Models/Patient.php';
require_once __DIR__ . '/../Models/Appointment.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class InvoiceController {
    private $invoiceModel;
    private $invoiceItemModel;
    private $paymentModel;
    private $patientModel;
    private $appointmentModel;

    public function __construct() {
        $this->invoiceModel = new Invoice();
        $this->invoiceItemModel = new InvoiceItem();
        $this->paymentModel = new Payment();
        $this->patientModel = new Patient();
        $this->appointmentModel = new Appointment();
    }

    // Danh sách hóa đơn
    public function index() {
        Auth::requireLogin();

        if (Auth::isPatient()) {
            // Bệnh nhân chỉ xem hóa đơn của mình
            $patient = $this->patientModel->findByUserId(Auth::id());
            $invoices = $this->invoiceModel->getByPatientId($patient['id']);
        } else {
            // Admin/Doctor xem tất cả
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

        require_once APP_PATH . '/Views/invoices/show.php';
    }

    // Form tạo hóa đơn mới
    public function create() {
        Auth::requireLogin();
        
        if (!Auth::isAdmin() && !Auth::isDoctor()) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập';
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        // Lấy danh sách bệnh nhân
        $patients = $this->patientModel->getAll();
        
        // Lấy danh sách lịch hẹn đã hoàn thành chưa có hóa đơn
        $appointments = $this->appointmentModel->getAll();

        require_once APP_PATH . '/Views/invoices/create.php';
    }

    // Tạo hóa đơn từ lịch hẹn (TỰ ĐỘNG ĐIỀN THÔNG TIN)
    public function createFromAppointment($appointment_id) {
        Auth::requireLogin();
        
        if (!Auth::isAdmin() && !Auth::isDoctor()) {
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

        // Lấy danh sách bệnh nhân (để hiển thị trong form)
        $patients = $this->patientModel->getAll();
        $appointments = $this->appointmentModel->getAll();

        require_once APP_PATH . '/Views/invoices/create_from_appointment.php';
    }

    // Lưu hóa đơn mới
    public function store() {
        Auth::requireLogin();
        
        if (!Auth::isAdmin() && !Auth::isDoctor()) {
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
            if (Auth::isAdmin()) {
                // Admin xác nhận thanh toán ngay
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

        // TODO: Tích hợp MoMo API
        // Cần: Partner Code, Access Key, Secret Key từ MoMo
        
        require_once APP_PATH . '/Views/invoices/momo.php';
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
