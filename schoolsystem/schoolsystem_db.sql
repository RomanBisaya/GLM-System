-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2024 at 08:41 PM
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
(1, 'Forrest', 'Gump', 'theadmin', '$2y$10$yBVvri4bWnpqj6JPHRcpMep8Nv0ybZRgYbe7E96lg3yVOQ3l3tsRe');

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
(74, 13, 56, '2024-05-12', 'Active', 'YS 5: Grade 4', 1);

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
(58, 39, 10, NULL, 'Good'),
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
(104, 74, 15, NULL, 'Very Good');

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
(56, 15, '1st Semester', '2024-2025', '1pm - 2pm TTh');

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
  `PaymentStatus` enum('Fully Paid','Partially Paid','Not Paid') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`PaymentID`, `StudentID`, `Amount`, `AmountPaid`, `StartDate`, `EndDate`, `PaymentStatus`) VALUES
(1, 9, 3000.00, 3000.00, '2024-06-03', '2024-07-08', 'Fully Paid'),
(2, 10, 3000.00, 3000.00, '2024-05-06', '2024-06-03', 'Fully Paid'),
(3, 13, 3000.00, 3000.00, '2024-05-06', '2024-06-03', 'Fully Paid'),
(6, 7, 1000.00, 500.00, '2024-05-06', '2024-06-03', 'Partially Paid');

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
(13, 'Asa', 'Gubat', 'Mitaka', NULL, NULL, NULL, 'femcel', '$2y$10$myV5TK30aoNH7.8TkqfWXOuRgqrCtle2vfyCADVhxVHaRqHy89IvG', 'Active');

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
(16, 'Araling Panlipunan 5', '', 'YS 6: Grade 5', 1, '2024-04-18 03:42:10', '2024-04-18 03:42:10');

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
(1, 'Harry', 'Evans', 'Potter', 'chosenoone', '$2y$10$CdONOpXVq1RSfWgMsKVGFuQoeI8QDUcvVrlU6V.jRbqrVVgd9ymJa'),
(2, 'Cloud', 'Jones', 'Strife', 'materiaowner', '$2y$10$OhsoYM1FpfQfaJANKSsq4eZgiZJtTma8Y2/8tCFD.FRwBHMfJocPC');

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
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `StudentID` (`StudentID`);

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
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enrollment`
--
ALTER TABLE `enrollment`
  MODIFY `EnrollmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `GradeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `offerings`
--
ALTER TABLE `offerings`
  MODIFY `OfferingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `StudentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `SubjectID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `TeacherID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `students` (`StudentID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
