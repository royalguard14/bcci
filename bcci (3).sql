-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2024 at 07:14 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bcci`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_year`
--

CREATE TABLE `academic_year` (
  `id` int(11) NOT NULL,
  `start` year(4) NOT NULL,
  `end` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `academic_year`
--

INSERT INTO `academic_year` (`id`, `start`, `end`) VALUES
(1, 2024, 2025);

-- --------------------------------------------------------

--
-- Table structure for table `attendance_records`
--

CREATE TABLE `attendance_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `eh_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('P','A','T','E') NOT NULL COMMENT 'P = Present, A = Absent, T = Tardy, E = Excused',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `campus_info`
--

CREATE TABLE `campus_info` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `function` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `campus_info`
--

INSERT INTO `campus_info` (`id`, `name`, `function`) VALUES
(1, 'Website Title', 'Zear Developer'),
(2, 'Campus Name', 'Butuan City Collage Inc'),
(3, 'Operating Day', 'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'),
(4, 'Operating Time', '6:00AM-9:00PM'),
(5, 'Academic Year', '1'),
(6, 'Institutional Email', 'bxucity.edu.ph');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `course_name` varchar(50) NOT NULL,
  `code` varchar(10) NOT NULL,
  `room_ids` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `course_name`, `code`, `room_ids`) VALUES
(1, 'Bachelor of Science in Business Administration', 'BSBA', '1,2,4'),
(2, 'Bachelor of Science in Criminology', 'BSCrim', '3'),
(3, 'Bachelor of Science in Education', 'BSED', ''),
(4, 'Bachelor of Science in Information Technology', 'BSIT', '');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_history`
--

CREATE TABLE `enrollment_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `grade_level_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `adviser_id` int(11) NOT NULL,
  `academic_year_id` int(11) DEFAULT NULL,
  `enrollment_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `grade_records`
--

CREATE TABLE `grade_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `eh_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `grading_id` int(11) NOT NULL,
  `grade` decimal(5,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `permission_name`) VALUES
(10, 'Access Logs'),
(4, 'Manage Courses'),
(7, 'Manage Documents'),
(5, 'Manage Enrollment'),
(6, 'Manage Grades'),
(8, 'Manage Payments'),
(2, 'Manage Permissions'),
(1, 'Manage Roles'),
(3, 'Manage Users'),
(13, 'Student Access'),
(14, 'System Management'),
(9, 'View Reports');

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `id` int(11) NOT NULL,
  `photo_path` varchar(500) DEFAULT NULL,
  `profile_id` int(11) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `sex` enum('M','F','') DEFAULT NULL,
  `birth_date` date NOT NULL,
  `house_street_sitio_purok` varchar(255) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `municipality_city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `photo_path`, `profile_id`, `last_name`, `first_name`, `middle_name`, `sex`, `birth_date`, `house_street_sitio_purok`, `barangay`, `municipality_city`, `province`, `contact_number`, `created_at`) VALUES
(1, 'assets/documents/38296576/case.png', 272, 'Bautista', 'Ghaizar', 'Atara', '', '1993-10-13', 'Purok 3 Upper', 'Doongan', 'Butuan', 'Agusan Del Norte', '09277294457', '2024-12-07 15:44:38'),
(2, 'assets/documents/41915834/case.png', 273, 'Bautista', 'Ghaizar', 'Atara', '', '1996-10-13', 'Purok 3 Upper', 'Doongan', 'No Data', 'No Data', '09277294457', '2024-12-07 15:46:13'),
(3, 'assets/documents/64068633/class diagram.drawio.png', 274, 'Bautista', 'Ghaizar', 'Atara', '', '3131-10-11', 'PUROK 3 UPPER DOONGAN BUTUAN CITY', 'No Data', 'Butuan', 'Agusan Del Norte', '09277294457', '2024-12-07 15:56:53'),
(4, 'assets/documents/70776329/case.png', 275, 'Bautista', 'Ghaizar', 'Atara', '', '2024-12-31', 'Purok 3 Upper', 'asd', 'Butuan', 'Agusan Del Norte', '09277294457', '2024-12-07 16:12:12'),
(5, 'assets/documents/84730196/case.png', 276, 'Bautista', 'Ghaizar', 'Atara', '', '1993-10-13', 'Purok 3 Upper', 'Doongan', 'Butuan', 'Agusan Del Norte', '09277294457', '2024-12-07 16:12:59'),
(6, 'assets/documents/74394606/case.png', 277, 'Bautista', 'Ghaizar', 'Atara', '', '1993-10-13', 'Purok 3 Upper', 'Doongan', 'Butuan', 'Agusan Del Norte', '09277294457', '2024-12-07 16:15:21'),
(7, 'assets/documents/25875077/case.png', 278, 'Bautista', 'Ghaizar', 'Atara', '', '1993-10-13', 'Purok 3 Upper', 'Doongan', 'Butuan', 'Agusan Del Norte', '09277294457', '2024-12-07 16:27:10');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `permission_id` varchar(600) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `permission_id`) VALUES
(1, 'Zear Developer', '10,4,7,5,6,8,2,1,3,14,9'),
(2, 'Registrar', '7,5,9'),
(3, 'Professor', '6,9'),
(4, 'Student', '13'),
(5, 'Accounting Staff', '8,9'),
(6, 'Auditor', '10,9'),
(7, 'Dean', '');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `type` enum('Room','Laboratory') NOT NULL,
  `location` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `type`, `location`) VALUES
(1, 'Room', 101),
(2, 'Room', 102),
(3, 'Laboratory', 1),
(4, 'Laboratory', 2);

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `academic_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `day` varchar(20) NOT NULL,
  `time_slot` varchar(20) NOT NULL,
  `session_type` enum('Lecture','Lab') NOT NULL,
  `adviser` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `academic_id`, `course_id`, `semester`, `subject_id`, `day`, `time_slot`, `session_type`, `adviser`) VALUES
