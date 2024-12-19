-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2024 at 01:18 PM
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
-- Table structure for table `academic_record`
--

CREATE TABLE `academic_record` (
  `id` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `academic_record`
--

INSERT INTO `academic_record` (`id`, `c_id`, `status`, `user_id`) VALUES
(6, 1, NULL, 8),
(7, 1, NULL, 5);

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
(6, 'Institutional Email', 'bxucity.edu.ph'),
(7, 'Enrollment', '1'),
(8, 'Payment Setting', '{\"unit_fee\":\"201\",\"handling_fee\":\"1202\",\"laboratory_fee\":\"5700\",\"miscellaneous_fee\":\"4750\",\"other_fee\":\"17758\",\"registration_fee\":\"1950\"}'),
(9, 'Terms', '1'),
(10, 'SCHOOL DIRECTOR', 'Ghaizar A. Bautista');

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
-- Table structure for table `employment_info`
--

CREATE TABLE `employment_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `employment_info`
--

INSERT INTO `employment_info` (`id`, `user_id`, `course_id`) VALUES
(5, 7, 1),
(10, 3, 1),
(11, 9, 1);

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_history`
--

CREATE TABLE `enrollment_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `course_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `subjects_taken` varchar(500) NOT NULL,
  `status` varchar(30) NOT NULL,
  `academic_year_id` int(11) DEFAULT NULL,
  `enrollment_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `enrollment_history`
--

INSERT INTO `enrollment_history` (`id`, `user_id`, `course_id`, `semester_id`, `subjects_taken`, `status`, `academic_year_id`, `enrollment_date`) VALUES
(1, 8, 1, 1, '[{\"subjectId\":\"1\",\"scheduleIds\":[1,2]},{\"subjectId\":\"2\",\"scheduleIds\":[3,4]},{\"subjectId\":\"3\",\"scheduleIds\":[5,6]},{\"subjectId\":\"4\",\"scheduleIds\":[7,8]},{\"subjectId\":\"5\",\"scheduleIds\":[9,10]},{\"subjectId\":\"34\",\"scheduleIds\":[11,12]},{\"subjectId\":\"38\",\"scheduleIds\":[13,14]},{\"subjectId\":\"132\",\"scheduleIds\":[15,16]}]', 'ENROLLED', 1, '2024-12-14');

-- --------------------------------------------------------

--
-- Table structure for table `grade_records`
--

CREATE TABLE `grade_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `eh_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  `grade` decimal(5,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `grade_records`
--

INSERT INTO `grade_records` (`id`, `user_id`, `eh_id`, `subject_id`, `term_id`, `grade`, `created_at`, `updated_at`) VALUES
(1, 8, 1, 1, 1, '90.52', '2024-12-16 00:49:06', '2024-12-17 18:03:57'),
(2, 8, 1, 1, 2, '90.00', '2024-12-16 00:49:06', '2024-12-16 00:49:06'),
(6, 8, 1, 1, 3, '70.00', '2024-12-17 17:24:22', '2024-12-17 17:24:22'),
(7, 8, 1, 1, 4, '71.00', '2024-12-17 17:24:41', '2024-12-17 17:40:00');

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
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `eh_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date_pay` datetime NOT NULL,
  `remark` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `eh_id`, `amount`, `date_pay`, `remark`) VALUES
(1, 1, '150.00', '2024-12-14 07:10:26', 'enrolmentfee'),
(2, 1, '300.00', '2024-12-14 07:12:34', 'enrolmentfee'),
(11, 1, '232.00', '2024-12-17 13:29:03', 'enrolmentfee'),
(12, 1, '1000.00', '2024-12-17 13:29:21', 'enrolmentfee'),
(14, 1, '500.00', '2024-12-17 13:34:04', '');

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
(15, 'Manage Teacher'),
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
(1, NULL, 1, 'Smith', 'John', 'A.', 'M', '1990-01-01', 'Street 1', 'Barangay 1', 'City 1', 'Province 1', '09123456789', '2024-12-17 04:54:29'),
(2, NULL, 2, 'Doe', 'Jane', 'B.', 'F', '1991-02-02', 'Street 2', 'Barangay 2', 'City 2', 'Province 2', '09234567890', '2024-12-17 04:54:29'),
(3, NULL, 3, 'Brown', 'Michael', 'C.', 'M', '1992-03-03', 'Street 3', 'Barangay 3', 'City 3', 'Province 3', '09345678901', '2024-12-17 04:54:29'),
(4, NULL, 4, 'Davis', 'Emily', 'D.', 'F', '1993-04-04', 'Street 4', 'Barangay 4', 'City 4', 'Province 4', '09456789012', '2024-12-17 04:54:29'),
(5, NULL, 5, 'Wilson', 'Daniel', 'E.', 'M', '1994-05-05', 'Street 5', 'Barangay 5', 'City 5', 'Province 5', '09567890123', '2024-12-17 04:54:29'),
(6, NULL, 6, 'Anderson', 'Anna', 'F.', 'F', '1995-06-06', 'Street 6', 'Barangay 6', 'City 6', 'Province 6', '09678901234', '2024-12-17 04:54:29'),
(7, NULL, 7, 'Thomas', 'David', 'G.', 'M', '1996-07-07', 'Street 7', 'Barangay 7', 'City 7', 'Province 7', '09789012345', '2024-12-17 04:54:29'),
(8, NULL, 8, 'Taylor', 'Jessica', 'H.', 'F', '1997-08-08', 'Street 8', 'Barangay 8', 'City 8', 'Province 8', '09890123456', '2024-12-17 04:54:29'),
(9, NULL, 9, 'Moore', 'James', 'I.', 'M', '1998-09-09', 'Street 9', 'Barangay 9', 'City 9', 'Province 9', '09901234567', '2024-12-17 04:54:29');

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
(7, 'Dean', '15');

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
  `adviser` int(11) DEFAULT NULL,
  `batch` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `academic_id`, `course_id`, `semester`, `subject_id`, `day`, `time_slot`, `session_type`, `adviser`, `batch`) VALUES
(1, 1, 1, '1', 1, 'Thursday', '07:30AM-09:00AM', 'Lecture', 9, 1),
(2, 1, 1, '1', 1, 'Saturday', '10:30AM-12:00PM', 'Lecture', 9, 1),
(3, 1, 1, '1', 2, 'Monday', '04:30PM-06:00PM', 'Lecture', 9, 1),
(4, 1, 1, '1', 2, 'Saturday', '06:00AM-07:30AM', 'Lecture', 9, 1),
(5, 1, 1, '1', 3, 'Tuesday', '12:00PM-01:30PM', 'Lecture', 3, 1),
(6, 1, 1, '1', 3, 'Friday', '01:30PM-03:00PM', 'Lecture', 3, 1),
(7, 1, 1, '1', 4, 'Thursday', '09:00AM-10:30AM', 'Lecture', 9, 1),
(8, 1, 1, '1', 4, 'Thursday', '06:00AM-07:30AM', 'Lecture', 9, 1),
(9, 1, 1, '1', 5, 'Friday', '03:00PM-04:30PM', 'Lecture', 3, 1),
(10, 1, 1, '1', 5, 'Monday', '03:00PM-04:30PM', 'Lecture', 3, 1),
(11, 1, 1, '1', 34, 'Thursday', '06:00PM-07:30PM', 'Lecture', NULL, 1),
(12, 1, 1, '1', 34, 'Wednesday', '06:00PM-07:30PM', 'Lecture', NULL, 1),
(13, 1, 1, '1', 38, 'Wednesday', '09:00AM-10:30AM', 'Lecture', NULL, 1),
(14, 1, 1, '1', 38, 'Tuesday', '01:30PM-03:00PM', 'Lecture', NULL, 1),
(15, 1, 1, '1', 132, 'Thursday', '12:00PM-01:30PM', 'Lecture', NULL, 1),
(16, 1, 1, '1', 132, 'Friday', '07:30AM-09:00AM', 'Lecture', NULL, 1),
(17, 1, 1, '2', 6, 'Monday', '07:30AM-09:00AM', 'Lecture', NULL, 1),
(18, 1, 1, '2', 6, 'Tuesday', '06:00AM-07:30AM', 'Lecture', NULL, 1),
(19, 1, 1, '2', 7, 'Monday', '10:30AM-12:00PM', 'Lecture', NULL, 1),
(20, 1, 1, '2', 7, 'Tuesday', '09:00AM-10:30AM', 'Lecture', NULL, 1),
(21, 1, 1, '2', 8, 'Thursday', '12:00PM-01:30PM', 'Lecture', NULL, 1),
(22, 1, 1, '2', 8, 'Wednesday', '06:00PM-07:30PM', 'Lecture', NULL, 1),
(23, 1, 1, '2', 9, 'Wednesday', '10:30AM-12:00PM', 'Lecture', NULL, 1),
(24, 1, 1, '2', 9, 'Tuesday', '06:00PM-07:30PM', 'Lecture', NULL, 1),
(25, 1, 1, '2', 10, 'Saturday', '09:00AM-10:30AM', 'Lecture', NULL, 1),
(26, 1, 1, '2', 10, 'Monday', '04:30PM-06:00PM', 'Lecture', NULL, 1),
(27, 1, 1, '2', 11, 'Thursday', '04:30PM-06:00PM', 'Lecture', NULL, 1),
(28, 1, 1, '2', 11, 'Saturday', '03:00PM-04:30PM', 'Lecture', NULL, 1),
(29, 1, 1, '2', 12, 'Thursday', '03:00PM-04:30PM', 'Lecture', NULL, 1),
(30, 1, 1, '2', 12, 'Monday', '03:00PM-04:30PM', 'Lecture', NULL, 1),
(31, 1, 1, '3', 14, 'Thursday', '10:30AM-12:00PM', 'Lecture', NULL, 1),
(32, 1, 1, '3', 14, 'Saturday', '07:30PM-09:00PM', 'Lecture', NULL, 1),
(33, 1, 1, '3', 15, 'Monday', '06:00AM-07:30AM', 'Lecture', NULL, 1),
(34, 1, 1, '3', 15, 'Saturday', '07:30AM-09:00AM', 'Lecture', NULL, 1),
(35, 1, 1, '3', 16, 'Saturday', '12:00PM-01:30PM', 'Lecture', NULL, 1),
(36, 1, 1, '3', 16, 'Monday', '07:30PM-09:00PM', 'Lecture', NULL, 1),
(37, 1, 1, '3', 17, 'Tuesday', '07:30PM-09:00PM', 'Lecture', NULL, 1),
(38, 1, 1, '3', 17, 'Tuesday', '03:00PM-04:30PM', 'Lecture', NULL, 1),
(39, 1, 1, '3', 18, 'Saturday', '01:30PM-03:00PM', 'Lecture', NULL, 1),
(40, 1, 1, '3', 18, 'Tuesday', '10:30AM-12:00PM', 'Lecture', NULL, 1),
(41, 1, 1, '3', 19, 'Wednesday', '03:00PM-04:30PM', 'Lecture', NULL, 1),
(42, 1, 1, '3', 19, 'Wednesday', '09:00AM-10:30AM', 'Lecture', NULL, 1),
(43, 1, 1, '3', 20, 'Tuesday', '06:00PM-07:30PM', 'Lecture', NULL, 1),
(44, 1, 1, '3', 20, 'Monday', '04:30PM-06:00PM', 'Lecture', NULL, 1),
(45, 1, 1, '4', 21, 'Friday', '01:30PM-03:00PM', 'Lecture', NULL, 1),
(46, 1, 1, '4', 21, 'Wednesday', '06:00PM-07:30PM', 'Lecture', NULL, 1),
(47, 1, 1, '4', 22, 'Saturday', '06:00AM-07:30AM', 'Lecture', NULL, 1),
(48, 1, 1, '4', 22, 'Friday', '10:30AM-12:00PM', 'Lecture', NULL, 1),
(49, 1, 1, '4', 23, 'Monday', '03:00PM-04:30PM', 'Lecture', NULL, 1),
(50, 1, 1, '4', 23, 'Tuesday', '06:00PM-07:30PM', 'Lecture', NULL, 1),
(51, 1, 1, '4', 24, 'Wednesday', '12:00PM-01:30PM', 'Lecture', NULL, 1),
(52, 1, 1, '4', 24, 'Thursday', '04:30PM-06:00PM', 'Lecture', NULL, 1),
(53, 1, 1, '4', 25, 'Tuesday', '07:30AM-09:00AM', 'Lecture', NULL, 1),
(54, 1, 1, '4', 25, 'Thursday', '10:30AM-12:00PM', 'Lecture', NULL, 1),
(55, 1, 2, '1', 1, 'Wednesday', '04:30PM-06:00PM', 'Lecture', NULL, 1),
(56, 1, 2, '1', 1, 'Thursday', '10:30AM-12:00PM', 'Lecture', NULL, 1),
(57, 1, 2, '1', 2, 'Monday', '07:30PM-09:00PM', 'Lecture', NULL, 1),
(58, 1, 2, '1', 2, 'Monday', '12:00PM-01:30PM', 'Lecture', NULL, 1),
(59, 1, 2, '1', 3, 'Monday', '06:00AM-07:30AM', 'Lecture', NULL, 1),
(60, 1, 2, '1', 3, 'Monday', '10:30AM-12:00PM', 'Lecture', NULL, 1),
(61, 1, 2, '1', 4, 'Friday', '07:30AM-09:00AM', 'Lecture', NULL, 1),
(62, 1, 2, '1', 4, 'Wednesday', '09:00AM-10:30AM', 'Lecture', NULL, 1),
(63, 1, 2, '1', 34, 'Friday', '12:00PM-01:30PM', 'Lecture', NULL, 1),
(64, 1, 2, '1', 34, 'Saturday', '06:00AM-07:30AM', 'Lecture', NULL, 1),
(65, 1, 2, '1', 35, 'Tuesday', '07:30AM-09:00AM', 'Lecture', NULL, 1),
(66, 1, 2, '1', 35, 'Tuesday', '06:00AM-07:30AM', 'Lecture', NULL, 1),
(67, 1, 2, '1', 36, 'Saturday', '07:30AM-09:00AM', 'Lecture', NULL, 1),
(68, 1, 2, '1', 36, 'Thursday', '06:00AM-07:30AM', 'Lecture', NULL, 1),
(69, 1, 2, '1', 37, 'Thursday', '06:00PM-07:30PM', 'Lecture', NULL, 1),
(70, 1, 2, '1', 37, 'Tuesday', '01:30PM-03:00PM', 'Lecture', NULL, 1),
(71, 1, 2, '1', 38, 'Wednesday', '03:00PM-04:30PM', 'Lecture', NULL, 1),
(72, 1, 2, '1', 38, 'Thursday', '01:30PM-03:00PM', 'Lecture', NULL, 1),
(73, 1, 2, '1', 132, 'Thursday', '07:30AM-09:00AM', 'Lecture', NULL, 1),
(74, 1, 2, '1', 132, 'Wednesday', '12:00PM-01:30PM', 'Lecture', NULL, 1),
(75, 1, 2, '2', 6, 'Wednesday', '06:00PM-07:30PM', 'Lecture', NULL, 1),
(76, 1, 2, '2', 6, 'Saturday', '03:00PM-04:30PM', 'Lecture', NULL, 1),
(77, 1, 2, '2', 7, 'Saturday', '12:00PM-01:30PM', 'Lecture', NULL, 1),
(78, 1, 2, '2', 7, 'Friday', '06:00PM-07:30PM', 'Lecture', NULL, 1),
(79, 1, 2, '2', 8, 'Saturday', '04:30PM-06:00PM', 'Lecture', NULL, 1),
(80, 1, 2, '2', 8, 'Thursday', '06:00AM-07:30AM', 'Lecture', NULL, 1),
(81, 1, 2, '2', 10, 'Wednesday', '07:30AM-09:00AM', 'Lecture', NULL, 1),
(82, 1, 2, '2', 10, 'Monday', '07:30AM-09:00AM', 'Lecture', NULL, 1),
(83, 1, 2, '2', 12, 'Monday', '03:00PM-04:30PM', 'Lecture', NULL, 1),
(84, 1, 2, '2', 12, 'Monday', '06:00PM-07:30PM', 'Lecture', NULL, 1),
(85, 1, 2, '2', 40, 'Tuesday', '07:30AM-09:00AM', 'Lecture', NULL, 1),
(86, 1, 2, '2', 40, 'Saturday', '10:30AM-12:00PM', 'Lecture', NULL, 1),
(87, 1, 2, '2', 41, 'Tuesday', '06:00AM-07:30AM', 'Lecture', NULL, 1),
(88, 1, 2, '2', 41, 'Saturday', '06:00AM-07:30AM', 'Lecture', NULL, 1),
(89, 1, 1, '1', 1, 'Monday', '03:00PM-04:30PM', 'Lecture', 9, 2),
(90, 1, 1, '1', 1, 'Thursday', '12:00PM-01:30PM', 'Lecture', 9, 2),
(91, 1, 1, '1', 2, 'Tuesday', '12:00PM-01:30PM', 'Lecture', 9, 2),
(92, 1, 1, '1', 3, 'Saturday', '07:30AM-09:00AM', 'Lecture', 9, 2),
(93, 1, 1, '1', 4, 'Monday', '09:00AM-10:30AM', 'Lecture', 9, 2),
(94, 1, 1, '1', 4, 'Wednesday', '10:30AM-12:00PM', 'Lecture', 9, 2),
(95, 1, 1, '1', 5, 'Thursday', '01:30PM-03:00PM', 'Lecture', NULL, 2),
(96, 1, 1, '1', 5, 'Monday', '10:30AM-12:00PM', 'Lecture', NULL, 2),
(97, 1, 1, '1', 34, 'Saturday', '09:00AM-10:30AM', 'Lecture', NULL, 2),
(98, 1, 1, '1', 34, 'Wednesday', '03:00PM-04:30PM', 'Lecture', NULL, 2),
(99, 1, 1, '1', 38, 'Tuesday', '07:30AM-09:00AM', 'Lecture', NULL, 2),
(100, 1, 1, '1', 38, 'Tuesday', '04:30PM-06:00PM', 'Lecture', NULL, 2),
(101, 1, 1, '1', 132, 'Wednesday', '06:00AM-07:30AM', 'Lecture', NULL, 2),
(102, 1, 1, '1', 132, 'Saturday', '01:30PM-03:00PM', 'Lecture', NULL, 2),
(103, 1, 1, '2', 6, 'Saturday', '06:00AM-07:30AM', 'Lecture', 3, 2),
(104, 1, 1, '2', 6, 'Tuesday', '01:30PM-03:00PM', 'Lecture', 3, 2),
(105, 1, 1, '2', 7, 'Monday', '07:30AM-09:00AM', 'Lecture', NULL, 2),
(106, 1, 1, '2', 7, 'Saturday', '07:30PM-09:00PM', 'Lecture', NULL, 2),
(107, 1, 1, '2', 8, 'Friday', '04:30PM-06:00PM', 'Lecture', NULL, 2),
(108, 1, 1, '2', 8, 'Thursday', '06:00PM-07:30PM', 'Lecture', NULL, 2),
(109, 1, 1, '2', 9, 'Saturday', '03:00PM-04:30PM', 'Lecture', NULL, 2),
(110, 1, 1, '2', 9, 'Friday', '06:00PM-07:30PM', 'Lecture', NULL, 2),
(111, 1, 1, '2', 10, 'Thursday', '07:30AM-09:00AM', 'Lecture', NULL, 2),
(112, 1, 1, '2', 10, 'Wednesday', '07:30AM-09:00AM', 'Lecture', NULL, 2),
(113, 1, 1, '2', 11, 'Monday', '12:00PM-01:30PM', 'Lecture', NULL, 2),
(114, 1, 1, '2', 11, 'Thursday', '03:00PM-04:30PM', 'Lecture', NULL, 2),
(115, 1, 1, '2', 12, 'Tuesday', '04:30PM-06:00PM', 'Lecture', NULL, 2),
(116, 1, 1, '2', 12, 'Tuesday', '06:00AM-07:30AM', 'Lecture', NULL, 2),
(117, 1, 1, '3', 14, 'Tuesday', '07:30PM-09:00PM', 'Lecture', NULL, 2),
(118, 1, 1, '3', 14, 'Tuesday', '06:00PM-07:30PM', 'Lecture', NULL, 2),
(119, 1, 1, '3', 15, 'Thursday', '09:00AM-10:30AM', 'Lecture', NULL, 2),
(120, 1, 1, '3', 15, 'Tuesday', '10:30AM-12:00PM', 'Lecture', NULL, 2),
(121, 1, 1, '3', 16, 'Wednesday', '04:30PM-06:00PM', 'Lecture', NULL, 2),
(122, 1, 1, '3', 16, 'Tuesday', '01:30PM-03:00PM', 'Lecture', NULL, 2),
(123, 1, 1, '3', 17, 'Friday', '01:30PM-03:00PM', 'Lecture', NULL, 2),
(124, 1, 1, '3', 17, 'Wednesday', '06:00PM-07:30PM', 'Lecture', NULL, 2),
(125, 1, 1, '3', 18, 'Friday', '10:30AM-12:00PM', 'Lecture', NULL, 2),
(126, 1, 1, '3', 18, 'Thursday', '06:00AM-07:30AM', 'Lecture', NULL, 2),
(127, 1, 1, '3', 19, 'Friday', '06:00AM-07:30AM', 'Lecture', NULL, 2),
(128, 1, 1, '3', 20, 'Thursday', '07:30PM-09:00PM', 'Lecture', NULL, 2),
(129, 1, 1, '3', 20, 'Thursday', '04:30PM-06:00PM', 'Lecture', NULL, 2),
(130, 1, 1, '4', 21, 'Monday', '06:00AM-07:30AM', 'Lecture', NULL, 2),
(131, 1, 1, '4', 21, 'Tuesday', '06:00PM-07:30PM', 'Lecture', NULL, 2),
(132, 1, 1, '4', 22, 'Monday', '03:00PM-04:30PM', 'Lecture', NULL, 2),
(133, 1, 1, '4', 22, 'Thursday', '06:00AM-07:30AM', 'Lecture', NULL, 2),
(134, 1, 1, '4', 23, 'Thursday', '12:00PM-01:30PM', 'Lecture', NULL, 2),
(135, 1, 1, '4', 23, 'Friday', '01:30PM-03:00PM', 'Lecture', NULL, 2),
(136, 1, 1, '4', 24, 'Monday', '01:30PM-03:00PM', 'Lecture', NULL, 2),
(137, 1, 1, '4', 24, 'Thursday', '01:30PM-03:00PM', 'Lecture', NULL, 2),
(138, 1, 1, '4', 25, 'Wednesday', '12:00PM-01:30PM', 'Lecture', NULL, 2),
(139, 1, 1, '4', 25, 'Saturday', '12:00PM-01:30PM', 'Lecture', NULL, 2),
(140, 1, 2, '1', 1, 'Saturday', '04:30PM-06:00PM', 'Lecture', NULL, 2),
(141, 1, 2, '1', 1, 'Saturday', '10:30AM-12:00PM', 'Lecture', NULL, 2),
(142, 1, 2, '1', 2, 'Saturday', '03:00PM-04:30PM', 'Lecture', NULL, 2),
(143, 1, 2, '1', 2, 'Monday', '09:00AM-10:30AM', 'Lecture', NULL, 2),
(144, 1, 2, '1', 3, 'Tuesday', '07:30AM-09:00AM', 'Lecture', NULL, 2),
(145, 1, 2, '1', 3, 'Tuesday', '10:30AM-12:00PM', 'Lecture', NULL, 2),
(146, 1, 2, '1', 4, 'Saturday', '09:00AM-10:30AM', 'Lecture', NULL, 2),
(147, 1, 2, '1', 4, 'Monday', '07:30AM-09:00AM', 'Lecture', NULL, 2),
(148, 1, 2, '1', 34, 'Thursday', '01:30PM-03:00PM', 'Lecture', NULL, 2),
(149, 1, 2, '1', 34, 'Thursday', '07:30PM-09:00PM', 'Lecture', NULL, 2),
(150, 1, 2, '1', 35, 'Tuesday', '06:00PM-07:30PM', 'Lecture', NULL, 2),
(151, 1, 2, '1', 36, 'Thursday', '04:30PM-06:00PM', 'Lecture', NULL, 2),
(152, 1, 2, '1', 36, 'Tuesday', '04:30PM-06:00PM', 'Lecture', NULL, 2),
(153, 1, 2, '1', 37, 'Monday', '12:00PM-01:30PM', 'Lecture', NULL, 2),
(154, 1, 2, '1', 37, 'Monday', '01:30PM-03:00PM', 'Lecture', NULL, 2),
(155, 1, 2, '1', 38, 'Tuesday', '07:30PM-09:00PM', 'Lecture', NULL, 2),
(156, 1, 2, '1', 132, 'Tuesday', '01:30PM-03:00PM', 'Lecture', NULL, 2),
(157, 1, 2, '1', 132, 'Wednesday', '07:30PM-09:00PM', 'Lecture', NULL, 2),
(158, 1, 2, '2', 6, 'Monday', '06:00PM-07:30PM', 'Lecture', NULL, 2),
(159, 1, 2, '2', 6, 'Monday', '12:00PM-01:30PM', 'Lecture', NULL, 2),
(160, 1, 2, '2', 7, 'Wednesday', '10:30AM-12:00PM', 'Lecture', NULL, 2),
(161, 1, 2, '2', 7, 'Tuesday', '09:00AM-10:30AM', 'Lecture', NULL, 2),
(162, 1, 2, '2', 8, 'Thursday', '09:00AM-10:30AM', 'Lecture', NULL, 2),
(163, 1, 2, '2', 8, 'Friday', '07:30AM-09:00AM', 'Lecture', NULL, 2),
(164, 1, 2, '2', 10, 'Saturday', '06:00PM-07:30PM', 'Lecture', NULL, 2),
(165, 1, 2, '2', 10, 'Thursday', '01:30PM-03:00PM', 'Lecture', NULL, 2),
(166, 1, 2, '2', 12, 'Wednesday', '09:00AM-10:30AM', 'Lecture', NULL, 2),
(167, 1, 2, '2', 12, 'Saturday', '01:30PM-03:00PM', 'Lecture', NULL, 2),
(168, 1, 2, '2', 40, 'Saturday', '06:00AM-07:30AM', 'Lecture', NULL, 2),
(169, 1, 2, '2', 40, 'Monday', '09:00AM-10:30AM', 'Lecture', NULL, 2),
(170, 1, 2, '2', 41, 'Tuesday', '07:30AM-09:00AM', 'Lecture', NULL, 2),
(171, 1, 2, '2', 41, 'Wednesday', '12:00PM-01:30PM', 'Lecture', NULL, 2),
(172, 1, 1, '1', 1, 'Tuesday', '06:00AM-07:30AM', 'Lecture', 9, 3),
(173, 1, 1, '1', 1, 'Monday', '01:30PM-03:00PM', 'Lecture', 9, 3),
(174, 1, 1, '1', 2, 'Wednesday', '09:00AM-10:30AM', 'Lecture', 9, 2),
(175, 1, 1, '1', 2, 'Wednesday', '07:30AM-09:00AM', 'Lecture', 9, 2),
(176, 1, 1, '1', 3, 'Monday', '06:00AM-07:30AM', 'Lecture', 9, 2),
(177, 1, 1, '1', 3, 'Tuesday', '03:00PM-04:30PM', 'Lecture', 9, 2),
(178, 1, 1, '1', 4, 'Wednesday', '06:00AM-07:30AM', 'Lecture', 9, 3),
(179, 1, 1, '1', 4, 'Friday', '10:30AM-12:00PM', 'Lecture', 9, 3),
(180, 1, 1, '1', 5, 'Tuesday', '07:30PM-09:00PM', 'Lecture', NULL, 3),
(181, 1, 1, '1', 5, 'Thursday', '09:00AM-10:30AM', 'Lecture', NULL, 3),
(182, 1, 1, '1', 34, 'Monday', '07:30AM-09:00AM', 'Lecture', NULL, 3),
(183, 1, 1, '1', 34, 'Tuesday', '12:00PM-01:30PM', 'Lecture', NULL, 3),
(184, 1, 1, '1', 38, 'Saturday', '09:00AM-10:30AM', 'Lecture', NULL, 3),
(185, 1, 1, '1', 38, 'Monday', '04:30PM-06:00PM', 'Lecture', NULL, 3),
(186, 1, 1, '1', 132, 'Friday', '06:00PM-07:30PM', 'Lecture', NULL, 3),
(187, 1, 1, '1', 132, 'Thursday', '07:30AM-09:00AM', 'Lecture', NULL, 3),
(188, 1, 1, '2', 7, 'Friday', '10:30AM-12:00PM', 'Lecture', NULL, 3),
(189, 1, 1, '2', 8, 'Friday', '07:30AM-09:00AM', 'Lecture', NULL, 3),
(190, 1, 1, '2', 9, 'Monday', '01:30PM-03:00PM', 'Lecture', NULL, 3),
(191, 1, 1, '2', 9, 'Thursday', '12:00PM-01:30PM', 'Lecture', NULL, 3),
(192, 1, 1, '2', 10, 'Friday', '09:00AM-10:30AM', 'Lecture', NULL, 3),
(193, 1, 1, '2', 10, 'Wednesday', '09:00AM-10:30AM', 'Lecture', NULL, 3),
(194, 1, 1, '2', 11, 'Saturday', '06:00PM-07:30PM', 'Lecture', NULL, 3),
(195, 1, 1, '2', 11, 'Friday', '04:30PM-06:00PM', 'Lecture', NULL, 3),
(196, 1, 1, '2', 12, 'Tuesday', '12:00PM-01:30PM', 'Lecture', NULL, 3),
(197, 1, 1, '2', 12, 'Tuesday', '09:00AM-10:30AM', 'Lecture', NULL, 3),
(198, 1, 1, '3', 14, 'Monday', '01:30PM-03:00PM', 'Lecture', NULL, 3),
(199, 1, 1, '3', 14, 'Saturday', '01:30PM-03:00PM', 'Lecture', NULL, 3),
(200, 1, 1, '3', 15, 'Wednesday', '09:00AM-10:30AM', 'Lecture', NULL, 3),
(201, 1, 1, '3', 15, 'Friday', '01:30PM-03:00PM', 'Lecture', NULL, 3),
(202, 1, 1, '3', 16, 'Tuesday', '07:30PM-09:00PM', 'Lecture', NULL, 3),
(203, 1, 1, '3', 16, 'Tuesday', '10:30AM-12:00PM', 'Lecture', NULL, 3),
(204, 1, 1, '3', 17, 'Friday', '09:00AM-10:30AM', 'Lecture', NULL, 3),
(205, 1, 1, '3', 17, 'Saturday', '10:30AM-12:00PM', 'Lecture', NULL, 3),
(206, 1, 1, '3', 18, 'Tuesday', '06:00PM-07:30PM', 'Lecture', NULL, 3),
(207, 1, 1, '3', 18, 'Friday', '06:00PM-07:30PM', 'Lecture', NULL, 3),
(208, 1, 1, '3', 19, 'Thursday', '07:30PM-09:00PM', 'Lecture', NULL, 2),
(209, 1, 1, '3', 19, 'Thursday', '06:00AM-07:30AM', 'Lecture', NULL, 2),
(210, 1, 1, '3', 20, 'Friday', '04:30PM-06:00PM', 'Lecture', NULL, 3),
(211, 1, 1, '3', 20, 'Thursday', '07:30AM-09:00AM', 'Lecture', NULL, 3),
(212, 1, 1, '4', 21, 'Friday', '07:30PM-09:00PM', 'Lecture', NULL, 3),
(213, 1, 1, '4', 21, 'Tuesday', '01:30PM-03:00PM', 'Lecture', NULL, 3),
(214, 1, 1, '4', 22, 'Monday', '06:00PM-07:30PM', 'Lecture', NULL, 3),
(215, 1, 1, '4', 22, 'Friday', '04:30PM-06:00PM', 'Lecture', NULL, 3),
(216, 1, 1, '4', 23, 'Thursday', '07:30PM-09:00PM', 'Lecture', NULL, 3),
(217, 1, 1, '4', 23, 'Thursday', '10:30AM-12:00PM', 'Lecture', NULL, 3),
(218, 1, 1, '4', 24, 'Tuesday', '03:00PM-04:30PM', 'Lecture', NULL, 3),
(219, 1, 1, '4', 24, 'Friday', '01:30PM-03:00PM', 'Lecture', NULL, 3),
(220, 1, 1, '4', 25, 'Thursday', '06:00PM-07:30PM', 'Lecture', NULL, 3),
(221, 1, 1, '4', 25, 'Saturday', '03:00PM-04:30PM', 'Lecture', NULL, 3),
(222, 1, 2, '1', 1, 'Monday', '03:00PM-04:30PM', 'Lecture', NULL, 3),
(223, 1, 2, '1', 1, 'Friday', '06:00PM-07:30PM', 'Lecture', NULL, 3),
(224, 1, 2, '1', 2, 'Thursday', '06:00AM-07:30AM', 'Lecture', NULL, 3),
(225, 1, 2, '1', 2, 'Tuesday', '03:00PM-04:30PM', 'Lecture', NULL, 3),
(226, 1, 2, '1', 3, 'Wednesday', '04:30PM-06:00PM', 'Lecture', NULL, 3),
(227, 1, 2, '1', 3, 'Thursday', '07:30PM-09:00PM', 'Lecture', NULL, 3),
(228, 1, 2, '1', 4, 'Tuesday', '10:30AM-12:00PM', 'Lecture', NULL, 3),
(229, 1, 2, '1', 4, 'Monday', '09:00AM-10:30AM', 'Lecture', NULL, 3),
(230, 1, 2, '1', 34, 'Thursday', '06:00PM-07:30PM', 'Lecture', NULL, 3),
(231, 1, 2, '1', 34, 'Saturday', '12:00PM-01:30PM', 'Lecture', NULL, 3),
(232, 1, 2, '1', 35, 'Wednesday', '06:00AM-07:30AM', 'Lecture', NULL, 2),
(233, 1, 2, '1', 35, 'Monday', '01:30PM-03:00PM', 'Lecture', NULL, 2),
(234, 1, 2, '1', 36, 'Tuesday', '06:00PM-07:30PM', 'Lecture', NULL, 3),
(235, 1, 2, '1', 36, 'Wednesday', '10:30AM-12:00PM', 'Lecture', NULL, 3),
(236, 1, 2, '1', 37, 'Thursday', '04:30PM-06:00PM', 'Lecture', NULL, 3),
(237, 1, 2, '1', 37, 'Saturday', '07:30AM-09:00AM', 'Lecture', NULL, 3),
(238, 1, 2, '1', 38, 'Monday', '06:00AM-07:30AM', 'Lecture', NULL, 2),
(239, 1, 2, '1', 38, 'Monday', '04:30PM-06:00PM', 'Lecture', NULL, 2),
(240, 1, 2, '1', 132, 'Saturday', '06:00PM-07:30PM', 'Lecture', NULL, 3),
(241, 1, 2, '1', 132, 'Friday', '04:30PM-06:00PM', 'Lecture', NULL, 3),
(242, 1, 2, '2', 6, 'Monday', '10:30AM-12:00PM', 'Lecture', NULL, 3),
(243, 1, 2, '2', 6, 'Tuesday', '06:00PM-07:30PM', 'Lecture', NULL, 3),
(244, 1, 2, '2', 7, 'Saturday', '07:30PM-09:00PM', 'Lecture', NULL, 3),
(245, 1, 2, '2', 7, 'Wednesday', '01:30PM-03:00PM', 'Lecture', NULL, 3),
(246, 1, 2, '2', 8, 'Friday', '04:30PM-06:00PM', 'Lecture', NULL, 3),
(247, 1, 2, '2', 8, 'Thursday', '06:00PM-07:30PM', 'Lecture', NULL, 3),
(248, 1, 2, '2', 10, 'Saturday', '12:00PM-01:30PM', 'Lecture', NULL, 3),
(249, 1, 2, '2', 10, 'Thursday', '07:30AM-09:00AM', 'Lecture', NULL, 3),
(250, 1, 2, '2', 12, 'Monday', '04:30PM-06:00PM', 'Lecture', NULL, 3),
(251, 1, 2, '2', 40, 'Wednesday', '12:00PM-01:30PM', 'Lecture', NULL, 3),
(252, 1, 2, '2', 40, 'Wednesday', '03:00PM-04:30PM', 'Lecture', NULL, 3),
(253, 1, 2, '2', 41, 'Monday', '09:00AM-10:30AM', 'Lecture', NULL, 3);

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
(1, 'Understanding the Self', '', 'GE 1', 3, 0, NULL),
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
(132, 'Civic Welfare Training Services 1', '', 'NSTP 1', 3, 0, ''),
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
(1, 'zhie@zear.developer.com', 'admin', '$2y$10$Y3A7u1B6/Fchy.twJAypLOhLmD1/KCYjy/BGce2P5jUOKThSTXD3u', 1, 1, 0, NULL, '2024-11-03 03:22:39', '2024-12-12 03:34:45'),
(2, '', 'registrar', '$2y$10$F3I24OvhzcXva/WHz7gXnOZDHrrE4ClwWeRjEg.tAWFBDmHQTHniu', 2, 1, 0, NULL, '2024-12-02 00:38:51', '2024-12-12 03:34:52'),
(3, '', 'teacher', '$2y$10$fbZlqalMvffD6qtlAf9AUemOIWn7GLv4QSRtQSMSHngM2exdHOFSi', 3, 1, 0, NULL, '2024-12-02 00:39:11', '2024-12-12 03:34:54'),
(4, '', 'student1', '$2y$10$vpb/MIhDVc.Z0Jvm7oiji.dWvXGEWHuyykVCiGDKQ/6UF3s30mOEy', 4, 1, 0, NULL, '2024-12-02 00:39:27', '2024-12-12 03:34:57'),
(5, '', 'student2', '$2y$10$.YGT/Bw6BL6V9TSiRXaQAebrUP.krIOcmeFT4FTUL1gE49JfbK9qO', 4, 1, 0, NULL, '2024-12-02 00:39:37', '2024-12-12 03:35:00'),
(6, '', 'accounting', '$2y$10$Vz/U3mzfZ1QOD/WSAF44e.P8TcxpiFceauMioV8.3MuBsWly4Ynxq', 5, 1, 0, NULL, '2024-12-02 00:39:46', '2024-12-12 03:35:04'),
(7, '', 'dean', '$2y$10$gaZT6PYoj2W8FA8Jf/Tc1egcIbHy1xlXCJ6HrnorzGIVz7qOUZHja', 7, 1, 0, NULL, '2024-12-02 00:40:00', '2024-12-17 03:26:14'),
(8, 'ghaizar.bautista@bxucity.edu.ph', '21001235800', '$2y$10$pNzJYYvU4AzCX.8eS4HyG.kK6eZDQahNoS.TguaR4NREh6EZM37WO', 4, 1, 0, NULL, '2024-12-12 03:40:44', '2024-12-15 04:43:09'),
(9, 'ghaizar.bautistaa@bxucity.edu.ph', '123456', '$2y$10$UN/5xxPp2lu2KHuZCtT4r.Py9Fh4IxMokrf2DXOPwX7gzEvYfBXSi', 3, 1, 0, NULL, '2024-12-12 06:59:04', '2024-12-17 13:59:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_record`
--
ALTER TABLE `academic_record`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

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
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employment_info`
--
ALTER TABLE `employment_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `enrollment_history`
--
ALTER TABLE `enrollment_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `grade_records`
--
ALTER TABLE `grade_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eh_id` (`eh_id`);

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
-- AUTO_INCREMENT for table `academic_record`
--
ALTER TABLE `academic_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `employment_info`
--
ALTER TABLE `employment_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `enrollment_history`
--
ALTER TABLE `enrollment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `grade_records`
--
ALTER TABLE `grade_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_record`
--
ALTER TABLE `academic_record`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD CONSTRAINT `attendance_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `eh_id` FOREIGN KEY (`eh_id`) REFERENCES `enrollment_history` (`id`);

--
-- Constraints for table `employment_info`
--
ALTER TABLE `employment_info`
  ADD CONSTRAINT `employment_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `employment_info_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `department` (`id`);

--
-- Constraints for table `enrollment_history`
--
ALTER TABLE `enrollment_history`
  ADD CONSTRAINT `enrollment_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `enrollment_history_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `grade_level` (`id`),
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
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`eh_id`) REFERENCES `enrollment_history` (`id`) ON DELETE CASCADE;

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
