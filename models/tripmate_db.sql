-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 30, 2025 at 04:23 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tripmate_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `tour_id` int NOT NULL,
  `customer_user_id` int NOT NULL,
  `sales_user_id` int NOT NULL,
  `total_guests` int NOT NULL,
  `booking_status` enum('pending','deposit','completed','canceled') COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `date_booked` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `tour_id`, `customer_user_id`, `sales_user_id`, `total_guests`, `booking_status`, `total_price`, `date_booked`) VALUES
(1, 1, 4, 3, 2, 'completed', '7000000.00', '2025-11-05 14:30:00'),
(2, 2, 5, 3, 1, 'canceled', '6200000.00', '2025-11-06 10:15:00'),
(3, 1, 4, 3, 4, 'pending', '16000000.00', '2025-11-23 13:11:58'),
(4, 2, 4, 3, 2, 'completed', '3000000.00', '2025-11-25 12:59:53'),
(5, 3, 4, 3, 5, 'deposit', '10000000.00', '2025-11-29 13:10:33');

-- --------------------------------------------------------

--
-- Table structure for table `booking_guests`
--

CREATE TABLE `booking_guests` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date NOT NULL,
  `id_document_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_checked_in` tinyint(1) NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_guests`
--

INSERT INTO `booking_guests` (`id`, `booking_id`, `full_name`, `gender`, `dob`, `id_document_no`, `is_checked_in`, `notes`) VALUES
(1, 1, 'Lê Văn Khánh', 'male', '1990-05-15', '031234567890', 0, 'Không có ghi chú'),
(2, 1, 'Nguyễn Thị linh', 'female', '1992-08-20', '031234567891', 0, 'Dị ứng hải sản'),
(3, 3, 'lê văn khánh', 'male', '1999-04-21', '008199323313', 0, ''),
(4, 3, 'lê văn khánh', 'male', '1999-06-06', '008199323313', 0, ''),
(5, 3, 'lê văn khánh', 'male', '2000-06-23', '008199323313', 0, ''),
(6, 3, 'lê văn khánh', 'male', '1993-05-23', '008199323313', 0, ''),
(7, 4, 'lê văn khánh', 'male', '2025-11-01', '008199323313', 0, ''),
(8, 4, 'lê văn khánh', 'female', '2025-10-30', '008199323313', 0, ''),
(9, 5, 'lê văn khánh', 'male', '2025-11-02', '008199323313', 0, ''),
(10, 5, 'lê văn khánh', 'male', '2025-10-31', '008199323313', 0, ''),
(11, 5, 'lê văn khánh', 'male', '2025-10-29', '008199323313', 0, ''),
(12, 5, 'lê văn khánh', 'male', '2025-11-03', '008199323313', 0, ''),
(13, 5, 'lê văn khánh', 'male', '2025-11-05', '008199323313', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `booking_requests`
--

CREATE TABLE `booking_requests` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `guest_id` int NOT NULL,
  `request_type` enum('diet','medical','room','transport','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_requests`
--

INSERT INTO `booking_requests` (`id`, `booking_id`, `guest_id`, `request_type`, `details`) VALUES
(1, 1, 2, 'diet', 'Khách ăn chay trường'),
(2, 1, 2, 'medical', 'Dị ứng hải sản, cần chuẩn bị thuốc dị ứng');

-- --------------------------------------------------------

--
-- Table structure for table `booking_status_logs`
--

