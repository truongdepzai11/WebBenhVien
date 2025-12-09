<?php

require_once __DIR__ . '/../Models/PackageAppointment.php';
require_once __DIR__ . '/../Models/Patient.php';
require_once __DIR__ . '/../Models/HealthPackage.php';
require_once __DIR__ . '/../Models/Appointment.php';
require_once __DIR__ . '/../Models/Doctor.php';
require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/Mailer.php';

class PackageAppointmentController {
    private $packageAppointmentModel;
    private $patientModel;
    private $packageModel;
    private $appointmentModel;
    private $doctorModel;

    public function __construct() {
        $this->packageAppointmentModel = new PackageAppointment();
        $this->patientModel = new Patient();
        $this->packageModel = new HealthPackage();
        $this->appointmentModel = new Appointment();
        $this->doctorModel = new Doctor();
    }

    private function getPatientUserIdByPackageAppointmentId($packageAppointmentId) {
        try {
            $db = new Database(); $conn = $db->getConnection();
            $st = $conn->prepare('SELECT u.id FROM package_appointments pa JOIN patients p ON p.id = pa.patient_id JOIN users u ON u.id = p.user_id WHERE pa.id = ?');
            $st->execute([(int)$packageAppointmentId]);
            $uid = $st->fetchColumn();
            return $uid ? (int)$uid : null;
        } catch (\Throwable $e) { return null; }
    }

    private function notify($userId, $title, $message, $link = null, $type = 'system') {
        try {
            $db = new Database(); $conn = $db->getConnection();
            $st = $conn->prepare('INSERT INTO notifications (user_id, title, message, link, type, is_read, created_at) VALUES (?,?,?,?,?,0, NOW())');
            $st->execute([(int)$userId, $title, $message, $link, $type]);
        } catch (\Throwable $e) { /* ignore */ }

        try {
            $db = new Database(); $conn = $db->getConnection();
            $u = $conn->prepare('SELECT email, full_name FROM users WHERE id = ?');
            $u->execute([(int)$userId]);
            $row = $u->fetch(\PDO::FETCH_ASSOC);
            if ($row && !empty($row['email'])) {
                $mailer = new Mailer();
                $url = $link ? (APP_URL . $link) : APP_URL;
                $body = '<p>' . htmlspecialchars($message) . '</p>'
                      . '<p><a href="' . htmlspecialchars($url) . '">Xem chi tiết</a></p>';
                $mailer->send($row['email'], $title, $body);
            }
        } catch (\Throwable $e) { /* ignore email errors */ }
    }

    // Danh sách đăng ký gói khám
    public function index() {
        Auth::requireLogin();

        $packageAppointments = [];

        if (Auth::isPatient()) {
            // Bệnh nhân chỉ xem của mình
            $patient = $this->patientModel->findByUserId(Auth::id());
            $packageAppointments = $this->packageAppointmentModel->getByPatientId($patient['id']);
        } else {
            // Admin/Receptionist xem tất cả
            $packageAppointments = $this->packageAppointmentModel->getAll();
        }

        // Bổ sung thông tin số lượng dịch vụ đã phân công cho mỗi gói
        $packageAppointments = array_map(function($pa) {
            // Đếm số dịch vụ đã phân công
            $pa['assigned_count'] = $this->appointmentModel->countAssignedByPackageAppointmentId($pa['id']);
            // Lấy tổng số dịch vụ trong gói
            $services = $this->packageModel->getPackageServices($pa['package_id']);
            $pa['total_services'] = is_array($services) ? count($services) : 0;
            return $pa;
        }, $packageAppointments);

        require_once APP_PATH . '/Views/package_appointments/index.php';
    }

