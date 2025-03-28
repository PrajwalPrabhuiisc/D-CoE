-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 23, 2025 at 04:20 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `diary_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `analyticsreports`
--

CREATE TABLE `analyticsreports` (
  `ReportID` int(11) NOT NULL,
  `ReportType` varchar(50) NOT NULL,
  `Data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`Data`)),
  `GeneratedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `analyticsreports`
--

INSERT INTO `analyticsreports` (`ReportID`, `ReportType`, `Data`, `GeneratedDate`) VALUES
(1, 'Task Status Summary', '{\"Not Started\": 2, \"In Progress\": 3, \"Completed\": 4, \"Blocked\": 1}', '2025-03-21 20:00:00'),
(2, 'Time Deviation Trend', '{\"2025-03-20\": 20, \"2025-03-21\": 15}', '2025-03-21 20:05:00'),
(3, 'Task Distribution', '{\"john_doe\": 3, \"jane_smith\": 3, \"mike_brown\": 2, \"emily_clark\": 1}', '2025-03-21 20:10:00');

-- --------------------------------------------------------

--
-- Table structure for table `diarysubmissions`
--

CREATE TABLE `diarysubmissions` (
  `SubmissionID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `EntryDate` date NOT NULL DEFAULT curdate(),
  `Submitted` tinyint(1) DEFAULT 0,
  `SubmissionTime` datetime DEFAULT NULL,
  `ReminderSent` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diarysubmissions`
--

INSERT INTO `diarysubmissions` (`SubmissionID`, `UserID`, `EntryDate`, `Submitted`, `SubmissionTime`, `ReminderSent`) VALUES
(1, 1, '2025-03-20', 1, '2025-03-20 17:30:00', 0),
(2, 2, '2025-03-20', 1, '2025-03-20 18:00:00', 0),
(3, 4, '2025-03-20', 0, NULL, 0),
(4, 1, '2025-03-21', 1, '2025-03-21 19:00:00', 0),
(5, 2, '2025-03-21', 1, '2025-03-21 18:45:00', 0),
(6, 3, '2025-03-21', 1, '2025-03-21 17:00:00', 0),
(7, 4, '2025-03-21', 1, '2025-03-21 19:30:00', 0),
(8, 5, '2025-03-21', 0, NULL, 1),
(9, 1, '2025-03-22', 0, NULL, 0),
(10, 2, '2025-03-22', 1, '2025-03-22 16:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `ProjectID` int(11) NOT NULL,
  `ProjectName` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`ProjectID`, `ProjectName`, `Description`, `CreatedAt`) VALUES
(1, 'Module X Development', 'Core functionality for system X', '2025-03-22 12:21:02'),
(2, 'System Y Upgrade', 'Performance improvements', '2025-03-22 12:21:02'),
(3, 'Bug Fixing Initiative', 'Resolve critical bugs', '2025-03-22 12:21:02');

-- --------------------------------------------------------

--
-- Table structure for table `projecttasks`
--

CREATE TABLE `projecttasks` (
  `TaskID` int(11) NOT NULL,
  `ProjectID` int(11) NOT NULL,
  `TaskName` varchar(100) NOT NULL,
  `Description` varchar(500) DEFAULT NULL,
  `OwnerID` int(11) NOT NULL,
  `Status` enum('Pending','Active','Completed') DEFAULT 'Pending',
  `Priority` enum('Low','Medium','High') DEFAULT 'Medium',
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projecttasks`
--

