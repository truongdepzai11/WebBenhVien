<?php

// Router đơn giản
class Router {
    private $routes = [];

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    public function dispatch($method, $uri) {
        // Loại bỏ query string
        $uri = strtok($uri, '?');
        
        // Loại bỏ base path
        $base_path = '/WebBenhvien/hospital-management-system/public';
        if (strpos($uri, $base_path) === 0) {
            $uri = substr($uri, strlen($base_path));
        }
        
        if (empty($uri) || $uri === '/') {
            $uri = '/';
        }

        // Tìm route khớp
        if (isset($this->routes[$method][$uri])) {
            return call_user_func($this->routes[$method][$uri]);
        }

        // Kiểm tra route có tham số
        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            // Escape dấu / trong route pattern
            $pattern = str_replace('/', '\/', $route);
            $pattern = preg_replace('/\{[^}]+\}/', '([^\/]+)', $pattern);
            if (preg_match('/^' . $pattern . '$/', $uri, $matches)) {
                array_shift($matches);
                return call_user_func_array($callback, $matches);
            }
        }

        // 404
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
    }
}

$router = new Router();

// Load controllers
require_once __DIR__ . '/../app/Controllers/HomeController.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/DashboardController.php';
require_once __DIR__ . '/../app/Controllers/PatientController.php';
require_once __DIR__ . '/../app/Controllers/DoctorController.php';
require_once __DIR__ . '/../app/Controllers/AppointmentController.php';
require_once __DIR__ . '/../app/Controllers/ProfileController.php';
require_once __DIR__ . '/../app/Controllers/AdminController.php';
require_once __DIR__ . '/../app/Controllers/InvoiceController.php';
require_once __DIR__ . '/../app/Controllers/ScheduleController.php';
require_once __DIR__ . '/../app/Controllers/MedicalRecordController.php';
require_once __DIR__ . '/../app/Controllers/ValidationController.php';
require_once __DIR__ . '/../app/Controllers/PackageController.php';
require_once __DIR__ . '/../app/Controllers/PackageAppointmentController.php';
require_once __DIR__ . '/../app/Controllers/ResultsController.php';
require_once __DIR__ . '/../app/Controllers/PrescriptionController.php';
require_once __DIR__ . '/../app/Controllers/DiagnosisController.php';

// Home Route (Landing Page)
$router->get('/', function() {
    $controller = new HomeController();
    $controller->index();
});

$router->get('/auth/login', function() {
    $controller = new AuthController();
    $controller->showLogin();
});

$router->post('/auth/login', function() {
    $controller = new AuthController();
    $controller->login();
});

$router->get('/auth/register', function() {
    $controller = new AuthController();
    $controller->showRegister();
});

$router->post('/auth/register', function() {
    $controller = new AuthController();
    $controller->register();
});

$router->get('/auth/logout', function() {
    $controller = new AuthController();
    $controller->logout();
});

// Dashboard Routes
$router->get('/dashboard', function() {
    $controller = new DashboardController();
    $controller->index();
});

// Notifications
$router->get('/notifications', function() {
    require_once APP_PATH . '/Controllers/NotificationController.php';
    $controller = new NotificationController();
    $controller->index();
});

$router->post('/notifications/{id}/read', function($id) {
    require_once APP_PATH . '/Controllers/NotificationController.php';
    $controller = new NotificationController();
    $controller->markRead($id);
});

$router->get('/api/notifications/unread-count', function() {
    require_once APP_PATH . '/Controllers/NotificationController.php';
    $controller = new NotificationController();
    $controller->unreadCount();
});

// Consultations (Patient)
$router->get('/consultations', function(){
    require_once APP_PATH . '/Controllers/ConsultationController.php';
    $c = new ConsultationController();
    $c->index();
});

$router->get('/consultations/create', function(){
    require_once APP_PATH . '/Controllers/ConsultationController.php';
    $c = new ConsultationController();
    $c->create();
});

$router->post('/consultations', function(){
    require_once APP_PATH . '/Controllers/ConsultationController.php';
    $c = new ConsultationController();
    $c->store();
});

$router->get('/consultations/{id}', function($id){
    require_once APP_PATH . '/Controllers/ConsultationController.php';
    $c = new ConsultationController();
    $c->show($id);
});

$router->post('/consultations/{id}/reply', function($id){
    require_once APP_PATH . '/Controllers/ConsultationController.php';
    $c = new ConsultationController();
    $c->reply($id);
});