CREATE TABLE `booking_status_logs` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `old_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `changed_by_user_id` int NOT NULL,
  `changed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_status_logs`
--

INSERT INTO `booking_status_logs` (`id`, `booking_id`, `old_status`, `new_status`, `changed_by_user_id`, `changed_at`) VALUES
(1, 1, 'pending', 'deposit', 3, '2025-11-05 14:35:00'),
(2, 3, 'pending', 'pending', 3, '2025-11-23 13:11:58'),
(3, 2, 'pending', 'canceled', 1, '2025-11-24 14:08:43'),
(4, 1, 'deposit', 'completed', 1, '2025-11-24 14:52:04'),
(5, 1, 'completed', 'deposit', 1, '2025-11-24 14:52:11'),
(6, 1, 'deposit', 'pending', 1, '2025-11-25 12:01:14'),
(7, 1, 'pending', 'completed', 1, '2025-11-25 12:01:23'),
(8, 4, 'pending', 'pending', 3, '2025-11-25 12:59:53'),
(9, 4, 'pending', 'completed', 1, '2025-11-25 13:00:16'),
(10, 1, 'completed', 'canceled', 1, '2025-11-25 13:04:50'),
(11, 1, 'canceled', 'completed', 1, '2025-11-25 13:33:09'),
(12, 5, 'pending', 'pending', 3, '2025-11-29 13:10:33'),
(13, 5, 'pending', 'deposit', 1, '2025-11-29 13:16:40');

-- --------------------------------------------------------

--
-- Table structure for table `cancellation_policies`
--

CREATE TABLE `cancellation_policies` (
  `id` int NOT NULL,
  `tour_id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `days_before` int NOT NULL COMMENT 'Số ngày trước ngày khởi hành',
  `refund_percentage` decimal(5,2) NOT NULL COMMENT 'Phần trăm hoàn tiền',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cancellation_policies`
--

INSERT INTO `cancellation_policies` (`id`, `tour_id`, `name`, `description`, `days_before`, `refund_percentage`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Hủy trước 30 ngày', 'Hoàn lại 100% chi phí tour', 30, '100.00', 1, '2025-11-28 11:39:09', '2025-11-28 11:39:09'),
(2, 1, 'Hủy từ 15-29 ngày', 'Hoàn lại 70% chi phí tour', 15, '70.00', 1, '2025-11-28 11:39:09', '2025-11-28 11:39:09'),
(3, 1, 'Hủy từ 7-14 ngày', 'Hoàn lại 50% chi phí tour', 7, '50.00', 1, '2025-11-28 11:39:09', '2025-11-28 11:39:09'),
(4, 1, 'Hủy dưới 7 ngày', 'Không hoàn tiền', 0, '0.00', 1, '2025-11-28 11:39:09', '2025-11-28 11:39:09');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int NOT NULL,
  `category_id` int DEFAULT NULL,
  `tour_id` int DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `expense_date` date DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `category_id`, `tour_id`, `amount`, `description`, `expense_date`, `created_by`, `created_at`) VALUES
(1, 1, 1, '5000000.00', 'Thuê xe 16 chỗ đi Hạ Long', '2025-12-01', 1, '2025-11-28 14:05:22'),
(2, 2, 1, '3000000.00', 'Tiền ăn trưa cho đoàn', '2025-12-01', 1, '2025-11-28 14:05:22'),
(3, 3, 1, '6000000.00', 'Tiền khách sạn 2 đêm', '2025-12-01', 1, '2025-11-28 14:05:22'),
(4, 4, 1, '2000000.00', 'Vé tham quan Vịnh Hạ Long', '2025-12-02', 1, '2025-11-28 14:05:22'),
(5, 5, 1, '2000000.00', 'Thù lao hướng dẫn viên', '2025-12-03', 1, '2025-11-28 14:05:22');

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `name`, `description`) VALUES
(1, 'Vận chuyển', 'Chi phí đi lại, xăng dầu, vé máy bay, tàu hỏa'),
(2, 'Ăn uống', 'Chi phí ăn uống cho đoàn'),
(3, 'Khách sạn', 'Chi phí thuê phòng khách sạn'),
(4, 'Vé tham quan', 'Vé vào cổng các điểm tham quan'),
(5, 'Lương HDV', 'Lương hướng dẫn viên'),
(6, 'Khác', 'Các chi phí khác');

-- --------------------------------------------------------

--
-- Table structure for table `guides_info`
--

CREATE TABLE `guides_info` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `identity_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guide_type` enum('domestic','international') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'domestic',
  `certificate_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `languages` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `experience_years` int NOT NULL,
  `specialized_route` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `health_status` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guides_info`
--

INSERT INTO `guides_info` (`id`, `user_id`, `identity_no`, `guide_type`, `certificate_no`, `languages`, `experience_years`, `specialized_route`, `health_status`, `notes`) VALUES
(1, 2, NULL, 'domestic', 'HDV-2024001', 'Tiếng Việt, English, Français', 5, 'Miền Bắc Việt Nam, Tây Bắc', 'Sức khỏe tốt, không bệnh nền', NULL),
(4, 1, NULL, 'domestic', 'HDV-2024001', '', 0, '', '', NULL),
(5, 1, NULL, 'domestic', 'HDV-2024001', '', 0, '', '', NULL),
(6, 1, NULL, 'domestic', 'HDV-2024001', '', 0, '', '', NULL),
(8, 1, '000821030982', 'domestic', 'HDV-2024001', '', 0, '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `guide_assignments`
--

CREATE TABLE `guide_assignments` (
  `id` int NOT NULL,
  `tour_id` int NOT NULL,
  `guide_user_id` int NOT NULL,
  `assignment_date` date NOT NULL,
  `assignment_type` enum('main_guide','assistant_guide','tour_leader') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'main_guide',
  `status` enum('pending','confirmed','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guide_assignments`
--

INSERT INTO `guide_assignments` (`id`, `tour_id`, `guide_user_id`, `assignment_date`, `assignment_type`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '2025-12-01', 'main_guide', 'confirmed', 'HDV chính cho tour Hạ Long', '2025-11-28 11:39:09', '2025-11-28 11:39:09'),
(2, 1, 2, '2025-12-02', 'main_guide', 'confirmed', 'HDV chính cho tour Hạ Long', '2025-11-28 11:39:09', '2025-11-28 11:39:09'),
(3, 1, 2, '2025-12-03', 'main_guide', 'confirmed', 'HDV chính cho tour Hạ Long', '2025-11-28 11:39:09', '2025-11-28 11:39:09');

-- --------------------------------------------------------

--
-- Table structure for table `guide_ratings`
--

