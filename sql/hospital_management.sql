-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 07, 2025 lúc 04:56 AM
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
  `doctor_id` int(11) DEFAULT NULL,
  `coordinator_doctor_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `package_appointment_id` int(11) DEFAULT NULL COMMENT 'ID cß╗ºa ─æ─âng k├¢ g├│i kh├ím (nß║┐u appointment n├áy thuß╗Öc 1 g├│i kh├ím)',
  `total_price` decimal(10,2) DEFAULT 0.00,
  `appointment_date` date NOT NULL,
  `appointment_time` time DEFAULT NULL,
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

INSERT INTO `appointments` (`id`, `appointment_code`, `patient_id`, `doctor_id`, `coordinator_doctor_id`, `package_id`, `package_appointment_id`, `total_price`, `appointment_date`, `appointment_time`, `reason`, `status`, `appointment_type`, `notes`, `confirmed_at`, `completed_at`, `cancelled_at`, `cancellation_reason`, `cancellation_fee`, `created_at`, `updated_at`) VALUES
(265, 'APT00001', 14, NULL, NULL, 5, 38, 1270000.00, '2025-12-14', NULL, 'Khám theo gói: Gói khám tầm soát ung thư cơ bản', 'completed', 'package', 'ko', NULL, NULL, NULL, NULL, 0.00, '2025-12-04 13:47:24', '2025-12-06 02:32:37'),
(266, 'APT00002', 14, 1, NULL, 5, 38, 300000.00, '2025-12-14', '08:00:00', 'Khám phát hiện khối u ngoại biên', 'confirmed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-04 13:47:50', '2025-12-04 13:47:54'),
(267, 'APT00003', 14, 5, NULL, 5, 38, 50000.00, '2025-12-14', '08:30:00', 'AFP (gan)', 'confirmed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-04 13:47:50', '2025-12-04 13:47:54'),
(268, 'APT00004', 14, 1, NULL, 5, 38, 70000.00, '2025-12-14', '09:00:00', 'CA 19-9 (tụy)', 'completed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-04 13:47:50', '2025-12-06 06:32:25'),
(269, 'APT00005', 14, 5, NULL, 5, 38, 100000.00, '2025-12-14', '09:30:00', 'CEA (đại tràng)', 'confirmed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-04 13:47:50', '2025-12-04 13:47:54'),
(270, 'APT00006', 14, 1, NULL, 5, 38, 200000.00, '2025-12-14', '10:00:00', 'Siêu âm toàn bộ ổ bụng', 'confirmed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-04 13:47:50', '2025-12-04 13:47:54'),
(271, 'APT00007', 14, 5, NULL, 5, 38, 150000.00, '2025-12-14', '10:30:00', 'Siêu âm tuyến giáp', 'confirmed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-04 13:47:50', '2025-12-04 13:47:54'),
(272, 'APT00008', 14, 1, NULL, 5, 38, 300000.00, '2025-12-14', '11:00:00', 'X-quang ngực', 'completed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-04 13:47:50', '2025-12-04 13:48:12'),
(273, 'APT00009', 14, 5, NULL, 5, 38, 100000.00, '2025-12-14', '11:30:00', 'Tư vấn nguy cơ ung thư theo tuổi', 'confirmed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-04 13:47:50', '2025-12-04 13:47:54'),
(274, 'APT00010', 14, NULL, NULL, 5, 39, 1270000.00, '2025-12-07', NULL, 'Khám theo gói: Gói khám tầm soát ung thư cơ bản', 'confirmed', 'package', 'ko', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 03:17:35', '2025-12-06 06:03:20'),
(275, 'APT00011', 14, 1, NULL, 5, 39, 300000.00, '2025-12-07', '08:00:00', 'Khám phát hiện khối u ngoại biên', 'confirmed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 03:18:40', '2025-12-06 06:03:20'),
(276, 'APT00012', 14, 5, NULL, 5, 39, 50000.00, '2025-12-07', '08:30:00', 'AFP (gan)', 'confirmed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 03:18:40', '2025-12-06 06:03:20'),
(277, 'APT00013', 14, 1, NULL, 5, 39, 70000.00, '2025-12-07', '09:00:00', 'CA 19-9 (tụy)', 'confirmed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 03:18:40', '2025-12-06 06:03:20'),
(278, 'APT00014', 14, 5, NULL, 5, 39, 100000.00, '2025-12-07', '09:30:00', 'CEA (đại tràng)', 'confirmed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 03:18:40', '2025-12-06 06:03:20'),
(279, 'APT00015', 14, 1, NULL, 5, 39, 200000.00, '2025-12-07', '10:00:00', 'Siêu âm toàn bộ ổ bụng', 'confirmed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 03:18:40', '2025-12-06 06:03:20'),
(280, 'APT00016', 14, 5, NULL, 5, 39, 150000.00, '2025-12-07', '10:30:00', 'Siêu âm tuyến giáp', 'confirmed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 03:18:40', '2025-12-06 06:03:20'),
(281, 'APT00017', 14, 1, NULL, 5, 39, 300000.00, '2025-12-07', '11:00:00', 'X-quang ngực', 'confirmed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 03:18:40', '2025-12-06 06:03:20'),
(282, 'APT00018', 14, 5, NULL, 5, 39, 100000.00, '2025-12-07', '11:30:00', 'Tư vấn nguy cơ ung thư theo tuổi', 'confirmed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 03:18:40', '2025-12-06 06:03:20'),
(283, 'APT00019', 14, NULL, NULL, 5, 40, 1270000.00, '2025-12-19', NULL, 'Khám theo gói: Gói khám tầm soát ung thư cơ bản', 'confirmed', 'package', 'ko', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 03:21:42', '2025-12-06 03:31:06'),
(284, 'APT00020', 14, 1, NULL, 5, 40, 300000.00, '2025-12-19', '08:00:00', 'Khám phát hiện khối u ngoại biên', 'completed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 03:30:51', '2025-12-06 03:53:57'),
(285, 'APT00021', 14, 5, NULL, 5, 40, 50000.00, '2025-12-19', '08:30:00', 'AFP (gan)', 'confirmed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 03:30:51', '2025-12-06 03:31:06'),
(286, 'APT00022', 14, 1, NULL, 5, 40, 70000.00, '2025-12-19', '09:00:00', 'CA 19-9 (tụy)', 'completed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 03:30:51', '2025-12-06 04:03:12'),
(287, 'APT00023', 14, 5, NULL, 5, 40, 100000.00, '2025-12-19', '09:30:00', 'CEA (đại tràng)', 'confirmed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 03:30:51', '2025-12-06 03:31:06'),
(288, 'APT00024', 14, 1, NULL, 5, 40, 200000.00, '2025-12-19', '10:00:00', 'Siêu âm toàn bộ ổ bụng', 'completed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 03:30:51', '2025-12-06 06:10:37'),
(289, 'APT00025', 14, 5, NULL, 5, 40, 150000.00, '2025-12-19', '10:30:00', 'Siêu âm tuyến giáp', 'confirmed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 03:30:51', '2025-12-06 03:31:06'),
(290, 'APT00026', 14, 1, NULL, 5, 40, 300000.00, '2025-12-19', '11:00:00', 'X-quang ngực', 'completed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 03:30:51', '2025-12-06 03:52:51'),
(291, 'APT00027', 14, 5, NULL, 5, 40, 100000.00, '2025-12-19', '11:30:00', 'Tư vấn nguy cơ ung thư theo tuổi', 'confirmed', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 03:30:51', '2025-12-06 03:31:06'),
(292, 'APT00028', 14, NULL, NULL, 5, 41, 1270000.00, '2025-12-26', NULL, 'Khám theo gói: Gói khám tầm soát ung thư cơ bản', 'confirmed', 'package', 'ko', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 04:29:40', '2025-12-06 05:46:11'),
(293, 'APT00029', 14, NULL, NULL, 5, 42, 1270000.00, '2025-12-18', NULL, 'Khám theo gói: Gói khám tầm soát ung thư cơ bản', 'pending', 'package', 'ko', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 04:59:38', '2025-12-06 04:59:38'),
(294, 'APT00030', 14, 6, NULL, NULL, NULL, 0.00, '2025-12-21', '14:00:00', 'ko', 'pending', 'regular', 'ko', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 05:08:04', '2025-12-06 05:08:04'),
(295, 'APT00031', 14, 5, NULL, NULL, NULL, 0.00, '2025-12-07', '15:00:00', 'ko', 'confirmed', 'regular', 'ko', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 05:11:39', '2025-12-06 06:04:25'),
(296, 'APT00032', 14, 2, NULL, NULL, NULL, 0.00, '2025-12-07', '15:00:00', 'ko', 'pending', 'regular', 'ko', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 05:14:47', '2025-12-06 05:14:47'),
(297, 'APT00033', 14, 7, NULL, NULL, NULL, 190000.00, '2025-12-21', '10:30:00', 'ko', 'completed', 'regular', 'ko', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 05:30:13', '2025-12-06 06:09:18'),
(298, 'APT00034', 14, 1, NULL, 5, 41, 300000.00, '2025-12-26', '08:00:00', 'Khám phát hiện khối u ngoại biên', 'pending', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 05:45:20', '2025-12-06 05:45:20'),
(299, 'APT00035', 14, 5, NULL, 5, 41, 50000.00, '2025-12-26', '08:30:00', 'AFP (gan)', 'pending', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 05:45:20', '2025-12-06 05:45:20'),
(300, 'APT00036', 14, 1, NULL, 5, 41, 70000.00, '2025-12-26', '09:00:00', 'CA 19-9 (tụy)', 'pending', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 05:45:20', '2025-12-06 05:45:20'),
(301, 'APT00037', 14, 5, NULL, 5, 41, 100000.00, '2025-12-26', '09:30:00', 'CEA (đại tràng)', 'pending', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 05:45:20', '2025-12-06 05:45:20'),
(302, 'APT00038', 14, 1, NULL, 5, 41, 200000.00, '2025-12-26', '10:00:00', 'Siêu âm toàn bộ ổ bụng', 'pending', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 05:45:20', '2025-12-06 05:45:20'),
(303, 'APT00039', 14, 5, NULL, 5, 41, 150000.00, '2025-12-26', '10:30:00', 'Siêu âm tuyến giáp', 'pending', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 05:45:20', '2025-12-06 05:45:20'),
(304, 'APT00040', 14, 1, NULL, 5, 41, 300000.00, '2025-12-26', '11:00:00', 'X-quang ngực', 'pending', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 05:45:20', '2025-12-06 05:45:20'),
(305, 'APT00041', 14, 5, NULL, 5, 41, 100000.00, '2025-12-26', '11:30:00', 'Tư vấn nguy cơ ung thư theo tuổi', 'pending', 'package', 'Tự động phân công - Gói khám: Gói khám tầm soát ung thư cơ bản', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 05:45:20', '2025-12-06 05:45:20'),
(306, 'APT00042', 14, 9, NULL, NULL, NULL, 300000.00, '2025-12-07', '15:00:00', 'ko', 'completed', 'regular', 'ko', NULL, NULL, NULL, NULL, 0.00, '2025-12-06 08:23:32', '2025-12-06 08:24:29'),
(307, 'APT00043', 14, NULL, NULL, 5, 43, 1270000.00, '2025-12-14', NULL, 'Khám theo gói: Gói khám tầm soát ung thư cơ bản', 'pending', 'package', 'ko', NULL, NULL, NULL, NULL, 0.00, '2025-12-07 03:50:56', '2025-12-07 03:50:56'),
(308, 'APT00044', 14, NULL, NULL, 5, 44, 1270000.00, '2025-12-19', NULL, 'Khám theo gói: Gói khám tầm soát ung thư cơ bản', 'pending', 'package', 'ko', NULL, NULL, NULL, NULL, 0.00, '2025-12-07 03:51:16', '2025-12-07 03:51:16'),
(309, 'APT00045', 14, NULL, NULL, 5, 45, 1270000.00, '2025-12-19', NULL, 'Khám theo gói: Gói khám tầm soát ung thư cơ bản', 'pending', 'package', 'ko', NULL, NULL, NULL, NULL, 0.00, '2025-12-07 03:51:41', '2025-12-07 03:51:41');

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
  `result_state` enum('draft','submitted','returned','approved') NOT NULL DEFAULT 'draft',
  `result_value` text DEFAULT NULL,
  `result_json` text DEFAULT NULL,
  `result_files` text DEFAULT NULL,
  `review_note` text DEFAULT NULL,
  `result_status` enum('normal','abnormal','pending') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `tested_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `appointment_package_services`
