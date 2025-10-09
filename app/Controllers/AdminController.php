<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Patient.php';
require_once __DIR__ . '/../Models/Doctor.php';
require_once __DIR__ . '/../Models/Specialization.php';
require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/Validator.php';

class AdminController {
    private $userModel;
    private $patientModel;
    private $doctorModel;
    private $specializationModel;

    public function __construct() {
        $this->userModel = new User();
        $this->patientModel = new Patient();
        $this->doctorModel = new Doctor();
        $this->specializationModel = new Specialization();
    }

    // Dashboard admin
    public function index() {
        Auth::requireAdmin();
        
        $stats = [
            'total_users' => $this->userModel->count(),
            'total_doctors' => $this->doctorModel->count(),
            'total_patients' => $this->patientModel->count(),
            'total_specializations' => count($this->specializationModel->getAll())
        ];
        
        require_once APP_PATH . '/Views/admin/index.php';
    }

    // ==================== QUẢN LÝ CHUYÊN KHOA ====================
    
    // Danh sách chuyên khoa
    public function specializations() {
        Auth::requireAdmin();
        $specializations = $this->specializationModel->getAll();
        require_once APP_PATH . '/Views/admin/specializations/index.php';
    }

    // Form tạo chuyên khoa
    public function createSpecialization() {
        Auth::requireAdmin();
        require_once APP_PATH . '/Views/admin/specializations/create.php';
    }

    // Lưu chuyên khoa mới
    public function storeSpecialization() {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/admin/specializations/create');
            exit;
        }

        $validator = new Validator($_POST);
        $validator->required('name', 'Tên chuyên khoa không được để trống')
                  ->required('min_age', 'Độ tuổi tối thiểu không được để trống')
                  ->required('max_age', 'Độ tuổi tối đa không được để trống')
                  ->required('gender_requirement', 'Yêu cầu giới tính không được để trống');

        if ($validator->fails()) {
            $_SESSION['error'] = $validator->firstError();
            $_SESSION['old'] = $_POST;
            header('Location: ' . APP_URL . '/admin/specializations/create');
            exit;
        }

        $query = "INSERT INTO specializations (name, description, min_age, max_age, gender_requirement) 
                  VALUES (:name, :description, :min_age, :max_age, :gender_requirement)";
        
