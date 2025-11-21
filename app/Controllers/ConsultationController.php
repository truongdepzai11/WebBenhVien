<?php
require_once APP_PATH . '/Models/Consultation.php';
require_once APP_PATH . '/Models/ConsultationMessage.php';
require_once APP_PATH . '/Models/ConsultationAttachment.php';
require_once APP_PATH . '/Models/Patient.php';
require_once APP_PATH . '/Models/Doctor.php';
require_once APP_PATH . '/Models/Notification.php';
require_once APP_PATH . '/Helpers/Auth.php';
require_once APP_PATH . '/Helpers/Mailer.php';

class ConsultationController {
    private $consultation;
    private $message;
    private $attachment;
    private $patientModel;
    private $doctorModel;
    private $notification;

    public function __construct() {
        $this->consultation = new Consultation();
        $this->message = new ConsultationMessage();
        $this->attachment = new ConsultationAttachment();
        $this->patientModel = new Patient();
        $this->doctorModel = new Doctor();
        $this->notification = new Notification();
    }

    public function index() {
        Auth::requireLogin();
        if (!Auth::isPatient()) { header('Location: '.APP_URL.'/dashboard'); exit; }
        $me = Auth::user();
        $patient = $this->patientModel->findByUserId($me['id']);
        $tickets = $this->consultation->listForPatient($patient['id']);
        $title = 'Tư vấn sức khỏe';
        ob_start();
        require APP_PATH . '/Views/consultations/index.php';
        $content = ob_get_clean();
        require APP_PATH . '/Views/layouts/main.php';
    }

    public function create() {
        Auth::requireLogin();
        if (!Auth::isPatient()) { header('Location: '.APP_URL.'/dashboard'); exit; }
        $doctors = $this->doctorModel->getAll();
        $title = 'Gửi câu hỏi';
        ob_start();
        require APP_PATH . '/Views/consultations/create.php';
        $content = ob_get_clean();
        require APP_PATH . '/Views/layouts/main.php';
    }