INSERT INTO `projecttasks` (`TaskID`, `ProjectID`, `TaskName`, `Description`, `OwnerID`, `Status`, `Priority`, `StartDate`, `EndDate`, `CreatedAt`, `UpdatedAt`) VALUES
(2, 1, 'Write Unit Tests', 'Ensure code coverage', 2, 'Completed', 'Medium', '2025-03-20', '2025-03-21', '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(3, 1, 'Optimize Module X', 'Improve performance', 1, 'Pending', 'Medium', '2025-03-23', '2025-03-25', '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(4, 2, 'Upgrade Server', 'Increase capacity', 5, 'Active', 'High', '2025-03-21', '2025-03-24', '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(5, 2, 'Test System Y', 'Validate upgrades', 5, 'Pending', 'Medium', '2025-03-24', '2025-03-26', '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(6, 2, 'Document APIs', 'API reference', 2, 'Completed', 'Low', '2025-03-21', '2025-03-22', '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(7, 3, 'Fix Bug #123', 'Crash on login', 4, 'Active', 'High', '2025-03-20', '2025-03-22', '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(9, 1, 'Design UI', 'Create mockups', 4, 'Pending', 'Medium', '2025-03-21', '2025-03-24', '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(10, 2, 'Review Code', 'Code quality check', 2, 'Active', 'Low', '2025-03-21', '2025-03-23', '2025-03-22 12:21:02', '2025-03-22 15:21:53'),
(11, 1, 'Debug Module X', NULL, 1, 'Active', 'Medium', '2025-03-22', NULL, '2025-03-22 14:45:22', '2025-03-22 15:33:28'),
(12, 2, 'Review Code', NULL, 2, 'Active', 'Low', '2025-03-22', NULL, '2025-03-22 22:43:42', '2025-03-22 22:44:41'),
(13, 1, 'Debug Module X', NULL, 1, 'Pending', 'Low', NULL, NULL, '2025-03-22 22:44:23', '2025-03-22 22:44:23'),
(14, 1, 'Review Code', NULL, 1, 'Pending', 'Low', NULL, NULL, '2025-03-22 22:45:42', '2025-03-22 22:45:42');

-- --------------------------------------------------------

--
-- Table structure for table `sateamobservations`
--

CREATE TABLE `sateamobservations` (
  `ObservationID` int(11) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `UserID` int(11) NOT NULL,
  `ObservationDate` date NOT NULL DEFAULT curdate(),
  `Details` text NOT NULL,
  `Category` varchar(50) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sateamobservations`
--

INSERT INTO `sateamobservations` (`ObservationID`, `Name`, `UserID`, `ObservationDate`, `Details`, `Category`, `CreatedAt`) VALUES
(1, NULL, 3, '2025-03-20', 'Team needs better debugging tools', 'Process', '2025-03-22 12:21:02'),
(2, NULL, 3, '2025-03-20', 'UI design delays due to feedback', 'Project', '2025-03-22 12:21:02'),
(3, NULL, 3, '2025-03-21', 'Good sprint planning session', 'Team', '2025-03-22 12:21:02'),
(4, NULL, 3, '2025-03-21', 'Server downtime affecting testing', 'Infrastructure', '2025-03-22 12:21:02'),
(5, NULL, 3, '2025-03-22', 'Code quality improving', 'Code', '2025-03-22 12:21:02'),
(6, NULL, 3, '2025-03-22', 'testing this field', 'Process Improvement', '2025-03-22 22:20:21');

-- --------------------------------------------------------

--
-- Table structure for table `taskdependencies`
--

CREATE TABLE `taskdependencies` (
  `DependencyID` int(11) NOT NULL,
  `TaskID` int(11) NOT NULL,
  `PredecessorID` int(11) NOT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `taskdependencies`
--

INSERT INTO `taskdependencies` (`DependencyID`, `TaskID`, `PredecessorID`, `CreatedAt`) VALUES
(2, 5, 4, '2025-03-22 12:21:02'),
(5, 10, 6, '2025-03-22 12:21:02'),
(7, 5, 7, '2025-03-22 14:52:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Role` varchar(50) DEFAULT 'Team Member'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Username`, `Email`, `Role`) VALUES
(1, 'john_doe', 'john@example.com', 'Team Member'),
(2, 'jane_smith', 'jane@example.com', 'Team Member'),
(3, 'sa_admin', 'sa_admin@example.com', 'SA Team'),
(4, 'mike_brown', 'mike@example.com', 'Team Member'),
(5, 'emily_clark', 'emily@example.com', 'Team Member');

-- --------------------------------------------------------

--
-- Table structure for table `workdiary`
--

CREATE TABLE `workdiary` (
  `EntryID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `EntryDate` date NOT NULL DEFAULT curdate(),
  `TaskDescription` varchar(500) NOT NULL,
  `TaskOwnerID` int(11) NOT NULL,
  `TaskStatus` enum('Not Started','In Progress','Completed','Blocked') DEFAULT 'Not Started',
  `AllocatedTime` int(11) NOT NULL,
  `ActualTime` int(11) DEFAULT NULL,
  `DeviationReason` text DEFAULT NULL,
  `PersonalInsights` text DEFAULT NULL,
  `Commitments` text DEFAULT NULL,
  `GeneralObservations` text DEFAULT NULL,
  `ImprovementSuggestions` text DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workdiary`
--

INSERT INTO `workdiary` (`EntryID`, `UserID`, `EntryDate`, `TaskDescription`, `TaskOwnerID`, `TaskStatus`, `AllocatedTime`, `ActualTime`, `DeviationReason`, `PersonalInsights`, `Commitments`, `GeneralObservations`, `ImprovementSuggestions`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 1, '2025-03-20', 'Debug module X', 1, 'In Progress', 120, 150, 'Unexpected errors', 'Tough but learned a lot', 'Finish by tomorrow', 'Team needs better tools', 'Add debug logging', '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(2, 2, '2025-03-20', 'Write unit tests', 2, 'Completed', 60, 50, NULL, 'Smooth process', 'Test coverage done', 'Good progress today', NULL, '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(3, 4, '2025-03-20', 'Design UI', 4, 'Not Started', 180, NULL, NULL, NULL, 'Start tomorrow', NULL, 'More design resources', '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(4, 1, '2025-03-21', 'Fix bugs in module X', 1, 'Completed', 90, 100, 'Complex logic', 'Satisfying fix', 'Review with team', 'Codebase is messy', 'Refactor later', '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(5, 2, '2025-03-21', 'Review code', 2, 'In Progress', 60, NULL, NULL, 'Ongoing', 'Finish by EOD', NULL, NULL, '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(6, 3, '2025-03-21', 'Plan sprint', 3, 'Completed', 120, 110, NULL, 'Good discussion', 'Tasks assigned', 'Team is motivated', NULL, '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(7, 4, '2025-03-21', 'Prototype UI', 4, 'In Progress', 150, 160, 'Client feedback', 'Iterating fast', 'Deliver by Monday', NULL, 'Faster feedback loops', '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(8, 5, '2025-03-21', 'Test system Y', 5, 'Blocked', 90, NULL, 'Waiting on server', 'Frustrating delay', 'Check tomorrow', 'Server issues', 'Better server uptime', '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(9, 1, '2025-03-22', 'Optimize module X', 1, 'Not Started', 120, NULL, NULL, NULL, 'Start Monday', NULL, NULL, '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(10, 2, '2025-03-22', 'Document APIs', 2, 'Completed', 60, 55, NULL, 'Clear docs', 'Share with team', NULL, NULL, '2025-03-22 12:21:02', '2025-03-22 12:21:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `analyticsreports`
--
ALTER TABLE `analyticsreports`
  ADD PRIMARY KEY (`ReportID`);

--
-- Indexes for table `diarysubmissions`
--
ALTER TABLE `diarysubmissions`
  ADD PRIMARY KEY (`SubmissionID`),
  ADD UNIQUE KEY `unique_submission` (`UserID`,`EntryDate`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`ProjectID`);

--
-- Indexes for table `projecttasks`
--
ALTER TABLE `projecttasks`
  ADD PRIMARY KEY (`TaskID`),
  ADD KEY `OwnerID` (`OwnerID`),
  ADD KEY `ProjectID` (`ProjectID`);

--
-- Indexes for table `sateamobservations`
--
ALTER TABLE `sateamobservations`
  ADD PRIMARY KEY (`ObservationID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `taskdependencies`
--
ALTER TABLE `taskdependencies`
  ADD PRIMARY KEY (`DependencyID`),
  ADD UNIQUE KEY `unique_dependency` (`TaskID`,`PredecessorID`),
  ADD KEY `PredecessorID` (`PredecessorID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `workdiary`
--
ALTER TABLE `workdiary`
  ADD PRIMARY KEY (`EntryID`),
  ADD KEY `TaskOwnerID` (`TaskOwnerID`),
  ADD KEY `idx_entry_date` (`EntryDate`),
  ADD KEY `idx_user_id` (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `analyticsreports`
--
ALTER TABLE `analyticsreports`
  MODIFY `ReportID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `diarysubmissions`
--
ALTER TABLE `diarysubmissions`
  MODIFY `SubmissionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `ProjectID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `projecttasks`
--
ALTER TABLE `projecttasks`
  MODIFY `TaskID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `sateamobservations`
--
ALTER TABLE `sateamobservations`
  MODIFY `ObservationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `taskdependencies`
--
ALTER TABLE `taskdependencies`
  MODIFY `DependencyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `workdiary`
--
ALTER TABLE `workdiary`
  MODIFY `EntryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `diarysubmissions`
--
ALTER TABLE `diarysubmissions`
  ADD CONSTRAINT `diarysubmissions_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `projecttasks`
--
ALTER TABLE `projecttasks`
  ADD CONSTRAINT `projecttasks_ibfk_1` FOREIGN KEY (`OwnerID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `projecttasks_ibfk_2` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`) ON DELETE CASCADE;

--
-- Constraints for table `sateamobservations`
--
ALTER TABLE `sateamobservations`
  ADD CONSTRAINT `sateamobservations_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `taskdependencies`
--
ALTER TABLE `taskdependencies`
  ADD CONSTRAINT `taskdependencies_ibfk_1` FOREIGN KEY (`TaskID`) REFERENCES `projecttasks` (`TaskID`) ON DELETE CASCADE,
  ADD CONSTRAINT `taskdependencies_ibfk_2` FOREIGN KEY (`PredecessorID`) REFERENCES `projecttasks` (`TaskID`) ON DELETE CASCADE;

--
-- Constraints for table `workdiary`
--
ALTER TABLE `workdiary`
  ADD CONSTRAINT `workdiary_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `workdiary_ibfk_2` FOREIGN KEY (`TaskOwnerID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
