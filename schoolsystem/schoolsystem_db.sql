-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2024 at 05:54 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `schoolsystem_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `AdminID` int(11) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`AdminID`, `FirstName`, `LastName`, `Username`, `Password`) VALUES
(1, 'Alice', 'Gou', 'chingchong', '$2y$10$CzWPlzy3tk12TWTQzesRMObaCTbS98Vkllt3b9/qXhxRkI4QiCBfK'),
(2, 'Julius', 'Caesar', 'theadmin', '$2y$10$b8QoUS/xUnm1jF0oQxCSxuTqiPeM3qcLHkkSe4I/z62pU8EPdWpcC');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment`
--

CREATE TABLE `enrollment` (
  `EnrollmentID` int(11) NOT NULL,
  `StudentID` int(11) NOT NULL,
  `OfferingID` int(11) NOT NULL,
  `EnrollmentDate` date DEFAULT curdate(),
  `Status` enum('Active','Completed','Withdrawn') DEFAULT 'Active',
  `SchoolLevel` varchar(50) DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollment`
--

INSERT INTO `enrollment` (`EnrollmentID`, `StudentID`, `OfferingID`, `EnrollmentDate`, `Status`, `SchoolLevel`, `IsActive`) VALUES
(31, 9, 43, '2024-04-21', 'Active', 'YS 6: Grade 5', 1),
(32, 9, 44, '2024-04-21', 'Active', 'YS 6: Grade 5', 1),
(33, 9, 45, '2024-04-21', 'Active', 'YS 6: Grade 5', 1),
(34, 7, 46, '2024-04-21', 'Active', 'Junior Nursery', 1),
(35, 7, 47, '2024-04-21', 'Active', 'Junior Nursery', 1),
(36, 7, 48, '2024-04-21', 'Active', 'Junior Nursery', 1),
(37, 6, 49, '2024-04-21', 'Active', 'YS 5: Grade 4', 1),
(38, 6, 50, '2024-04-21', 'Active', 'YS 5: Grade 4', 1),
(39, 6, 51, '2024-04-21', 'Active', 'YS 5: Grade 4', 1),
(40, 6, 52, '2024-04-21', 'Active', 'YS 5: Grade 4', 1),
(41, 6, 53, '2024-04-21', 'Active', 'YS 5: Grade 4', 1),
(42, 6, 54, '2024-04-21', 'Active', 'YS 5: Grade 4', 1),
(43, 6, 55, '2024-04-21', 'Active', 'YS 5: Grade 4', 1),
(44, 6, 56, '2024-04-21', 'Active', 'YS 5: Grade 4', 1),
(45, 8, 43, '2024-04-22', 'Active', 'YS 6: Grade 5', 1),
(46, 8, 44, '2024-04-22', 'Active', 'YS 6: Grade 5', 1),
(47, 8, 45, '2024-04-22', 'Active', 'YS 6: Grade 5', 1),
(48, 10, 43, '2024-04-22', 'Active', 'YS 6: Grade 5', 1),
(49, 10, 44, '2024-04-22', 'Active', 'YS 6: Grade 5', 1),
(50, 10, 45, '2024-04-22', 'Active', 'YS 6: Grade 5', 1),
(51, 11, 49, '2024-04-23', 'Active', 'YS 5: Grade 4', 1),
(52, 11, 50, '2024-04-23', 'Active', 'YS 5: Grade 4', 1),
(53, 11, 51, '2024-04-23', 'Active', 'YS 5: Grade 4', 1),
(54, 11, 52, '2024-04-23', 'Active', 'YS 5: Grade 4', 1),
(55, 11, 53, '2024-04-23', 'Active', 'YS 5: Grade 4', 1),
(56, 11, 54, '2024-04-23', 'Active', 'YS 5: Grade 4', 1),
(57, 11, 55, '2024-04-23', 'Active', 'YS 5: Grade 4', 1),
(58, 11, 56, '2024-04-23', 'Active', 'YS 5: Grade 4', 1),
(59, 12, 49, '2024-04-23', 'Active', 'YS 5: Grade 4', 1),
(60, 12, 50, '2024-04-23', 'Active', 'YS 5: Grade 4', 1),
(61, 12, 51, '2024-04-23', 'Active', 'YS 5: Grade 4', 1),
(62, 12, 52, '2024-04-23', 'Active', 'YS 5: Grade 4', 1),
(63, 12, 53, '2024-04-23', 'Active', 'YS 5: Grade 4', 1),
(64, 12, 54, '2024-04-23', 'Active', 'YS 5: Grade 4', 1),
(65, 12, 55, '2024-04-23', 'Active', 'YS 5: Grade 4', 1),
(66, 12, 56, '2024-04-23', 'Active', 'YS 5: Grade 4', 1),
(67, 13, 49, '2024-05-12', 'Active', 'YS 5: Grade 4', 1),
(68, 13, 50, '2024-05-12', 'Active', 'YS 5: Grade 4', 1),
(69, 13, 51, '2024-05-12', 'Active', 'YS 5: Grade 4', 1),
(70, 13, 52, '2024-05-12', 'Active', 'YS 5: Grade 4', 1),
(71, 13, 53, '2024-05-12', 'Active', 'YS 5: Grade 4', 1),
(72, 13, 54, '2024-05-12', 'Active', 'YS 5: Grade 4', 1),
(73, 13, 55, '2024-05-12', 'Active', 'YS 5: Grade 4', 1),
(74, 13, 56, '2024-05-12', 'Active', 'YS 5: Grade 4', 1),
(75, 14, 57, '2024-05-15', 'Active', 'YS 4: Grade 3', 1),
(76, 14, 58, '2024-05-15', 'Active', 'YS 4: Grade 3', 1),
(77, 14, 59, '2024-05-15', 'Active', 'YS 4: Grade 3', 1),
(78, 15, 60, '2024-05-27', 'Active', 'YS 3: Grade 2', 1),
(79, 15, 61, '2024-05-27', 'Active', 'YS 3: Grade 2', 1),
(80, 15, 62, '2024-05-27', 'Active', 'YS 3: Grade 2', 1),
(81, 16, 63, '2024-08-20', 'Active', 'YS 7: Grade 6', 1),
(82, 16, 64, '2024-08-20', 'Active', 'YS 7: Grade 6', 1),
(83, 17, 63, '2024-11-04', 'Active', 'YS 7: Grade 6', 1),
(84, 17, 64, '2024-11-04', 'Active', 'YS 7: Grade 6', 1),
(85, 18, 65, '2024-12-03', 'Active', 'YS 7: Grade 6', 1),
(86, 18, 66, '2024-12-03', 'Active', 'YS 7: Grade 6', 1),
(87, 18, 67, '2024-12-03', 'Active', 'YS 7: Grade 6', 1),
(88, 19, 65, '2024-12-03', 'Active', 'YS 7: Grade 6', 1),
(89, 19, 66, '2024-12-03', 'Active', 'YS 7: Grade 6', 1),
(90, 19, 67, '2024-12-03', 'Active', 'YS 7: Grade 6', 1);

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `GradeID` int(11) NOT NULL,
  `EnrollmentID` int(11) DEFAULT NULL,
  `SubjectID` int(11) DEFAULT NULL,
  `TestType` varchar(50) DEFAULT NULL,
  `Grade` enum('Ok','Good','Very Good','Excellent') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`GradeID`, `EnrollmentID`, `SubjectID`, `TestType`, `Grade`) VALUES
(7, 31, 1, NULL, 'Very Good'),
(8, 45, 1, NULL, 'Excellent'),
(9, 48, 1, NULL, 'Good'),
(49, 37, 8, NULL, 'Excellent'),
(50, 51, 8, NULL, 'Excellent'),
(51, 59, 8, NULL, 'Excellent'),
(55, 38, 9, NULL, 'Excellent'),
(56, 52, 9, NULL, 'Very Good'),
(57, 60, 9, NULL, 'Excellent'),
(58, 39, 10, NULL, 'Very Good'),
(59, 53, 10, NULL, 'Excellent'),
(60, 61, 10, NULL, 'Very Good'),
(67, 32, 2, NULL, 'Excellent'),
(68, 46, 2, NULL, 'Excellent'),
(69, 49, 2, NULL, 'Excellent'),
(70, 33, 16, NULL, 'Excellent'),
(71, 47, 16, NULL, 'Excellent'),
(72, 50, 16, NULL, 'Excellent'),
(76, 67, 8, NULL, 'Very Good'),
(80, 68, 9, NULL, 'Very Good'),
(84, 69, 10, NULL, 'Good'),
(85, 40, 11, NULL, 'Excellent'),
(86, 54, 11, NULL, 'Very Good'),
(87, 62, 11, NULL, 'Good'),
(88, 70, 11, NULL, 'Good'),
(89, 41, 12, NULL, 'Excellent'),
(90, 55, 12, NULL, 'Excellent'),
(91, 63, 12, NULL, 'Excellent'),
(92, 71, 12, NULL, 'Very Good'),
(93, 42, 13, NULL, 'Excellent'),
(94, 56, 13, NULL, 'Excellent'),
(95, 64, 13, NULL, 'Excellent'),
(96, 72, 13, NULL, 'Ok'),
(97, 43, 14, NULL, 'Excellent'),
(98, 57, 14, NULL, 'Excellent'),
(99, 65, 14, NULL, 'Excellent'),
(100, 73, 14, NULL, 'Excellent'),
(101, 44, 15, NULL, 'Excellent'),
(102, 58, 15, NULL, 'Excellent'),
(103, 66, 15, NULL, 'Excellent'),
(104, 74, 15, NULL, 'Very Good'),
(105, 75, 18, NULL, 'Excellent'),
(106, 76, 19, NULL, 'Excellent'),
(107, 77, 20, NULL, 'Excellent'),
(108, 78, 22, NULL, 'Excellent'),
(109, 82, 25, NULL, 'Excellent'),
(110, 81, 3, NULL, 'Very Good'),
(111, 34, 5, NULL, 'Excellent'),
(112, 35, 6, NULL, 'Very Good'),
(113, 36, 7, NULL, 'Excellent'),
(115, 83, 3, NULL, 'Excellent'),
(117, 84, 25, NULL, 'Excellent'),
(122, 85, 3, NULL, 'Very Good'),
(123, 86, 25, NULL, 'Excellent'),
(124, 87, 26, NULL, 'Excellent'),
(126, 88, 3, NULL, 'Excellent'),
(128, 89, 25, NULL, 'Excellent');

-- --------------------------------------------------------

--
-- Table structure for table `offerings`
--

CREATE TABLE `offerings` (
  `OfferingID` int(11) NOT NULL,
  `SubjectID` int(11) DEFAULT NULL,
  `Term` varchar(50) DEFAULT NULL,
  `SchoolYear` varchar(9) DEFAULT NULL,
  `ScheduleDetails` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offerings`
--

INSERT INTO `offerings` (`OfferingID`, `SubjectID`, `Term`, `SchoolYear`, `ScheduleDetails`) VALUES
(43, 1, '1st Semester', '2025-2026', '9am-10am'),
(44, 2, '1st Semester', '2025-2026', '1pm-2pm'),
(45, 16, '1st Semester', '2025-2026', '3pm-4pm'),
(46, 5, '1st Semester', '2024-2025', '8am'),
(47, 6, '1st Semester', '2024-2025', '9am'),
(48, 7, '1st Semester', '2024-2025', '1pm-2pm'),
(49, 8, '1st Semester', '2024-2025', '8am - 9am MWF'),
(50, 9, '1st Semester', '2024-2025', '9am - 10am MWF'),
(51, 10, '1st Semester', '2024-2025', '10am - 11am MWF'),
(52, 11, '1st Semester', '2024-2025', '1pm - 2pm MWF'),
(53, 12, '1st Semester', '2024-2025', '3pm - 4pm '),
(54, 13, '1st Semester', '2024-2025', '9am - 10am TTh'),
(55, 14, '1st Semester', '2024-2025', '10am - 11am TTh'),
(56, 15, '1st Semester', '2024-2025', '1pm - 2pm TTh'),
(57, 18, '1st Semester', '2024-2025', '8 am'),
(58, 19, '1st Semester', '2024-2025', '9 am'),
(59, 20, '1st Semester', '2024-2025', '1 pm'),
(60, 22, '1st Semester', '2024-2025', '8am-9am'),
(61, 23, '1st Semester', '2024-2025', '9am-10am'),
(62, 24, '1st Semester', '2024-2025', '1pm-2pm'),
(63, 3, '1st Semester', '2024-2025', '9am - 11am'),
(64, 25, '1st Semester', '2024-2025', '1pm - 3pm'),
(65, 3, '2 Semester', '2025-2026', ''),
(66, 25, '2 Semester', '2025-2026', ''),
(67, 26, '2 Semester', '2025-2026', '');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `EnrollmentID` int(11) NOT NULL,
  `StudentID` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `total_amount_paid` decimal(10,2) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `date_paid` date DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `running_balance` decimal(10,2) NOT NULL,
  `school_year` varchar(9) NOT NULL,
  `semester` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `EnrollmentID`, `StudentID`, `total_amount`, `total_amount_paid`, `start_date`, `end_date`, `date_paid`, `status`, `running_balance`, `school_year`, `semester`, `created_at`, `updated_at`) VALUES
