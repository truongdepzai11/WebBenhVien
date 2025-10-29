<?php

require_once __DIR__ . '/../Models/HealthPackage.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class PackageController {
    private $packageModel;

    public function __construct() {
        $this->packageModel = new HealthPackage();
    }

    // ==================== PUBLIC VIEWS ====================
    
    // Danh sách gói khám (Public)
    public function index() {
        $gender = $_GET['gender'] ?? '';
        $age = $_GET['age'] ?? '';

        // Lọc gói khám theo điều kiện
        if ($gender && $age) {
            $packages = $this->packageModel->getPackagesForPatient($gender, $age);
        } else {
            $packages = $this->packageModel->getAllActive();
        }

        // Lấy danh sách dịch vụ cho mỗi gói
        foreach ($packages as &$package) {
            $package['services'] = $this->packageModel->getServices($package['id']);
        }

        require_once APP_PATH . '/Views/packages/index.php';
    }

    // Chi tiết gói khám (Public)
    public function show($id) {
        $package = $this->packageModel->findById($id);
        
        if (!$package) {
            $_SESSION['error'] = 'Không tìm thấy gói khám!';
            header('Location: ' . APP_URL . '/packages');
            exit;
        }

        // Lấy danh sách dịch vụ theo category
        $services = $this->packageModel->getServices($id);
        
        // Nhóm dịch vụ theo category
        $servicesByCategory = [];
        foreach ($services as $service) {
            $category = $service['service_category'];
            if (!isset($servicesByCategory[$category])) {
                $servicesByCategory[$category] = [];
            }
            $servicesByCategory[$category][] = $service;
        }

        require_once APP_PATH . '/Views/packages/show.php';
    }

    // ==================== ADMIN MANAGEMENT ====================
    
    // Danh sách gói khám (Admin)
    public function adminIndex() {
        Auth::requireAdmin();
        
        $database = new Database();
        $conn = $database->getConnection();
        
        // Lấy tất cả gói khám (bao gồm cả inactive)
        $query = "SELECT hp.*, 
                         COUNT(DISTINCT ps.id) as service_count,
                         COUNT(DISTINCT a.id) as appointment_count
                  FROM health_packages hp
                  LEFT JOIN package_services ps ON hp.id = ps.package_id
                  LEFT JOIN appointments a ON hp.id = a.package_id
                  GROUP BY hp.id
                  ORDER BY hp.created_at DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require_once APP_PATH . '/Views/admin/packages/index.php';
    }

    // Form tạo gói khám mới
    public function create() {
        Auth::requireAdmin();
        require_once APP_PATH . '/Views/admin/packages/create.php';
    }

    // Lưu gói khám mới
    public function store() {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/admin/packages/create');
            exit;
        }

        $this->packageModel->name = $_POST['name'];
        $this->packageModel->description = $_POST['description'] ?? '';
        $this->packageModel->price_male = $_POST['price_male'] ?? null;
        $this->packageModel->price_female = $_POST['price_female'] ?? null;
        $this->packageModel->gender_requirement = $_POST['gender_requirement'];
        $this->packageModel->min_age = $_POST['min_age'];
        $this->packageModel->max_age = $_POST['max_age'];
        $this->packageModel->is_active = isset($_POST['is_active']) ? 1 : 0;

        if ($this->packageModel->create()) {
            $_SESSION['success'] = 'Thêm gói khám thành công!';
            header('Location: ' . APP_URL . '/admin/packages/' . $this->packageModel->id . '/services');
        } else {
            $_SESSION['error'] = 'Thêm gói khám thất bại!';
            header('Location: ' . APP_URL . '/admin/packages/create');
        }
        exit;
    }

    // Form sửa gói khám
    public function edit($id) {
        Auth::requireAdmin();
        
        $package = $this->packageModel->findById($id);
        if (!$package) {
            $_SESSION['error'] = 'Không tìm thấy gói khám!';
            header('Location: ' . APP_URL . '/admin/packages');
            exit;
        }
        
        require_once APP_PATH . '/Views/admin/packages/edit.php';
    }

    // Cập nhật gói khám
    public function update($id) {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/admin/packages/' . $id . '/edit');
            exit;
        }

        $this->packageModel->id = $id;
        $this->packageModel->name = $_POST['name'];
        $this->packageModel->description = $_POST['description'] ?? '';
        $this->packageModel->price_male = $_POST['price_male'] ?? null;
        $this->packageModel->price_female = $_POST['price_female'] ?? null;
        $this->packageModel->gender_requirement = $_POST['gender_requirement'];
        $this->packageModel->min_age = $_POST['min_age'];
        $this->packageModel->max_age = $_POST['max_age'];
        $this->packageModel->is_active = isset($_POST['is_active']) ? 1 : 0;

        if ($this->packageModel->update()) {
            $_SESSION['success'] = 'Cập nhật gói khám thành công!';
            header('Location: ' . APP_URL . '/admin/packages');
        } else {
            $_SESSION['error'] = 'Cập nhật gói khám thất bại!';
            header('Location: ' . APP_URL . '/admin/packages/' . $id . '/edit');
        }
        exit;
    }

    // Xóa gói khám
    public function delete($id) {
        Auth::requireAdmin();

        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "DELETE FROM health_packages WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Xóa gói khám thành công!';
        } else {
            $_SESSION['error'] = 'Xóa gói khám thất bại!';
        }

        header('Location: ' . APP_URL . '/admin/packages');
        exit;
    }

    // ==================== QUẢN LÝ DỊCH VỤ TRONG GÓI ====================
    
    // Quản lý dịch vụ trong gói
    public function manageServices($package_id) {
        Auth::requireAdmin();
        
        $package = $this->packageModel->findById($package_id);
        if (!$package) {
            $_SESSION['error'] = 'Không tìm thấy gói khám!';
            header('Location: ' . APP_URL . '/admin/packages');
            exit;
        }

        $services = $this->packageModel->getServices($package_id);
        
        require_once APP_PATH . '/Views/admin/packages/services.php';
    }

    // Thêm dịch vụ vào gói
    public function addService($package_id) {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/admin/packages/' . $package_id . '/services');
            exit;
        }

        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "INSERT INTO package_services 
                  (package_id, service_name, service_category, service_price, is_required, gender_specific, notes, display_order) 
                  VALUES (:package_id, :service_name, :service_category, :service_price, :is_required, :gender_specific, :notes, :display_order)";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':package_id', $package_id);
        $stmt->bindParam(':service_name', $_POST['service_name']);
        $stmt->bindParam(':service_category', $_POST['service_category']);
        $stmt->bindParam(':service_price', $_POST['service_price']);
        $is_required = isset($_POST['is_required']) ? 1 : 0;
        $stmt->bindParam(':is_required', $is_required);
        $stmt->bindParam(':gender_specific', $_POST['gender_specific']);
        $stmt->bindParam(':notes', $_POST['notes']);
        $stmt->bindParam(':display_order', $_POST['display_order']);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Thêm dịch vụ thành công!';
        } else {
            $_SESSION['error'] = 'Thêm dịch vụ thất bại!';
        }

        header('Location: ' . APP_URL . '/admin/packages/' . $package_id . '/services');
        exit;
    }

    // Xóa dịch vụ khỏi gói
    public function deleteService($package_id, $service_id) {
        Auth::requireAdmin();

        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "DELETE FROM package_services WHERE id = :id AND package_id = :package_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $service_id);
        $stmt->bindParam(':package_id', $package_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Xóa dịch vụ thành công!';
        } else {
            $_SESSION['error'] = 'Xóa dịch vụ thất bại!';
        }

        header('Location: ' . APP_URL . '/admin/packages/' . $package_id . '/services');
        exit;
    }

    // Toggle trạng thái gói khám
    public function toggleStatus($id) {
        Auth::requireAdmin();

        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "UPDATE health_packages SET is_active = NOT is_active WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Cập nhật trạng thái thành công!';
        } else {
            $_SESSION['error'] = 'Cập nhật trạng thái thất bại!';
        }

        header('Location: ' . APP_URL . '/admin/packages');
        exit;
    }

    // Cập nhật giá dịch vụ
    public function updateServicePrice($package_id, $service_id) {
        Auth::requireAdmin();

        $database = new Database();
        $conn = $database->getConnection();

        $query = "UPDATE package_services SET service_price = :price WHERE id = :id AND package_id = :package_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':price', $_POST['service_price']);
        $stmt->bindParam(':id', $service_id);
        $stmt->bindParam(':package_id', $package_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Cập nhật giá dịch vụ thành công!';
        } else {
            $_SESSION['error'] = 'Cập nhật giá dịch vụ thất bại!';
        }

        header('Location: ' . APP_URL . '/admin/packages/' . $package_id . '/services');
        exit;
    }

    // API: Lấy danh sách dịch vụ của gói (JSON)
    public function getServicesJson($package_id) {
        header('Content-Type: application/json');
        
        $services = $this->packageModel->getServices($package_id);
        
        echo json_encode([
            'success' => true,
            'services' => $services
        ]);
        exit;
    }
}
