-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 23, 2025 at 11:43 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pixellink`
--

-- --------------------------------------------------------

--
-- Table structure for table `client_job_requests`
--

CREATE TABLE `client_job_requests` (
  `request_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `designer_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `budget` varchar(100) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `posted_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('open','proposed','awaiting_deposit_verification','assigned','draft_submitted','awaiting_final_payment','final_payment_verification','completed','cancelled','rejected','pending_deposit') DEFAULT 'open',
  `revision_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_job_requests`
--

INSERT INTO `client_job_requests` (`request_id`, `client_id`, `designer_id`, `title`, `description`, `attachment_path`, `category_id`, `budget`, `deadline`, `posted_date`, `status`, `revision_count`) VALUES
(1, 3, NULL, 'ต้องการออกแบบแบนเนอร์โฆษณา', 'แบนเนอร์สำหรับโปรโมทสินค้าใหม่ 5 ชิ้น ขนาดต่างๆ', NULL, 1, '2,000-5,000 บาท', '2025-06-20', '2025-06-07 15:44:37', 'assigned', 0),
(2, 5, NULL, 'พัฒนาหน้า Landing Page', 'สำหรับแคมเปญการตลาดใหม่ เน้นการแปลงผู้เข้าชมเป็นลูกค้า', NULL, 3, '15,000-30,000 บาท', '2025-07-15', '2025-06-07 15:44:37', 'assigned', 0),
(3, 3, NULL, 'จ้างนักออกแบบ Package Product', 'ออกแบบบรรจุภัณฑ์สินค้าใหม่ 3 ชิ้น', NULL, 1, '8,000-15,000 บาท', '2025-07-01', '2025-06-07 15:44:37', 'open', 0),
(4, 3, 2, 'ป้ายอบจ.', 'ทำมาเหอะ', NULL, NULL, '5000', '2025-09-05', '2025-09-01 15:31:15', 'assigned', 0),
(33, 3, 2, 'ทำ ux/ui เว็บสั่งซื้อเสื้อผ้า', 'เน้นโทนสีดำ โมเดิร์น สไตล์ยุคใหม่', NULL, 2, '60000', '2025-09-21', '2025-09-14 03:47:01', 'awaiting_final_payment', 0),
(41, 3, 2, 'sdfsdfdsf', 'sdfsdfdsf', NULL, 6, '30000', '2025-09-27', '2025-09-14 10:14:08', 'awaiting_deposit_verification', 0),
(42, 3, 2, 'ไำพไำพไำพ', 'ไำพไำพำไพ', NULL, 4, '50000', '2025-09-26', '2025-09-15 09:41:26', 'awaiting_deposit_verification', 0),
(46, 3, 2, '252525', '5000', 'uploads/job_attachments/job_68c81ae9e93c5_1757944553.jpg', 6, '3000', '2025-09-19', '2025-09-15 13:55:53', 'awaiting_deposit_verification', 0),
(47, 3, 2, 'tertert', 'erer', NULL, 6, '5000', '2025-09-26', '2025-09-15 17:57:51', 'awaiting_deposit_verification', 0),
(48, 3, 2, 'hhhhh', 'hhhh', 'uploads/job_attachments/job_68c853b58830d_1757959093.png', 4, '3000', '2025-09-25', '2025-09-15 17:58:13', 'rejected', 0),
(49, 3, 2, '่้ืิเอดอดอดอ', 'ทื้ิอ', 'uploads/job_attachments/job_68c85416189b0_1757959190.jpg', 4, '5000', '2025-09-26', '2025-09-15 17:59:50', 'rejected', 0),
(50, 3, 2, '1231321', '546545', 'uploads/job_attachments/job_68c86c60a0d6a_1757965408.jpg', 2, '5000', '2025-09-26', '2025-09-15 19:43:28', 'rejected', 0),
(51, 3, 2, '44444', '4444444444444', 'uploads/job_attachments/job_68c86c6e6e2ce_1757965422.png', 6, '546456', '2025-09-27', '2025-09-15 19:43:42', 'rejected', 0),
(52, 3, 2, '11111111111', '111111111111111', 'uploads/job_attachments/job_68c876751dddf_1757967989.png', 6, '5000', '2025-09-26', '2025-09-15 20:26:29', 'rejected', 0),
(53, 3, 2, '2222222222222222', '222222222', NULL, 2, '40000', '2025-10-05', '2025-09-15 20:26:41', 'rejected', 0),
(54, 3, 2, '3333333333', '33333333333', 'uploads/job_attachments/job_68c8769012014_1757968016.png', 3, '6000', '2025-09-27', '2025-09-15 20:26:56', 'rejected', 0),
(55, 3, 2, 'กฟหกฟหกฟ', 'ฟหกฟหกหฟ', 'uploads/job_attachments/job_68c8950144ea0_1757975809.jpg', 4, '2000', '2025-09-28', '2025-09-15 22:36:49', 'awaiting_deposit_verification', 0),
(56, 3, 2, '55555555553', '24242', 'uploads/job_attachments/job_68c895138799c_1757975827.jpg', 5, '232323', '2025-10-03', '2025-09-15 22:37:07', 'awaiting_deposit_verification', 0),
(57, 3, 2, 'test', 'tttt', 'uploads/job_attachments/job_68c8c93f12c9f_1757989183.png', 5, '3000', '2025-09-26', '2025-09-16 02:19:43', 'completed', 0),
(58, 3, 2, 'ไำพำไพำไพ', 'หกดหกดหกด', 'uploads/job_attachments/job_68ca530c87710_1758089996.png', 4, '50000', '2025-09-20', '2025-09-17 06:19:56', 'awaiting_deposit_verification', 0),
(59, 3, 2, 'tttttttt', 'ttttttttttttttt', 'uploads/job_attachments/job_68cad88884ef8_1758124168.jfif', 4, '50000', '2025-09-26', '2025-09-17 15:49:28', 'cancelled', 0),
(60, 3, 2, 'yyyyyyyyyyyyy', 'yyyyyyyyyy', 'uploads/job_attachments/job_68cad897630af_1758124183.jfif', 4, '3000', '2025-10-04', '2025-09-17 15:49:43', 'awaiting_deposit_verification', 0),
(61, 3, 2, 'หหหหหหหหหหห', 'หหหหหหหหหหหหหหหหห', 'uploads/job_attachments/job_68caeace946cb_1758128846.jfif', 4, '30000', '2025-10-05', '2025-09-17 17:07:26', 'awaiting_deposit_verification', 0),
(62, 3, 2, 'ปปปปปปปปปปป', 'หหหหหหหหห', 'uploads/job_attachments/job_68caeade9b7cc_1758128862.jfif', 6, '3000', '2025-09-27', '2025-09-17 17:07:42', 'awaiting_deposit_verification', 0),
(63, 3, 2, 'เเเเเเเเเเเเเเเเเ', 'เเเเเเเเเเเเเเเ', 'uploads/job_attachments/job_68caed946f616_1758129556.png', 4, '3000', '2025-09-25', '2025-09-17 17:19:16', 'awaiting_deposit_verification', 0),
(64, 3, 2, 'ggggggggggggg', 'ggggggggggggggggggggggg', 'uploads/job_attachments/job_68cb0123a4a5c_1758134563.png', 4, '30000', '2025-10-03', '2025-09-17 18:42:43', 'awaiting_deposit_verification', 0),
(65, 3, 2, '414141', '414141', 'uploads/job_attachments/job_68cb0132d2476_1758134578.png', 4, '3000', '2025-10-04', '2025-09-17 18:42:58', '', 0),
(66, 3, 2, '456456', '54565465', 'uploads/job_attachments/job_68cb02d4d8246_1758134996.png', 6, '5000', '2025-10-04', '2025-09-17 18:49:56', '', 0),
(67, 3, 2, 'ฟกดฟกด', 'ฟหกฟดหฟด', 'uploads/job_attachments/job_68cb02ea86d30_1758135018.png', 2, '3000', '2025-09-28', '2025-09-17 18:50:18', '', 0),
(68, 3, 2, 'jjjjjjjjjjj', 'kkkkkkkkkkkkk', 'uploads/job_attachments/job_68cb02fb6cc2b_1758135035.png', 4, '5000', '2025-09-26', '2025-09-17 18:50:35', '', 0),
(69, 3, 2, '8546456', '48645456', 'uploads/job_attachments/job_68cb0690e1481_1758135952.png', 6, '3000', '2025-09-26', '2025-09-17 19:05:52', '', 0),
(70, 3, 2, 'ppppppppppppp', '456456', 'uploads/job_attachments/job_68cb06a371f02_1758135971.png', 2, '8000', '2025-09-25', '2025-09-17 19:06:11', '', 0),
(71, 3, 2, '898789789456132', '65646512', 'uploads/job_attachments/job_68cb06b4a2fe8_1758135988.png', 6, '9000', '2025-10-03', '2025-09-17 19:06:28', '', 0),
(72, 3, 2, 'lllllllll[lp[p', ',mpnbuvycgvubin', 'uploads/job_attachments/job_68cb06c5b4f48_1758136005.png', 6, '6000', '2025-09-25', '2025-09-17 19:06:45', '', 0),
(73, 3, 2, 'kjhgf', 'kjhgfvc', 'uploads/job_attachments/job_68cb06d54252a_1758136021.png', 2, '67888', '2025-09-28', '2025-09-17 19:07:01', '', 0),
(74, 3, 2, '456465456', '516156', 'uploads/job_attachments/job_68cb28c4dd1cc_1758144708.png', 6, '3000', '2025-10-05', '2025-09-17 21:31:48', 'awaiting_deposit_verification', 0),
(75, 3, 2, '56456', '458', 'uploads/job_attachments/job_68cb28d733f19_1758144727.png', 5, '500', '2025-09-28', '2025-09-17 21:32:07', 'awaiting_deposit_verification', 0),
(76, 3, 2, '8945612', '1652', 'uploads/job_attachments/job_68cb28e8a4a2c_1758144744.png', 6, '60000', '2025-09-28', '2025-09-17 21:32:24', 'awaiting_deposit_verification', 0),
(77, 3, 2, 'test', 'test', 'uploads/job_attachments/job_68cb7d8406b89_1758166404.png', 4, '3000', '2025-09-27', '2025-09-18 03:33:24', 'awaiting_deposit_verification', 0),
(78, 3, 2, 'ออกแบบ ux', 'tetst', 'uploads/job_attachments/job_68cb7f39a7b75_1758166841.png', 2, '3000', '2025-09-26', '2025-09-18 03:40:41', 'assigned', 0),
(79, 3, 2, 'ยินดีกับรักครั้งใหม่.', '..', 'uploads/job_attachments/job_68ce6756c9d09_1758357334.png', 4, '3000', '2025-09-28', '2025-09-20 08:35:34', '', 0),
(80, 3, 2, '111111111', '11111111111111', 'uploads/job_attachments/job_68ce6b2ce493e_1758358316.png', 4, '3000', '2025-09-27', '2025-09-20 08:51:56', 'pending_deposit', 0),
(81, 3, 2, '22222222222', '222222222222222', 'uploads/job_attachments/job_68ce6b4aab7c9_1758358346.png', 6, '4000', '2025-10-03', '2025-09-20 08:52:26', 'awaiting_deposit_verification', 0),
(82, 3, 2, '33333', '3333', NULL, 5, '5000', '2025-10-05', '2025-09-20 08:52:47', 'awaiting_deposit_verification', 0),
(83, 3, 2, 'เห้ยยยยยยย', 'หดเ้่แเา้สิ', 'uploads/job_attachments/job_68ce73a19e7a4_1758360481.png', 5, '800', '2025-10-03', '2025-09-20 09:28:01', 'assigned', 0),
(84, 3, 2, 'work', '555555', 'uploads/job_attachments/job_68ce789712dca_1758361751.png', 6, '400', '2025-09-27', '2025-09-20 09:49:11', 'pending_deposit', 0),
(85, 3, 2, 'สยสยสยสยสยสย', 'สยสยสยสยสยสย', 'uploads/job_attachments/job_68ce868f8350a_1758365327.png', 2, '3000', '2025-09-27', '2025-09-20 10:48:47', 'cancelled', 0),
(86, 3, 2, 'ง่วงมาก', 'ฟหกฟหก', 'uploads/job_attachments/job_68cf183982cd7_1758402617.png', 6, '350', '2025-09-27', '2025-09-20 21:10:17', 'open', 0),
(87, 3, 2, 'gegregrg', 'ergergerger', 'uploads/job_attachments/job_68d18cb446ddf_1758563508.jpg', 4, '350', '2025-10-03', '2025-09-22 17:51:48', 'pending_deposit', 0),
(88, 3, 2, 'ertretert', 'tertert', 'uploads/job_attachments/job_68d18d7c98a66_1758563708.png', 6, '6500', '2025-10-02', '2025-09-22 17:55:08', 'assigned', 0),
(89, 3, 2, 'test ชำระเงิน', 'test ชำระเงิน', 'uploads/job_attachments/job_68d1a186d04b0_1758568838.jpg', 4, '600', '2025-10-04', '2025-09-22 19:20:38', 'assigned', 0),
(90, 3, 2, 'test ชำระเงิน2', 'test ชำระเงิน2', '../uploads/draft_files/draft_90_68d2e4f0ca31b_C04.pdf', 4, '800', '2025-10-03', '2025-09-22 19:42:27', 'draft_submitted', 0),
(91, 3, 2, 'test ชำระเงิน3', 'test ชำระเงิน3', '../uploads/draft_files/draft_91_68d2e4c0460eb_pixellink-assistants-main.rar', 4, '670', '2025-10-05', '2025-09-22 19:53:15', 'final_payment_verification', 1),
(92, 3, 2, 'test ส่งไฟล์ฉบับร่าง1', 'test ส่งไฟล์ฉบับร่าง1', '../uploads/draft_files/draft_92_68d2d6a5c1142_IMG_Desing.png', 4, '3600', '2025-10-03', '2025-09-22 21:37:21', 'final_payment_verification', 2),
(93, 3, 2, 'ส่งฉบับร่าง 2', 'ส่งฉบับร่าง 2', '../uploads/draft_files/draft_93_68d274add8f12_logo.png', 6, '6000', '2025-09-25', '2025-09-22 22:41:43', 'final_payment_verification', 0),
(94, 3, 2, 'test รวมtab', 'test รวมtab', '../uploads/draft_files/draft_94_68d2e4a658455_pixellink-assistants-main.rar', 6, '60', '2025-10-03', '2025-09-23 18:17:37', 'awaiting_deposit_verification', 0),
(95, 3, 2, 'สวัสดีจ้างงาน', 'สวัสดีจ้างงาน', '../uploads/draft_files/draft_95_68d2e8bab88a0_S__5423114_0.jpg', 6, '300', '2025-09-26', '2025-09-23 18:33:30', 'final_payment_verification', 0),
(96, 5, 4, 'สวัสดีจ้างงาน', 'สวัสดีจ้างงาน', '../uploads/draft_files/draft_96_68d2f8ead7443_6.png', 2, '300', '2025-09-25', '2025-09-23 18:49:53', 'final_payment_verification', 1);

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `contract_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `designer_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `agreed_price` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `contract_status` enum('pending','active','awaiting_final_payment','completed','cancelled') DEFAULT 'pending',
  `payment_status` enum('pending','paid','partially_paid','refunded') DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contracts`
--

INSERT INTO `contracts` (`contract_id`, `request_id`, `designer_id`, `client_id`, `agreed_price`, `start_date`, `end_date`, `contract_status`, `payment_status`, `created_at`) VALUES
(1, 1, 2, 3, 3500.00, '2025-06-02', '2025-06-15', 'active', 'pending', '2025-06-09 14:41:52'),
(2, 4, 2, 3, 5000.00, '2025-09-14', NULL, 'active', 'pending', '2025-09-14 10:35:58'),
(3, 33, 2, 3, 60000.00, '2025-09-14', NULL, 'active', 'pending', '2025-09-14 16:20:52'),
(4, 57, 2, 3, 5000.00, '2025-09-16', '2025-09-17', 'completed', 'pending', '2025-09-17 03:53:38'),
(5, 49, 2, 3, 3000.00, '2025-09-16', NULL, 'pending', 'pending', '2025-09-17 03:58:22'),
(6, 48, 2, 3, 5000.00, '2025-09-17', NULL, 'pending', 'pending', '2025-09-17 13:22:55'),
(7, 58, 2, 3, 3000.00, '2025-09-17', NULL, 'pending', 'pending', '2025-09-17 14:35:19'),
(8, 56, 2, 3, 50000.00, '2025-09-17', NULL, 'pending', 'pending', '2025-09-17 14:35:36'),
(9, 47, 2, 3, 30000.00, '2025-09-17', NULL, 'pending', 'pending', '2025-09-17 15:04:15'),
(10, 46, 2, 3, 30000.00, '2025-09-17', NULL, 'pending', 'pending', '2025-09-17 15:37:30'),
(11, 42, 2, 3, 35000.00, '2025-09-17', NULL, 'pending', 'pending', '2025-09-17 17:12:28'),
(12, 41, 2, 3, 3000.00, '2025-09-17', NULL, 'pending', 'pending', '2025-09-17 17:21:46'),
(13, 60, 2, 3, 3500.00, '2025-09-17', NULL, 'pending', 'pending', '2025-09-17 22:52:53'),
(14, 55, 2, 3, 3000.00, '2025-09-17', NULL, 'pending', 'pending', '2025-09-18 00:03:37'),
(15, 63, 2, 3, 3000.00, '2025-09-17', NULL, 'pending', 'pending', '2025-09-18 00:24:57'),
(16, 62, 2, 3, 3000.00, '2025-09-17', NULL, 'pending', 'pending', '2025-09-18 00:26:08'),
(17, 61, 2, 3, 30000.00, '2025-09-17', NULL, 'pending', 'pending', '2025-09-18 00:38:21'),
(18, 89, 2, 3, 700.00, '2025-09-23', NULL, 'active', 'pending', '2025-09-23 02:21:30'),
(19, 90, 2, 3, 900.00, '2025-09-23', NULL, 'active', 'pending', '2025-09-23 02:43:31'),
(20, 88, 2, 3, 650.00, '2025-09-23', NULL, 'active', 'pending', '2025-09-23 02:50:09'),
(21, 91, 2, 3, 890.00, '2025-09-23', NULL, 'active', 'pending', '2025-09-23 02:54:06'),
(22, 92, 2, 3, 3800.00, '2025-09-23', NULL, 'active', 'pending', '2025-09-23 04:38:10'),
(23, 93, 2, 3, 6500.00, '2025-09-23', NULL, 'active', 'pending', '2025-09-23 05:42:38'),
(24, 94, 2, 3, 65.00, '2025-09-24', NULL, 'active', 'pending', '2025-09-24 01:18:21'),
(25, 95, 2, 3, 350.00, '2025-09-24', NULL, 'active', 'pending', '2025-09-24 01:34:35'),
(26, 96, 4, 5, 350.00, '2025-09-24', NULL, 'active', 'pending', '2025-09-24 01:51:38');

-- --------------------------------------------------------

--
-- Table structure for table `designer_portfolio`
--

CREATE TABLE `designer_portfolio` (
  `portfolio_id` int(11) NOT NULL,
  `designer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `project_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_applications`