// Consultations (Doctor)
$router->get('/doctor/consultations', function(){
    require_once APP_PATH . '/Controllers/ConsultationController.php';
    $c = new ConsultationController();
    $c->doctorIndex();
});
// Patient Routes
$router->get('/patients', function() {
    $controller = new PatientController();
    $controller->index();
});

$router->get('/patients/{id}', function($id) {
    $controller = new PatientController();
    $controller->show($id);
});

$router->get('/patients/search', function() {
    $controller = new PatientController();
    $controller->search();
});

// Doctor Routes
$router->get('/doctors', function() {
    $controller = new DoctorController();
    $controller->index();
});

$router->get('/doctors/{id}', function($id) {
    $controller = new DoctorController();
    $controller->show($id);
});

$router->get('/doctors/search', function() {
    $controller = new DoctorController();
    $controller->search();
});

// Specialization Routes (Public)
$router->get('/specializations', function() {
    require_once __DIR__ . '/../app/Controllers/SpecializationController.php';
    $controller = new SpecializationController();
    $controller->index();
});

$router->get('/specializations/{id}', function($id) {
    require_once __DIR__ . '/../app/Controllers/SpecializationController.php';
    $controller = new SpecializationController();
    $controller->show($id);
});

// Appointment Routes
$router->get('/appointments', function() {
    $controller = new AppointmentController();
    $controller->index();
});

$router->get('/appointments/{id}', function($id) {
    $controller = new AppointmentController();
    $controller->show($id);
});

$router->get('/appointments/{id}/cancel', function($id) {
    $controller = new AppointmentController();
    $controller->showCancelForm($id);
});

$router->post('/appointments/{id}/cancel', function($id) {
    $controller = new AppointmentController();
    $controller->cancel($id);
});

$router->get('/appointments/{id}/confirm', function($id) {
    $controller = new AppointmentController();
    $controller->confirm($id);
});

$router->get('/appointments/{id}/complete', function($id) {
    $controller = new AppointmentController();
    $controller->complete($id);
});

$router->get('/appointments/{id}/no-show', function($id) {
    $controller = new AppointmentController();
    $controller->markNoShow($id);
});

$router->get('/appointments/create', function() {
    $controller = new AppointmentController();
    $controller->create();
});

$router->post('/appointments/store', function() {
    $controller = new AppointmentController();
    $controller->store();
});

$router->post('/appointments/{id}/update-status', function($id) {
    $controller = new AppointmentController();
    $controller->updateStatus($id);
});

// Doctor submit/save results for package service appointment
$router->post('/appointments/{id}/results/save', function($id) {
    $controller = new AppointmentController();
    $controller->saveResults($id);
});

$router->post('/appointments/{id}/results/submit', function($id) {
    $controller = new AppointmentController();
    $controller->submitResults($id);
});

$router->post('/appointments/{id}/cancel', function($id) {
    $controller = new AppointmentController();
    $controller->cancel($id);
});

// Profile Routes
$router->get('/profile', function() {
    $controller = new ProfileController();
    $controller->index();
});

$router->post('/profile/update', function() {
    $controller = new ProfileController();
    $controller->update();
});

$router->get('/profile/change-password', function() {
    $controller = new ProfileController();
    $controller->showChangePassword();
});

$router->post('/profile/change-password', function() {
    $controller = new ProfileController();
    $controller->changePassword();
});

// Admin Routes
$router->get('/admin', function() {
    $controller = new AdminController();
    $controller->index();
});

// Admin - Specializations
$router->get('/admin/specializations', function() {
    $controller = new AdminController();
    $controller->specializations();
});

$router->get('/admin/specializations/create', function() {
    $controller = new AdminController();
    $controller->createSpecialization();
});

$router->post('/admin/specializations/store', function() {
    $controller = new AdminController();
    $controller->storeSpecialization();
});

$router->get('/admin/specializations/{id}/edit', function($id) {
    $controller = new AdminController();
    $controller->editSpecialization($id);
});

$router->post('/admin/specializations/{id}/update', function($id) {
    $controller = new AdminController();
    $controller->updateSpecialization($id);
});

$router->post('/admin/specializations/{id}/delete', function($id) {
    $controller = new AdminController();
    $controller->deleteSpecialization($id);
});

// Admin - Doctors
$router->get('/admin/doctors', function() {
    $controller = new AdminController();
    $controller->doctors();
});