    // Chi tiết đăng ký gói khám
    public function show($id) {
        Auth::requireLogin();

        $packageAppointment = $this->packageAppointmentModel->findById($id);

        if (!$packageAppointment) {
            $_SESSION['error'] = 'Không tìm thấy đăng ký gói khám';
            header('Location: ' . APP_URL . '/package-appointments');
            exit;
        }

        // Kiểm tra quyền: Bệnh nhân chỉ xem của mình
        if (Auth::isPatient()) {
            $patient = $this->patientModel->findByUserId(Auth::id());
            if ($packageAppointment['patient_id'] != $patient['id']) {
                $_SESSION['error'] = 'Bạn không có quyền xem đăng ký này';
                header('Location: ' . APP_URL . '/package-appointments');
                exit;
            }
        }

        // Chuẩn bị dữ liệu hóa đơn: tìm lịch tổng hợp gói và hóa đơn (nếu có)
        $summaryAppointment = $this->appointmentModel->getSummaryByPackageAppointmentId($id);
        $summaryInvoice = null;
        if ($summaryAppointment) {
            require_once APP_PATH . '/Models/Invoice.php';
            $invoiceModel = new Invoice();
            $summaryInvoice = $invoiceModel->findByAppointmentId($summaryAppointment['id']);
        }

        // Lấy danh sách dịch vụ đã chọn (ưu tiên), nếu chưa có thì lấy toàn bộ dịch vụ cấu hình của gói
        if ($summaryAppointment) {
            $packageServices = $this->packageModel->getSelectedServicesByAppointmentId($summaryAppointment['id']);
        }
        if (empty($packageServices)) {
            $packageServices = $this->packageModel->getPackageServices($packageAppointment['package_id']);
        }

        $assignedCount = $this->appointmentModel->countAssignedByPackageAppointmentId($id);
        $serviceAppointments = $this->appointmentModel->getByPackageAppointmentId($id);
        if (!is_array($serviceAppointments)) {
            $serviceAppointments = [];
        }
        $appointments = $serviceAppointments;
        $completionStats = $this->appointmentModel->getChildCompletionStats($id);

        // Chỉ tính các lịch có bác sĩ (bất kể appointment_type) → tránh bỏ sót dữ liệu cũ
        $serviceAppointments = array_values(array_filter($serviceAppointments, function($a){
            return !empty($a['doctor_id']);
        }));
        // Đếm theo dịch vụ duy nhất dựa trên reason (tên dịch vụ)
        $uniqueReasons = [];
        foreach ($serviceAppointments as $apt) {
            $key = strtolower(trim($apt['reason'] ?? ''));
            if ($key !== '' && !in_array($key, $uniqueReasons, true)) {
                $uniqueReasons[] = $key;
            }
        }
        $assignedCount = count($uniqueReasons);

        // Lấy danh sách bác sĩ được phép theo từng dịch vụ để hiển thị đúng dropdown
        $doctorsByService = [];
        try {
            $db = new Database(); $conn = $db->getConnection();
            $svcIds = array_map(function($s){ return (int)$s['id']; }, $packageServices);
            if (!empty($svcIds)) {
                $in = implode(',', array_fill(0, count($svcIds), '?'));
                $st = $conn->prepare("SELECT psd.service_id, d.id, u.full_name AS doctor_name, s.name AS spec
                                       FROM package_service_doctors psd
                                       JOIN doctors d ON d.id = psd.doctor_id
                                       LEFT JOIN users u ON u.id = d.user_id
                                       LEFT JOIN specializations s ON s.id = d.specialization_id
                                       WHERE psd.service_id IN ($in)");
                $st->execute($svcIds);
                foreach ($st->fetchAll(PDO::FETCH_ASSOC) ?: [] as $row) {
                    $sid = (int)$row['service_id'];
                    if (!isset($doctorsByService[$sid])) $doctorsByService[$sid] = [];
                    $doctorsByService[$sid][] = [
                        'id' => (int)$row['id'],
                        'full_name' => $row['doctor_name'] ?? '',
                        'specialization' => $row['spec'] ?? ''
                    ];
                }
            }
        } catch (\Throwable $e) { $doctorsByService = []; }

        // Tải trạng thái kết quả theo từng dịch vụ (APS) + metrics để điều phối xem/xét duyệt
        $apsByServiceId = [];
        $metricsByServiceId = [];
        if ($summaryAppointment) {
            $db = new Database(); $conn = $db->getConnection();
            // APS cho lịch tổng hợp
            $st = $conn->prepare('SELECT aps.*, ps.service_name FROM appointment_package_services aps JOIN package_services ps ON aps.service_id = ps.id WHERE aps.appointment_id = ? ORDER BY ps.display_order, aps.id');
            $st->execute([(int)$summaryAppointment['id']]);
            $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
            foreach ($rows as $r) { $apsByServiceId[(int)$r['service_id']] = $r; }

            // Tất cả metrics của gói này theo service
            $st2 = $conn->prepare('SELECT service_id, metric_name, result_value, result_status, reference_range, notes FROM package_test_results WHERE appointment_id = ? ORDER BY id');
            $st2->execute([(int)$summaryAppointment['id']]);
            foreach ($st2->fetchAll(PDO::FETCH_ASSOC) ?: [] as $m) {
                $sid = (int)$m['service_id'];
                if (!isset($metricsByServiceId[$sid])) $metricsByServiceId[$sid] = [];
                $metricsByServiceId[$sid][] = $m;
            }

            // Cập nhật final_status hiển thị (không ghi DB ở đây)
            $final = $this->calculateFinalStatus($rows);
            $packageAppointment['final_status'] = $final;
        }

        // (đã chuẩn bị $summaryAppointment, $summaryInvoice, $apsByServiceId, $metricsByServiceId ở trên)

        require_once APP_PATH . '/Views/package_appointments/show.php';
    }

    private function calculateFinalStatus($apsRows) {
        if (empty($apsRows)) return 'in_progress';
        $hasReturned = false; $allApproved = true; $anySubmitted = false;
        foreach ($apsRows as $r) {
            $st = $r['result_state'] ?? 'draft';
            if ($st === 'returned') $hasReturned = true;
            if ($st !== 'approved') $allApproved = false;
            if ($st === 'submitted') $anySubmitted = true;
        }
        if ($hasReturned) return 'returned';
        if ($allApproved) return 'approved';
        if ($anySubmitted) return 'awaiting_review';
        return 'in_progress';
    }

    // POST: duyệt hoặc trả về kết quả của 1 dịch vụ
    public function reviewService($packageAppointmentId) {
        Auth::requireLogin();
        if (!Auth::isAdmin() && !Auth::isDoctor()) {
            $_SESSION['error'] = 'Không có quyền duyệt kết quả';
            header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId); exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId); exit;
        }

        $action = $_POST['action'] ?? '';
        $serviceId = (int)($_POST['service_id'] ?? 0);
        $reviewNote = trim($_POST['review_note'] ?? '');