--

CREATE TABLE `job_applications` (
  `application_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `designer_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `proposal_text` text DEFAULT NULL,
  `offered_price` decimal(10,2) DEFAULT NULL,
  `application_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','accepted','rejected','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_applications`
--

INSERT INTO `job_applications` (`application_id`, `request_id`, `designer_id`, `client_id`, `proposal_text`, `offered_price`, `application_date`, `status`) VALUES
(1, 1, 2, 3, 'สามารถออกแบบให้ตรงคอนเซ็ปต์และส่งงานได้ตามกำหนดครับ', 3500.00, '2025-06-07 15:44:37', 'pending'),
(2, 2, 2, 5, 'สนใจงาน Landing Page ครับ มีประสบการณ์ด้านนี้โดยตรง สามารถทำให้ติด SEO ได้', 20000.00, '2025-06-07 15:44:37', 'rejected'),
(3, 3, 4, 3, 'ถนัดงานออกแบบแพ็กเกจจิ้งครับ มีตัวอย่างผลงานให้ดู', 10000.00, '2025-06-07 15:44:37', 'rejected'),
(4, 1, 2, 3, '0', 2500.00, '2025-06-09 07:07:29', 'rejected'),
(5, 46, 2, 3, 'เพิ่มเวลาในการทำงานได้ไหมครับ', 30000.00, '2025-09-15 16:24:50', 'accepted'),
(6, 42, 2, 3, '132123', 35000.00, '2025-09-15 17:50:23', 'accepted'),
(7, 41, 2, 3, 'asdasd', 3000.00, '2025-09-15 17:50:48', 'accepted'),
(8, 48, 2, 3, 'thrh', 5000.00, '2025-09-15 17:59:01', 'rejected'),
(9, 49, 2, 3, 'efgeger', 3000.00, '2025-09-15 18:08:24', 'rejected'),
(10, 47, 2, 3, '546545', 30000.00, '2025-09-15 18:23:53', 'accepted'),
(11, 57, 2, 3, '55555555', 5000.00, '2025-09-16 15:04:53', 'accepted'),
(12, 58, 2, 3, 'asdasdasd', 3000.00, '2025-09-17 07:17:51', 'accepted'),
(13, 56, 2, 3, '443543', 50000.00, '2025-09-17 07:18:07', 'accepted'),
(14, 55, 2, 3, 'daadadada', 3000.00, '2025-09-17 15:50:16', 'accepted'),
(15, 60, 2, 3, 'yyyyyyyyyyy', 3500.00, '2025-09-17 15:50:35', 'accepted'),
(16, 62, 2, 3, 'ขยายเวลาใหม่ให้ผมหน่อยครับถ้าโอเค', 3000.00, '2025-09-17 17:08:12', 'accepted'),
(17, 61, 2, 3, 'ttttttttttttttttttt', 30000.00, '2025-09-17 17:08:27', 'accepted'),
(18, 63, 2, 3, '-', 3000.00, '2025-09-17 17:20:08', 'accepted'),
(19, 59, 2, 3, '5555555', 30000.00, '2025-09-17 18:26:50', 'rejected'),
(20, 65, 2, 3, '55555555', 35000.00, '2025-09-17 18:43:25', 'accepted'),
(21, 68, 2, 3, '55555', 5000.00, '2025-09-17 18:51:00', 'accepted'),
(22, 67, 2, 3, '564546123', 30000.00, '2025-09-17 18:51:11', 'accepted'),
(23, 66, 2, 3, '1225', 5000.00, '2025-09-17 18:51:21', 'accepted'),
(24, 73, 2, 3, '525252', 5000.00, '2025-09-17 19:08:38', 'accepted'),
(25, 72, 2, 3, '525252', 525252.00, '2025-09-17 19:08:43', 'accepted'),
(26, 71, 2, 3, '52525252', 525252.00, '2025-09-17 19:08:50', 'accepted'),
(27, 69, 2, 3, '5252512', 525252.00, '2025-09-17 19:08:56', 'accepted'),
(28, 70, 2, 3, '525252', 525252.00, '2025-09-17 19:09:02', 'accepted'),
(29, 76, 2, 3, 'yhyhy', 444.00, '2025-09-17 21:33:06', 'accepted'),
(30, 75, 2, 3, '4444', 4444.00, '2025-09-17 21:33:19', 'accepted'),
(31, 74, 2, 3, '555555', 99999999.99, '2025-09-17 21:33:27', 'accepted'),
(32, 77, 2, 3, 'test', 3000.00, '2025-09-18 03:33:45', 'accepted'),
(33, 78, 2, 3, '', 3500.00, '2025-09-18 03:41:40', 'accepted'),
(34, 79, 2, 3, '....', 3500.00, '2025-09-20 08:36:05', 'accepted'),
(35, 64, 2, 3, 'ggggggg\r\n', 3000.00, '2025-09-20 08:46:32', 'accepted'),
(36, 82, 2, 3, '', 5000.00, '2025-09-20 08:53:20', 'accepted'),
(37, 81, 2, 3, '', 4000.00, '2025-09-20 08:53:29', 'accepted'),
(38, 83, 2, 3, 'sdfdsfds', 850.00, '2025-09-20 09:28:44', 'accepted'),
(39, 80, 2, 3, '1111111', 2000.00, '2025-09-20 09:38:31', 'accepted'),
(40, 84, 2, 3, '', 450.00, '2025-09-20 09:49:39', 'accepted'),
(41, 85, 2, 3, '56465456', 6000.00, '2025-09-20 10:49:29', 'rejected'),
(42, 87, 2, 3, '', 650.00, '2025-09-22 17:53:10', 'accepted'),
(43, 88, 2, 3, 'gegrg', 650.00, '2025-09-22 17:55:33', 'accepted'),
(44, 89, 2, 3, 'test ชำระเงิน', 700.00, '2025-09-22 19:21:10', 'accepted'),
(45, 90, 2, 3, 'test ชำระเงิน2', 900.00, '2025-09-22 19:43:13', 'accepted'),
(46, 91, 2, 3, 'test ชำระเงิน3', 890.00, '2025-09-22 19:53:37', 'accepted'),
(47, 92, 2, 3, 'test ส่งไฟล์ฉบับร่าง1', 3800.00, '2025-09-22 21:37:44', 'accepted'),
(48, 93, 2, 3, 'ส่งฉบับร่าง 2', 6500.00, '2025-09-22 22:42:07', 'accepted'),
(49, 94, 2, 3, 'test รวมtab', 65.00, '2025-09-23 18:17:57', 'accepted'),
(50, 95, 2, 3, 'สวัสดีจ้างงาน', 350.00, '2025-09-23 18:34:14', 'accepted'),
(51, 96, 4, 5, 'สวัสดีจ้างงาน', 350.00, '2025-09-23 18:50:45', 'accepted');

-- --------------------------------------------------------

--
-- Table structure for table `job_categories`
--

CREATE TABLE `job_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_categories`
--

INSERT INTO `job_categories` (`category_id`, `category_name`) VALUES
(1, 'Graphic Design'),
(5, 'Illustration'),
(4, 'Logo Design'),
(6, 'Photography'),
(2, 'UI/UX Design'),
(3, 'Web Development');

-- --------------------------------------------------------

--
-- Table structure for table `job_postings`
--

CREATE TABLE `job_postings` (
  `post_id` int(11) NOT NULL,
  `designer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price_range` varchar(100) DEFAULT NULL,
  `posted_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive','completed') DEFAULT 'active',
  `client_id` int(11) NOT NULL,
  `main_image_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_postings`
--

INSERT INTO `job_postings` (`post_id`, `designer_id`, `title`, `description`, `category_id`, `price_range`, `posted_date`, `status`, `client_id`, `main_image_id`) VALUES
(1, 2, 'รับงาน UI/UX Design', 'ออกแบบเว็บไซต์และแอปพลิเคชันที่ใช้งานง่ายและสวยงาม', 2, '10,000-25,000 บาท', '2025-06-07 15:44:37', 'active', 0, 53),
(2, 2, 'บริการออกแบบโลโก้', 'ออกแบบโลโก้สำหรับธุรกิจขนาดเล็กและสตาร์ทอัพ', 4, '3,000-8,000 บาท', '2025-06-07 15:44:37', 'active', 0, 54),
(3, 4, 'รับวาดภาพประกอบดิจิทัล', 'รับงานภาพประกอบสำหรับหนังสือ, โฆษณา, เกม', 5, '5,000-15,000 บาท', '2025-06-07 15:44:37', 'active', 0, 59),
(4, 7, 'รับออกแบบโปสเตอร์สินค้า', 'ออกแบบโปสเตอร์โฆษณาสินค้าแบบมืออาชีพ สะดุดตา', 1, '2,500–6,000 บาท', '2025-07-10 16:34:16', 'active', 0, 59),
(14, 10, 'ทำอินโฟกราฟิกนำเสนอ', 'ออกแบบภาพอินโฟกราฟิกสำหรับพรีเซนต์หรือโซเชียลมีเดีย', 1, '3,000–8,000 บาท', '2025-07-10 16:40:37', 'active', 0, 58),
(15, 10, 'วาดภาพประกอบนิทาน', 'วาดภาพประกอบแนวเด็กน่ารักสดใส สำหรับหนังสือนิทาน', 2, '5,000–12,000 บาท', '2025-07-10 16:43:43', 'active', 0, 53),
(16, 4, 'วาดตัวละครแนวแฟนตาซี', 'รับวาดคาแรคเตอร์สไตล์เกม/อนิเมะแฟนตาซี', 2, '8,000–20,000 บาท', '2025-07-10 16:43:43', 'active', 0, 56),
(17, 7, 'ออกแบบโลโก้แบรนด์แฟชั่น', 'สร้างโลโก้สำหรับแบรนด์เสื้อผ้าหรือแฟชั่นสมัยใหม่', 3, '4,000–10,000 บาท', '2025-07-10 16:43:43', 'active', 0, 55),
(18, 7, 'โลโก้ธุรกิจท้องถิ่น', 'โลโก้เรียบง่าย เหมาะสำหรับร้านอาหาร คาเฟ่ และ SME', 3, '2,000–5,000 บาท', '2025-07-10 16:43:43', 'active', 0, 54),
(19, 4, 'ถ่ายภาพโปรไฟล์', 'รับถ่ายภาพโปรไฟล์สำหรับใช้ในงานหรือโซเชียลมีเดีย', 4, '1,500–4,000 บาท', '2025-07-10 16:43:43', 'active', 0, 57),
(20, 10, 'ถ่ายสินค้าเพื่อขายออนไลน์', 'ถ่ายภาพสินค้าพร้อมแต่งภาพ เหมาะกับตลาดออนไลน์', 4, '3,000–7,000 บาท', '2025-07-10 16:43:43', 'active', 0, 52),
(21, 2, 'ออกแบบ UI เว็บไซต์', 'ดีไซน์หน้าเว็บให้สวยงาม น่าใช้งาน และตอบโจทย์ UX', 5, '10,000–25,000 บาท', '2025-07-10 16:43:43', 'active', 0, 52),
(22, 4, 'พัฒนาเว็บไซต์ด้วย HTML/CSS', 'รับสร้างเว็บไซต์พื้นฐานด้วย HTML/CSS ตามแบบที่ลูกค้าต้องการ', 6, '8,000–20,000 บาท', '2025-07-10 16:43:43', 'active', 0, 58),
(23, 2, 'ออกแบบป้าย', 'รับออกแบบป้าย ทันสมัย,สีสันสดสวย,คุ้มราคา100%', 1, '500–3000 บาท', '2025-07-23 17:24:13', 'active', 0, 61);

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`log_id`, `user_id`, `action`, `details`, `ip_address`, `timestamp`) VALUES
(1, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-06-23 23:41:02'),
(2, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-23 23:41:10'),
(3, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-06-24 00:13:54'),
(4, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 00:14:02'),
(5, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 00:21:15'),
(6, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-06-24 00:26:42'),
(7, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 00:26:49'),
(8, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 00:28:20'),
(9, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-06-24 01:04:59'),
(10, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-06-24 01:05:15'),
(11, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 01:05:24'),
(12, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-06-24 01:07:40'),
(13, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-06-24 01:33:18'),
(14, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-06-24 01:36:37'),
(15, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 01:36:43'),
(16, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-06-24 13:21:56'),
(17, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 13:22:02'),
(18, 8, 'Login Successful', 'User logged in: chalida', '::1', '2025-06-24 13:24:52'),
(19, 2, 'Login Attempt Failed', 'Inactive account: khoapun', '::1', '2025-06-24 13:29:42'),
(20, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-06-24 13:29:51'),
(21, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 13:29:58'),
(22, 2, 'Login Attempt Failed', 'Inactive account: khoapun', '::1', '2025-06-24 13:33:15'),
(23, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 13:33:36'),
(24, 2, 'Login Attempt Failed', 'Inactive account: khoapun', '::1', '2025-06-24 13:34:04'),
(25, 7, 'Login Successful', 'User logged in: pakawat.in', '::1', '2025-06-24 13:34:19'),
(26, 7, 'Login Attempt Failed', 'Invalid user type: designer for user pakawat.in', '::1', '2025-06-24 13:34:19'),
(27, 2, 'Login Attempt Failed', 'Inactive account: khoapun', '::1', '2025-06-24 13:36:13'),
(28, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-06-24 13:36:34'),
(29, 2, 'Login Attempt Failed', 'Inactive account: khoapun', '::1', '2025-06-24 13:38:45'),
(30, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-06-24 13:38:51'),
(31, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 13:46:19'),
(32, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-06-24 13:46:27'),
(33, 2, 'Login Attempt Failed', 'Inactive account: khoapun', '::1', '2025-06-24 13:47:01'),
(34, 7, 'Login Successful', 'User logged in: pakawat.in', '::1', '2025-06-24 13:49:33'),
(35, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-06-24 13:49:42'),
(36, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 13:49:52'),
(37, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-06-24 13:50:18'),
(38, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-06-24 13:51:04'),
(39, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 13:51:14'),
(40, 8, 'Login Successful', 'User logged in: chalida', '::1', '2025-06-24 13:52:12'),
(41, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 13:52:22'),
(42, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 13:56:22'),
(43, 8, 'Login Attempt Failed', 'Account not approved: chalida', '::1', '2025-06-24 13:56:36'),
(44, 7, 'Login Attempt Failed', 'Incorrect password for: pakawat.in', '::1', '2025-06-24 13:57:34'),
(45, 7, 'Login Attempt Failed', 'Account not approved: pakawat.in', '::1', '2025-06-24 13:57:41'),
(46, 2, 'Login Attempt Failed', 'Inactive account: khoapun', '::1', '2025-06-24 13:57:49'),
(47, 2, 'Login Attempt Failed', 'Inactive account: khoapun', '::1', '2025-06-24 13:58:07'),
(48, 3, 'Login Attempt Failed', 'Account not approved: beer888', '::1', '2025-06-24 13:58:22'),
(49, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-06-24 13:58:28'),
(50, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 13:58:32'),
(51, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-06-24 13:58:57'),
(52, 2, 'Login Attempt Failed', 'Inactive account: khoapun', '::1', '2025-06-24 13:59:16'),
(53, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-06-24 13:59:33'),
(54, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 13:59:42'),
(55, 2, 'Login Attempt Failed', 'Account not approved: khoapun', '::1', '2025-06-24 14:00:05'),
(56, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 14:00:09'),
(57, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-06-24 14:00:23'),
(58, 7, 'Login Attempt Failed', 'Account not approved: pakawat.in', '::1', '2025-06-24 14:00:41'),
(59, 8, 'Login Attempt Failed', 'Account not approved: chalida', '::1', '2025-06-24 14:00:45'),
(60, 9, 'Login Attempt Failed', 'Account not approved: party', '::1', '2025-06-24 14:53:24'),
(61, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-06-24 14:53:32'),
(62, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 14:53:37'),
(63, 8, 'Login Attempt Failed', 'Incorrect password for: chalida', '::1', '2025-06-24 15:50:39'),
(64, 8, 'Login Attempt Failed', 'Incorrect password for: chalida', '::1', '2025-06-24 15:50:54'),
(65, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 15:51:08'),
(66, 8, 'Login Attempt Failed', 'Incorrect password for: chalida', '::1', '2025-06-24 15:52:06'),
(67, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 16:09:30'),
(68, 8, 'Login Attempt Failed', 'Incorrect password for: chalida', '::1', '2025-06-24 16:17:37'),
(69, 8, 'Login Attempt Failed', 'Incorrect password for: chalida', '::1', '2025-06-24 16:17:52'),
(70, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 16:21:24'),
(71, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 16:39:19'),
(72, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-06-24 17:18:39'),
(73, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 17:18:43'),
(74, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-06-24 17:19:53'),
(75, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-06-24 17:20:05'),
(76, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-06-24 17:25:53'),
(77, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-24 17:31:26'),
(78, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-06-24 17:31:36'),
(79, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-06-24 17:34:08'),
(80, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-06-24 17:42:04'),
(81, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-30 21:27:26'),
(82, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-30 21:29:33'),
(83, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-06-30 22:40:26'),
(84, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-03 15:43:27'),
(85, 7, 'Login Attempt Failed', 'Incorrect password for: pakawat.in', '::1', '2025-07-03 15:51:11'),
(86, 7, 'Login Attempt Failed', 'Account not approved: pakawat.in', '::1', '2025-07-03 15:51:24'),
(87, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-03 15:51:34'),
(88, 7, 'Login Successful', 'User logged in: pakawat.in', '::1', '2025-07-03 15:52:42'),
(89, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-03 15:53:32'),
(90, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-03 15:55:30'),
(91, 7, 'Login Successful', 'User logged in: pakawat.in', '::1', '2025-07-03 15:55:46'),
(92, 7, 'Login Successful', 'User logged in: pakawat.in', '::1', '2025-07-03 15:56:48'),
(93, 7, 'Login Successful', 'User logged in: pakawat.in', '::1', '2025-07-03 15:57:26'),
(94, 7, 'Login Successful', 'User logged in: pakawat.in', '::1', '2025-07-03 16:04:11'),
(95, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-03 16:05:04'),
(96, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-03 16:05:44'),
(97, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-07 16:25:23'),
(98, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-07 17:44:12'),
(99, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-07 17:47:09'),
(100, 7, 'Login Successful', 'User logged in: pakawat.in', '::1', '2025-07-07 17:47:19'),
(101, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-07 17:47:28'),
(102, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-07 17:49:05'),
(103, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-07 17:49:51'),
(104, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-07 17:50:23'),
(105, 12, 'Login Attempt Failed', 'Account not approved: TESTTTTT', '::1', '2025-07-07 21:55:43'),
(106, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-07 21:55:49'),
(107, 12, 'Login Successful', 'User logged in: TESTTTTT', '::1', '2025-07-07 21:56:08'),
(108, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-10 21:09:13'),
(109, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-10 21:12:19'),
(110, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 21:25:36'),
(111, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 21:26:31'),
(112, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 21:29:30'),
(113, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-10 21:31:35'),
(114, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-10 21:54:16'),
(115, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-10 21:55:25'),
(116, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 21:56:33'),
(117, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 21:57:08'),
(118, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 22:02:33'),
(119, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 22:02:56'),
(120, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 22:04:01'),
(121, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 22:14:58'),
(122, 7, 'Login Successful', 'User logged in: pakawat.in', '::1', '2025-07-10 22:16:08'),
(123, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 22:17:44'),
(124, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 22:20:19'),
(125, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 22:20:56'),
(126, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 22:23:07'),
(127, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 22:38:20'),
(128, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 22:44:35'),
(129, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 22:45:31'),
(130, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 23:06:27'),
(131, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 23:09:25'),
(132, 2, 'Login Attempt Failed', 'Incorrect password for: khoapun', '::1', '2025-07-10 23:10:40'),
(133, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 23:12:43'),
(134, 2, 'Login Attempt Failed', 'Incorrect password for: khoapun', '::1', '2025-07-10 23:16:05'),
(135, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 23:16:10'),
(136, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 23:21:58'),
(137, 2, 'Login Attempt Failed', 'Incorrect password for: khoapun', '::1', '2025-07-10 23:24:56'),
(138, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 23:25:02'),
(139, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 23:51:40'),
(140, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 23:56:44'),
(141, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 23:58:46'),
(142, 2, 'Login Attempt Failed', 'Incorrect password for: khoapun', '::1', '2025-07-10 23:59:08'),
(143, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-10 23:59:15'),
(144, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-11 00:03:57'),
(145, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-11 00:07:26'),
(146, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-11 00:11:49'),
(147, 2, 'Login Attempt Failed', 'Incorrect password for: khoapun', '::1', '2025-07-11 00:14:06'),
(148, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-11 00:14:12'),
(149, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-11 00:17:38'),
(150, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-11 00:23:46'),
(151, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-11 00:24:55'),
(152, NULL, 'Login Attempt Failed', 'Username not found: ิbeer888', '::1', '2025-07-11 00:31:56'),
(153, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-11 00:32:01'),
(154, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-11 00:33:33'),
(155, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-11 00:35:41'),
(156, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-11 00:40:28'),
(157, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-11 00:43:42'),
(158, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-11 00:44:27'),
(159, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-11 00:45:10'),
(160, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-11 00:45:51'),
(161, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-11 00:46:05'),
(162, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-11 00:46:22'),
(163, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-11 00:47:29'),
(164, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-11 00:48:51'),
(165, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-11 00:49:03'),
(166, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-11 00:50:51'),
(167, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-11 00:54:22'),
(168, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-11 00:58:14'),
(169, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-11 00:59:05'),
(170, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-11 01:00:21'),
(171, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-11 01:02:51'),
(172, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-11 01:05:20'),
(173, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-11 14:59:35'),
(174, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-11 14:59:51'),
(175, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-11 15:01:03'),
(176, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-12 21:07:26'),
(177, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-12 21:08:08'),
(178, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-12 21:09:57'),
(179, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-12 21:12:23'),
(180, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-12 21:13:50'),
(181, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-12 21:16:22'),
(182, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-12 21:17:26'),
(183, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-12 21:45:17'),
(184, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-12 21:46:09'),
(185, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-12 21:52:44'),
(186, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-12 22:01:20'),
(187, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-12 22:49:31'),
(188, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-12 23:42:55'),
(189, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-13 01:15:06'),
(190, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-13 02:18:40'),
(191, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-13 02:21:01'),
(192, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-13 02:21:31'),
(193, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-14 20:57:52'),
(194, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-07-14 20:58:50'),
(195, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-14 20:58:54'),
(196, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-14 21:24:47'),
(197, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-14 21:28:49'),
(198, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-14 21:32:24'),
(199, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-14 21:58:34'),
(200, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-14 21:59:48'),
(201, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-14 22:05:16'),
(202, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-14 22:10:23'),
(203, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-07-14 22:12:31'),
(204, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-14 22:12:36'),
(205, NULL, 'Login Attempt Failed', 'Username not found: ad', '::1', '2025-07-14 22:28:43'),
(206, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-07-14 22:28:54'),
(207, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-14 22:29:01'),
(208, 2, 'Login Attempt Failed', 'Incorrect password for: khoapun', '::1', '2025-07-14 22:31:13'),
(209, NULL, 'Login Attempt Failed', 'Username not found: khoa', '::1', '2025-07-14 22:31:18'),
(210, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-14 22:31:23'),
(211, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-07-14 22:41:19'),
(212, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-14 22:41:25'),
(213, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-14 22:42:54'),
(214, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-14 23:27:42'),
(215, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-14 23:27:59'),
(216, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-14 23:31:43'),
(217, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-14 23:38:44'),
(218, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-14 23:48:37'),
(219, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-14 23:48:59'),
(220, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-14 23:52:41'),
(221, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-14 23:53:08'),
(222, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-15 00:00:49'),
(223, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-15 00:14:17'),
(224, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-07-15 00:14:30'),
(225, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-07-15 00:22:44'),
(226, NULL, 'Login Attempt Failed', 'Username not found: asd', '::1', '2025-07-15 00:25:48'),
(227, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-15 00:26:06'),
(228, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-15 00:26:24'),
(229, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-15 00:33:42'),
(230, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-16 23:52:18'),
(231, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-23 23:02:23'),
(232, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-23 23:52:03'),
(233, 10, 'Login Attempt Failed', 'Account not approved: party888', '::1', '2025-07-24 00:38:34'),
(234, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-24 00:38:42'),
(235, 10, 'Login Successful', 'User logged in: party888', '::1', '2025-07-24 00:38:59'),
(236, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-24 00:42:01'),
(237, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-24 00:43:24'),
(238, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-24 01:19:10'),
(239, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-24 01:37:09'),
(240, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-25 23:02:47'),
(241, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-25 23:05:17'),
(242, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-25 23:09:45'),
(243, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-25 23:11:03'),
(244, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-25 23:13:02'),
(245, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-25 23:15:45'),
(246, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-25 23:21:05'),
(247, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-25 23:25:44'),
(248, 2, 'Login Attempt Failed', 'Incorrect password for: khoapun', '::1', '2025-07-26 00:02:09'),
(249, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-26 00:02:14'),
(250, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-26 00:04:40'),
(251, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-26 00:14:14'),
(252, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-26 00:19:03'),
(253, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-26 01:36:06'),
(254, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-26 01:36:45'),
(255, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-26 01:37:26'),
(256, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-26 22:38:44'),
(257, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-27 00:00:42'),
(258, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-27 00:59:09'),
(259, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-27 01:01:56'),
(260, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-27 01:04:26'),
(261, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-27 01:05:48'),
(262, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-27 01:06:22'),
(263, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-27 01:09:05'),
(264, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-30 14:04:56'),
(265, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-30 14:30:44'),
(266, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-30 15:34:11'),
(267, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-30 15:35:42'),
(268, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-30 15:39:30'),
(269, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-30 16:13:00'),
(270, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-30 16:17:28'),
(271, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-30 16:21:25'),
(272, 10, 'Login Successful', 'User logged in: party888', '::1', '2025-07-30 16:21:47'),
(273, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-07-30 16:22:07'),
(274, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-30 16:22:44'),
(275, 4, 'Login Successful', 'User logged in: anna', '::1', '2025-07-30 16:27:32'),
(276, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-30 16:29:27'),
(277, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-30 19:55:42'),
(278, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-30 21:08:49'),
(279, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-30 21:10:03'),
(280, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-30 21:30:03'),
(281, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-30 22:26:59'),
(282, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-07-30 22:43:23'),
(283, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-02 21:03:18'),
(284, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-02 21:33:11'),
(285, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-02 21:33:29'),
(286, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-02 21:50:31'),
(287, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-02 22:00:03'),
(288, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-02 22:02:02'),
(289, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-02 22:10:51'),
(290, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-02 22:13:02'),
(291, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-02 22:14:26'),
(292, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-02 22:31:11'),
(293, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-02 23:29:20'),
(294, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-02 23:45:46'),
(295, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-02 23:50:58'),
(296, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-03 00:02:31'),
(297, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-03 00:28:45'),
(298, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-04 15:42:09'),
(299, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-08-04 22:06:26'),
(300, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-08-05 08:43:11'),
(301, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-08-05 08:43:17'),
(302, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-05 08:43:40'),
(303, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-08-05 09:02:30'),
(304, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-05 09:07:00'),
(305, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-08-05 09:18:07'),
(306, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-05 09:19:13'),
(307, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-08-05 09:22:26'),
(308, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-05 09:23:40'),
(309, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-08-05 09:38:55'),
(310, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-05 09:44:13'),
(311, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-08-05 12:10:04'),
(312, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-05 12:10:25'),
(313, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-08-05 12:13:21'),
(314, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-08-05 12:13:30'),
(315, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-05 12:25:39'),
(316, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-08-05 12:42:40'),
(317, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-05 12:58:35'),
(318, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-08-05 12:59:57'),
(319, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-05 13:06:03'),
(320, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-05 13:06:53'),
(321, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-08-07 12:07:03'),
(322, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-07 12:08:50'),
(323, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-08-07 12:17:38'),
(324, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-07 12:19:27'),
(325, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-08-07 13:39:11'),
(326, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-07 13:40:13'),
(327, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-26 18:02:04'),
(328, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-08-26 18:09:18'),
(329, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-26 18:21:07'),
(330, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-26 18:43:19'),
(331, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-26 18:51:12'),
(332, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-27 12:21:58'),
(333, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-27 12:30:50'),
(334, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-27 13:25:18'),
(335, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-27 13:27:56'),
(336, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-08-27 14:10:43'),
(337, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-08-27 14:16:11'),
(338, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-08-27 14:32:30'),
(339, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-28 14:53:30'),
(340, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-28 14:59:48'),
(341, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-28 15:06:58'),
(342, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-08-28 15:11:52'),
(343, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-08-28 15:15:52'),
(344, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-08-28 15:20:32'),
(345, 3, 'Login Attempt Failed', 'Incorrect password for: beer888', '::1', '2025-09-01 19:06:02'),
(346, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-01 19:06:09'),
(347, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-01 20:46:18'),
(348, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-01 20:51:45'),
(349, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-01 20:52:22'),
(350, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-01 21:13:42'),
(351, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-01 21:18:03'),
(352, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-01 21:21:29'),
(353, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-01 21:27:23'),
(354, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-01 21:51:41'),
(355, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-01 21:52:01'),
(356, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-01 21:52:58'),
(357, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-01 21:53:28'),
(358, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-01 21:54:09'),
(359, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-01 22:02:07'),
(360, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-01 22:02:18'),
(361, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-01 22:02:40'),
(362, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-01 22:05:43'),
(363, NULL, 'Login Attempt Failed', 'Username not found: ิbeer888', '::1', '2025-09-01 22:11:59'),
(364, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-01 22:12:07'),
(365, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-01 22:22:56'),
(366, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-01 22:32:42'),
(367, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-04 02:18:35'),
(368, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-04 02:20:41'),
(369, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-04 02:23:30'),
(370, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-04 02:24:12'),
(371, 3, 'Login Attempt Failed', 'Incorrect password for: beer888', '::1', '2025-09-08 15:34:14'),
(372, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-08 15:34:18'),
(373, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-08 15:38:25'),
(374, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-08 15:38:49'),
(375, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-08 15:40:16'),
(376, 3, 'Login Attempt Failed', 'Incorrect password for: beer888', '::1', '2025-09-08 15:40:38'),
(377, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-08 15:40:50'),
(378, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-08 15:43:16'),
(379, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-08 16:08:58'),
(380, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-08 16:09:24'),
(381, 2, 'Login Attempt Failed', 'Incorrect password for: khoapun', '::1', '2025-09-08 16:17:13'),
(382, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-08 16:17:17'),
(383, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-08 16:22:26'),
(384, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-08 16:48:54'),
(385, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-08 16:59:29'),
(386, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-08 17:02:51'),
(387, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-08 17:03:36'),
(388, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-08 17:13:59'),
(389, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-08 17:30:00'),
(390, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-08 17:30:14'),
(391, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-08 17:30:39'),
(392, 4, 'Login Successful', 'User logged in: anna', '::1', '2025-09-08 17:36:39'),
(393, 4, 'Login Successful', 'User logged in: anna', '::1', '2025-09-08 17:37:46'),
(394, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-08 17:38:15'),
(395, 4, 'Login Successful', 'User logged in: anna', '::1', '2025-09-08 17:38:41'),
(396, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-08 17:41:08'),
(397, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-08 18:09:55'),
(398, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-08 18:11:23'),
(399, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-08 18:11:39'),
(400, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-08 18:14:38'),
(401, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-08 18:16:35'),
(402, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-08 18:37:10'),
(403, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-08 18:39:24'),
(404, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-09-08 19:39:11'),
(405, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-09-08 19:39:16'),
(406, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-08 19:53:56'),
(407, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-08 19:54:32'),
(408, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-08 23:00:11'),
(409, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-08 23:20:39'),
(410, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-08 23:39:24'),
(411, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-09 02:23:39'),
(412, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-09 09:44:38'),
(413, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-09 09:46:39'),
(414, 4, 'Login Successful', 'User logged in: anna', '::1', '2025-09-09 09:47:00'),
(415, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-09-09 09:51:24'),
(416, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-09-09 09:51:34'),
(417, 4, 'Login Successful', 'User logged in: anna', '::1', '2025-09-09 09:52:56'),
(418, 2, 'Login Attempt Failed', 'Incorrect password for: khoapun', '::1', '2025-09-09 09:55:26'),
(419, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-09 09:55:32'),
(420, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-09 10:03:16'),
(421, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-09 10:09:29'),
(422, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-09-09 10:13:22'),
(423, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-09-09 10:14:27'),
(424, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-09-09 10:14:45'),
(425, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-09 10:15:09'),
(426, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-09 10:16:46'),
(427, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-09 10:28:51'),
(428, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-09 10:31:43'),
(429, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-09-10 13:48:21'),
(430, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-09-10 13:48:26'),
(431, 2, 'Login Attempt Failed', 'Incorrect password for: khoapun', '::1', '2025-09-10 13:55:12'),
(432, 2, 'Login Attempt Failed', 'Incorrect password for: khoapun', '::1', '2025-09-10 13:55:16'),
(433, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-10 13:55:21'),
(434, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-10 14:10:47'),
(435, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-10 14:11:28'),
(436, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-10 14:21:57'),
(437, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-10 14:22:35'),
(438, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-10 14:32:30'),
(439, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-10 14:33:35'),
(440, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-10 14:34:03'),
(441, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-10 14:35:36'),
(442, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-10 14:36:12'),
(443, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-10 14:40:48'),
(444, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-10 14:54:22'),
(445, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-10 14:55:20'),
(446, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-10 15:25:46'),
(447, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-10 15:26:02'),
(448, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-10 15:33:41'),
(449, 4, 'Login Successful', 'User logged in: anna', '::1', '2025-09-10 15:45:06'),
(450, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-10 15:45:30'),
(451, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-10 16:07:22'),
(452, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-10 16:07:57'),
(453, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-10 16:08:56'),
(454, 3, 'Login Attempt Failed', 'Incorrect password for: beer888', '::1', '2025-09-13 14:23:40'),
(455, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-13 14:23:46'),
(456, 3, 'Login Attempt Failed', 'Incorrect password for: beer888', '::1', '2025-09-13 14:24:26'),
(457, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-13 14:24:30'),
(458, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-13 14:34:38'),
(459, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-13 14:38:18'),
(460, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-13 14:47:47'),
(461, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-13 14:49:21'),
(462, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-13 14:51:58'),
(463, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-13 15:09:49'),
(464, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-13 15:10:07'),
(465, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-13 15:27:20'),
(466, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-13 15:27:41'),
(467, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-13 15:35:21'),
(468, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-13 15:35:46'),
(469, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-13 15:46:59'),
(470, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-13 16:10:53'),
(471, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-13 16:34:04'),
(472, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-13 16:42:27'),
(473, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-13 16:45:40'),
(474, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-13 17:00:46'),
(475, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-13 17:16:10'),
(476, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-13 17:19:45'),
(477, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-13 17:57:51'),
(478, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-13 17:58:14'),
(479, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-14 09:25:36'),
(480, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-14 09:39:36'),
(481, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-14 09:40:12'),
(482, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-14 10:43:32'),
(483, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-14 10:47:14'),
(484, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-14 10:52:23'),
(485, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-14 11:22:16'),
(486, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-14 11:31:01'),
(487, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-14 12:02:24'),
(488, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-14 14:55:25'),
(489, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-14 15:51:38'),
(490, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-14 15:52:12'),
(491, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-14 17:13:28'),
(492, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-14 17:14:20'),
(493, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-14 17:18:40'),
(494, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-14 17:19:37'),
(495, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-14 18:23:53'),
(496, 2, 'Login Attempt Failed', 'Incorrect password for: khoapun', '::1', '2025-09-15 16:41:37'),
(497, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-15 16:41:41'),
(498, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-15 17:08:04'),
(499, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-15 18:12:36'),
(500, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-15 18:16:29'),
(501, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-15 18:17:18'),
(502, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-15 18:19:29'),
(503, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-15 18:23:46'),
(504, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-15 20:20:53'),
(505, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-15 20:21:29'),
(506, 4, 'Login Successful', 'User logged in: anna', '::1', '2025-09-15 20:24:19'),
(507, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-15 20:24:46'),
(508, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-15 20:38:08'),
(509, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-15 20:55:25'),
(510, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-15 20:56:08'),
(511, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-15 21:57:24'),
(512, 2, 'Login Attempt Failed', 'Incorrect password for: khoapun', '::1', '2025-09-15 22:06:32'),
(513, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-15 22:06:37'),
(514, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-15 23:35:59'),
(515, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-15 23:36:32'),
(516, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-15 23:40:21'),
(517, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-15 23:54:12'),
(518, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-15 23:54:37'),
(519, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-16 00:09:23'),
(520, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-16 00:13:33'),
(521, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-16 00:24:06'),
(522, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-16 00:35:56'),
(523, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-16 00:36:12'),
(524, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-16 00:42:11'),
(525, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-16 00:43:05'),
(526, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-16 00:57:31'),
(527, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-16 00:58:24'),
(528, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-16 00:59:28'),
(529, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-16 01:08:04'),
(530, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-16 01:10:50'),
(531, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-16 01:11:16'),
(532, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-16 02:43:08'),
(533, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-16 02:43:51'),
(534, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-16 03:03:07'),
(535, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-16 03:26:02'),
(536, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-16 03:27:06'),
(537, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-16 05:36:29'),
(538, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-16 05:37:17'),
(539, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-16 08:54:11'),
(540, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-16 09:03:55'),
(541, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-16 09:07:45'),
(542, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-16 09:07:56'),
(543, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-16 09:19:03'),
(544, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-16 09:20:00'),
(545, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-16 09:23:53'),
(546, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-16 09:32:03'),
(547, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-17 02:23:00'),
(548, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-17 02:23:35'),
(549, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-17 02:25:31'),
(550, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-17 02:39:16'),
(551, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-17 03:30:08'),
(552, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-17 03:56:35'),
(553, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-17 03:57:59'),
(554, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-17 13:11:31'),
(555, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-17 13:18:44'),
(556, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-17 13:20:05'),
(557, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-17 13:22:23'),
(558, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-17 13:24:31'),
(559, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-17 14:35:06'),
(560, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-17 14:49:46'),
(561, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-17 15:04:06'),
(562, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-17 15:05:08'),
(563, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-17 15:36:21'),
(564, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-17 15:37:45'),
(565, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-17 17:01:55'),
(566, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-17 17:24:43'),
(567, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-17 17:27:23'),
(568, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-17 21:32:24'),
(569, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-17 22:49:53'),
(570, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-17 22:50:44'),
(571, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 00:07:03'),
(572, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-18 00:07:49'),
(573, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 00:08:35'),
(574, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-18 00:19:24'),
(575, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 00:20:16'),
(576, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-18 01:17:46'),
(577, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 01:21:04'),
(578, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-18 01:26:34'),
(579, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 01:27:03'),
(580, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-18 01:43:05');
INSERT INTO `logs` (`log_id`, `user_id`, `action`, `details`, `ip_address`, `timestamp`) VALUES
(581, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 01:44:03'),
(582, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-18 01:46:17'),
(583, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 01:47:52'),
(584, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-18 01:50:42'),
(585, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 01:51:30'),
(586, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 02:05:27'),
(587, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 02:07:08'),
(588, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-18 02:08:25'),
(589, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 02:09:07'),
(590, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-18 03:09:52'),
(591, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 03:32:08'),
(592, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-18 03:36:53'),
(593, 4, 'Login Successful', 'User logged in: anna', '::1', '2025-09-18 03:38:03'),
(594, 7, 'Login Successful', 'User logged in: kitsada.in', '::1', '2025-09-18 03:39:55'),
(595, 10, 'Login Successful', 'User logged in: party888', '::1', '2025-09-18 03:40:28'),
(596, 5, 'Login Attempt Failed', 'Account not approved: tech_corp', '::1', '2025-09-18 03:42:26'),
(597, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-09-18 03:42:35'),
(598, 5, 'Login Successful', 'User logged in: tech_corp', '::1', '2025-09-18 03:42:55'),
(599, 6, 'Login Successful', 'User logged in: krit.ti', '::1', '2025-09-18 03:43:20'),
(600, 12, 'Login Attempt Failed', 'Incorrect password for: TESTTTTT', '::1', '2025-09-18 03:43:41'),
(601, 12, 'Login Successful', 'User logged in: TESTTTTT', '::1', '2025-09-18 03:43:54'),
(602, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-18 03:46:04'),
(603, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 03:48:42'),
(604, 2, 'Login Attempt Failed', 'Incorrect password for: khoapun', '::1', '2025-09-18 04:32:35'),
(605, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-18 04:32:45'),
(606, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-09-18 04:33:35'),
(607, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 04:33:40'),
(608, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 10:32:53'),
(609, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-18 10:33:31'),
(610, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 10:33:55'),
(611, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-18 10:35:26'),
(612, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 10:35:52'),
(613, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-18 10:40:55'),
(614, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 10:41:48'),
(615, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-18 17:16:15'),
(616, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-20 15:12:00'),
(617, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-20 15:12:09'),
(618, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-20 15:13:13'),
(619, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-20 15:35:45'),
(620, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-20 15:36:14'),
(621, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-20 15:46:08'),
(622, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-20 15:46:46'),
(623, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-20 15:52:55'),
(624, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-20 15:53:39'),
(625, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-20 16:27:31'),
(626, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-20 16:28:19'),
(627, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-20 16:28:55'),
(628, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-20 16:38:15'),
(629, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-20 16:38:37'),
(630, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-20 16:45:14'),
(631, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-20 16:48:36'),
(632, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-20 16:49:21'),
(633, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-20 16:49:53'),
(634, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-20 17:48:13'),
(635, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-20 17:48:59'),
(636, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-20 17:49:46'),
(637, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-20 23:50:02'),
(638, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-21 00:33:10'),
(639, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-21 01:50:32'),
(640, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-21 03:20:57'),
(641, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-21 03:23:13'),
(642, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-21 03:46:37'),
(643, 4, 'Login Successful', 'User logged in: anna', '::1', '2025-09-21 04:03:28'),
(644, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-21 04:04:37'),
(645, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-21 04:10:36'),
(646, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-21 04:11:11'),
(647, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-21 04:19:55'),
(648, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-09-21 04:22:07'),
(649, 3, 'Login Attempt Failed', 'Incorrect password for: beer888', '::1', '2025-09-21 04:24:19'),
(650, 3, 'Login Attempt Failed', 'Incorrect password for: beer888', '::1', '2025-09-21 04:24:28'),
(651, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-21 04:24:32'),
(652, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-21 17:56:19'),
(653, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-22 01:16:43'),
(654, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-22 01:16:55'),
(655, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-22 02:06:43'),
(656, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-22 02:07:03'),
(657, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-22 02:26:29'),
(658, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-22 02:27:18'),
(659, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-22 02:27:43'),
(660, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-22 02:28:18'),
(661, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-22 04:31:57'),
(662, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-22 04:33:20'),
(663, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-22 04:59:09'),
(664, 10, 'Login Successful', 'User logged in: party888', '::1', '2025-09-22 05:02:32'),
(665, 5, 'Login Successful', 'User logged in: tech_corp', '::1', '2025-09-22 05:03:06'),
(666, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-22 05:04:13'),
(667, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-09-22 05:28:17'),
(668, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-09-22 05:28:25'),
(669, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-09-22 05:43:43'),
(670, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-22 06:04:18'),
(671, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-09-22 06:04:42'),
(672, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-22 06:04:56'),
(673, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-09-22 06:08:08'),
(674, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-22 17:38:10'),
(675, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-22 17:40:28'),
(676, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-09-22 17:45:09'),
(677, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-09-22 17:45:14'),
(678, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-22 18:14:15'),
(679, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-22 18:14:49'),
(680, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-09-22 18:15:06'),
(681, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-09-22 18:15:10'),
(682, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-09-22 18:15:18'),
(683, 1, 'Login Attempt Failed', 'Incorrect password for: admin', '::1', '2025-09-22 18:15:23'),
(684, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-09-22 18:15:28'),
(685, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-22 18:53:24'),
(686, 1, 'Login Successful', 'User logged in: admin', '::1', '2025-09-22 18:53:38'),
(687, 3, 'Login Attempt Failed', 'Incorrect password for: beer888', '::1', '2025-09-22 18:54:01'),
(688, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-22 18:54:06'),
(689, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-22 18:54:17'),
(690, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-22 19:21:32'),
(691, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-22 19:22:12'),
(692, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-22 19:27:19'),
(693, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-22 19:28:49'),
(694, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-22 19:44:38'),
(695, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 00:39:13'),
(696, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 00:51:02'),
(697, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-23 00:52:43'),
(698, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 00:53:19'),
(699, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 00:54:47'),
(700, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-23 00:55:20'),
(701, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 00:55:45'),
(702, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-23 00:57:46'),
(703, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 00:58:04'),
(704, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-23 01:06:10'),
(705, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 01:07:38'),
(706, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-23 02:20:46'),
(707, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 02:21:18'),
(708, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 02:42:37'),
(709, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-23 02:42:57'),
(710, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 02:43:22'),
(711, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-23 02:53:22'),
(712, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 02:53:56'),
(713, 2, 'Login Attempt Failed', 'Incorrect password for: khoapun', '::1', '2025-09-23 03:50:18'),
(714, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-23 03:50:22'),
(715, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 04:12:57'),
(716, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-23 04:14:10'),
(717, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 04:36:52'),
(718, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-23 04:37:28'),
(719, 3, 'Login Attempt Failed', 'Incorrect password for: beer888', '::1', '2025-09-23 04:37:55'),
(720, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 04:37:59'),
(721, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-23 04:38:29'),
(722, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 05:41:08'),
(723, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-23 05:41:54'),
(724, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 05:42:22'),
(725, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-23 05:42:58'),
(726, 3, 'Login Attempt Failed', 'Incorrect password for: beer888', '::1', '2025-09-23 17:22:45'),
(727, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 17:22:54'),
(728, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-23 17:23:51'),
(729, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 17:40:33'),
(730, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-23 18:02:39'),
(731, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-23 18:06:22'),
(732, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-24 00:17:10'),
(733, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-24 00:18:04'),
(734, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-24 00:18:31'),
(735, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-24 00:19:00'),
(736, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-24 00:19:21'),
(737, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-24 00:19:38'),
(738, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-24 01:17:44'),
(739, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-24 01:18:11'),
(740, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-24 01:18:53'),
(741, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-24 01:20:52'),
(742, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-24 01:22:35'),
(743, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-24 01:32:15'),
(744, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-24 01:33:37'),
(745, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-24 01:34:24'),
(746, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-24 01:35:48'),
(747, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-24 01:36:57'),
(748, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-24 01:41:59'),
(749, 5, 'Login Successful', 'User logged in: tech_corp', '::1', '2025-09-24 01:48:45'),
(750, 4, 'Login Successful', 'User logged in: anna', '::1', '2025-09-24 01:50:05'),
(751, 5, 'Login Attempt Failed', 'Incorrect password for: tech_corp', '::1', '2025-09-24 01:51:13'),
(752, 5, 'Login Successful', 'User logged in: tech_corp', '::1', '2025-09-24 01:51:21'),
(753, 2, 'Login Successful', 'User logged in: khoapun', '::1', '2025-09-24 01:53:16'),
(754, 4, 'Login Successful', 'User logged in: anna', '::1', '2025-09-24 01:53:48'),
(755, 5, 'Login Successful', 'User logged in: tech_corp', '::1', '2025-09-24 01:54:32'),
(756, 4, 'Login Successful', 'User logged in: anna', '::1', '2025-09-24 02:30:23'),
(757, 3, 'Login Successful', 'User logged in: beer888', '::1', '2025-09-24 02:44:37'),
(758, 5, 'Login Successful', 'User logged in: tech_corp', '::1', '2025-09-24 02:44:57'),
(759, 4, 'Login Successful', 'User logged in: anna', '::1', '2025-09-24 02:45:29'),
(760, 5, 'Login Successful', 'User logged in: tech_corp', '::1', '2025-09-24 02:45:59'),
(761, 4, 'Login Successful', 'User logged in: anna', '::1', '2025-09-24 02:52:31'),
(762, 4, 'Login Successful', 'User logged in: anna', '::1', '2025-09-24 04:26:48');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=unread, 1=read'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `from_user_id`, `to_user_id`, `message`, `timestamp`, `is_read`) VALUES
(1, 3, 2, '55555', '2025-08-28 09:10:58', 1),
(2, 3, 2, '55555', '2025-08-28 09:11:00', 1),
(3, 3, 2, '55555', '2025-08-28 09:11:01', 1),
(4, 3, 2, '55555', '2025-08-28 09:11:02', 1),
(5, 3, 2, '55555', '2025-08-28 09:11:02', 1),
(6, 3, 2, '55555', '2025-08-28 09:11:02', 1),
(7, 3, 2, '55555', '2025-08-28 09:11:02', 1),
(8, 3, 2, '55555', '2025-08-28 09:11:10', 1),
(9, 3, 2, '55555', '2025-08-28 09:11:10', 1),
(10, 3, 2, '55555', '2025-08-28 09:11:10', 1),
(11, 3, 2, '5456', '2025-08-28 09:19:50', 1),
(12, 3, 2, '5456', '2025-08-28 09:19:51', 1),
(13, 3, 2, '5456', '2025-08-28 09:19:51', 1),
(14, 3, 2, '5456', '2025-08-28 09:19:51', 1),
(15, 3, 2, 'คคคคคคคคคคคคค', '2025-08-28 09:26:17', 1),
(16, 3, 2, 'นร้รน้', '2025-08-28 09:34:14', 1),
(17, 3, 2, 'test', '2025-08-28 09:34:41', 1),
(18, 3, 2, 'uuuuu', '2025-08-28 09:38:42', 1),
(19, 3, 2, 'nnnn', '2025-08-28 09:43:13', 1),
(20, 3, 2, 'nnnn', '2025-08-28 09:43:25', 1),
(21, 3, 2, ';;;;', '2025-08-28 09:49:20', 1),
(22, 3, 2, 'wwww', '2025-08-28 09:50:20', 1),
(23, 3, 2, 'ำดำดำ', '2025-08-28 10:13:21', 1),
(24, 3, 2, 'ำดำดำดำด', '2025-08-28 10:13:33', 1),
(25, 3, 2, 'สวัสดีครับ', '2025-09-01 12:27:18', 1),
(26, 3, 2, 'หกดห', '2025-09-01 12:30:22', 1),
(27, 3, 2, 'ะะพะพ', '2025-09-01 12:32:36', 1),
(28, 3, 2, 'ะำหะ', '2025-09-01 12:38:47', 1),
(29, 3, 2, 'ะำหะ', '2025-09-01 12:38:51', 1),
(30, 3, 2, 'test', '2025-09-01 13:22:54', 1),
(31, 3, 2, 'test', '2025-09-01 13:44:17', 1),
(32, 3, 2, '5656', '2025-09-01 13:45:48', 1),
(33, 2, 3, 'ว่าไงจ้ะรูปหล่อ', '2025-09-01 14:13:05', 1),
(34, 2, 3, 'เก่งอยู่นี้หน่า', '2025-09-01 14:13:18', 1),
(35, 3, 2, 'จ้าาา', '2025-09-01 14:13:55', 1),
(36, 3, 2, '56454', '2025-09-01 14:51:51', 1),
(37, 3, 2, '45456', '2025-09-01 14:51:53', 1),
(38, 3, 2, 'ojo', '2025-09-01 14:51:53', 1),
(39, 2, 3, 'dwdwdwd', '2025-09-01 14:53:45', 1),
(40, 2, 3, 'เห้ย', '2025-09-01 15:02:29', 1),
(41, 2, 3, 'ยากจังวะ', '2025-09-01 15:02:31', 1),
(42, 3, 2, 'bfbfbfb', '2025-09-01 15:05:33', 1),
(43, 3, 2, 'ได้ยื่นข้อเสนอ: \'ป้ายอบจ.\' เรียบร้อยแล้ว', '2025-09-01 15:31:15', 1),
(44, 3, 2, ',pp,', '2025-09-03 19:20:01', 1),
(45, 3, 2, 'ได้ยื่นข้อเสนอ: \'tttt\' เรียบร้อยแล้ว', '2025-09-03 19:20:21', 1),
(46, 2, 3, '5456', '2025-09-03 19:23:47', 1),
(47, 3, 2, 'l;l;k', '2025-09-03 19:24:23', 1),
(48, 3, 2, 'kp', '2025-09-03 19:24:24', 1),
(49, 3, 2, 'opjopjop', '2025-09-08 08:38:18', 1),
(50, 3, 2, 'ได้ยื่นข้อเสนอ: \'ui\' เรียบร้อยแล้ว', '2025-09-08 08:40:06', 1),
(51, 3, 2, 'Hello', '2025-09-08 08:46:33', 1),
(52, 3, 2, 'Hello', '2025-09-08 08:46:33', 1),
(53, 4, 2, 'hi', '2025-09-08 10:38:08', 1),
(54, 3, 2, 'ได้ยื่นข้อเสนอ: \'รรรร\' เรียบร้อยแล้ว', '2025-09-08 11:16:24', 1),
(55, 3, 2, 'ได้ยื่นข้อเสนอ: \'เเเเเ\' เรียบร้อยแล้ว', '2025-09-08 12:17:40', 1),
(56, 3, 2, 'ได้ยื่นข้อเสนอ: \'ออกแบบป้าย\' เรียบร้อยแล้ว', '2025-09-08 16:19:23', 1),
(57, 3, 4, 'hello', '2025-09-09 02:45:34', 1),
(58, 3, 2, 'hello', '2025-09-09 03:09:19', 1),
(59, 3, 2, 'ได้ยื่นข้อเสนอ: \'ux ui\' เรียบร้อยแล้ว', '2025-09-09 03:31:33', 1),
(60, 3, 2, 'คุณได้รับคำขอจ้างงานใหม่: \'วาดการ์ตูนแนวก้านกล้วย\' (Request ID: #11). กรุณาตรวจสอบและยื่นใบเสนอราคา', '2025-09-10 09:07:08', 1),
(61, 3, 2, 'คุณได้รับคำขอจ้างงานใหม่: \'วาดการ์ตูนแนวก้านกล้วย\' (Request ID: #12). กรุณาตรวจสอบและยื่นใบเสนอราคา', '2025-09-13 07:24:10', 1),
(62, 2, 4, 'test', '2025-09-13 07:29:33', 1),
(63, 2, 4, 'test', '2025-09-13 07:34:18', 1),
(64, 2, 3, 'test', '2025-09-13 07:34:27', 1),
(65, 3, 2, 'เห', '2025-09-13 08:05:49', 1),
(66, 3, 2, 'เห', '2025-09-13 08:05:49', 1),
(67, 3, 2, 'ก', '2025-09-13 08:05:51', 1),
(68, 3, 2, 'ก', '2025-09-13 08:05:51', 1),
(69, 3, 2, 'ด', '2025-09-13 08:09:25', 1),
(70, 3, 2, 'ด', '2025-09-13 08:09:25', 1),
(71, 3, 2, 'กไกไก', '2025-09-13 08:09:28', 1),
(72, 3, 2, 'กไกไก', '2025-09-13 08:09:28', 1),
(73, 3, 2, 'เะเะเะเ', '2025-09-13 08:09:39', 1),
(74, 3, 2, 'เะเะเะเ', '2025-09-13 08:09:39', 1),
(75, 2, 3, 'กกกก', '2025-09-13 08:09:59', 1),
(76, 2, 3, 'หกหกห', '2025-09-13 08:10:01', 1),
(77, 3, 2, 'กดก', '2025-09-13 08:10:15', 1),
(78, 3, 2, 'กดก', '2025-09-13 08:10:15', 1),
(79, 3, 2, 'เพเ', '2025-09-13 08:12:08', 1),
(80, 3, 2, 'เพเ', '2025-09-13 08:12:08', 1),
(81, 3, 2, 'ด', '2025-09-13 08:14:39', 1),
(82, 3, 2, 'ด', '2025-09-13 08:14:41', 1),
(83, 3, 2, 'ลูกค้าได้ส่งคำขอจ้างงาน: \'test\' กรุณาพิจารณารับงานนี้', '2025-09-13 08:27:12', 1),
(84, 3, 2, 'ลูกค้าได้ส่งคำขอจ้างงาน: \'test\' กรุณาพิจารณารับงานนี้', '2025-09-13 08:32:41', 1),
(85, 3, 2, '.315', '2025-09-13 08:47:18', 1),
(86, 3, 2, 'h', '2025-09-13 10:00:55', 1),
(87, 3, 2, 'h', '2025-09-13 10:00:55', 1),
(88, 3, 2, 'เเ', '2025-09-13 10:07:33', 1),
(89, 3, 2, '้้', '2025-09-13 10:07:35', 1),
(90, 3, 2, '้ะ้้ะ้ะ้', '2025-09-13 10:07:42', 1),
(91, 3, 2, 'เะเะ', '2025-09-13 10:07:57', 1),
(92, 3, 2, 'เะเะเ', '2025-09-13 10:08:02', 1),
(93, 3, 2, 'tet', '2025-09-13 10:08:09', 1),
(94, 3, 2, 'tedy', '2025-09-13 10:08:13', 1),
(95, 3, 2, 'grgr', '2025-09-13 10:08:19', 1),
(96, 3, 2, 'grgr', '2025-09-13 10:08:19', 1),
(97, 3, 2, 'g', '2025-09-13 10:08:21', 1),
(98, 3, 2, 'g', '2025-09-13 10:08:21', 1),
(99, 3, 2, 'g', '2025-09-13 10:08:23', 1),
(100, 3, 2, 'g', '2025-09-13 10:08:23', 1),
(101, 3, 2, 'เะเะเะ', '2025-09-13 10:11:22', 1),
(102, 3, 2, 'เะเะเะเ', '2025-09-13 10:11:23', 1),
(103, 3, 2, 'ิ', '2025-09-13 10:11:25', 1),
(104, 3, 2, 'ได้ยื่นข้อเสนอ: \'yjyjyj\' เรียบร้อยแล้ว', '2025-09-13 10:13:59', 1),
(105, 3, 2, 'ได้ยื่นข้อเสนอ: \'หกดหกด\' เรียบร้อยแล้ว', '2025-09-13 10:25:28', 1),
(106, 3, 2, 'ได้ยื่นข้อเสนอ: \'tttttt\' เรียบร้อยแล้ว', '2025-09-13 10:29:16', 1),
(107, 3, 2, 'ได้ยื่นข้อเสนอ: \'รวนวร\' เรียบร้อยแล้ว', '2025-09-13 10:30:43', 1),
(108, 3, 2, 'ได้ยื่นข้อเสนอ: \'กด้กด้กด\' เรียบร้อยแล้ว', '2025-09-13 10:33:23', 1),
(109, 3, 2, 'ได้ยื่นข้อเสนอ: \'ไดำไดำดไำด\' เรียบร้อยแล้ว', '2025-09-13 10:35:27', 1),
(110, 3, 2, 'ด้เด', '2025-09-13 10:36:52', 1),
(111, 3, 2, 'ด้เด', '2025-09-13 10:36:52', 1),
(112, 3, 2, 'ด', '2025-09-13 10:39:13', 1),
(113, 3, 2, 'ด', '2025-09-13 10:39:13', 1),
(114, 3, 2, 'ด', '2025-09-13 10:39:15', 1),
(115, 3, 2, 'ด', '2025-09-13 10:39:15', 1),
(116, 3, 2, 'ด', '2025-09-13 10:39:17', 1),
(117, 3, 2, 'ด', '2025-09-13 10:39:17', 1),
(118, 3, 2, 'ด', '2025-09-13 10:39:20', 1),
(119, 3, 2, 'ด', '2025-09-13 10:39:20', 1),
(120, 3, 2, 'ไำพไำพ', '2025-09-13 10:40:54', 1),
(121, 3, 2, 'ไำพไำพ', '2025-09-13 10:40:54', 1),
(122, 3, 2, 'ำไพำไพไำพ', '2025-09-13 10:40:56', 1),
(123, 3, 2, 'ำไพำไพไำพ', '2025-09-13 10:40:56', 1),
(124, 3, 2, 'ไำพำไพ', '2025-09-13 10:40:57', 1),
(125, 3, 2, 'ไำพำไพ', '2025-09-13 10:40:57', 1),
(126, 3, 2, 'หกด', '2025-09-13 10:43:18', 1),
(127, 3, 2, 'หกด', '2025-09-13 10:43:20', 1),
(128, 3, 2, 'ด', '2025-09-13 10:43:24', 1),
(129, 3, 2, 'ได้ยื่นข้อเสนอ: \'ไำพไำพไำพ\' เรียบร้อยแล้ว', '2025-09-13 10:48:25', 1),
(130, 3, 2, 'ได้ยื่นข้อเสนอ: \'sdfdfs\' เรียบร้อยแล้ว', '2025-09-13 10:50:03', 1),
(131, 3, 2, '<b>📄 ใบเสนอราคาจ้างงาน</b><br><b>ชื่องาน/โปรเจกต์:</b> sdfdfs<br><b>ประเภทงาน:</b> -- กรุณาเลือกประเภทงาน --<br><b>รายละเอียด:</b> fsdfsdf<br><b>งบประมาณ:</b> 50000 บาท<br><b>ส่งมอบงานภายในวันที่:</b> 2025-09-26', '2025-09-13 10:50:03', 1),
(132, 3, 2, 'ได้ยื่นข้อเสนอ: \'tttttt\' เรียบร้อยแล้ว', '2025-09-13 10:50:26', 1),
(133, 3, 2, '<b>📄 ใบเสนอราคาจ้างงาน</b><br><b>ชื่องาน/โปรเจกต์:</b> tttttt<br><b>ประเภทงาน:</b> -- กรุณาเลือกประเภทงาน --<br><b>รายละเอียด:</b> sdfdsf<br><b>งบประมาณ:</b> 5000 บาท<br><b>ส่งมอบงานภายในวันที่:</b> 2025-09-21', '2025-09-13 10:50:26', 1),
(134, 3, 2, 'ได้ยื่นข้อเสนอ: \'werwerwer\' เรียบร้อยแล้ว', '2025-09-13 10:51:04', 1),
(135, 3, 2, '<b>📄 ใบเสนอราคาจ้างงาน</b><br><b>ชื่องาน/โปรเจกต์:</b> werwerwer<br><b>ประเภทงาน:</b> -- กรุณาเลือกประเภทงาน --<br><b>รายละเอียด:</b> asdasdasd<br><b>งบประมาณ:</b> 35000 บาท<br><b>ส่งมอบงานภายในวันที่:</b> 2025-09-20', '2025-09-13 10:51:04', 1),
(136, 3, 2, 'ได้ยื่นข้อเสนอ: \'ewrwerwer\' เรียบร้อยแล้ว', '2025-09-13 10:53:04', 1),
(137, 3, 2, '<b>📄 ใบเสนอราคาจ้างงาน</b><br><b>ชื่องาน/โปรเจกต์:</b> ewrwerwer<br><b>ประเภทงาน:</b> -- กรุณาเลือกประเภทงาน --<br><b>รายละเอียด:</b> sdfsdf<br><b>งบประมาณ:</b> 6000 บาท<br><b>ส่งมอบงานภายในวันที่:</b> 2025-09-20', '2025-09-13 10:53:04', 1),
(138, 3, 2, 'ได้ยื่นข้อเสนอ: \'rrrrrr\' เรียบร้อยแล้ว', '2025-09-13 10:56:21', 1),
(139, 3, 2, '<b>📄 ใบเสนอราคาจ้างงาน</b><br><b>ชื่องาน/โปรเจกต์:</b> rrrrrr<br><b>ประเภทงาน:</b> -- กรุณาเลือกประเภทงาน --<br><b>รายละเอียด:</b> rrrrrrrrrrrrrrr<br><b>งบประมาณ:</b> 60000 บาท<br><b>ส่งมอบงานภายในวันที่:</b> 2025-09-27', '2025-09-13 10:56:21', 1),
(140, 3, 2, 'ได้ยื่นข้อเสนอ: \'หกดหกด\' เรียบร้อยแล้ว', '2025-09-13 10:57:19', 1),
(141, 3, 2, '<b>📄 ใบเสนอราคาจ้างงาน</b><br><b>ชื่องาน/โปรเจกต์:</b> หกดหกด<br><b>ประเภทงาน:</b> -- กรุณาเลือกประเภทงาน --<br><b>รายละเอียด:</b> หกดหกดหกด<br><b>งบประมาณ:</b> 6000 บาท<br><b>ส่งมอบงานภายในวันที่:</b> 2025-09-16', '2025-09-13 10:57:19', 1),
(142, 3, 2, 'กไ', '2025-09-13 10:57:40', 1),
(143, 2, 3, '615', '2025-09-13 10:58:02', 1),
(144, 2, 3, '56465', '2025-09-13 10:58:03', 1),
(145, 2, 3, '65', '2025-09-13 10:58:05', 1),
(146, 3, 2, 'กห', '2025-09-13 10:58:26', 1),
(147, 3, 2, 'กห', '2025-09-13 10:58:26', 1),
(148, 3, 2, 'ห', '2025-09-13 10:58:30', 1),
(149, 3, 2, 'ห', '2025-09-13 10:58:30', 1),
(150, 3, 2, 'ำดำ', '2025-09-13 11:01:08', 1),
(151, 3, 2, 'ำดำ', '2025-09-13 11:01:08', 1),
(152, 3, 2, 'ดำ', '2025-09-13 11:01:10', 1),
(153, 3, 2, 'ดำ', '2025-09-13 11:01:10', 1),
(154, 3, 2, 'หกด', '2025-09-13 11:01:12', 1),
(155, 3, 2, 'หกด', '2025-09-13 11:01:12', 1),
(156, 3, 2, 'หกด', '2025-09-13 11:01:13', 1),
(157, 3, 2, 'หกด', '2025-09-13 11:01:13', 1),
(158, 3, 2, 'หกด', '2025-09-13 11:01:15', 1),
(159, 3, 2, 'หกด', '2025-09-13 11:01:15', 1),
(160, 3, 2, 'หกด', '2025-09-13 11:01:16', 1),
(161, 3, 2, 'หกด', '2025-09-13 11:01:16', 1),
(162, 3, 2, '323', '2025-09-13 11:01:22', 1),
(163, 3, 2, '323', '2025-09-13 11:01:22', 1),
(164, 3, 4, 'asd', '2025-09-13 11:02:22', 1),
(165, 3, 4, 'asd', '2025-09-13 11:02:22', 1),
(166, 3, 2, 'qwe', '2025-09-13 11:02:27', 1),
(167, 3, 2, 'qwe', '2025-09-13 11:02:27', 1),
(168, 3, 2, 'ฟหก', '2025-09-13 11:04:26', 1),
(169, 3, 2, 'ฟหก', '2025-09-13 11:04:26', 1),
(170, 3, 2, 'เเเเ', '2025-09-13 11:04:29', 1),
(171, 3, 2, 'เเเเ', '2025-09-13 11:04:29', 1),
(172, 3, 2, 'อแแออ', '2025-09-13 11:04:31', 1),
(173, 3, 2, 'อแแออ', '2025-09-13 11:04:31', 1),
(174, 3, 4, 's', '2025-09-13 11:07:36', 1),
(175, 3, 4, 's', '2025-09-13 11:07:36', 1),
(176, 3, 4, 'ไำพ', '2025-09-13 11:11:18', 1),
(177, 3, 4, 'ไำพ', '2025-09-13 11:11:18', 1),
(178, 3, 4, 'ไำพ', '2025-09-13 11:11:20', 1),
(179, 3, 4, 'ไำพ', '2025-09-13 11:11:20', 1),
(180, 3, 4, 'ำไพ', '2025-09-13 11:11:21', 1),
(181, 3, 4, 'ำไพ', '2025-09-13 11:11:21', 1),
(182, 3, 4, 'ำไพ', '2025-09-13 11:11:23', 1),
(183, 3, 4, 'ำไพ', '2025-09-13 11:11:23', 1),
(184, 3, 2, 'ฟหก', '2025-09-13 11:11:43', 1),
(185, 3, 2, 'ฟหก', '2025-09-13 11:11:43', 1),
(186, 3, 2, 'พ', '2025-09-13 11:11:45', 1),
(187, 3, 2, 'พ', '2025-09-13 11:11:45', 1),
(188, 3, 2, 'ไ', '2025-09-13 11:11:46', 1),
(189, 3, 2, 'ไ', '2025-09-13 11:11:46', 1),
(190, 3, 2, 'ไ', '2025-09-13 11:11:47', 1),
(191, 3, 2, 'ไ', '2025-09-13 11:11:47', 1),
(192, 3, 2, 'ีรส', '2025-09-13 11:12:45', 1),
(193, 3, 2, 'ีรส', '2025-09-13 11:12:45', 1),
(194, 3, 2, 'ีรส', '2025-09-13 11:12:47', 1),
(195, 3, 2, 'ีรส', '2025-09-13 11:12:47', 1),
(196, 3, 2, 'ีรส', '2025-09-13 11:12:49', 1),
(197, 3, 2, 'ีรส', '2025-09-13 11:12:49', 1),
(198, 3, 2, 'รีสรีส', '2025-09-13 11:12:54', 1),
(199, 3, 2, 'รีสรีส', '2025-09-13 11:12:54', 1),
(200, 3, 2, '545', '2025-09-13 11:15:39', 1),
(201, 3, 2, '5', '2025-09-13 11:15:40', 1),
(202, 3, 2, '7', '2025-09-13 11:15:41', 1),
(203, 3, 4, '6', '2025-09-13 11:15:45', 1),
(204, 3, 4, '6', '2025-09-13 11:15:45', 1),
(205, 3, 2, 'ลูกค้าได้ส่งคำขอจ้างงาน: \'test\' <a href=\'#\' class=\'view-request-details\' data-request-id=\'28\'>คลิกเพื่อดูรายละเอียด</a>', '2025-09-14 02:22:03', 1),
(206, 3, 2, 'คุณได้ส่งคำขอจ้างงาน: \'test\' <a href=\'#\' class=\'view-request-details\' data-request-id=\'29\'>สำเร็จแล้ว คลิกเพื่อดูรายละเอียด</a>', '2025-09-14 02:23:42', 1),
(207, 3, 2, 'คุณได้ส่งคำขอจ้างงาน: \'เเเเเเเเเเเเเเ\' \'สำเร็จแล้ว\'<a href=\'#\' class=\'view-request-details\' data-request-id=\'30\'>คลิกเพื่อดูรายละเอียด</a>', '2025-09-14 02:24:43', 1),
(208, 3, 2, 'คุณได้ส่งคำขอจ้างงาน: \'หกหหกห\' สำเร็จแล้ว<a href=\'#\' class=\'view-request-details\' data-request-id=\'31\'>คลิกเพื่อดูรายละเอียด</a>', '2025-09-14 02:25:03', 1),
(209, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"\\u0e14\\u0e01\\u0e14\\u0e01\\u0e14\\u0e01\\u0e14\",\"request_id\":32}', '2025-09-14 02:39:55', 1),
(210, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"\\u0e17\\u0e33 ux\\/ui \\u0e40\\u0e27\\u0e47\\u0e1a\\u0e2a\\u0e31\\u0e48\\u0e07\\u0e0b\\u0e37\\u0e49\\u0e2d\\u0e40\\u0e2a\\u0e37\\u0e49\\u0e2d\\u0e1c\\u0e49\\u0e32\",\"request_id\":33}', '2025-09-14 03:47:01', 1),
(211, 2, 3, 'สวัสดีครับ ผมเห็นงานแล้ว เดียวจะส่งใบเสนอราคาไปให้นะครับว่าคุณโอเคไหม', '2025-09-14 03:50:00', 1),
(212, 3, 2, 'ได้ครับ', '2025-09-14 03:52:36', 1),
(236, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"\\u0e01\\u0e14\\u0e2b\\u0e01\\u0e14\",\"request_id\":45}', '2025-09-15 13:37:45', 1),
(237, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"252525\",\"request_id\":46}', '2025-09-15 13:55:53', 1),
(238, 2, 3, 'หกดกหด', '2025-09-15 16:54:26', 1),
(239, 2, 3, 'กหดกหด', '2025-09-15 16:54:31', 1),
(240, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"tertert\",\"request_id\":47}', '2025-09-15 17:57:51', 1),
(241, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"hhhhh\",\"request_id\":48}', '2025-09-15 17:58:13', 1),
(242, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"\\u0e48\\u0e49\\u0e37\\u0e34\\u0e40\\u0e2d\\u0e14\\u0e2d\\u0e14\\u0e2d\\u0e14\\u0e2d\",\"request_id\":49}', '2025-09-15 17:59:50', 1),
(243, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"1231321\",\"request_id\":50}', '2025-09-15 19:43:28', 1),
(244, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"44444\",\"request_id\":51}', '2025-09-15 19:43:42', 1),
(245, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"11111111111\",\"request_id\":52}', '2025-09-15 20:26:29', 1),
(246, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"2222222222222222\",\"request_id\":53}', '2025-09-15 20:26:41', 1),
(247, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"3333333333\",\"request_id\":54}', '2025-09-15 20:26:56', 1),
(248, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"\\u0e01\\u0e1f\\u0e2b\\u0e01\\u0e1f\\u0e2b\\u0e01\\u0e1f\",\"request_id\":55}', '2025-09-15 22:36:49', 1),
(249, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"55555555553\",\"request_id\":56}', '2025-09-15 22:37:07', 1),
(250, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"test\",\"request_id\":57}', '2025-09-16 02:19:43', 1),
(251, 2, 3, 'test', '2025-09-16 02:23:40', 1),
(252, 3, 2, 'ผู้ว่าจ้างได้ส่งหลักฐานการชำระเงินสำหรับงาน Request ID: #57 แล้ว กรุณาตรวจสอบและยืนยันเพื่อเริ่มงาน', '2025-09-16 20:56:06', 1),
(253, 3, 2, 'ผู้ว่าจ้างได้ส่งหลักฐานการชำระเงินสำหรับงาน Request ID: #57 แล้ว กรุณาตรวจสอบและยืนยันเพื่อเริ่มงาน', '2025-09-16 20:56:26', 1),
(254, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"\\u0e44\\u0e33\\u0e1e\\u0e33\\u0e44\\u0e1e\\u0e33\\u0e44\\u0e1e\",\"request_id\":58}', '2025-09-17 06:19:56', 1),
(255, 3, 2, 'ผู้ว่าจ้างได้ส่งหลักฐานการชำระเงินสำหรับงาน Request ID: #49 แล้ว กรุณาตรวจสอบและยืนยันเพื่อเริ่มงาน', '2025-09-17 06:24:17', 1),
(256, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"tttttttt\",\"request_id\":59}', '2025-09-17 15:49:28', 1),
(257, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"yyyyyyyyyyyyy\",\"request_id\":60}', '2025-09-17 15:49:43', 1),
(258, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"\\u0e2b\\u0e2b\\u0e2b\\u0e2b\\u0e2b\\u0e2b\\u0e2b\\u0e2b\\u0e2b\\u0e2b\\u0e2b\",\"request_id\":61}', '2025-09-17 17:07:26', 1),
(259, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"\\u0e1b\\u0e1b\\u0e1b\\u0e1b\\u0e1b\\u0e1b\\u0e1b\\u0e1b\\u0e1b\\u0e1b\\u0e1b\",\"request_id\":62}', '2025-09-17 17:07:42', 1),
(260, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"\\u0e40\\u0e40\\u0e40\\u0e40\\u0e40\\u0e40\\u0e40\\u0e40\\u0e40\\u0e40\\u0e40\\u0e40\\u0e40\\u0e40\\u0e40\\u0e40\\u0e40\",\"request_id\":63}', '2025-09-17 17:19:16', 1),
(261, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"ggggggggggggg\",\"request_id\":64}', '2025-09-17 18:42:43', 1),
(262, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"414141\",\"request_id\":65}', '2025-09-17 18:42:58', 1),
(263, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"456456\",\"request_id\":66}', '2025-09-17 18:49:56', 1),
(264, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"\\u0e1f\\u0e01\\u0e14\\u0e1f\\u0e01\\u0e14\",\"request_id\":67}', '2025-09-17 18:50:18', 1),
(265, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"jjjjjjjjjjj\",\"request_id\":68}', '2025-09-17 18:50:35', 1),
(266, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"8546456\",\"request_id\":69}', '2025-09-17 19:05:52', 1),
(267, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"ppppppppppppp\",\"request_id\":70}', '2025-09-17 19:06:11', 1),
(268, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"898789789456132\",\"request_id\":71}', '2025-09-17 19:06:28', 1),
(269, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"lllllllll[lp[p\",\"request_id\":72}', '2025-09-17 19:06:45', 1),
(270, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"kjhgf\",\"request_id\":73}', '2025-09-17 19:07:01', 1),
(271, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"456465456\",\"request_id\":74}', '2025-09-17 21:31:48', 1),
(272, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"56456\",\"request_id\":75}', '2025-09-17 21:32:07', 1),
(273, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"8945612\",\"request_id\":76}', '2025-09-17 21:32:24', 1),
(274, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"test\",\"request_id\":77}', '2025-09-18 03:33:24', 1),
(275, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"\\u0e2d\\u0e2d\\u0e01\\u0e41\\u0e1a\\u0e1a ux\",\"request_id\":78}', '2025-09-18 03:40:41', 1),
(276, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"\\u0e22\\u0e34\\u0e19\\u0e14\\u0e35\\u0e01\\u0e31\\u0e1a\\u0e23\\u0e31\\u0e01\\u0e04\\u0e23\\u0e31\\u0e49\\u0e07\\u0e43\\u0e2b\\u0e21\\u0e48.\",\"request_id\":79}', '2025-09-20 08:35:34', 1),
(277, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"111111111\",\"request_id\":80}', '2025-09-20 08:51:56', 1),
(278, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"22222222222\",\"request_id\":81}', '2025-09-20 08:52:26', 1),
(279, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"33333\",\"request_id\":82}', '2025-09-20 08:52:47', 1),
(280, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"\\u0e40\\u0e2b\\u0e49\\u0e22\\u0e22\\u0e22\\u0e22\\u0e22\\u0e22\\u0e22\",\"request_id\":83}', '2025-09-20 09:28:01', 1),
(281, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"work\",\"request_id\":84}', '2025-09-20 09:49:11', 1),
(282, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"\\u0e2a\\u0e22\\u0e2a\\u0e22\\u0e2a\\u0e22\\u0e2a\\u0e22\\u0e2a\\u0e22\\u0e2a\\u0e22\",\"request_id\":85}', '2025-09-20 10:48:47', 1),
(283, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"\\u0e07\\u0e48\\u0e27\\u0e07\\u0e21\\u0e32\\u0e01\",\"request_id\":86}', '2025-09-20 21:10:17', 1),
(284, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"gegregrg\",\"request_id\":87}', '2025-09-22 17:51:48', 1),
(285, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"ertretert\",\"request_id\":88}', '2025-09-22 17:55:08', 1),
(286, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"test \\u0e0a\\u0e33\\u0e23\\u0e30\\u0e40\\u0e07\\u0e34\\u0e19\",\"request_id\":89}', '2025-09-22 19:20:38', 1),
(287, 3, 2, 'ผู้ว่าจ้างได้ส่งหลักฐานการชำระเงินสำหรับงาน Request ID: #89 แล้ว กรุณาตรวจสอบและยืนยันเพื่อเริ่มงาน', '2025-09-22 19:25:11', 1),
(288, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"test \\u0e0a\\u0e33\\u0e23\\u0e30\\u0e40\\u0e07\\u0e34\\u0e192\",\"request_id\":90}', '2025-09-22 19:42:27', 1),
(289, 3, 2, 'ผู้ว่าจ้างได้ส่งหลักฐานการชำระเงินสำหรับงาน Request ID: #90 แล้ว กรุณาตรวจสอบและยืนยันเพื่อเริ่มงาน', '2025-09-22 19:43:41', 1),
(290, 3, 2, 'ผู้ว่าจ้างได้ส่งหลักฐานการชำระเงินสำหรับงาน Request ID: #88 แล้ว กรุณาตรวจสอบและยืนยันเพื่อเริ่มงาน', '2025-09-22 19:50:17', 1),
(291, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"test \\u0e0a\\u0e33\\u0e23\\u0e30\\u0e40\\u0e07\\u0e34\\u0e193\",\"request_id\":91}', '2025-09-22 19:53:15', 1),
(292, 3, 2, 'ผู้ว่าจ้างได้ส่งหลักฐานการชำระเงินสำหรับงาน Request ID: #91 แล้ว กรุณาตรวจสอบและยืนยันเพื่อเริ่มงาน', '2025-09-22 19:54:12', 1),
(293, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"test \\u0e2a\\u0e48\\u0e07\\u0e44\\u0e1f\\u0e25\\u0e4c\\u0e09\\u0e1a\\u0e31\\u0e1a\\u0e23\\u0e48\\u0e32\\u0e071\",\"request_id\":92}', '2025-09-22 21:37:21', 1),
(294, 3, 2, 'ผู้ว่าจ้างได้ส่งหลักฐานการชำระเงินสำหรับงาน Request ID: #92 แล้ว กรุณาตรวจสอบและยืนยันเพื่อเริ่มงาน', '2025-09-22 21:38:16', 1),
(295, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"\\u0e2a\\u0e48\\u0e07\\u0e09\\u0e1a\\u0e31\\u0e1a\\u0e23\\u0e48\\u0e32\\u0e07 2\",\"request_id\":93}', '2025-09-22 22:41:43', 1),
(296, 3, 2, 'ผู้ว่าจ้างได้ส่งหลักฐานการชำระเงินสำหรับงาน Request ID: #93 แล้ว กรุณาตรวจสอบและยืนยันเพื่อเริ่มงาน', '2025-09-22 22:42:45', 1),
(297, 3, 2, 'ผู้ว่าจ้างได้ยอมรับงานฉบับร่างแล้ว และกำลังจะชำระเงินส่วนที่เหลือ', '2025-09-23 10:57:52', 1),
(298, 3, 2, 'ผู้ว่าจ้างขอแก้ไขงาน: <br><i>\"แก้สีหน่อย\"</i><br>กรุณาตรวจสอบและดำเนินการแก้ไข', '2025-09-23 10:58:16', 1),
(299, 3, 2, '4565465', '2025-09-23 11:02:28', 1),
(300, 2, 3, '5645645', '2025-09-23 11:02:49', 1),
(301, 3, 2, 'ผู้ว่าจ้างขอแก้ไขงาน: \"อยากให้แก้ไขเพิ่ม\"กรุณาตรวจสอบและดำเนินการแก้ไข', '2025-09-23 17:16:55', 1),
(302, 3, 2, 'ผู้ว่าจ้างขอแก้ไขงาน: \"5555\"กรุณาตรวจสอบและดำเนินการแก้ไข', '2025-09-23 17:18:22', 0),
(303, 3, 2, 'ผู้ว่าจ้างขอแก้ไขงาน: \"65656\"กรุณาตรวจสอบและดำเนินการแก้ไข', '2025-09-23 17:19:13', 0),
(304, 3, 2, 'ผู้ว่าจ้างได้ยอมรับงานฉบับร่างแล้ว และกำลังจะชำระเงินส่วนที่เหลือ', '2025-09-23 17:19:52', 0),
(305, 3, 2, 'ผู้ว่าจ้างได้ชำระเงินส่วนที่เหลือแล้ว กรุณาตรวจสอบและส่งมอบไฟล์งานสุดท้าย', '2025-09-23 17:45:18', 0),
(306, 3, 2, 'ผู้ว่าจ้างได้ชำระเงินส่วนที่เหลือแล้ว กรุณาตรวจสอบและส่งมอบไฟล์งานสุดท้าย', '2025-09-23 17:45:51', 0),
(307, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"test \\u0e23\\u0e27\\u0e21tab\",\"request_id\":94}', '2025-09-23 18:17:37', 0),
(308, 3, 2, 'ผู้ว่าจ้างได้ส่งหลักฐานการชำระเงินสำหรับงาน Request ID: #94 แล้ว กรุณาตรวจสอบและยืนยันเพื่อเริ่มงาน', '2025-09-23 18:18:27', 0),
(309, 3, 2, 'ผู้ว่าจ้างได้ยอมรับงานฉบับร่างแล้ว และกำลังจะชำระเงินส่วนที่เหลือ', '2025-09-23 18:22:15', 0),
(310, 3, 2, 'ผู้ว่าจ้างได้ส่งหลักฐานการชำระเงินสำหรับงาน Request ID: #94 แล้ว กรุณาตรวจสอบและยืนยันเพื่อเริ่มงาน', '2025-09-23 18:22:22', 0),
(311, 3, 2, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"\\u0e2a\\u0e27\\u0e31\\u0e2a\\u0e14\\u0e35\\u0e08\\u0e49\\u0e32\\u0e07\\u0e07\\u0e32\\u0e19\",\"request_id\":95}', '2025-09-23 18:33:30', 0),
(312, 3, 2, 'ผู้ว่าจ้างได้ส่งหลักฐานการชำระเงินสำหรับงาน Request ID: #95 แล้ว กรุณาตรวจสอบและยืนยันเพื่อเริ่มงาน', '2025-09-23 18:35:33', 0),
(313, 3, 2, 'ผู้ว่าจ้างได้ยอมรับงานฉบับร่างแล้ว และกำลังจะชำระเงินส่วนที่เหลือ', '2025-09-23 18:37:44', 0),
(314, 3, 2, 'ผู้ว่าจ้างได้ยอมรับงานฉบับร่างแล้ว และกำลังจะชำระเงินส่วนที่เหลือ', '2025-09-23 18:40:14', 0),
(315, 3, 2, 'ผู้ว่าจ้างได้ชำระเงินส่วนที่เหลือแล้ว กรุณาตรวจสอบและส่งมอบไฟล์งานสุดท้าย', '2025-09-23 18:40:39', 0),
(316, 3, 2, 'ผู้ว่าจ้างได้ชำระเงินส่วนที่เหลือแล้ว กรุณาตรวจสอบและส่งมอบไฟล์งานสุดท้าย', '2025-09-23 18:41:49', 0),
(317, 5, 4, 'SYSTEM_JOB_OFFER::{\"type\":\"job_offer\",\"title\":\"\\u0e2a\\u0e27\\u0e31\\u0e2a\\u0e14\\u0e35\\u0e08\\u0e49\\u0e32\\u0e07\\u0e07\\u0e32\\u0e19\",\"request_id\":96}', '2025-09-23 18:49:53', 1),
(318, 5, 4, 'ผู้ว่าจ้างได้ส่งหลักฐานการชำระเงินสำหรับงาน Request ID: #96 แล้ว กรุณาตรวจสอบและยืนยันเพื่อเริ่มงาน', '2025-09-23 19:29:56', 1),
(319, 5, 4, 'ผู้ว่าจ้างขอแก้ไขงาน: \"แก้ไข1\"กรุณาตรวจสอบและดำเนินการแก้ไข', '2025-09-23 19:45:15', 0),
(320, 5, 4, 'ผู้ว่าจ้างได้ยอมรับงานฉบับร่างแล้ว และกำลังจะชำระเงินส่วนที่เหลือ', '2025-09-23 19:46:10', 0),
(321, 5, 4, 'ผู้ว่าจ้างได้ชำระเงินส่วนที่เหลือแล้ว กรุณาตรวจสอบและส่งมอบไฟล์งานสุดท้าย', '2025-09-23 19:46:22', 0);

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address` text DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `profile_picture_url` varchar(255) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `tiktok_url` varchar(255) DEFAULT NULL,
  `payment_qr_code_url` varchar(255) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`profile_id`, `user_id`, `address`, `company_name`, `bio`, `skills`, `profile_picture_url`, `facebook_url`, `instagram_url`, `tiktok_url`, `payment_qr_code_url`, `bank_name`, `account_number`) VALUES
(1, 2, '123 Design St, BKK', 'PixelLink co. ltd', 'Passionate UI/UX designer and dev.', 'UX/UI, Figma, Photoshop, AI,Canva', '/uploads/profile_pictures/profile_2_1758564450.jpg', 'https://www.facebook.com/', 'https://www.instagram.com/', 'https://www.tiktok.com/th-TH/', '/uploads/qr_codes/qr_2_1758398445.jfif', 'กรุงไทย', '6790013520'),
(2, 3, '456 Business Rd, Nonthaburi', 'Acme Corp', NULL, NULL, '/uploads/profile_pictures/profile_3_1758482656.jpg', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 4, '789 Art Ave, Chiang Mai', '', 'Junior graphic designer looking for freelance work.', 'Photoshop, Illustrator', '/uploads/profile_pictures/profile_4_1758402259.jpg', '', '', '', '/uploads/qr_codes/qr_4_1758653643.jfif', 'กรุงไทย', '6790013520'),
(4, 5, '101 Tech Tower, Bangkok', 'Tech Corp', NULL, NULL, '/uploads/profile_pictures/profile_5_1758492204.jpg', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `reporter_id` int(11) NOT NULL,
  `reported_user_id` int(11) DEFAULT NULL,
  `reported_post_id` int(11) DEFAULT NULL,
  `report_type` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `report_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','resolved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`report_id`, `reporter_id`, `reported_user_id`, `reported_post_id`, `report_type`, `description`, `report_date`, `status`) VALUES
(1, 3, NULL, NULL, 'spam', 'มีผู้ใช้งานส่งข้อความสแปมเข้ามาในแชท', '2025-06-07 15:44:37', 'pending'),
(2, 2, 5, NULL, 'user_misconduct', 'ผู้ว่าจ้างไม่ตอบกลับหลังจากตกลงราคาแล้ว', '2025-06-07 15:44:37', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `reviewed_user_id` int(11) NOT NULL,
  `rating` decimal(2,1) DEFAULT NULL CHECK (`rating` >= 1.0 and `rating` <= 5.0),
  `comment` text DEFAULT NULL,
  `review_type` enum('designer_review_client','client_review_designer') DEFAULT NULL,
  `review_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `contract_id`, `reviewer_id`, `reviewed_user_id`, `rating`, `comment`, `review_type`, `review_date`, `status`) VALUES
(1, 1, 3, 2, 5.0, 'ออกแบบได้สวยงาม รวดเร็ว และเข้าใจความต้องการเป็นอย่างดีครับ', 'client_review_designer', '2025-06-07 15:44:37', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'site_name', 'PixelLink Platform', 'ชื่อเว็บไซต์หรือแพลตฟอร์ม', '2025-06-09 14:46:36'),
(2, 'admin_email', 'admin@example.com', 'อีเมลสำหรับผู้ดูแลระบบหรือการแจ้งเตือน', '2025-06-09 14:46:36'),
(3, 'platform_commission_rate', '10.00', 'อัตราค่าคอมมิชชั่นของแพลตฟอร์ม (เป็นเปอร์เซ็นต์)', '2025-06-09 14:46:36'),
(4, 'min_withdrawal_amount', '500', 'จำนวนเงินขั้นต่ำในการถอนเงิน', '2025-06-09 14:46:36');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `payer_id` int(11) NOT NULL,
  `payee_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) DEFAULT NULL,
  `status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `slip_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `contract_id`, `payer_id`, `payee_id`, `amount`, `transaction_date`, `payment_method`, `status`, `slip_path`) VALUES
(1, 1, 3, 2, 3500.00, '2025-06-07 15:44:37', 'Credit Card', 'completed', NULL),
(2, 18, 3, 2, 140.00, '2025-09-22 19:25:11', 'Bank Transfer', 'pending', NULL),
(3, 19, 3, 2, 180.00, '2025-09-22 19:43:41', 'Bank Transfer', 'pending', '../uploads/payment_slips/slip_90_1758570221.png'),
(4, 20, 3, 2, 130.00, '2025-09-22 19:50:17', 'Bank Transfer', 'pending', '../uploads/payment_slips/slip_88_1758570617.jpg'),
(5, 21, 3, 2, 178.00, '2025-09-22 19:54:12', 'Bank Transfer', 'pending', '../uploads/payment_slips/slip_91_1758570852.jpg'),
(6, 22, 3, 2, 760.00, '2025-09-22 21:38:16', 'Bank Transfer', 'pending', '../uploads/payment_slips/slip_92_1758577096.png'),
(7, 23, 3, 2, 1300.00, '2025-09-22 22:42:45', 'Bank Transfer', 'pending', '../uploads/payment_slips/slip_93_1758580965.jpg'),
(8, 23, 3, 2, 5200.00, '2025-09-23 17:45:18', 'Bank Transfer', 'pending', '../uploads/payment_slips/final_slip_93_1758649518.jpg'),
(9, 22, 3, 2, 3040.00, '2025-09-23 17:45:51', 'Bank Transfer', 'pending', '../uploads/payment_slips/final_slip_92_1758649551.jpg'),
(10, 24, 3, 2, 13.00, '2025-09-23 18:18:27', 'Bank Transfer', 'pending', '../uploads/payment_slips/slip_94_1758651507.png'),
(11, 24, 3, 2, 13.00, '2025-09-23 18:22:22', 'Bank Transfer', 'pending', '../uploads/payment_slips/slip_94_1758651742.png'),
(12, 25, 3, 2, 70.00, '2025-09-23 18:35:33', 'Bank Transfer', 'pending', '../uploads/payment_slips/slip_95_1758652533.png'),
(13, 21, 3, 2, 712.00, '2025-09-23 18:40:39', 'Bank Transfer', 'pending', '../uploads/payment_slips/final_slip_91_1758652839.png'),
(14, 25, 3, 2, 280.00, '2025-09-23 18:41:49', 'Bank Transfer', 'pending', '../uploads/payment_slips/final_slip_95_1758652909.png'),
(15, 26, 5, 4, 70.00, '2025-09-23 19:29:56', 'Bank Transfer', 'pending', '../uploads/payment_slips/slip_96_1758655796.png'),
(16, 26, 5, 4, 280.00, '2025-09-23 19:46:22', 'Bank Transfer', 'pending', '../uploads/payment_slips/final_slip_96_1758656782.png');

-- --------------------------------------------------------

--
-- Table structure for table `uploaded_files`
--

CREATE TABLE `uploaded_files` (
  `file_id` int(11) NOT NULL,
  `contract_id` int(11) DEFAULT NULL,
  `job_post_id` int(11) DEFAULT NULL,
  `uploader_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_size` int(11) DEFAULT NULL,
  `uploaded_by_user_id` int(11) DEFAULT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `uploaded_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uploaded_files`
--

INSERT INTO `uploaded_files` (`file_id`, `contract_id`, `job_post_id`, `uploader_id`, `file_name`, `file_path`, `uploaded_at`, `file_size`, `uploaded_by_user_id`, `file_type`, `uploaded_date`) VALUES
(23, NULL, NULL, 2, '', '../uploads/job_images/job_img_6889ca3c8f767.jpg', '2025-07-30 07:31:08', 314895, 2, 'image/jpeg', '2025-07-30 14:31:08'),
(24, NULL, NULL, 2, '', '../uploads/job_images/job_img_6889ce803fae4.jpg', '2025-07-30 07:49:20', 314895, 2, 'image/jpeg', '2025-07-30 14:49:20'),
(25, NULL, NULL, 2, 'job_img_6889d42d330ec1753863213.jpg', '../uploads/job_images/job_img_6889d42d330ec1753863213.jpg', '2025-07-30 08:13:33', 568822, NULL, 'image/jpeg', '2025-07-30 15:13:33'),
(26, NULL, NULL, 2, 'job_img_688a2790a325c1753884560.png', '../uploads/job_images/job_img_688a2790a325c1753884560.png', '2025-07-30 14:09:20', 206400, NULL, 'image/png', '2025-07-30 21:09:20'),
(27, NULL, NULL, 2, 'job_img_689075243fba91754297636.png', '../uploads/job_images/job_img_689075243fba91754297636.png', '2025-08-04 08:53:56', 206400, NULL, 'image/png', '2025-08-04 15:53:56'),
(29, NULL, NULL, 2, 'job_img_6890a9c2375571754311106.jpg', '../uploads/job_images/job_img_6890a9c2375571754311106.jpg', '2025-08-04 12:38:26', 295791, NULL, 'image/jpeg', '2025-08-04 19:38:26'),
(35, NULL, NULL, 2, 'job_img_6891638b0f6ec1754358667.png', '../uploads/job_images/job_img_6891638b0f6ec1754358667.png', '2025-08-05 01:51:07', 1058584, NULL, 'image/png', '2025-08-05 08:51:07'),
(36, NULL, NULL, 2, 'job_img_689164471af951754358855.jpg', '../uploads/job_images/job_img_689164471af951754358855.jpg', '2025-08-05 01:54:15', 50249, NULL, 'image/jpeg', '2025-08-05 08:54:15'),
(38, NULL, NULL, 2, 'job_img_68916a6ee76d51754360430.png', '../uploads/job_images/job_img_68916a6ee76d51754360430.png', '2025-08-05 02:20:30', 1058584, NULL, 'image/png', '2025-08-05 09:20:30'),
(41, NULL, NULL, 2, 'job_img_6891925c8b2381754370652.png', '../uploads/job_images/job_img_6891925c8b2381754370652.png', '2025-08-05 05:10:52', 1058584, NULL, 'image/png', '2025-08-05 12:10:52'),
(44, NULL, NULL, 2, 'job_img_689437dad240e1754544090.png', '../uploads/job_images/job_img_689437dad240e1754544090.png', '2025-08-07 05:21:30', 1058584, NULL, 'image/png', '2025-08-07 12:21:30'),
(46, NULL, NULL, 2, 'job_img_68b5a673ab19e1756735091.png', '../uploads/job_images/job_img_68b5a673ab19e1756735091.png', '2025-09-01 13:58:11', 206400, NULL, 'image/png', '2025-09-01 20:58:11'),
(47, NULL, NULL, 2, 'job_img_68c7f4d57c2a51757934805.png', '../uploads/job_images/job_img_68c7f4d57c2a51757934805.png', '2025-09-15 11:13:25', 60490, NULL, 'image/png', '2025-09-15 18:13:25'),
(48, 4, NULL, 3, 'slip_57_1758056166.jpg', 'uploads/payment_slips/slip_57_1758056166.jpg', '2025-09-16 20:56:06', NULL, NULL, 'image/jpeg', NULL),
(49, 4, NULL, 3, 'slip_57_1758056186.png', 'uploads/payment_slips/slip_57_1758056186.png', '2025-09-16 20:56:26', NULL, NULL, 'image/png', NULL),
(50, 5, NULL, 3, 'slip_49_1758090257.png', 'uploads/payment_slips/slip_49_1758090257.png', '2025-09-17 06:24:17', NULL, NULL, 'image/png', NULL),
(51, NULL, NULL, 2, 'job_img_68cac625575211758119461.jfif', '../uploads/job_images/job_img_68cac625575211758119461.jfif', '2025-09-17 14:31:01', 133241, NULL, 'image/jpeg', '2025-09-17 21:31:01'),
(52, NULL, NULL, 2, 'job_img_68cb1abc8e4a81758141116.png', '../uploads/job_images/job_img_68cb1abc8e4a81758141116.png', '2025-09-17 20:31:56', 140746, NULL, 'image/png', '2025-09-18 03:31:56'),
(53, NULL, NULL, 2, 'job_img_68cb1bf862ec51758141432.png', '../uploads/job_images/job_img_68cb1bf862ec51758141432.png', '2025-09-17 20:37:12', 542903, NULL, 'image/png', '2025-09-18 03:37:12'),
(54, NULL, NULL, 2, 'job_img_68cb1c0a062a01758141450.png', '../uploads/job_images/job_img_68cb1c0a062a01758141450.png', '2025-09-17 20:37:30', 143688, NULL, 'image/png', '2025-09-18 03:37:30'),
(55, NULL, NULL, 2, 'job_img_68cb1c14964be1758141460.png', '../uploads/job_images/job_img_68cb1c14964be1758141460.png', '2025-09-17 20:37:40', 55987, NULL, 'image/png', '2025-09-18 03:37:40'),
(56, NULL, NULL, 4, 'job_img_68cb1c41a14a01758141505.png', '../uploads/job_images/job_img_68cb1c41a14a01758141505.png', '2025-09-17 20:38:25', 547297, NULL, 'image/png', '2025-09-18 03:38:25'),
(57, NULL, NULL, 4, 'job_img_68cb1c50cbd7e1758141520.png', '../uploads/job_images/job_img_68cb1c50cbd7e1758141520.png', '2025-09-17 20:38:40', 607406, NULL, 'image/png', '2025-09-18 03:38:40'),
(58, NULL, NULL, 4, 'job_img_68cb1c5b252131758141531.png', '../uploads/job_images/job_img_68cb1c5b252131758141531.png', '2025-09-17 20:38:51', 287932, NULL, 'image/png', '2025-09-18 03:38:51'),
(59, NULL, NULL, 4, 'job_img_68cb1c69767cb1758141545.png', '../uploads/job_images/job_img_68cb1c69767cb1758141545.png', '2025-09-17 20:39:05', 431342, NULL, 'image/png', '2025-09-18 03:39:05'),
(60, NULL, NULL, 2, 'job_img_68cef0de54e1e1758392542.png', '../uploads/job_images/job_img_68cef0de54e1e1758392542.png', '2025-09-20 18:22:22', 143688, NULL, 'image/png', '2025-09-21 01:22:22'),
(61, NULL, NULL, 2, 'job_img_68cf122ecc3e91758401070.png', '../uploads/job_images/job_img_68cf122ecc3e91758401070.png', '2025-09-20 20:44:30', 55987, NULL, 'image/png', '2025-09-21 03:44:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `user_type` enum('admin','designer','client') NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_approved` tinyint(1) DEFAULT 0,
  `last_activity` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Not Verified, 1=Verified',
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `first_name`, `last_name`, `phone_number`, `user_type`, `registration_date`, `is_approved`, `last_activity`, `is_active`, `is_verified`, `last_login`) VALUES
(1, 'admin', '12345678', 'admin@pixellink.com', 'กฤษดา', 'บุญจันดา', '0901234567', 'admin', '2025-06-07 15:44:37', 1, NULL, 1, 1, NULL),
(2, 'khoapun', '1234', 'jane@example.com', 'ศิขริน', 'คอมิธิน', '0812345678', 'designer', '2025-06-07 15:44:37', 1, '2025-09-16 23:30:42', 1, 1, NULL),
(3, 'beer888', '1234', 'bob@company.com', 'เบียร์', 'สมิท', '0987654321', 'client', '2025-06-07 15:44:37', 1, '2025-09-24 01:33:08', 1, 1, NULL),
(4, 'anna', '1234', 'anna@portfolio.net', 'Anna', 'Lee', '0891112222', 'designer', '2025-06-07 15:44:37', 1, '2025-09-09 09:49:42', 1, 0, NULL),
(5, 'tech_corp', '1234', 'hr@techcorp.com', 'Tech', 'Corp HR', '029998888', 'client', '2025-06-07 15:44:37', 1, '2025-09-24 02:45:22', 1, 0, NULL),
(6, 'krit.ti', '12345678', 'krit.ti@rmuti.ac.th', 'Krit', 'T.siriwattana', '0000000000', 'admin', '2025-06-08 11:16:59', 1, NULL, 1, 0, NULL),
(7, 'kitsada.in', '1234', 'pakawat.in@gmail.com', 'kitsada', 'Ariyawatkul\r\n', '0000000000', 'designer', '2025-06-09 07:58:49', 1, NULL, 1, 0, NULL),
(10, 'party888', '1234', 'kkiii@gmail.com', 'กิตติพงศ์', 'เถื่อนกลาง', '0555555555', 'designer', '2025-06-24 09:38:07', 1, NULL, 1, 0, NULL),
(12, 'TESTTTTT', 'Test_lll123456789@', 'KKKKKKK@gmail.com', 'TEST12332', 'PROJECT1', '0999999999', 'designer', '2025-07-07 14:54:31', 1, NULL, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `verification_submissions`
--

CREATE TABLE `verification_submissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `document_path` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verification_submissions`
--

INSERT INTO `verification_submissions` (`id`, `user_id`, `document_path`, `status`, `submitted_at`) VALUES
(1, 2, '/uploads/verification_docs/verify_designer_2_1758491828.png', 'approved', '2025-09-21 21:57:08'),
(2, 3, '/uploads/verification_docs/verify_client_3_1758492014.png', 'approved', '2025-09-21 22:00:14'),
(3, 5, '/uploads/verification_docs/verify_client_5_1758492212.png', 'pending', '2025-09-21 22:03:32'),
(4, 2, '/uploads/verification_docs/verify_designer_2_1758539679.png', 'approved', '2025-09-22 11:14:39'),
(5, 3, '/uploads/verification_docs/verify_client_3_1758539699.png', 'approved', '2025-09-22 11:14:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `client_job_requests`
--
ALTER TABLE `client_job_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `designer_id` (`designer_id`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`contract_id`),
  ADD UNIQUE KEY `request_id` (`request_id`),
  ADD KEY `designer_id` (`designer_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `designer_portfolio`
--
ALTER TABLE `designer_portfolio`
  ADD PRIMARY KEY (`portfolio_id`),
  ADD KEY `designer_id` (`designer_id`);

--
-- Indexes for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `designer_id` (`designer_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `job_categories`
--
ALTER TABLE `job_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `job_postings`
--
ALTER TABLE `job_postings`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `designer_id` (`designer_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `from_user_id` (`from_user_id`),
  ADD KEY `to_user_id` (`to_user_id`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `reporter_id` (`reporter_id`),
  ADD KEY `reported_user_id` (`reported_user_id`),
  ADD KEY `reported_post_id` (`reported_post_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `contract_id` (`contract_id`),
  ADD KEY `reviewer_id` (`reviewer_id`),
  ADD KEY `reviewed_user_id` (`reviewed_user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `contract_id` (`contract_id`),
  ADD KEY `payer_id` (`payer_id`),
  ADD KEY `payee_id` (`payee_id`);

--
-- Indexes for table `uploaded_files`
--
ALTER TABLE `uploaded_files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `contract_id` (`contract_id`),
  ADD KEY `uploader_id` (`uploader_id`),
  ADD KEY `fk_job_post_id` (`job_post_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `verification_submissions`
--
ALTER TABLE `verification_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `client_job_requests`
--
ALTER TABLE `client_job_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `contract_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `designer_portfolio`
--
ALTER TABLE `designer_portfolio`
  MODIFY `portfolio_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `job_categories`
--
ALTER TABLE `job_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `job_postings`
--
ALTER TABLE `job_postings`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=763;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=322;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `uploaded_files`
--
ALTER TABLE `uploaded_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `verification_submissions`
--
ALTER TABLE `verification_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `client_job_requests`
--
ALTER TABLE `client_job_requests`
  ADD CONSTRAINT `client_job_requests_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `client_job_requests_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `job_categories` (`category_id`),
  ADD CONSTRAINT `client_job_requests_ibfk_3` FOREIGN KEY (`designer_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `contracts_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `client_job_requests` (`request_id`),
  ADD CONSTRAINT `contracts_ibfk_2` FOREIGN KEY (`designer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `contracts_ibfk_3` FOREIGN KEY (`client_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `designer_portfolio`
--
ALTER TABLE `designer_portfolio`
  ADD CONSTRAINT `designer_portfolio_ibfk_1` FOREIGN KEY (`designer_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD CONSTRAINT `job_applications_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `client_job_requests` (`request_id`),
  ADD CONSTRAINT `job_applications_ibfk_2` FOREIGN KEY (`designer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `job_applications_ibfk_3` FOREIGN KEY (`client_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `job_postings`
--
ALTER TABLE `job_postings`
  ADD CONSTRAINT `job_postings_ibfk_1` FOREIGN KEY (`designer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `job_postings_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `job_categories` (`category_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`reported_user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reports_ibfk_3` FOREIGN KEY (`reported_post_id`) REFERENCES `job_postings` (`post_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`contract_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`reviewed_user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`contract_id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`payer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`payee_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `uploaded_files`
--
ALTER TABLE `uploaded_files`
  ADD CONSTRAINT `fk_job_post_id` FOREIGN KEY (`job_post_id`) REFERENCES `job_postings` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `uploaded_files_ibfk_2` FOREIGN KEY (`uploader_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `verification_submissions`
--
ALTER TABLE `verification_submissions`
  ADD CONSTRAINT `verification_submissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
