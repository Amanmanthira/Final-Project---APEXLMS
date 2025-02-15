-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 15, 2025 at 01:25 PM
-- Server version: 8.0.18
-- PHP Version: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `apex_admin_id` int(16) NOT NULL AUTO_INCREMENT,
  `apex_admin_name` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `apex_admin_password` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`apex_admin_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`apex_admin_id`, `apex_admin_name`, `apex_admin_password`) VALUES
(1, 'admin', '$2y$10$HqoQJTvm0NwRbktRJBKdSuIF15nZj8O5hXiA6ZjBnC3sTuuwqmlIy');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
CREATE TABLE IF NOT EXISTS `assignments` (
  `assignment_id` int(11) NOT NULL AUTO_INCREMENT,
  `assignment_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `assignment_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1',
  `topic_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`assignment_id`),
  KEY `fk_assignments_topic` (`topic_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`assignment_id`, `assignment_name`, `assignment_description`, `due_date`, `created_at`, `is_active`, `topic_id`) VALUES
(15, 'sample', '', '2025-02-03', '2025-02-02 03:32:03', 1, 30);

-- --------------------------------------------------------

--
-- Table structure for table `assignment_resources`
--

DROP TABLE IF EXISTS `assignment_resources`;
CREATE TABLE IF NOT EXISTS `assignment_resources` (
  `resource_id` int(11) NOT NULL AUTO_INCREMENT,
  `assignment_id` int(11) DEFAULT NULL,
  `resource_type` enum('PDF','Video','Image','Audio','Zip') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `resource_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`resource_id`),
  KEY `fk_assignment_resources_assignment` (`assignment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignment_resources`
--

INSERT INTO `assignment_resources` (`resource_id`, `assignment_id`, `resource_type`, `resource_path`) VALUES
(15, 15, 'PDF', '../assets/private/assignments/assignment_679ee733177fd_amanmanthiracv (2).pdf');

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
--

DROP TABLE IF EXISTS `assignment_submissions`;
CREATE TABLE IF NOT EXISTS `assignment_submissions` (
  `submission_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `assignment_id` int(11) DEFAULT NULL,
  `submission_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `submission_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`submission_id`),
  KEY `fk_assignment_submissions_student` (`student_id`),
  KEY `fk_assignment_submissions_assignment` (`assignment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignment_submissions`
--

INSERT INTO `assignment_submissions` (`submission_id`, `student_id`, `assignment_id`, `submission_path`, `submission_date`) VALUES
(13, 19, 15, '../assets/private/assignments_submissions/assignment_submission_679ee744efbfe.pdf', '2025-02-02 03:32:20');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE IF NOT EXISTS `attendance` (
  `attendance_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('Present','Absent','Late') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_id` int(11) NOT NULL,
  PRIMARY KEY (`attendance_id`),
  KEY `student_id` (`student_id`),
  KEY `course_id` (`course_id`),
  KEY `fk_topic_id` (`topic_id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `student_id`, `course_id`, `topic_id`, `attendance_date`, `status`, `content_id`) VALUES
(35, 19, 15, 30, '2025-02-02', 'Present', 46);

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('Pending','Responded') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `name`, `email`, `subject`, `message`, `status`, `created_date`, `response`) VALUES
(3, 'aman', 'amanmanthira32326@gmail.com', 'test', 'sample', 'Responded', '2025-02-02 03:40:44', 'respond');

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
CREATE TABLE IF NOT EXISTS `content` (
  `content_id` int(11) NOT NULL AUTO_INCREMENT,
  `content_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` int(11) NOT NULL DEFAULT '1',
  `content_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `content_type` enum('Zoom Link','PDF','Video','Audio','Image','Text') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `class_time` datetime DEFAULT NULL,
  PRIMARY KEY (`content_id`),
  KEY `fk_content_topic` (`topic_id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`content_id`, `content_path`, `topic_id`, `created_at`, `is_active`, `content_name`, `content_type`, `content_text`, `class_time`) VALUES
(46, '', 30, '2025-02-02 03:31:16', 1, 'test', 'Zoom Link', 'http://localhost/lms/admin/view_course.php?course_id=15', '2025-02-02 09:01:00');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
  `course_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `course_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `department_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `course_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `course_image_square` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `course_fee` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`course_id`),
  KEY `fk_courses_department` (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `course_description`, `department_id`, `start_date`, `end_date`, `is_active`, `course_image`, `course_image_square`, `created_at`, `course_fee`) VALUES
(15, 'new course', 'test', 0, '2025-02-08', '2025-02-21', 1, '../assets/public/course_images/image_679ee6ad3d56d3.62305248.png', '../assets/public/course_images/image_679ee6ad4014c5.00285290.png', '2025-02-02 03:29:49', '35000.00');

-- --------------------------------------------------------

--
-- Table structure for table `course_announcements`
--

