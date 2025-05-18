-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2025 at 03:03 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `exam_monitoring`
--
CREATE DATABASE IF NOT EXISTS `exam_monitoring` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `exam_monitoring`;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admission_office','invigilator') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `admins`:
--

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `password_hash`, `role`, `created_at`) VALUES
(1, 'adminoffice', 'atc123', '$2y$10$V43nu/l9p6IjrWwaFooauetBlKdMH6wVpvSaSnfWPGzk9/zT5.KnW', 'admission_office', '2025-05-17 16:21:54'),
(2, 'invig', 'lct123', '$2y$10$PMk2TrEPjp.nNtqpf.TnL.GgQKjyB6eyiR4.0mqueeiq0OBRiFsaC', 'invigilator', '2025-05-17 16:21:54'),
(3, 'roland', 'lab4', '$2y$10$vx.TD11u9KFPklEAcRkK/eI1eQHNB.3jIaocUaAQleHm8K/SeySwm', 'invigilator', '2025-05-17 16:29:28'),
(4, 'emmanuel', '2512', '$2y$10$WGyl3PbkaKZqZNwCMgUHu.qcQkpiSQQRhD8k522R5.wl./3KAK/xO', 'admission_office', '2025-05-17 19:41:53');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `admission_no` varchar(50) NOT NULL,
  `exam_no` varchar(50) NOT NULL,
  `venue` varchar(50) NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `invigilator_id` int(11) NOT NULL,
  `invigilator_name` varchar(100) NOT NULL,
  `attendance_status` enum('present','absent') NOT NULL DEFAULT 'absent'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `attendance`:
--   `student_id`
--       `students` -> `id`
--

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `admission_no`, `exam_no`, `venue`, `recorded_at`, `invigilator_id`, `invigilator_name`, `attendance_status`) VALUES
(1, 499, '23050512020', 'T2/EB/42/5538', 'US02', '2025-05-17 16:48:56', 2, 'invig', 'present'),
(2, 500, '23030612006', 'T2/EB/42/1960', 'US02', '2025-05-17 16:48:56', 2, 'invig', 'present'),
(3, 415, '23020312212', 'T2/C/42/1886', 'F12', '2025-05-17 16:54:11', 2, 'invig', 'present'),
(4, 416, '23020312213', 'T2/C/42/6586', 'F12', '2025-05-17 16:54:11', 2, 'invig', 'present'),
(5, 417, '23020312217', 'T2/C/42/8006', 'F12', '2025-05-17 16:54:11', 2, 'invig', 'present'),
(6, 418, '23020312222', 'T2/C/42/4097', 'F12', '2025-05-17 16:54:11', 2, 'invig', 'present'),
(7, 401, '23010202011', 'T2/C/42/5411', 'F12', '2025-05-17 16:54:11', 2, 'invig', 'present'),
(8, 402, '23020312166', 'T2/C/42/8481', 'F12', '2025-05-17 16:54:11', 2, 'invig', 'present'),
(9, 499, '23050512020', 'T2/EB/42/5538', 'F12', '2025-05-17 16:54:11', 2, 'invig', 'present'),
(10, 500, '23030612006', 'T2/EB/42/1960', 'F12', '2025-05-17 16:54:11', 2, 'invig', 'present'),
(11, 411, '23020312200', 'T2/C/42/2192', 'F12', '2025-05-17 17:08:02', 2, 'invig', 'present'),
(12, 411, '23020312200', 'T2/C/42/2192', 'F12', '2025-05-17 17:14:57', 2, 'invig', 'present'),
(13, 394, '23020302027', 'T2/C/42/1459', 'US02', '2025-05-17 17:14:57', 2, 'invig', 'present'),
(16, 513, '23041112012', 'T2/EB/42/6338', 'DH', '2025-05-17 17:25:42', 2, 'invig', 'present'),
(17, 502, '23030602001', 'T2/EB/42/3081', 'DH', '2025-05-17 17:25:42', 2, 'invig', 'present'),
(18, 513, '23041112012', 'T2/EB/42/6338', 'DH', '2025-05-17 17:26:47', 2, 'invig', 'present'),
(19, 506, '23030612022', 'T2/EB/42/6601', 'DH', '2025-05-17 17:26:47', 2, 'invig', 'present'),
(20, 512, '23030612031', 'T2/EB/42/6094', 'DH', '2025-05-17 18:07:01', 2, 'invig', 'present');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `exam_id` int(11) NOT NULL,
  `course` varchar(255) NOT NULL,
  `exam_date` date NOT NULL,
  `venue` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `exams`:
--

-- --------------------------------------------------------

--
-- Table structure for table `incidents`
--