(27, 81, 16, 30000.00, 30000.00, '0000-00-00', '0000-00-00', NULL, 'Fully Paid', 0.00, '2024-2025', 'Second Semester', '2024-12-04 23:15:35', '2024-12-04 23:34:49'),
(33, 88, 19, 30000.00, 30500.00, '0000-00-00', '0000-00-00', NULL, 'Partially Paid', -500.00, '2024-2025', 'First Semester', '2024-12-06 05:51:39', '2024-12-06 05:54:53');

-- --------------------------------------------------------

--
-- Table structure for table `paymenthistory`
--

CREATE TABLE `paymenthistory` (
  `history_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `StudentID` int(11) NOT NULL,
  `EnrollmentID` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `running_balance` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL,
  `change_type` varchar(50) NOT NULL,
  `change_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_paid` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paymenthistory`
--

INSERT INTO `paymenthistory` (`history_id`, `payment_id`, `StudentID`, `EnrollmentID`, `total_amount`, `amount_paid`, `running_balance`, `status`, `change_type`, `change_date`, `date_paid`) VALUES
(28, 27, 16, 81, 30000.00, 0.00, 30000.00, 'Not Paid', 'Initial Payment', '2024-12-04 23:15:35', NULL),
(29, 27, 16, 81, 30000.00, 13000.00, 17000.00, 'Partially Paid', 'Payment Added', '2024-12-04 23:16:12', '2024-12-06'),
(30, 27, 16, 81, 30000.00, 5000.00, 25000.00, 'Partially Paid', 'Payment Added', '2024-12-04 23:24:30', '2024-12-09'),
(31, 27, 16, 81, 30000.00, 10000.00, 2000.00, 'Partially Paid', 'Payment Added', '2024-12-04 23:33:42', '2024-12-11'),
(32, 27, 16, 81, 30000.00, 2000.00, 0.00, 'Fully Paid', 'Payment Added', '2024-12-04 23:34:49', '2024-12-13'),
(59, 33, 19, 88, 30000.00, 0.00, 30000.00, 'Not Paid', 'Initial Payment', '2024-12-06 05:51:39', NULL),
(60, 33, 19, 88, 30000.00, 5000.00, 25000.00, 'Partially Paid', 'Payment Added', '2024-12-06 05:51:55', '2024-12-06'),
(61, 33, 19, 88, 30000.00, 9000.00, 16000.00, 'Partially Paid', 'Payment Added', '2024-12-06 05:52:39', '2024-12-16'),
(63, 33, 19, 88, 30000.00, 16500.00, -500.00, 'Partially Paid', 'Payment Added', '2024-12-06 05:54:53', '2024-12-20');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `PaymentID` int(11) NOT NULL,
  `StudentID` int(11) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `AmountPaid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `PaymentStatus` enum('Fully Paid','Partially Paid','Not Paid') NOT NULL,
  `DatePaid` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`PaymentID`, `StudentID`, `Amount`, `AmountPaid`, `StartDate`, `EndDate`, `PaymentStatus`, `DatePaid`) VALUES
(1, 9, 3000.00, 3000.00, '2024-06-03', '2024-07-08', 'Fully Paid', NULL),
(2, 10, 3000.00, 3000.00, '2024-05-06', '2024-06-03', 'Fully Paid', NULL),
(3, 13, 3000.00, 3000.00, '2024-05-06', '2024-06-03', 'Fully Paid', NULL),
(6, 7, 1000.00, 500.00, '2024-05-06', '2024-06-03', 'Partially Paid', NULL),
(7, 14, 3000.00, 3999.00, '2024-05-06', '2024-06-03', 'Fully Paid', NULL),
(8, 15, 3000.00, 2000.00, '2024-05-06', '2024-06-03', 'Partially Paid', NULL),
(11, 17, 3000.00, 2500.00, '2024-11-20', '2024-12-20', 'Partially Paid', NULL),
(26, 16, 3000.00, 3000.00, '2024-12-02', '2024-12-31', 'Fully Paid', '2024-12-02'),
(27, 18, 30000.00, 3000.00, '2025-06-16', '2026-03-16', 'Partially Paid', '2024-12-03'),
(28, 19, 30000.00, 15000.00, '2025-06-16', '2025-03-07', 'Partially Paid', '2024-12-03');

-- --------------------------------------------------------

--
-- Table structure for table `payment_history`
--

CREATE TABLE `payment_history` (
  `PaymentHistoryID` int(11) NOT NULL,
  `StudentID` int(11) DEFAULT NULL,
  `AmountPaid` decimal(10,2) DEFAULT NULL,
  `RunningBalance` decimal(10,2) DEFAULT NULL,
  `DatePaid` date DEFAULT NULL,
  `Status` varchar(255) DEFAULT NULL,
  `TransactionDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `PaymentID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_history`
--

INSERT INTO `payment_history` (`PaymentHistoryID`, `StudentID`, `AmountPaid`, `RunningBalance`, `DatePaid`, `Status`, `TransactionDate`, `PaymentID`) VALUES
(6, NULL, 200.00, 2800.00, '2024-12-02', 'Partially Paid', '2024-12-01 16:00:00', 25),
(7, NULL, 300.00, 2500.00, '2024-12-02', 'Partially Paid', '2024-12-01 16:00:00', 25),
(8, NULL, 1000.00, 1500.00, '2024-12-02', 'Partially Paid', '2024-12-01 16:00:00', 25),
(9, NULL, 600.00, 2400.00, '2024-12-02', 'Partially Paid', '2024-12-01 16:00:00', 26),
(10, NULL, 600.00, 1800.00, '2024-12-02', 'Partially Paid', '2024-12-01 16:00:00', 26),
(11, NULL, 1800.00, 0.00, '2024-12-02', 'Fully Paid', '2024-12-01 16:00:00', 26),
(12, NULL, 3000.00, 27000.00, '2024-12-03', 'Partially Paid', '2024-12-02 16:00:00', 27),
(13, NULL, 15000.00, 15000.00, '2024-12-03', 'Partially Paid', '2024-12-02 16:00:00', 28);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `StudentID` int(11) NOT NULL,
  `FirstName` varchar(100) DEFAULT NULL,
  `MiddleName` varchar(100) DEFAULT NULL,
  `LastName` varchar(100) DEFAULT NULL,
  `BirthDate` date DEFAULT NULL,
  `SpecialNeedsDetails` text DEFAULT NULL,
  `ParentGuardianContactInfo` varchar(255) DEFAULT NULL,
  `Username` varchar(50) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Status` varchar(10) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`StudentID`, `FirstName`, `MiddleName`, `LastName`, `BirthDate`, `SpecialNeedsDetails`, `ParentGuardianContactInfo`, `Username`, `Password`, `Status`) VALUES
(6, 'Garfield', 'Holland', 'Wayne', NULL, NULL, NULL, 'orangeming', '$2y$10$asimqxa0vfTq2oIDpL6fDeooR.p16ZkmQEE1FZhwXt2md1cd9vnzi', 'Active'),
(7, 'Vertin', 'Gonzalez', 'Tan', NULL, NULL, NULL, 'arcanist', '$2y$10$wrPQqxmrM2hxYjAnLZFds.zC1JZfllMkuzz9PVYb3Xa7b3vaX4ise', 'Active'),
(8, 'Falin', 'Simba', 'Artoria', NULL, NULL, NULL, 'foodygoody', '$2y$10$CisGqcodfcylqnexFeis/Oc23/G1x2SDZLsBChQKTtdDCoNUXl5TC', 'Active'),
(9, 'Henry William', 'Dalgliesh', 'Cavill', NULL, NULL, NULL, 'witcher', '$2y$10$qAA1gR33ks7B4smAQFsqDOOtUC4GpjAfaXpmR6tdGtqOYtpaZSWCy', 'Active'),
(10, 'Jhastine', 'Mondido', 'Ucab', NULL, NULL, NULL, 'smashburger', '$2y$10$oTHLwAO/4ICHenVlgHdMneLZEPBh.2W64K78rM70Cu3xKS.bHc91q', 'Active'),
(11, 'Kent', 'Locco', 'Mocco', NULL, NULL, NULL, 'Kennatsit', '$2y$10$.ndnRDOvbcAa1pyNTu6L2epGNnsFKfLAgP9TlLoYHiyMcosZf7zNu', 'Active'),
(12, 'Jaslene', 'Bacolod', 'Guzman', NULL, NULL, NULL, 'jaslayer', '$2y$10$6ayX00Dqo4EIzTUU8Y/4ruu3CxN0vshKkIupxqV8vxNa.Y3tQlPe.', 'Active'),
(13, 'Asa', 'War', 'Mitaka', NULL, NULL, NULL, 'femcel', '$2y$10$myV5TK30aoNH7.8TkqfWXOuRgqrCtle2vfyCADVhxVHaRqHy89IvG', 'Active'),
(14, 'Whit Kyle', 'Gripo', 'Yonting', NULL, NULL, NULL, 'kylegundam', '$2y$10$Srowm/umdkq8dXzgBY/JB.wDCf2QG.4R.q74Tam0xMjkxsI3MQhFu', 'Active'),
(15, 'Charlie', 'Dagumo', 'Martinez', NULL, NULL, NULL, 'CharlieM', '$2y$10$E5kvgVJOEj8EDsRu9bD8der8W34lAV1H2w41HHr3YPyiElVWntf6e', 'Active'),
(16, 'Thom', 'Bends', 'Yorke', NULL, NULL, NULL, 'radiohead', '$2y$10$kfMyJ/C93DY2CTe.1dDde.wNhSkeAEWMw.quCWt8rP8PdW0jfy7Ta', 'Active'),
(17, 'Sova', 'Panaghugpong', 'Valorant', NULL, NULL, NULL, 'sentinel', '$2y$10$EAEup0B8qUhz5rV3HuMBTumnONwUQ2FBNTxolceY4TrOoJ/TtXfTq', 'Active'),
(18, 'Aljun', 'Mata', 'Itturiaga', NULL, NULL, NULL, 'aljun.mata', '$2y$10$IwwHtyWvsE9v7dlPj2zAdeMSBH8gAVYor1eiYE5V./NxcHtDKMUtC', 'Active'),
(19, 'Ken', 'Ballsy', 'Takakura', NULL, NULL, NULL, 'numbahwan', '$2y$10$N/oYbF4lGb/.QGooQrMnf.OyXIblydMiVQ.HqN8gR4TzGYi4EMQ3O', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `SubjectID` int(11) NOT NULL,
  `SubjectName` varchar(100) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `SubjectSchoolLevel` varchar(255) DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`SubjectID`, `SubjectName`, `Description`, `SubjectSchoolLevel`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'Filipino 5', 'Tagalog ni', 'YS 6: Grade 5', 1, '2024-03-20 12:05:54', '2024-04-13 10:47:26'),
(2, 'Science 5', 'experiment ni bai', 'YS 6: Grade 5', 1, '2024-03-20 12:11:51', '2024-03-20 12:12:04'),
(3, 'Araling Panlipunan 6', 'dunno', 'YS 7: Grade 6', 1, '2024-03-20 12:45:40', '2024-03-20 12:45:52'),
(5, 'English', '', 'Junior Nursery', 1, '2024-03-26 09:22:40', '2024-04-22 02:51:52'),
(6, 'Rhymes', '', 'Junior Nursery', 1, '2024-03-26 09:23:27', '2024-03-26 09:23:27'),
(7, 'Writing', '', 'Junior Nursery', 1, '2024-03-26 09:24:17', '2024-03-26 09:24:17'),
(8, 'Filipino 4', '', 'YS 5: Grade 4', 1, '2024-04-04 01:01:34', '2024-04-04 01:01:34'),
(9, 'English 4', '', 'YS 5: Grade 4', 1, '2024-04-04 01:02:03', '2024-04-04 01:02:03'),
(10, 'Mathematics 4', '', 'YS 5: Grade 4', 1, '2024-04-04 01:02:17', '2024-04-04 01:02:17'),
(11, 'Science 4', '', 'YS 5: Grade 4', 1, '2024-04-04 01:02:53', '2024-04-04 01:02:53'),
(12, 'Araling Panlipunan 4', '', 'YS 5: Grade 4', 1, '2024-04-04 01:03:11', '2024-04-04 01:03:11'),
(13, 'Edukasyon sa Pagpapakatao 4', '', 'YS 5: Grade 4', 1, '2024-04-04 01:03:40', '2024-04-04 01:03:40'),
(14, 'MAPEH 4', '', 'YS 5: Grade 4', 1, '2024-04-04 01:04:10', '2024-04-04 01:04:10'),
(15, 'Edukasyong Pantahanan at Pangkabuhayan 4', '', 'YS 5: Grade 4', 1, '2024-04-04 01:04:30', '2024-04-04 01:04:30'),
(16, 'Araling Panlipunan 5', '', 'YS 6: Grade 5', 1, '2024-04-18 03:42:10', '2024-04-18 03:42:10'),
(18, 'Math 3', '', 'YS 4: Grade 3', 1, '2024-05-15 06:55:08', '2024-05-15 06:55:08'),
(19, 'Filipino 3', '', 'YS 4: Grade 3', 1, '2024-05-15 06:55:29', '2024-05-15 06:55:29'),
(20, 'Araling Panlipunan 3', '', 'YS 4: Grade 3', 1, '2024-05-15 06:55:48', '2024-05-15 06:55:48'),
(22, 'English 2', '', 'YS 3: Grade 2', 1, '2024-05-27 04:34:54', '2024-05-27 04:34:54'),
(23, 'Filipino 2', '', 'YS 3: Grade 2', 1, '2024-05-27 04:35:10', '2024-05-27 04:35:10'),
(24, 'Araling Panlipunan 2', '', 'YS 3: Grade 2', 1, '2024-05-27 04:35:29', '2024-05-27 04:35:29'),
(25, 'English 6', '', 'YS 7: Grade 6', 1, '2024-08-20 10:59:19', '2024-08-20 10:59:19'),
(26, 'Math 6', '', 'YS 7: Grade 6', 1, '2024-12-03 03:06:02', '2024-12-03 03:06:02');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `TeacherID` int(11) NOT NULL,
  `FirstName` varchar(255) NOT NULL,
  `MiddleName` varchar(255) DEFAULT NULL,
  `LastName` varchar(255) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`TeacherID`, `FirstName`, `MiddleName`, `LastName`, `Username`, `Password`) VALUES
(1, 'Harry', 'Evans', 'Potter', 'thechosenone', '$2y$10$CdONOpXVq1RSfWgMsKVGFuQoeI8QDUcvVrlU6V.jRbqrVVgd9ymJa'),
(2, 'Cloud', 'Jones', 'Strife', 'materiaowner', '$2y$10$OhsoYM1FpfQfaJANKSsq4eZgiZJtTma8Y2/8tCFD.FRwBHMfJocPC'),
(4, 'Rosalie', 'Elisio', 'Mondejar', 'RosalieM', '$2y$10$utVYFJiTF8K1E0f2U7SFcuueKEEZQGmnfmK9lG2swTHnbo0c8IZta'),
(5, 'Donald', 'McLeod', 'Trump', 'elephant', '$2y$10$URZqtblksnZDbpwI7.67EO5dSuSfrKaPACXqZOSOeXd7ec5InCAg6'),
(6, 'John', 'the', 'Baptist', 'delasalle', '$2y$10$dIXtnjxspFFsyBOQ.0207erZlvxAVtKzUmoNgBHs3cP7pCgeN29pC');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`AdminID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indexes for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD PRIMARY KEY (`EnrollmentID`),
  ADD UNIQUE KEY `idx_student_offering` (`StudentID`,`OfferingID`),
  ADD KEY `OfferingID` (`OfferingID`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`GradeID`),
  ADD UNIQUE KEY `idx_enrollment_subject` (`EnrollmentID`,`SubjectID`),
  ADD KEY `SubjectID` (`SubjectID`);

--
-- Indexes for table `offerings`
--
ALTER TABLE `offerings`
  ADD PRIMARY KEY (`OfferingID`),
  ADD KEY `SubjectID` (`SubjectID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_EnrollmentID` (`EnrollmentID`),
  ADD KEY `fk_StudentID` (`StudentID`);

--
-- Indexes for table `paymenthistory`
--
ALTER TABLE `paymenthistory`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `payment_id` (`payment_id`),
  ADD KEY `StudentID` (`StudentID`),
  ADD KEY `EnrollmentID` (`EnrollmentID`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `StudentID` (`StudentID`);

--
-- Indexes for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD PRIMARY KEY (`PaymentHistoryID`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`StudentID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`SubjectID`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`TeacherID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `enrollment`
--
ALTER TABLE `enrollment`
  MODIFY `EnrollmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `GradeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT for table `offerings`
--
ALTER TABLE `offerings`
  MODIFY `OfferingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `paymenthistory`
--
ALTER TABLE `paymenthistory`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `payment_history`
--
ALTER TABLE `payment_history`
  MODIFY `PaymentHistoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `StudentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `SubjectID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `TeacherID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD CONSTRAINT `enrollment_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `students` (`StudentID`),
  ADD CONSTRAINT `enrollment_ibfk_2` FOREIGN KEY (`OfferingID`) REFERENCES `offerings` (`OfferingID`);

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`EnrollmentID`) REFERENCES `enrollment` (`EnrollmentID`),
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`SubjectID`) REFERENCES `subjects` (`SubjectID`);

--
-- Constraints for table `offerings`
--
ALTER TABLE `offerings`
  ADD CONSTRAINT `offerings_ibfk_1` FOREIGN KEY (`SubjectID`) REFERENCES `subjects` (`SubjectID`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk_EnrollmentID` FOREIGN KEY (`EnrollmentID`) REFERENCES `enrollment` (`EnrollmentID`),
  ADD CONSTRAINT `fk_StudentID` FOREIGN KEY (`StudentID`) REFERENCES `students` (`StudentID`);

--
-- Constraints for table `paymenthistory`
--
ALTER TABLE `paymenthistory`
  ADD CONSTRAINT `paymenthistory_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`),
  ADD CONSTRAINT `paymenthistory_ibfk_2` FOREIGN KEY (`StudentID`) REFERENCES `students` (`StudentID`),
  ADD CONSTRAINT `paymenthistory_ibfk_3` FOREIGN KEY (`EnrollmentID`) REFERENCES `enrollment` (`EnrollmentID`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `students` (`StudentID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
