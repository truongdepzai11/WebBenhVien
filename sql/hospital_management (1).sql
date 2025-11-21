 -- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 31, 2025 lúc 08:51 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `hospital_management`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `appointment_code` varchar(20) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `coordinator_doctor_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT 0.00,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled','late_cancelled','no_show') DEFAULT 'pending',
  `appointment_type` enum('regular','package') DEFAULT 'regular',
  `notes` text DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `cancellation_fee` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `appointments`
--

INSERT INTO `appointments` (`id`, `appointment_code`, `patient_id`, `doctor_id`, `coordinator_doctor_id`, `package_id`, `total_price`, `appointment_date`, `appointment_time`, `reason`, `status`, `appointment_type`, `notes`, `confirmed_at`, `completed_at`, `cancelled_at`, `cancellation_reason`, `cancellation_fee`, `created_at`, `updated_at`) VALUES
(1, 'APT00001', 1, 6, NULL, NULL, 0.00, '2025-10-17', '14:30:00', 'đau mắt', 'completed', 'regular', '', NULL, NULL, NULL, NULL, 0.00, '2025-10-04 06:56:13', '2025-10-04 07:03:04'),
(2, 'APT00002', 1, 4, NULL, NULL, 0.00, '2025-10-04', '09:00:00', 'đau mắt', 'completed', 'regular', '', NULL, NULL, NULL, NULL, 0.00, '2025-10-04 07:14:42', '2025-10-04 07:31:45'),
(3, 'APT00003', 1, 5, NULL, NULL, 0.00, '2025-10-04', '15:00:00', 'đau tại', 'completed', 'regular', '', NULL, NULL, NULL, NULL, 0.00, '2025-10-04 07:58:19', '2025-10-04 08:11:15'),
(4, 'APT00004', 1, 5, NULL, NULL, 0.00, '2025-10-09', '14:00:00', 'đau răng', 'completed', 'regular', '', NULL, NULL, NULL, NULL, 0.00, '2025-10-04 08:01:07', '2025-10-04 08:11:39'),
(5, 'APT00005', 1, 5, NULL, NULL, 0.00, '2025-10-04', '16:00:00', 'đau họng', 'late_cancelled', 'regular', '', NULL, NULL, '2025-10-04 08:25:21', 'bânj', 80000.00, '2025-10-04 08:13:55', '2025-10-04 08:25:21'),
(6, 'APT00006', 1, 5, NULL, NULL, 0.00, '2025-10-04', '16:30:00', 'đau cổ', 'late_cancelled', 'regular', '', NULL, NULL, '2025-10-04 08:36:57', 'hủy', 80000.00, '2025-10-04 08:36:11', '2025-10-04 08:36:57'),
(7, 'APT00007', 4, 1, NULL, NULL, 0.00, '2025-10-04', '16:00:00', 'đau đầu', 'completed', 'regular', 'Walk-in - Đặt bởi Administrator', NULL, NULL, NULL, NULL, 0.00, '2025-10-04 09:11:47', '2025-10-04 09:14:04'),
(8, 'APT00008', 5, 1, NULL, NULL, 0.00, '2025-10-24', '11:00:00', 'đau đầu', 'completed', 'regular', 'Walk-in - Đặt bởi Administrator', NULL, NULL, NULL, NULL, 0.00, '2025-10-04 09:44:32', '2025-10-08 06:18:00'),
(9, 'APT00009', 6, 1, NULL, NULL, 0.00, '2025-10-04', '15:00:00', 'đau đầu', 'confirmed', 'regular', 'Walk-in - Đặt bởi Administrator', NULL, NULL, NULL, NULL, 0.00, '2025-10-04 09:56:43', '2025-10-04 09:56:43'),
(10, 'APT00010', 1, 2, NULL, NULL, 0.00, '2025-10-07', '15:00:00', 'đau dạ dày', 'completed', 'regular', '', NULL, NULL, NULL, NULL, 0.00, '2025-10-06 06:15:43', '2025-10-06 06:18:10'),
(11, 'APT00011', 8, 1, NULL, NULL, 0.00, '2025-10-06', '09:00:00', 'đau đầu', 'confirmed', 'regular', 'Walk-in - Đặt bởi Administrator', NULL, NULL, NULL, NULL, 0.00, '2025-10-06 06:24:14', '2025-10-06 06:24:14'),
(12, 'APT00012', 1, 4, NULL, NULL, 0.00, '2025-10-07', '15:00:00', 'đau mắt phải', 'pending', 'regular', '', NULL, NULL, NULL, NULL, 0.00, '2025-10-06 11:28:49', '2025-10-06 11:28:49'),
(13, 'APT00013', 9, 2, NULL, NULL, 0.00, '2025-10-07', '16:00:00', 'mệt , đau đầu ,...', 'confirmed', 'regular', '', NULL, NULL, NULL, NULL, 0.00, '2025-10-07 04:08:13', '2025-10-07 04:08:43'),
(14, 'APT00014', 10, 5, NULL, NULL, 0.00, '2025-10-09', '08:00:00', 'đau đầu', 'completed', 'regular', 'Walk-in - Đặt bởi Hà Thị Tâm', NULL, NULL, NULL, NULL, 0.00, '2025-10-09 03:31:31', '2025-10-09 03:53:10'),
(15, 'APT00015', 1, 6, NULL, NULL, 0.00, '2025-10-10', '08:30:00', 'mụn', 'pending', 'regular', '', NULL, NULL, NULL, NULL, 0.00, '2025-10-09 04:50:29', '2025-10-09 04:50:29'),
(16, 'APT00016', 1, 2, NULL, NULL, 0.00, '2025-10-11', '09:30:00', 'đau lưng', 'pending', 'regular', '', NULL, NULL, NULL, NULL, 0.00, '2025-10-09 04:51:11', '2025-10-09 04:51:11'),
(17, 'APT00017', 1, 5, NULL, NULL, 0.00, '2025-10-09', '13:00:00', 'ho', 'pending', 'regular', '', NULL, NULL, NULL, NULL, 0.00, '2025-10-09 04:53:17', '2025-10-09 04:53:17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `appointment_package_services`
--

CREATE TABLE `appointment_package_services` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_price` decimal(10,2) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `result_value` text DEFAULT NULL,
  `result_status` enum('normal','abnormal','pending') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `tested_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `doctor_code` varchar(20) NOT NULL,
  `specialization_id` int(11) NOT NULL,
  `license_number` varchar(50) NOT NULL,
  `qualification` text DEFAULT NULL,
  `experience_years` int(11) DEFAULT 0,
  `consultation_fee` decimal(10,2) DEFAULT 0.00,
  `available_days` varchar(100) DEFAULT NULL,
  `available_hours` varchar(50) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `education` text DEFAULT NULL,
  `certifications` text DEFAULT NULL,
  `languages` varchar(255) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_patients` int(11) DEFAULT 0,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `doctors`