--

INSERT INTO `appointment_package_services` (`id`, `appointment_id`, `service_id`, `service_price`, `doctor_id`, `status`, `result_state`, `result_value`, `result_json`, `result_files`, `review_note`, `result_status`, `notes`, `tested_at`, `created_at`) VALUES
(182, 265, 73, 300000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-04 13:47:24'),
(183, 265, 74, 50000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-04 13:47:24'),
(184, 265, 75, 100000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-04 13:47:24'),
(185, 265, 76, 70000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-04 13:47:24'),
(186, 265, 77, 200000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-04 13:47:24'),
(187, 265, 78, 300000.00, NULL, 'pending', 'approved', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-04 13:47:24'),
(188, 265, 79, 150000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-04 13:47:24'),
(189, 265, 80, 100000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-04 13:47:24'),
(190, 274, 73, 300000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 03:17:35'),
(191, 274, 74, 50000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 03:17:35'),
(192, 274, 75, 100000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 03:17:35'),
(193, 274, 76, 70000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 03:17:35'),
(194, 274, 77, 200000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 03:17:35'),
(195, 274, 78, 300000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 03:17:35'),
(196, 274, 79, 150000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 03:17:35'),
(197, 274, 80, 100000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 03:17:35'),
(198, 283, 73, 300000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 03:21:42'),
(199, 283, 74, 50000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 03:21:42'),
(200, 283, 75, 100000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 03:21:42'),
(201, 283, 76, 70000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 03:21:42'),
(202, 283, 77, 200000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 03:21:42'),
(203, 283, 78, 300000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 03:21:42'),
(204, 283, 79, 150000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 03:21:42'),
(205, 283, 80, 100000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 03:21:42'),
(206, 292, 73, 300000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 04:29:40'),
(207, 292, 74, 50000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 04:29:40'),
(208, 292, 75, 100000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 04:29:40'),
(209, 292, 76, 70000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 04:29:40'),
(210, 292, 77, 200000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 04:29:40'),
(211, 292, 78, 300000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 04:29:40'),
(212, 292, 79, 150000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 04:29:40'),
(213, 292, 80, 100000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 04:29:40'),
(214, 293, 73, 300000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 04:59:38'),
(215, 293, 74, 50000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 04:59:38'),
(216, 293, 75, 100000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 04:59:38'),
(217, 293, 76, 70000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 04:59:39'),
(218, 293, 77, 200000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 04:59:39'),
(219, 293, 78, 300000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 04:59:39'),
(220, 293, 79, 150000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 04:59:39'),
(221, 293, 80, 100000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-06 04:59:39'),
(222, 307, 73, 300000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:50:56'),
(223, 307, 74, 50000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:50:56'),
(224, 307, 75, 100000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:50:56'),
(225, 307, 76, 70000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:50:56'),
(226, 307, 77, 200000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:50:56'),
(227, 307, 78, 300000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:50:56'),
(228, 307, 79, 150000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:50:56'),
(229, 307, 80, 100000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:50:56'),
(230, 308, 73, 300000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:51:16'),
(231, 308, 74, 50000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:51:16'),
(232, 308, 75, 100000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:51:16'),
(233, 308, 76, 70000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:51:16'),
(234, 308, 77, 200000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:51:16'),
(235, 308, 78, 300000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:51:16'),
(236, 308, 79, 150000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:51:16'),
(237, 308, 80, 100000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:51:16'),
(238, 309, 73, 300000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:51:41'),
(239, 309, 74, 50000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:51:41'),
(240, 309, 75, 100000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:51:41'),
(241, 309, 76, 70000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:51:41'),
(242, 309, 77, 200000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:51:41'),
(243, 309, 78, 300000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:51:41'),
(244, 309, 79, 150000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:51:41'),
(245, 309, 80, 100000.00, NULL, 'pending', 'draft', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2025-12-07 03:51:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `appointment_results`
--

CREATE TABLE `appointment_results` (
  `id` int(10) UNSIGNED NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `status` enum('draft','submitted','approved') NOT NULL DEFAULT 'draft',
  `review_note` text DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `appointment_results`
--

INSERT INTO `appointment_results` (`id`, `appointment_id`, `status`, `review_note`, `submitted_at`, `approved_at`, `created_at`, `updated_at`) VALUES
(1, 297, 'submitted', 'ko có vấn đề gì', '2025-12-06 18:21:43', NULL, '2025-12-06 18:21:43', '2025-12-06 18:21:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `appointment_result_items`
--

CREATE TABLE `appointment_result_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `result_id` int(10) UNSIGNED NOT NULL,
  `metric_name` varchar(255) DEFAULT NULL,
  `result_value` varchar(255) DEFAULT NULL,
  `reference_range` varchar(255) DEFAULT NULL,
  `result_status` varchar(50) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `appointment_result_items`
--

INSERT INTO `appointment_result_items` (`id`, `result_id`, `metric_name`, `result_value`, `reference_range`, `result_status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'bình thường', 'tốt', '167-172', 'normal', 'ko', '2025-12-06 18:21:43', '2025-12-06 18:21:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `consultations`
--

CREATE TABLE `consultations` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(12) DEFAULT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `doctor_id` int(10) UNSIGNED DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `status` enum('open','answered','closed') DEFAULT 'open',
  `last_message_at` datetime DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `consultations`
--

INSERT INTO `consultations` (`id`, `code`, `patient_id`, `doctor_id`, `subject`, `status`, `last_message_at`, `created_at`) VALUES
(2, 'CST000001', 14, 9, 'tai biến', 'open', '2025-12-06 20:36:45', '2025-12-06 13:36:44');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `consultation_attachments`
--

CREATE TABLE `consultation_attachments` (
  `id` int(10) UNSIGNED NOT NULL,
  `message_id` int(10) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(10) UNSIGNED DEFAULT 0,
  `mime_type` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `consultation_messages`
--

CREATE TABLE `consultation_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `consultation_id` int(10) UNSIGNED NOT NULL,
  `sender_user_id` int(10) UNSIGNED NOT NULL,
  `message_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `consultation_messages`
--

INSERT INTO `consultation_messages` (`id`, `consultation_id`, `sender_user_id`, `message_text`, `created_at`) VALUES
(1, 1, 31, 'em muons hỏi bác sĩ khám bệnh', '2025-11-21 13:26:17'),
(2, 2, 31, 'bị rối loạn', '2025-12-06 13:36:45');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `diagnoses`
--

CREATE TABLE `diagnoses` (
  `id` int(10) UNSIGNED NOT NULL,
  `appointment_id` int(10) UNSIGNED DEFAULT NULL,
  `package_appointment_id` int(10) UNSIGNED DEFAULT NULL,
  `doctor_id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `primary_icd10` varchar(20) DEFAULT NULL,
  `secondary_icd10` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`secondary_icd10`)),
  `clinical_findings` text DEFAULT NULL,
  `assessment` text DEFAULT NULL,
  `plan` text DEFAULT NULL,
  `status` enum('draft','submitted','approved','finalized') NOT NULL DEFAULT 'draft',
  `signed_by` int(10) UNSIGNED DEFAULT NULL,
  `signed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `diagnoses`
--

INSERT INTO `diagnoses` (`id`, `appointment_id`, `package_appointment_id`, `doctor_id`, `patient_id`, `primary_icd10`, `secondary_icd10`, `clinical_findings`, `assessment`, `plan`, `status`, `signed_by`, `signed_at`, `created_at`, `updated_at`) VALUES
(11, 272, 38, 1, 14, 'ko có gì khác thường', NULL, 'ko', 'ko', NULL, 'approved', 2, '2025-12-04 20:48:32', '2025-12-04 20:48:32', '2025-12-04 20:48:32'),
(12, 297, NULL, 7, 14, 'ko có gì khác thường', NULL, 'ko', 'ko', NULL, 'approved', 8, '2025-12-06 18:26:24', '2025-12-06 18:23:14', '2025-12-06 18:26:24');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cooldown_days` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `health_packages`
--

INSERT INTO `health_packages` (`id`, `package_code`, `name`, `description`, `gender_requirement`, `min_age`, `max_age`, `is_active`, `created_at`, `updated_at`, `cooldown_days`) VALUES
(1, 'PKG0001', 'Gói khám sức khỏe tổng quát - Nam', 'Gói khám toàn diện dành cho nam giới, bao gồm các xét nghiệm cơ bản và chuyên sâu', 'male', 18, 100, 1, '2025-10-29 03:35:33', '2025-10-31 04:24:04', 0),
(2, 'PKG0002', 'Gói khám sức khỏe tổng quát - Nữ', 'Gói khám toàn diện dành cho nữ giới, bao gồm các xét nghiệm cơ bản và chuyên sâu', 'female', 18, 100, 1, '2025-10-29 03:35:33', '2025-11-11 07:16:03', 0),
(3, 'PKG0003', 'Gói khám phụ sản (mẹ bầu và thai nhi)', 'Gói khám phụ sản dành cho phụ nữ mang thai, bao gồm các xét nghiệm, siêu âm và tư vấn định kỳ giúp theo dõi sức khỏe mẹ và thai nhi.', 'female', 18, 80, 1, '2025-11-11 06:58:39', '2025-11-11 06:58:39', 0),
(4, 'PKG0004', 'Gói khám sức khỏe tổng quát Nam-Nữ', 'Gói khám giúp đánh giá toàn diện tình trạng sức khỏe hiện tại, bao gồm xét nghiệm máu, đường huyết, chức năng gan thận, chẩn đoán hình ảnh cơ bản và tư vấn bác sĩ.', 'both', 18, 80, 1, '2025-11-21 07:23:23', '2025-11-21 07:23:23', 0),
(5, 'PKG0005', 'Gói khám tầm soát ung thư cơ bản', 'Gói khám phát hiện sớm các loại ung thư phổ biến (gan, phổi, dạ dày, đại tràng...). Bao gồm xét nghiệm dấu ấn ung thư, siêu âm tổng quát và chẩn đoán hình ảnh.', 'both', 18, 80, 1, '2025-11-21 07:32:57', '2025-11-21 07:32:57', 0);

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
(13, 'INV202512060001', 297, 14, 190000.00, 0.00, 0.00, 190000.00, 'pending', 'cash', 'unpaid', '', '2025-12-06 15:42:21', NULL, '2025-12-13', '2025-12-06 15:42:21', '2025-12-06 15:42:21');

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
(61, 13, 'consultation', NULL, 'Phí khám Răng hàm mặt', 1, 190000.00, 190000.00, '2025-12-06 15:42:21');

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
(5, 'MED005', 'Cetirizine 10mg', 'Cetirizine', NULL, 'Chống dị ứng', 'Viên', 'Viên nén', '10mg', 'Công ty Dược E', 1500.00, 700, 10, 30, 1, NULL, NULL, 1, '2025-10-04 05:42:12', '2025-10-04 05:42:12'),
(6, 'MED006', 'Ibuprofen 400mg', 'Ibuprofen', NULL, 'Giảm đau, kháng viêm', 'Viên', 'Viên nén', '400mg', 'Công ty Dược A', 3500.00, 1200, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(7, 'MED007', 'Naproxen 250mg', 'Naproxen', NULL, 'Giảm đau, kháng viêm', 'Viên', 'Viên nén', '250mg', 'Công ty Dược A', 4500.00, 600, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(8, 'MED008', 'Paracetamol 80mg (trẻ em)', 'Paracetamol', NULL, 'Giảm đau, hạ sốt (Nhi)', 'Viên', 'Viên đặt hậu môn', '80mg', 'Công ty Dược A', 1500.00, 800, 20, 30, 0, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(9, 'MED009', 'Loratadine 10mg', 'Loratadine', NULL, 'Chống dị ứng', 'Viên', 'Viên nén', '10mg', 'Công ty Dược F', 2500.00, 700, 20, 30, 0, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(10, 'MED010', 'Chlorpheniramine 4mg', 'Chlorpheniramine', NULL, 'Chống dị ứng', 'Viên', 'Viên nén', '4mg', 'Công ty Dược F', 1000.00, 900, 20, 30, 0, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(11, 'MED011', 'Xylometazoline 0.1% spray', 'Xylometazoline', NULL, 'Tai mũi họng', 'Lọ', 'Xịt mũi', '0.1%', 'Công ty Dược G', 25000.00, 200, 10, 24, 0, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(12, 'MED012', 'Esomeprazole 20mg', 'Esomeprazole', NULL, 'Tiêu hoá', 'Viên', 'Viên nang', '20mg', 'Công ty Dược D', 6000.00, 500, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(13, 'MED013', 'Domperidone 10mg', 'Domperidone', NULL, 'Tiêu hoá', 'Viên', 'Viên nén', '10mg', 'Công ty Dược D', 2500.00, 600, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(14, 'MED014', 'Drotaverine 40mg', 'Drotaverine', NULL, 'Tiêu hoá - Giảm co thắt', 'Viên', 'Viên nén', '40mg', 'Công ty Dược D', 3000.00, 400, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(15, 'MED015', 'ORS gói (Oresol)', 'Glucose + Electrolytes', NULL, 'Bù nước - tiêu chảy', 'Gói', 'Bột pha', NULL, 'Công ty Dược H', 3000.00, 2000, 50, 24, 0, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(16, 'MED016', 'Loperamide 2mg', 'Loperamide', NULL, 'Tiêu chảy', 'Viên', 'Viên nang', '2mg', 'Công ty Dược H', 2000.00, 500, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(17, 'MED017', 'Salbutamol Inhaler 100mcg', 'Salbutamol', NULL, 'Hô hấp', 'Ống', 'Bơm hít', '100mcg/puff', 'Công ty Dược I', 65000.00, 150, 20, 24, 1, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(18, 'MED018', 'Ambroxol 30mg', 'Ambroxol', NULL, 'Long đờm', 'Viên', 'Viên nén', '30mg', 'Công ty Dược I', 2500.00, 600, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(19, 'MED019', 'Dextromethorphan 15mg/5ml', 'Dextromethorphan', NULL, 'Giảm ho', 'Chai', 'Siro', '15mg/5ml', 'Công ty Dược I', 38000.00, 180, 10, 24, 0, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(20, 'MED020', 'Amlodipine 5mg', 'Amlodipine', NULL, 'Tim mạch', 'Viên', 'Viên nén', '5mg', 'Công ty Dược J', 3000.00, 700, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(21, 'MED021', 'Losartan 50mg', 'Losartan', NULL, 'Tim mạch', 'Viên', 'Viên nén', '50mg', 'Công ty Dược J', 4500.00, 600, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(22, 'MED022', 'Atorvastatin 20mg', 'Atorvastatin', NULL, 'Rối loạn lipid', 'Viên', 'Viên nén', '20mg', 'Công ty Dược J', 7000.00, 500, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(23, 'MED023', 'Metformin 500mg', 'Metformin', NULL, 'Đái tháo đường', 'Viên', 'Viên nén', '500mg', 'Công ty Dược K', 2500.00, 900, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(24, 'MED024', 'Amoxicillin + Clavulanate 1g', 'Amoxicillin/Clavulanic acid', NULL, 'Kháng sinh', 'Viên', 'Viên nén', '875/125mg', 'Công ty Dược B', 12000.00, 400, 20, 24, 1, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(25, 'MED025', 'Azithromycin 500mg', 'Azithromycin', NULL, 'Kháng sinh', 'Viên', 'Viên nén', '500mg', 'Công ty Dược B', 15000.00, 350, 20, 24, 1, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(26, 'MED026', 'Cefuroxime 500mg', 'Cefuroxime', NULL, 'Kháng sinh', 'Viên', 'Viên nén', '500mg', 'Công ty Dược B', 14000.00, 300, 20, 24, 1, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(27, 'MED027', 'Vitamin B-Complex', 'B group vitamins', NULL, 'Vitamin', 'Viên', 'Viên nén', NULL, 'Công ty Dược C', 4000.00, 800, 20, 30, 0, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(28, 'MED028', 'Folic Acid 5mg', 'Folic acid', NULL, 'Phụ sản', 'Viên', 'Viên nén', '5mg', 'Công ty Dược L', 2000.00, 700, 20, 30, 0, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(29, 'MED029', 'Sắt (Ferrous fumarate) 200mg', 'Ferrous fumarate', NULL, 'Phụ sản/Thiếu máu', 'Viên', 'Viên nén', '200mg', 'Công ty Dược L', 3000.00, 700, 20, 30, 0, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(30, 'MED030', 'Calcium + Vitamin D3', 'Calcium carbonate + Cholecalciferol', NULL, 'Xương khớp', 'Viên', 'Viên nén', '600mg/400IU', 'Công ty Dược M', 6000.00, 500, 20, 30, 0, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(31, 'MED031', 'Kẽm gluconate 10mg/5ml', 'Zinc gluconate', NULL, 'Nhi khoa', 'Chai', 'Siro', '10mg/5ml', 'Công ty Dược N', 42000.00, 200, 10, 24, 0, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(32, 'MED032', 'Clotrimazole 1% cream', 'Clotrimazole', NULL, 'Kháng nấm', 'Tuýp', 'Kem bôi', '1%', 'Công ty Dược O', 18000.00, 250, 10, 24, 0, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(33, 'MED033', 'Hydrocortisone 1% cream', 'Hydrocortisone', NULL, 'Da liễu', 'Tuýp', 'Kem bôi', '1%', 'Công ty Dược O', 20000.00, 250, 10, 24, 1, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(34, 'MED034', 'Mupirocin 2% ointment', 'Mupirocin', NULL, 'Da liễu - Kháng khuẩn', 'Tuýp', 'Mỡ bôi', '2%', 'Công ty Dược O', 45000.00, 150, 10, 24, 1, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(35, 'MED035', 'Omeprazole 20mg (gói uống)', 'Omeprazole', NULL, 'Tiêu hoá', 'Gói', 'Bột pha', '20mg', 'Công ty Dược D', 3800.00, 400, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(36, 'MED036', 'ORS kẽm (Oresol + Zinc)', 'Glucose + Electrolytes + Zinc', NULL, 'Bù nước/Nhi', 'Gói', 'Bột pha', NULL, 'Công ty Dược H', 4500.00, 1000, 50, 24, 0, NULL, NULL, 1, '2025-12-04 04:34:00', '2025-12-04 04:34:00'),
(37, 'MED037', 'Diclofenac 50mg', 'Diclofenac', NULL, 'Giảm đau, kháng viêm', 'Viên', 'Viên nén', '50mg', 'Cty Dược A', 3500.00, 600, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(38, 'MED038', 'Acetylcystein 200mg', 'Acetylcysteine', NULL, 'Long đờm', 'Gói', 'Bột pha', '200mg', 'Cty Dược I', 3500.00, 800, 20, 24, 0, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(39, 'MED039', 'Natri Clorid 0.9% 500ml', 'Sodium Chloride', NULL, 'Dịch truyền', 'Chai', 'Dịch truyền', '0.9%', 'Cty Dược P', 18000.00, 120, 10, 12, 1, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(40, 'MED040', 'Fexofenadine 180mg', 'Fexofenadine', NULL, 'Chống dị ứng', 'Viên', 'Viên nén', '180mg', 'Cty Dược F', 7000.00, 400, 20, 30, 0, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(41, 'MED041', 'Mometasone nasal spray', 'Mometasone', NULL, 'Tai mũi họng', 'Lọ', 'Xịt mũi', '50mcg/dose', 'Cty Dược F', 82000.00, 120, 10, 24, 1, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(42, 'MED042', 'Betadine 10% gargle', 'Povidone Iodine', NULL, 'Sát khuẩn họng', 'Chai', 'Dung dịch', '10%', 'Cty Dược Q', 28000.00, 150, 10, 24, 0, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(43, 'MED043', 'Rabeprazole 20mg', 'Rabeprazole', NULL, 'Tiêu hoá', 'Viên', 'Viên nén', '20mg', 'Cty Dược D', 6500.00, 500, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(44, 'MED044', 'Itopride 50mg', 'Itopride', NULL, 'Rối loạn tiêu hoá', 'Viên', 'Viên nén', '50mg', 'Cty Dược D', 6000.00, 350, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(45, 'MED045', 'Simethicone 80mg', 'Simethicone', NULL, 'Chống đầy hơi', 'Viên', 'Viên nhai', '80mg', 'Cty Dược H', 2500.00, 600, 20, 24, 0, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(46, 'MED046', 'Budesonide/Formoterol 160/4.5', 'Budesonide+Formoterol', NULL, 'Hô hấp', 'Ống', 'Bơm hít', '160/4.5mcg', 'Cty Dược I', 185000.00, 90, 10, 24, 1, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(47, 'MED047', 'Montelukast 10mg', 'Montelukast', NULL, 'Hen/Viêm mũi dị ứng', 'Viên', 'Viên nén', '10mg', 'Cty Dược I', 12000.00, 300, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(48, 'MED048', 'Bisoprolol 5mg', 'Bisoprolol', NULL, 'Tim mạch', 'Viên', 'Viên nén', '5mg', 'Cty Dược J', 3500.00, 500, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(49, 'MED049', 'Clopidogrel 75mg', 'Clopidogrel', NULL, 'Kháng kết tập tiểu cầu', 'Viên', 'Viên nén', '75mg', 'Cty Dược J', 9500.00, 250, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(50, 'MED050', 'Perindopril 5mg', 'Perindopril', NULL, 'Tim mạch', 'Viên', 'Viên nén', '5mg', 'Cty Dược J', 6000.00, 300, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(51, 'MED051', 'Gliclazide MR 60mg', 'Gliclazide', NULL, 'Đái tháo đường', 'Viên', 'Viên nén phóng thích biến đổi', '60mg', 'Cty Dược K', 7000.00, 300, 20, 30, 1, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(52, 'MED052', 'Insulin Aspart 100IU/ml', 'Insulin Aspart', NULL, 'Đái tháo đường', 'Bút', 'Dung dịch tiêm', '100IU/ml', 'Cty Dược K', 280000.00, 60, 10, 12, 1, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(53, 'MED053', 'Levofloxacin 500mg', 'Levofloxacin', NULL, 'Kháng sinh', 'Viên', 'Viên nén', '500mg', 'Cty Dược B', 16000.00, 250, 20, 24, 1, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(54, 'MED054', 'Ciprofloxacin 500mg', 'Ciprofloxacin', NULL, 'Kháng sinh', 'Viên', 'Viên nén', '500mg', 'Cty Dược B', 12000.00, 250, 20, 24, 1, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(55, 'MED055', 'Amikacin 500mg/2ml', 'Amikacin', NULL, 'Kháng sinh tiêm', 'Ống', 'Dung dịch tiêm', '500mg/2ml', 'Cty Dược B', 45000.00, 80, 10, 12, 1, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(56, 'MED056', 'Vitamin D3 1000IU', 'Cholecalciferol', NULL, 'Vitamin', 'Viên', 'Viên nang mềm', '1000IU', 'Cty Dược M', 3500.00, 500, 20, 30, 0, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(57, 'MED057', 'Magnesium B6', 'Magnesium + Pyridoxine', NULL, 'Khoáng chất', 'Viên', 'Viên nén', NULL, 'Cty Dược M', 5500.00, 400, 20, 30, 0, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(58, 'MED058', 'Probiotic 5 strains', 'Lactobacillus/Bifido', NULL, 'Tiêu hoá/Nhi', 'Gói', 'Bột pha', NULL, 'Cty Dược H', 8000.00, 300, 20, 24, 0, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(59, 'MED059', 'Sắt (Ferrous sulfate) 325mg', 'Ferrous sulfate', NULL, 'Phụ sản/Thiếu máu', 'Viên', 'Viên nén', '325mg', 'Cty Dược L', 3500.00, 500, 20, 30, 0, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(60, 'MED060', 'Iod bổ sung thai kỳ', 'Iodine', NULL, 'Phụ sản', 'Viên', 'Viên nén', '150mcg', 'Cty Dược L', 3000.00, 400, 20, 30, 0, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(61, 'MED061', 'Ketoconazole 2% shampoo', 'Ketoconazole', NULL, 'Kháng nấm', 'Chai', 'Dầu gội', '2%', 'Cty Dược O', 55000.00, 120, 10, 24, 0, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23'),
(62, 'MED062', 'Fusidic acid 2% cream', 'Fusidic acid', NULL, 'Da liễu - Kháng khuẩn', 'Tuýp', 'Kem bôi', '2%', 'Cty Dược O', 52000.00, 140, 10, 24, 1, NULL, NULL, 1, '2025-12-04 04:43:23', '2025-12-04 04:43:23');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `type` varchar(50) DEFAULT 'reminder',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `link`, `type`, `is_read`, `created_at`) VALUES
(116, 31, 'Đăng ký gói khám thành công', 'Bạn đã đăng ký gói khám Gói khám tầm soát ung thư cơ bản vào ngày 14/12/2025. Vui lòng chờ phân công bác sĩ.', '/package-appointments/38', 'system', 0, '2025-12-04 13:47:24'),
(117, 31, 'Lịch hẹn đã được xác nhận', 'Lịch hẹn theo gói khám của bạn vào 14/12/2025 đã được xác nhận.', '/appointments/265', 'system', 0, '2025-12-04 13:47:54'),
(118, 31, 'Đơn thuốc đã sẵn sàng', 'Đơn thuốc của bạn đã được duyệt.', '/prescriptions/11/export-pdf', 'system', 0, '2025-12-04 13:49:08'),
(119, 31, 'Đăng ký gói khám thành công', 'Bạn đã đăng ký gói khám Gói khám tầm soát ung thư cơ bản vào ngày 07/12/2025. Vui lòng chờ phân công bác sĩ.', '/package-appointments/39', 'system', 0, '2025-12-06 03:17:35'),
(120, 31, 'Đăng ký gói khám thành công', 'Bạn đã đăng ký gói khám Gói khám tầm soát ung thư cơ bản vào ngày 19/12/2025. Vui lòng chờ phân công bác sĩ.', '/package-appointments/40', 'system', 0, '2025-12-06 03:21:42'),
(121, 31, 'Lịch hẹn đã được xác nhận', 'Lịch hẹn theo gói khám của bạn vào 19/12/2025 đã được xác nhận.', '/appointments/283', 'system', 0, '2025-12-06 03:31:06'),
(122, 31, 'Đăng ký gói khám thành công', 'Bạn đã đăng ký gói khám Gói khám tầm soát ung thư cơ bản vào ngày 26/12/2025. Vui lòng chờ phân công bác sĩ.', '/package-appointments/41', 'system', 0, '2025-12-06 04:29:40'),
(123, 31, 'Đăng ký gói khám thành công', 'Bạn đã đăng ký gói khám Gói khám tầm soát ung thư cơ bản vào ngày 18/12/2025. Vui lòng chờ phân công bác sĩ.', '/package-appointments/42', 'system', 0, '2025-12-06 04:59:39'),
(124, 1, 'Có lịch hẹn mới từ bệnh nhân yến ', 'yến  vừa đặt gói khám vào ngày 18/12/2025. Vui lòng kiểm tra và xử lý.', '/package-appointments/42', 'system', 0, '2025-12-06 04:59:41'),
(125, 31, 'Đặt lịch khám thành công', 'Bạn đã đặt lịch khám ngày 21/12/2025 lúc 14:00 với bác sĩ BS. Vũ Thị F. Vui lòng chờ xác nhận.', '/appointments/294', 'system', 0, '2025-12-06 05:08:04'),
(126, 7, 'Bạn có lịch hẹn mới cần xác nhận', 'Bệnh nhân yến  đặt lịch ngày 21/12/2025 lúc 14:00. Vui lòng kiểm tra và xác nhận.', '/appointments/294', 'system', 0, '2025-12-06 05:08:07'),
(127, 1, 'Có lịch hẹn mới từ bệnh nhân yến ', 'yến  vừa đặt lịch khám vào ngày 21/12/2025 lúc 14:00. Vui lòng kiểm tra và xử lý.', '/appointments/294', 'system', 0, '2025-12-06 05:08:10'),
(128, 31, 'Đặt lịch khám thành công', 'Bạn đã đặt lịch khám ngày 07/12/2025 lúc 15:00 với bác sĩ BS. Hoàng Văn E. Vui lòng chờ xác nhận.', '/appointments/295', 'system', 0, '2025-12-06 05:11:39'),
(129, 6, 'Bạn có lịch hẹn mới cần xác nhận', 'Bệnh nhân yến  đặt lịch ngày 07/12/2025 lúc 15:00. Vui lòng kiểm tra và xác nhận.', '/appointments/295', 'system', 0, '2025-12-06 05:11:42'),
(130, 1, 'Có lịch hẹn mới từ bệnh nhân yến ', 'yến  vừa đặt lịch khám vào ngày 07/12/2025 lúc 15:00. Vui lòng kiểm tra và xử lý.', '/appointments/295', 'system', 0, '2025-12-06 05:11:46'),
(131, 31, 'Đặt lịch khám thành công', 'Bạn đã đặt lịch khám ngày 07/12/2025 lúc 15:00 với bác sĩ BS. Trần Thị B. Vui lòng chờ xác nhận.', '/appointments/296', 'system', 0, '2025-12-06 05:14:47'),
(132, 3, 'Bạn có lịch hẹn mới cần xác nhận', 'Bệnh nhân yến  đặt lịch ngày 07/12/2025 lúc 15:00. Vui lòng kiểm tra và xác nhận.', '/appointments/296', 'system', 0, '2025-12-06 05:14:50'),
(133, 1, 'Có lịch hẹn mới từ bệnh nhân yến ', 'yến  vừa đặt lịch khám vào ngày 07/12/2025 lúc 15:00. Vui lòng kiểm tra và xử lý.', '/appointments/296', 'system', 0, '2025-12-06 05:14:53'),
(134, 31, 'Đặt lịch khám thành công', 'Bạn đã đặt lịch khám ngày 21/12/2025 lúc 10:30 với bác sĩ BS. Đặng Văn G. Vui lòng chờ xác nhận.', '/appointments/297', 'system', 0, '2025-12-06 05:30:13'),
(135, 8, 'Bạn có lịch hẹn mới cần xác nhận', 'Bệnh nhân yến  đặt lịch ngày 21/12/2025 lúc 10:30. Vui lòng kiểm tra và xác nhận.', '/appointments/297', 'system', 0, '2025-12-06 05:30:17'),
(136, 1, 'Có lịch hẹn mới từ bệnh nhân yến ', 'yến  vừa đặt lịch khám vào ngày 21/12/2025 lúc 10:30. Vui lòng kiểm tra và xử lý.', '/appointments/297', 'system', 0, '2025-12-06 05:30:20'),
(137, 31, 'Lịch hẹn đã được xác nhận', 'Lịch hẹn của bạn vào 26/12/2025 lúc không rõ giờ với bác sĩ  đã được xác nhận.', '/appointments/292', 'reminder', 0, '2025-12-06 05:46:11'),
(138, 31, 'Lịch hẹn đã được xác nhận', 'Lịch hẹn của bạn vào 07/12/2025 lúc không rõ giờ với bác sĩ  đã được xác nhận.', '/appointments/274', 'reminder', 0, '2025-12-06 06:03:20'),
(139, 31, 'Lịch hẹn đã được xác nhận', 'Lịch hẹn của bạn vào 07/12/2025 lúc 15:00 với bác sĩ BS. Hoàng Văn E đã được xác nhận.', '/appointments/295', 'reminder', 0, '2025-12-06 06:04:25'),
(140, 31, 'Lịch hẹn đã được xác nhận', 'Lịch hẹn của bạn vào 21/12/2025 lúc 10:30 với bác sĩ BS. Đặng Văn G đã được xác nhận.', '/appointments/297', 'reminder', 0, '2025-12-06 06:09:04'),
(141, 31, 'Đặt lịch khám thành công', 'Bạn đã đặt lịch khám ngày 07/12/2025 lúc 15:00 với bác sĩ BS.Phạm Quang Trường. Vui lòng chờ xác nhận.', '/appointments/306', 'system', 0, '2025-12-06 08:23:32'),
(142, 26, 'Bạn có lịch hẹn mới cần xác nhận', 'Bệnh nhân yến  đặt lịch ngày 07/12/2025 lúc 15:00. Vui lòng kiểm tra và xác nhận.', '/appointments/306', 'system', 0, '2025-12-06 08:23:35'),
(143, 1, 'Có lịch hẹn mới từ bệnh nhân yến ', 'yến  vừa đặt lịch khám vào ngày 07/12/2025 lúc 15:00. Vui lòng kiểm tra và xử lý.', '/appointments/306', 'system', 0, '2025-12-06 08:23:38'),
(144, 31, 'Lịch hẹn đã được xác nhận', 'Lịch hẹn của bạn vào 07/12/2025 lúc 15:00 với bác sĩ BS.Phạm Quang Trường đã được xác nhận.', '/appointments/306', 'reminder', 0, '2025-12-06 08:24:12'),
(145, 31, 'Đơn thuốc đã sẵn sàng', 'Đơn thuốc của bạn đã được duyệt.', '/prescriptions/12/export-pdf', 'system', 0, '2025-12-06 11:32:14'),
(146, 26, 'Câu hỏi tư vấn mới', 'Bạn có câu hỏi mới: tai biến', '/consultations/2', 'system', 0, '2025-12-06 13:36:45'),
(147, 31, 'Đăng ký gói khám thành công', 'Bạn đã đăng ký gói khám Gói khám tầm soát ung thư cơ bản vào ngày 14/12/2025. Vui lòng chờ phân công bác sĩ.', '/package-appointments/43', 'system', 0, '2025-12-07 03:50:56'),
(148, 1, 'Có lịch hẹn mới từ bệnh nhân yến ', 'yến  vừa đặt gói khám vào ngày 14/12/2025. Vui lòng kiểm tra và xử lý.', '/package-appointments/43', 'system', 0, '2025-12-07 03:50:59'),
(149, 31, 'Đăng ký gói khám thành công', 'Bạn đã đăng ký gói khám Gói khám tầm soát ung thư cơ bản vào ngày 19/12/2025. Vui lòng chờ phân công bác sĩ.', '/package-appointments/44', 'system', 0, '2025-12-07 03:51:16'),
(150, 1, 'Có lịch hẹn mới từ bệnh nhân yến ', 'yến  vừa đặt gói khám vào ngày 19/12/2025. Vui lòng kiểm tra và xử lý.', '/package-appointments/44', 'system', 0, '2025-12-07 03:51:17'),
(151, 31, 'Đăng ký gói khám thành công', 'Bạn đã đăng ký gói khám Gói khám tầm soát ung thư cơ bản vào ngày 19/12/2025. Vui lòng chờ phân công bác sĩ.', '/package-appointments/45', 'system', 0, '2025-12-07 03:51:41'),
(152, 1, 'Có lịch hẹn mới từ bệnh nhân yến ', 'yến  vừa đặt gói khám vào ngày 19/12/2025. Vui lòng kiểm tra và xử lý.', '/package-appointments/45', 'system', 0, '2025-12-07 03:51:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `package_appointments`
--

CREATE TABLE `package_appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled',
  `final_status` enum('in_progress','awaiting_review','returned','approved') NOT NULL DEFAULT 'in_progress',
  `coordinator_doctor_id` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `final_pdf_path` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT 0.00,
  `created_by` int(11) NOT NULL COMMENT 'User ID cß╗ºa ngã░ß╗Øi tß║ío (receptionist)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `package_appointments`
--

INSERT INTO `package_appointments` (`id`, `patient_id`, `package_id`, `appointment_date`, `status`, `final_status`, `coordinator_doctor_id`, `approved_by`, `approved_at`, `final_pdf_path`, `notes`, `total_price`, `created_by`, `created_at`, `updated_at`) VALUES
(38, 14, 5, '2025-12-14', 'scheduled', 'in_progress', NULL, NULL, NULL, NULL, 'ko', 1270000.00, 31, '2025-12-04 13:47:24', '2025-12-04 13:47:24'),
(39, 14, 5, '2025-12-07', 'scheduled', 'in_progress', NULL, NULL, NULL, NULL, 'ko', 1270000.00, 31, '2025-12-06 03:17:35', '2025-12-06 03:17:35'),
(40, 14, 5, '2025-12-19', 'scheduled', 'in_progress', NULL, NULL, NULL, NULL, 'ko', 1270000.00, 31, '2025-12-06 03:21:42', '2025-12-06 03:21:42'),
(41, 14, 5, '2025-12-26', 'scheduled', 'in_progress', NULL, NULL, NULL, NULL, 'ko', 1270000.00, 31, '2025-12-06 04:29:40', '2025-12-06 04:29:40'),
(42, 14, 5, '2025-12-18', 'scheduled', 'in_progress', NULL, NULL, NULL, NULL, 'ko', 1270000.00, 31, '2025-12-06 04:59:38', '2025-12-06 04:59:38'),
(43, 14, 5, '2025-12-14', 'scheduled', 'in_progress', NULL, NULL, NULL, NULL, 'ko', 1270000.00, 31, '2025-12-07 03:50:56', '2025-12-07 03:50:56'),
(44, 14, 5, '2025-12-19', 'scheduled', 'in_progress', NULL, NULL, NULL, NULL, 'ko', 1270000.00, 31, '2025-12-07 03:51:16', '2025-12-07 03:51:16'),
(45, 14, 5, '2025-12-19', 'scheduled', 'in_progress', NULL, NULL, NULL, NULL, 'ko', 1270000.00, 31, '2025-12-07 03:51:41', '2025-12-07 03:51:41');

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

INSERT INTO `package_services` (`id`, `package_id`, `service_name`, `service_price`, `duration_minutes`, `service_category`, `is_required`, `gender_specific`, `notes`, `display_order`, `created_at`) VALUES
(1, 1, 'Chụp X quang ngực thẳng số hóa (1 phim)', 500000.00, 30, 'imaging', 1, 'both', NULL, 1, '2025-10-28 20:35:33'),
(2, 1, 'Siêu âm ổ bụng (gan mật, tụy, lách, thận, bàng quang)', 400000.00, 30, 'imaging', 1, 'both', NULL, 2, '2025-10-28 20:35:33'),
(3, 1, 'Siêu âm vú', 400000.00, 30, 'imaging', 1, 'male', NULL, 3, '2025-10-28 20:35:33'),
(4, 1, 'Siêu âm tuyến giáp', 400000.00, 30, 'imaging', 1, 'both', NULL, 4, '2025-10-28 20:35:33'),
(5, 1, 'Điện tim ECG', 500000.00, 30, 'general', 1, 'both', NULL, 5, '2025-10-28 20:35:33'),
(6, 1, 'Vị khuẩn nhuộm soi (huyết trắng)', 600000.00, 30, 'specialist', 1, 'both', NULL, 6, '2025-10-28 20:35:33'),
(7, 1, 'Sinh hiệu (Mạch, huyết áp, chiều cao, cân nặng, chỉ số BMI)', 150000.00, 30, 'general', 1, 'both', NULL, 10, '2025-10-28 20:35:33'),
(8, 1, 'Khám Nội tổng quát (BS nội)', 150000.00, 30, 'general', 1, 'both', NULL, 11, '2025-10-28 20:35:33'),
(9, 1, 'Khám Phụ khoa (BS phụ khoa)', 250000.00, 30, 'specialist', 1, 'male', NULL, 12, '2025-10-28 20:35:33'),
(10, 1, 'Tổng phân tích tế bào máu ngoại vi (máy laser)', 200000.00, 30, 'blood_test', 1, 'both', NULL, 20, '2025-10-28 20:35:33'),
(11, 1, 'Tổng phân tích nước tiểu (máy tự động)', 50000.00, 30, 'urine_test', 1, 'both', NULL, 30, '2025-10-28 20:35:33'),
(12, 1, 'Định lượng cholesterol toàn phần', 180000.00, 30, 'blood_test', 1, 'both', NULL, 40, '2025-10-28 20:35:33'),
(13, 1, 'Định lượng HDL-C', 200000.00, 30, 'blood_test', 1, 'both', NULL, 41, '2025-10-28 20:35:33'),
(14, 1, 'Định lượng LDL-C', 200000.00, 30, 'blood_test', 1, 'both', NULL, 42, '2025-10-28 20:35:33'),
(15, 1, 'Định lượng Triglycerid', 200000.00, 30, 'blood_test', 1, 'both', NULL, 43, '2025-10-28 20:35:33'),
(16, 1, 'Đo hoạt độ ALT (GPT)', 200000.00, 30, 'blood_test', 1, 'both', NULL, 50, '2025-10-28 20:35:33'),
(17, 1, 'Đo hoạt độ AST (GOT)', 200000.00, 30, 'blood_test', 1, 'both', NULL, 51, '2025-10-28 20:35:33'),
(18, 1, 'Đo hoạt độ GGT', 200000.00, 30, 'blood_test', 1, 'both', NULL, 52, '2025-10-28 20:35:33'),
(19, 1, 'Định lượng Creatinin máu', 200000.00, 30, 'blood_test', 1, 'both', NULL, 60, '2025-10-29 03:35:33'),
(20, 1, 'Định lượng Ure máu', 200000.00, 30, 'blood_test', 1, 'both', NULL, 61, '2025-10-29 03:35:33'),
(21, 1, 'Định lượng TSH máu', 200000.00, 30, 'blood_test', 1, 'both', NULL, 70, '2025-10-29 03:35:33'),
(22, 1, 'Định lượng FT4 máu', 200000.00, 30, 'blood_test', 1, 'both', NULL, 71, '2025-10-29 03:35:33'),
(23, 1, 'Định lượng Acid Uric máu', 90000.00, 30, 'blood_test', 1, 'both', NULL, 80, '2025-10-29 03:35:33'),
(24, 1, 'Định lượng Glucose', 60000.00, 30, 'blood_test', 1, 'both', NULL, 90, '2025-10-29 03:35:33'),
(25, 1, 'Định lượng HbA1c', 150000.00, 30, 'blood_test', 1, 'both', NULL, 91, '2025-10-29 03:35:33'),
(26, 1, 'HbsAg miễn dịch tự động', 200000.00, 30, 'blood_test', 1, 'both', NULL, 100, '2025-10-29 03:35:33'),
(27, 1, 'HbsAb miễn dịch tự động', 200000.00, 30, 'blood_test', 1, 'both', NULL, 101, '2025-10-29 03:35:33'),
(28, 1, 'HCV Ab miễn dịch tự động', 200000.00, 30, 'blood_test', 1, 'both', NULL, 102, '2025-10-29 03:35:33'),
(29, 2, 'Chụp X quang ngực thẳng số hóa (1 phim)', 500000.00, 30, 'imaging', 1, 'both', NULL, 1, '2025-10-29 03:35:33'),
(30, 2, 'Siêu âm ổ bụng (gan mật, tụy, lách, thận, bàng quang)', 400000.00, 30, 'imaging', 1, 'both', NULL, 2, '2025-10-29 03:35:33'),
(31, 2, 'Siêu âm vú', 400000.00, 30, 'imaging', 1, 'female', NULL, 3, '2025-10-29 03:35:33'),
(32, 2, 'Siêu âm tuyến giáp', 400000.00, 30, 'imaging', 1, 'both', NULL, 4, '2025-10-29 03:35:33'),
(33, 2, 'Điện tim ECG', 300000.00, 30, 'general', 1, 'both', NULL, 5, '2025-10-29 03:35:33'),
(34, 2, 'Vị khuẩn nhuộm soi (huyết trắng)', 300000.00, 30, 'specialist', 1, 'female', NULL, 6, '2025-10-29 03:35:33'),
(35, 2, 'Sinh hiệu (Mạch, huyết áp, chiều cao, cân nặng, chỉ số BMI)', 150000.00, 30, 'general', 1, 'both', NULL, 10, '2025-10-29 03:35:33'),
(36, 2, 'Khám Nội tổng quát (BS nội)', 150000.00, 30, 'general', 1, 'both', NULL, 11, '2025-10-29 03:35:33'),
(37, 2, 'Khám Phụ khoa (BS phụ khoa)', 250000.00, 30, 'specialist', 1, 'female', NULL, 12, '2025-10-29 03:35:33'),
(38, 2, 'Tổng phân tích tế bào máu ngoại vi (máy laser)', 200000.00, 30, 'blood_test', 1, 'both', NULL, 20, '2025-10-29 03:35:33'),
(39, 2, 'Tổng phân tích nước tiểu (máy tự động)', 50000.00, 30, 'urine_test', 1, 'both', NULL, 30, '2025-10-29 03:35:33'),
(40, 2, 'Định lượng cholesterol toàn phần', 180000.00, 30, 'blood_test', 1, 'both', NULL, 40, '2025-10-29 03:35:33'),
(41, 2, 'Định lượng HDL-C', 200000.00, 30, 'blood_test', 1, 'both', NULL, 41, '2025-10-29 03:35:33'),
(42, 2, 'Định lượng LDL-C', 200000.00, 30, 'blood_test', 1, 'both', NULL, 42, '2025-10-29 03:35:33'),
(43, 2, 'Định lượng Triglycerid', 200000.00, 30, 'blood_test', 1, 'both', NULL, 43, '2025-10-29 03:35:33'),
(44, 2, 'Đo hoạt độ ALT (GPT)', 200000.00, 30, 'blood_test', 1, 'both', NULL, 50, '2025-10-29 03:35:33'),
(45, 2, 'Đo hoạt độ AST (GOT)', 200000.00, 30, 'blood_test', 1, 'both', NULL, 51, '2025-10-29 03:35:33'),
(46, 2, 'Đo hoạt độ GGT', 200000.00, 30, 'blood_test', 1, 'both', NULL, 52, '2025-10-29 03:35:33'),
(47, 2, 'Định lượng Creatinin máu', 200000.00, 30, 'blood_test', 1, 'both', NULL, 60, '2025-10-29 03:35:33'),
(48, 2, 'Định lượng Ure máu', 200000.00, 30, 'blood_test', 1, 'both', NULL, 61, '2025-10-29 03:35:33'),
(49, 2, 'Định lượng TSH máu', 200000.00, 30, 'blood_test', 1, 'both', NULL, 70, '2025-10-29 03:35:33'),
(50, 2, 'Định lượng FT4 máu', 200000.00, 30, 'blood_test', 1, 'both', NULL, 71, '2025-10-29 03:35:33'),
(51, 2, 'Định lượng Acid Uric máu', 90000.00, 30, 'blood_test', 1, 'both', NULL, 80, '2025-10-29 03:35:33'),
(52, 2, 'Định lượng Glucose', 60000.00, 30, 'blood_test', 1, 'both', NULL, 90, '2025-10-29 03:35:33'),
(53, 2, 'Định lượng HbA1c', 150000.00, 30, 'blood_test', 1, 'both', NULL, 91, '2025-10-29 03:35:33'),
(54, 2, 'HbsAg miễn dịch tự động', 200000.00, 30, 'blood_test', 1, 'both', NULL, 100, '2025-10-29 03:35:33'),
(55, 2, 'HbsAb miễn dịch tự động', 200000.00, 30, 'blood_test', 1, 'both', NULL, 101, '2025-10-29 03:35:33'),
(56, 2, 'HCV Ab miễn dịch tự động', 200000.00, 30, 'blood_test', 1, 'both', NULL, 102, '2025-10-29 03:35:33'),
(58, 3, 'Siêu âm thai định kỳ', 300000.00, 20, 'general', 1, 'female', 'khám thai định kỳ cho mẹ bầu ', 0, '2025-11-11 07:03:04'),
(60, 3, 'Xét nghiệm nước tiểu', 50000.00, 30, 'urine_test', 1, 'female', 'ko ', 0, '2025-11-11 07:05:22'),
(61, 3, 'kiểm tra đường huyết, huyết áp', 400000.00, 30, 'other', 0, 'female', 'ko', 0, '2025-11-11 07:06:08'),
(62, 3, 'Tư vấn dinh dưỡng và chế độ chăm sóc mẹ bầu', 50000.00, 30, 'other', 0, 'female', 'ko ', 0, '2025-11-11 07:08:01'),
(64, 4, 'Khám nội tổng quát', 400000.00, 30, 'general', 1, 'both', 'khám nội quan tổng quát', 0, '2025-11-21 07:26:13'),
(67, 4, 'Mỡ máu (Cholesterol – Triglyceride – HDL – LDL)', 300000.00, 30, 'blood_test', 1, 'both', 'xét nghiệm mỡ máu (Cholesterol – Triglyceride – HDL – LDL)', 0, '2025-11-21 07:28:47'),
(68, 4, 'Tổng phân tích nước tiểu 10 thông số', 80000.00, 30, 'urine_test', 1, 'both', 'Tổng phân tích nước tiểu 10 thông số', 0, '2025-11-21 07:29:33'),
(69, 4, 'Siêu âm ổ bụng tổng quát', 50000.00, 30, 'imaging', 1, 'both', 'ko có', 0, '2025-11-21 07:30:04'),
(71, 4, 'In kết quả + tư vấn sức khỏe', 50000.00, 20, 'other', 0, 'both', 'In kết quả + tư vấn sức khỏe', 0, '2025-11-21 07:31:04'),
(73, 5, 'Khám phát hiện khối u ngoại biên', 300000.00, 30, 'general', 1, 'both', 'Khám phát hiện khối u ngoại biên', 0, '2025-11-21 07:33:51'),
(74, 5, 'AFP (gan)', 50000.00, 30, 'blood_test', 1, 'both', 'xét nghiệm máu AFP (gan)', 0, '2025-11-21 07:35:00'),
(75, 5, 'CEA (đại tràng)', 100000.00, 30, 'blood_test', 1, 'both', 'xét nghiệm máu CEA (đại tràng)', 0, '2025-11-21 07:35:24'),
(76, 5, 'CA 19-9 (tụy)', 70000.00, 30, 'blood_test', 1, 'both', 'xét nghiệm máu CA 19-9 (tụy)', 0, '2025-11-21 07:36:00'),
(77, 5, 'Siêu âm toàn bộ ổ bụng', 200000.00, 30, 'imaging', 1, 'both', 'Siêu âm toàn bộ ổ bụng', 0, '2025-11-21 07:36:39'),
(78, 5, 'X-quang ngực', 300000.00, 30, 'imaging', 1, 'both', 'chụp X-quang ngực', 0, '2025-11-21 07:37:12'),
(79, 5, 'Siêu âm tuyến giáp', 150000.00, 30, 'imaging', 1, 'both', 'Siêu âm tuyến giáp', 0, '2025-11-21 07:37:57'),
(80, 5, 'Tư vấn nguy cơ ung thư theo tuổi', 100000.00, 30, 'other', 0, 'both', 'Tư vấn nguy cơ ung thư theo tuổi , giới tính , ...', 0, '2025-11-21 07:39:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `package_service_doctors`
--

CREATE TABLE `package_service_doctors` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `package_service_doctors`
--

INSERT INTO `package_service_doctors` (`id`, `service_id`, `doctor_id`, `created_at`) VALUES
(6, 64, 4, '2025-12-03 06:25:20'),
(7, 64, 2, '2025-12-03 06:25:20'),
(8, 64, 6, '2025-12-03 06:25:20'),
(11, 67, 3, '2025-12-03 06:25:36'),
(12, 67, 4, '2025-12-03 06:25:36'),
(13, 68, 7, '2025-12-03 06:25:40'),
(14, 68, 5, '2025-12-03 06:25:40'),
(17, 69, 6, '2025-12-03 06:25:53'),
(18, 69, 9, '2025-12-03 06:25:53'),
(19, 71, 3, '2025-12-03 06:25:59'),
(20, 71, 1, '2025-12-03 06:25:59'),
(25, 73, 5, '2025-12-04 10:33:20'),
(26, 73, 1, '2025-12-04 10:33:20'),
(29, 74, 5, '2025-12-04 10:33:26'),
(30, 74, 1, '2025-12-04 10:33:26'),
(31, 76, 5, '2025-12-04 10:33:28'),
(32, 76, 1, '2025-12-04 10:33:28'),
(33, 75, 5, '2025-12-04 10:33:31'),
(34, 75, 1, '2025-12-04 10:33:31'),
(35, 77, 5, '2025-12-04 10:33:35'),
(36, 77, 1, '2025-12-04 10:33:35'),
(37, 78, 5, '2025-12-04 10:33:38'),
(38, 78, 1, '2025-12-04 10:33:38'),
(39, 79, 5, '2025-12-04 10:33:42'),
(40, 79, 1, '2025-12-04 10:33:42'),
(41, 80, 5, '2025-12-04 10:33:45'),
(42, 80, 1, '2025-12-04 10:33:45'),
(43, 58, 5, '2025-12-04 12:58:40'),
(44, 58, 3, '2025-12-04 12:58:40'),
(45, 60, 5, '2025-12-04 12:58:44'),
(46, 60, 3, '2025-12-04 12:58:44'),
(47, 61, 5, '2025-12-04 12:58:48'),
(48, 61, 3, '2025-12-04 12:58:48'),
(49, 62, 5, '2025-12-04 12:58:53'),
(50, 62, 3, '2025-12-04 12:58:53');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `package_test_results`
--

CREATE TABLE `package_test_results` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `metric_name` varchar(255) NOT NULL,
  `result_value` varchar(255) DEFAULT NULL,
  `result_status` enum('normal','abnormal','pending') DEFAULT 'pending',
  `reference_range` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `tested_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `package_test_results`
--

INSERT INTO `package_test_results` (`id`, `appointment_id`, `service_id`, `metric_name`, `result_value`, `result_status`, `reference_range`, `notes`, `tested_at`, `created_at`, `updated_at`) VALUES
(29, 265, 78, 'bình thường', 'tốt', 'normal', '167-172', 'ko', NULL, '2025-12-04 13:48:21', '2025-12-04 13:48:21');

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
(11, 28, 'PAT00011', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-22 09:08:04'),
(12, 29, 'PAT00012', '2007-02-28', 'male', 'le loi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-31 07:57:11'),
(13, 30, 'PAT00013', '2004-03-31', 'male', 'le loi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-31 08:00:34'),
(14, 31, 'PAT00014', '2000-02-11', 'female', 'ko', '', '', NULL, '', '', NULL, NULL, '2025-11-21 12:01:44'),
(15, 32, 'PAT00015', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-11 11:42:47'),
(16, 33, 'PAT00016', '2001-07-11', 'male', '118/3a to ly', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-11 11:47:00');

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

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(10) UNSIGNED NOT NULL,
  `prescription_code` varchar(20) DEFAULT NULL,
  `appointment_id` int(10) UNSIGNED DEFAULT NULL,
  `package_appointment_id` int(10) UNSIGNED DEFAULT NULL,
  `diagnosis_id` int(10) UNSIGNED DEFAULT NULL,
  `doctor_id` int(10) UNSIGNED DEFAULT NULL,
  `patient_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('draft','submitted','approved','dispensed','canceled') NOT NULL DEFAULT 'draft',
  `notes_to_patient` text DEFAULT NULL,
  `notes_internal` text DEFAULT NULL,
  `total_items` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pdf_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `prescription_code`, `appointment_id`, `package_appointment_id`, `diagnosis_id`, `doctor_id`, `patient_id`, `status`, `notes_to_patient`, `notes_internal`, `total_items`, `pdf_path`, `created_at`, `updated_at`) VALUES
(5, 'RX20251204181032203', 217, 32, NULL, 1, 14, 'approved', NULL, NULL, 1, NULL, '2025-12-04 11:10:32', '2025-12-04 11:10:41'),
(6, 'RX20251204181620197', 215, 32, NULL, 1, 14, 'approved', NULL, NULL, 1, NULL, '2025-12-04 11:16:20', '2025-12-04 11:16:23'),
(7, 'RX20251204185626757', 227, 33, NULL, 1, 14, 'approved', NULL, NULL, 1, NULL, '2025-12-04 11:56:26', '2025-12-04 11:56:31'),
(8, 'RX20251204185900389', 225, 33, NULL, 1, 14, 'approved', NULL, NULL, 1, NULL, '2025-12-04 11:59:00', '2025-12-04 11:59:02'),
(9, 'RX20251204195026388', 212, 32, NULL, 5, 14, 'approved', NULL, NULL, 1, NULL, '2025-12-04 12:50:26', '2025-12-04 12:50:30'),
(10, 'RX20251204195704838', 245, 35, NULL, 1, 14, 'approved', NULL, NULL, 1, NULL, '2025-12-04 12:57:04', '2025-12-04 12:57:06'),
(11, 'RX20251204204906261', 272, 38, NULL, 1, 14, 'approved', NULL, NULL, 1, NULL, '2025-12-04 13:49:06', '2025-12-04 13:49:08'),
(12, 'RX20251206183212597', 297, NULL, NULL, 7, 14, 'approved', NULL, NULL, 1, NULL, '2025-12-06 11:32:12', '2025-12-06 11:32:14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `prescription_items`
--

CREATE TABLE `prescription_items` (
  `id` int(11) NOT NULL,
  `prescription_id` int(10) UNSIGNED DEFAULT NULL,
  `prescription_code` varchar(20) NOT NULL,
  `medical_record_id` int(11) DEFAULT NULL,
  `medicine_id` int(11) NOT NULL,
  `drug_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `dosage` varchar(100) NOT NULL,
  `frequency` varchar(100) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `instructions` mediumtext DEFAULT NULL,
  `route` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `refills_allowed` int(11) DEFAULT 0,
  `refills_remaining` int(11) DEFAULT 0,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `prescription_items`
--

INSERT INTO `prescription_items` (`id`, `prescription_id`, `prescription_code`, `medical_record_id`, `medicine_id`, `drug_name`, `quantity`, `dosage`, `frequency`, `duration`, `instructions`, `route`, `start_date`, `end_date`, `refills_allowed`, `refills_remaining`, `status`, `created_at`, `updated_at`) VALUES
(6, 5, 'RX20251204181032203', NULL, 8, NULL, 1, '1', '2', '4', 'sau ăn', 'uống', '2025-12-05', '2025-12-09', 0, 0, 'active', '2025-12-04 11:10:32', '2025-12-04 11:10:32'),
(7, 6, 'RX20251204181620197', NULL, 9, NULL, 1, '1', '2', '4', 'sau ăn', 'uống', '2025-12-05', '2025-12-06', 0, 0, 'active', '2025-12-04 11:16:20', '2025-12-04 11:16:20'),
(8, 7, 'RX20251204185626757', NULL, 7, NULL, 1, '1', '2', '4', 'sau ăn', 'uống', '2025-12-10', '2025-12-12', 0, 0, 'active', '2025-12-04 11:56:26', '2025-12-04 11:56:26'),
(9, 8, 'RX20251204185900389', NULL, 38, NULL, 1, '1', '2', '4', 'sau ăn', 'uống', '2025-12-05', '2025-12-11', 0, 0, 'active', '2025-12-04 11:59:00', '2025-12-04 11:59:00'),
(10, 9, 'RX20251204195026388', NULL, 55, NULL, 1, '1', '2', '4', 'sau ăn', 'uống', '2025-12-05', '2025-12-12', 0, 0, 'active', '2025-12-04 12:50:26', '2025-12-04 12:50:26'),
(11, 10, 'RX20251204195704838', NULL, 38, NULL, 1, '1', '2', '4', 'sau ăn', 'uống', '2025-12-06', '2025-12-05', 0, 0, 'active', '2025-12-04 12:57:04', '2025-12-04 12:57:04'),
(12, 11, 'RX20251204204906261', NULL, 38, NULL, 1, '1', '2', '4', 'sau ăn', 'uống', '2025-12-05', '2025-12-07', 0, 0, 'active', '2025-12-04 13:49:06', '2025-12-04 13:49:06'),
(13, 12, 'RX20251206183212597', NULL, 46, NULL, 1, '1', '2', '4', 'sau ăn', 'uống', '2025-12-13', '2025-12-13', 0, 0, 'active', '2025-12-06 11:32:12', '2025-12-06 11:32:12');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `service_allowed_medicines`
--

CREATE TABLE `service_allowed_medicines` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(12, 'truong789', 'truongph32003@gmail.com', '$2y$10$u918Z51pNso8Sq9XEinVHeFxlygsCj72B34kTQnJsDTd7N/Nn/WLa', 'Phạm Quang Trường', '0973436483', '2003-03-12', 'female', '116/3a\r\n67/32', NULL, 'patient', 1, '2025-10-04 06:19:32', '2025-11-21 12:30:48'),
(13, 'truongquang', 'quanly@gmail.com', '$2y$10$5T8t8he4M8Dgavnkpsz.NOgvp5Cxy8WMcLEompRKqLH6MvuyCbqjm', 'Hà Thị Tâm', '0123456785', '2003-03-13', 'male', '116/3a\r\n67/32', NULL, 'patient', 1, '2025-10-04 06:22:25', '2025-10-04 06:22:25'),
(14, 'patient_1759569106', 'An@gmail.com', '$2y$10$godvlAIvAJr2CvVzOj3ZEu/Q96Io8/PmYe3W.LAGzAqLg6Uma6hhm', 'Phạm Đình An', '0973438567', NULL, NULL, NULL, NULL, 'patient', 1, '2025-10-04 09:11:47', '2025-10-04 09:11:47'),
(15, 'patient_1759571072', 'duong@gamil.com', '$2y$10$JQe.4RX/dCLPMZ1.Jk0mOe4TrVpoq8rWryELXEqIkTubmN0bh5c0m', 'Binh Duong', '03821321', NULL, NULL, NULL, NULL, 'patient', 1, '2025-10-04 09:44:32', '2025-10-04 09:44:32'),
(16, 'patient_1759571803', 'An111@gmail.com', '$2y$10$0BsUCKD0XK9LNsccfO.L9OcrYNvcXA3sxWYqSPkpygS4jDMPcln7O', 'Phạm An', '0973438567', '2025-10-17', 'male', 'le loi', NULL, 'patient', 1, '2025-10-04 09:56:43', '2025-10-04 09:56:43'),
(17, 'truong7890', 'truongdeptrai@gmail.com', '$2y$10$zmv75E6OfnduuAQvuH3pTujjtyDx0H3G9QRh5GKl81elm67EI9MIC', 'Dinh Huy', '0973733213', '2000-03-06', 'male', '37/Tân Phú', NULL, 'patient', 1, '2025-10-06 02:57:41', '2025-10-06 02:57:41'),
(18, 'patient_1759731854', 'huy@gmail.com', '$2y$10$wzV4MD50ZtWMPqn3hN4eEuddpRt/P3AFOnseWEafqUf0rQyoBkIzK', 'Đình Huy', '093123213', '2023-03-08', 'male', 'le loi1456', NULL, 'patient', 1, '2025-10-06 06:24:14', '2025-10-06 06:24:14'),
(19, 'vanh', 'vanh@gmail.com', '$2y$10$84t1U8FNzfKdsHM0IG128e5Dr3rnTRgpT1krR3OcYJ1Ruy8KPgXiC', 'Lê Văn Việt Anh', '0967584675', '2003-09-09', 'male', 'Nhà 8', NULL, 'patient', 1, '2025-10-07 04:06:48', '2025-10-07 04:06:48'),
(20, 'letan1', 'letan@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hà Thị Tâm', '0909835121', '1975-05-15', 'male', '116/3A  St, TP.HCM', NULL, 'receptionist', 1, '2025-10-09 03:19:33', '2025-10-09 03:19:33'),
(21, 'patient_1759980691', 'truong@gamil.com', '$2y$10$BowsSO5bqxuwxkC0g5fVsusDvpFMLlqQ4x5iu3nZYYNgLa8B/SY2q', 'Phạm Quang Trường', '0956474834', '2025-10-10', 'male', 'le loi', NULL, 'patient', 1, '2025-10-09 03:31:31', '2025-10-09 03:31:31'),
(26, 'dr.truong', 'truongpham1203@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'BS.Phạm Quang Trường', '0975758586', NULL, NULL, NULL, NULL, 'doctor', 1, '2025-10-09 06:40:45', '2025-11-25 09:36:40'),
(27, 'dr', 'vanh11@gmail.com', '$2y$10$kMtDY94idZYVSdFzOLl9PuoiNhxnjOTNEgtYVXAZdDxb/Z/9PEBqe', 'BS.Vanh Le', '0973436483', NULL, NULL, NULL, NULL, 'doctor', 1, '2025-10-09 07:01:41', '2025-10-10 07:19:04'),
(28, 'truong1234', 'truongpham1203200311@gmail.com', '$2y$10$mIvZiScxryqbU0DRgAsMUenvfNJHp1qWpdfvz69CzDrjgUcvpA71a', 'Phạm Văn Duy', '0964753132', '2008-02-22', 'male', '116/3a\r\n67/32', NULL, 'patient', 1, '2025-10-22 09:08:04', '2025-10-22 09:08:04'),
(29, 'patient_20251031145711', 'patient_20251031145711@temp.com', '$2y$10$PQBuSu4l55Ru10x99iMIHOq1z0Z9V5opRqdcAcmmdf/d1nAyuDoQW', 'huy le tran', '0964758463', NULL, NULL, NULL, NULL, 'patient', 1, '2025-10-31 07:57:11', '2025-10-31 07:57:11'),
(30, 'patient_20251031150034', 'patient_20251031150034@temp.com', '$2y$10$QZJP.l719yVwtvz.3fciiu8JrYp9haOIuxOpsNDTxcrvfHb3WzR6O', 'huy le dinh tran', '097777444', NULL, NULL, NULL, NULL, 'patient', 1, '2025-10-31 08:00:34', '2025-10-31 08:00:34'),
(31, 'thitam', 'truongpham12032003@gmail.com', '$2y$10$wRSRZ/sMaCkSgEK0PHk1VOJZ8z1cb65eZsLlyJJMgc.eWw8VANk.2', 'yến ', '0966758758', '2000-02-11', 'female', 'ko', NULL, 'patient', 1, '2025-11-11 08:43:23', '2025-11-21 12:31:06'),
(32, 'truongpham', 'truong7821212@gmail.com', '$2y$10$NsvBAQg7kdBPm.RymzYDCuNnSB3LPeYrpfX2e2TOOqoki.bFm3DMe', 'trường quang', '0966758759', '2003-06-06', 'male', '116/3a\r\n67/37', NULL, 'patient', 1, '2025-11-11 11:42:47', '2025-11-11 11:42:47'),
(33, 'patient_20251111184700', 'patient_20251111184700@temp.com', '$2y$10$frqFyVGVpTckEhO9gwCJduzZhaNxvoit7EllR87hvYVwhqvjWon6W', 'Pham Quang Trường', '0973647584', NULL, NULL, NULL, NULL, 'patient', 1, '2025-11-11 11:47:00', '2025-11-11 11:47:00');

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
  ADD KEY `coordinator_doctor_id` (`coordinator_doctor_id`),
  ADD KEY `fk_appointments_package_appointment` (`package_appointment_id`);

--
-- Chỉ mục cho bảng `appointment_package_services`
--
ALTER TABLE `appointment_package_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_appointment` (`appointment_id`),
  ADD KEY `idx_service` (`service_id`),
  ADD KEY `idx_doctor` (`doctor_id`),
  ADD KEY `idx_aps_state` (`result_state`),
  ADD KEY `idx_aps_apt_svc` (`appointment_id`,`service_id`);

--
-- Chỉ mục cho bảng `appointment_results`
--
ALTER TABLE `appointment_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_result_appointment` (`appointment_id`);

--
-- Chỉ mục cho bảng `appointment_result_items`
--
ALTER TABLE `appointment_result_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_result_item` (`result_id`);

--
-- Chỉ mục cho bảng `consultations`
--
ALTER TABLE `consultations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `status` (`status`),
  ADD KEY `last_message_at` (`last_message_at`);

--
-- Chỉ mục cho bảng `consultation_attachments`
--
ALTER TABLE `consultation_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`);

--
-- Chỉ mục cho bảng `consultation_messages`
--
ALTER TABLE `consultation_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consultation_id` (`consultation_id`),
  ADD KEY `sender_user_id` (`sender_user_id`);

--
-- Chỉ mục cho bảng `diagnoses`
--
ALTER TABLE `diagnoses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dx_apt` (`appointment_id`),
  ADD KEY `idx_dx_pa` (`package_appointment_id`),
  ADD KEY `idx_dx_patient` (`patient_id`),
  ADD KEY `idx_dx_doctor` (`doctor_id`);

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
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_read` (`is_read`);

--
-- Chỉ mục cho bảng `package_appointments`
--
ALTER TABLE `package_appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `package_id` (`package_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_pkg_final_status` (`final_status`);

--
-- Chỉ mục cho bảng `package_services`
--
ALTER TABLE `package_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_package` (`package_id`),
  ADD KEY `idx_category` (`service_category`);

--
-- Chỉ mục cho bảng `package_service_doctors`
--
ALTER TABLE `package_service_doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_service_doctor` (`service_id`,`doctor_id`),
  ADD KEY `idx_psd_service` (`service_id`),
  ADD KEY `idx_psd_doctor` (`doctor_id`);

--
-- Chỉ mục cho bảng `package_test_results`
--
ALTER TABLE `package_test_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `idx_appointment` (`appointment_id`),
  ADD KEY `idx_status` (`result_status`),
  ADD KEY `idx_ptr_apt_svc` (`appointment_id`,`service_id`);

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
  ADD KEY `idx_rx_code` (`prescription_code`),
  ADD KEY `idx_rx_apt` (`appointment_id`),
  ADD KEY `idx_rx_pa` (`package_appointment_id`),
  ADD KEY `idx_rx_diag` (`diagnosis_id`),
  ADD KEY `idx_rx_patient` (`patient_id`),
  ADD KEY `idx_rx_status` (`status`);

--
-- Chỉ mục cho bảng `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `prescription_code` (`prescription_code`),
  ADD KEY `idx_medical_record` (`medical_record_id`),
  ADD KEY `idx_medicine` (`medicine_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_prescription_id` (`prescription_id`);

--
-- Chỉ mục cho bảng `service_allowed_medicines`
--
ALTER TABLE `service_allowed_medicines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_service_medicine` (`service_id`,`medicine_id`),
  ADD KEY `idx_sam_service` (`service_id`),
  ADD KEY `idx_sam_medicine` (`medicine_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=310;

--
-- AUTO_INCREMENT cho bảng `appointment_package_services`
--
ALTER TABLE `appointment_package_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=246;

--
-- AUTO_INCREMENT cho bảng `appointment_results`
--
ALTER TABLE `appointment_results`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `appointment_result_items`
--
ALTER TABLE `appointment_result_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `consultations`
--
ALTER TABLE `consultations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `consultation_attachments`
--
ALTER TABLE `consultation_attachments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `consultation_messages`
--
ALTER TABLE `consultation_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `diagnoses`
--
ALTER TABLE `diagnoses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `health_packages`
--
ALTER TABLE `health_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT cho bảng `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT cho bảng `package_appointments`
--
ALTER TABLE `package_appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT cho bảng `package_services`
--
ALTER TABLE `package_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT cho bảng `package_service_doctors`
--
ALTER TABLE `package_service_doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT cho bảng `package_test_results`
--
ALTER TABLE `package_test_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT cho bảng `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `prescription_items`
--
ALTER TABLE `prescription_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `service_allowed_medicines`
--
ALTER TABLE `service_allowed_medicines`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

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
  ADD CONSTRAINT `appointments_ibfk_4` FOREIGN KEY (`coordinator_doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_appointments_package_appointment` FOREIGN KEY (`package_appointment_id`) REFERENCES `package_appointments` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `appointment_package_services`
--
ALTER TABLE `appointment_package_services`
  ADD CONSTRAINT `appointment_package_services_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_package_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `package_services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_package_services_ibfk_3` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `appointment_results`
--
ALTER TABLE `appointment_results`
  ADD CONSTRAINT `appointment_results_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `appointment_result_items`
--
ALTER TABLE `appointment_result_items`
  ADD CONSTRAINT `appointment_result_items_ibfk_1` FOREIGN KEY (`result_id`) REFERENCES `appointment_results` (`id`) ON DELETE CASCADE;

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
-- Các ràng buộc cho bảng `package_appointments`
--
ALTER TABLE `package_appointments`
  ADD CONSTRAINT `fk_package_appointments_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_package_appointments_package` FOREIGN KEY (`package_id`) REFERENCES `health_packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_package_appointments_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `package_services`
--
ALTER TABLE `package_services`
  ADD CONSTRAINT `package_services_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `health_packages` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `package_service_doctors`
--
ALTER TABLE `package_service_doctors`
  ADD CONSTRAINT `fk_psd_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_psd_service` FOREIGN KEY (`service_id`) REFERENCES `package_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Các ràng buộc cho bảng `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD CONSTRAINT `prescription_items_ibfk_1` FOREIGN KEY (`medical_record_id`) REFERENCES `medical_records` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescription_items_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`);

--
-- Các ràng buộc cho bảng `service_allowed_medicines`
--
ALTER TABLE `service_allowed_medicines`
  ADD CONSTRAINT `fk_sam_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sam_service` FOREIGN KEY (`service_id`) REFERENCES `package_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