CREATE TABLE `guide_ratings` (
  `id` int NOT NULL,
  `guide_user_id` int NOT NULL,
  `booking_id` int NOT NULL,
  `rating` tinyint NOT NULL COMMENT 'Điểm đánh giá từ 1-5',
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guide_ratings`
--

INSERT INTO `guide_ratings` (`id`, `guide_user_id`, `booking_id`, `rating`, `comment`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 5, 'Hướng dẫn viên nhiệt tình, chuyên nghiệp', 4, '2025-11-28 11:39:09', '2025-11-28 11:39:09'),
(2, 2, 2, 4, 'Kiến thức tốt, nhưng hơi ít nói', 5, '2025-11-28 11:39:09', '2025-11-28 11:39:09');

-- --------------------------------------------------------

--
-- Table structure for table `guide_schedules`
--

CREATE TABLE `guide_schedules` (
  `id` int NOT NULL,
  `guide_user_id` int NOT NULL,
  `schedule_date` date NOT NULL,
  `status` enum('available','assigned','on_leave','sick_leave','day_off') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guide_schedules`
--

INSERT INTO `guide_schedules` (`id`, `guide_user_id`, `schedule_date`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, '2025-12-01', 'assigned', 'Hướng dẫn tour Hạ Long 3N2Đ', '2025-11-28 11:39:09', '2025-11-28 11:39:09'),
(2, 2, '2025-12-02', 'assigned', 'Hướng dẫn tour Hạ Long 3N2Đ', '2025-11-28 11:39:09', '2025-11-28 11:39:09'),
(3, 2, '2025-12-03', 'assigned', 'Hướng dẫn tour Hạ Long 3N2Đ', '2025-11-28 11:39:09', '2025-11-28 11:39:09'),
(4, 2, '2025-12-05', 'on_leave', 'Nghỉ phép cá nhân', '2025-11-28 11:39:09', '2025-11-28 11:39:09'),
(5, 2, '2025-12-10', 'available', NULL, '2025-11-28 11:39:09', '2025-11-28 11:39:09'),
(6, 2, '2025-12-11', 'available', NULL, '2025-11-28 11:39:09', '2025-11-28 11:39:09'),
(7, 2, '2025-12-12', 'available', NULL, '2025-11-28 11:39:09', '2025-11-28 11:39:09');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `name`, `address`, `phone`, `email`, `status`) VALUES
(1, 'Khách sạn Mường Thanh', 'Số 1 Đường Lê Thánh Tông, Hà Nội', '024 3823 2808', 'muongthanh@example.com', 1),
(2, 'Fusion Suites Saigon', '3-5-7-9-11-13-15-17-19-21-23-25-27-29-31-33-35-37-39-41-43-45-47-49-51-53-55-57-59-61-63-65-67-69-71-73-75-77-79-81-83-85-87-89-91-93-95-97-99-101-103-105-107-109-111-113-115-117-119-121-123-125-127-129-131-1', '028 3823 9000', 'info@fusionsuitessaigon.com', 1);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','bank_transfer','credit_card','momo','zalopay') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` datetime NOT NULL,
  `status` enum('pending','completed','failed','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `amount`, `payment_method`, `transaction_id`, `payment_date`, `status`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, '7000000.00', 'bank_transfer', 'TRX123456789', '2025-11-05 15:00:00', 'completed', 'Thanh toán đầy đủ', 3, '2025-11-28 11:39:09', '2025-11-28 11:39:09'),
(2, 2, '2000000.00', 'momo', 'MOMO123456789', '2025-11-06 11:00:00', 'completed', 'Đặt cọc 30%', 3, '2025-11-28 11:39:09', '2025-11-28 11:39:09'),
(3, 3, '5000000.00', 'credit_card', 'CC123456789', '2025-11-23 13:30:00', 'completed', 'Thanh toán đợt 1', 3, '2025-11-28 11:39:09', '2025-11-28 11:39:09'),
(4, 5, '10000000.00', 'bank_transfer', '', '2025-11-19 13:15:00', 'completed', 'hoàn tất', NULL, '2025-11-29 06:16:04', '2025-11-29 06:16:04'),
(5, 2, '5555555.00', 'bank_transfer', '', '2025-11-21 16:52:00', 'pending', '', 17, '2025-11-29 09:53:07', '2025-11-29 09:53:07');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `description`) VALUES
(1, 'manage_users', 'Quản lý người dùng'),
(2, 'manage_tours', 'Quản lý tour'),
(3, 'manage_bookings', 'Quản lý đặt tour'),
(4, 'manage_hotels', 'Quản lý khách sạn'),
(5, 'view_reports', 'Xem báo cáo'),
(6, 'manage_expenses', 'Quản lý chi phí'),
(7, 'manage_guides', 'Quản lý hướng dẫn viên');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int NOT NULL,
  `hotel_id` int DEFAULT NULL,
  `room_type_id` int DEFAULT NULL,
  `room_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('available','occupied','maintenance') COLLATE utf8mb4_unicode_ci DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `hotel_id`, `room_type_id`, `room_number`, `status`) VALUES
(1, 1, 1, '101', 'available'),
(2, 1, 1, '102', 'available'),
(3, 1, 2, '201', 'available'),
(4, 1, 2, '202', 'available'),
(5, 1, 3, '301', 'available'),
(6, 2, 2, '101', 'available'),
(7, 2, 2, '102', 'available'),
(8, 2, 3, '201', 'available'),
(9, 2, 4, '301', 'available'),
(10, 2, 4, '302', 'available');

-- --------------------------------------------------------

--
-- Table structure for table `room_assignments`
--

CREATE TABLE `room_assignments` (
  `id` int NOT NULL,
  `booking_id` int DEFAULT NULL,
  `room_id` int DEFAULT NULL,
  `check_in_date` date DEFAULT NULL,
  `check_out_date` date DEFAULT NULL,
  `status` enum('reserved','checked_in','checked_out','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'reserved'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `max_occupancy` int DEFAULT NULL,
  `base_price` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `name`, `description`, `max_occupancy`, `base_price`) VALUES
(1, 'Phòng Đơn', 'Phòng đơn tiêu chuẩn, 1 giường đơn', 1, '1000000.00'),
(2, 'Phòng Đôi', 'Phòng đôi tiêu chuẩn, 1 giường đôi', 2, '1500000.00'),
(3, 'Phòng Đôi Cao Cấp', 'Phòng đôi cao cấp, view đẹp', 2, '2000000.00'),
(4, 'Suite', 'Phòng suite sang trọng', 4, '3500000.00');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('hotel','restaurant','transport','attraction','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `type`, `contact_person`, `phone`, `email`, `address`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Khách sạn Hạ Long Plaza', 'hotel', 'Nguyễn Văn A', '0912345678', 'info@halongplaza.com', 'Số 8, đường Hạ Long, TP. Hạ Long, Quảng Ninh', 'Khách sạn 4 sao với view ra vịnh Hạ Long', 1, '2025-11-28 11:39:09', '2025-11-28 11:39:09'),
(2, 'Nhà hàng Hải Sản Biển Đông', 'restaurant', 'Trần Thị B', '0987654321', 'info@bienandong.vn', '156 Lê Thánh Tông, TP. Hạ Long', 'Chuyên các món hải sản tươi sống', 1, '2025-11-28 11:39:09', '2025-11-28 11:39:09'),
(3, 'Công ty TNHH Vận tải Hạ Long', 'transport', 'Lê Văn C', '0912345679', 'info@halongtransport.com', 'Khu công nghiệp Cái Lân, TP. Hạ Long', 'Cung cấp dịch vụ xe du lịch 4-45 chỗ', 1, '2025-11-28 11:39:09', '2025-11-28 11:39:09'),
(4, 'Khu du lịch Sun World Hạ Long', 'attraction', 'Phạm Thị D', '0912345680', 'info@sunworldhalong.com', 'Bãi Cháy, TP. Hạ Long', 'Khu vui chơi giải trí hàng đầu Hạ Long', 1, '2025-11-28 11:39:09', '2025-11-28 11:39:09');

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `id` int NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tour_type` enum('domestic','international','custom') COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('upcoming','ongoing','finished') COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `created_by_user_id` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `category_id` int DEFAULT NULL,
  `cancellation_policy_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `title`, `description`, `tour_type`, `price`, `image`, `status`, `supplier_id`, `created_by_user_id`, `created_at`, `category_id`, `cancellation_policy_id`) VALUES
(1, 'Tour Hạ Long - Tuần Châu 3N2Đ', 'Khám phá vịnh Hạ Long kỳ quan thiên nhiên thế giới, nghỉ dưỡng tại Tuần Châu', 'domestic', '4000000.00', 'tours/cover/1764422706-t___i_xu___ng__3_.jpeg', 'upcoming', 1, 1, '2025-01-10 08:00:00', 2, NULL),
(2, 'Tour Đà Nẵng - Hội An - Bà Nà 4N3Đ', 'Trải nghiệm thành phố đáng sống, phố cổ Hội An và cầu Vàng Bà Nà', 'domestic', '1500000.00', 'tours/cover/1764488387-t___i_xu___ng__2_.jpeg', 'upcoming', 1, 1, '2025-01-11 09:00:00', 2, NULL),
(3, 'Tour Bangkok - Pattaya 5N4Đ', 'Khám phá thủ đô Thái Lan và thành phố biển Pattaya', 'international', '2000000.00', 'tours/cover/1764488243-t___i_xu___ng__1_.jpeg', 'upcoming', 4, 1, '2025-01-12 10:00:00', 2, NULL),
(4, 'HÀ NỘI – CẦN THƠ – SÓC TRĂNG – CÔN ĐẢO', '', 'domestic', '3000000.00', 'tours/cover/1764488235-t___i_xu___ng.jpeg', 'upcoming', 4, 1, '2025-11-14 20:05:53', 7, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tour_categories`
--

