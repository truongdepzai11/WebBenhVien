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
    $controller->pay($id);
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

// API - Get package services
$router->get('/api/package-services/{package_id}', function($package_id) {
    $controller = new PackageController();
    $controller->getServicesJson($package_id);
});

return $router;