CREATE TABLE `incidents` (
  `id` int(11) NOT NULL,
  `incident_type` varchar(100) NOT NULL,
  `reported_by` varchar(100) NOT NULL,
  `admission_no` varchar(50) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `details` text NOT NULL,
  `reviewed_status` enum('pending','reviewed') NOT NULL DEFAULT 'pending',
  `reported_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `incidents`:
--

--
-- Dumping data for table `incidents`
--

INSERT INTO `incidents` (`id`, `incident_type`, `reported_by`, `admission_no`, `student_id`, `details`, `reviewed_status`, `reported_at`) VALUES
(1, 'student', 'roland', '23050512018', 441, 'The student with admission number 23050512018 (Abdallah Mussa Shariff) has done the following: oievownpvqwnpiwinwepewnpofeqm', 'reviewed', '2025-05-17 16:58:38'),
(2, 'student', 'roland', '23050512018', 441, 'The student with admission number 23050512018 (Abdallah Mussa Shariff) has done the following: he has copied', 'reviewed', '2025-05-17 19:34:26');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `reports`:
--   `exam_id`
--       `exams` -> `exam_id`
--

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `settings`:
--

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'theme', 'light'),
(2, 'security_level', 'medium');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `admission_no` varchar(50) NOT NULL,
  `nta_level` varchar(50) DEFAULT NULL,
  `exam_no` varchar(50) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `venue` varchar(50) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `attended` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `students`:
--

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `admission_no`, `nta_level`, `exam_no`, `program`, `venue`, `image_path`, `created_at`, `attended`) VALUES
(267, 'Abillah Mmole matiku', '23010112001', '4', 'T2/AE/42/7070', 'Ordinary Diploma In Auto-electrical And Electronics Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(268, 'Brayan Mathato Kimario', '23050512038', '4', 'T2/AE/42/9855', 'Ordinary Diploma In Auto-electrical And Electronics Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(269, 'Calvin Francis Leandry', '23010102013', '4', 'T2/AE/42/1910', 'Ordinary Diploma In Auto-electrical And Electronics Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(270, 'Carolina Abraham Karua', '23010112003', '4', 'T2/AE/42/5224', 'Ordinary Diploma In Auto-electrical And Electronics Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(271, 'Edson Venus Kimario', '23010112042', '4', 'T2/AE/42/5265', 'Ordinary Diploma In Auto-electrical And Electronics Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(272, 'Ezra Aman Mwambebule', '23010112010', '4', 'T2/AE/42/9597', 'Ordinary Diploma In Auto-electrical And Electronics Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(273, 'Faustin Benjamin Ndimila', '23010112011', '4', 'T2/AE/42/8627', 'Ordinary Diploma In Auto-electrical And Electronics Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(274, 'Hekima Oscar Sila', '23010112015', '4', 'T2/AE/42/5238', 'Ordinary Diploma In Auto-electrical And Electronics Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(275, 'Hisham Chande Natty', '23010112017', '4', 'T2/AE/42/4231', 'Ordinary Diploma In Auto-electrical And Electronics Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(276, 'Nassoro Hassan Mtachala', '23010212024', '4', 'T2/AE/42/4106', 'Ordinary Diploma In Auto-electrical And Electronics Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(277, 'Niyumva Edmas Buyabara', '23010112028', '4', 'T2/AE/42/2428', 'Ordinary Diploma In Auto-electrical And Electronics Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(278, 'Sada Kongwe Mpinga', '23010102010', '4', 'T2/AE/42/9948', 'Ordinary Diploma In Auto-electrical And Electronics Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(279, 'Yakobo Panday Qali', '23010112037', '4', 'T2/AE/42/6801', 'Ordinary Diploma In Auto-electrical And Electronics Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(280, 'Kimweri Emmanuel jkbqiqqce', '23010112022', '4', 'T2/AE/42/3131', 'Ordinary Diploma In Auto-electrical And Electronics Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(281, 'Mariki Owen Myumbilwa', '23010112023', '4', 'T2/AE/42/4918', 'Ordinary Diploma In Auto-electrical And Electronics Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(282, 'Nassir Lessi Majala', '23010112027', '4', 'T2/AE/42/7242', 'Ordinary Diploma In Auto-electrical And Electronics Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(283, 'Yagala Saidi sido', '23010112036', '4', 'T2/AE/42/9087', 'Ordinary Diploma In Auto-electrical And Electronics Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(284, 'Daniel Binomgabi Erick', '23010202004', '4', 'T2/A/42/6879', 'Ordinary Diploma In Automotive Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(285, 'Doreen Nyesiga Respicius', '23010202006', '4', 'T2/A/42/6609', 'Ordinary Diploma In Automotive Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(286, 'Elipidius Godwin Augustine', '23010202007', '4', 'T2/A/42/9090', 'Ordinary Diploma In Automotive Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(287, 'Fourie M Zacharia', '23010212011', '4', 'T2/A/42/9711', 'Ordinary Diploma In Automotive Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(288, 'Greyson Christom Mwagu', '23010212014', '4', 'T2/A/42/4878', 'Ordinary Diploma In Automotive Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(289, 'John Lazima John', '23061212042', '4', 'T2/A/42/8430', 'Ordinary Diploma In Automotive Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(290, 'Jumanne Shabani Mboga', '23010212018', '4', 'T2/A/42/5062', 'Ordinary Diploma In Automotive Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(291, 'Leticia Kasalali Lupilya', '23010202009', '4', 'T2/A/42/2383', 'Ordinary Diploma In Automotive Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(292, 'Ramadhani Mwidini Mkumbaru', '23010212028', '4', 'T2/A/42/5662', 'Ordinary Diploma In Automotive Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(293, 'Richard Gasper Mrosso', '23030612217', '4', 'T2/A/42/3878', 'Ordinary Diploma In Automotive Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(294, 'Vigginia Charles Nyoni', '23010202013', '4', 'T2/A/42/5390', 'Ordinary Diploma In Automotive Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(295, 'Collin Kelvin Nyanda', '23010212007', '4', 'T2/A/42/6851', 'Ordinary Diploma In Automotive Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(296, 'Herneva Elisamehe Meironyi', '23010212016', '4', 'T2/A/42/4385', 'Ordinary Diploma In Automotive Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(297, 'Junior Joseph Temu', '23010212019', '4', 'T2/A/42/1831', 'Ordinary Diploma In Automotive Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(298, 'Lagwen Goodness Dahaye', '23010212021', '4', 'T2/A/42/3986', 'Ordinary Diploma In Automotive Engineering', 'G11', NULL, '2025-05-17 16:57:23', 0),
(299, 'Nazael Chrispin Chabule', '23010212025', '4', 'T2/A/42/4545', 'Ordinary Diploma In Automotive Engineering', 'G11', NULL, '2025-05-17 16:57:23', 0),
(300, 'Saidi Nasibu Msuya', '23010212029', '4', 'T2/A/42/9556', 'Ordinary Diploma In Automotive Engineering', 'G11', NULL, '2025-05-17 16:57:23', 0),
(301, 'Agribeta Anastasius Nayambo', '23070912002', '4', 'T2/CH/42/3070', 'Ordinary Diploma In Civil And Highway Engineering', 'S10', NULL, '2025-05-17 16:57:23', 0),
(302, 'Bless Bonifas Masole', '23070912010', '4', 'T2/CH/42/3621', 'Ordinary Diploma In Civil And Highway Engineering', 'S10', NULL, '2025-05-17 16:57:23', 0),
(303, 'Christopher Godwin Kileo', '23070902003', '4', 'T2/CH/42/7308', 'Ordinary Diploma In Civil And Highway Engineering', 'S10', NULL, '2025-05-17 16:57:23', 0),
(304, 'Damiano Doita Joseph', '23070912014', '4', 'T2/CH/42/7495', 'Ordinary Diploma In Civil And Highway Engineering', 'S10', NULL, '2025-05-17 16:57:23', 0),
(305, 'David Timotheo Mushi', '23030612050', '4', 'T2/CH/42/4106', 'Ordinary Diploma In Civil And Highway Engineering', 'S10', NULL, '2025-05-17 16:57:23', 0),
(306, 'Efraim Greyson Mlanga', '23070912023', '4', 'T2/CH/42/6199', 'Ordinary Diploma In Civil And Highway Engineering', 'S10', NULL, '2025-05-17 16:57:23', 0),
(307, 'Eunice Godwin Lema', '23020312073', '4', 'T2/CH/42/4122', 'Ordinary Diploma In Civil And Highway Engineering', 'S10', NULL, '2025-05-17 16:57:23', 0),
(308, 'Faustine Mussa Boyi', '23070912026', '4', 'T2/CH/42/8436', 'Ordinary Diploma In Civil And Highway Engineering', 'S10', NULL, '2025-05-17 16:57:23', 0),
(309, 'Gerson Modest Tarimo', '23070902018', '4', 'T2/CH/42/9728', 'Ordinary Diploma In Civil And Highway Engineering', 'S10', NULL, '2025-05-17 16:57:23', 0),
(310, 'Hussein Ramadhani Omary', '23070902008', '4', 'T2/CH/42/3949', 'Ordinary Diploma In Civil And Highway Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(311, 'Iqraa Sururu Kassiba', '23070902009', '4', 'T2/CH/42/2635', 'Ordinary Diploma In Civil And Highway Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(312, 'Isaya Alex Wangwe', '23070912039', '4', 'T2/CH/42/8108', 'Ordinary Diploma In Civil And Highway Engineering', 'S10', NULL, '2025-05-17 16:57:23', 0),
(313, 'Kelvin Kizito Sway', '23070902010', '4', 'T2/CH/42/1152', 'Ordinary Diploma In Civil And Highway Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(314, 'Laura Rodrick Kweka', '23070902011', '4', 'T2/CH/42/3422', 'Ordinary Diploma In Civil And Highway Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(315, 'Lucian Omari juma', '23070912050', '4', 'T2/CH/42/6494', 'Ordinary Diploma In Civil And Highway Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(316, 'Luqman Athuman Abubakar', '23070902012', '4', 'T2/CH/42/7089', 'Ordinary Diploma In Civil And Highway Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(317, 'Mashalla-kelvin Charles Mashall a', '23020302018', '4', 'T2/CH/42/8973', 'Ordinary Diploma In Civil And Highway Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(318, 'Mussa Shija Tanganyika', '23070912056', '4', 'T2/CH/42/1521', 'Ordinary Diploma In Civil And Highway Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(319, 'Mwanahiba Chilumba Mohamm', '23070912057', '4', 'T2/CH/42/1487', 'Ordinary Diploma In Civil And Highway Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(320, 'Octavian Severin Sorole', '23061402011', '4', 'T2/CH/42/7700', 'Ordinary Diploma In Civil And Highway Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(321, 'Petro Emmanuel Casmiry', '23070912061', '4', 'T2/CH/42/4863', 'Ordinary Diploma In Civil And Highway Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(322, 'Ramadhani Mnyindo Mshana', '23070912062', '4', 'T2/CH/42/8367', 'Ordinary Diploma In Civil And Highway Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(323, 'Robin Fabian Kisingi', '23070912064', '4', 'T2/CH/42/4675', 'Ordinary Diploma In Civil And Highway Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(324, 'Victor Deus Manyama', '23070912072', '4', 'T2/CH/42/6439', 'Ordinary Diploma In Civil And Highway Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(325, 'Walter Heriamini Mghase', '23061402014', '4', 'T2/CH/42/8920', 'Ordinary Diploma In Civil And Highway Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(326, 'Winfrida Isdory Kisoka', '23070902016', '4', 'T2/CH/42/5425', 'Ordinary Diploma In Civil And Highway Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(327, 'Anitha Henry Sanga', '23070912006', '4', 'T2/CH/42/6311', 'Ordinary Diploma In Civil And Highway Engineering', 'F12', NULL, '2025-05-17 16:57:23', 0),
(328, 'Deborah John Kaira', '23070902017', '4', 'T2/CH/42/3356', 'Ordinary Diploma In Civil And Highway Engineering', 'F12', NULL, '2025-05-17 16:57:23', 0),
(329, 'Godliving Sigifrid Tarimo', '23070912033', '4', 'T2/CH/42/5984', 'Ordinary Diploma In Civil And Highway Engineering', 'F12', NULL, '2025-05-17 16:57:23', 0),
(330, 'Jonathan Jonas Mtani', '23070912043', '4', 'T2/CH/42/6506', 'Ordinary Diploma In Civil And Highway Engineering', 'F12', NULL, '2025-05-17 16:57:23', 0),
(331, 'Maxjunior Kolbe Innocent', '23070902013', '4', 'T2/CH/42/9166', 'Ordinary Diploma In Civil And Highway Engineering', 'F12', NULL, '2025-05-17 16:57:23', 0),
(332, 'Rebecca Justice Mdabida', '23070912063', '4', 'T2/CH/42/7645', 'Ordinary Diploma In Civil And Highway Engineering', 'F12', NULL, '2025-05-17 16:57:23', 0),
(333, 'Christopher Kelvin Sagala', '23020402003', '4', 'T2/CI/42/2100', 'Ordinary Diploma In Civil And Irrigation Engineering', 'F12', NULL, '2025-05-17 16:57:23', 0),
(334, 'Dagobert Medard Munubi', '23020402005', '4', 'T2/CI/42/3629', 'Ordinary Diploma In Civil And Irrigation Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(335, 'Jenipher Alphonce Njiku', '23020412010', '4', 'T2/CI/42/6805', 'Ordinary Diploma In Civil And Irrigation Engineering', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(336, 'Jesca Kalikwela Godwin', '23020402009', '4', 'T2/CI/42/5986', 'Ordinary Diploma In Civil And Irrigation Engineering', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(337, 'Nyafuru Jovin Ruhumbika', '23020402011', '4', 'T2/CI/42/4257', 'Ordinary Diploma In Civil And Irrigation Engineering', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(338, 'Stella Nyalandu Mnyeti', '23020402013', '4', 'T2/CI/42/1510', 'Ordinary Diploma In Civil And Irrigation Engineering', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(339, 'Steven Frank Chambo', '22020412027', '4', 'T2/CI/42/8089', 'Ordinary Diploma In Civil And Irrigation Engineering', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(340, 'Swagarya Ramadhani Swagarya', '23020412017', '4', 'T2/CI/42/7399', 'Ordinary Diploma In Civil And Irrigation Engineering', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(341, 'Vency Adam Lema', '23020402014', '4', 'T2/CI/42/5408', 'Ordinary Diploma In Civil And Irrigation Engineering', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(342, 'Eliazari Benard Damas', '23020412005', '4', 'T2/CI/42/8335', 'Ordinary Diploma In Civil And Irrigation Engineering', 'S10', NULL, '2025-05-17 16:57:23', 0),
(343, 'Godwin Albart Paresso', '23020412008', '4', 'T2/CI/42/8451', 'Ordinary Diploma In Civil And Irrigation Engineering', 'S10', NULL, '2025-05-17 16:57:23', 0),
(344, 'James Patrick Kabaye', '23020412009', '4', 'T2/CI/42/9032', 'Ordinary Diploma In Civil And Irrigation Engineering', 'S10', NULL, '2025-05-17 16:57:23', 0),
(345, 'Abdulrahman Othman Ramadha', '23020302001', '4', 'T2/C/42/8617', 'Ordinary Diploma In Civil Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(346, 'Abraham Silas Mollel', '23020312003', '4', 'T2/C/42/7452', 'Ordinary Diploma In Civil Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(347, 'Abubakari Ramadhani Nkya', '23020312005', '4', 'T2/C/42/5579', 'Ordinary Diploma In Civil Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(348, 'Agness Chacha Mgesi', '23020312007', '4', 'T2/C/42/4862', 'Ordinary Diploma In Civil Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(349, 'Alicia Borniface Mchome', '23020302002', '4', 'T2/C/42/2119', 'Ordinary Diploma In Civil Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(350, 'Ammos Mazengo chacha', '23020312018', '4', 'T2/C/42/2566', 'Ordinary Diploma In Civil Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(351, 'Anthony Kulwa Anthony', '23020312023', '4', 'T2/C/42/9813', 'Ordinary Diploma In Civil Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(352, 'Ayubu Habibu Rajabu', '23020312027', '4', 'T2/C/42/9385', 'Ordinary Diploma In Civil Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(353, 'Bakari Charles Maningi', '23020312028', '4', 'T2/C/42/6850', 'Ordinary Diploma In Civil Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(354, 'Bedastus Masunu Benson', '23020312035', '4', 'T2/C/42/8865', 'Ordinary Diploma In Civil Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(355, 'Bless Makene Makene', '23020302004', '4', 'T2/C/42/6491', 'Ordinary Diploma In Civil Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(356, 'Brighton John Ndunguru', '23061402003', '4', 'T2/C/42/4793', 'Ordinary Diploma In Civil Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(357, 'Brightson Eliniachie Ngowi', '23020302005', '4', 'T2/C/42/3122', 'Ordinary Diploma In Civil Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(358, 'Colin Edmund Mkenda', '23020302006', '4', 'T2/C/42/9782', 'Ordinary Diploma In Civil Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(359, 'Daudi Dotto Anthony', '23020312049', '4', 'T2/C/42/6551', 'Ordinary Diploma In Civil Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(360, 'David Prosper Minja', '23020302007', '4', 'T2/C/42/1474', 'Ordinary Diploma In Civil Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(361, 'Dennis Herman Mchau', '23020302025', '4', 'T2/C/42/6664', 'Ordinary Diploma In Civil Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(362, 'Emmanuel Bundala Kayanda', '23020302026', '4', 'T2/C/42/2619', 'Ordinary Diploma In Civil Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(363, 'Erick Jonas Ndoliki', '23030712054', '4', 'T2/C/42/3447', 'Ordinary Diploma In Civil Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(364, 'Ernest Yesaya Lyanga', '23020312072', '4', 'T2/C/42/5446', 'Ordinary Diploma In Civil Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(365, 'Eusebio Joseph Kazimorogoro', '23020312074', '4', 'T2/C/42/8226', 'Ordinary Diploma In Civil Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(366, 'Evance Essau Lyimo', '23020312075', '4', 'T2/C/42/2806', 'Ordinary Diploma In Civil Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(367, 'Francis Elihuruma Melami', '23020312079', '4', 'T2/C/42/7542', 'Ordinary Diploma In Civil Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(368, 'Frank Edward Mollel', '23020312081', '4', 'T2/C/42/7659', 'Ordinary Diploma In Civil Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(369, 'Fred Sammy Mwansasu', '23020312083', '4', 'T2/C/42/7253', 'Ordinary Diploma In Civil Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(370, 'Fredrick S Wilson', '23020312084', '4', 'T2/C/42/1794', 'Ordinary Diploma In Civil Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(371, 'Geofrey Machugu Kisanta', '23020312088', '4', 'T2/C/42/8498', 'Ordinary Diploma In Civil Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(372, 'George Gerald Kamundi', '23020312089', '4', 'T2/C/42/2923', 'Ordinary Diploma In Civil Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(373, 'Gerald George Assey', '23020312090', '4', 'T2/C/42/8605', 'Ordinary Diploma In Civil Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(374, 'Gideon Mathias Massawe', '23020312092', '4', 'T2/C/42/1757', 'Ordinary Diploma In Civil Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(375, 'Gift Deosdedith Hunja', '23020302012', '4', 'T2/C/42/9429', 'Ordinary Diploma In Civil Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(376, 'Godbless Ngemela Archard', '23020302013', '4', 'T2/C/42/7883', 'Ordinary Diploma In Civil Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(377, 'Godfrey Gerald Tarimo', '22020312158', '4', 'T2/C/42/9739', 'Ordinary Diploma In Civil Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(378, 'Godlove Meshack Ngailo', '23020312093', '4', 'T2/C/42/9660', 'Ordinary Diploma In Civil Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(379, 'Grace Trophone Mariseli', '23020302014', '4', 'T2/C/42/8974', 'Ordinary Diploma In Civil Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(380, 'Hafidhu Hussein Omari', '22020312056', '4', 'T2/C/42/3614', 'Ordinary Diploma In Civil Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(381, 'Halima Mohamed Johora', '23020312098', '4', 'T2/C/42/2370', 'Ordinary Diploma In Civil Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(382, 'Hamis A Mkwizu', '23050512086', '4', 'T2/C/42/2721', 'Ordinary Diploma In Civil Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(383, 'Hellyansen Jackson Challe', '23031802007', '4', 'T2/C/42/1234', 'Ordinary Diploma In Civil Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(384, 'Idd Juma Idd', '23020312102', '4', 'T2/C/42/8361', 'Ordinary Diploma In Civil Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(385, 'Joel Peniel Masaki', '23020312115', '4', 'T2/C/42/8673', 'Ordinary Diploma In Civil Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(386, 'Johnson Junior Machibya', '22020312080', '4', 'T2/C/42/3777', 'Ordinary Diploma In Civil Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(387, 'Jolin Johnstone Kakiziba', '23020302015', '5', 'T2/C/42/4949', 'Ordinary Diploma In Civil Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(388, 'Jordan Rogers Mushi', '23020312121', '5', 'T2/C/42/8022', 'Ordinary Diploma In Civil Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(389, 'Joseph Jerome Shiyo', '23020312122', '5', 'T2/C/42/8769', 'Ordinary Diploma In Civil Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(390, 'Joshua Vedasto Kaijage', '22020312082', '5', 'T2/C/42/3912', 'Ordinary Diploma In Civil Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(391, 'Julieth Anandumi Kirundwa', '23020312127', '5', 'T2/C/42/5796', 'Ordinary Diploma In Civil Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(392, 'Justine Lenard Shaba', '23020312131', '5', 'T2/C/42/7850', 'Ordinary Diploma In Civil Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(393, 'Kelvin Innocent Mamiro', '23020302016', '5', 'T2/C/42/9249', 'Ordinary Diploma In Civil Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(394, 'Kelvin Roman Kileo', '23020302027', '5', 'T2/C/42/1459', 'Ordinary Diploma In Civil Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(395, 'Leodger Peter john', '23020312144', '5', 'T2/C/42/9199', 'Ordinary Diploma In Civil Engineering', 'G11', NULL, '2025-05-17 16:57:23', 0),
(396, 'Leonard Samwel Magweiga', '23020312145', '5', 'T2/C/42/9925', 'Ordinary Diploma In Civil Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(397, 'Mackmilian Emilian Lyimo', '23020302024', '5', 'T2/C/42/5157', 'Ordinary Diploma In Civil Engineering', 'G11', NULL, '2025-05-17 16:57:23', 0),
(398, 'Magesa Redenatus Mashauri', '23020312151', '5', 'T2/C/42/9625', 'Ordinary Diploma In Civil Engineering', 'G11', NULL, '2025-05-17 16:57:23', 0),
(399, 'Marco Joseph Kingu', '23020312152', '5', 'T2/C/42/4230', 'Ordinary Diploma In Civil Engineering', 'G11', NULL, '2025-05-17 16:57:23', 0),
(400, 'Mark Sogoyo Simbila', '23020312155', '5', 'T2/C/42/2333', 'Ordinary Diploma In Civil Engineering', 'G11', NULL, '2025-05-17 16:57:23', 0),
(401, 'Moses Nyamwihula Crodward', '23010202011', '5', 'T2/C/42/5411', 'Ordinary Diploma In Civil Engineering', 'G11', NULL, '2025-05-17 16:57:23', 0),
(402, 'Mwajuma Amri Shakani', '23020312166', '6', 'T2/C/42/8481', 'Ordinary Diploma In Civil Engineering', 'G11', NULL, '2025-05-17 16:57:23', 0),
(403, 'Nolasco C Haule', '21020312158', '6', 'T2/C/42/2801', 'Ordinary Diploma In Civil Engineering', 'G11', NULL, '2025-05-17 16:57:23', 0),
(404, 'Oscar Kashamba Mununi', '23020312172', '6', 'T2/C/42/7190', 'Ordinary Diploma In Civil Engineering', 'G11', NULL, '2025-05-17 16:57:23', 0),
(405, 'Patricia Augustino Hakika', '23020302023', '6', 'T2/C/42/3789', 'Ordinary Diploma In Civil Engineering', 'G11', NULL, '2025-05-17 16:57:23', 0),
(406, 'Peter Kansarage Deogratias', '23020312174', '6', 'T2/C/42/3026', 'Ordinary Diploma In Civil Engineering', 'G11', NULL, '2025-05-17 16:57:23', 0),
(407, 'Rahim Juma Pima', '23020312179', '6', 'T2/C/42/3498', 'Ordinary Diploma In Civil Engineering', 'G11', NULL, '2025-05-17 16:57:23', 0),
(408, 'Rodlin Petro Mrema', '23020312182', '6', 'T2/C/42/3653', 'Ordinary Diploma In Civil Engineering', 'F12', NULL, '2025-05-17 16:57:23', 0),
(409, 'Samwel Silvester Shechambo', '23020312192', '6', 'T2/C/42/8008', 'Ordinary Diploma In Civil Engineering', 'F12', NULL, '2025-05-17 16:57:23', 0),
(410, 'Shufaa M Omar', '23020312197', '6', 'T2/C/42/2337', 'Ordinary Diploma In Civil Engineering', 'F12', NULL, '2025-05-17 16:57:23', 0),
(411, 'Solomon Daud Mkumbo', '23020312200', '6', 'T2/C/42/2192', 'Ordinary Diploma In Civil Engineering', 'F12', NULL, '2025-05-17 16:57:23', 0),
(412, 'Stephan Sostenesi Kaonja', '23020312202', '6', 'T2/C/42/1183', 'Ordinary Diploma In Civil Engineering', 'F12', NULL, '2025-05-17 16:57:23', 0),
(413, 'Vedastus Nyahili Mwikwabe', '23020312207', '6', 'T2/C/42/2457', 'Ordinary Diploma In Civil Engineering', 'F12', NULL, '2025-05-17 16:57:23', 0),
(414, 'Wilbroad William Ginana', '23020302022', '6', 'T2/C/42/3693', 'Ordinary Diploma In Civil Engineering', 'F12', NULL, '2025-05-17 16:57:23', 0),
(415, 'Wilson Albert Galahenga', '23020312212', '6', 'T2/C/42/1886', 'Ordinary Diploma In Civil Engineering', 'F12', NULL, '2025-05-17 16:57:23', 0),
(416, 'Wilson William Gumbo', '23020312213', '4', 'T2/C/42/6586', 'Ordinary Diploma In Civil Engineering', 'F12', NULL, '2025-05-17 16:57:23', 0),
(417, 'Yehoshafati Gilbert Mbesere', '23020312217', '4', 'T2/C/42/8006', 'Ordinary Diploma In Civil Engineering', 'F12', NULL, '2025-05-17 16:57:23', 0),
(418, 'Yusufu Mustafa Abdala', '23020312222', '4', 'T2/C/42/4097', 'Ordinary Diploma In Civil Engineering', 'F12', NULL, '2025-05-17 16:57:23', 0),
(419, 'Abondo Luckson Abondo', '23020312002', '4', 'T2/C/42/5739', 'Ordinary Diploma In Civil Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(420, 'Amos Aloyce Loserian', '23020312019', '4', 'T2/C/42/3193', 'Ordinary Diploma In Civil Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(421, 'Baraka Kediel Mshana', '23030612029', '4', 'T2/C/42/2804', 'Ordinary Diploma In Civil Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(422, 'Benedicto James Yakobo', '23020312036', '4', 'T2/C/42/6986', 'Ordinary Diploma In Civil Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(423, 'Bukombe Lucas Bugeraha', '23020312043', '4', 'T2/C/42/3865', 'Ordinary Diploma In Civil Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(424, 'Coly Noah Mafie', '23051012088', '4', 'T2/C/42/6506', 'Ordinary Diploma In Civil Engineering', 'T10', NULL, '2025-05-17 16:57:23', 0),
(425, 'Dickson Emanuel Kitomari', '23020312056', '4', 'T2/C/42/8141', 'Ordinary Diploma In Civil Engineering', 'T11', NULL, '2025-05-17 16:57:23', 0),
(426, 'Elick Nyamweru Nyamweru', '23020302010', '4', 'T2/C/42/1461', 'Ordinary Diploma In Civil Engineering', 'T11', NULL, '2025-05-17 16:57:23', 0),
(427, 'Elisha Elisante Naftali', '23020312065', '4', 'T2/C/42/4447', 'Ordinary Diploma In Civil Engineering', 'T11', NULL, '2025-05-17 16:57:23', 0),
(428, 'Elvis Prosper Shirima', '23020302011', '4', 'T2/C/42/4073', 'Ordinary Diploma In Civil Engineering', 'T11', NULL, '2025-05-17 16:57:23', 0),
(429, 'Francis Martin Goi', '23020312080', '4', 'T2/C/42/8756', 'Ordinary Diploma In Civil Engineering', 'T11', NULL, '2025-05-17 16:57:23', 0),
(430, 'Gasper Michael Mtweve', '23020312086', '4', 'T2/C/42/4517', 'Ordinary Diploma In Civil Engineering', 'T11', NULL, '2025-05-17 16:57:23', 0),
(431, 'Jordan Nuru Mathayo', '23020312120', '4', 'T2/C/42/3422', 'Ordinary Diploma In Civil Engineering', 'T11', NULL, '2025-05-17 16:57:23', 0),
(432, 'Konna Mtaki Nyamuhega', '23020312139', '4', 'T2/C/42/8775', 'Ordinary Diploma In Civil Engineering', 'T11', NULL, '2025-05-17 16:57:23', 0),
(433, 'Latifa Rashid Habibu', '23020312140', '4', 'T2/C/42/9193', 'Ordinary Diploma In Civil Engineering', 'T11', NULL, '2025-05-17 16:57:23', 0),
(434, 'Laurent Lucas Kashindye', '23020312142', '4', 'T2/C/42/8282', 'Ordinary Diploma In Civil Engineering', 'T12', NULL, '2025-05-17 16:57:23', 0),
(435, 'Nathan Nahum Nanyaro', '23020312168', '4', 'T2/C/42/3378', 'Ordinary Diploma In Civil Engineering', 'T12', NULL, '2025-05-17 16:57:23', 0),
(436, 'Nuru Fedrick Tsere', '23020312171', '4', 'T2/C/42/9837', 'Ordinary Diploma In Civil Engineering', 'T12', NULL, '2025-05-17 16:57:23', 0),
(437, 'Ramadhani Abdallah Kileng`a', '23020312227', '4', 'T2/C/42/1553', 'Ordinary Diploma In Civil Engineering', 'T12', NULL, '2025-05-17 16:57:23', 0),
(438, 'Simon S Chilanza', '23020312198', '4', 'T2/C/42/4151', 'Ordinary Diploma In Civil Engineering', 'T12', NULL, '2025-05-17 16:57:23', 0),
(439, 'Stephano K Lapya', '23020312203', '4', 'T2/C/42/7053', 'Ordinary Diploma In Civil Engineering', 'T12', NULL, '2025-05-17 16:57:23', 0),
(440, 'Veronica Baraka Mollel', '23020312208', '4', 'T2/C/42/1202', 'Ordinary Diploma In Civil Engineering', 'T12', NULL, '2025-05-17 16:57:23', 0),
(441, 'Abdallah Mussa Shariff', '23050512018', '4', 'T2/CS/42/4802', 'Ordinary Diploma In Computer Science', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(442, 'Abdul Masoud Mkambara', '23050512019', '4', 'T2/CS/42/1349', 'Ordinary Diploma In Computer Science', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(443, 'Albert Fred Mallya', '23050512002', '4', 'T2/CS/42/5590', 'Ordinary Diploma In Computer Science', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(444, 'Annuari Juma Omari', '23050512032', '4', 'T2/CS/42/6003', 'Ordinary Diploma In Computer Science', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(445, 'Arnold Baltazary Marandu', '23050512166', '7', 'T2/CS/42/1438', 'Ordinary Diploma In Computer Science', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(446, 'Ashrafu Hussein Hashimu', '23050502001', '7', 'T2/CS/42/8835', 'Ordinary Diploma In Computer Science', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(447, 'Bright Juma Hakika', '23050512040', '7', 'T2/CS/42/4295', 'Ordinary Diploma In Computer Science', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(448, 'Bryson Elias Mollel', '23050512041', '7', 'T2/CS/42/1075', 'Ordinary Diploma In Computer Science', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(449, 'Busungu Fadhili Busungu', '23050502002', '7', 'T2/CS/42/1864', 'Ordinary Diploma In Computer Science', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(450, 'Chrisant Andrew Kleruu', '23050512046', '7', 'T2/CS/42/6351', 'Ordinary Diploma In Computer Science', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(451, 'Daniel Timoth Matope', '23050512053', '7', 'T2/CS/42/2556', 'Ordinary Diploma In Computer Science', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(452, 'David Modestus Mbwatila', '23050512054', '7', 'T2/CS/42/9961', 'Ordinary Diploma In Computer Science', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(453, 'Elina Asteri John', '23050502004', '7', 'T2/CS/42/2916', 'Ordinary Diploma In Computer Science', 'UG06', NULL, '2025-05-17 16:57:23', 0),
(454, 'Enock Brayson Moshi', '23050512066', '7', 'T2/CS/42/5189', 'Ordinary Diploma In Computer Science', 'S06', NULL, '2025-05-17 16:57:23', 0),
(455, 'Evodi Athumani Nzenga', '23050512070', '7', 'T2/CS/42/4755', 'Ordinary Diploma In Computer Science', 'UG07', NULL, '2025-05-17 16:57:23', 0),
(456, 'Godson Gervas Mweta', '23050512082', '7', 'T2/CS/42/1999', 'Ordinary Diploma In Computer Science', 'UG07', NULL, '2025-05-17 16:57:23', 0),
(457, 'Harison Limbu Mizingo', '23050512087', '7', 'T2/CS/42/1022', 'Ordinary Diploma In Computer Science', 'UG07', NULL, '2025-05-17 16:57:23', 0),
(458, 'Hendry E Kimaro', '23050512090', '7', 'T2/CS/42/9839', 'Ordinary Diploma In Computer Science', 'UG07', NULL, '2025-05-17 16:57:23', 0),
(459, 'Hope Mutalemwa Alex', '23050502006', '7', 'T2/CS/42/1582', 'Ordinary Diploma In Computer Science', 'S06', NULL, '2025-05-17 16:57:23', 0),
(460, 'Irene Apolinary Steven', '23050502007', '7', 'T2/CS/42/8404', 'Ordinary Diploma In Computer Science', 'S06', NULL, '2025-05-17 16:57:23', 0),
(461, 'Jackline Samwel Henry', '23050512094', '7', 'T2/CS/42/4621', 'Ordinary Diploma In Computer Science', 'UG07', NULL, '2025-05-17 16:57:23', 0),
(462, 'James Goodluck Olal', '23050512008', '7', 'T2/CS/42/9520', 'Ordinary Diploma In Computer Science', 'UG07', NULL, '2025-05-17 16:57:23', 0),
(463, 'Joseph Pascal Mahoo', '23050512098', '7', 'T2/CS/42/4658', 'Ordinary Diploma In Computer Science', 'UG07', NULL, '2025-05-17 16:57:23', 0),
(464, 'Joshua Johnson Lyimo', '22050512028', '7', 'T2/CS/42/4059', 'Ordinary Diploma In Computer Science', 'UG07', NULL, '2025-05-17 16:57:23', 0),
(465, 'Joshua Richard Kiula', '23050512101', '7', 'T2/CS/42/2401', 'Ordinary Diploma In Computer Science', 'UG07', NULL, '2025-05-17 16:57:23', 0),
(466, 'Kepha Paul Chima', '23050512106', '7', 'T2/CS/42/2696', 'Ordinary Diploma In Computer Science', 'UG07', NULL, '2025-05-17 16:57:23', 0),
(467, 'Khatibu Ali Malumbo', '23050512107', '7', 'T2/CS/42/1370', 'Ordinary Diploma In Computer Science', 'S06', NULL, '2025-05-17 16:57:23', 0),
(468, 'Laurence Oscar Msangira', '23050512110', '7', 'T2/CS/42/5348', 'Ordinary Diploma In Computer Science', 'UG07', NULL, '2025-05-17 16:57:23', 0),
(469, 'Mohamed Zaharani Kisilwa', '23050512116', '7', 'T2/CS/42/6134', 'Ordinary Diploma In Computer Science', 'UG07', NULL, '2025-05-17 16:57:23', 0),
(470, 'Mushi Leonard Milton', '22050512114', '7', 'T2/CS/42/5183', 'Ordinary Diploma In Computer Science', 'UG07', NULL, '2025-05-17 16:57:23', 0),
(471, 'Naswiru Byamungu Nuha', '23050512117', '7', 'T2/CS/42/9665', 'Ordinary Diploma In Computer Science', 'UG07', NULL, '2025-05-17 16:57:23', 0),
(472, 'Nuzrath Hashim Mchalikwao', '23050512123', '7', 'T2/CS/42/2835', 'Ordinary Diploma In Computer Science', 'UG07', NULL, '2025-05-17 16:57:23', 0),
(473, 'Peniel Menangsilig Salekwa', '23050502013', '7', 'T2/CS/42/8234', 'Ordinary Diploma In Computer Science', 'S06', NULL, '2025-05-17 16:57:23', 0),
(474, 'Rachel Yohana Bwire', '23020302019', '7', 'T2/CS/42/2865', 'Ordinary Diploma In Computer Science', 'UG07', NULL, '2025-05-17 16:57:23', 0),
(475, 'Rahman Getiga Sogone', '23050512129', '7', 'T2/CS/42/6375', 'Ordinary Diploma In Computer Science', 'S06', NULL, '2025-05-17 16:57:23', 0),
(476, 'Raphael John Kundy', '23050512131', '7', 'T2/CS/42/7088', 'Ordinary Diploma In Computer Science', 'UG07', NULL, '2025-05-17 16:57:23', 0),
(477, 'Roberto Richard Robinson', '23050512134', '7', 'T2/CS/42/2554', 'Ordinary Diploma In Computer Science', 'UG07', NULL, '2025-05-17 16:57:23', 0),
(478, 'Shamsa Salehe Umudy', '23050502014', '7', 'T2/CS/42/5079', 'Ordinary Diploma In Computer Science', 'UF01', NULL, '2025-05-17 16:57:23', 0),
(479, 'Sheila Evarist Mushi', '22050512014', '7', 'T2/CS/42/6008', 'Ordinary Diploma In Computer Science', 'UF01', NULL, '2025-05-17 16:57:23', 0),
(480, 'Stephen Masangula Ntemo', '23050512146', '7', 'T2/CS/42/1866', 'Ordinary Diploma In Computer Science', 'UF01', NULL, '2025-05-17 16:57:23', 0),
(481, 'Tonny William Moshi', '23050512015', '7', 'T2/CS/42/9912', 'Ordinary Diploma In Computer Science', 'UF01', NULL, '2025-05-17 16:57:23', 0),
(482, 'Usama Tewa Hassan', '22050512128', '7', 'T2/CS/42/5882', 'Ordinary Diploma In Computer Science', 'UF01', NULL, '2025-05-17 16:57:23', 0),
(483, 'Valerian Fredrick Muganda', '23010102011', '7', 'T2/CS/42/7840', 'Ordinary Diploma In Computer Science', 'UF01', NULL, '2025-05-17 16:57:23', 0),
(484, 'Zuhura Johanness Mrashan', '23050512152', '7', 'T2/CS/42/6048', 'Ordinary Diploma In Computer Science', 'UF01', NULL, '2025-05-17 16:57:23', 0),
(485, 'Alfred Deusdedit Mrefu', '23050512024', '7', 'T2/CS/42/7039', 'Ordinary Diploma In Computer Science', 'S07', NULL, '2025-05-17 16:57:23', 0),
(486, 'Benson Juma Mkubwa', '23050512154', '7', 'T2/CS/42/5349', 'Ordinary Diploma In Computer Science', 'S07', NULL, '2025-05-17 16:57:23', 0),
(487, 'Ceslia Nikas Urio', '23050512043', '7', 'T2/CS/42/4054', 'Ordinary Diploma In Computer Science', 'S07', NULL, '2025-05-17 16:57:23', 0),
(488, 'Christopher Werema Matiko', '23050512155', '7', 'T2/CS/42/6598', 'Ordinary Diploma In Computer Science', 'T11', NULL, '2025-05-17 16:57:23', 0),
(489, 'Maisam Murjan Mwinyi', '23050512011', '7', 'T2/CS/42/1324', 'Ordinary Diploma In Computer Science', 'T11', NULL, '2025-05-17 16:57:23', 0),
(490, 'Abdulrahman Said Bakari', '23050512021', '7', 'T2/CF/42/5521', 'Ordinary Diploma In Cyber Security And Digital Forensic', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(491, 'Erick Gasper Alfonce', '23051012136', '4', 'T2/CF/42/8939', 'Ordinary Diploma In Cyber Security And Digital Forensic', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(492, 'Esau Nehemia Magaro', '23050512069', '4', 'T2/CF/42/1400', 'Ordinary Diploma In Cyber Security And Digital Forensic', 'S06', NULL, '2025-05-17 16:57:23', 0),
(493, 'Lolafrola James Elikana', '23051002008', '4', 'T2/CF/42/9195', 'Ordinary Diploma In Cyber Security And Digital Forensic', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(494, 'Ngairo Richard emmanuel', '23050512120', '4', 'T2/CF/42/3478', 'Ordinary Diploma In Cyber Security And Digital Forensic', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(495, 'Ridhiwan Mussa Tibesigwa', '23010102009', '4', 'T2/CF/42/7946', 'Ordinary Diploma In Cyber Security And Digital Forensic', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(496, 'Stela Ng`wandu Mataifa', '23052712008', '4', 'T2/CF/42/5265', 'Ordinary Diploma In Cyber Security And Digital Forensic', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(497, 'Vincent Simoni Massawe', '23050512150', '4', 'T2/CF/42/7161', 'Ordinary Diploma In Cyber Security And Digital Forensic', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(498, 'Abdallah Mashaka Kolloh', '23052712001', '4', 'T2/CF/42/6367', 'Ordinary Diploma In Cyber Security And Digital Forensic', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(499, 'Abdulatif Abubakari Umbe', '23050512020', '8', 'T2/EB/42/5538', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(500, 'Agness Ombeni Mavoa', '23030612006', '8', 'T2/EB/42/1960', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(501, 'Allan Christopher Diu', '23030612008', '8', 'T2/EB/42/9708', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(502, 'Allen Emmanuel Sengati', '23030602001', '8', 'T2/EB/42/3081', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(503, 'Amos Chacha Magori', '23030612014', '8', 'T2/EB/42/9410', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(504, 'Ananias Deus Bonephace', '23030612016', '8', 'T2/EB/42/2205', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(505, 'Anna S Ung`eng`e', '23030612021', '8', 'T2/EB/42/7714', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(506, 'Aristarick Meleckisedeck Mwano', '23030612022', '8', 'T2/EB/42/6601', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(507, 'Atupiye Taifa Mbazah', '23030612024', '8', 'T2/EB/42/1731', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(508, 'Bahati Lazaro Sibone', '23030612027', '8', 'T2/EB/42/9524', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(509, 'Baraka Ezekia Lwila', '23010202002', '8', 'T2/EB/42/7419', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(510, 'Baraka Mwita Wambura', '23030612030', '8', 'T2/EB/42/7421', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(511, 'Barring Patrick Chonjo', '23030602004', '8', 'T2/EB/42/4849', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(512, 'Beatrice Noah Nkosya', '23030612031', '8', 'T2/EB/42/6094', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(513, 'Benedict Kahashi Mgoli', '23041112012', '8', 'T2/EB/42/6338', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'DH', NULL, '2025-05-17 16:57:23', 0),
(514, 'Benedictor Rehemael Palangyo', '23030612032', '8', 'T2/EB/42/4159', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(515, 'Benny Owden Mwaikema', '23062012003', '8', 'T2/EB/42/2820', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(516, 'Brian Denis Mkenda', '23030612035', '8', 'T2/EB/42/6637', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(517, 'Catheline Bunzari Kulwa', '23041112014', '8', 'T2/EB/42/9230', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(518, 'Christian Jadeson Mawole', '22030612129', '8', 'T2/EB/42/3596', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'R12/13', NULL, '2025-05-17 16:57:23', 0),
(519, 'Consesa Viatory Vianey', '23020402004', '8', 'T2/EB/42/1973', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(520, 'Daniel Museru tito', '22030612087', '8', 'T2/EB/42/9519', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(521, 'Daniel Meshack Siara', '23030712041', '8', 'T2/EB/42/1846', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(522, 'Darison Johansen Tibaingana', '23030612045', '8', 'T2/EB/42/4220', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(523, 'Datius Rugaibura Audax', '23030612046', '8', 'T2/EB/42/8940', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(524, 'Daudi Ngusa Makoye', '23041112018', '8', 'T2/EB/42/8677', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(525, 'Daudi Prosper Towilo', '23030612047', '8', 'T2/EB/42/1908', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(526, 'Davidi Samweli Sasita', '23030612051', '8', 'T2/EB/42/2105', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(527, 'Dawson Sebastian Malunde', '23030612052', '8', 'T2/EB/42/5007', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(528, 'Debora Edson Mlay', '23030612054', '8', 'T2/EB/42/6172', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'US02', NULL, '2025-05-17 16:57:23', 0),
(529, 'Denis Dinno Wella', '23030612055', '8', 'T2/EB/42/6480', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(530, 'Deograthias Mazemule Sangera', '23030612056', '8', 'T2/EB/42/5051', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(531, 'Derick Mugewa fununu', '23030612059', '8', 'T2/EB/42/7373', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0),
(532, 'Derick Dominic Mpelasoka', '23030612057', '8', 'T2/EB/42/1694', 'Ordinary Diploma In Electrical And Biomedical Engineering', 'H/WAY', NULL, '2025-05-17 16:57:23', 0);

-- --------------------------------------------------------

--
-- Table structure for table `student_venues`
--

CREATE TABLE `student_venues` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `venue_id` int(11) NOT NULL,
  `assignment_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `student_venues`:
--   `student_id`
--       `students` -> `id`
--   `venue_id`
--       `venues` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `venues`
--

CREATE TABLE `venues` (
  `id` int(11) NOT NULL,
  `venue_name` varchar(50) NOT NULL,
  `capacity` int(11) NOT NULL,
  `assigned_students` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `venues`:
--

--
-- Dumping data for table `venues`
--

INSERT INTO `venues` (`id`, `venue_name`, `capacity`, `assigned_students`, `created_at`) VALUES
(1, 'R12/13', 150, 41, '2025-05-17 16:35:14'),
(2, 'US02', 120, 19, '2025-05-17 16:35:14'),
(3, 'DH', 90, 34, '2025-05-17 16:35:14'),
(4, 'G11', 180, 15, '2025-05-17 16:35:14'),
(5, 'S10', 75, 13, '2025-05-17 16:35:14'),
(6, 'F12', 140, 18, '2025-05-17 16:35:14'),
(7, 'H/WAY', 50, 34, '2025-05-17 16:35:14'),
(8, 'T10', 130, 19, '2025-05-17 16:35:14'),
(9, 'T11', 170, 11, '2025-05-17 16:35:14'),
(10, 'S07', 60, 3, '2025-05-17 16:35:14'),
(11, 'S06', 200, 7, '2025-05-17 16:35:14'),
(12, 'UG06', 80, 20, '2025-05-17 16:35:14'),
(13, 'UG07', 110, 18, '2025-05-17 16:35:14'),
(14, 'UF05', 100, 0, '2025-05-17 16:35:14'),
(15, 'UF01', 95, 7, '2025-05-17 16:35:14'),
(16, 'T12', 160, 7, '2025-05-17 16:35:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`exam_id`);

--
-- Indexes for table `incidents`
--
ALTER TABLE `incidents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `fk_exam` (`exam_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admission_no` (`admission_no`);

--
-- Indexes for table `student_venues`
--
ALTER TABLE `student_venues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `venue_id` (`venue_id`);

--
-- Indexes for table `venues`
--
ALTER TABLE `venues`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `venue_name` (`venue_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=533;

--
-- AUTO_INCREMENT for table `student_venues`
--
ALTER TABLE `student_venues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `venues`
--
ALTER TABLE `venues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=305;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`) ON DELETE SET NULL;

--
-- Constraints for table `student_venues`
--
ALTER TABLE `student_venues`
  ADD CONSTRAINT `student_venues_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `student_venues_ibfk_2` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