DROP TABLE IF EXISTS `course_announcements`;
CREATE TABLE IF NOT EXISTS `course_announcements` (
  `announcement_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `announcement` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`announcement_id`),
  KEY `course_id` (`course_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_announcements`
--

INSERT INTO `course_announcements` (`announcement_id`, `course_id`, `announcement`, `created_date`, `is_active`) VALUES
(4, 15, 'test', '2025-02-02 03:45:38', 1);

-- --------------------------------------------------------

--
-- Table structure for table `course_enrollments`
--

DROP TABLE IF EXISTS `course_enrollments`;
CREATE TABLE IF NOT EXISTS `course_enrollments` (
  `enrollment_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `enrollment_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`enrollment_id`),
  KEY `fk_course_enrollments_course` (`course_id`),
  KEY `student_id` (`student_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_enrollments`
--

INSERT INTO `course_enrollments` (`enrollment_id`, `student_id`, `course_id`, `enrollment_date`, `is_active`) VALUES
(18, 19, 15, '2025-02-02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `course_full_payments`
--

DROP TABLE IF EXISTS `course_full_payments`;
CREATE TABLE IF NOT EXISTS `course_full_payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_status_updated_date` date DEFAULT NULL,
  `payment_slip_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_status` enum('Pending','Confirmed','Denied') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `fk_course_full_payments_student` (`student_id`),
  KEY `fk_course_full_payments_course` (`course_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_installment_payments`
--

DROP TABLE IF EXISTS `course_installment_payments`;
CREATE TABLE IF NOT EXISTS `course_installment_payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `installment_number` int(11) DEFAULT NULL,
  `installment_amount` decimal(10,2) DEFAULT NULL,
  `installment_payment_date` date DEFAULT NULL,
  `installment_payment_status` enum('Pending','Confirmed','Denied') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `installment_payment_slip_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_status_updated_date` date DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `fk_course_installment_payments_student` (`student_id`),
  KEY `fk_course_installment_payments_course` (`course_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_installment_payments`
--

INSERT INTO `course_installment_payments` (`payment_id`, `student_id`, `course_id`, `installment_number`, `installment_amount`, `installment_payment_date`, `installment_payment_status`, `installment_payment_slip_path`, `payment_status_updated_date`) VALUES
(17, 19, 15, 1, '8750.00', '2025-02-02', 'Confirmed', '../assets/private/payment_slips/payment_slip_679ee6c2f230b.png', '2025-02-02');

-- --------------------------------------------------------

--
-- Table structure for table `course_lecturers`
--

DROP TABLE IF EXISTS `course_lecturers`;
CREATE TABLE IF NOT EXISTS `course_lecturers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) DEFAULT NULL,
  `lecturer_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_course_lecturer` (`course_id`,`lecturer_id`),
  KEY `lecturer_id` (`lecturer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_lecturers`
--

INSERT INTO `course_lecturers` (`id`, `course_id`, `lecturer_id`) VALUES
(12, 15, 13);

-- --------------------------------------------------------

--
-- Table structure for table `course_materials`
--

DROP TABLE IF EXISTS `course_materials`;
CREATE TABLE IF NOT EXISTS `course_materials` (
  `course_material_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `course_material_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`course_material_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_materials`
--

INSERT INTO `course_materials` (`course_material_id`, `course_id`, `course_material_name`, `is_active`, `created_at`) VALUES
(11, 15, 'test tute', 1, '2025-02-02 09:06:31');

-- --------------------------------------------------------

--
-- Table structure for table `course_material_distribution`
--

DROP TABLE IF EXISTS `course_material_distribution`;
CREATE TABLE IF NOT EXISTS `course_material_distribution` (
  `distribution_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_material_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `sent_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `received_date` datetime DEFAULT NULL,
  `status` enum('Sent','Received') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Sent',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`distribution_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_material_distribution`
--

INSERT INTO `course_material_distribution` (`distribution_id`, `course_material_id`, `student_id`, `sent_date`, `received_date`, `status`, `created_at`) VALUES
(20, 11, 19, '2025-02-02 09:06:58', '2025-02-02 09:07:12', 'Received', '2025-02-02 09:06:58');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`, `description`, `is_active`) VALUES
(7, 'IT Department', 'IT', 1);

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

DROP TABLE IF EXISTS `lecturers`;
CREATE TABLE IF NOT EXISTS `lecturers` (
  `lecturer_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`lecturer_id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_lecturers_department` (`department`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`lecturer_id`, `first_name`, `last_name`, `created_date`, `is_active`, `address`, `email`, `password`, `phone_number`, `department`) VALUES
(13, 'thushara', 'perera', '2025-02-02 09:03:30', 1, 'no.84,thammita,makevira', 'amanmanthiraamex@gmail.com', '$2y$10$d8CZQcLkvHjnGprqRChikuAooZRaejBgWTegoE0hopxBKEriloUta', '0758059818', '7');

-- --------------------------------------------------------

--
-- Table structure for table `marks`
--

DROP TABLE IF EXISTS `marks`;
CREATE TABLE IF NOT EXISTS `marks` (
  `mark_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `assignment_id` int(11) DEFAULT NULL,
  `mark` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`mark_id`),
  KEY `fk_marks_student` (`student_id`),
  KEY `fk_marks_assignment` (`assignment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marks`
--

INSERT INTO `marks` (`mark_id`, `student_id`, `assignment_id`, `mark`) VALUES
(12, 19, 15, '78.00');

-- --------------------------------------------------------

--
-- Table structure for table `otps`
--

DROP TABLE IF EXISTS `otps`;
CREATE TABLE IF NOT EXISTS `otps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `otp` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '0',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gender` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mobile_number` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `username`, `email`, `password`, `full_name`, `created_at`, `is_active`, `address`, `gender`, `mobile_number`) VALUES
(19, 'aman', 'amanmanthira32326@gmail.com', '$2y$10$5ckKc5nesHmjIAyZd8lPceE7X67VHc2puT.g1vFOsYNoS5BxPCWoi', 'aman manthira', '2025-02-02 03:28:22', 1, 'no.84,thammita,makevita', 'Male', '0741800901');

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

DROP TABLE IF EXISTS `topics`;
CREATE TABLE IF NOT EXISTS `topics` (
  `topic_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) DEFAULT NULL,
  `topic_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `creation_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`topic_id`),
  UNIQUE KEY `topic_id` (`topic_id`,`course_id`),
  KEY `fk_topics_course` (`course_id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topics`
--

INSERT INTO `topics` (`topic_id`, `course_id`, `topic_name`, `creation_date`, `is_active`) VALUES
(30, 15, 'new topic', '2025-02-02 09:00:57', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
