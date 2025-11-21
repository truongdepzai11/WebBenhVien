<?php
// Daily reminders: create in-app notifications and send email for appointments tomorrow (status=confirmed)

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Models/Notification.php';
require_once __DIR__ . '/../app/Helpers/Mailer.php';

$database = new Database();
$conn = $database->getConnection();

$tomorrow = (new DateTime('tomorrow'))->format('Y-m-d');

$sql = "SELECT a.id AS appointment_id, a.appointment_date, a.appointment_time,
               u.id AS user_id, u.email, u.full_name,
               p.caregiver_name, p.caregiver_email, p.caregiver_relation, p.caregiver_notify,
               du.full_name AS doctor_name
        FROM appointments a
        LEFT JOIN patients p ON a.patient_id = p.id
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN doctors d ON a.doctor_id = d.id
        LEFT JOIN users du ON d.user_id = du.id
        WHERE a.status = 'confirmed' AND a.appointment_date = :d";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':d', $tomorrow);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$notif = new Notification();
$mailer = new Mailer();

$created = 0; $sent = 0;
foreach ($rows as $r) {
    $title = 'Nhắc lịch khám ngày ' . date('d/m/Y', strtotime($r['appointment_date']));
    $timeStr = !empty($r['appointment_time']) ? date('H:i', strtotime($r['appointment_time'])) : 'không rõ giờ';
    $message = 'Bạn có lịch khám với bác sĩ ' . ($r['doctor_name'] ?? '...') . ' vào lúc ' . $timeStr . ' ngày ' . date('d/m/Y', strtotime($r['appointment_date'])) . '.';
    $link = '/appointments/' . (int)$r['appointment_id'];

    if ($notif->create((int)$r['user_id'], $title, $message, $link, 'reminder')) {
        $created++;
    }

    if (!empty($r['email'])) {
        $html = '<p>Chào ' . htmlspecialchars($r['full_name']) . ',</p>'
              . '<p>Hệ thống nhắc bạn lịch khám ngày <strong>' . date('d/m/Y', strtotime($r['appointment_date'])) . '</strong> lúc <strong>' . $timeStr . '</strong>.'
              . ' Bác sĩ: ' . htmlspecialchars($r['doctor_name'] ?? '') . '.</p>'
              . '<p>Xem chi tiết: <a href="' . APP_URL . $link . '">' . APP_URL . $link . '</a></p>'
              . '<p>Trân trọng.</p>';
        if ($mailer->send($r['email'], $title, $html)) {
            $sent++;
        }
    }

    // Email người thân (nếu có và có đồng ý nhận thông báo)
    if (!empty($r['caregiver_email']) && (int)($r['caregiver_notify'] ?? 0) === 1) {
        $cgName = $r['caregiver_name'] ?: 'Người thân';
        $htmlCg = '<p>Chào ' . htmlspecialchars($cgName) . ',</p>'
                . '<p>Hệ thống nhắc bạn: người thân của bạn (' . htmlspecialchars($r['full_name']) . ') có lịch khám ngày <strong>'
                . date('d/m/Y', strtotime($r['appointment_date'])) . '</strong> lúc <strong>' . $timeStr . '</strong> với bác sĩ '
                . htmlspecialchars($r['doctor_name'] ?? '') . '.</p>'
                . '<p>Xem chi tiết: <a href="' . APP_URL . $link . '">' . APP_URL . $link . '</a></p>'
                . '<p>Trân trọng.</p>';
        if ($mailer->send($r['caregiver_email'], '[Người thân] ' . $title, $htmlCg)) {
            $sent++;
        }
    }
}

$logDir = BASE_PATH . '/storage/logs';
if (!is_dir($logDir)) @mkdir($logDir, 0777, true);
file_put_contents($logDir . '/reminders.log', date('Y-m-d H:i:s') . " | created=$created sent=$sent for date=$tomorrow\n", FILE_APPEND);

echo "Reminders created=$created, emails sent=$sent for $tomorrow\n";