CREATE TABLE `tour_categories` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category_type` enum('domestic','international','custom') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'domestic',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_categories`
--

INSERT INTO `tour_categories` (`id`, `name`, `description`, `category_type`, `created_at`) VALUES
(2, 'Khám phá Văn hóa & Di sản', 'Các chuyến đi tham quan di tích lịch sử, bảo tàng và trải nghiệm văn hóa địa phương.', 'domestic', '2025-11-14 11:15:53'),
(3, 'Du lịch Mạo hiểm & Trekking', 'Các tour trekking, leo núi, thám hiểm hang động dành cho người thích thử thách.', 'domestic', '2025-11-14 11:15:53'),
(7, 'Nghỉ dưỡng Biển Đảo', 'Các tour du lịch tới các bãi biển và đảo nổi tiếng để thư giãn và tắm biển.', 'domestic', '2025-11-25 06:45:07'),
(8, 'Tour Nước ngoài', 'Các chuyến du lịch tới các quốc gia khác, khám phá thế giới.', 'international', '2025-11-25 06:45:38');

-- --------------------------------------------------------

--
-- Table structure for table `tour_expenses`
--

CREATE TABLE `tour_expenses` (
  `id` int NOT NULL,
  `tour_id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `expense_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `is_actual_cost` tinyint(1) NOT NULL,
  `date_incurred` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_expenses`
--

INSERT INTO `tour_expenses` (`id`, `tour_id`, `supplier_id`, `expense_type`, `description`, `amount`, `is_actual_cost`, `date_incurred`) VALUES
(1, 1, 1, 'Khách sạn', 'Chi phí 2 đêm tại Hạ Long Plaza', '1200000.00', 1, '2025-11-01'),
(2, 1, 2, 'Vận chuyển', 'Xe 45 chỗ Hà Nội - Hạ Long', '800000.00', 1, '2025-11-02'),
(3, 1, 3, 'Ẩm thực', 'Các bữa ăn trong tour', '900000.00', 1, '2025-11-03'),
(4, 1, 4, 'Bảo hiểm', 'Bảo hiểm du lịch cho đoàn tour Hạ Long - Tuần Châu 3N2Đ (20 khách)', '1500000.00', 1, '2025-11-01'),
(5, 2, 4, 'Bảo hiểm', 'Bảo hiểm du lịch cho tour Đà Nẵng - Hội An - Bà Nà 4N3Đ (15 khách)', '1800000.00', 1, '2025-11-10'),
(6, 3, 4, 'Bảo hiểm', 'Bảo hiểm du lịch quốc tế cho tour Bangkok - Pattaya 5N4Đ (25 khách)', '3000000.00', 1, '2025-11-15'),
(7, 2, 1, 'Khách sạn', 'Khách sạn 3 đêm tại Đà Nẵng / Hội An cho tour Đà Nẵng - Hội An - Bà Nà 4N3Đ', '4500000.00', 1, '2025-11-18'),
(8, 3, 1, 'Khách sạn', 'Khách sạn 4 đêm tại Bangkok và Pattaya cho tour Thái Lan 5N4Đ', '5200000.00', 1, '2025-11-20'),
(9, 2, 2, 'Vận chuyển', 'Xe đưa đón sân bay và tham quan nội thành Đà Nẵng - Hội An', '3000000.00', 1, '2025-11-18'),
(10, 3, 2, 'Vận chuyển', 'Xe du lịch đưa đón đoàn tại Thái Lan trong suốt hành trình', '3800000.00', 1, '2025-11-20'),
(11, 2, 3, 'Ăn uống', 'Các bữa ăn chính cho đoàn tour Đà Nẵng - Hội An - Bà Nà 4N3Đ', '2600000.00', 1, '2025-11-18'),
(12, 3, 3, 'Ăn uống', 'Bữa tối hải sản cho đoàn tour Bangkok - Pattaya 5N4Đ', '3100000.00', 1, '2025-11-21'),
(13, 1, 2, 'Vé tham quan', 'Vé tham quan hang Sửng Sốt và đảo Titop trong tour Hạ Long', '2400000.00', 1, '2025-11-02'),
(14, 2, 2, 'Vé tham quan', 'Vé tham quan Bà Nà Hills và các điểm tham quan tại Đà Nẵng', '3500000.00', 1, '2025-11-19'),
(15, 1, 1, 'Hướng dẫn viên', 'Chi phí HDV theo đoàn tour Hạ Long 3N2Đ', '1800000.00', 1, '2025-11-01'),
(16, 3, 1, 'Hướng dẫn viên', 'Chi phí HDV theo đoàn tour Thái Lan 5N4Đ', '3200000.00', 1, '2025-11-20'),
(17, 3, 3, 'Giải trí', 'Chi phí show Alcazar tại Pattaya cho đoàn', '2700000.00', 1, '2025-11-21'),
(18, 2, 3, 'Dịch vụ khác', 'Phụ thu dịch vụ giặt là cho đoàn tại khách sạn Đà Nẵng', '400000.00', 1, '2025-11-19');

-- --------------------------------------------------------

--
-- Table structure for table `tour_images`
--

CREATE TABLE `tour_images` (
  `id` int NOT NULL,
  `tour_id` int NOT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_images`
--

INSERT INTO `tour_images` (`id`, `tour_id`, `image_path`, `created_at`) VALUES
(1, 1, 'https://storage.googleapis.com/vietnam-travel-data/halong/halong_bay_cruise.jpg', '2025-11-14 18:57:10'),
(2, 1, 'https://storage.googleapis.com/vietnam-travel-data/halong/halong_kayak_cave.jpg', '2025-11-14 18:57:10'),
(3, 1, 'https://storage.googleapis.com/vietnam-travel-data/halong/halong_sunset.jpg', '2025-11-14 18:57:10');

-- --------------------------------------------------------

--
-- Table structure for table `tour_itineraries`
--

CREATE TABLE `tour_itineraries` (
  `id` int NOT NULL,
  `tour_id` int NOT NULL,
  `day_number` int NOT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `activities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tour_itinerary_items`
--

CREATE TABLE `tour_itinerary_items` (
  `id` int NOT NULL,
  `tour_id` int NOT NULL,
  `day_number` int NOT NULL,
  `activity_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `slot` enum('morning','noon','afternoon','evening') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `meal_plan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_itinerary_items`
--

INSERT INTO `tour_itinerary_items` (`id`, `tour_id`, `day_number`, `activity_time`, `end_time`, `slot`, `title`, `details`, `meal_plan`) VALUES
(181, 4, 1, '07:30:00', '11:30:00', 'morning', 'Buổi sáng', 'Đang cập nhật hoạt động buổi sáng', ''),
(182, 4, 1, '11:30:00', '13:30:00', 'noon', 'Buổi trưa', 'Đang cập nhật bữa trưa', ''),
(183, 4, 1, '14:00:00', '17:30:00', 'afternoon', 'Buổi chiều', 'Đang cập nhật hoạt động buổi chiều', ''),
(184, 4, 1, '19:00:00', '21:00:00', 'evening', 'Buổi tối', 'Đang cập nhật hoạt động buổi tối', ''),
(185, 4, 2, '07:30:00', '11:30:00', 'morning', 'Buổi sáng', 'Đang cập nhật hoạt động buổi sáng', ''),
(186, 4, 2, '11:30:00', '13:30:00', 'noon', 'Buổi trưa', 'Đang cập nhật bữa trưa', ''),
(187, 4, 2, '14:00:00', '17:30:00', 'afternoon', 'Buổi chiều', 'Đang cập nhật hoạt động buổi chiều', ''),
(188, 4, 2, '19:00:00', '21:00:00', 'evening', 'Buổi tối', 'Đang cập nhật hoạt động buổi tối', ''),
(189, 4, 3, '07:30:00', '11:30:00', 'morning', 'Buổi sáng', 'Đang cập nhật hoạt động buổi sáng', ''),
(190, 4, 3, '11:30:00', '13:30:00', 'noon', 'Buổi trưa', 'Đang cập nhật bữa trưa', ''),
(191, 4, 3, '14:00:00', '17:30:00', 'afternoon', 'Buổi chiều', 'Đang cập nhật hoạt động buổi chiều', ''),
(192, 4, 3, '19:00:00', '21:00:00', 'evening', 'Buổi tối', 'Đang cập nhật hoạt động buổi tối', ''),
(193, 3, 1, '07:30:00', '11:30:00', 'morning', 'Buổi sáng', 'Đang cập nhật hoạt động buổi sáng', ''),
(194, 3, 1, '11:30:00', '13:30:00', 'noon', 'Buổi trưa', 'Đang cập nhật bữa trưa', ''),
(195, 3, 1, '14:00:00', '17:30:00', 'afternoon', 'Buổi chiều', 'Đang cập nhật hoạt động buổi chiều', ''),
(196, 3, 1, '19:00:00', '21:00:00', 'evening', 'Buổi tối', 'Đang cập nhật hoạt động buổi tối', ''),
(197, 3, 2, '07:30:00', '11:30:00', 'morning', 'Buổi sáng', 'Đang cập nhật hoạt động buổi sáng', ''),
(198, 3, 2, '11:30:00', '13:30:00', 'noon', 'Buổi trưa', 'Đang cập nhật bữa trưa', ''),
(199, 3, 2, '14:00:00', '17:30:00', 'afternoon', 'Buổi chiều', 'Đang cập nhật hoạt động buổi chiều', ''),
(200, 3, 2, '19:00:00', '21:00:00', 'evening', 'Buổi tối', 'Đang cập nhật hoạt động buổi tối', ''),
(201, 3, 3, '07:30:00', '11:30:00', 'morning', 'Buổi sáng', 'Đang cập nhật hoạt động buổi sáng', ''),
(202, 3, 3, '11:30:00', '13:30:00', 'noon', 'Buổi trưa', 'Đang cập nhật bữa trưa', ''),
(203, 3, 3, '14:00:00', '17:30:00', 'afternoon', 'Buổi chiều', 'Đang cập nhật hoạt động buổi chiều', ''),
(204, 3, 3, '19:00:00', '21:00:00', 'evening', 'Buổi tối', 'Đang cập nhật hoạt động buổi tối', ''),
(205, 3, 4, '07:30:00', '11:30:00', 'morning', 'Buổi sáng', 'Đang cập nhật hoạt động buổi sáng', ''),
(206, 3, 4, '11:30:00', '13:30:00', 'noon', 'Buổi trưa', 'Đang cập nhật bữa trưa', ''),
(207, 3, 4, '14:00:00', '17:30:00', 'afternoon', 'Buổi chiều', 'Đang cập nhật hoạt động buổi chiều', ''),
(208, 3, 4, '19:00:00', '21:00:00', 'evening', 'Buổi tối', 'Đang cập nhật hoạt động buổi tối', ''),
(209, 3, 5, '07:30:00', '11:30:00', 'morning', 'Buổi sáng', 'Đang cập nhật hoạt động buổi sáng', ''),
(210, 3, 5, '11:30:00', '13:30:00', 'noon', 'Buổi trưa', 'Đang cập nhật bữa trưa', ''),
(211, 3, 5, '14:00:00', '17:30:00', 'afternoon', 'Buổi chiều', 'Đang cập nhật hoạt động buổi chiều', ''),
(212, 3, 5, '19:00:00', '21:00:00', 'evening', 'Buổi tối', 'Đang cập nhật hoạt động buổi tối', '');

-- --------------------------------------------------------

--
-- Table structure for table `tour_logs`
--

CREATE TABLE `tour_logs` (
  `id` int NOT NULL,
  `tour_id` int NOT NULL,
  `guide_user_id` int NOT NULL,
  `log_date` datetime NOT NULL,
  `incident_details` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_feedback` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `weather` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_logs`
--

INSERT INTO `tour_logs` (`id`, `tour_id`, `guide_user_id`, `log_date`, `incident_details`, `customer_feedback`, `weather`) VALUES
(1, 1, 2, '2025-12-01 20:00:00', 'Xe đến điểm đón khách chậm 15 phút do ùn tắc giao thông', 'Khách hàng hài lòng với dịch vụ', 'Nắng nhẹ'),
(2, 1, 2, '2025-12-02 21:00:00', 'Một khách bị say sóng khi đi thuyền, đã xử lý kịp thời', 'Khách hàng rất thích cảnh đẹp vịnh Hạ Long', 'Nhiều mây'),
(3, 3, 1, '2025-11-28 14:34:00', 'khách trải nghiệm tốt', 'rất tốt', 'mưa');

-- --------------------------------------------------------

--
-- Table structure for table `tour_log_images`
--

CREATE TABLE `tour_log_images` (
  `id` int NOT NULL,
  `log_id` int NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tour_prices`
--

CREATE TABLE `tour_prices` (
  `id` int NOT NULL,
  `tour_id` int NOT NULL,
  `adult_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `child_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `infant_price` decimal(12,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_prices`
--

INSERT INTO `tour_prices` (`id`, `tour_id`, `adult_price`, `child_price`, `infant_price`) VALUES
(4, 4, '3000000.00', '0.00', '0.00'),
(5, 3, '2000000.00', '0.00', '0.00'),
(7, 1, '4000000.00', '0.00', '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `tour_schedules`
--

CREATE TABLE `tour_schedules` (
  `id` int NOT NULL,
  `tour_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `guide_user_id` int NOT NULL,
  `driver_user_id` int NOT NULL,
  `max_capacity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_schedules`
--

INSERT INTO `tour_schedules` (`id`, `tour_id`, `start_date`, `end_date`, `guide_user_id`, `driver_user_id`, `max_capacity`) VALUES
(1, 1, '2025-12-01', '2025-12-03', 12, 2, 20),
(3, 3, '2025-12-10', '2025-12-14', 2, 2, 25),
(4, 3, '2025-11-06', '2025-11-12', 16, 1, 20);

-- --------------------------------------------------------

--
-- Table structure for table `tour_suppliers`
--

CREATE TABLE `tour_suppliers` (
  `id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_type` enum('hotel','transport','restaurant','ticket','insurance') COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_suppliers`
--

INSERT INTO `tour_suppliers` (`id`, `name`, `contact_person`, `service_type`, `phone`) VALUES
(1, 'Khách sạn Hạ Long Plaza', 'Nguyễn Văn A', 'hotel', '02363849201'),
(2, 'Công ty xe khách Sao Việt', 'Trần Văn B', 'transport', '02437192834'),
(3, 'Nhà hàng Hải Sản Biển Đông', 'Lê Thị C', 'restaurant', '02363849202'),
(4, 'Công ty bảo hiểm Bảo Việt', 'Phạm Văn D', 'insurance', '02437192835');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identity_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','guide','sales','traveler') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `avatar`, `identity_no`, `password`, `role`, `is_active`, `created_at`) VALUES
(1, 'hoàng văn thái', 'admin@tripmate.com', '0123456789', NULL, NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, '2025-01-01 10:00:00'),
(2, 'Nguyễn Văn phúc', 'guide1@tripmate.com', '0123456790', 'assets/images/guides/default-avatar.png', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'guide', 1, '2025-01-02 09:00:00'),
(3, 'Trần Thị diệu', 'sales1@tripmate.com', '0123456791', NULL, NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sales', 1, '2025-01-02 10:00:00'),
(4, 'Lê Văn Khánh', 'customer1@email.com', '0123456792', NULL, NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'traveler', 1, '2025-01-03 11:00:00'),
(5, 'Phạm Thị hạnh', 'customer2@email.com', '0123456793', NULL, NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'traveler', 1, '2025-01-03 12:00:00'),
(12, 'vu anh hao', 'hvu572@gmail.com', '0969775850', 'assets/images/guides/default-avatar.png', NULL, '$2y$10$cf1IW2WjFtUCjGTaZQU.XekKaPBIzHq93Q751P5MSLjlW4MBeyjfq', 'guide', 1, '2025-11-21 15:44:11'),
(13, 'vu anh hao', 'hvu574@gmail.com', '0969775850', 'assets/images/guides/default-avatar.png', NULL, '$2y$10$yGl7nL7jyJkpqYbFogQceu31Sg8MJ3f2YBkstFSj/7.UmBXERC492', 'guide', 1, '2025-11-21 15:49:29'),
(16, 'Nguyễn Văn phúc', 'guide@gmail.com', '0969775850', 'assets/images/guides/default-avatar.png', NULL, '$2y$10$dRZdRxvBR8RDnL7J3I/CjO2gtYrPBeDEGTqzKYg4D1RTBtD47yFgK', 'guide', 1, '2025-11-26 19:05:37'),
(17, 'vu anh hao', 'hvu572766@gmail.com', '0969775850', NULL, NULL, '$2y$10$ml58PmTzrNXF1ALXZ1kNau4sSzVLCWP86NVXfEkJHmDpvNuPAQ6JG', 'admin', 1, '2025-11-26 20:38:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_id` (`tour_id`),
  ADD KEY `customer_user_id` (`customer_user_id`),
  ADD KEY `sales_user_id` (`sales_user_id`);

--
-- Indexes for table `booking_guests`
--
ALTER TABLE `booking_guests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `booking_requests`
--
ALTER TABLE `booking_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `guest_id` (`guest_id`);

--
-- Indexes for table `booking_status_logs`
--
ALTER TABLE `booking_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `changed_by_user_id` (`changed_by_user_id`);

--
-- Indexes for table `cancellation_policies`
--
ALTER TABLE `cancellation_policies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_id` (`tour_id`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `tour_id` (`tour_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guides_info`
--
ALTER TABLE `guides_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `guide_assignments`
--
ALTER TABLE `guide_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_id` (`tour_id`),
  ADD KEY `guide_user_id` (`guide_user_id`),
  ADD KEY `assignment_date` (`assignment_date`);

--
-- Indexes for table `guide_ratings`
--
ALTER TABLE `guide_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guide_user_id` (`guide_user_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `guide_schedules`
--
ALTER TABLE `guide_schedules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `guide_date` (`guide_user_id`,`schedule_date`),
  ADD KEY `schedule_date` (`schedule_date`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `status` (`status`),
  ADD KEY `payment_date` (`payment_date`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `room_assignments`
--
ALTER TABLE `room_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by_user_id` (`created_by_user_id`),
  ADD KEY `fk_tour_category` (`category_id`),
  ADD KEY `idx_tours_supplier_id` (`supplier_id`),
  ADD KEY `fk_tours_cancellation_policy` (`cancellation_policy_id`);

--
-- Indexes for table `tour_categories`
--
ALTER TABLE `tour_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `tour_expenses`
--
ALTER TABLE `tour_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_id` (`tour_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `tour_images`
--
ALTER TABLE `tour_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_id` (`tour_id`);

--
-- Indexes for table `tour_itineraries`
--
ALTER TABLE `tour_itineraries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_id` (`tour_id`);

--
-- Indexes for table `tour_itinerary_items`
--
ALTER TABLE `tour_itinerary_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tii_tour_day` (`tour_id`,`day_number`);

--
-- Indexes for table `tour_logs`
--
ALTER TABLE `tour_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_id` (`tour_id`),
  ADD KEY `guide_user_id` (`guide_user_id`);

--
-- Indexes for table `tour_log_images`
--
ALTER TABLE `tour_log_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `log_id` (`log_id`);

--
-- Indexes for table `tour_prices`
--
ALTER TABLE `tour_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_tour` (`tour_id`),
  ADD KEY `idx_tour_id` (`tour_id`);

--
-- Indexes for table `tour_schedules`
--
ALTER TABLE `tour_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_id` (`tour_id`),
  ADD KEY `guide_user_id` (`guide_user_id`),
  ADD KEY `driver_user_id` (`driver_user_id`);

--
-- Indexes for table `tour_suppliers`
--
ALTER TABLE `tour_suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `booking_guests`
--
ALTER TABLE `booking_guests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `booking_requests`
--
ALTER TABLE `booking_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `booking_status_logs`
--
ALTER TABLE `booking_status_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `cancellation_policies`
--
ALTER TABLE `cancellation_policies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `guides_info`
--
ALTER TABLE `guides_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `guide_assignments`
--
ALTER TABLE `guide_assignments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `guide_ratings`
--
ALTER TABLE `guide_ratings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `guide_schedules`
--
ALTER TABLE `guide_schedules`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `room_assignments`
--
ALTER TABLE `room_assignments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tour_categories`
--
ALTER TABLE `tour_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tour_expenses`
--
ALTER TABLE `tour_expenses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tour_images`
--
ALTER TABLE `tour_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tour_itineraries`
--
ALTER TABLE `tour_itineraries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `tour_itinerary_items`
--
ALTER TABLE `tour_itinerary_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- AUTO_INCREMENT for table `tour_logs`
--
ALTER TABLE `tour_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tour_log_images`
--
ALTER TABLE `tour_log_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tour_prices`
--
ALTER TABLE `tour_prices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tour_schedules`
--
ALTER TABLE `tour_schedules`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tour_suppliers`
--
ALTER TABLE `tour_suppliers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`customer_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`sales_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `booking_guests`
--
ALTER TABLE `booking_guests`
  ADD CONSTRAINT `booking_guests_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `booking_requests`
--
ALTER TABLE `booking_requests`
  ADD CONSTRAINT `booking_requests_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `booking_requests_ibfk_2` FOREIGN KEY (`guest_id`) REFERENCES `booking_guests` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `booking_status_logs`
--
ALTER TABLE `booking_status_logs`
  ADD CONSTRAINT `booking_status_logs_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `booking_status_logs_ibfk_2` FOREIGN KEY (`changed_by_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`),
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`),
  ADD CONSTRAINT `expenses_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `guides_info`
--
ALTER TABLE `guides_info`
  ADD CONSTRAINT `guides_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`),
  ADD CONSTRAINT `rooms_ibfk_2` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`);

--
-- Constraints for table `room_assignments`
--
ALTER TABLE `room_assignments`
  ADD CONSTRAINT `room_assignments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  ADD CONSTRAINT `room_assignments_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `tours`
--
ALTER TABLE `tours`
  ADD CONSTRAINT `fk_tour_category` FOREIGN KEY (`category_id`) REFERENCES `tour_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tours_cancellation_policy` FOREIGN KEY (`cancellation_policy_id`) REFERENCES `cancellation_policies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tours_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `tour_suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tours_ibfk_1` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `tour_expenses`
--
ALTER TABLE `tour_expenses`
  ADD CONSTRAINT `tour_expenses_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `tour_expenses_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `tour_suppliers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `tour_images`
--
ALTER TABLE `tour_images`
  ADD CONSTRAINT `tour_images_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `tour_itineraries`
--
ALTER TABLE `tour_itineraries`
  ADD CONSTRAINT `tour_itineraries_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `tour_itinerary_items`
--
ALTER TABLE `tour_itinerary_items`
  ADD CONSTRAINT `tour_itinerary_items_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `tour_logs`
--
ALTER TABLE `tour_logs`
  ADD CONSTRAINT `tour_logs_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `tour_logs_ibfk_2` FOREIGN KEY (`guide_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `tour_log_images`
--
ALTER TABLE `tour_log_images`
  ADD CONSTRAINT `fk_tour_log_images_logs` FOREIGN KEY (`log_id`) REFERENCES `tour_logs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_schedules`
--
ALTER TABLE `tour_schedules`
  ADD CONSTRAINT `tour_schedules_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `tour_schedules_ibfk_2` FOREIGN KEY (`guide_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `tour_schedules_ibfk_3` FOREIGN KEY (`driver_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
