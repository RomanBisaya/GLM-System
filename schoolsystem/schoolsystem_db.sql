-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2024 at 02:09 PM
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
  `StudentID` int(11) DEFAULT NULL,
  `OfferingID` int(11) DEFAULT NULL,
  `EnrollmentDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 3, '1st Semester', '2024-2025', ''),
(2, 1, '1st Semester', '2024-2025', '8am - 9am'),
(3, 2, '1st Semester', '2024-2025', '8am - 9am'),
(4, 1, '1st Semester', '2023-2024', '1pm - 2pm'),
(5, 2, '1st Semester', '2023-2024', '1pm - 2pm'),
(6, 1, '1st Semester', '2022-2023', '9am-10am'),
(7, 2, '1st Semester', '2022-2023', '1pm-2pm');

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
  `SchoolLevel` varchar(50) DEFAULT NULL,
  `Username` varchar(50) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Status` varchar(10) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`StudentID`, `FirstName`, `MiddleName`, `LastName`, `BirthDate`, `SpecialNeedsDetails`, `ParentGuardianContactInfo`, `SchoolLevel`, `Username`, `Password`, `Status`) VALUES
(6, 'Garfield', 'Holland', 'Wayne', NULL, NULL, NULL, 'YS 7: Grade 6', 'orangeming', '$2y$10$asimqxa0vfTq2oIDpL6fDeooR.p16ZkmQEE1FZhwXt2md1cd9vnzi', 'Active'),
(7, 'Vertin', 'Gonzalez', 'Tan', NULL, NULL, NULL, 'YS 6: Grade 5', 'arcanist', '$2y$10$wrPQqxmrM2hxYjAnLZFds.zC1JZfllMkuzz9PVYb3Xa7b3vaX4ise', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `SubjectID` int(11) NOT NULL,
  `SubjectName` varchar(100) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `SchoolLevel` varchar(50) DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`SubjectID`, `SubjectName`, `Description`, `SchoolLevel`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'Filipino 5', 'Tagalog ni', 'YS 6: Grade 5', 1, '2024-03-20 12:05:54', '2024-03-20 12:10:46'),
(2, 'Science 5', 'experiment ni bai', 'YS 6: Grade 5', 1, '2024-03-20 12:11:51', '2024-03-20 12:12:04'),
(3, 'Araling Panlipunan 6', 'dunno', 'YS 7: Grade 6', 1, '2024-03-20 12:45:40', '2024-03-20 12:45:52');

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
  ADD KEY `StudentID` (`StudentID`),
  ADD KEY `OfferingID` (`OfferingID`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`GradeID`),
  ADD KEY `EnrollmentID` (`EnrollmentID`),
  ADD KEY `SubjectID` (`SubjectID`);

--
-- Indexes for table `offerings`
--
ALTER TABLE `offerings`
  ADD PRIMARY KEY (`OfferingID`),
  ADD KEY `SubjectID` (`SubjectID`);

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
  MODIFY `EnrollmentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `GradeID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offerings`
--
ALTER TABLE `offerings`
  MODIFY `OfferingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `StudentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `SubjectID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