--

INSERT INTO `doctors` (`id`, `user_id`, `doctor_code`, `specialization_id`, `license_number`, `qualification`, `experience_years`, `consultation_fee`, `available_days`, `available_hours`, `bio`, `education`, `certifications`, `languages`, `rating`, `total_patients`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 2, 'DOC001', 1, 'LIC001', 'Bác sĩ chuyên khoa II', 15, 200000.00, 'Thứ 2,Thứ 4,Thứ 6', '08:00-17:00', 'Chuyên gia tim mạch với 15 năm kinh nghiệm', NULL, NULL, NULL, 0.00, 0, 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(2, 3, 'DOC002', 2, 'LIC002', 'Bác sĩ chuyên khoa I', 10, 150000.00, 'Thứ 3,Thứ 5,Thứ 7', '08:00-17:00', 'Bác sĩ nội khoa giàu kinh nghiệm', NULL, NULL, NULL, 0.00, 0, 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(3, 4, 'DOC003', 3, 'LIC003', 'Bác sĩ chuyên khoa II', 12, 180000.00, 'Thứ 2,Thứ 3,Thứ 4,Thứ 5,Thứ 6', '08:00-16:00', 'Chuyên gia nhi khoa', NULL, NULL, NULL, 0.00, 0, 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(4, 5, 'DOC004', 9, 'LIC004', 'Bác sĩ chuyên khoa I', 8, 170000.00, 'Thứ 2,Thứ 3,Thứ 5', '08:00-17:00', 'Bác sĩ chuyên khoa mắt', NULL, NULL, NULL, 0.00, 0, 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(5, 6, 'DOC005', 8, 'LIC005', 'Bác sĩ chuyên khoa II', 14, 160000.00, 'Thứ 2,Thứ 4,Thứ 6', '08:00-16:00', 'Chuyên gia tai mũi họng', NULL, NULL, NULL, 0.00, 0, 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(6, 7, 'DOC006', 7, 'LIC006', 'Bác sĩ chuyên khoa I', 9, 140000.00, 'Thứ 3,Thứ 5,Thứ 7', '08:00-17:00', 'Bác sĩ da liễu', NULL, NULL, NULL, 0.00, 0, 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(7, 8, 'DOC007', 10, 'LIC007', 'Bác sĩ chuyên khoa II', 11, 190000.00, 'Thứ 2,Thứ 3,Thứ 4,Thứ 5,Thứ 6', '08:00-18:00', 'Chuyên gia răng hàm mặt', NULL, NULL, NULL, 0.00, 0, 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(9, 26, 'DOC0026', 8, 'LCII10', 'Tốt nghiệp tiến sĩ DH Havert', 3, 300000.00, 'Full time', '8:00-17:00', NULL, NULL, NULL, NULL, 0.00, 0, 1, '2025-10-09 06:40:45', '2025-10-09 06:40:45'),
(10, 27, 'DOC0027', 9, 'LCII111', 'Tốt nghiệp tiến sĩ DH Havert 2', 5, 200000.00, 'Full time', '08:00-17:00', NULL, NULL, NULL, NULL, 0.00, 0, 1, '2025-10-09 07:01:41', '2025-10-09 07:01:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `health_packages`
--

CREATE TABLE `health_packages` (
  `id` int(11) NOT NULL,
  `package_code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `gender_requirement` enum('both','male','female') DEFAULT 'both',
  `min_age` int(11) DEFAULT 0,
  `max_age` int(11) DEFAULT 150,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `health_packages`
--

INSERT INTO `health_packages` (`id`, `package_code`, `name`, `description`, `gender_requirement`, `min_age`, `max_age`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'PKG0001', 'Gói khám sức khỏe tổng quát - Nam', 'Gói khám toàn diện dành cho nam giới, bao gồm các xét nghiệm cơ bản và chuyên sâu', 'male', 18, 100, 1, '2025-10-29 03:35:33', '2025-10-31 04:24:04'),
(2, 'PKG0002', 'Gói khám sức khỏe tổng quát - Nữ', 'Gói khám toàn diện dành cho nữ giới, bao gồm các xét nghiệm cơ bản và chuyên sâu', 'female', 18, 150, 1, '2025-10-29 03:35:33', '2025-10-29 04:32:45');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `invoice_code` varchar(20) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `patient_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `final_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','cancelled','refunded') DEFAULT 'pending',
  `payment_method` enum('cash','momo','vnpay','bank_transfer') DEFAULT 'cash',
  `payment_status` enum('unpaid','paid','partial','refunded') DEFAULT 'unpaid',
  `notes` text DEFAULT NULL,
  `issued_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `paid_date` timestamp NULL DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_code`, `appointment_id`, `patient_id`, `total_amount`, `discount_amount`, `tax_amount`, `final_amount`, `status`, `payment_method`, `payment_status`, `notes`, `issued_date`, `paid_date`, `due_date`, `created_at`, `updated_at`) VALUES
(1, 'INV202510040001', 2, 1, 170000.00, 0.00, 3000.00, 173000.00, 'paid', 'cash', 'paid', 'cần thanh toán', '2025-10-04 07:41:35', '2025-10-04 07:45:26', '2025-10-11', '2025-10-04 07:41:35', '2025-10-04 07:45:26'),
(2, 'INV202510040002', 5, 1, 80000.00, 0.00, 0.00, 80000.00, 'pending', 'cash', 'unpaid', 'Phí hủy lịch muộn (trong 24h)', '2025-10-04 08:25:21', NULL, '2025-10-11', '2025-10-04 08:25:21', '2025-10-04 08:25:21'),
(3, 'INV202510040003', 6, 1, 80000.00, 0.00, 0.00, 80000.00, 'pending', 'cash', 'unpaid', 'Phí hủy lịch muộn (trong 24h)', '2025-10-04 08:36:57', NULL, '2025-10-11', '2025-10-04 08:36:57', '2025-10-04 08:36:57'),
(4, 'INV202510060004', 10, 1, 150000.00, 0.00, 0.00, 150000.00, 'pending', 'cash', 'unpaid', 'fdsfdsfdsf', '2025-10-06 06:18:43', NULL, '2025-10-13', '2025-10-06 06:18:43', '2025-10-06 06:18:43'),
(5, 'INV202510080005', 8, 5, 200000.00, 0.00, 0.00, 200000.00, 'pending', 'cash', 'unpaid', '', '2025-10-08 06:49:34', NULL, '2025-10-15', '2025-10-08 06:49:34', '2025-10-08 06:49:34'),
(6, 'INV202510090006', 3, 10, 200000.00, 0.00, 0.00, 200000.00, 'pending', 'cash', 'unpaid', '', '2025-10-09 03:54:19', NULL, '2025-10-16', '2025-10-09 03:54:19', '2025-10-09 03:54:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `item_type` enum('consultation','medicine','test','procedure','other') NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_type`, `item_id`, `description`, `quantity`, `unit_price`, `total_price`, `created_at`) VALUES
(1, 1, 'consultation', NULL, 'Phí khám Mắt', 1, 170000.00, 170000.00, '2025-10-04 07:41:35'),
(2, 2, 'other', NULL, 'Phí hủy lịch khám muộn', 1, 80000.00, 80000.00, '2025-10-04 08:25:21'),
(3, 3, 'other', NULL, 'Phí hủy lịch khám muộn', 1, 80000.00, 80000.00, '2025-10-04 08:36:57'),
(4, 4, 'consultation', NULL, 'Phí khám Nội khoa', 1, 150000.00, 150000.00, '2025-10-06 06:18:43'),
(5, 5, 'consultation', NULL, 'Phí khám Tim mạch', 1, 200000.00, 200000.00, '2025-10-08 06:49:34'),
(6, 6, 'consultation', NULL, 'kahsm', 1, 200000.00, 200000.00, '2025-10-09 03:54:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `medical_records`
--

CREATE TABLE `medical_records` (
  `id` int(11) NOT NULL,
  `record_code` varchar(20) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `visit_date` date NOT NULL,
  `chief_complaint` text DEFAULT NULL,
  `symptoms` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `test_results` text DEFAULT NULL,
  `diagnosis` text NOT NULL,
  `treatment_plan` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `vital_signs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vital_signs`)),
  `attachments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `medical_records`
--

INSERT INTO `medical_records` (`id`, `record_code`, `patient_id`, `doctor_id`, `appointment_id`, `visit_date`, `chief_complaint`, `symptoms`, `treatment`, `prescription`, `test_results`, `diagnosis`, `treatment_plan`, `notes`, `follow_up_date`, `vital_signs`, `attachments`, `created_at`, `updated_at`) VALUES
(1, 'MR000001', 1, 5, NULL, '2025-10-04', NULL, 'ho', 'dsdsa', NULL, NULL, ' suốt', NULL, 'sadsad', NULL, NULL, NULL, '2025-10-04 10:23:19', '2025-10-04 10:23:19'),
(2, 'MR000002', 1, 2, NULL, '2025-10-07', NULL, 'ho cần phẩu thuật', 'mỗ', NULL, NULL, 'mắc ung thư ', NULL, 'bình tĩnh ', NULL, NULL, NULL, '2025-10-06 06:20:26', '2025-10-06 06:20:26');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `medicines`
--

CREATE TABLE `medicines` (
  `id` int(11) NOT NULL,
  `medicine_code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `generic_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `dosage_form` varchar(50) DEFAULT NULL,
  `strength` varchar(50) DEFAULT NULL,
  `manufacturer` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `stock_quantity` int(11) DEFAULT 0,
  `min_stock_level` int(11) DEFAULT 10,
  `expiry_alert_days` int(11) DEFAULT 30,
  `requires_prescription` tinyint(1) DEFAULT 1,
  `side_effects` text DEFAULT NULL,
  `contraindications` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `medicines`
--

INSERT INTO `medicines` (`id`, `medicine_code`, `name`, `generic_name`, `description`, `category`, `unit`, `dosage_form`, `strength`, `manufacturer`, `price`, `stock_quantity`, `min_stock_level`, `expiry_alert_days`, `requires_prescription`, `side_effects`, `contraindications`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'MED001', 'Paracetamol 500mg', 'Paracetamol', NULL, 'Giảm đau, hạ sốt', 'Viên', 'Viên nén', '500mg', 'Công ty Dược A', 2000.00, 1000, 10, 30, 1, NULL, NULL, 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(2, 'MED002', 'Amoxicillin 500mg', 'Amoxicillin', NULL, 'Kháng sinh', 'Viên', 'Viên nang', '500mg', 'Công ty Dược B', 5000.00, 500, 10, 30, 1, NULL, NULL, 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(3, 'MED003', 'Vitamin C 1000mg', 'Ascorbic Acid', NULL, 'Vitamin', 'Viên', 'Viên sủi', '1000mg', 'Công ty Dược C', 3000.00, 800, 10, 30, 1, NULL, NULL, 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(4, 'MED004', 'Omeprazole 20mg', 'Omeprazole', NULL, 'Tiêu hóa', 'Viên', 'Viên nang', '20mg', 'Công ty Dược D', 4000.00, 600, 10, 30, 1, NULL, NULL, 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(5, 'MED005', 'Cetirizine 10mg', 'Cetirizine', NULL, 'Chống dị ứng', 'Viên', 'Viên nén', '10mg', 'Công ty Dược E', 1500.00, 700, 10, 30, 1, NULL, NULL, 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12');

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `package_prices`
-- (See below for the actual view)
--
CREATE TABLE `package_prices` (
`package_id` int(11)
,`package_name` varchar(255)
,`gender_requirement` enum('both','male','female')
,`total_services` bigint(21)
,`total_price` decimal(32,2)
,`required_price` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `package_services`
--

CREATE TABLE `package_services` (
  `id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `service_price` decimal(10,2) DEFAULT 0.00,
  `duration_minutes` int(11) DEFAULT 30,
  `service_category` enum('general','blood_test','urine_test','imaging','specialist','other') DEFAULT 'general',
  `is_required` tinyint(1) DEFAULT 1,
  `gender_specific` enum('both','male','female') DEFAULT 'both',
  `notes` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `package_services`
--

INSERT INTO `package_services` (`id`, `package_id`, `service_name`, `service_price`, `service_category`, `is_required`, `gender_specific`, `notes`, `display_order`, `created_at`) VALUES
(1, 1, 'Chụp X quang ngực thẳng số hóa (1 phim)', 500000.00, 'imaging', 1, 'both', NULL, 1, '2025-10-29 03:35:33'),
(2, 1, 'Siêu âm ổ bụng (gan mật, tụy, lách, thận, bàng quang)', 400000.00, 'imaging', 1, 'both', NULL, 2, '2025-10-29 03:35:33'),
(3, 1, 'Siêu âm vú', 400000.00, 'imaging', 1, 'male', NULL, 3, '2025-10-29 03:35:33'),
(4, 1, 'Siêu âm tuyến giáp', 400000.00, 'imaging', 1, 'both', NULL, 4, '2025-10-29 03:35:33'),
(5, 1, 'Điện tim ECG', 500000.00, 'general', 1, 'both', NULL, 5, '2025-10-29 03:35:33'),
(6, 1, 'Vị khuẩn nhuộm soi (huyết trắng)', 600000.00, 'specialist', 1, 'both', NULL, 6, '2025-10-29 03:35:33'),
(7, 1, 'Sinh hiệu (Mạch, huyết áp, chiều cao, cân nặng, chỉ số BMI)', 150000.00, 'general', 1, 'both', NULL, 10, '2025-10-29 03:35:33'),
(8, 1, 'Khám Nội tổng quát (BS nội)', 150000.00, 'general', 1, 'both', NULL, 11, '2025-10-29 03:35:33'),
(9, 1, 'Khám Phụ khoa (BS phụ khoa)', 250000.00, 'specialist', 1, 'male', NULL, 12, '2025-10-29 03:35:33'),
(10, 1, 'Tổng phân tích tế bào máu ngoại vi (máy laser)', 200000.00, 'blood_test', 1, 'both', NULL, 20, '2025-10-29 03:35:33'),
(11, 1, 'Tổng phân tích nước tiểu (máy tự động)', 50000.00, 'urine_test', 1, 'both', NULL, 30, '2025-10-29 03:35:33'),
(12, 1, 'Định lượng cholesterol toàn phần', 180000.00, 'blood_test', 1, 'both', NULL, 40, '2025-10-29 03:35:33'),
(13, 1, 'Định lượng HDL-C', 200000.00, 'blood_test', 1, 'both', NULL, 41, '2025-10-29 03:35:33'),
(14, 1, 'Định lượng LDL-C', 200000.00, 'blood_test', 1, 'both', NULL, 42, '2025-10-29 03:35:33'),
(15, 1, 'Định lượng Triglycerid', 200000.00, 'blood_test', 1, 'both', NULL, 43, '2025-10-29 03:35:33'),
(16, 1, 'Đo hoạt độ ALT (GPT)', 200000.00, 'blood_test', 1, 'both', NULL, 50, '2025-10-29 03:35:33'),
(17, 1, 'Đo hoạt độ AST (GOT)', 200000.00, 'blood_test', 1, 'both', NULL, 51, '2025-10-29 03:35:33'),
(18, 1, 'Đo hoạt độ GGT', 200000.00, 'blood_test', 1, 'both', NULL, 52, '2025-10-29 03:35:33'),
(19, 1, 'Định lượng Creatinin máu', 200000.00, 'blood_test', 1, 'both', NULL, 60, '2025-10-29 03:35:33'),
(20, 1, 'Định lượng Ure máu', 200000.00, 'blood_test', 1, 'both', NULL, 61, '2025-10-29 03:35:33'),
(21, 1, 'Định lượng TSH máu', 200000.00, 'blood_test', 1, 'both', NULL, 70, '2025-10-29 03:35:33'),
(22, 1, 'Định lượng FT4 máu', 200000.00, 'blood_test', 1, 'both', NULL, 71, '2025-10-29 03:35:33'),
(23, 1, 'Định lượng Acid Uric máu', 90000.00, 'blood_test', 1, 'both', NULL, 80, '2025-10-29 03:35:33'),
(24, 1, 'Định lượng Glucose', 60000.00, 'blood_test', 1, 'both', NULL, 90, '2025-10-29 03:35:33'),
(25, 1, 'Định lượng HbA1c', 150000.00, 'blood_test', 1, 'both', NULL, 91, '2025-10-29 03:35:33'),
(26, 1, 'HbsAg miễn dịch tự động', 200000.00, 'blood_test', 1, 'both', NULL, 100, '2025-10-29 03:35:33'),
(27, 1, 'HbsAb miễn dịch tự động', 200000.00, 'blood_test', 1, 'both', NULL, 101, '2025-10-29 03:35:33'),
(28, 1, 'HCV Ab miễn dịch tự động', 200000.00, 'blood_test', 1, 'both', NULL, 102, '2025-10-29 03:35:33'),
(29, 2, 'Chụp X quang ngực thẳng số hóa (1 phim)', 500000.00, 'imaging', 1, 'both', NULL, 1, '2025-10-29 03:35:33'),
(30, 2, 'Siêu âm ổ bụng (gan mật, tụy, lách, thận, bàng quang)', 400000.00, 'imaging', 1, 'both', NULL, 2, '2025-10-29 03:35:33'),
(31, 2, 'Siêu âm vú', 400000.00, 'imaging', 1, 'female', NULL, 3, '2025-10-29 03:35:33'),
(32, 2, 'Siêu âm tuyến giáp', 400000.00, 'imaging', 1, 'both', NULL, 4, '2025-10-29 03:35:33'),
(33, 2, 'Điện tim ECG', 300000.00, 'general', 1, 'both', NULL, 5, '2025-10-29 03:35:33'),
(34, 2, 'Vị khuẩn nhuộm soi (huyết trắng)', 300000.00, 'specialist', 1, 'female', NULL, 6, '2025-10-29 03:35:33'),
(35, 2, 'Sinh hiệu (Mạch, huyết áp, chiều cao, cân nặng, chỉ số BMI)', 150000.00, 'general', 1, 'both', NULL, 10, '2025-10-29 03:35:33'),
(36, 2, 'Khám Nội tổng quát (BS nội)', 150000.00, 'general', 1, 'both', NULL, 11, '2025-10-29 03:35:33'),
(37, 2, 'Khám Phụ khoa (BS phụ khoa)', 250000.00, 'specialist', 1, 'female', NULL, 12, '2025-10-29 03:35:33'),
(38, 2, 'Tổng phân tích tế bào máu ngoại vi (máy laser)', 200000.00, 'blood_test', 1, 'both', NULL, 20, '2025-10-29 03:35:33'),
(39, 2, 'Tổng phân tích nước tiểu (máy tự động)', 50000.00, 'urine_test', 1, 'both', NULL, 30, '2025-10-29 03:35:33'),
(40, 2, 'Định lượng cholesterol toàn phần', 180000.00, 'blood_test', 1, 'both', NULL, 40, '2025-10-29 03:35:33'),
(41, 2, 'Định lượng HDL-C', 200000.00, 'blood_test', 1, 'both', NULL, 41, '2025-10-29 03:35:33'),
(42, 2, 'Định lượng LDL-C', 200000.00, 'blood_test', 1, 'both', NULL, 42, '2025-10-29 03:35:33'),
(43, 2, 'Định lượng Triglycerid', 200000.00, 'blood_test', 1, 'both', NULL, 43, '2025-10-29 03:35:33'),
(44, 2, 'Đo hoạt độ ALT (GPT)', 200000.00, 'blood_test', 1, 'both', NULL, 50, '2025-10-29 03:35:33'),
(45, 2, 'Đo hoạt độ AST (GOT)', 200000.00, 'blood_test', 1, 'both', NULL, 51, '2025-10-29 03:35:33'),
(46, 2, 'Đo hoạt độ GGT', 200000.00, 'blood_test', 1, 'both', NULL, 52, '2025-10-29 03:35:33'),
(47, 2, 'Định lượng Creatinin máu', 200000.00, 'blood_test', 1, 'both', NULL, 60, '2025-10-29 03:35:33'),
(48, 2, 'Định lượng Ure máu', 200000.00, 'blood_test', 1, 'both', NULL, 61, '2025-10-29 03:35:33'),
(49, 2, 'Định lượng TSH máu', 200000.00, 'blood_test', 1, 'both', NULL, 70, '2025-10-29 03:35:33'),
(50, 2, 'Định lượng FT4 máu', 200000.00, 'blood_test', 1, 'both', NULL, 71, '2025-10-29 03:35:33'),
(51, 2, 'Định lượng Acid Uric máu', 90000.00, 'blood_test', 1, 'both', NULL, 80, '2025-10-29 03:35:33'),
(52, 2, 'Định lượng Glucose', 60000.00, 'blood_test', 1, 'both', NULL, 90, '2025-10-29 03:35:33'),
(53, 2, 'Định lượng HbA1c', 150000.00, 'blood_test', 1, 'both', NULL, 91, '2025-10-29 03:35:33'),
(54, 2, 'HbsAg miễn dịch tự động', 200000.00, 'blood_test', 1, 'both', NULL, 100, '2025-10-29 03:35:33'),
(55, 2, 'HbsAb miễn dịch tự động', 200000.00, 'blood_test', 1, 'both', NULL, 101, '2025-10-29 03:35:33'),
(56, 2, 'HCV Ab miễn dịch tự động', 200000.00, 'blood_test', 1, 'both', NULL, 102, '2025-10-29 03:35:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `package_test_results`
--

CREATE TABLE `package_test_results` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `result_value` varchar(255) DEFAULT NULL,
  `result_status` enum('normal','abnormal','pending') DEFAULT 'pending',
  `reference_range` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `tested_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `patient_code` varchar(20) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `emergency_contact` varchar(100) DEFAULT NULL,
  `emergency_phone` varchar(20) DEFAULT NULL,
  `insurance_number` varchar(50) DEFAULT NULL,
  `insurance_provider` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `patients`
--

INSERT INTO `patients` (`id`, `user_id`, `patient_code`, `date_of_birth`, `gender`, `address`, `blood_type`, `allergies`, `medical_history`, `emergency_contact`, `emergency_phone`, `insurance_number`, `insurance_provider`, `updated_at`) VALUES
(1, 9, 'PAT001', '1990-05-15', 'male', '123 Đường ABC, Quận 1, TP.HCM', 'A+', 'gà,mèo', NULL, 'Phạm Thị F', '0923456789', NULL, NULL, '2025-10-31 03:29:05'),
(2, 10, 'PAT002', '1985-08-20', 'female', '456 Đường XYZ, Quận 2, TP.HCM', 'A+', NULL, NULL, 'Hoàng Văn G', '0923456780', NULL, NULL, '2025-10-04 09:53:52'),
(3, 13, 'PAT00003', '2003-03-13', 'male', '116/3a\r\n67/32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-04 09:53:52'),
(4, 14, 'PAT00004', NULL, NULL, NULL, NULL, NULL, NULL, '0973438567', NULL, NULL, NULL, '2025-10-04 09:11:47'),
(5, 15, 'PAT00005', '2018-02-10', 'male', 'le loi', NULL, NULL, NULL, '03821321', NULL, NULL, NULL, '2025-10-04 09:44:32'),
(6, 16, 'PAT00006', '2025-10-17', 'male', 'le loi', NULL, NULL, NULL, '0973438567', NULL, NULL, NULL, '2025-10-04 09:56:43'),
(7, 17, 'PAT00007', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-06 02:57:41'),
(8, 18, 'PAT00008', '2023-03-08', 'male', 'le loi1456', NULL, NULL, NULL, '093123213', NULL, NULL, NULL, '2025-10-06 06:24:14'),
(9, 19, 'PAT00009', '2003-09-09', 'male', 'Nhà 8', '', '', NULL, 'đasad', 'đasa', NULL, NULL, '2025-10-07 07:59:33'),
(10, 21, 'PAT00010', '2025-10-10', 'male', 'le loi', NULL, NULL, NULL, '0956474834', NULL, NULL, NULL, '2025-10-09 03:31:31'),
(11, 28, 'PAT00011', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-22 09:08:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `payment_code` varchar(20) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','momo','vnpay','bank_transfer') NOT NULL,
  `payment_status` enum('pending','success','failed','refunded') DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `gateway_response` text DEFAULT NULL,
  `payment_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `payments`
--

INSERT INTO `payments` (`id`, `payment_code`, `invoice_id`, `amount`, `payment_method`, `payment_status`, `transaction_id`, `gateway_response`, `payment_date`, `created_at`, `updated_at`) VALUES
(1, 'PAY202510040001', 1, 173000.00, 'momo', 'pending', NULL, NULL, NULL, '2025-10-04 07:45:20', '2025-10-04 07:45:20'),
(2, 'PAY202510040002', 1, 173000.00, 'cash', 'success', NULL, NULL, '2025-10-04 07:45:26', '2025-10-04 07:45:26', '2025-10-04 07:45:26'),
(3, 'PAY202510060003', 2, 80000.00, 'momo', 'pending', NULL, NULL, NULL, '2025-10-06 06:28:50', '2025-10-06 06:28:50'),
(4, 'PAY202510060004', 2, 80000.00, 'vnpay', 'pending', NULL, NULL, NULL, '2025-10-06 06:28:54', '2025-10-06 06:28:54'),
(5, 'PAY202510060005', 2, 80000.00, 'vnpay', 'pending', NULL, NULL, NULL, '2025-10-06 06:29:13', '2025-10-06 06:29:13'),
(6, 'PAY202510060006', 2, 80000.00, 'vnpay', 'pending', NULL, NULL, NULL, '2025-10-06 06:29:16', '2025-10-06 06:29:16'),
(7, 'PAY202510060007', 2, 80000.00, 'momo', 'pending', NULL, NULL, NULL, '2025-10-06 06:29:22', '2025-10-06 06:29:22'),
(8, 'PAY202510060008', 2, 80000.00, 'momo', 'pending', NULL, NULL, NULL, '2025-10-06 06:32:46', '2025-10-06 06:32:46'),
(9, 'PAY202510060009', 2, 80000.00, 'cash', 'pending', NULL, NULL, NULL, '2025-10-06 06:41:01', '2025-10-06 06:41:01'),
(10, 'PAY202510060010', 4, 150000.00, 'cash', 'pending', NULL, NULL, NULL, '2025-10-06 06:54:09', '2025-10-06 06:54:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL,
  `prescription_code` varchar(20) NOT NULL,
  `medical_record_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `dosage` varchar(100) NOT NULL,
  `frequency` varchar(100) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `instructions` text DEFAULT NULL,
  `route` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `refills_allowed` int(11) DEFAULT 0,
  `refills_remaining` int(11) DEFAULT 0,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `specializations`
--

CREATE TABLE `specializations` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `min_age` int(11) DEFAULT 0,
  `max_age` int(11) DEFAULT 150,
  `gender_requirement` enum('male','female','both') DEFAULT 'both',
  `icon` varchar(50) DEFAULT 'fa-stethoscope',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `specializations`
--

INSERT INTO `specializations` (`id`, `name`, `description`, `min_age`, `max_age`, `gender_requirement`, `icon`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Tim mạch', 'Chuyên khoa tim mạch, điều trị các bệnh về tim và mạch máu', 1, 100, 'both', 'fa-heartbeat', 1, '2025-10-04 05:42:12', '2025-10-10 06:55:03'),
(2, 'Nội khoa', 'Chuyên khoa nội tổng quát', 1, 80, 'both', 'fa-stethoscope', 1, '2025-10-04 05:42:12', '2025-10-10 06:54:19'),
(3, 'Nhi khoa', 'Chuyên khoa nhi, điều trị cho trẻ em', 0, 15, 'both', 'fa-baby', 1, '2025-10-04 05:42:12', '2025-10-10 06:56:42'),
(4, 'Lão khoa', 'Chuyên khoa chăm sóc người cao tuổi', 50, 80, 'both', 'fa-user-clock', 1, '2025-10-04 05:42:12', '2025-10-10 06:53:39'),
(5, 'Sản phụ khoa', 'Chuyên khoa phụ nữ và thai sản', 15, 60, 'female', 'fa-female', 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(6, 'Nam khoa', 'Chuyên khoa nam giới', 18, 150, 'male', 'fa-male', 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(7, 'Da liễu', 'Chuyên khoa da liễu, làm đẹp', 5, 63, 'both', 'fa-hand-sparkles', 1, '2025-10-04 05:42:12', '2025-10-10 06:51:27'),
(8, 'Tai mũi họng', 'Chuyên khoa tai mũi họng', 1, 100, 'both', 'fa-head-side-cough', 1, '2025-10-04 05:42:12', '2025-10-10 06:54:53'),
(9, 'Mắt', 'Chuyên khoa mắt', 4, 80, 'both', 'fa-eye', 1, '2025-10-04 05:42:12', '2025-10-10 06:53:59'),
(10, 'Răng hàm mặt', 'Chuyên khoa răng hàm mặt', 1, 100, 'both', 'fa-tooth', 1, '2025-10-04 05:42:12', '2025-10-10 06:54:31'),
(11, 'Chấn thương , chỉnh hình', 'Chuyên khoa điệu trị các bệnh lý về xương , khớp ', 1, 100, 'both', 'fa-stethoscope', 1, '2025-10-10 07:08:47', '2025-10-10 07:08:47');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT 'other',
  `address` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `role` enum('admin','doctor','patient','staff','receptionist') DEFAULT 'patient',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `date_of_birth`, `gender`, `address`, `avatar`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '0123456789', '1980-01-01', 'male', '123 Admin St, TP.HCM', NULL, 'admin', 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(2, 'dr.nguyen', 'nguyen@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'BS. Nguyễn Văn A', '0987654321', '1975-05-15', 'male', '456 Doctor St, TP.HCM', NULL, 'doctor', 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(3, 'dr.tran', 'tran@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'BS. Trần Thị B', '0987654322', '1980-08-20', 'female', '789 Doctor St, TP.HCM', NULL, 'doctor', 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(4, 'dr.le', 'le@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'BS. Lê Văn C', '0987654323', '1978-03-10', 'male', '321 Doctor St, TP.HCM', NULL, 'doctor', 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(5, 'dr.pham', 'pham@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'BS. Phạm Thị D', '0987654324', '1985-11-25', 'female', '654 Doctor St, TP.HCM', NULL, 'doctor', 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(6, 'dr.hoang', 'hoang@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'BS. Hoàng Văn E', '0987654325', '1982-07-30', 'male', '987 Doctor St, TP.HCM', NULL, 'doctor', 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(7, 'dr.vu', 'vu@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'BS. Vũ Thị F', '0987654326', '1988-02-14', 'female', '147 Doctor St, TP.HCM', NULL, 'doctor', 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(8, 'dr.dang', 'dang@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'BS. Đặng Văn G', '0987654327', '1979-12-05', 'male', '258 Doctor St, TP.HCM', NULL, 'doctor', 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(9, 'patient1', 'patient1@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Văn D', '0912345678', '1990-05-15', 'male', '123 Đường ABC, Quận 1, TP.HCM', NULL, 'patient', 1, '2025-10-04 05:42:12', '2025-10-31 03:29:05'),
(10, 'patient2', 'patient2@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hoàng Thị E', '0912345679', '1985-08-20', 'female', '456 Đường XYZ, Quận 2, TP.HCM', NULL, 'patient', 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(11, 'truong123', 'Truongpham@gmail.com', '$2y$10$lrYJOWIlBucF6rpv4MsmqOZ.7Cdc/xmwkDm2F8Z796qKta4OovGn2', 'Phạm Quang Trường', '0973436483', NULL, 'other', NULL, NULL, 'patient', 1, '2025-10-04 06:14:05', '2025-10-04 06:14:05'),
(12, 'truong789', 'truongpham12032003@gmail.com', '$2y$10$u918Z51pNso8Sq9XEinVHeFxlygsCj72B34kTQnJsDTd7N/Nn/WLa', 'Phạm Quang Trường', '0973436483', '2003-03-12', 'female', '116/3a\r\n67/32', NULL, 'patient', 1, '2025-10-04 06:19:32', '2025-10-04 06:19:32'),
(13, 'truongquang', 'quanly@gmail.com', '$2y$10$5T8t8he4M8Dgavnkpsz.NOgvp5Cxy8WMcLEompRKqLH6MvuyCbqjm', 'Hà Thị Tâm', '0123456785', '2003-03-13', 'male', '116/3a\r\n67/32', NULL, 'patient', 1, '2025-10-04 06:22:25', '2025-10-04 06:22:25'),
(14, 'patient_1759569106', 'An@gmail.com', '$2y$10$godvlAIvAJr2CvVzOj3ZEu/Q96Io8/PmYe3W.LAGzAqLg6Uma6hhm', 'Phạm Đình An', '0973438567', NULL, NULL, NULL, NULL, 'patient', 1, '2025-10-04 09:11:47', '2025-10-04 09:11:47'),
(15, 'patient_1759571072', 'duong@gamil.com', '$2y$10$JQe.4RX/dCLPMZ1.Jk0mOe4TrVpoq8rWryELXEqIkTubmN0bh5c0m', 'Binh Duong', '03821321', NULL, NULL, NULL, NULL, 'patient', 1, '2025-10-04 09:44:32', '2025-10-04 09:44:32'),
(16, 'patient_1759571803', 'An111@gmail.com', '$2y$10$0BsUCKD0XK9LNsccfO.L9OcrYNvcXA3sxWYqSPkpygS4jDMPcln7O', 'Phạm An', '0973438567', '2025-10-17', 'male', 'le loi', NULL, 'patient', 1, '2025-10-04 09:56:43', '2025-10-04 09:56:43'),
(17, 'truong7890', 'truongdeptrai@gmail.com', '$2y$10$zmv75E6OfnduuAQvuH3pTujjtyDx0H3G9QRh5GKl81elm67EI9MIC', 'Dinh Huy', '0973733213', '2000-03-06', 'male', '37/Tân Phú', NULL, 'patient', 1, '2025-10-06 02:57:41', '2025-10-06 02:57:41'),
(18, 'patient_1759731854', 'huy@gmail.com', '$2y$10$wzV4MD50ZtWMPqn3hN4eEuddpRt/P3AFOnseWEafqUf0rQyoBkIzK', 'Đình Huy', '093123213', '2023-03-08', 'male', 'le loi1456', NULL, 'patient', 1, '2025-10-06 06:24:14', '2025-10-06 06:24:14'),
(19, 'vanh', 'vanh@gmail.com', '$2y$10$84t1U8FNzfKdsHM0IG128e5Dr3rnTRgpT1krR3OcYJ1Ruy8KPgXiC', 'Lê Văn Việt Anh', '0967584675', '2003-09-09', 'male', 'Nhà 8', NULL, 'patient', 1, '2025-10-07 04:06:48', '2025-10-07 04:06:48'),
(20, 'letan1', 'letan@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hà Thị Tâm', '0909835121', '1975-05-15', 'male', '116/3A  St, TP.HCM', NULL, 'receptionist', 1, '2025-10-09 03:19:33', '2025-10-09 03:19:33'),
(21, 'patient_1759980691', 'truong@gamil.com', '$2y$10$BowsSO5bqxuwxkC0g5fVsusDvpFMLlqQ4x5iu3nZYYNgLa8B/SY2q', 'Phạm Quang Trường', '0956474834', '2025-10-10', 'male', 'le loi', NULL, 'patient', 1, '2025-10-09 03:31:31', '2025-10-09 03:31:31'),
(26, 'dr.truong', 'truongpham1203@gmail.com', '$2y$10$8Aa7GlfqzBS47vOYXZnSX.RnF8A1ZDN74OctuYJQvPg6eRZqJeo6u', 'BS.Phạm Quang Trường', '0975758586', NULL, NULL, NULL, NULL, 'doctor', 1, '2025-10-09 06:40:45', '2025-10-10 07:15:20'),
(27, 'dr', 'vanh11@gmail.com', '$2y$10$kMtDY94idZYVSdFzOLl9PuoiNhxnjOTNEgtYVXAZdDxb/Z/9PEBqe', 'BS.Vanh Le', '0973436483', NULL, NULL, NULL, NULL, 'doctor', 1, '2025-10-09 07:01:41', '2025-10-10 07:19:04'),
(28, 'truong1234', 'truongpham1203200311@gmail.com', '$2y$10$mIvZiScxryqbU0DRgAsMUenvfNJHp1qWpdfvz69CzDrjgUcvpA71a', 'Phạm Văn Duy', '0964753132', '2008-02-22', 'male', '116/3a\r\n67/32', NULL, 'patient', 1, '2025-10-22 09:08:04', '2025-10-22 09:08:04');

-- --------------------------------------------------------

--
-- Cấu trúc cho view `package_prices`
--
DROP TABLE IF EXISTS `package_prices`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `package_prices`  AS SELECT `hp`.`id` AS `package_id`, `hp`.`name` AS `package_name`, `hp`.`gender_requirement` AS `gender_requirement`, count(`ps`.`id`) AS `total_services`, sum(`ps`.`service_price`) AS `total_price`, sum(case when `ps`.`is_required` = 1 then `ps`.`service_price` else 0 end) AS `required_price` FROM (`health_packages` `hp` left join `package_services` `ps` on(`ps`.`package_id` = `hp`.`id`)) GROUP BY `hp`.`id` ;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `appointment_code` (`appointment_code`),
  ADD KEY `idx_appointment_date` (`appointment_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_patient` (`patient_id`),
  ADD KEY `idx_doctor` (`doctor_id`),
  ADD KEY `idx_package` (`package_id`),
  ADD KEY `idx_type` (`appointment_type`),
  ADD KEY `coordinator_doctor_id` (`coordinator_doctor_id`);

--
-- Chỉ mục cho bảng `appointment_package_services`
--
ALTER TABLE `appointment_package_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_appointment` (`appointment_id`),
  ADD KEY `idx_service` (`service_id`),
  ADD KEY `idx_doctor` (`doctor_id`);

--
-- Chỉ mục cho bảng `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `doctor_code` (`doctor_code`),
  ADD UNIQUE KEY `license_number` (`license_number`),
  ADD KEY `idx_doctor_code` (`doctor_code`),
  ADD KEY `idx_specialization` (`specialization_id`);

--
-- Chỉ mục cho bảng `health_packages`
--
ALTER TABLE `health_packages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `package_code` (`package_code`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_gender` (`gender_requirement`);

--
-- Chỉ mục cho bảng `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_code` (`invoice_code`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `idx_patient` (`patient_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_payment_status` (`payment_status`);

--
-- Chỉ mục cho bảng `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_invoice` (`invoice_id`);

--
-- Chỉ mục cho bảng `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `record_code` (`record_code`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `idx_record_code` (`record_code`),
  ADD KEY `idx_patient` (`patient_id`),
  ADD KEY `idx_doctor` (`doctor_id`),
  ADD KEY `idx_visit_date` (`visit_date`);

--
-- Chỉ mục cho bảng `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `medicine_code` (`medicine_code`),
  ADD KEY `idx_medicine_code` (`medicine_code`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_category` (`category`);

--
-- Chỉ mục cho bảng `package_services`
--
ALTER TABLE `package_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_package` (`package_id`),
  ADD KEY `idx_category` (`service_category`);

--
-- Chỉ mục cho bảng `package_test_results`
--
ALTER TABLE `package_test_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `idx_appointment` (`appointment_id`),
  ADD KEY `idx_status` (`result_status`);

--
-- Chỉ mục cho bảng `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `patient_code` (`patient_code`),
  ADD KEY `idx_patient_code` (`patient_code`);

--
-- Chỉ mục cho bảng `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_code` (`payment_code`),
  ADD KEY `idx_invoice` (`invoice_id`),
  ADD KEY `idx_status` (`payment_status`),
  ADD KEY `idx_transaction` (`transaction_id`);

--
-- Chỉ mục cho bảng `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `prescription_code` (`prescription_code`),
  ADD KEY `idx_medical_record` (`medical_record_id`),
  ADD KEY `idx_medicine` (`medicine_id`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `specializations`
--
ALTER TABLE `specializations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_name` (`name`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `appointment_package_services`
--
ALTER TABLE `appointment_package_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `health_packages`
--
ALTER TABLE `health_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `package_services`
--
ALTER TABLE `package_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT cho bảng `package_test_results`
--
ALTER TABLE `package_test_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `specializations`
--
ALTER TABLE `specializations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`package_id`) REFERENCES `health_packages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointments_ibfk_4` FOREIGN KEY (`coordinator_doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `appointment_package_services`
--
ALTER TABLE `appointment_package_services`
  ADD CONSTRAINT `appointment_package_services_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_package_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `package_services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_package_services_ibfk_3` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doctors_ibfk_2` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`id`);

--
-- Các ràng buộc cho bảng `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medical_records_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medical_records_ibfk_3` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `package_services`
--
ALTER TABLE `package_services`
  ADD CONSTRAINT `package_services_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `health_packages` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `package_test_results`
--
ALTER TABLE `package_test_results`
  ADD CONSTRAINT `package_test_results_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `package_test_results_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `package_services` (`id`);

--
-- Các ràng buộc cho bảng `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`medical_record_id`) REFERENCES `medical_records` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