        $stmt = $this->specializationModel->conn->prepare($query);
        $stmt->bindParam(':name', $_POST['name']);
        $stmt->bindParam(':description', $_POST['description']);
        $stmt->bindParam(':min_age', $_POST['min_age']);
        $stmt->bindParam(':max_age', $_POST['max_age']);
        $stmt->bindParam(':gender_requirement', $_POST['gender_requirement']);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Thêm chuyên khoa thành công!';
            header('Location: ' . APP_URL . '/admin/specializations');
        } else {
            $_SESSION['error'] = 'Thêm chuyên khoa thất bại!';
            header('Location: ' . APP_URL . '/admin/specializations/create');
        }
        exit;
    }

    // Xóa chuyên khoa
    public function deleteSpecialization($id) {
        Auth::requireAdmin();

        $query = "DELETE FROM specializations WHERE id = :id";
        $stmt = $this->specializationModel->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Xóa chuyên khoa thành công!';
        } else {
            $_SESSION['error'] = 'Xóa chuyên khoa thất bại!';
        }

        header('Location: ' . APP_URL . '/admin/specializations');
        exit;
    }

    // ==================== QUẢN LÝ BÁC SĨ ====================
    
    // Danh sách bác sĩ
    public function doctors() {
        Auth::requireAdmin();
        $doctors = $this->doctorModel->getAll();
        require_once APP_PATH . '/Views/admin/doctors/index.php';
    }

    // Form tạo bác sĩ
    public function createDoctor() {
        Auth::requireAdmin();
        $specializations = $this->specializationModel->getAll();
        require_once APP_PATH . '/Views/admin/doctors/create.php';
    }

    // Lưu bác sĩ mới
    public function storeDoctor() {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/admin/doctors/create');
            exit;
        }

        $validator = new Validator($_POST);
        $validator->required('username', 'Tên đăng nhập không được để trống')
                  ->required('email', 'Email không được để trống')
                  ->email('email', 'Email không hợp lệ')
                  ->required('password', 'Mật khẩu không được để trống')
                  ->min('password', 6, 'Mật khẩu phải có ít nhất 6 ký tự')
                  ->required('full_name', 'Họ tên không được để trống')
                  ->required('phone', 'Số điện thoại không được để trống')
                  ->required('specialization', 'Chuyên khoa không được để trống')
                  ->required('license_number', 'Số giấy phép hành nghề không được để trống');

        if ($validator->fails()) {
            $_SESSION['error'] = $validator->firstError();
            $_SESSION['old'] = $_POST;
            header('Location: ' . APP_URL . '/admin/doctors/create');
            exit;
        }

        // Tạo user
        $this->userModel->username = $_POST['username'];
        $this->userModel->email = $_POST['email'];
        $this->userModel->password = $_POST['password']; // Model sẽ tự hash
        $this->userModel->full_name = $_POST['full_name'];
        $this->userModel->phone = $_POST['phone'];
        $this->userModel->role = 'doctor';

        if ($this->userModel->create()) {
            $user_id = $this->userModel->id;
            
            // Tạo doctor
            $doctor_code = 'DOC' . str_pad($user_id, 4, '0', STR_PAD_LEFT);
            
            $this->doctorModel->user_id = $user_id;
            $this->doctorModel->doctor_code = $doctor_code;
            $this->doctorModel->specialization_id = $_POST['specialization'];
            $this->doctorModel->license_number = $_POST['license_number'];
            $this->doctorModel->qualification = $_POST['qualification'] ?? '';
            $this->doctorModel->experience_years = $_POST['experience_years'] ?? 0;
            $this->doctorModel->consultation_fee = $_POST['consultation_fee'] ?? 0;
            $this->doctorModel->available_days = $_POST['available_days'] ?? '';
            $this->doctorModel->available_hours = $_POST['available_hours'] ?? '';

            if ($this->doctorModel->create()) {
                $_SESSION['success'] = 'Thêm bác sĩ thành công!';
                header('Location: ' . APP_URL . '/admin/doctors');
            } else {
                $_SESSION['error'] = 'Thêm thông tin bác sĩ thất bại!';
                header('Location: ' . APP_URL . '/admin/doctors/create');
            }
        } else {
            $_SESSION['error'] = 'Tạo tài khoản thất bại! Username hoặc email đã tồn tại.';
            $_SESSION['old'] = $_POST;
            header('Location: ' . APP_URL . '/admin/doctors/create');
        }
        exit;
    }

    // Form sửa bác sĩ
    public function editDoctor($id) {
        Auth::requireAdmin();
        
        $doctor = $this->doctorModel->findById($id);
        if (!$doctor) {
            $_SESSION['error'] = 'Không tìm thấy bác sĩ!';
            header('Location: ' . APP_URL . '/admin/doctors');
            exit;
        }
        
        $specializations = $this->specializationModel->getAll();
        require_once APP_PATH . '/Views/admin/doctors/edit.php';
    }

    // Cập nhật bác sĩ
    public function updateDoctor($id) {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/admin/doctors/' . $id . '/edit');
            exit;
        }

        $doctor = $this->doctorModel->findById($id);
        if (!$doctor) {
            $_SESSION['error'] = 'Không tìm thấy bác sĩ!';
            header('Location: ' . APP_URL . '/admin/doctors');
            exit;
        }

        // Cập nhật user
        $user = $this->userModel->findById($doctor['user_id']);
        $this->userModel->id = $user['id'];
        $this->userModel->full_name = $_POST['full_name'];
        $this->userModel->email = $_POST['email'];
        $this->userModel->phone = $_POST['phone'];
        $this->userModel->update();

        // Cập nhật doctor
        $this->doctorModel->id = $id;
        $this->doctorModel->specialization = $_POST['specialization'];
        $this->doctorModel->license_number = $_POST['license_number'];
        $this->doctorModel->qualification = $_POST['qualification'];
        $this->doctorModel->experience_years = $_POST['experience_years'];
        $this->doctorModel->consultation_fee = $_POST['consultation_fee'];
        $this->doctorModel->available_days = $_POST['available_days'];
        $this->doctorModel->available_hours = $_POST['available_hours'];

        if ($this->doctorModel->update()) {
            $_SESSION['success'] = 'Cập nhật bác sĩ thành công!';
        } else {
            $_SESSION['error'] = 'Cập nhật bác sĩ thất bại!';
        }

        header('Location: ' . APP_URL . '/admin/doctors');
        exit;
    }

    // Xóa bác sĩ
    public function deleteDoctor($id) {
        Auth::requireAdmin();

        $doctor = $this->doctorModel->findById($id);
        if ($doctor) {
            // Xóa user sẽ cascade xóa doctor
            $query = "DELETE FROM users WHERE id = :id";
            $stmt = $this->userModel->conn->prepare($query);
            $stmt->bindParam(':id', $doctor['user_id']);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Xóa bác sĩ thành công!';
            } else {
                $_SESSION['error'] = 'Xóa bác sĩ thất bại!';
            }
        }

        header('Location: ' . APP_URL . '/admin/doctors');
        exit;
    }

    // ==================== QUẢN LÝ USERS ====================
    
    public function users() {
        Auth::requireAdmin();
        
        $users = $this->userModel->getAll();
        
        require_once APP_PATH . '/Views/admin/users/index.php';
    }
}
