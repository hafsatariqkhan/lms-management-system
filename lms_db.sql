-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2026 at 06:41 PM
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
-- Database: `lms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Admin_ID` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Admin_ID`, `Password`) VALUES
('admin', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `assignment`
--

CREATE TABLE `assignment` (
  `Assignment_ID` varchar(10) NOT NULL,
  `Assignment_Title` varchar(255) DEFAULT NULL,
  `Course_ID` varchar(10) DEFAULT NULL,
  `Due_Date` date DEFAULT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignment`
--

INSERT INTO `assignment` (`Assignment_ID`, `Assignment_Title`, `Course_ID`, `Due_Date`, `Description`) VALUES
('', 'Make a Table Student', 'CS501', '2026-05-24', 'Make a table student and add columns'),
('A701', 'OS Process Scheduling', 'CS502', '2026-05-25', 'Solve the CPU scheduling problems (FIFO, Round Robin) given in the PDF.'),
('A702', 'Deadlock Avoidance Lab', 'CS502', '2026-05-30', 'Implement Bankers Algorithm in C or C++ and upload the code file.'),
('A703', 'SQL Complex Queries', 'CS501', '2026-05-28', 'Write SQL queries for the given database schema.');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `Attendance_ID` int(11) NOT NULL,
  `Std_ID` varchar(10) NOT NULL,
  `Course_ID` varchar(10) NOT NULL,
  `Attendance_Date` date NOT NULL,
  `Status` enum('Present','Absent') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`Attendance_ID`, `Std_ID`, `Course_ID`, `Attendance_Date`, `Status`) VALUES
(1, '101', 'CS501', '2026-05-15', 'Present'),
(2, '101', 'CS501', '2026-05-16', 'Present'),
(3, '101', 'CS501', '2026-05-17', 'Absent'),
(4, '102', 'DS503', '2026-05-15', 'Present'),
(5, '103', 'CS502', '0000-00-00', 'Present'),
(6, '104', 'CS501', '2026-05-15', 'Absent'),
(7, '105', 'E504', '2026-05-15', 'Present');

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `Course_ID` varchar(10) NOT NULL,
  `Course_Name` varchar(255) DEFAULT NULL,
  `Credit_Hours` int(11) DEFAULT 3
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`Course_ID`, `Course_Name`, `Credit_Hours`) VALUES
('AI507', 'Artificial Intelligence', 4),
('BBA507', 'Principles of Marketing', 3),
('BBA508', 'Financial Accounting', 3),
('CS501', 'Advanced Database Systems', 3),
('CS502', 'Operating Systems', 3),
('DS503', 'Data Mining Techniques', 3),
('E504', 'Literature', 3),
('SE505', 'Software Architecture & Design', 3),
('SE506', 'DevOps & Cloud Computing', 3);

-- --------------------------------------------------------

--
-- Table structure for table `course_allocation`
--

