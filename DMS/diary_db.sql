-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2025 at 09:01 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

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
(10, 2, '2025-03-22', 1, '2025-03-22 16:00:00', 0),
(11, 4, '2025-03-23', 1, '2025-03-23 15:16:39', 0),
(12, 2, '2025-03-25', 1, '2025-03-25 12:15:19', 0);

-- --------------------------------------------------------

--
-- Table structure for table `privatediaryentries`
--

CREATE TABLE `privatediaryentries` (
  `PrivateEntryID` int(11) NOT NULL,
  `WorkDiaryEntryID` int(11) NOT NULL,
  `PrivateTaskDescription` varchar(500) DEFAULT NULL,
  `PrivateTaskStatus` enum('Not Started','In Progress','Completed','Blocked') DEFAULT NULL,
  `PrivateInsights` text DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `privatediaryentries`
--

INSERT INTO `privatediaryentries` (`PrivateEntryID`, `WorkDiaryEntryID`, `PrivateTaskDescription`, `PrivateTaskStatus`, `PrivateInsights`, `CreatedAt`) VALUES
(1, 16, 'it is taking too much time', 'Not Started', 'none', '2025-03-25 12:15:19');

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
(3, 1, 'Optimize Module X', 'Improve performance', 4, 'Completed', 'Medium', '2025-03-23', '2025-03-25', '2025-03-22 12:21:02', '2025-03-28 22:34:35'),
(4, 2, 'Upgrade Server', 'Increase capacity', 5, 'Completed', 'High', '2025-03-21', '2025-03-24', '2025-03-22 12:21:02', '2025-03-23 18:23:11'),
(5, 2, 'Test System Y', 'Validate upgrades', 5, 'Pending', 'Medium', '2025-03-24', '2025-03-26', '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(6, 2, 'Document APIs', 'API reference', 2, 'Completed', 'Low', '2025-03-21', '2025-03-22', '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(7, 3, 'Fix Bug #123', 'Crash on login', 4, 'Active', 'High', '2025-03-20', '2025-03-22', '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(9, 1, 'Design UI', 'Create mockups', 4, 'Pending', 'Medium', '2025-03-21', '2025-03-24', '2025-03-22 12:21:02', '2025-03-22 12:21:02'),
(10, 2, 'Review Code', 'Code quality check', 2, 'Active', 'Low', '2025-03-21', '2025-03-23', '2025-03-22 12:21:02', '2025-03-22 15:21:53'),
(11, 1, 'Debug Module X', NULL, 4, 'Active', 'Medium', '2025-03-22', NULL, '2025-03-22 14:45:22', '2025-03-23 15:11:00'),
(12, 2, 'Review Code', NULL, 2, 'Active', 'Low', '2025-03-22', NULL, '2025-03-22 22:43:42', '2025-03-22 22:44:41'),
(15, 2, 'ui development', NULL, 5, 'Pending', 'High', NULL, NULL, '2025-03-23 18:21:22', '2025-03-23 18:25:48'),
(16, 1, 'Review Code', NULL, 5, 'Completed', 'Medium', NULL, NULL, '2025-03-23 18:22:34', '2025-03-24 11:26:10');

--
-- Triggers `projecttasks`
--
DELIMITER $$
CREATE TRIGGER `projecttasks_after_delete` AFTER DELETE ON `projecttasks` FOR EACH ROW BEGIN
    INSERT INTO `projecttasks_audit_log` (
        `TaskID`, `ProjectID`, `TaskName`, `Description`, 
        `OwnerID`, `Status`, `Priority`, `StartDate`, 
        `EndDate`, `CreatedAt`, `UpdatedAt`, `ChangeType`
    ) VALUES (
        OLD.TaskID, OLD.ProjectID, OLD.TaskName, OLD.Description,
        OLD.OwnerID, OLD.Status, OLD.Priority, OLD.StartDate,
        OLD.EndDate, OLD.CreatedAt, OLD.UpdatedAt, 'DELETE'
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `projecttasks_after_insert` AFTER INSERT ON `projecttasks` FOR EACH ROW BEGIN
    INSERT INTO `projecttasks_audit_log` (
        `TaskID`, `ProjectID`, `TaskName`, `Description`, 
        `OwnerID`, `Status`, `Priority`, `StartDate`, 
        `EndDate`, `CreatedAt`, `UpdatedAt`, `ChangeType`
    ) VALUES (
        NEW.TaskID, NEW.ProjectID, NEW.TaskName, NEW.Description,
        NEW.OwnerID, NEW.Status, NEW.Priority, NEW.StartDate,
        NEW.EndDate, NEW.CreatedAt, NEW.UpdatedAt, 'INSERT'
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `projecttasks_after_update` AFTER UPDATE ON `projecttasks` FOR EACH ROW BEGIN
    INSERT INTO `projecttasks_audit_log` (
        `TaskID`, `ProjectID`, `TaskName`, `Description`, 
        `OwnerID`, `Status`, `Priority`, `StartDate`, 
        `EndDate`, `CreatedAt`, `UpdatedAt`, `ChangeType`
    ) VALUES (
        NEW.TaskID, NEW.ProjectID, NEW.TaskName, NEW.Description,
        NEW.OwnerID, NEW.Status, NEW.Priority, NEW.StartDate,
        NEW.EndDate, NEW.CreatedAt, NEW.UpdatedAt, 'UPDATE'
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `projecttasks_audit_log`
--

CREATE TABLE `projecttasks_audit_log` (
  `AuditLogID` int(11) NOT NULL,
  `TaskID` int(11) NOT NULL,
  `ProjectID` int(11) NOT NULL,
  `TaskName` varchar(100) NOT NULL,
  `Description` varchar(500) DEFAULT NULL,
  `OwnerID` int(11) NOT NULL,
  `Status` enum('Pending','Active','Completed') DEFAULT NULL,
  `Priority` enum('Low','Medium','High') DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `CreatedAt` datetime DEFAULT NULL,
  `UpdatedAt` datetime DEFAULT NULL,
  `ChangeType` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `ChangedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projecttasks_audit_log`
--

INSERT INTO `projecttasks_audit_log` (`AuditLogID`, `TaskID`, `ProjectID`, `TaskName`, `Description`, `OwnerID`, `Status`, `Priority`, `StartDate`, `EndDate`, `CreatedAt`, `UpdatedAt`, `ChangeType`, `ChangedAt`) VALUES
(1, 3, 1, 'Optimize Module X', 'Improve performance', 4, 'Completed', 'Medium', '2025-03-23', '2025-03-25', '2025-03-22 12:21:02', '2025-03-28 22:34:35', 'UPDATE', '2025-03-28 22:34:35');

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
(6, NULL, 3, '2025-03-22', 'testing this field', 'Process Improvement', '2025-03-22 22:20:21'),
(7, NULL, 3, '2025-03-23', 'asa', 'Technical Issue', '2025-03-23 15:17:19'),
(8, NULL, 3, '2025-03-24', 'asas', 'Process Improvement', '2025-03-24 11:48:35');

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
  `TaskStatus` enum('Not Started','In Progress','Completed','Blocked') DEFAULT 'Not Started',
  `AllocatedTime` decimal(5,2) NOT NULL,
  `ActualTime` decimal(5,2) DEFAULT NULL,
  `DeviationReason` text DEFAULT NULL,
  `PersonalInsights` text DEFAULT NULL,
  `Commitments` text DEFAULT NULL,
  `GeneralObservations` text DEFAULT NULL,
  `ImprovementSuggestions` text DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `IsPrivate` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workdiary`
--

INSERT INTO `workdiary` (`EntryID`, `UserID`, `EntryDate`, `TaskDescription`, `TaskStatus`, `AllocatedTime`, `ActualTime`, `DeviationReason`, `PersonalInsights`, `Commitments`, `GeneralObservations`, `ImprovementSuggestions`, `CreatedAt`, `UpdatedAt`, `IsPrivate`) VALUES
(1, 1, '2025-03-20', 'Debug module X', 'In Progress', 2.00, 2.50, 'Unexpected errors', 'Tough but learned a lot', 'Finish by tomorrow', 'Team needs better tools', 'Add debug logging', '2025-03-22 12:21:02', '2025-03-23 20:37:10', 0),
(2, 2, '2025-03-20', 'Write unit tests', 'Completed', 1.00, 0.83, NULL, 'Smooth process', 'Test coverage done', 'Good progress today', NULL, '2025-03-22 12:21:02', '2025-03-23 20:37:10', 0),
(3, 4, '2025-03-20', 'Design UI', 'Not Started', 3.00, NULL, NULL, NULL, 'Start tomorrow', NULL, 'More design resources', '2025-03-22 12:21:02', '2025-03-23 20:37:10', 0),
(4, 1, '2025-03-21', 'Fix bugs in module X', 'Completed', 1.50, 1.67, 'Complex logic', 'Satisfying fix', 'Review with team', 'Codebase is messy', 'Refactor later', '2025-03-22 12:21:02', '2025-03-23 20:37:10', 0),
(5, 2, '2025-03-21', 'Review code', 'In Progress', 1.00, NULL, NULL, 'Ongoing', 'Finish by EOD', NULL, NULL, '2025-03-22 12:21:02', '2025-03-23 20:37:10', 0),
(6, 3, '2025-03-21', 'Plan sprint', 'Completed', 2.00, 1.83, NULL, 'Good discussion', 'Tasks assigned', 'Team is motivated', NULL, '2025-03-22 12:21:02', '2025-03-23 20:37:10', 0),
(7, 4, '2025-03-21', 'Prototype UI', 'In Progress', 2.50, 2.67, 'Client feedback', 'Iterating fast', 'Deliver by Monday', NULL, 'Faster feedback loops', '2025-03-22 12:21:02', '2025-03-23 20:37:10', 0),
(8, 5, '2025-03-21', 'Test system Y', 'Blocked', 1.50, NULL, 'Waiting on server', 'Frustrating delay', 'Check tomorrow', 'Server issues', 'Better server uptime', '2025-03-22 12:21:02', '2025-03-23 20:37:10', 0),
(9, 1, '2025-03-22', 'Optimize module X', 'Not Started', 2.00, NULL, NULL, NULL, 'Start Monday', NULL, NULL, '2025-03-22 12:21:02', '2025-03-23 20:37:10', 0),
(10, 2, '2025-03-22', 'Document APIs', 'Completed', 1.00, 0.92, NULL, 'Clear docs', 'Share with team', NULL, NULL, '2025-03-22 12:21:02', '2025-03-23 20:37:10', 0),
(11, 4, '2025-03-23', 'team building', 'In Progress', 2.00, 4.00, 'na', 'na', NULL, NULL, NULL, '2025-03-23 15:16:39', '2025-03-23 20:37:10', 0),
(14, 2, '2025-03-25', 'Review Code', 'Completed', 8.00, 5.50, 'na', 'work is done', NULL, NULL, NULL, '2025-03-25 11:59:58', '2025-03-25 11:59:58', 1),
(15, 2, '2025-03-25', 'Review Code', 'Completed', 4.00, 8.00, 'complex', 'aasasa', NULL, NULL, NULL, '2025-03-25 12:00:38', '2025-03-25 12:00:38', 1),
(16, 2, '2025-03-25', 'Review Code', 'In Progress', 5.00, 12.00, 'complex process', 'about to complete', 'about the completion', 'na', 'nothing', '2025-03-25 12:15:19', '2025-03-25 12:15:19', 1);

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
-- Indexes for table `privatediaryentries`
--
ALTER TABLE `privatediaryentries`
  ADD PRIMARY KEY (`PrivateEntryID`),
  ADD KEY `WorkDiaryEntryID` (`WorkDiaryEntryID`);

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
-- Indexes for table `projecttasks_audit_log`
--
ALTER TABLE `projecttasks_audit_log`
  ADD PRIMARY KEY (`AuditLogID`);

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
  MODIFY `SubmissionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `privatediaryentries`
--
ALTER TABLE `privatediaryentries`
  MODIFY `PrivateEntryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `ProjectID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `projecttasks`
--
ALTER TABLE `projecttasks`
  MODIFY `TaskID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `projecttasks_audit_log`
--
ALTER TABLE `projecttasks_audit_log`
  MODIFY `AuditLogID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sateamobservations`
--
ALTER TABLE `sateamobservations`
  MODIFY `ObservationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `taskdependencies`
--
ALTER TABLE `taskdependencies`
  MODIFY `DependencyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `workdiary`
--
ALTER TABLE `workdiary`
  MODIFY `EntryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `diarysubmissions`
--
ALTER TABLE `diarysubmissions`
  ADD CONSTRAINT `diarysubmissions_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `privatediaryentries`
--
ALTER TABLE `privatediaryentries`
  ADD CONSTRAINT `privatediaryentries_ibfk_1` FOREIGN KEY (`WorkDiaryEntryID`) REFERENCES `workdiary` (`EntryID`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `workdiary_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
