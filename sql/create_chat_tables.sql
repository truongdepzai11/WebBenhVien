-- Chatbot Tables for Health Consultation

-- Table: chat_conversations
-- Lưu thông tin cuộc trò chuyện của bệnh nhân với chatbot
CREATE TABLE IF NOT EXISTS `chat_conversations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `patient_id` int(11) NOT NULL,
    `title` varchar(255) DEFAULT NULL,
    `status` enum('active','closed','archived') DEFAULT 'active',
    `last_message_at` datetime DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_patient_id` (`patient_id`),
    KEY `idx_status` (`status`),
    KEY `idx_last_message_at` (`last_message_at`),
    CONSTRAINT `fk_chat_conversations_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: chat_messages
-- Lưu tin nhắn giữa bệnh nhân và chatbot
CREATE TABLE IF NOT EXISTS `chat_messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `conversation_id` int(11) NOT NULL,
    `sender_type` enum('patient','bot') NOT NULL,
    `sender_id` int(11) DEFAULT NULL, -- NULL cho bot
    `message_type` enum('text','image','file') DEFAULT 'text',
    `content` text NOT NULL,
    `file_path` varchar(500) DEFAULT NULL,
    `ai_response` text DEFAULT NULL, -- Phản hồi từ AI
    `ai_confidence` decimal(3,2) DEFAULT NULL, -- Độ tin cậy của AI (0.00-1.00)
    `is_read` tinyint(1) DEFAULT 0,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_conversation_id` (`conversation_id`),
    KEY `idx_sender_type` (`sender_type`),
    KEY `idx_created_at` (`created_at`),
    CONSTRAINT `fk_chat_messages_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `chat_conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: chat_participants
-- Lưu thông tin người tham gia (cho mở rộng sau này)
CREATE TABLE IF NOT EXISTS `chat_participants` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `conversation_id` int(11) NOT NULL,
    `participant_type` enum('patient','bot','doctor','admin') NOT NULL,
    `participant_id` int(11) DEFAULT NULL,
    `joined_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `last_active_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `is_active` tinyint(1) DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `idx_conversation_id` (`conversation_id`),
    KEY `idx_participant_type` (`participant_type`),
    CONSTRAINT `fk_chat_participants_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `chat_conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for testing
INSERT INTO `chat_conversations` (`patient_id`, `title`, `status`) VALUES
(1, 'Tư vấn về đau đầu', 'active'),
(1, 'Hỏi về huyết áp', 'active');

INSERT INTO `chat_messages` (`conversation_id`, `sender_type`, `message_type`, `content`, `ai_response`, `ai_confidence`) VALUES
(1, 'patient', 'text', 'Tôi hay bị đau đầu vào buổi sáng, phải làm sao?', 'Đau đầu buổi sáng có thể do nhiều nguyên nhân: thiếu ngủ, stress, hoặc vấn đề về huyết áp. Bạn nên: 1) Ngủ đủ 7-8 tiếng 2) Uống đủ nước 3) Kiểm tra huyết áp. Nếu kéo dài, hãy đến khám bác sĩ.', 0.85),
(1, 'bot', 'text', 'Đau đầu buổi sáng có thể do nhiều nguyên nhân: thiếu ngủ, stress, hoặc vấn đề về huyết áp. Bạn nên: 1) Ngủ đủ 7-8 tiếng 2) Uống đủ nước 3) Kiểm tra huyết áp. Nếu kéo dài, hãy đến khám bác sĩ.', NULL, NULL),
(2, 'patient', 'text', 'Huyết áp của tôi là 140/90, có cao không?', 'Huyết áp 140/90 được coi là cao (tăng huyết áp độ 1). Bạn nên: 1) Giảm muối trong ăn uống 2) Tập thể dục đều đặn 3) Giảm stress 4) Uống đủ nước. Hãy theo dõi và tái khám sau 1-2 tuần.', 0.92),
(2, 'bot', 'text', 'Huyết áp 140/90 được coi là cao (tăng huyết áp độ 1). Bạn nên: 1) Giảm muối trong ăn uống 2) Tập thể dục đều đặn 3) Giảm stress 4) Uống đủ nước. Hãy theo dõi và tái khám sau 1-2 tuần.', NULL, NULL);

INSERT INTO `chat_participants` (`conversation_id`, `participant_type`, `participant_id`) VALUES
(1, 'patient', 1),
(1, 'bot', NULL),
(2, 'patient', 1),
(2, 'bot', NULL);