    public function store() {
        Auth::requireLogin();
        if (!Auth::isPatient() || $_SERVER['REQUEST_METHOD']!=='POST') { header('Location: '.APP_URL.'/consultations'); exit; }
        $me = Auth::user();
        $patient = $this->patientModel->findByUserId($me['id']);
        $doctor_id = !empty($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : null;
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        if ($subject==='') { $_SESSION['error']='Vui lòng nhập chủ đề'; header('Location: '.APP_URL.'/consultations/create'); exit; }
        if ($message==='') { $_SESSION['error']='Vui lòng nhập nội dung'; header('Location: '.APP_URL.'/consultations/create'); exit; }
        $this->consultation->patient_id = $patient['id'];
        $this->consultation->doctor_id = $doctor_id;
        $this->consultation->subject = $subject;
        $this->consultation->status = 'open';
        if (!$this->consultation->create()) { $_SESSION['error']='Tạo ticket thất bại'; header('Location: '.APP_URL.'/consultations/create'); exit; }
        $cid = (int)$this->consultation->id;
        $this->message->consultation_id = $cid;
        $this->message->sender_user_id = $me['id'];
        $this->message->message_text = $message;
        $this->message->create();
        $this->consultation->id = $cid; $this->consultation->touch();
        $this->handleUploads($this->message->id, $cid);
        $_SESSION['success'] = 'Gửi câu hỏi thành công';
        if ($doctor_id) {
            $doc = $this->doctorModel->findById($doctor_id);
            if ($doc) {
                $link = '/consultations/'.$cid;
                $this->notification->create((int)$doc['user_id'], 'Câu hỏi tư vấn mới', 'Bạn có câu hỏi mới: '.$subject, $link, 'system');
                if (!empty($doc['email'])) {
                    $mailer = new Mailer();
                    $html = '<p>Chào '.htmlspecialchars($doc['full_name']).',</p><p>Bạn có câu hỏi tư vấn mới: <strong>'.htmlspecialchars($subject).'</strong>.</p><p>Xem: <a href="'.APP_URL.$link.'">'.APP_URL.$link.'</a></p>';
                    $mailer->send($doc['email'], 'Câu hỏi tư vấn mới', $html);
                }
            }
        }
        header('Location: '.APP_URL.'/consultations/'.$cid);
        exit;
    }

    public function show($id) {
        Auth::requireLogin();
        $ticket = $this->consultation->findById($id);
        if (!$ticket) { header('Location: '.APP_URL.'/consultations'); exit; }
        $allowed = false;
        if (Auth::isPatient()) {
            $me = Auth::user();
            $patient = $this->patientModel->findByUserId($me['id']);
            $allowed = $patient && $ticket['patient_id']==$patient['id'];
        } elseif (Auth::isDoctor()) {
            $me = Auth::user();
            $doctor = $this->doctorModel->findByUserId($me['id']);
            $allowed = $doctor && (int)$ticket['doctor_id']===(int)$doctor['id'];
        } elseif (Auth::isAdmin()) { $allowed = true; }
        if (!$allowed) { header('Location: '.APP_URL.'/dashboard'); exit; }
        $messages = $this->message->listByConsultation($id);
        $allAtt = $this->attachment->listByConsultation($id);
        $attachmentsByMsg = [];
        foreach ($allAtt as $a) { $attachmentsByMsg[$a['message_id']][] = $a; }
        $title = 'Tư vấn: '.$ticket['subject'];
        ob_start();
        require APP_PATH . '/Views/consultations/show.php';
        $content = ob_get_clean();
        require APP_PATH . '/Views/layouts/main.php';
    }

    public function reply($id) {
        Auth::requireLogin();
        if ($_SERVER['REQUEST_METHOD']!=='POST') { header('Location: '.APP_URL.'/consultations/'.$id); exit; }
        $ticket = $this->consultation->findById($id);
        if (!$ticket) { header('Location: '.APP_URL.'/consultations'); exit; }
        $me = Auth::user();
        $allowed = false;
        $notifyUserId = null;
        if (Auth::isPatient()) {
            $patient = $this->patientModel->findByUserId($me['id']);
            $allowed = $patient && $ticket['patient_id']==$patient['id'];
            $notifyUserId = $this->doctorUserId($ticket['doctor_id']);
        } elseif (Auth::isDoctor()) {
            $doctor = $this->doctorModel->findByUserId($me['id']);
            $allowed = $doctor && (int)$ticket['doctor_id']===(int)$doctor['id'];
            $notifyUserId = $this->patientUserId($ticket['patient_id']);
        } elseif (Auth::isAdmin()) { $allowed = true; }
        if (!$allowed) { header('Location: '.APP_URL.'/dashboard'); exit; }
        $msg = trim($_POST['message'] ?? '');
        if ($msg==='') { $_SESSION['error']='Vui lòng nhập nội dung'; header('Location: '.APP_URL.'/consultations/'.$id); exit; }
        $this->message->consultation_id = $id;
        $this->message->sender_user_id = $me['id'];
        $this->message->message_text = $msg;
        $this->message->create();
        $this->consultation->id = $id; $this->consultation->touch();
        $this->handleUploads($this->message->id, $id);
        if (Auth::isDoctor()) { $this->consultation->setStatus('answered'); }
        if ($notifyUserId) {
            $link = '/consultations/'.$id;
            $this->notification->create($notifyUserId, 'Phản hồi tư vấn mới', 'Bạn có phản hồi mới cho: '.$ticket['subject'], $link, 'system');
        }
        header('Location: '.APP_URL.'/consultations/'.$id);
        exit;
    }

    public function doctorIndex() {
        Auth::requireLogin(); if (!Auth::isDoctor()) { header('Location: '.APP_URL.'/dashboard'); exit; }
        $me = Auth::user();
        $tickets = $this->consultation->listForDoctorUser($me['id'], null, 100);
        $title = 'Hộp thư tư vấn';
        ob_start();
        require APP_PATH . '/Views/consultations/doctor_index.php';
        $content = ob_get_clean();
        require APP_PATH . '/Views/layouts/main.php';
    }

    private function handleUploads($messageId, $consultationId) {
        if (!isset($_FILES['attachments'])) return;
        $files = $_FILES['attachments'];
        $count = is_array($files['name']) ? count($files['name']) : 0;
        if ($count===0) return;
        $dir = BASE_PATH . '/storage/consultations/' . $consultationId;
        if (!is_dir($dir)) @mkdir($dir, 0777, true);
        for ($i=0; $i<$count; $i++) {
            if ($files['error'][$i]!==UPLOAD_ERR_OK) continue;
            $name = basename($files['name'][$i]);
            $tmp = $files['tmp_name'][$i];
            $size = (int)$files['size'][$i];
            if ($size > 10*1024*1024) continue;
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $safe = uniqid('att_').'.'.$ext;
            $dest = $dir . '/' . $safe;
            if (move_uploaded_file($tmp, $dest)) {
                $rel = '/storage/consultations/'.$consultationId.'/'.$safe;
                $this->attachment->message_id = $messageId;
                $this->attachment->file_path = $rel;
                $this->attachment->file_name = $name;
                $this->attachment->file_size = $size;
                $this->attachment->mime_type = mime_content_type($dest) ?: null;
                $this->attachment->create();
            }
        }
    }

    private function patientUserId($patient_id) {
        $p = $this->patientModel->findById($patient_id);
        return $p ? (int)$p['user_id'] : null;
    }
    private function doctorUserId($doctor_id) {
        if (!$doctor_id) return null;
        $d = $this->doctorModel->findById($doctor_id);
        return $d ? (int)$d['user_id'] : null;
    }
}