        // Tìm summary appointment id
        $summary = $this->appointmentModel->getSummaryByPackageAppointmentId($packageAppointmentId);
        if (!$summary || !$serviceId) {
            $_SESSION['error'] = 'Thiếu dữ liệu duyệt kết quả.';
            header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId); exit;
        }

        $db = new Database(); $conn = $db->getConnection();
        // Cập nhật APS
        $state = $action === 'approve' ? 'approved' : ($action === 'return' ? 'returned' : null);
        if (!$state) { $_SESSION['error'] = 'Hành động không hợp lệ.'; header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId); exit; }

        $upd = $conn->prepare('UPDATE appointment_package_services SET result_state = :st, review_note = :note WHERE appointment_id = :aid AND service_id = :sid');
        $upd->execute([':st'=>$state, ':note'=>$reviewNote, ':aid'=>(int)$summary['id'], ':sid'=>$serviceId]);

        // Tính lại final_status và lưu vào package_appointments
        $st = $conn->prepare('SELECT result_state FROM appointment_package_services WHERE appointment_id = ?');
        $st->execute([(int)$summary['id']]);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $final = $this->calculateFinalStatus($rows);

        $paUpdSql = 'UPDATE package_appointments SET final_status = :fs';
        $params = [':fs'=>$final, ':id'=>$packageAppointmentId];
        if ($final === 'approved') {
            $paUpdSql .= ', approved_by = :ab, approved_at = NOW()';
            $params[':ab'] = Auth::id();
        }
        $paUpdSql .= ' WHERE id = :id';
        $paUpd = $conn->prepare($paUpdSql);
        $paUpd->execute($params);

        // Gửi thông báo cho bệnh nhân
        try {
            $patientUserId = $this->getPatientUserIdByPackageAppointmentId($packageAppointmentId);
            if ($patientUserId) {
                if ($state === 'approved') {
                    $this->notify($patientUserId, 'Kết quả dịch vụ đã được duyệt', 'Dịch vụ trong gói của bạn đã được duyệt.', '/my-results/package/' . $packageAppointmentId, 'system');
                } elseif ($state === 'returned') {
                    $this->notify($patientUserId, 'Kết quả bị trả về', 'Một dịch vụ trong gói của bạn cần bổ sung. Vui lòng chờ bác sĩ cập nhật.', '/my-results/package/' . $packageAppointmentId, 'system');
                }
                if ($final === 'approved') {
                    $this->notify($patientUserId, 'Kết quả gói đã sẵn sàng', 'Tất cả dịch vụ đã duyệt. Bạn có thể xem và tải PDF.', '/my-results/package/' . $packageAppointmentId, 'system');
                }
            }
        } catch (\Throwable $e) { /* ignore notify errors */ }

        $_SESSION['success'] = $state === 'approved' ? 'Đã duyệt dịch vụ.' : 'Đã trả về dịch vụ.';
        header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId); exit;
    }

    // Xuất PDF tổng hợp kết quả
    public function exportPdf($packageAppointmentId) {
        Auth::requireLogin();

        $pa = $this->packageAppointmentModel->findById($packageAppointmentId);
        $summary = $this->appointmentModel->getSummaryByPackageAppointmentId($packageAppointmentId);
        if (!$pa || !$summary) { $_SESSION['error']='Thiếu dữ liệu gói.'; header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId); exit; }

        // Quyền: Admin/Doctor luôn được phép. Bệnh nhân chỉ được phép nếu là chủ sở hữu và gói đã approved.
        $isOwnerPatient = false;
        if (!Auth::isAdmin() && !Auth::isDoctor()) {
            $patient = $this->patientModel->findByUserId(Auth::id());
            $isOwnerPatient = $patient && (int)$patient['id'] === (int)$pa['patient_id'];
            if (!$isOwnerPatient) {
                $_SESSION['error'] = 'Không có quyền xuất PDF';
                header('Location: ' . APP_URL . '/'); exit;
            }
        }

        $db = new Database(); $conn = $db->getConnection();
        // Kiểm tra đã approved hết chưa
        $aps = $conn->prepare('SELECT aps.*, ps.service_name FROM appointment_package_services aps JOIN package_services ps ON aps.service_id=ps.id WHERE aps.appointment_id = ?');
        $aps->execute([(int)$summary['id']]);
        $apsRows = $aps->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $final = $this->calculateFinalStatus($apsRows);
        if ($final !== 'approved') {
            $_SESSION['error'] = 'Chỉ xuất PDF khi tất cả dịch vụ đã được duyệt.';
            // Nếu yêu cầu từ trang bệnh nhân, quay về trang bệnh nhân
            if ($isOwnerPatient) { header('Location: ' . APP_URL . '/my-results/package/' . $packageAppointmentId); exit; }
            header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId); exit;
        }

        // Load metrics
        $metricsStmt = $conn->prepare('SELECT service_id, metric_name, result_value, result_status, reference_range, notes FROM package_test_results WHERE appointment_id = ? ORDER BY service_id, id');
        $metricsStmt->execute([(int)$summary['id']]);
        $metrics = $metricsStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $byService = [];
        foreach ($metrics as $m) { $byService[(int)$m['service_id']][] = $m; }

        // Build HTML
        $styles = '<style>
            body{ font-family: DejaVu Sans, sans-serif; font-size:12px; }
            h2{ margin: 0 0 8px 0; }
            h3{ margin: 14px 0 6px 0; }
            table{ border-collapse:collapse; width:100%; }
            th, td{ border:1px solid #444; padding:6px; vertical-align: top; }
            th{ background:#f2f2f2; }
            .meta td{ border:none; padding:4px 6px; }
        </style>';
        $html = $styles;
        $html .= '<h2 style="text-align:center">Kết quả gói khám: '.htmlspecialchars($pa['package_name']).'</h2>';
        $html .= '<table class="meta" width="100%">'
              .  '<tr><td><strong>Bệnh nhân:</strong> ' . htmlspecialchars($pa['patient_name'] ?? '') . ' (' . htmlspecialchars($pa['patient_code'] ?? '') . ')</td><td><strong>Ngày khám:</strong> ' . (!empty($pa['appointment_date']) ? htmlspecialchars(date('d/m/Y', strtotime($pa['appointment_date']))) : '') . '</td></tr>'
              .  '<tr><td><strong>SĐT:</strong> ' . htmlspecialchars($pa['patient_phone'] ?? '') . '</td><td><strong>Email:</strong> ' . htmlspecialchars($pa['patient_email'] ?? '') . '</td></tr>'
              .  '</table>';
        foreach ($apsRows as $row) {
            $html .= '<h3>'.htmlspecialchars($row['service_name']).'</h3>';
            // Doctor/state/date header per service: derive from service appointment by reason
            $hdr = [];
            try {
                $aptQ = $conn->prepare('SELECT a.appointment_date, a.appointment_time, du.full_name AS doctor_name, s.name AS spec
                                         FROM appointments a
                                         LEFT JOIN doctors d ON d.id = a.doctor_id
                                         LEFT JOIN users du ON du.id = d.user_id
                                         LEFT JOIN specializations s ON s.id = d.specialization_id
                                         WHERE a.package_appointment_id = ? AND LOWER(TRIM(a.reason)) = LOWER(TRIM(?)) AND a.doctor_id IS NOT NULL
                                         ORDER BY a.appointment_date, a.appointment_time LIMIT 1');
                $aptQ->execute([(int)$packageAppointmentId, (string)$row['service_name']]);
                if ($aptRow = $aptQ->fetch(PDO::FETCH_ASSOC)) {
                    if (!empty($aptRow['doctor_name'])) $hdr[] = '<strong>Bác sĩ:</strong> '.htmlspecialchars($aptRow['doctor_name']);
                    if (!empty($aptRow['spec'])) $hdr[] = '<strong>Chuyên khoa:</strong> '.htmlspecialchars($aptRow['spec']);
                    $dateStr = '';
                    if (!empty($aptRow['appointment_date'])) {
                        $dateStr = date('d/m/Y', strtotime($aptRow['appointment_date']));
                        if (!empty($aptRow['appointment_time'])) { $dateStr .= ' - ' . date('H:i', strtotime($aptRow['appointment_time'])); }
                        $hdr[] = '<strong>Ngày khám dịch vụ:</strong> ' . htmlspecialchars($dateStr);
                    }
                }
            } catch(\Throwable $e) { /* ignore */ }
            $stLbl = $row['result_state'] ?? '';
            if ($stLbl !== '') $hdr[] = '<strong>Trạng thái dịch vụ:</strong> '.htmlspecialchars($stLbl);
            if (!empty($hdr)) { $html .= '<div style="margin:4px 0 6px 0">'.implode(' — ', $hdr).'</div>'; }

            $html .= '<table>'
                   .' <tr><th>Chỉ số</th><th>Kết quả</th><th>Khoảng tham chiếu</th><th>Tình trạng</th><th>Ghi chú</th></tr>';
            foreach ($byService[(int)$row['service_id']] ?? [] as $m) {
                $html .= '<tr>'
                      . '<td>'.htmlspecialchars($m['metric_name']).'</td>'
                      . '<td>'.htmlspecialchars($m['result_value'] ?? '').'</td>'
                      . '<td>'.htmlspecialchars($m['reference_range'] ?? '').'</td>'
                      . '<td>'.htmlspecialchars($m['result_status'] ?? '').'</td>'
                      . '<td>'.htmlspecialchars($m['notes'] ?? '').'</td>'
                      . '</tr>';
            }
            $html .= '</table>';
        }

        // Footer
        $html .= '<div style="margin-top:24px; display:flex; justify-content:space-between;">'
              .  '<div><strong>Ngày in:</strong> ' . date('d/m/Y H:i') . '</div>'
              .  '<div style="text-align:right; min-width:240px;">Người duyệt kết quả<br><br><br>....................................</div>'
              .  '</div>';

        // Try Dompdf if available
        $pdfPath = null; $ok = false;
        try {
            if (class_exists('Dompdf\\Dompdf')) {
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $output = $dompdf->output();
                $dir = APP_PATH . '/storage/reports'; if (!is_dir($dir)) @mkdir($dir, 0777, true);
                $pdfPath = $dir . '/package_report_' . $packageAppointmentId . '.pdf';
                file_put_contents($pdfPath, $output);
                $ok = true;
            }
        } catch (\Throwable $e) { $ok = false; }

        if ($ok) {
            // Save path
            $stUp = $conn->prepare('UPDATE package_appointments SET final_pdf_path = :p WHERE id = :id');
            $stUp->execute([':p'=>$pdfPath, ':id'=>$packageAppointmentId]);
            // Notify patient PDF ready
            try {
                $patientUserId = $this->getPatientUserIdByPackageAppointmentId($packageAppointmentId);
                if ($patientUserId) {
                    $this->notify($patientUserId, 'Có file kết quả PDF', 'Kết quả gói đã có file PDF để tải.', '/my-results/package/' . $packageAppointmentId, 'system');
                }
            } catch (\Throwable $e) { /* ignore notify errors */ }
            $_SESSION['success'] = 'Đã tạo PDF kết quả.';
            if ($isOwnerPatient) { header('Location: ' . APP_URL . '/my-results/package/' . $packageAppointmentId); exit; }
            header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId); exit;
        } else {
            // Fallback: render HTML inline
            header('Content-Type: text/html; charset=utf-8');
            echo $html; exit;
        }
    }

    // Tải file PDF kết quả đã tạo
    public function downloadPdf($packageAppointmentId) {
        Auth::requireLogin();

        // Cho phép: Admin/Doctor hoặc chính bệnh nhân sở hữu gói
        $pa = $this->packageAppointmentModel->findById($packageAppointmentId);
        if (!$pa) { http_response_code(404); echo 'Không tìm thấy gói khám'; return; }
        if (!Auth::isAdmin() && !Auth::isDoctor()) {
            $patient = $this->patientModel->findByUserId(Auth::id());
            if (!$patient || (int)$patient['id'] !== (int)$pa['patient_id']) {
                http_response_code(403); echo 'Bạn không có quyền tải tệp này'; return;
            }
        }

        $path = $pa['final_pdf_path'] ?? null;
        if (!$path || !is_file($path)) {
            $_SESSION['error'] = 'Chưa có file PDF hoặc file không tồn tại.';
            header('Location: ' . APP_URL . '/my-results/package/' . $packageAppointmentId);
            exit;
        }

        // Stream file về trình duyệt
        $filename = 'ket_qua_goi_' . $packageAppointmentId . '.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: private, max-age=3600');
        readfile($path);
        exit;
    }

    // Phân công bác sĩ tự động
    public function autoAssignDoctors($packageAppointmentId) {
        Auth::requireLogin();

        // Chỉ Admin/Receptionist mới được phân công
        if (!Auth::isAdmin() && !Auth::isReceptionist()) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện chức năng này';
            header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId);
            exit;
        }

        $packageAppointment = $this->packageAppointmentModel->findById($packageAppointmentId);

        if (!$packageAppointment) {
            $_SESSION['error'] = 'Không tìm thấy đăng ký gói khám';
            header('Location: ' . APP_URL . '/package-appointments');
            exit;
        }

        // Lấy danh sách dịch vụ trong gói
        $packageServices = $this->packageModel->getPackageServices($packageAppointment['package_id']);

        if (empty($packageServices)) {
            $_SESSION['error'] = 'Gói khám không có dịch vụ nào';
            header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId);
            exit;
        }

        // Ngày bắt đầu khám
        $startDate = null;
        try {
            if (!empty($packageAppointment['appointment_date'])) {
                $startDate = new DateTime($packageAppointment['appointment_date']);
            }
        } catch (Exception $e) {
            $startDate = null;
        }
        if ($startDate === null) {
            $startDate = new DateTime(date('Y-m-d'));
        }
        $currentTime = new DateTime('08:00:00'); // Bắt đầu từ 8h sáng
        $appointmentsCreated = 0;
        $failedServices = [];

        foreach ($packageServices as $service) {
            // Tìm bác sĩ phù hợp với dịch vụ, thử tối đa 7 ngày kế tiếp
            $doctor = null; $tryDate = clone $startDate; $tryTime = clone $currentTime;
            for ($day=0; $day<7 && !$doctor; $day++) {
                $doctor = $this->findSuitableDoctor($service, $tryDate, $tryTime, $packageAppointmentId);
                if ($doctor) break;
                // chuyển sang ngày kế tiếp và reset giờ
                $tryDate->modify('+1 day');
                $tryTime = new DateTime('08:00:00');
            }

            if ($doctor) {
                // Tạo appointment
                $this->appointmentModel->appointment_code = 'APT' . date('YmdHis') . rand(100, 999);
                $this->appointmentModel->patient_id = $packageAppointment['patient_id'];
                $this->appointmentModel->doctor_id = $doctor['id'];
                $this->appointmentModel->package_id = $packageAppointment['package_id'];
                $this->appointmentModel->package_appointment_id = $packageAppointmentId;
                // Đảm bảo không trùng giờ với dịch vụ khác trong gói
                $dateYmd = $tryDate->format('Y-m-d');
                $slot = $tryTime->format('H:i:s');
                $usedSlots = $this->getUsedTimeSlots($packageAppointmentId, $dateYmd);
                // Tránh trùng giờ bắt đầu với dịch vụ khác trong cùng gói/ngày
                while (in_array($slot, $usedSlots, true)) {
                    $tryTime->modify('+5 minutes');
                    if ((int)$tryTime->format('H') >= 17) {
                        $tryDate->modify('+1 day');
                        $dateYmd = $tryDate->format('Y-m-d');
                        $tryTime = new DateTime('08:00:00');
                    }
                    $slot = $tryTime->format('H:i:s');
                    $usedSlots = $this->getUsedTimeSlots($packageAppointmentId, $dateYmd);
                }
                $this->appointmentModel->appointment_date = $dateYmd;
                $this->appointmentModel->appointment_time = $slot;
                // DB enum chỉ hỗ trợ 'regular' | 'package'
        $this->appointmentModel->appointment_type = 'package';
                $this->appointmentModel->reason = $service['service_name'];
                $this->appointmentModel->total_price = $service['service_price'] ?? null;
                $this->appointmentModel->status = 'pending';
                $this->appointmentModel->notes = 'Tự động phân công - Gói khám: ' . $packageAppointment['package_name'];

                if ($this->appointmentModel->create()) {
                    $appointmentsCreated++;
                } else {
                    $failedServices[] = $service['service_name'];
                }

                // Tăng thời gian theo thời lượng dịch vụ (phút) cho dịch vụ tiếp theo
                $duration = (int)($service['duration_minutes'] ?? 30);
                if ($duration <= 0) { $duration = 30; }
                $currentTime->modify('+' . $duration . ' minutes');

                // Nếu quá 17h, chuyển sang ngày hôm sau cho điểm bắt đầu các dịch vụ tiếp theo
                if ($tryTime->format('H') >= 17) {
                    $startDate->modify('+1 day');
                    $currentTime = new DateTime('08:00:00');
                }
            } else {
                // Không tìm được bác sĩ
                $failedServices[] = $service['service_name'];
            }
        }

        // Thông báo kết quả
        $totalServices = count($packageServices);
        
        if ($appointmentsCreated > 0) {
            if ($appointmentsCreated == $totalServices) {
                $_SESSION['success'] = "✅ Đã phân công thành công {$appointmentsCreated}/{$totalServices} lịch khám";
            } else {
                $_SESSION['warning'] = "⚠️ Đã phân công {$appointmentsCreated}/{$totalServices} lịch khám. " . 
                                       count($failedServices) . " dịch vụ chưa phân công được bác sĩ.";
            }
        } else {
            $_SESSION['error'] = '❌ Không thể phân công bác sĩ. Vui lòng kiểm tra lại lịch làm việc của bác sĩ.';
        }

        header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId);
        exit;
    }

    // Mapping dịch vụ → chuyên khoa
    private function getServiceSpecializationMap() {
        return [
            // Khám lâm sàng
            'Khám nội tổng quát' => 'Nội khoa',
            'Khám tim mạch' => 'Tim mạch',
            'Khám hô hấp' => 'Hô hấp',
            'Khám tiêu hóa' => 'Tiêu hóa',
            'Khám thần kinh' => 'Thần kinh',
            'Khám mắt' => 'Mắt',
            'Khám tai mũi họng' => 'Tai Mũi Họng',
            'Khám răng hàm mặt' => 'Răng Hàm Mặt',
            'Khám da liễu' => 'Da liễu',
            'Khám cơ xương khớp' => 'Cơ Xương Khớp',
            'Khám sản phụ khoa' => 'Sản Phụ khoa',
            'Khám tiết niệu' => 'Tiết niệu',
            
            // Xét nghiệm & Chẩn đoán hình ảnh
            'Xét nghiệm' => 'Xét nghiệm',
            'Siêu âm' => 'Chẩn đoán hình ảnh',
            'X-quang' => 'Chẩn đoán hình ảnh',
            'Điện tim' => 'Tim mạch',
            'Nội soi' => 'Tiêu hóa',
        ];
    }

    // Tìm chuyên khoa phù hợp cho dịch vụ
    private function findSpecializationForService($serviceName) {
        $map = $this->getServiceSpecializationMap();
        
        // Tìm khớp chính xác
        if (isset($map[$serviceName])) {
            return $map[$serviceName];
        }
        
        // Tìm khớp một phần (chứa từ khóa)
        foreach ($map as $keyword => $specialization) {
            if (stripos($serviceName, $keyword) !== false) {
                return $specialization;
            }
        }
        
        // Mặc định: Nội khoa (bác sĩ đa khoa)
        return 'Nội khoa';
    }

    // Tìm bác sĩ phù hợp cho dịch vụ (theo chuyên môn)
    private function findSuitableDoctor($service, $date, $time, $packageAppointmentId) {
        $serviceName = $service["service_name"];
        $requiredSpecialization = $this->findSpecializationForService($serviceName);
        
        $database = new Database();
        $conn = $database->getConnection();

        // Lấy danh sách bác sĩ được phép cho dịch vụ này (nếu cấu hình)
        $allowedIds = [];
        try {
            $stAllowed = $conn->prepare('SELECT doctor_id FROM package_service_doctors WHERE service_id = ?');
            $stAllowed->execute([(int)$service['id']]);
            $allowedIds = array_map('intval', array_column($stAllowed->fetchAll(PDO::FETCH_ASSOC) ?: [], 'doctor_id'));
        } catch (\Throwable $e) { $allowedIds = []; }

        $doctors = [];
        if (!empty($allowedIds)) {
            // Ưu tiên tuyệt đối: chỉ chọn trong danh sách bác sĩ được cấu hình cho dịch vụ
            $place = implode(',', array_fill(0, count($allowedIds), '?'));
            $stmt = $conn->prepare("SELECT d.*, u.full_name, s.name as specialization_name FROM doctors d LEFT JOIN users u ON d.user_id=u.id LEFT JOIN specializations s ON s.id=d.specialization_id WHERE d.id IN ($place)");
            $stmt->execute($allowedIds);
            $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } else {
            // 1) Chọn theo chuyên khoa, ưu tiên bác sĩ available
            $query1 = "SELECT d.*, u.full_name, s.name as specialization_name
                       FROM doctors d
                       LEFT JOIN users u ON d.user_id = u.id
                       LEFT JOIN specializations s ON d.specialization_id = s.id
                       WHERE s.name = :specialization
                       AND (d.is_available = 1 OR d.is_available IS NULL)
                       ORDER BY d.total_patients ASC";
            $stmt1 = $conn->prepare($query1);
            $stmt1->bindParam(':specialization', $requiredSpecialization);
            if ($stmt1->execute()) { $doctors = $stmt1->fetchAll(PDO::FETCH_ASSOC) ?: []; }
            // 2) Nếu chưa có, bỏ điều kiện available
            if (empty($doctors)) {
                $query2 = "SELECT d.*, u.full_name, s.name as specialization_name
                           FROM doctors d
                           LEFT JOIN users u ON d.user_id = u.id
                           LEFT JOIN specializations s ON d.specialization_id = s.id
                           WHERE s.name = :specialization
                           ORDER BY d.total_patients ASC";
                $stmt2 = $conn->prepare($query2);
                $stmt2->bindParam(':specialization', $requiredSpecialization);
                $stmt2->execute();
                $doctors = $stmt2->fetchAll(PDO::FETCH_ASSOC) ?: [];
            }
            // 3) Nếu vẫn chưa có, LIKE theo tên chuyên khoa (nới lỏng)
            if (empty($doctors)) {
                $like = '%' . $requiredSpecialization . '%';
                $query3 = "SELECT d.*, u.full_name, s.name as specialization_name
                           FROM doctors d
                           LEFT JOIN users u ON d.user_id = u.id
                           LEFT JOIN specializations s ON d.specialization_id = s.id
                           WHERE s.name LIKE :like
                           ORDER BY d.total_patients ASC";
                $stmt3 = $conn->prepare($query3);
                $stmt3->bindParam(':like', $like);
                $stmt3->execute();
                $doctors = $stmt3->fetchAll(PDO::FETCH_ASSOC) ?: [];
            }
        }

        if (empty($doctors)) { return null; }

        // Tải hiện tại theo ngày và số dịch vụ đã gán trong gói này
        $loadMap = [];
        $inPackageMap = [];
        $ymd = $date->format('Y-m-d');
        foreach ($doctors as $d) {
            $docId = (int)$d['id'];
            $st1 = $conn->prepare("SELECT COUNT(*) c FROM appointments WHERE doctor_id = :did AND appointment_date = :d AND status IN ('pending','confirmed','completed')");
            $st1->execute([':did'=>$docId, ':d'=>$ymd]);
            $loadMap[$docId] = (int)$st1->fetchColumn();
            $st2 = $conn->prepare("SELECT COUNT(*) c FROM appointments WHERE doctor_id = :did AND package_appointment_id = :pa");
            $st2->execute([':did'=>$docId, ':pa'=>$packageAppointmentId]);
            $inPackageMap[$docId] = (int)$st2->fetchColumn();
        }

        // Nới giới hạn mỗi bác sĩ trong cùng gói: nếu có cấu hình allowedIds thì bỏ giới hạn; nếu không, cap=3
        $perPackageCap = !empty($allowedIds) ? 99 : 3;

        // Sắp xếp cân tải
        usort($doctors, function($a, $b) use ($loadMap, $inPackageMap) {
            $da = (int)$a['id']; $db = (int)$b['id'];
            $cmp = ($loadMap[$da] <=> $loadMap[$db]);
            if ($cmp !== 0) return $cmp;
            return ($inPackageMap[$da] <=> $inPackageMap[$db]);
        });

        foreach ($doctors as $doctor) {
            $docId = (int)$doctor['id'];
            if ($inPackageMap[$docId] >= $perPackageCap) { continue; }
            if ($this->appointmentModel->isDoctorAvailable($docId, $ymd, $time->format('H:i:s'))) {
                return $doctor;
            }
        }
        
        // Nếu không tìm được bác sĩ chuyên khoa, thử Nội khoa nhưng vẫn phải thuộc allowedIds nếu có cấu hình
        if ($requiredSpecialization !== 'Nội khoa') {
            $query = "SELECT d.*, u.full_name, s.name as specialization_name
                      FROM doctors d
                      LEFT JOIN users u ON d.user_id = u.id
                      LEFT JOIN specializations s ON d.specialization_id = s.id
                      WHERE s.name = 'Nội khoa'
                      AND (d.is_available = 1 OR d.is_available IS NULL)
                      ORDER BY d.total_patients ASC";
            
            $stmt = $conn->prepare($query);
            $stmt->execute();
            
            $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($allowedIds)) {
                $doctors = array_values(array_filter($doctors, function($d) use ($allowedIds){ return in_array((int)$d['id'], $allowedIds, true); }));
            }

            // Tương tự sắp xếp cân tải
            foreach ($doctors as $d) {
                $docId = (int)$d['id'];
                $q1 = "SELECT COUNT(*) c FROM appointments WHERE doctor_id = :did AND appointment_date = :d AND status IN ('pending','confirmed','completed')";
                $st1 = $conn->prepare($q1);
                $ymd = $date->format('Y-m-d');
                $st1->bindParam(':did', $docId, PDO::PARAM_INT);
                $st1->bindParam(':d', $ymd);
                $st1->execute();
                $loadMap[$docId] = (int)$st1->fetchColumn();

                $q2 = "SELECT COUNT(*) c FROM appointments WHERE doctor_id = :did AND package_appointment_id = :pa";
                $st2 = $conn->prepare($q2);
                $st2->bindParam(':did', $docId, PDO::PARAM_INT);
                $st2->bindParam(':pa', $packageAppointmentId, PDO::PARAM_INT);
                $st2->execute();
                $inPackageMap[$docId] = (int)$st2->fetchColumn();
            }

            usort($doctors, function($a, $b) use ($loadMap, $inPackageMap) {
                $da = (int)$a['id']; $db = (int)$b['id'];
                $cmp = ($loadMap[$da] <=> $loadMap[$db]);
                if ($cmp !== 0) return $cmp;
                return ($inPackageMap[$da] <=> $inPackageMap[$db]);
            });
            
            foreach ($doctors as $doctor) {
                $docId = (int)$doctor['id'];
                if ($inPackageMap[$docId] >= $perPackageCap) { continue; }
                $isAvailable = $this->appointmentModel->isDoctorAvailable(
                    $docId,
                    $date->format('Y-m-d'),
                    $time->format('H:i:s')
                );

                if ($isAvailable) {
                    return $doctor;
                }
            }
        }
        
        // 4) Cuối cùng: chỉ chọn trong allowedIds (nếu có). Nếu không có allowedIds cấu hình thì có thể lấy tất cả.
        $anyDocs = [];
        if (empty($doctors) && empty($allowedIds)) {
            // Không có cấu hình allowedIds: cho phép lấy tất cả như cũ
            $queryAny = "SELECT d.*, u.full_name FROM doctors d LEFT JOIN users u ON d.user_id = u.id";
            $stmtAny = $conn->prepare($queryAny);
            $stmtAny->execute();
            $anyDocs = $stmtAny->fetchAll(PDO::FETCH_ASSOC);
        } else if (empty($doctors) && !empty($allowedIds)) {
            // Có cấu hình allowedIds: chỉ lấy trong danh sách này
            $place = implode(',', array_fill(0, count($allowedIds), '?'));
            $stmtAny = $conn->prepare("SELECT d.*, u.full_name FROM doctors d LEFT JOIN users u ON d.user_id = u.id WHERE d.id IN ($place)");
            $stmtAny->execute($allowedIds);
            $anyDocs = $stmtAny->fetchAll(PDO::FETCH_ASSOC);
        }
        if (!empty($anyDocs)) {
            // Tính tải và sắp xếp
            $loadMap = [];
            $inPackageMap = [];
            foreach ($anyDocs as $d) {
                $docId = (int)$d['id'];
                $q1 = "SELECT COUNT(*) c FROM appointments WHERE doctor_id = :did AND appointment_date = :d AND status IN ('pending','confirmed','completed')";
                $st1 = $conn->prepare($q1);
                $ymd = $date->format('Y-m-d');
                $st1->bindParam(':did', $docId, PDO::PARAM_INT);
                $st1->bindParam(':d', $ymd);
                $st1->execute();
                $loadMap[$docId] = (int)$st1->fetchColumn();

                $q2 = "SELECT COUNT(*) c FROM appointments WHERE doctor_id = :did AND package_appointment_id = :pa";
                $st2 = $conn->prepare($q2);
                $st2->bindParam(':did', $docId, PDO::PARAM_INT);
                $st2->bindParam(':pa', $packageAppointmentId, PDO::PARAM_INT);
                $st2->execute();
                $inPackageMap[$docId] = (int)$st2->fetchColumn();
            }
            usort($anyDocs, function($a, $b) use ($loadMap, $inPackageMap) {
                $da = (int)$a['id']; $db = (int)$b['id'];
                $cmp = ($loadMap[$da] <=> $loadMap[$db]);
                if ($cmp !== 0) return $cmp;
                return ($inPackageMap[$da] <=> $inPackageMap[$db]);
            });
            foreach ($anyDocs as $doctor) {
                $docId = (int)$doctor['id'];
                if ($inPackageMap[$docId] >= $perPackageCap) { continue; }
                if ($this->appointmentModel->isDoctorAvailable(
                    $docId,
                    $date->format('Y-m-d'),
                    $time->format('H:i:s')
                )) {
                    return $doctor;
                }
            }
        }

        return null;
    }
    
    // Phân loại dịch vụ theo nhóm
    private function categorizeServices($services) {
        $clinical = [];      // Khám lâm sàng
        $laboratory = [];    // Xét nghiệm
        $imaging = [];       // Chẩn đoán hình ảnh
        
        foreach ($services as $service) {
            $name = $service['service_name'];
            
            if (stripos($name, 'Xét nghiệm') !== false) {
                $laboratory[] = $service;
            } elseif (stripos($name, 'Siêu âm') !== false || 
                      stripos($name, 'X-quang') !== false ||
                      stripos($name, 'CT') !== false ||
                      stripos($name, 'MRI') !== false) {
                $imaging[] = $service;
            } else {
                $clinical[] = $service;
            }
        }
        
        return [$clinical, $laboratory, $imaging];
    }

    // Lấy các khung giờ đã dùng trong cùng gói ở một ngày (để không trùng giờ giữa các dịch vụ)
    private function getUsedTimeSlots($packageAppointmentId, $dateYmd) {
        $used = [];
        $serviceAppointments = $this->appointmentModel->getByPackageAppointmentId($packageAppointmentId);
        if (!is_array($serviceAppointments)) {
            $serviceAppointments = [];
        }
        foreach ($serviceAppointments as $a) {
            if (!empty($a['doctor_id']) && !empty($a['appointment_date']) && $a['appointment_date'] === $dateYmd && !empty($a['appointment_time'])) {
                // Chuẩn hóa dạng H:i:s
                $t = date('H:i:s', strtotime($a['appointment_time']));
                $used[] = $t;
            }
        }
        return array_values(array_unique($used));
    }

    // Phân công bác sĩ thủ công
    public function assignDoctor() {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/package-appointments');
            exit;
        }

        $packageAppointmentId = $_POST['package_appointment_id'];
        $serviceId = $_POST['service_id'];
        $doctorId = $_POST['doctor_id'];
        $appointmentDate = $_POST['appointment_date'];
        $appointmentTime = $_POST['appointment_time'];

        $packageAppointment = $this->packageAppointmentModel->findById($packageAppointmentId);

        // Lấy giá dịch vụ từ cấu hình gói
        $servicePrice = null;
        $services = $this->packageModel->getPackageServices($packageAppointment['package_id']);
        foreach ($services as $svc) {
            if ((string)$svc['id'] === (string)$serviceId) { $servicePrice = $svc['service_price']; break; }
        }

        // RÀNG BUỘC: Không trùng giờ giữa các dịch vụ trong cùng gói
        $normalizedTime = date('H:i:s', strtotime($appointmentTime));
        $used = $this->getUsedTimeSlots($packageAppointmentId, $appointmentDate);
        if (in_array($normalizedTime, $used, true)) {
            $_SESSION['error'] = 'Giờ khám này đã được dùng cho dịch vụ khác trong gói. Vui lòng chọn khung giờ cách nhau 30 phút (ví dụ: 08:30, 09:00, ...)';
            header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId);
            exit;
        }

        // Kiểm tra bác sĩ được phép cho dịch vụ này
        try {
            $db = new Database(); $conn = $db->getConnection();
            $st = $conn->prepare('SELECT 1 FROM package_service_doctors WHERE service_id = ? AND doctor_id = ? LIMIT 1');
            $st->execute([(int)$serviceId, (int)$doctorId]);
            $ok = (bool)$st->fetchColumn();
            if (!$ok) {
                $_SESSION['error'] = 'Bác sĩ chọn không được cấu hình cho dịch vụ này.';
                header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId);
                exit;
            }
        } catch (\Throwable $e) { /* nếu lỗi DB, tiếp tục nhưng về nguyên tắc không nên */ }

        // Tạo appointment CHI TIẾT cho dịch vụ (để có thể có nhiều bác sĩ cho 1 gói)
        $this->appointmentModel->appointment_code = 'APT' . date('YmdHis') . rand(100, 999);
        $this->appointmentModel->patient_id = $packageAppointment['patient_id'];
        $this->appointmentModel->doctor_id = $doctorId;
        $this->appointmentModel->package_id = $packageAppointment['package_id'];
        $this->appointmentModel->package_appointment_id = $packageAppointmentId;
        $this->appointmentModel->appointment_date = $appointmentDate;
        $this->appointmentModel->appointment_time = $normalizedTime;
        // DB enum chỉ có 'regular' | 'package'
        $this->appointmentModel->appointment_type = 'package';
        $this->appointmentModel->reason = $_POST['service_name'];
        $this->appointmentModel->total_price = $servicePrice;
        $this->appointmentModel->status = 'pending';
        $this->appointmentModel->notes = 'Phân công thủ công - Gói khám: ' . $packageAppointment['package_name'];

        if ($this->appointmentModel->create()) {
            $_SESSION['success'] = 'Đã phân công bác sĩ cho dịch vụ trong gói.';
        } else {
            $_SESSION['error'] = 'Không thể tạo lịch hẹn cho dịch vụ.';
        }

        header('Location: ' . APP_URL . '/package-appointments/' . $packageAppointmentId);
        exit;
    }

    // Hủy đăng ký gói khám
    public function cancel($id) {
        Auth::requireLogin();

        $packageAppointment = $this->packageAppointmentModel->findById($id);

        if (!$packageAppointment) {
            $_SESSION['error'] = 'Không tìm thấy đăng ký gói khám';
            header('Location: ' . APP_URL . '/package-appointments');
            exit;
        }

        // Cập nhật trạng thái
        if ($this->packageAppointmentModel->updateStatus($id, 'cancelled')) {
            $_SESSION['success'] = 'Đã hủy đăng ký gói khám';
        } else {
            $_SESSION['error'] = 'Hủy đăng ký thất bại';
        }

        header('Location: ' . APP_URL . '/package-appointments');
        exit;
    }
}
