<?php

require_once __DIR__ . '/../../config/database.php';

class Notification {
    private $conn;
    private $table = 'notifications';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->ensureTable();
    }

    private function ensureTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `message` TEXT NOT NULL,
            `link` VARCHAR(255) DEFAULT NULL,
            `type` VARCHAR(50) DEFAULT 'reminder',
            `is_read` TINYINT(1) DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (`user_id`),
            INDEX (`is_read`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $this->conn->exec($sql);
    }

    public function create($user_id, $title, $message, $link = null, $type = 'reminder') {
        $q = "INSERT INTO {$this->table} (user_id, title, message, link, type) VALUES (:uid, :title, :msg, :link, :type)";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':msg', $message);
        $stmt->bindParam(':link', $link);
        $stmt->bindParam(':type', $type);
        return $stmt->execute();
    }

    public function getByUser($user_id, $limit = 100) {
        $q = "SELECT * FROM {$this->table} WHERE user_id = :uid ORDER BY created_at DESC LIMIT :lim";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($id, $user_id) {
        $q = "UPDATE {$this->table} SET is_read = 1 WHERE id = :id AND user_id = :uid";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getUnreadCount($user_id) {
        $q = "SELECT COUNT(*) AS c FROM {$this->table} WHERE user_id = :uid AND is_read = 0";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['c'] ?? 0);
    }

    // Kiểm tra đã có thông báo cùng user+link+type chưa (để tránh tạo trùng)
    public function existsByUserLinkType($user_id, $link, $type) {
        $q = "SELECT 1 FROM {$this->table} WHERE user_id = :uid AND link = :link AND type = :type LIMIT 1";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':link', $link);
        $stmt->bindParam(':type', $type);
        $stmt->execute();
        return (bool)$stmt->fetchColumn();
    }

    public function createIfNotExists($user_id, $title, $message, $link, $type = 'reminder') {
        if (!$this->existsByUserLinkType($user_id, $link, $type)) {
            return $this->create($user_id, $title, $message, $link, $type);
        }
        return true;
    }
}
