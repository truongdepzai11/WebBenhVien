<?php

class Mailer {
    private $fromEmail;
    private $fromName;
    private $enabled;

    public function __construct() {
        // Nạp cấu hình nếu có
        $cfgPath = BASE_PATH . '/config/mailer.php';
        if (file_exists($cfgPath)) {
            $cfg = include $cfgPath;
            if (is_array($cfg)) {
                foreach ([
                    'SMTP_HOST','SMTP_PORT','SMTP_USER','SMTP_PASS','FROM_EMAIL','FROM_NAME'
                ] as $k) {
                    if (isset($cfg[$k]) && $cfg[$k] !== '') { putenv($k.'='.$cfg[$k]); }
                }
            }
        }
        $this->fromEmail = getenv('FROM_EMAIL') ?: 'no-reply@example.com';
        $this->fromName = getenv('FROM_NAME') ?: APP_NAME;
        $this->enabled = (bool) getenv('SMTP_HOST'); // nếu có SMTP_HOST coi như bật gửi thật (qua PHPMailer nếu có)
    }

    public function send($toEmail, $subject, $htmlBody) {
        // Nếu có PHPMailer (đã cài) và có cấu hình SMTP thì dùng, ngược lại fallback log-only
        if ($this->enabled && $this->tryPHPMailer($toEmail, $subject, $htmlBody)) {
            return true;
        }
        // Fallback: ghi log để không chặn luồng
        $logDir = BASE_PATH . '/storage/logs';
        if (!is_dir($logDir)) @mkdir($logDir, 0777, true);
        $line = date('Y-m-d H:i:s') . " | TO: $toEmail | SUBJ: $subject\n";
        $line .= strip_tags($htmlBody) . "\n-----------------------------\n";
        file_put_contents($logDir . '/mail.log', $line, FILE_APPEND);
        return true;
    }

    private function tryPHPMailer($toEmail, $subject, $htmlBody) {
        try {
            // Thử autoload nếu có
            if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
                require_once BASE_PATH . '/vendor/autoload.php';
                if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                    // set up debug log target
                    $logDir = BASE_PATH . '/storage/logs';
                    if (!is_dir($logDir)) @mkdir($logDir, 0777, true);
                    $debugFile = $logDir . '/mail.log';
                    $mail->SMTPDebug = 2; // verbose client/server messages
                    $mail->Debugoutput = function ($str, $level) use ($debugFile) {
                        $prefix = date('Y-m-d H:i:s') . " | SMTP[$level] ";
                        @file_put_contents($debugFile, $prefix . $str . "\n", FILE_APPEND);
                    };
                    $mail->isSMTP();
                    $mail->Host = getenv('SMTP_HOST');
                    $portEnv = getenv('SMTP_PORT') ?: 587;
                    $port = is_numeric($portEnv) ? (int)$portEnv : 587;
                    $mail->Port = $port;
                    $mail->SMTPAuth = true;
                    $mail->Username = getenv('SMTP_USER');
                    $mail->Password = getenv('SMTP_PASS');
                    $mail->SMTPSecure = ($port === 465)
                        ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
                        : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    // Local dev often uses self-signed certs; relax verification for localhost
                    $mail->SMTPOptions = [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true,
                        ],
                    ];
                    $mail->Timeout = 20; // seconds
                    $mail->CharSet = 'UTF-8';

                    $mail->setFrom($this->fromEmail, $this->fromName);
                    $mail->addAddress($toEmail);
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body = $htmlBody;
                    $mail->AltBody = strip_tags($htmlBody);

                    $mail->send();
                    return true;
                }
            }
        } catch (\Throwable $e) {
            $logDir = BASE_PATH . '/storage/logs';
            if (!is_dir($logDir)) @mkdir($logDir, 0777, true);
            $line = date('Y-m-d H:i:s') . ' | SMTP[exception] ' . $e->getMessage() . "\n";
            @file_put_contents($logDir . '/mail.log', $line, FILE_APPEND);
        }
        return false;
    }
}