CREATE TABLE `course_allocation` (
  `Allocation_ID` int(11) NOT NULL,
  `Instructor_ID` varchar(50) DEFAULT NULL,
  `Course_ID` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_allocation`
--

INSERT INTO `course_allocation` (`Allocation_ID`, `Instructor_ID`, `Course_ID`) VALUES
(4, 'I306', 'BBA508');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment`
--

CREATE TABLE `enrollment` (
  `Std_ID` varchar(10) NOT NULL,
  `Course_ID` varchar(10) NOT NULL,
  `Enrollment_Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollment`
--

INSERT INTO `enrollment` (`Std_ID`, `Course_ID`, `Enrollment_Date`) VALUES
('101', 'CS501', '2026-02-15'),
('101', 'CS502', '2026-02-15'),
('102', 'DS503', '2026-02-15'),
('103', 'CS502', '2026-02-15'),
('104', 'CS501', '2026-02-15'),
('105', 'E504', '2026-02-15'),
('106', 'SE505', '2026-02-15'),
('107', 'SE505', '2026-02-15'),
('108', 'BBA507', '2026-02-15');

-- --------------------------------------------------------

--
-- Table structure for table `instructor`
--

CREATE TABLE `instructor` (
  `Instructor_ID` varchar(10) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Department` varchar(50) NOT NULL,
  `Joining_Date` date DEFAULT NULL,
  `Leaving_Date` date DEFAULT NULL,
  `Designation` varchar(50) DEFAULT 'Lecturer',
  `Qualification` varchar(50) DEFAULT 'MSCS',
  `Phone` varchar(20) DEFAULT '0300-1234567',
  `CNIC` varchar(20) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `Gender` varchar(10) DEFAULT NULL,
  `Status` varchar(20) DEFAULT 'Active',
  `Registration_Date` date DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instructor`
--

INSERT INTO `instructor` (`Instructor_ID`, `Name`, `Email`, `Department`, `Joining_Date`, `Leaving_Date`, `Designation`, `Qualification`, `Phone`, `CNIC`, `Address`, `Gender`, `Status`, `Registration_Date`, `profile_pic`) VALUES
('I301', 'Dr. Imran Khan', 'imran.khan@numl.edu.pk', 'English', '2024-01-15', NULL, 'Assistant Professor', 'PhD in English Literature', '0301-8765432', '42101-1234567-1', 'House 12-B, Gulshan-e-Iqbal, Block 13-D, Karachi', 'Male', 'Active', '2026-01-10', NULL),
('I302', 'Dr. Ayesha Siddiqui', 'ayesha.s@numl.edu.pk', 'Computer Science', '2024-02-10', NULL, 'Associate Professor', 'PhD in Computer Science', '0321-9876543', '42201-9876543-2', 'Flat 402, Clifton Block 4, Karachi', 'Female', 'Active', '2026-01-15', NULL),
('I303', 'Mr. Ahmed Raza', 'ahmed.r@numl.edu.pk', 'Computer Science', '2023-09-05', NULL, 'Lecturer', 'MSCS (Data Science)', '0333-5554433', '42301-5554433-3', 'House 88, KDA Scheme 1, Karachi', 'Male', 'Active', '2026-02-01', NULL),
('I304', 'Moazzam Ali', 'mozzam.a@numl.edu.pk', 'Computer Science', '2023-11-20', NULL, 'Senior Lecturer', 'MSCS (Software Engineering)', '0345-1122334', '42401-1122334-4', 'Plot 45, North Nazimabad, Block L, Karachi', 'Male', 'Active', '2026-02-20', NULL),
('I305', 'Dr. Fahad Ansari', 'fahad.m@numl.edu.pk', 'Software Engineering', '2024-03-01', NULL, 'Assistant Professor', 'PhD in Software Engineering', '0312-4455667', '42501-4455667-5', 'House 22, Phase 6, DHA, Karachi', 'Male', 'Active', '2026-03-05', NULL),
('I306', 'Eman Khan ', 'emank106@gmail.com', 'Business Administration', '2024-02-16', '0000-00-00', 'Lecturer', 'MBA', '03126782321', '41304-7760891-0', 'B-321 Sector 5 D Surjani Town', 'Female', 'Active', '2026-05-21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE `module` (
  `Module_ID` varchar(10) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Course_ID` varchar(10) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `File_Name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `module`
--

INSERT INTO `module` (`Module_ID`, `Name`, `Course_ID`, `Description`, `File_Name`) VALUES
('', 'ER Diagram', 'CS501', 'Here is your ER Diagram', '1779519928_ER diagram.png');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `Notification_ID` int(11) NOT NULL,
  `Std_ID` varchar(50) NOT NULL,
  `Message` text NOT NULL,
  `Status` varchar(20) DEFAULT 'unread',
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp(),
  `Assignment_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`Notification_ID`, `Std_ID`, `Message`, `Status`, `Created_At`, `Assignment_ID`) VALUES
(7, '101', '📁 New Material Uploaded: ER Diagram  has been added in your course Advanced Database Systems.', 'read', '2026-05-17 08:26:41', NULL),
(8, '104', '📁 New Material Uploaded: ER Diagram  has been added in your course Advanced Database Systems.', 'unread', '2026-05-17 08:26:41', NULL),
(9, '101', '🎯 Your assignment \'OS Process Scheduling\' has been graded! Grade: 85/100.', 'read', '2026-05-17 23:27:21', NULL),
(10, '101', '🎯 Your assignment \'OS Process Scheduling\' has been graded! Grade: 85/100.', 'read', '2026-05-18 04:50:59', NULL),
(11, '101', '📝 New Assignment Alert: \'Make a Table Student\' has been posted in Advanced Database Systems. Due Date: 24-May-2026', 'read', '2026-05-23 06:33:07', NULL),
(12, '104', '📝 New Assignment Alert: \'Make a Table Student\' has been posted in Advanced Database Systems. Due Date: 24-May-2026', 'unread', '2026-05-23 06:33:07', NULL),
(13, '101', '📁 New Material Uploaded: ER Diagram has been added in your course Advanced Database Systems.', 'read', '2026-05-23 07:05:28', NULL),
(14, '104', '📁 New Material Uploaded: ER Diagram has been added in your course Advanced Database Systems.', 'unread', '2026-05-23 07:05:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `Std_ID` varchar(10) NOT NULL,
  `Registration_Date` date DEFAULT NULL,
  `Name` varchar(50) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Department` varchar(50) NOT NULL,
  `Program` varchar(50) NOT NULL DEFAULT 'BSCS',
  `Admission_Year` int(4) NOT NULL DEFAULT 2026,
  `Academic_Session` varchar(20) NOT NULL DEFAULT 'Fall',
  `Profile_Pic` varchar(255) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `CNIC` varchar(20) DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `Semester` int(11) DEFAULT 4,
  `Shift` varchar(20) NOT NULL DEFAULT 'Morning',
  `Gender` varchar(10) NOT NULL DEFAULT 'Male',
  `Graduation_Year` int(4) NOT NULL DEFAULT 2030,
  `Status` varchar(20) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`Std_ID`, `Registration_Date`, `Name`, `Email`, `Department`, `Program`, `Admission_Year`, `Academic_Session`, `Profile_Pic`, `Phone`, `CNIC`, `DOB`, `Semester`, `Shift`, `Gender`, `Graduation_Year`, `Status`) VALUES
('101', '2023-09-01', 'Fatima Zehra', 'fatima.zehra@gmail.com', 'Computer Science', 'BSCS', 2023, 'Fall', NULL, '03001234567', '42101-2345678-2', '2005-04-12', 7, 'Morning', 'Female', 2027, 'Active'),
('102', '2023-09-01', 'Ali Khan', 'ali.k@gmail.com', 'Data Science', 'BSCS', 2023, 'Fall', NULL, '03219876543', '42101-8765432-1', '2004-11-18', 7, 'Evening', 'Male', 2027, 'Active'),
('103', '2024-09-03', 'Sarah Ahmed', 'sara.a@gmail.com', 'Computer Science', 'BSCS', 2024, 'Fall', NULL, '03334567890', '42101-3456789-4', '2006-01-22', 5, 'Morning', 'Female', 2028, 'Active'),
('104', '2024-09-04', 'Sana Shah', 'sana.shah@gmail.com', 'Computer Science', 'BSCS', 2024, 'Fall', NULL, '03123456789', '42101-9876543-6', '2005-08-05', 5, 'Morning', 'Female', 2028, 'Active'),
('105', '2024-09-05', 'Huzaifa Farhan', 'huzaifa20@gmail.com', 'English', 'BSCS', 2024, 'Fall', NULL, '03456789012', '42101-4567890-7', '2006-03-14', 5, 'Morning', 'Male', 2028, 'Active'),
('106', '2025-09-08', 'Zainab Malik', 'zainab.m@gmail.com', 'Software Engineering', 'BSCS', 2025, 'Fall', NULL, '03012345678', '42101-5678901-8', '2007-12-25', 3, 'Morning', 'Female', 2029, 'Active'),
('107', '2025-09-09', 'Bilal Raza', 'bilal.raza@gmail.com', 'Software Engineering', 'BSCS', 2025, 'Fall', NULL, '03223456789', '42101-6789012-9', '2006-07-19', 3, 'Morning', 'Male', 2029, 'Active'),
('108', NULL, 'Eshal Fatima', 'eshal.f@gmail.com', 'Business Administration', 'BSCS', 2026, 'Fall', NULL, '03345678901', NULL, NULL, 4, 'Morning', 'Male', 2030, 'Active'),
('109', '2025-09-10', 'Hamza Jameel', 'hamzajameel456@gmail.com', 'BBA', 'BSCS', 2025, 'Fall', NULL, '03156789012', '42101-7890123-3', '2006-10-30', 3, 'Morning', 'Male', 2029, 'Active'),
('110', '2026-05-20', 'Hafsa Tariq', 'hafsatariq.cs@gmail.com', 'Computer Science', 'BSCS', 2024, 'Fall', NULL, '03368271814', '41304-4720771-0', '2006-12-07', 4, 'Morning', 'Female', 2028, 'Active'),
('112', '2026-05-23', 'Subhan Khan ', 'subhank25@gmail.com', 'Information Technology', 'BSIT', 2026, 'Fall', NULL, '03124567890', '42101-0395698-3', '2005-03-17', 1, 'Morning', 'Male', 2030, 'Active'),
('113', '2026-05-23', 'Parishay Farhan', 'parishayfarhan18@gmail.com', 'Computer Science', 'BSCS', 2025, 'Fall', NULL, '03219876543', '42101-2345678-2', '2009-08-18', 4, 'Morning', 'Female', 2029, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `submission`
--

CREATE TABLE `submission` (
  `Submission_ID` int(11) NOT NULL,
  `Std_ID` varchar(10) DEFAULT NULL,
  `Assignment_ID` varchar(10) DEFAULT NULL,
  `Submitted_Date` date DEFAULT NULL,
  `Status` varchar(20) DEFAULT 'Pending',
  `File_Name` varchar(255) DEFAULT NULL,
  `Grade` varchar(20) DEFAULT NULL,
  `Feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submission`
--

INSERT INTO `submission` (`Submission_ID`, `Std_ID`, `Assignment_ID`, `Submitted_Date`, `Status`, `File_Name`, `Grade`, `Feedback`) VALUES
(1, '101', 'A701', '2026-05-18', 'Submitted', 'COAL notes.pdf', '85/100', 'Good Work');

-- --------------------------------------------------------

--
-- Table structure for table `teaches`
--

CREATE TABLE `teaches` (
  `Instructor_ID` varchar(10) NOT NULL,
  `Course_ID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teaches`
--

INSERT INTO `teaches` (`Instructor_ID`, `Course_ID`) VALUES
('I301', 'E504'),
('I302', 'CS501'),
('I302', 'CS502'),
('I303', 'DS503'),
('I305', 'SE505');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `role` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `semester` varchar(20) DEFAULT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Admin_ID`);

--
-- Indexes for table `assignment`
--
ALTER TABLE `assignment`
  ADD PRIMARY KEY (`Assignment_ID`),
  ADD KEY `Course_ID` (`Course_ID`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`Attendance_ID`),
  ADD KEY `Std_ID` (`Std_ID`),
  ADD KEY `Course_ID` (`Course_ID`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`Course_ID`);

--
-- Indexes for table `course_allocation`
--
ALTER TABLE `course_allocation`
  ADD PRIMARY KEY (`Allocation_ID`),
  ADD KEY `Instructor_ID` (`Instructor_ID`),
  ADD KEY `Course_ID` (`Course_ID`);

--
-- Indexes for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD PRIMARY KEY (`Std_ID`,`Course_ID`),
  ADD KEY `Course_ID` (`Course_ID`);

--
-- Indexes for table `instructor`
--
ALTER TABLE `instructor`
  ADD PRIMARY KEY (`Instructor_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`Module_ID`),
  ADD KEY `Course_ID` (`Course_ID`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`Notification_ID`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`Std_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `submission`
--
ALTER TABLE `submission`
  ADD PRIMARY KEY (`Submission_ID`),
  ADD KEY `Std_ID` (`Std_ID`),
  ADD KEY `Assignment_ID` (`Assignment_ID`);

--
-- Indexes for table `teaches`
--
ALTER TABLE `teaches`
  ADD PRIMARY KEY (`Instructor_ID`,`Course_ID`),
  ADD KEY `Course_ID` (`Course_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `Attendance_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `course_allocation`
--
ALTER TABLE `course_allocation`
  MODIFY `Allocation_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `Notification_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `submission`
--
ALTER TABLE `submission`
  MODIFY `Submission_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignment`
--
ALTER TABLE `assignment`
  ADD CONSTRAINT `assignment_ibfk_1` FOREIGN KEY (`Course_ID`) REFERENCES `course` (`Course_ID`);

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`Std_ID`) REFERENCES `student` (`Std_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`Course_ID`) REFERENCES `course` (`Course_ID`) ON DELETE CASCADE;

--
-- Constraints for table `course_allocation`
--
ALTER TABLE `course_allocation`
  ADD CONSTRAINT `course_allocation_ibfk_1` FOREIGN KEY (`Instructor_ID`) REFERENCES `instructor` (`Instructor_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_allocation_ibfk_2` FOREIGN KEY (`Course_ID`) REFERENCES `course` (`Course_ID`) ON DELETE CASCADE;

--
-- Constraints for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD CONSTRAINT `enrollment_ibfk_1` FOREIGN KEY (`Std_ID`) REFERENCES `student` (`Std_ID`),
  ADD CONSTRAINT `enrollment_ibfk_2` FOREIGN KEY (`Course_ID`) REFERENCES `course` (`Course_ID`);

--
-- Constraints for table `module`
--
ALTER TABLE `module`
  ADD CONSTRAINT `module_ibfk_1` FOREIGN KEY (`Course_ID`) REFERENCES `course` (`Course_ID`);

--
-- Constraints for table `submission`
--
ALTER TABLE `submission`
  ADD CONSTRAINT `submission_ibfk_1` FOREIGN KEY (`Std_ID`) REFERENCES `student` (`Std_ID`),
  ADD CONSTRAINT `submission_ibfk_2` FOREIGN KEY (`Assignment_ID`) REFERENCES `assignment` (`Assignment_ID`);

--
-- Constraints for table `teaches`
--
ALTER TABLE `teaches`
  ADD CONSTRAINT `teaches_ibfk_1` FOREIGN KEY (`Instructor_ID`) REFERENCES `instructor` (`Instructor_ID`),
  ADD CONSTRAINT `teaches_ibfk_2` FOREIGN KEY (`Course_ID`) REFERENCES `course` (`Course_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