$router->get('/admin/doctors/create', function() {
    $controller = new AdminController();
    $controller->createDoctor();
});

$router->post('/admin/doctors/store', function() {
    $controller = new AdminController();
    $controller->storeDoctor();
});

$router->get('/admin/doctors/{id}/edit', function($id) {
    $controller = new AdminController();
    $controller->editDoctor($id);
});

$router->post('/admin/doctors/{id}/update', function($id) {
    $controller = new AdminController();
    $controller->updateDoctor($id);
});

$router->post('/admin/doctors/{id}/delete', function($id) {
    $controller = new AdminController();
    $controller->deleteDoctor($id);
});

// Admin - Users
$router->get('/admin/users', function() {
    $controller = new AdminController();
    $controller->users();
});

// ==================== SCHEDULE ROUTES ====================
$router->get('/schedule', function() {
    $controller = new ScheduleController();
    $controller->index();
});

$router->get('/schedule/add-patient', function() {
    $controller = new ScheduleController();
    $controller->addPatient();
});

$router->post('/schedule/store-walk-in', function() {
    $controller = new ScheduleController();
    $controller->storeWalkIn();
});

$router->post('/schedule/store-package-walkin', function() {
    $controller = new ScheduleController();
    $controller->storePackageWalkin();
});

// ==================== INVOICE ROUTES ====================
$router->get('/invoices', function() {
    $controller = new InvoiceController();
    $controller->index();
});

$router->get('/invoices/create', function() {
    $controller = new InvoiceController();
    $controller->create();
});

$router->get('/invoices/create-from-appointment/{id}', function($id) {
    $controller = new InvoiceController();
    $controller->createFromAppointment($id);
});

$router->post('/invoices/store', function() {
    $controller = new InvoiceController();
    $controller->store();
});

$router->get('/invoices/{id}', function($id) {
    $controller = new InvoiceController();
    $controller->show($id);
});

$router->get('/invoices/{id}/pay', function($id) {
    $controller = new InvoiceController();
    $controller->pay($id);
});

$router->post('/invoices/{id}/pay', function($id) {
    $controller = new InvoiceController();
    $controller->processPayment($id);
});

// MoMo payment routes
$router->get('/invoices/{id}/momo', function($id){
    $controller = new InvoiceController();
    $controller->momo($id);
});

$router->get('/payments/momo/return', function(){
    require_once APP_PATH . '/Controllers/PaymentController.php';
    $pc = new PaymentController();
    $pc->momoReturn();
});

$router->post('/payments/momo/ipn', function(){
    require_once APP_PATH . '/Controllers/PaymentController.php';
    $pc = new PaymentController();
    $pc->momoIpn();
});

// ==================== MEDICAL RECORDS ROUTES ====================
$router->get('/medical-records', function() {
    $controller = new MedicalRecordController();
    $controller->index();
});

$router->get('/medical-records/create', function() {
    $controller = new MedicalRecordController();
    $controller->create();
});

$router->post('/medical-records/store', function() {
    $controller = new MedicalRecordController();
    $controller->store();
});

$router->get('/medical-records/{id}', function($id) {
    $controller = new MedicalRecordController();
    $controller->show($id);
});

$router->post('/invoices/{id}/process-payment', function($id) {
    $controller = new InvoiceController();
    $controller->processPayment($id);
});

$router->get('/invoices/{id}/print', function($id) {
    $controller = new InvoiceController();
    $controller->print($id);
});

$router->post('/invoices/{id}/delete', function($id) {
    $controller = new InvoiceController();
    $controller->delete($id);
});

// API Validation
$router->get('/api/validate/username', function() {
    $controller = new ValidationController();
    $controller->checkUsername();
});

$router->get('/api/validate/email', function() {
    $controller = new ValidationController();
    $controller->checkEmail();
});

$router->get('/api/validate/phone', function() {
    $controller = new ValidationController();
    $controller->checkPhone();
});

// API - Doctor: get patient's appointments with current doctor (confirmed/completed)
$router->get('/api/doctor/patient-appointments/{patient_id}', function($patient_id) {
    $controller = new MedicalRecordController();
    $controller->getPatientAppointmentsForDoctor($patient_id);
});

// ==================== HEALTH PACKAGES ====================

// Public - Packages
$router->get('/packages', function() {
    $controller = new PackageController();
    $controller->index();
});

$router->get('/packages/{id}', function($id) {
    $controller = new PackageController();
    $controller->show($id);
});