(1, 1, 1, '1', 1, 'Friday', '06:00PM-07:30PM', 'Lecture', NULL),
(2, 1, 1, '1', 1, 'Thursday', '10:30AM-12:00PM', 'Lecture', NULL),
(3, 1, 1, '1', 2, 'Thursday', '03:00PM-04:30PM', 'Lecture', NULL),
(4, 1, 1, '1', 2, 'Thursday', '01:30PM-03:00PM', 'Lecture', NULL),
(5, 1, 1, '1', 3, 'Thursday', '07:30AM-09:00AM', 'Lecture', NULL),
(6, 1, 1, '1', 3, 'Tuesday', '04:30PM-06:00PM', 'Lecture', NULL),
(7, 1, 1, '1', 4, 'Monday', '10:30AM-12:00PM', 'Lecture', NULL),
(8, 1, 1, '1', 4, 'Monday', '04:30PM-06:00PM', 'Lecture', NULL),
(9, 1, 1, '1', 5, 'Wednesday', '09:00AM-10:30AM', 'Lecture', NULL),
(10, 1, 1, '1', 5, 'Wednesday', '04:30PM-06:00PM', 'Lecture', NULL),
(11, 1, 1, '1', 34, 'Saturday', '12:00PM-01:30PM', 'Lecture', NULL),
(12, 1, 1, '1', 34, 'Tuesday', '09:00AM-10:30AM', 'Lecture', NULL),
(13, 1, 1, '1', 38, 'Wednesday', '06:00AM-07:30AM', 'Lecture', NULL),
(14, 1, 1, '1', 38, 'Wednesday', '01:30PM-03:00PM', 'Lecture', NULL),
(15, 1, 1, '1', 132, 'Saturday', '09:00AM-10:30AM', 'Lecture', NULL),
(16, 1, 1, '1', 132, 'Friday', '06:00AM-07:30AM', 'Lecture', NULL),
(17, 1, 1, '2', 6, 'Thursday', '06:00AM-07:30AM', 'Lecture', NULL),
(18, 1, 1, '2', 6, 'Saturday', '12:00PM-01:30PM', 'Lecture', NULL),
(19, 1, 1, '2', 7, 'Tuesday', '04:30PM-06:00PM', 'Lecture', NULL),
(20, 1, 1, '2', 7, 'Saturday', '07:30AM-09:00AM', 'Lecture', NULL),
(21, 1, 1, '2', 8, 'Thursday', '07:30AM-09:00AM', 'Lecture', NULL),
(22, 1, 1, '2', 8, 'Friday', '01:30PM-03:00PM', 'Lecture', NULL),
(23, 1, 1, '2', 9, 'Friday', '07:30AM-09:00AM', 'Lecture', NULL),
(24, 1, 1, '2', 9, 'Thursday', '03:00PM-04:30PM', 'Lecture', NULL),
(25, 1, 1, '2', 10, 'Friday', '10:30AM-12:00PM', 'Lecture', NULL),
(26, 1, 1, '2', 10, 'Wednesday', '03:00PM-04:30PM', 'Lecture', NULL),
(27, 1, 1, '2', 11, 'Monday', '04:30PM-06:00PM', 'Lecture', NULL),
(28, 1, 1, '2', 11, 'Wednesday', '07:30PM-09:00PM', 'Lecture', NULL),
(29, 1, 1, '2', 12, 'Tuesday', '12:00PM-01:30PM', 'Lecture', NULL),
(30, 1, 1, '2', 12, 'Monday', '07:30PM-09:00PM', 'Lecture', NULL),
(31, 1, 1, '3', 14, 'Tuesday', '04:30PM-06:00PM', 'Lecture', NULL),
(32, 1, 1, '3', 14, 'Wednesday', '01:30PM-03:00PM', 'Lecture', NULL),
(33, 1, 1, '3', 15, 'Friday', '07:30AM-09:00AM', 'Lecture', NULL),
(34, 1, 1, '3', 15, 'Friday', '10:30AM-12:00PM', 'Lecture', NULL),
(35, 1, 1, '3', 16, 'Saturday', '03:00PM-04:30PM', 'Lecture', NULL),
(36, 1, 1, '3', 16, 'Monday', '04:30PM-06:00PM', 'Lecture', NULL),
(37, 1, 1, '3', 17, 'Tuesday', '06:00PM-07:30PM', 'Lecture', NULL),
(38, 1, 1, '3', 17, 'Tuesday', '01:30PM-03:00PM', 'Lecture', NULL),
(39, 1, 1, '3', 18, 'Friday', '12:00PM-01:30PM', 'Lecture', NULL),
(40, 1, 1, '3', 18, 'Thursday', '09:00AM-10:30AM', 'Lecture', NULL),
(41, 1, 1, '3', 19, 'Thursday', '01:30PM-03:00PM', 'Lecture', NULL),
(42, 1, 1, '3', 19, 'Monday', '06:00PM-07:30PM', 'Lecture', NULL),
(43, 1, 1, '3', 20, 'Thursday', '07:30PM-09:00PM', 'Lecture', NULL),
(44, 1, 1, '3', 20, 'Monday', '09:00AM-10:30AM', 'Lecture', NULL),
(45, 1, 1, '4', 21, 'Friday', '07:30PM-09:00PM', 'Lecture', NULL),
(46, 1, 1, '4', 21, 'Tuesday', '09:00AM-10:30AM', 'Lecture', NULL),
(47, 1, 1, '4', 22, 'Friday', '01:30PM-03:00PM', 'Lecture', NULL),
(48, 1, 1, '4', 22, 'Tuesday', '07:30AM-09:00AM', 'Lecture', NULL),
(49, 1, 1, '4', 23, 'Friday', '06:00AM-07:30AM', 'Lecture', NULL),
(50, 1, 1, '4', 23, 'Friday', '03:00PM-04:30PM', 'Lecture', NULL),
(51, 1, 1, '4', 24, 'Tuesday', '07:30PM-09:00PM', 'Lecture', NULL),
(52, 1, 1, '4', 24, 'Friday', '04:30PM-06:00PM', 'Lecture', NULL),
(53, 1, 1, '4', 25, 'Wednesday', '10:30AM-12:00PM', 'Lecture', NULL),
(54, 1, 1, '4', 25, 'Friday', '06:00PM-07:30PM', 'Lecture', NULL),
(55, 1, 2, '1', 1, 'Thursday', '06:00AM-07:30AM', 'Lecture', NULL),
(56, 1, 2, '1', 1, 'Monday', '01:30PM-03:00PM', 'Lecture', NULL),
(57, 1, 2, '1', 2, 'Monday', '07:30PM-09:00PM', 'Lecture', NULL),
(58, 1, 2, '1', 2, 'Monday', '10:30AM-12:00PM', 'Lecture', NULL),
(59, 1, 2, '1', 3, 'Wednesday', '12:00PM-01:30PM', 'Lecture', NULL),
(60, 1, 2, '1', 3, 'Friday', '06:00AM-07:30AM', 'Lecture', NULL),
(61, 1, 2, '1', 4, 'Monday', '06:00AM-07:30AM', 'Lecture', NULL),
(62, 1, 2, '1', 4, 'Tuesday', '06:00AM-07:30AM', 'Lecture', NULL),
(63, 1, 2, '1', 34, 'Tuesday', '07:30AM-09:00AM', 'Lecture', NULL),
(64, 1, 2, '1', 34, 'Friday', '07:30PM-09:00PM', 'Lecture', NULL),
(65, 1, 2, '1', 35, 'Friday', '04:30PM-06:00PM', 'Lecture', NULL),
(66, 1, 2, '1', 35, 'Wednesday', '07:30PM-09:00PM', 'Lecture', NULL),
(67, 1, 2, '1', 36, 'Saturday', '07:30AM-09:00AM', 'Lecture', NULL),
(68, 1, 2, '1', 36, 'Monday', '07:30AM-09:00AM', 'Lecture', NULL),
(69, 1, 2, '1', 37, 'Tuesday', '09:00AM-10:30AM', 'Lecture', NULL),
(70, 1, 2, '1', 37, 'Thursday', '06:00PM-07:30PM', 'Lecture', NULL),
(71, 1, 2, '1', 38, 'Friday', '12:00PM-01:30PM', 'Lecture', NULL),
(72, 1, 2, '1', 38, 'Saturday', '06:00AM-07:30AM', 'Lecture', NULL),
(73, 1, 2, '1', 132, 'Wednesday', '01:30PM-03:00PM', 'Lecture', NULL),
(74, 1, 2, '1', 132, 'Saturday', '10:30AM-12:00PM', 'Lecture', NULL),
(75, 1, 2, '2', 6, 'Saturday', '07:30PM-09:00PM', 'Lecture', NULL),
(76, 1, 2, '2', 6, 'Monday', '07:30AM-09:00AM', 'Lecture', NULL),
(77, 1, 2, '2', 7, 'Monday', '07:30PM-09:00PM', 'Lecture', NULL),
(78, 1, 2, '2', 7, 'Tuesday', '07:30AM-09:00AM', 'Lecture', NULL),
(79, 1, 2, '2', 8, 'Friday', '07:30PM-09:00PM', 'Lecture', NULL),
(80, 1, 2, '2', 8, 'Tuesday', '03:00PM-04:30PM', 'Lecture', NULL),
(81, 1, 2, '2', 10, 'Thursday', '06:00PM-07:30PM', 'Lecture', NULL),
(82, 1, 2, '2', 10, 'Thursday', '04:30PM-06:00PM', 'Lecture', NULL),
(83, 1, 2, '2', 12, 'Wednesday', '12:00PM-01:30PM', 'Lecture', NULL),
(84, 1, 2, '2', 12, 'Monday', '06:00PM-07:30PM', 'Lecture', NULL),
(85, 1, 2, '2', 40, 'Thursday', '07:30AM-09:00AM', 'Lecture', NULL),
(86, 1, 2, '2', 40, 'Friday', '06:00AM-07:30AM', 'Lecture', NULL),
(87, 1, 2, '2', 41, 'Tuesday', '06:00AM-07:30AM', 'Lecture', NULL),
(88, 1, 2, '2', 41, 'Wednesday', '09:00AM-10:30AM', 'Lecture', NULL),
(89, 1, 1, '1', 1, 'Tuesday', '06:00PM-07:30PM', 'Lecture', NULL),
(90, 1, 1, '1', 1, 'Thursday', '06:00AM-07:30AM', 'Lecture', NULL),
(91, 1, 1, '1', 2, 'Friday', '06:00PM-07:30PM', 'Lecture', NULL),
(92, 1, 1, '1', 2, 'Wednesday', '10:30AM-12:00PM', 'Lecture', NULL),
(93, 1, 1, '1', 3, 'Wednesday', '06:00AM-07:30AM', 'Lecture', NULL),
(94, 1, 1, '1', 3, 'Tuesday', '09:00AM-10:30AM', 'Lecture', NULL),
(95, 1, 1, '1', 4, 'Wednesday', '07:30AM-09:00AM', 'Lecture', NULL),
(96, 1, 1, '1', 5, 'Tuesday', '07:30PM-09:00PM', 'Lecture', NULL),
(97, 1, 1, '1', 5, 'Saturday', '07:30AM-09:00AM', 'Lecture', NULL),
(98, 1, 1, '1', 34, 'Saturday', '03:00PM-04:30PM', 'Lecture', NULL),
(99, 1, 1, '1', 34, 'Wednesday', '04:30PM-06:00PM', 'Lecture', NULL),
(100, 1, 1, '1', 38, 'Friday', '09:00AM-10:30AM', 'Lecture', NULL),
(101, 1, 1, '1', 38, 'Tuesday', '12:00PM-01:30PM', 'Lecture', NULL),
(102, 1, 1, '1', 132, 'Monday', '12:00PM-01:30PM', 'Lecture', NULL),
(103, 1, 1, '1', 132, 'Saturday', '06:00PM-07:30PM', 'Lecture', NULL),
(104, 1, 1, '2', 6, 'Saturday', '01:30PM-03:00PM', 'Lecture', NULL),
(105, 1, 1, '2', 6, 'Friday', '10:30AM-12:00PM', 'Lecture', NULL),
(106, 1, 1, '2', 7, 'Friday', '09:00AM-10:30AM', 'Lecture', NULL),
(107, 1, 1, '2', 7, 'Wednesday', '10:30AM-12:00PM', 'Lecture', NULL),
(108, 1, 1, '2', 8, 'Friday', '04:30PM-06:00PM', 'Lecture', NULL),
(109, 1, 1, '2', 8, 'Tuesday', '06:00PM-07:30PM', 'Lecture', NULL),
(110, 1, 1, '2', 9, 'Wednesday', '07:30AM-09:00AM', 'Lecture', NULL),
(111, 1, 1, '2', 9, 'Tuesday', '12:00PM-01:30PM', 'Lecture', NULL),
(112, 1, 1, '2', 10, 'Thursday', '10:30AM-12:00PM', 'Lecture', NULL),
(113, 1, 1, '2', 10, 'Monday', '03:00PM-04:30PM', 'Lecture', NULL),
(114, 1, 1, '2', 11, 'Monday', '12:00PM-01:30PM', 'Lecture', NULL),
(115, 1, 1, '2', 11, 'Friday', '07:30PM-09:00PM', 'Lecture', NULL),
(116, 1, 1, '2', 12, 'Wednesday', '07:30PM-09:00PM', 'Lecture', NULL),
(117, 1, 1, '2', 12, 'Friday', '06:00AM-07:30AM', 'Lecture', NULL),
(118, 1, 1, '3', 14, 'Monday', '12:00PM-01:30PM', 'Lecture', NULL),
(119, 1, 1, '3', 14, 'Friday', '06:00AM-07:30AM', 'Lecture', NULL),
(120, 1, 1, '3', 15, 'Tuesday', '12:00PM-01:30PM', 'Lecture', NULL),
(121, 1, 1, '3', 15, 'Saturday', '12:00PM-01:30PM', 'Lecture', NULL),
(122, 1, 1, '3', 16, 'Monday', '06:00PM-07:30PM', 'Lecture', NULL),
(123, 1, 1, '3', 17, 'Thursday', '04:30PM-06:00PM', 'Lecture', NULL),
(124, 1, 1, '3', 17, 'Saturday', '09:00AM-10:30AM', 'Lecture', NULL),
(125, 1, 1, '3', 18, 'Saturday', '07:30AM-09:00AM', 'Lecture', NULL),
(126, 1, 1, '3', 18, 'Friday', '01:30PM-03:00PM', 'Lecture', NULL),
(127, 1, 1, '3', 19, 'Wednesday', '06:00AM-07:30AM', 'Lecture', NULL),
(128, 1, 1, '3', 19, 'Tuesday', '06:00AM-07:30AM', 'Lecture', NULL),
(129, 1, 1, '3', 20, 'Thursday', '06:00AM-07:30AM', 'Lecture', NULL),
(130, 1, 1, '3', 20, 'Friday', '12:00PM-01:30PM', 'Lecture', NULL),
(131, 1, 1, '4', 21, 'Friday', '03:00PM-04:30PM', 'Lecture', NULL),
(132, 1, 1, '4', 21, 'Monday', '07:30PM-09:00PM', 'Lecture', NULL),
(133, 1, 1, '4', 22, 'Thursday', '09:00AM-10:30AM', 'Lecture', NULL),
(134, 1, 1, '4', 22, 'Monday', '07:30AM-09:00AM', 'Lecture', NULL),
(135, 1, 1, '4', 23, 'Friday', '07:30AM-09:00AM', 'Lecture', NULL),
(136, 1, 1, '4', 23, 'Saturday', '07:30PM-09:00PM', 'Lecture', NULL),
(137, 1, 1, '4', 24, 'Thursday', '06:00AM-07:30AM', 'Lecture', NULL),
(138, 1, 1, '4', 24, 'Saturday', '01:30PM-03:00PM', 'Lecture', NULL),
(139, 1, 1, '4', 25, 'Tuesday', '04:30PM-06:00PM', 'Lecture', NULL),
(140, 1, 1, '4', 25, 'Saturday', '03:00PM-04:30PM', 'Lecture', NULL),
(141, 1, 2, '1', 1, 'Tuesday', '06:00AM-07:30AM', 'Lecture', NULL),
(142, 1, 2, '1', 1, 'Monday', '06:00AM-07:30AM', 'Lecture', NULL),
(143, 1, 2, '1', 2, 'Tuesday', '06:00PM-07:30PM', 'Lecture', NULL),
(144, 1, 2, '1', 2, 'Saturday', '07:30AM-09:00AM', 'Lecture', NULL),
(145, 1, 2, '1', 3, 'Tuesday', '04:30PM-06:00PM', 'Lecture', NULL),
(146, 1, 2, '1', 3, 'Tuesday', '09:00AM-10:30AM', 'Lecture', NULL),
(147, 1, 2, '1', 4, 'Monday', '09:00AM-10:30AM', 'Lecture', NULL),
(148, 1, 2, '1', 4, 'Monday', '03:00PM-04:30PM', 'Lecture', NULL),
(149, 1, 2, '1', 34, 'Friday', '01:30PM-03:00PM', 'Lecture', NULL),
(150, 1, 2, '1', 34, 'Saturday', '10:30AM-12:00PM', 'Lecture', NULL),
(151, 1, 2, '1', 35, 'Thursday', '12:00PM-01:30PM', 'Lecture', NULL),
(152, 1, 2, '1', 35, 'Saturday', '07:30PM-09:00PM', 'Lecture', NULL),
(153, 1, 2, '1', 36, 'Wednesday', '10:30AM-12:00PM', 'Lecture', NULL),
(154, 1, 2, '1', 36, 'Thursday', '04:30PM-06:00PM', 'Lecture', NULL),
(155, 1, 2, '1', 37, 'Monday', '01:30PM-03:00PM', 'Lecture', NULL),
(156, 1, 2, '1', 37, 'Saturday', '06:00AM-07:30AM', 'Lecture', NULL),
(157, 1, 2, '1', 38, 'Thursday', '03:00PM-04:30PM', 'Lecture', NULL),
(158, 1, 2, '1', 38, 'Saturday', '01:30PM-03:00PM', 'Lecture', NULL),
(159, 1, 2, '1', 132, 'Tuesday', '07:30AM-09:00AM', 'Lecture', NULL),
(160, 1, 2, '1', 132, 'Wednesday', '06:00AM-07:30AM', 'Lecture', NULL),
(161, 1, 2, '2', 6, 'Thursday', '01:30PM-03:00PM', 'Lecture', NULL),
(162, 1, 2, '2', 6, 'Friday', '10:30AM-12:00PM', 'Lecture', NULL),
(163, 1, 2, '2', 7, 'Monday', '06:00PM-07:30PM', 'Lecture', NULL),
(164, 1, 2, '2', 7, 'Saturday', '12:00PM-01:30PM', 'Lecture', NULL),
(165, 1, 2, '2', 8, 'Wednesday', '04:30PM-06:00PM', 'Lecture', NULL),
(166, 1, 2, '2', 8, 'Saturday', '03:00PM-04:30PM', 'Lecture', NULL),
(167, 1, 2, '2', 10, 'Thursday', '03:00PM-04:30PM', 'Lecture', NULL),
(168, 1, 2, '2', 10, 'Wednesday', '01:30PM-03:00PM', 'Lecture', NULL),
(169, 1, 2, '2', 12, 'Saturday', '07:30AM-09:00AM', 'Lecture', NULL),
(170, 1, 2, '2', 12, 'Monday', '10:30AM-12:00PM', 'Lecture', NULL),
(171, 1, 2, '2', 40, 'Friday', '01:30PM-03:00PM', 'Lecture', NULL),
(172, 1, 2, '2', 40, 'Saturday', '06:00AM-07:30AM', 'Lecture', NULL),
(173, 1, 2, '2', 41, 'Saturday', '07:30PM-09:00PM', 'Lecture', NULL),
(174, 1, 2, '2', 41, 'Friday', '07:30AM-09:00AM', 'Lecture', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `semester`
--

CREATE TABLE `semester` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `subject_ids` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `semester`
--

INSERT INTO `semester` (`id`, `course_id`, `semester`, `subject_ids`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '1,2,3,4,5,34,38,132', '2024-12-09 02:18:31', '2024-12-09 02:20:09'),
(2, 1, 2, '6,7,8,9,10,11,12', '2024-12-09 02:18:31', '2024-12-09 02:20:55'),
(3, 1, 3, '14,15,16,17,18,19,20', '2024-12-09 02:18:31', '2024-12-09 02:22:32'),
(4, 1, 4, '21,22,23,24,25', '2024-12-09 02:18:31', '2024-12-09 02:24:31'),
(5, 1, 5, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(6, 1, 6, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(7, 1, 7, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(8, 1, 8, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(9, 2, 1, '1,2,3,4,34,35,36,37,38,132', '2024-12-09 02:18:31', '2024-12-09 23:55:42'),
(10, 2, 2, '6,7,8,10,12,40,41', '2024-12-09 02:18:31', '2024-12-09 23:56:57'),
(11, 2, 3, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(12, 2, 4, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(13, 2, 5, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(14, 2, 6, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(15, 2, 7, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(16, 2, 8, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(17, 3, 1, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(18, 3, 2, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(19, 3, 3, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(20, 3, 4, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(21, 3, 5, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(22, 3, 6, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(23, 3, 7, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(24, 3, 8, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(25, 4, 1, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(26, 4, 2, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(27, 4, 3, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(28, 4, 4, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(29, 4, 5, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(30, 4, 6, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(31, 4, 7, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31'),
(32, 4, 8, '', '2024-12-09 02:18:31', '2024-12-09 02:18:31');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `code` varchar(10) NOT NULL,
  `unit_lec` int(11) DEFAULT NULL,
  `unit_lab` int(11) DEFAULT NULL,
  `pre_req` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `description`, `code`, `unit_lec`, `unit_lab`, `pre_req`) VALUES
(1, 'Understanding the Self', '', 'GE 1', 3, 0, ''),
(2, 'Reading in the Philippine History', '', 'GE 2', 3, 0, ''),
(3, 'The Contemporary World', '', 'GE 3', 3, 0, ''),
(4, 'Ang Kulikulum ng Filipino sa Batayang Antas ng Edukasyon', '', 'FIL 1', 3, 0, ''),
(5, 'PATHFit 1: Movement Competency Training', '', 'PE 1', 3, 0, ''),
(6, 'Purposive Communication', '', 'GE 5', 3, 0, ''),
(7, 'Art Appreciation', '', 'GE 6', 3, 0, ''),
(8, 'Science, Technology & Society', '', 'GE 7', 3, 0, ''),
(9, 'Ethics', '', 'GE 8', 3, 0, ''),
(10, 'Sanaysay at Talumpati', '', 'FIL 2', 2, 0, ''),
(11, 'PATHFit 2: Exercise based Fitness Activities', '', 'PE 2', 3, 0, ''),
(12, 'Civic Welfare Training Service 2', '', 'NSTP 2', 3, 0, ''),
(13, 'Health Care and Disaster Preparedness', '', 'HCCB 100', 3, 0, ''),
(14, 'Rizal\'s Life & Works', '', 'GE 9', 3, 0, ''),
(15, 'Masining na Pagpapahayag', '', 'FIL 3', 3, 0, ''),
(16, 'Mathematics, Science & Philosophy', '', 'GE Electiv', 3, 0, ''),
(17, 'Social Science & Philosphy', '', 'GE electiv', 3, 0, ''),
(18, 'Basic Microeconomics (ECO)', '', 'BA CC 1', 3, 0, ''),
(19, 'Basic Macroeconimoics', '', 'BUS ECON 1', 3, 0, ''),
(20, 'PATHFit 3: Dance', '', 'PE 3', 2, 0, ''),
(21, 'Arts & Humanities', '', 'GE Electiv', 3, 0, ''),
(22, 'Business Law (Obligation & Contracts)', '', 'BA CC 2', 3, 0, ''),
(23, 'Taxation(Income Taxation)', '', 'BA CC 3', 3, 0, ''),
(24, 'History of Economic Thought', '', 'BUS ECON 2', 3, 0, ''),
(25, 'Advance Microeconomics', '', 'BUS ECON 3', 3, 0, ''),
(26, 'PATHFit 4: Sports', '', 'PE 4', 2, 0, ''),
(27, 'Philippine Literature and Literature of Mindanao', '', 'LIT 1', 3, 0, ''),
(28, 'Strategic Management', '', 'CBMEC 1', 3, 0, ''),
(29, 'Good Governance % Social Responsibility', '', 'BA CC 4', 3, 0, ''),
(30, 'Human Resource Manangement ', '', 'BA CC 5', 3, 0, ''),
(31, 'International Trade & Agreements', '', 'BA CC 6', 3, 0, ''),
(32, 'Advance Macroeconomics', '', 'BUS ECON 4', 3, 0, ''),
(33, 'Managerial Economics', '', 'BUS ECON 5', 3, 0, ''),
(34, 'Mathematics in the Mdern World (Plane Trigonometry)', '', 'GE 4', 3, 0, ''),
(35, 'Environmental Science', '', 'EC 1', 3, 0, ''),
(36, 'Introduction to Criminology', '', 'Criminolog', 3, 0, ''),
(37, 'Fundamentals of Martial ARTS', '', 'PE 1', 2, 0, ''),
(38, 'The Institution and Life of Jesus', '', 'ReED 1', 3, 0, ''),
(39, 'Art Appreciation', '', 'GE6', 3, 0, ''),
(40, 'Introduction to Philippine Criminal Justice System', '', 'CLJ 1', 3, 0, ''),
(41, 'Arnis & Disarming Technique', '', 'PE 3', 2, 0, ''),
(42, 'Institutional Corrections', '', 'CA 1', 3, 0, ''),
(43, 'Forensic Photography', '', 'Forensic 1', 2, 1, ''),
(44, 'Character Formation, Nationalism & Patriotism', '', 'CFLM 1', 3, 0, ''),
(45, 'Specialized Crime Investigation 1 with Legal Medicine', '', 'CDI 2', 3, 0, ''),
(46, 'Personal Identifacation Techniques', '', 'Forensic 2', 2, 1, ''),
(47, 'General Chemistry ( Organic)', '', 'AdGE', 2, 1, ''),
(48, 'Human Behavior and Victimology', '', 'Criminolog', 3, 0, ''),
(49, 'Introduction to industruial Security Concepts', '', 'LEA 3', 3, 0, ''),
(50, 'Fundamental of Markmanship', '', 'PE 4', 2, 0, ''),
(51, 'Human Right Education', '', 'CLJ 2', 3, 0, ''),
(52, 'Character Formation w/ Leadership, Decision Making Manangement and Administration', '', 'CFLM 2', 3, 0, ''),
(53, 'Specialized Crime inv. 2 w/ Simulation on interrogation & interview', '', 'CDI 3', 3, 2, ''),
(54, 'Forensic Chemistry & Toxicology', '', 'Forensic 3', 3, 2, ''),
(55, 'Criminal Law (BOOK 1)', '', 'CLJ 3', 3, 0, ''),
(56, 'Law Enforcement Operations & Planning w/ Crime Mapping', '', 'LEA 4', 3, 0, ''),
(57, 'Traffic Management & Accident investigation w/ Driving', '', 'CDI 4', 3, 0, ''),
(58, 'Proffessional Conduct & Ethical Standards', '', 'Criminolog', 3, 0, ''),
(59, 'Criminological Research 1 ( Reseach Methods w/ Applied Statics)', '', 'Criminolog', 3, 2, ''),
(60, 'Non- Institutional Corrections', '', 'CA 2', 3, 0, ''),
(61, 'Criminal Law (Book 2)', '', 'CLJ 4', 4, 0, ''),
(62, 'Questioned Documents Examination', '', 'Forensic 4', 2, 1, ''),
(63, 'Juvenie Delinquency & Juvenie Justice System', '', 'Criminolog', 3, 0, ''),
(64, 'Lie Detection Techniques', '', 'Forensic 5', 2, 1, ''),
(65, 'Technical English 1 (Investigative Report writing & Presentation', '', 'CDI 5', 3, 0, ''),
(66, 'Fire Protection & Arson Investigation', '', 'CDI 6', 3, 0, ''),
(67, 'Criminological Research 2 ( Thesis Writing & Presentation)', '', 'Criminolog', 3, 0, ''),
(68, 'Institutional Enhancement Course 1 ( Criminal Jurisprudence & Procedure Law Enforcement Administration, Crime Detection & Investagation', '', 'IEC 1', 4, 0, ''),
(69, 'Forensic Balistics', '', 'Forensic 6', 2, 1, ''),
(70, 'Criminal Procedure & Court Testimony', '', 'CLJ 6', 3, 0, ''),
(71, 'Therapeutic Modalities', '', 'CA 3', 3, 0, ''),
(72, 'Evidence', '', 'CLJ 5', 3, 0, ''),
(73, 'Dispute Resoulution & Crisi/Incidents Management', '', 'Criminolog', 3, 0, ''),
(74, 'Vice and Drug Education & Control ', '', 'CDI 7', 3, 0, ''),
(75, 'Technical English 2 (Legal Forms)', '', 'CDI 8', 3, 0, ''),
(76, 'Intro to Cybercrime & Environmental Law & Protection', '', 'CDI 9 ', 3, 0, ''),
(77, 'Institutional Enhancement Course 2 ( Criminalistics, Correctional Administration, Criminal Sociology', '', 'IEC 2', 4, 1, ''),
(78, 'Intership (On the Job Training 540 hours', '', 'Criminolog', 6, 0, ''),
(79, 'Institutional Enhancement Course 3 Final Coaching & Post Test in 6 Board Areas', '', 'IEC 3', 4, 0, ''),
(80, 'The Child & Adolescent Learners & Learning Principle', '', 'EDUC 1', 3, 0, ''),
(81, 'The Teaching Profession', '', 'EDUC 5', 3, 0, ''),
(82, 'Assessment in Learning 1', '', 'EDUC 8', 3, 0, ''),
(83, 'Introduction to Linguistics', '', 'Linguistic', 3, 0, ''),
(84, 'Facilitating Learner- Centered Teaching', '', 'EDUC 4', 3, 0, ''),
(85, 'Assessment of Learning 2', '', 'EDUC 10', 3, 0, ''),
(86, 'Language, Culture and Society', '', 'Linguistic', 3, 0, ''),
(87, 'Mathematics in the Moderm World', '', 'GE4', 3, 0, ''),
(88, 'Principles & Theories of Language Acqustition and Learning', '', 'ELT 1', 3, 0, ''),
(89, 'Language Program & Inclusive Educ', '', 'ELT 2', 3, 0, ''),
(90, 'Foundation of Special & Inclusive Educ', '', 'EDUC 6', 3, 0, ''),
(91, 'Technology For Teaching & Leaning 1', '', 'EDUC 3', 3, 0, ''),
(92, 'Mathematics, Science & Technology', '', 'GE 10', 3, 0, ''),
(93, 'Social SCIENCE & philoshy', '', 'GE 11', 3, 0, ''),
(94, 'Institutional Enhancement Course in Education 1 ( English & Filipino', '', 'IECE 1', 2, 0, ''),
(95, 'Language Learning Materials Development', '', 'ELT 3', 3, 0, ''),
(96, 'Teaching & Assessment of Lit. Studies', '', 'ELT 4', 3, 0, ''),
(97, 'Teaching & Assessment of marcoskills', '', 'ELT 5', 3, 0, ''),
(98, 'Building & Enhancing New Literacies Across the Curriculum', '', 'EDUC 2', 3, 0, ''),
(99, 'Arts & Humanities', '', 'GE 12', 3, 0, ''),
(100, 'Institutional Enhancement Course in Education 2 ( Mathenatics & Science )', '', 'IECE 2', 2, 0, ''),
(101, 'Structure of English', '', 'Linguistic', 3, 0, ''),
(102, 'Teaching & Assessment of Grammar', '', 'ELT 6', 3, 0, ''),
(103, 'Speech & Theater ARTS', '', 'ELT 7', 3, 0, ''),
(104, 'Language Research', '', 'ELT 8', 3, 0, ''),
(105, 'Technical Writing', '', 'ELT 9 ', 3, 0, ''),
(106, 'Children & Adolescent Literature', '', 'Literature', 3, 0, ''),
(107, 'Mythology & folklore', '', 'Literature', 3, 0, ''),
(108, 'Survey of Phil, Literature in English', '', 'Literature', 3, 0, ''),
(109, 'The Teacher and the Community, School Culture & Organizational Leadership', '', 'EDUC 9', 3, 0, ''),
(110, 'Institutional Enhancement Course in Education 4 (professional Education)', '', 'IECE 3', 2, 0, ''),
(111, 'The Teacher & The School Curriculum', '', 'EDUC 7', 3, 0, ''),
(112, 'Survey of AfrO Asian Literature', '', 'Literature', 3, 0, ''),
(113, 'Contemporary & Popular & Emergent Lit', '', 'Literature', 3, 0, ''),
(114, 'Literary Criticism', '', 'Literature', 3, 0, ''),
(115, 'Campys Journalism', '', 'Literature', 3, 0, ''),
(116, 'Technology for Teaching & Learning 2- ( Technology in Language Education)', '', 'Allied 2', 3, 0, ''),
(117, 'Remedial instruction', '', 'Elective 1', 3, 0, ''),
(118, 'Creative Writing', '', 'Elective 2', 3, 0, ''),
(119, 'Institutional Enhancement Course in Education 4 ( Proffessional Educational)', '', 'IECE 4', 2, 0, ''),
(120, 'Observation of Teaching- Learning in Actual School Environment', '', 'FS 1', 3, 0, ''),
(121, 'Participation & Teaching Assistantship', '', 'FS 2', 3, 0, ''),
(122, 'Institutional Diagnostic Course in Gneral & Professional Education', '', 'IECE 5', 3, 0, ''),
(123, 'Teaching intemship', '', 'Educ 11', 6, 0, ''),
(124, 'Undestanding the Self', '', 'GE 101', 3, 0, ''),
(125, 'Mathematics in Modern World', '', 'GE 102', 3, 0, ''),
(126, 'Reading in Phil History', '', 'GE 103', 3, 0, ''),
(127, ' The Contemporary World', '', 'GE 104', 3, 0, ''),
(128, 'Introduction to Computing', '', 'IT 101', 2, 1, ''),
(129, 'Computer Programming 1', '', 'IT 102', 2, 1, ''),
(130, 'Speech and Oral Communication', '', 'IT Ins 101', 2, 1, ''),
(131, 'Movement Comptency Training', '', 'PATHFit 1', 2, 0, ''),
(132, 'Civic Welfare Training Services 1', '', 'NSPT 1', 3, 0, ''),
(134, 'Discrete Mathematics', '', 'IT 103', 3, 0, ''),
(135, 'Computer Programming 2', '', 'IT 104', 2, 1, ''),
(136, 'Intro to Human Computer Interaction', '', 'IT 105', 2, 2, ''),
(137, 'Entrepreneurial Mind', '', 'GE Elec 10', 3, 0, ''),
(138, 'Living in the IT Era', '', 'GE ELEC 10', 3, 0, ''),
(139, 'Exercise- base Fitnes Activities', '', 'PATHFit 2', 2, 0, ''),
(140, 'Science, Technology & Society', '', 'GE 106', 3, 0, ''),
(141, 'Reading Visual Arts', '', 'GE Elec 10', 3, 0, ''),
(142, 'Special Topics in Business Economics', '', 'IT Ins 102', 2, 1, ''),
(143, 'Data Structure and Algorithm', '', 'IT 106', 2, 1, ''),
(144, 'Object- Oriented Programming', '', 'IT ELEC 10', 2, 1, ''),
(145, 'Platform Tehcnologies', '', 'IT Elect 1', 2, 1, ''),
(146, 'Life and Teaching of Jesus', '', 'REED 1', 3, 0, ''),
(147, 'Dance', '', 'PATHFit 3', 2, 0, ''),
(148, 'Art Appreciation', '', 'GE 107', 3, 0, ''),
(149, 'Information Manangement 1', '', 'IT 107', 2, 1, ''),
(150, 'Quantitative Methods', '', 'IT 108', 2, 1, ''),
(151, 'Networking 1', '', 'IT 109', 2, 2, ''),
(152, 'Integrative Programming and Technologies 1', '', 'IT 110', 2, 1, ''),
(153, 'Rizal\'s Life and Works', '', 'Rizal ', 3, 0, ''),
(154, 'Sports', '', 'PATHFit 4', 2, 0, ''),
(155, 'ETHICS', '', 'GE 108', 3, 0, ''),
(156, 'Advance Database Management System', '', 'IT 111', 2, 1, ''),
(157, 'Networking 2', '', 'IT 112', 2, 2, ''),
(158, 'System Integration and Architecture1', '', 'IT 113', 2, 1, ''),
(159, 'Event Driven Programming', '', 'IT 114', 2, 1, ''),
(160, 'Web System & Technologies', '', 'IT Elec 10', 2, 2, ''),
(161, 'Instituitional Capston Project', '', 'IT Ins 103', 3, 0, ''),
(162, 'Information Assurance and Security 1', '', 'IT 115', 2, 2, ''),
(163, 'Social and Professional Issues', '', 'IT 116', 3, 0, ''),
(164, 'Application and Emerging Technologies', '', 'IT 117', 2, 2, ''),
(165, 'Capstone Project and Research 2', '', 'CAP 102', 0, 3, ''),
(166, 'PRATICUM (500 Hours)', '', 'PRAC101', 0, 3, ''),
(167, 'Theories of crime causation', '', 'Criminolog', 3, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `isActive` tinyint(1) DEFAULT 0,
  `isDelete` tinyint(1) DEFAULT 0,
  `profile_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `username`, `password`, `role_id`, `isActive`, `isDelete`, `profile_id`, `created_at`, `updated_at`) VALUES
(2, 'zhie@zear.developer.com', 'admin', '$2y$10$Y3A7u1B6/Fchy.twJAypLOhLmD1/KCYjy/BGce2P5jUOKThSTXD3u', 1, 1, 0, NULL, '2024-11-03 03:22:39', '2024-11-20 12:18:34'),
(266, '', 'registrar', '$2y$10$F3I24OvhzcXva/WHz7gXnOZDHrrE4ClwWeRjEg.tAWFBDmHQTHniu', 2, 1, 0, NULL, '2024-12-02 00:38:51', '2024-12-02 00:38:51'),
(267, '', 'teacher', '$2y$10$fbZlqalMvffD6qtlAf9AUemOIWn7GLv4QSRtQSMSHngM2exdHOFSi', 3, 1, 0, NULL, '2024-12-02 00:39:11', '2024-12-02 00:39:11'),
(268, '', 'student1', '$2y$10$vpb/MIhDVc.Z0Jvm7oiji.dWvXGEWHuyykVCiGDKQ/6UF3s30mOEy', 4, 1, 0, NULL, '2024-12-02 00:39:27', '2024-12-02 00:39:27'),
(269, '', 'student2', '$2y$10$.YGT/Bw6BL6V9TSiRXaQAebrUP.krIOcmeFT4FTUL1gE49JfbK9qO', 4, 1, 0, NULL, '2024-12-02 00:39:37', '2024-12-02 00:39:37'),
(270, '', 'accounting', '$2y$10$Vz/U3mzfZ1QOD/WSAF44e.P8TcxpiFceauMioV8.3MuBsWly4Ynxq', 5, 1, 0, NULL, '2024-12-02 00:39:46', '2024-12-02 00:39:46'),
(271, '', 'auditor', '$2y$10$gaZT6PYoj2W8FA8Jf/Tc1egcIbHy1xlXCJ6HrnorzGIVz7qOUZHja', 6, 1, 0, NULL, '2024-12-02 00:40:00', '2024-12-02 00:40:00'),
(272, 'ghaizar.bautistabxucity.edu.ph', '38296576', '$2y$10$iq3VmknudLfS3ztztVbDL.v87jAqjWaEeKfnPf1Mq8LvM32eEDqQK', 4, 1, 0, NULL, '2024-12-07 15:44:38', '2024-12-11 02:45:03'),
(274, 'ghaizar.bautista@bxucity.edu.ph', '64068633', '$2y$10$c1gJsGBPOzqAt8sL00ntMesqmluunz1.AK/VxUx9pulqvpUXmSTQu', 4, 0, 0, NULL, '2024-12-07 15:56:53', '2024-12-07 15:56:53'),
(275, 'ghaizar.bautista@bxucity.edu.ph', '70776329', '$2y$10$6gYsQ8CQ21it5qUoO6SLPeIFLaqdYqWIl2p2R1O0fG9LMHqL.y2EO', 4, 0, 0, NULL, '2024-12-07 16:12:12', '2024-12-07 16:12:12'),
(276, 'ghaizar.bautista@bxucity.edu.ph', '84730196', '$2y$10$SxJfdF606uAroPKeKyk4t.nw3PM0dBtw38B.PWrQvAKRtbkXDmqf2', 4, 0, 0, NULL, '2024-12-07 16:12:59', '2024-12-07 16:12:59'),
(277, 'ghaizar.bautista@bxucity.edu.ph', '74394606', '$2y$10$Ti9gJ3MHFZw2K9ciieRu8.gRGb/N3aonjMN1noaKG6DHX6d/HCo32', 4, 0, 0, NULL, '2024-12-07 16:15:21', '2024-12-07 16:15:21'),
(278, 'ghaizar.bautista@bxucity.edu.ph', '25875077', '$2y$10$5iDdOJqobyxdsGukyUlNj.i8DYiZbJg368lQVcnrX120/wvcHPRee', 4, 0, 0, NULL, '2024-12-07 16:27:10', '2024-12-07 16:27:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_year`
--
ALTER TABLE `academic_year`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `eh_id` (`eh_id`);

--
-- Indexes for table `campus_info`
--
ALTER TABLE `campus_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollment_history`
--
ALTER TABLE `enrollment_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `grade_level_id` (`grade_level_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `enrollment_history_ibfk_4` (`adviser_id`),
  ADD KEY `enrollment_history_ibfk_5` (`academic_year_id`);

--
-- Indexes for table `grade_records`
--
ALTER TABLE `grade_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `eh_id` (`eh_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `academic_id` (`academic_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `adviser` (`adviser`);

--
-- Indexes for table `semester`
--
ALTER TABLE `semester`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_year`
--
ALTER TABLE `academic_year`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `attendance_records`
--
ALTER TABLE `attendance_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `campus_info`
--
ALTER TABLE `campus_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `enrollment_history`
--
ALTER TABLE `enrollment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grade_records`
--
ALTER TABLE `grade_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT for table `semester`
--
ALTER TABLE `semester`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=279;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD CONSTRAINT `attendance_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `eh_id` FOREIGN KEY (`eh_id`) REFERENCES `enrollment_history` (`id`);

--
-- Constraints for table `enrollment_history`
--
ALTER TABLE `enrollment_history`
  ADD CONSTRAINT `enrollment_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `enrollment_history_ibfk_2` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_level` (`id`),
  ADD CONSTRAINT `enrollment_history_ibfk_3` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`),
  ADD CONSTRAINT `enrollment_history_ibfk_4` FOREIGN KEY (`adviser_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `enrollment_history_ibfk_5` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_year` (`id`);

--
-- Constraints for table `grade_records`
--
ALTER TABLE `grade_records`
  ADD CONSTRAINT `grade_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grade_records_ibfk_2` FOREIGN KEY (`eh_id`) REFERENCES `enrollment_history` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grade_records_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`academic_id`) REFERENCES `academic_year` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `department` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_4` FOREIGN KEY (`adviser`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `semester`
--
ALTER TABLE `semester`
  ADD CONSTRAINT `semester_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `department` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`profile_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
