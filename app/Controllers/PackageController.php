<?php

require_once __DIR__ . '/../Models/HealthPackage.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class PackageController {
    private $packageModel;

    public function __construct() {
        $this->packageModel = new HealthPackage();
    }

    // Cập nhật whitelist thuốc cho một dịch vụ trong gói
    public function updateServiceMedicines($package_id, $service_id) {
        Auth::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/admin/packages/' . $package_id . '/services');
            exit;
        }

        $medicineIds = $_POST['medicine_ids'] ?? [];
        if (!is_array($medicineIds)) { $medicineIds = []; }

        $database = new Database();
        $conn = $database->getConnection();
        try {
            $conn->beginTransaction();
            $del = $conn->prepare('DELETE FROM service_allowed_medicines WHERE service_id = ?');
            $del->execute([(int)$service_id]);
            if (!empty($medicineIds)) {
                $ins = $conn->prepare('INSERT INTO service_allowed_medicines(service_id, medicine_id, created_at) VALUES(?, ?, NOW())');
                foreach ($medicineIds as $mid) {
                    $ins->execute([(int)$service_id, (int)$mid]);
                }
            }
            $conn->commit();
            $_SESSION['success'] = 'Cập nhật whitelist thuốc thành công!';
        } catch (\Throwable $e) {
            if ($conn->inTransaction()) { $conn->rollBack(); }
            $_SESSION['error'] = 'Lưu whitelist thuốc thất bại!';
        }

        header('Location: ' . APP_URL . '/admin/packages/' . $package_id . '/services');
        exit;
    }

    // Cập nhật thời lượng dịch vụ (phút)
    public function updateServiceDuration($package_id, $service_id) {
        Auth::requireAdmin();

        $database = new Database();
        $conn = $database->getConnection();

        $duration = (int)($_POST['duration_minutes'] ?? 30);
        if ($duration <= 0) { $duration = 30; }

        $query = "UPDATE package_services SET duration_minutes = :duration WHERE id = :id AND package_id = :package_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':duration', $duration, PDO::PARAM_INT);
        $stmt->bindParam(':id', $service_id);
        $stmt->bindParam(':package_id', $package_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Cập nhật thời lượng dịch vụ thành công!';
        } else {
            $_SESSION['error'] = 'Cập nhật thời lượng dịch vụ thất bại!';
        }

        header('Location: ' . APP_URL . '/admin/packages/' . $package_id . '/services');
        exit;
    }

    // Cập nhật danh sách bác sĩ được phép thực hiện một dịch vụ trong gói
    public function updateServiceDoctors($package_id, $service_id) {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/admin/packages/' . $package_id . '/services');
            exit;
        }

        $doctorIds = $_POST['doctor_ids'] ?? [];
        if (!is_array($doctorIds)) { $doctorIds = []; }

        $database = new Database();
        $conn = $database->getConnection();
        try {
            $conn->beginTransaction();
            $del = $conn->prepare('DELETE FROM package_service_doctors WHERE service_id = ?');
            $del->execute([(int)$service_id]);
            if (!empty($doctorIds)) {
                $ins = $conn->prepare('INSERT INTO package_service_doctors(service_id, doctor_id, created_at) VALUES(?, ?, NOW())');
                foreach ($doctorIds as $did) {
                    $ins->execute([(int)$service_id, (int)$did]);
                }
            }
            $conn->commit();
            $_SESSION['success'] = 'Cập nhật bác sĩ cho dịch vụ thành công!';
        } catch (\Throwable $e) {
            if ($conn->inTransaction()) { $conn->rollBack(); }
            $_SESSION['error'] = 'Lưu danh sách bác sĩ thất bại!';
        }

        header('Location: ' . APP_URL . '/admin/packages/' . $package_id . '/services');
        exit;
    }

    // ==================== PUBLIC VIEWS ====================
    
    // Danh sách gói khám (Public)
    public function index() {
        // Kiểm tra user đang login
        $isLoggedIn = isset($_SESSION['user_id']);
        $userRole = $_SESSION['role'] ?? null;
        
        // Admin/Doctor thấy TẤT CẢ gói
        if ($isLoggedIn && in_array($userRole, ['admin', 'doctor', 'receptionist'])) {
            $packages = $this->packageModel->getAllActive();
        } 
        // Bệnh nhân thấy gói phù hợp với giới tính
        else if ($isLoggedIn && $userRole === 'patient') {
            // Lấy thông tin bệnh nhân
            require_once APP_PATH . '/Models/Patient.php';
            $patientModel = new Patient();
            $patient = $patientModel->findByUserId($_SESSION['user_id']);
            
            if ($patient) {
                $gender = $patient['gender'];
                $age = date('Y') - date('Y', strtotime($patient['date_of_birth']));
                $packages = $this->packageModel->getPackagesForPatient($gender, $age);
            } else {
                $packages = $this->packageModel->getAllActive();
            }
        }
        // Guest: Lọc theo query params hoặc hiện tất cả
        else {
            $gender = $_GET['gender'] ?? '';
            $age = $_GET['age'] ?? '';
            
            if ($gender && $age) {
                $packages = $this->packageModel->getPackagesForPatient($gender, $age);
            } else {
                $packages = $this->packageModel->getAllActive();
            }
        }

        // Lấy danh sách dịch vụ cho mỗi gói
        foreach ($packages as &$package) {
            $package['services'] = $this->packageModel->getServices($package['id']);
        }
        // Quan trọng: giải phóng tham chiếu để tránh leak tham chiếu khi render View
        unset($package);

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
                         COUNT(DISTINCT a.id) as appointment_count,
                         COALESCE(
                            (SELECT SUM(service_price) 
                             FROM package_services 
                             WHERE package_id = hp.id), 0
                         ) as total_price
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

        // Tải danh sách bác sĩ và mapping bác sĩ theo từng dịch vụ để hiển thị chọn lọc
        $database = new Database();
        $conn = $database->getConnection();
        // Tất cả bác sĩ kèm chuyên khoa và tên hiển thị
        $doctors = [];
        try {
            $st = $conn->query("SELECT d.id, du.full_name AS doctor_name, s.name AS spec FROM doctors d LEFT JOIN users du ON du.id = d.user_id LEFT JOIN specializations s ON s.id = d.specialization_id ORDER BY du.full_name ASC");
            $doctors = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) { $doctors = []; }
        // Mapping: service_id => [doctor_id,...]
        $allowedByService = [];
        try {
            $st2 = $conn->prepare('SELECT service_id, doctor_id FROM package_service_doctors WHERE service_id IN (SELECT id FROM package_services WHERE package_id = ?)');
            $st2->execute([(int)$package_id]);
            foreach ($st2->fetchAll(PDO::FETCH_ASSOC) ?: [] as $row) {
                $sid = (int)$row['service_id'];
                if (!isset($allowedByService[$sid])) $allowedByService[$sid] = [];
                $allowedByService[$sid][] = (int)$row['doctor_id'];
            }
        } catch (\Throwable $e) { /* ignore */ }

        // Tải danh sách thuốc và whitelist thuốc theo dịch vụ
        $medicines = [];
        try {
            $st3 = $conn->query("SELECT id, name, strength, dosage_form AS form FROM medicines ORDER BY name ASC");
            $medicines = $st3->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) { $medicines = []; }
        $medWhitelistByService = [];
        try {
            $st4 = $conn->prepare('SELECT service_id, medicine_id FROM service_allowed_medicines WHERE service_id IN (SELECT id FROM package_services WHERE package_id = ?)');
            $st4->execute([(int)$package_id]);
            foreach ($st4->fetchAll(PDO::FETCH_ASSOC) ?: [] as $row) {
                $sid = (int)$row['service_id'];
                if (!isset($medWhitelistByService[$sid])) $medWhitelistByService[$sid] = [];
                $medWhitelistByService[$sid][] = (int)$row['medicine_id'];
            }
        } catch (\Throwable $e) { /* ignore */ }

        require_once APP_PATH . '/Views/admin/packages/services.php';
    }

    // Thêm dịch vụ vào gói
    public function addService($package_id) {
        // ...

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/admin/packages/' . $package_id . '/services');
            exit;
        }

        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "INSERT INTO package_services 
                  (package_id, service_name, service_category, service_price, duration_minutes, is_required, gender_specific, notes, display_order) 
                  VALUES (:package_id, :service_name, :service_category, :service_price, :duration_minutes, :is_required, :gender_specific, :notes, :display_order)";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':package_id', $package_id);
        $stmt->bindParam(':service_name', $_POST['service_name']);
        $stmt->bindParam(':service_category', $_POST['service_category']);
        $stmt->bindParam(':service_price', $_POST['service_price']);
        $duration = (int)($_POST['duration_minutes'] ?? 30);
        if ($duration <= 0) { $duration = 30; }
        $stmt->bindParam(':duration_minutes', $duration, PDO::PARAM_INT);
        $is_required = isset($_POST['is_required']) ? 1 : 0;
        $stmt->bindParam(':is_required', $is_required);
        $stmt->bindParam(':gender_specific', $_POST['gender_specific']);
        $stmt->bindParam(':notes', $_POST['notes']);
        $stmt->bindParam(':display_order', $_POST['display_order']);

        if ($stmt->execute()) {
            // Ghi nhận danh sách bác sĩ được chọn cho dịch vụ mới
            $newServiceId = (int)$conn->lastInsertId();
            $doctorIds = $_POST['doctor_ids'] ?? [];
            if (is_array($doctorIds) && !empty($doctorIds) && $newServiceId > 0) {
                try {
                    $ins = $conn->prepare('INSERT IGNORE INTO package_service_doctors(service_id, doctor_id, created_at) VALUES(?, ?, NOW())');
                    foreach ($doctorIds as $did) {
                        $ins->execute([$newServiceId, (int)$did]);
                    }
                } catch (\Throwable $e) { /* ignore mapping errors */ }
            }
            $_SESSION['success'] = 'Thêm dịch vụ thành công!';
        } else {
            $_SESSION['error'] = 'Thêm dịch vụ thất bại!';
        }

        header('Location: ' . APP_URL . '/admin/packages/' . $package_id . '/services');
        exit;
    }

    // Toggle dịch vụ bắt buộc trong gói
    public function toggleServiceRequired($package_id, $service_id) {
        Auth::requireAdmin();

        $database = new Database();
        $conn = $database->getConnection();

        // Đảo trạng thái is_required
        $query = "UPDATE package_services SET is_required = NOT is_required WHERE id = :id AND package_id = :package_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $service_id);
        $stmt->bindParam(':package_id', $package_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Cập nhật bắt buộc thành công!';
        } else {
            $_SESSION['error'] = 'Cập nhật bắt buộc thất bại!';
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