// Admin - Packages Management
$router->get('/admin/packages', function() {
    $controller = new PackageController();
    $controller->adminIndex();
});

$router->get('/admin/packages/create', function() {
    $controller = new PackageController();
    $controller->create();
});

$router->post('/admin/packages/store', function() {
    $controller = new PackageController();
    $controller->store();
});

$router->get('/admin/packages/{id}/edit', function($id) {
    $controller = new PackageController();
    $controller->edit($id);
});

$router->post('/admin/packages/{id}/update', function($id) {
    $controller = new PackageController();
    $controller->update($id);
});

$router->post('/admin/packages/{id}/delete', function($id) {
    $controller = new PackageController();
    $controller->delete($id);
});

$router->post('/admin/packages/{id}/toggle-status', function($id) {
    $controller = new PackageController();
    $controller->toggleStatus($id);
});

// Admin - Package Services Management
$router->get('/admin/packages/{id}/services', function($id) {
    $controller = new PackageController();
    $controller->manageServices($id);
});

$router->post('/admin/packages/{package_id}/services/add', function($package_id) {
    $controller = new PackageController();
    $controller->addService($package_id);
});

$router->post('/admin/packages/{package_id}/services/{service_id}/delete', function($package_id, $service_id) {
    $controller = new PackageController();
    $controller->deleteService($package_id, $service_id);
});

$router->post('/admin/packages/{package_id}/services/{service_id}/update-price', function($package_id, $service_id) {
    $controller = new PackageController();
    $controller->updateServicePrice($package_id, $service_id);
});

$router->post('/admin/packages/{package_id}/services/{service_id}/update-duration', function($package_id, $service_id) {
    $controller = new PackageController();
    $controller->updateServiceDuration($package_id, $service_id);
});

$router->post('/admin/packages/{package_id}/services/{service_id}/toggle-required', function($package_id, $service_id) {
    $controller = new PackageController();
    $controller->toggleServiceRequired($package_id, $service_id);
});

// Admin - Save allowed doctors for a package service
$router->post('/admin/packages/{package_id}/services/{service_id}/doctors', function($package_id, $service_id) {
    $controller = new PackageController();
    $controller->updateServiceDoctors($package_id, $service_id);
});

// Admin - Save allowed medicines (whitelist) for a package service
$router->post('/admin/packages/{package_id}/services/{service_id}/medicines', function($package_id, $service_id) {
    $controller = new PackageController();
    $controller->updateServiceMedicines($package_id, $service_id);
});

// API - Get package services
$router->get('/api/package-services/{package_id}', function($package_id) {
    $controller = new PackageController();
    $controller->getServicesJson($package_id);
});

// ==================== PACKAGE APPOINTMENT ROUTES ====================
// Danh sách đăng ký gói khám
$router->get('/package-appointments', function() {
    $controller = new PackageAppointmentController();
    $controller->index();
});

// Xem lịch hẹn của gói khám (hiện danh sách appointments)
$router->get('/package-appointments/{id}/appointments', function($id) {
    $controller = new AppointmentController();
    $controller->indexByPackage($id);
});

// Chi tiết đăng ký gói khám
$router->get('/package-appointments/{id}', function($id) {
    $controller = new PackageAppointmentController();
    $controller->show($id);
});

// Điều phối duyệt/trả về kết quả dịch vụ
$router->post('/package-appointments/{id}/review-service', function($id) {
    $controller = new PackageAppointmentController();
    $controller->reviewService($id);
});

// Xuất PDF tổng hợp kết quả
$router->get('/package-appointments/{id}/export-pdf', function($id) {
    $controller = new PackageAppointmentController();
    $controller->exportPdf($id);
});

// Tải PDF kết quả
$router->get('/package-appointments/{id}/download-pdf', function($id) {
    $controller = new PackageAppointmentController();
    $controller->downloadPdf($id);
});

// ==================== PATIENT RESULTS ROUTES ====================
$router->get('/my-results', function() {
    $controller = new ResultsController();
    $controller->index();
});

$router->get('/my-results/package/{id}', function($id) {
    $controller = new ResultsController();
    $controller->package($id);
});

