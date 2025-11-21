<?php

require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Models/Notification.php';

class NotificationController {
    private $notificationModel;

    public function __construct() {
        $this->notificationModel = new Notification();
    }

    // Hiển thị danh sách thông báo cho bệnh nhân
    public function index() {
        Auth::requireLogin();
        if (!Auth::isPatient() && !Auth::isDoctor() && !Auth::isAdmin()) {
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }
        $title = 'Thông báo';

        // Tạo nhắc nhở tự động cho lịch khám sắp tới của chính user này
        try {
            require_once APP_PATH . '/Models/Appointment.php';
            require_once APP_PATH . '/Models/Patient.php';
            require_once APP_PATH . '/Models/PackageAppointment.php';
            $appointmentModel = new Appointment();
            $patientModel = new Patient();
            $pkgAptModel = new PackageAppointment();

            $userId = Auth::id();
            $patient = $patientModel->findByUserId($userId);
            if ($patient) {
                $all = $appointmentModel->getAll();
                $now = time();
                foreach ($all as $apt) {
                    if ((int)$apt['patient_id'] !== (int)$patient['id']) continue;
                    if (!in_array($apt['status'], ['confirmed'])) continue;
                    // Lấy ngày khám: ưu tiên từ appointment; nếu trống và là lịch theo gói, lấy từ package_appointments
                    $aptDateStr = $apt['appointment_date'];
                    if ((empty($aptDateStr) || $aptDateStr==='0000-00-00') && !empty($apt['package_appointment_id'])) {
                        $pkgA = $pkgAptModel->findById($apt['package_appointment_id']);
                        if ($pkgA && !empty($pkgA['appointment_date'])) {
                            $aptDateStr = $pkgA['appointment_date'];
                        }
                    }
                    if (empty($aptDateStr)) continue;
                    $ts = strtotime($aptDateStr . ' ' . ($apt['appointment_time'] ?? '00:00:00'));
                    if ($ts <= 0) continue;

                    // Tạo nhắc cho các lịch trong vòng 36 giờ tới (bao gồm hôm nay và ngày mai)
                    $diffHours = ($ts - $now) / 3600;
                    if ($diffHours >= 0 && $diffHours <= 36) {
                        $aptDate = date('d/m/Y', $ts);
                        $aptTime = !empty($apt['appointment_time']) ? date('H:i', strtotime($apt['appointment_time'])) : null;
                        $isPackage = !empty($apt['package_id']) && !empty($apt['package_appointment_id']);
                        $titleR = 'Nhắc lịch khám sắp tới';
                        if ($isPackage) {
                            $messageR = 'Bạn có lịch hẹn theo gói vào ' . $aptDate . ($aptTime ? (' lúc ' . $aptTime) : '') . '. Vui lòng đến đúng giờ.';
                        } else {
                            $doctorName = $apt['doctor_name'] ?? '';
                            $messageR = 'Bạn có lịch hẹn vào ' . $aptDate . ($aptTime ? (' lúc ' . $aptTime) : '') . ($doctorName ? (' với bác sĩ ' . $doctorName) : '') . '. Vui lòng đến đúng giờ.';
                        }
                        $link = '/appointments/' . $apt['id'];
                        // Tránh tạo trùng lặp nhiều lần
                        $this->notificationModel->createIfNotExists($userId, $titleR, $messageR, $link, 'reminder');
                    }
                }
            }
        } catch (\Throwable $e) {
            // Bỏ qua lỗi để không chặn trang thông báo
        }

        $notifications = $this->notificationModel->getByUser(Auth::id(), 200);
        ob_start();
        require APP_PATH . '/Views/notifications/index.php';
        $content = ob_get_clean();
        require APP_PATH . '/Views/layouts/main.php';
    }

    // Đánh dấu đã đọc
    public function markRead($id) {
        Auth::requireLogin();
        $this->notificationModel->markAsRead((int)$id, Auth::id());
        header('Location: ' . APP_URL . '/notifications');
        exit;
    }

    // API: lấy số lượng chưa đọc
    public function unreadCount() {
        Auth::requireLogin();
        header('Content-Type: application/json');
        echo json_encode(['count' => $this->notificationModel->getUnreadCount(Auth::id())]);
        exit;
    }
}