// ==================== API: Medicines search (simple) ====================
$router->get('/api/medicines', function() {
    require_once __DIR__ . '/../app/Controllers/PrescriptionController.php';
    require_once __DIR__ . '/../config/database.php';
    header('Content-Type: application/json; charset=utf-8');
    try {
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $db = new Database(); $conn = $db->getConnection();
        if ($q === '') {
            $stmt = $conn->query("SELECT id, medicine_code, name, dosage_form AS form, strength, generic_name, manufacturer, category, requires_prescription, side_effects, contraindications FROM medicines ORDER BY name LIMIT 20");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } else {
            // Advanced search: split by spaces and match each term against multiple fields
            $terms = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);
            $whereParts = [];
            $params = [];
            foreach ($terms as $t) {
                $like = '%' . $t . '%';
                $whereParts[] = "(medicine_code LIKE ? OR name LIKE ? OR generic_name LIKE ? OR manufacturer LIKE ? OR category LIKE ? OR strength LIKE ? OR dosage_form LIKE ? OR description LIKE ?)";
                array_push($params, $like, $like, $like, $like, $like, $like, $like, $like);
            }
            $whereSql = implode(' AND ', $whereParts);
            $orderHint = '%' . $q . '%';
            $sql = "SELECT id, medicine_code, name, dosage_form AS form, strength, generic_name, manufacturer, category, requires_prescription, side_effects, contraindications
                    FROM medicines
                    WHERE $whereSql
                    ORDER BY 
                        CASE WHEN medicine_code LIKE ? THEN 0 ELSE 1 END,
                        CASE WHEN name LIKE ? THEN 0 ELSE 1 END,
                        name
                    LIMIT 20";
            $stmt = $conn->prepare($sql);
            $execParams = array_merge($params, [$orderHint, $orderHint]);
            $stmt->execute($execParams);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }
        echo json_encode(['items'=>$rows], JSON_UNESCAPED_UNICODE);
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['error'=>'server_error']);
    }
});

// ==================== PRESCRIPTIONS ROUTES ====================
// Doctor/Admin: save prescription for an appointment (create/update header + items)
$router->post('/appointments/{id}/prescriptions/save', function($id) {
    $controller = new PrescriptionController();
    $controller->saveForAppointment($id);
});

// Doctor/Admin: submit prescription
$router->post('/prescriptions/{id}/submit', function($id) {
    $controller = new PrescriptionController();
    $controller->submit($id);
});

// Admin/Doctor: approve prescription
$router->post('/prescriptions/{id}/approve', function($id) {
    $controller = new PrescriptionController();
    $controller->approve($id);
});

// Admin: dispense
$router->post('/prescriptions/{id}/dispense', function($id) {
    $controller = new PrescriptionController();
    $controller->dispense($id);
});

// Export/Download PDF
$router->get('/prescriptions/{id}/export-pdf', function($id) {
    $controller = new PrescriptionController();
    $controller->exportPdf($id);
});

$router->get('/prescriptions/{id}/download-pdf', function($id) {
    $controller = new PrescriptionController();
    $controller->downloadPdf($id);
});

// Export consolidated prescriptions for a package appointment
$router->get('/package-appointments/{id}/export-prescriptions', function($id) {
    $controller = new PrescriptionController();
    $controller->exportPackagePdf($id);
});

// ==================== DIAGNOSES ROUTES ====================
// Doctor/Admin: save diagnosis for an appointment
$router->post('/appointments/{id}/diagnoses/save', function($id) {
    $controller = new DiagnosisController();
    $controller->saveForAppointment($id);
});

// Doctor/Admin: submit diagnosis
$router->post('/diagnoses/{id}/submit', function($id) {
    $controller = new DiagnosisController();
    $controller->submit($id);
});

// Admin/Doctor: approve diagnosis
$router->post('/diagnoses/{id}/approve', function($id) {
    $controller = new DiagnosisController();
    $controller->approve($id);
});

// Helper API: get latest diagnosis json
$router->get('/appointments/{id}/diagnoses/latest', function($id) {
    $controller = new DiagnosisController();
    $controller->latestJson($id);
});

// Phân công bác sĩ tự động
$router->post('/package-appointments/{id}/auto-assign', function($id) {
    $controller = new PackageAppointmentController();
    $controller->autoAssignDoctors($id);
});

// Phân công bác sĩ thủ công
$router->post('/package-appointments/assign-doctor', function() {
    $controller = new PackageAppointmentController();
    $controller->assignDoctor();
});

// Hủy đăng ký gói khám
$router->post('/package-appointments/{id}/cancel', function($id) {
    $controller = new PackageAppointmentController();
    $controller->cancel($id);
});

return $router;
