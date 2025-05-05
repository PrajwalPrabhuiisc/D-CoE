-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 05, 2025 at 05:07 PM
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
(3, 14, '2025-04-28', 1, '2025-04-28 18:50:55', 0),
(4, 3, '2025-04-28', 1, '2025-04-28 22:07:08', 0),
(5, 13, '2025-04-28', 1, '2025-04-28 21:35:47', 0),
(8, 12, '2025-04-28', 1, '2025-04-28 21:39:46', 0),
(11, 1, '2025-04-28', 1, '2025-04-28 21:44:34', 0),
(15, 15, '2025-04-28', 1, '2025-04-28 21:48:03', 0),
(20, 9, '2025-04-28', 1, '2025-04-28 21:48:58', 0),
(23, 10, '2025-04-28', 1, '2025-04-28 22:58:28', 0),
(24, 11, '2025-04-28', 1, '2025-04-28 23:04:37', 0),
(25, 4, '2025-04-29', 1, '2025-04-29 21:54:30', 0),
(26, 13, '2025-04-29', 1, '2025-04-29 22:22:19', 0),
(27, 3, '2025-04-29', 1, '2025-04-29 22:19:50', 0),
(29, 9, '2025-04-29', 1, '2025-04-29 22:22:51', 0),
(30, 10, '2025-04-29', 1, '2025-04-29 22:35:15', 0),
(31, 14, '2025-04-30', 1, '2025-04-30 22:52:42', 0),
(32, 4, '2025-04-30', 1, '2025-04-30 21:00:50', 0),
(33, 12, '2025-04-30', 1, '2025-04-30 21:57:43', 0),
(34, 15, '2025-04-30', 1, '2025-04-30 22:02:08', 0),
(36, 11, '2025-04-30', 1, '2025-04-30 23:08:01', 0),
(37, 2, '2025-05-01', 1, '2025-05-01 20:03:41', 0),
(38, 13, '2025-05-01', 1, '2025-05-01 20:04:47', 0),
(39, 3, '2025-05-01', 1, '2025-05-01 20:12:43', 0),
(40, 15, '2025-05-02', 1, '2025-05-02 22:30:50', 0),
(41, 11, '2025-05-02', 1, '2025-05-02 22:52:04', 0);

-- --------------------------------------------------------

--
-- Table structure for table `privatediaryentries`
--

CREATE TABLE `privatediaryentries` (
  `PrivateEntryID` int(11) NOT NULL,
  `WorkDiaryEntryID` int(11) NOT NULL,
  `PrivateTaskDescription` varchar(500) DEFAULT NULL,
  `PrivateTaskStatus` enum('Not Started','In Progress','Completed','Blocked','None') DEFAULT NULL,
  `PrivateInsights` text DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `privatediaryentries`
--

INSERT INTO `privatediaryentries` (`PrivateEntryID`, `WorkDiaryEntryID`, `PrivateTaskDescription`, `PrivateTaskStatus`, `PrivateInsights`, `CreatedAt`) VALUES
(2, 30, 'Salary Analysis, Performance Appraisal, Organisational Chart', 'In Progress', NULL, '2025-04-28 21:30:42'),
(3, 32, 'BD Onboarding, Induction', 'Completed', NULL, '2025-04-28 21:35:47'),
(4, 46, 'Attendance monitoring system using RPie', 'Completed', 'None', '2025-04-28 22:05:01'),
(5, 47, 'Procurement of soldering station and FPGA is finished', 'Completed', 'nil', '2025-04-28 22:07:08'),
(6, 48, 'None', NULL, 'None', '2025-04-28 22:58:28'),
(7, 49, NULL, NULL, 'due to last 2 weeks the faculties are busy with interview and meetings and projects etc. there was delay and as usual last minute rush.', '2025-04-28 23:04:37'),
(8, 52, 'None', NULL, 'None', '2025-04-29 22:19:50'),
(9, 53, 'Working on Different Policy to add in Organisational Chart', 'In Progress', NULL, '2025-04-29 22:22:19'),
(10, 55, 'None', NULL, 'None', '2025-04-29 22:35:15'),
(11, 88, 'Material Collection', 'In Progress', 'material delay', '2025-05-02 22:52:04');

-- --------------------------------------------------------

--
-- Table structure for table `Projects`
--

CREATE TABLE `Projects` (
  `ProjectID` int(11) NOT NULL,
  `ProjectName` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Projects`
--

INSERT INTO `Projects` (`ProjectID`, `ProjectName`, `Description`, `CreatedAt`) VALUES
(1, 'Raman Hall Infrastructure Development', 'Setting up and equipping Raman Hall as a functional facility for prototyping, training, or educational activities.', '2025-04-19 14:56:23'),
(2, 'Studio Design and Setup', 'Designing and equipping a studio space for design, prototyping, or content creation activities.', '2025-04-19 14:56:23'),
(3, 'Welding and Fabrication Facility Setup', 'Establishing a welding and fabrication room with necessary equipment and infrastructure.', '2025-04-19 14:56:23'),
(4, 'Mechanical Prototyping Lab Development', 'Procuring and setting up equipment and resources for a mechanical prototyping lab.', '2025-04-19 14:56:23'),
(5, 'Electronics Prototyping Lab Development', 'Equipping a lab for electronics prototyping activities.', '2025-04-19 14:56:23'),
(6, 'Content Creation and AV Facility Setup', 'Setting up a dedicated facility for content creation, including audiovisual equipment and resources.', '2025-04-19 14:56:23'),
(7, 'Educational Module Development', 'Creating content, slides, videos, and coordinating delivery for educational modules (e.g., Aesthetics & Semiotics, Design Thinking, Ergonomics).', '2025-04-19 14:56:23'),
(8, 'Event and Workshop Organization', 'Planning and executing events, workshops, and forums for various stakeholders.', '2025-04-19 14:56:23'),
(9, 'Digital Presence and Marketing', 'Developing and managing online platforms, social media, and marketing content.', '2025-04-19 14:56:23'),
(10, 'Procurement and Inventory Management', 'Managing procurement processes, inventory, and logistics for equipment and resources.', '2025-04-19 14:56:23'),
(11, 'Administrative and HR Operations', 'Managing organizational policies, hiring, and administrative tasks.', '2025-04-19 14:56:23'),
(12, 'Financial and Reporting Management', 'Handling financial reports, budgets, and expenditure tracking.', '2025-04-19 14:56:23'),
(13, 'Collaboration and Outreach', 'Building partnerships, coordinating with external stakeholders, and managing MoUs.', '2025-04-19 14:56:23'),
(14, '3D Printing Services', 'Managing 3D printing activities and services for students.', '2025-04-19 14:56:23'),
(15, 'Industry 4.0 and Advanced Manufacturing', 'Developing resources and insights related to Industry 4.0 and advanced manufacturing techniques.', '2025-04-19 14:56:23'),
(16, 'Policy and Guidelines Development', 'Creating guidelines and policies for events and operations.', '2025-04-19 14:56:23'),
(17, '3D Printing Services', 'Managind 3d Printing services for students', '2025-04-19 14:56:23'),
(18, 'Industry 4.0 advanced manufacturing', 'Developing resources and insights related to Industry 4.0 and advanced manufacturing', '2025-04-19 14:56:23'),
(19, 'Policy and Guidelines development', 'Creating guidelines and policies for the events and operations', '2025-04-19 14:56:23');

-- --------------------------------------------------------

--
-- Table structure for table `ProjectTasks`
--

CREATE TABLE `ProjectTasks` (
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
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `PeopleDependencies` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ProjectTasks`
--

INSERT INTO `ProjectTasks` (`TaskID`, `ProjectID`, `TaskName`, `Description`, `OwnerID`, `Status`, `Priority`, `StartDate`, `EndDate`, `CreatedAt`, `UpdatedAt`, `PeopleDependencies`) VALUES
(150, 4, 'Follow up with the vendor regarding the Raman Hall electrical work', 'Coordinate with vendor for electrical work in Raman Hall', 2, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(151, 4, 'Follow up with the vendor regarding AC Installation', 'Coordinate with vendor for AC installation in Raman Hall', 2, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(152, 4, 'Paint Work in Raman Hall', 'Manage painting of Raman Hall interiors', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'NishathSalma, FSIDTeam(PO To be released), 45'),
(153, 4, 'Windows & Roller Blinds (Raman Hall)', 'Install windows and roller blinds in Raman Hall', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'NishathSalma, FSIDTeam(PO To be released), 45'),
(154, 4, 'Storage Design (Raman Hall)', 'Design storage solutions for Raman Hall', 5, 'Pending', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(155, 4, 'Collecting status about Renovation work', 'Track progress of Raman Hall renovation', 4, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(156, 5, 'Prototype of Studio Table', 'Develop prototype for studio table', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', '45'),
(157, 5, 'Mezzanine Floor (Studio)', 'Construct mezzanine floor in studio', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(158, 5, 'Layout Design (Studio)', 'Plan layout for studio space', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(159, 5, 'Storage Design (Studio)', 'Design storage solutions for studio', 5, 'Pending', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(160, 5, 'Fabrication of Studio Tables', 'Fabricate tables for studio', 5, 'Pending', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'NishathSalma, FSIDTeam(PO To be released), 45'),
(161, 6, 'Welding room / spray painting room line vendor', 'Coordinate with vendor for welding/painting room setup', 10, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(162, 6, 'Arranging the tables for the welding machine in the welding room', 'Set up tables for welding machines', 10, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(163, 6, 'MIG welding installation', 'Install MIG welding equipment', 7, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(164, 6, 'MIG welding Gas lines and Co2 cylinder', 'Set up gas lines and CO2 cylinder for MIG welding', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(165, 6, 'TIG welding installation', 'Install TIG welding equipment', 7, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(166, 6, 'TIG welding Gas lines and Argon cylinder', 'Set up gas lines and Argon cylinder for TIG welding', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(167, 6, 'Plasma welding Air line', 'Set up air line for plasma welding', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(168, 6, 'Welding Table and accessories for MIG, TIG and Plasma welding', 'Procure and set up welding tables and accessories', 7, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(169, 6, 'Plasma Installation', 'Install plasma welding equipment', 7, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(170, 7, 'Content Creation for Mechanical Prototyping', 'Develop content for mechanical prototyping module', 2, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(171, 7, 'CMM Machine Procurement - Mechanical Prototyping', 'Procure CMM machine for prototyping lab', 2, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(172, 7, 'Follow up with the vendor for CMM Installation & Training', 'Coordinate CMM installation and training', 2, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(173, 7, 'Raw Material Procurement for 3D Printers - Mechanical Prototyping', 'Procure raw materials for 3D printers', 2, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(174, 7, 'UTM Machine Procurement - Mechanical Prototyping', 'Procure UTM machine for prototyping lab', 2, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(175, 7, 'Follow up with the vendor for UTM Installation & Training', 'Coordinate UTM installation and training', 2, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(176, 7, 'Vacuum casting', 'Set up vacuum casting equipment', 7, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(177, 7, 'Clay printer', 'Procure and set up clay printer', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(178, 7, 'Ceramic printer', 'Procure and set up ceramic printer', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(179, 7, 'LSS cutter', 'Procure and set up LSS cutter', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(180, 7, 'CNC laser router', 'Procure and set up CNC laser router', 7, 'Pending', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(181, 7, 'Procurement process for mechanical prototyping', 'Manage procurement for mechanical prototyping', 11, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(182, 7, 'Coordinate regarding Mechanical Prototyping Module', 'Coordinate delivery of mechanical prototyping module', 12, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(183, 7, 'Module lead tech support for mechanical prototyping', 'Provide technical support for prototyping module', 11, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(184, 8, 'Procurements for Electronics Prototyping (EP)', 'Procure equipment for electronics prototyping', 3, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-30 02:41:35', NULL),
(185, 8, 'Procurement process for design prototyping', 'Manage procurement for design prototyping', 11, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(186, 9, 'Content creation room set up', 'Set up content creation room', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(187, 9, 'Content creation facility AV set up', 'Install AV equipment for content creation', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(188, 9, 'PC’s for Raman hall *17', 'Procure 17 PCs for Raman Hall', 7, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(189, 9, 'PC for Content creation room', 'Procure PC for content creation room', 7, 'Pending', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(190, 9, 'UPS for content creation room', 'Procure UPS for content creation room', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(191, 9, 'Video conferencing system', 'Install video conferencing system', 7, 'Pending', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(192, 9, 'Display units for virtual training', 'Procure display units for virtual training', 7, 'Pending', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(193, 9, 'Content creation room', 'Manage content creation room setup', 3, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(194, 10, 'Kit Design (Aesthetic & Semiotics)', 'Design kit for Aesthetics & Semiotics module', 5, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'Subramanian, 17'),
(195, 10, 'Slides For Aesthetics & Semiotics Module', 'Prepare slides for Aesthetics & Semiotics', 5, 'Pending', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', '17'),
(196, 10, 'Slides for Design Thinking & Methodology Module', 'Prepare slides for Design Thinking module', 5, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(197, 10, 'Slide for Foundational Workshop (Aesthetics & Semiotics)', 'Prepare slide for Aesthetics workshop', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(198, 10, 'Slide for Foundational Workshop (Design Thinking & Methodology)', 'Prepare slide for Design Thinking workshop', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(199, 10, 'Video Script (Design Thinking & Methodology)', 'Write video script for Design Thinking', 5, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(200, 10, 'Video Script (Aesthetics & Semiotics)', 'Write video script for Aesthetics', 5, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(201, 10, 'Content Creation of Module 2', 'Develop content for Module 2', 9, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(202, 10, 'Content Creation of Module 9', 'Develop content for Module 9', 9, 'Pending', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(203, 10, 'Prepare Slide for the Ergonomics and Human Factors module', 'Prepare slides for Ergonomics module', 12, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(204, 10, 'Prepare for delivery of the Ergonomics and Human Factors module', 'Coordinate delivery of Ergonomics module', 12, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(205, 10, 'Prepare script for Pre-recorded videos', 'Write scripts for pre-recorded videos', 12, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(206, 10, 'Complete Pre Recorded videos', 'Produce pre-recorded videos', 12, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(207, 10, 'Complete training by the Professors', 'Coordinate professor-led training', 12, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(208, 10, 'Pre-recorded videos for design and mechanical prototyping', 'Produce videos for prototyping modules', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(209, 10, 'Procurement process for Aesthetics and Semiotics', 'Manage procurement for Aesthetics module', 11, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(210, 10, 'Procurement process for Ergonomics and Human Factors', 'Manage procurement for Ergonomics module', 11, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(211, 10, 'Module lead tech support for aesthetics', 'Provide tech support for Aesthetics module', 11, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(212, 10, 'Module lead tech support for ergonomics', 'Provide tech support for Ergonomics module', 11, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(213, 11, '21st April - Event', 'Organize event on 21st April', 13, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-30 02:38:11', NULL),
(214, 11, 'Event Organising', 'Plan and execute events', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(215, 11, 'Foundation Day Workshop', 'Organize Foundation Day workshop', 9, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(216, 11, 'Organize forum for principals and deans of various college', 'Organize forum for college principals/deans', 15, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(217, 11, 'Organize forum for Industry Professionals', 'Organize forum for industry professionals', 15, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(218, 11, 'Organize forum for Startups and incubation centres', 'Organize forum for startups', 15, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(219, 11, 'Arrange office visits by guest', 'Coordinate guest office visits', 15, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(220, 11, 'Organise and Coordinate 1 day workshop', 'Organize 1-day workshop', 15, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(221, 11, 'Arranging Collaterals', 'Prepare event collaterals', 4, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(222, 11, 'Arranging Refreshments & Food arrangements for Meetings & Events', 'Organize food for events', 4, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(223, 11, 'Organising Cake for Team Birthday & activities', 'Plan team birthday celebrations', 4, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:13:14', NULL),
(224, 11, 'Event setup', 'Set up event venues', 11, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(225, 11, 'Social Events & Organizing', 'Organize social events', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(226, 11, 'Photographs + Event Updates', 'Capture and share event updates', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(227, 12, 'Developing Intranet Web site and Online approval flow for 3D Printing services', 'Develop intranet for 3D printing services', 2, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(228, 12, 'Youtube channel', 'Manage Youtube channel', 3, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(229, 12, 'Website Developing', 'Develop organization website', 14, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(230, 12, 'Collaterals', 'Create marketing collaterals', 14, 'Completed', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(231, 12, 'Social Media Post', 'Create social media posts', 14, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(232, 12, 'Video Editing', 'Edit videos for marketing', 14, 'Pending', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(233, 12, 'Photography', 'Capture photos for marketing', 14, 'Pending', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(234, 12, 'Prepare content for the website', 'Develop website content', 12, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(235, 12, 'Coordinate with the website vendor', 'Liaise with website vendor', 12, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(236, 12, 'Get approval for the website content', 'Obtain approval for website content', 12, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(237, 12, 'Posting In LinkedIn', 'Post updates on LinkedIn', 15, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(238, 12, 'Sending information mail', 'Send informational emails', 15, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-30 02:40:17', NULL),
(239, 12, 'Electronic Marketing', 'Execute electronic marketing campaigns', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(240, 12, 'Social Media (LinkedIn)', 'Manage LinkedIn presence', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(241, 13, 'Finalizing Payment Gateway Service provider', 'Select payment gateway provider', 2, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(242, 13, '8 Height adjustable Desks', 'Procure 8 height adjustable desks', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', '45'),
(243, 13, '4 Height adjustable Desks', 'Procure 4 height adjustable desks', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'NishathSalma, FSIDTeam(PO To be released), 45'),
(244, 13, 'Procurement of RHINO 8 (Software)', 'Procure RHINO 8 software', 5, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(245, 13, 'Procurement of Clipstudio Paint Pro (Software)', 'Procure Clipstudio Paint Pro software', 5, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(246, 13, 'Procurement of Keyshot (Software)', 'Procure Keyshot software', 5, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(247, 13, 'Virtual Training Facility Procurement', 'Procure virtual training equipment', 9, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(248, 13, 'AR/VR Headset', 'Procure AR/VR headsets', 9, 'Completed', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-30 02:42:01', NULL),
(249, 13, 'New item procurement - QR code', 'Procure items with QR code tracking', 10, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(250, 13, 'Complete the procurement of the equipment', 'Finalize equipment procurement', 12, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(251, 13, 'Procurement & Logistics', 'Manage procurement and logistics', 4, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(252, 13, 'Equipment & Hardware Inventory', 'Maintain equipment inventory', 4, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(253, 13, 'Stationery Inventory', 'Maintain stationery inventory', 4, 'Completed', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(254, 13, 'Managing inventory', 'Oversee inventory management', 10, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(255, 13, 'Procurement process for regular activities', 'Manage regular procurement', 11, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(256, 13, 'Procurement', 'Execute procurement tasks', 8, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(257, 13, 'Procurement', 'Execute procurement tasks', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(258, 14, 'Hiring for Business Developer, System Admin, Digital Marketing Intern and Graphic Design Intern', 'Recruit for various roles', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(259, 14, 'Creating SOP', 'Develop standard operating procedures', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(260, 14, 'Organizational Chart', 'Create organizational chart', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(261, 14, 'Performance Appraisal Policy', 'Develop appraisal policy', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(262, 14, 'Grievance Cell', 'Establish grievance cell', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(263, 14, 'Organizational Policy', 'Develop organizational policies', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(264, 14, 'Vendor Interaction', 'Coordinate with vendors', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(265, 14, 'Admin', 'Perform administrative tasks', 8, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(266, 14, 'Collecting Salaries & Insurances details', 'Gather salary and insurance data', 4, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(267, 15, 'Preparing financial report every week', 'Prepare weekly financial reports', 4, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(268, 15, 'Preparing Budget every month Capex and Opex', 'Prepare monthly budget', 4, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(269, 15, 'Collecting status - Statement of expenditure, Purchase Orders details, Expenditure details & Payment', 'Track expenditure and POs', 4, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(270, 15, 'Making Reports - Visionary Forum', 'Prepare reports for Visionary Forum', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(271, 15, 'Maintain payment tracker', 'Track payments', 10, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(272, 15, 'Report making', 'Generate reports', 15, 'Pending', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(273, 16, 'Reach out to colleges', 'Contact colleges for collaboration', 15, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(274, 16, 'Prepare Minutes of Meeting', 'Document meeting minutes', 15, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(275, 16, 'MoU Preparation', 'Prepare MoUs', 15, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(276, 16, 'MoU Sharing', 'Share MoUs with partners', 15, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-30 02:40:45', NULL),
(277, 16, 'Prepare PPT', 'Create presentations', 15, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(278, 16, 'Organise and coordinate meeting', 'Coordinate meetings', 15, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(279, 16, 'Maintain College info table', 'Update college information', 15, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(280, 16, 'Reach out to industries, Startups etc', 'Contact industries and startups', 15, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(281, 16, 'Follow up on meetings and forum', 'Follow up on meetings', 15, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(282, 16, 'Co ordinate with Prof. Vishal', 'Liaise with Prof. Vishal', 11, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(283, 16, 'Co ordinate with Prof. NDS', 'Liaise with Prof. NDS', 11, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(284, 16, 'Industries & Written Content', 'Create industry-related content', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(285, 16, 'Engagement', 'Engage with stakeholders', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 20:08:37', 'Apala'),
(286, 16, 'Collaborations & Synergy', 'Foster collaborations', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(287, 17, '3D printing for M.Des / M.Tech/ Ph.D students', 'Provide 3D printing services', 2, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(288, 17, 'Managing machines - SF - (Metal AM, Wire EDM, metal laser cutter, CO2 laser cutter, FDM 3D printer, ', 'Manage 3D printing machines', 10, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(289, 17, 'Work order follow up', 'Follow up on 3D printing work orders', 10, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(290, 18, 'Industry 4.0 Experience & Insights', 'Develop Industry 4.0 insights', 1, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-30 02:38:46', NULL),
(291, 18, 'Manufacturing Techniques for Polymers', 'Document polymer manufacturing techniques', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(292, 19, 'Policies', 'Develop organizational policies', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(293, 19, 'Past Event Guidelines', 'Create event guidelines', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(294, 19, 'Forum & Scripting', 'Prepare forum scripts', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(295, 8, 'Project', 'Manage electronics prototyping project', 3, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-30 02:41:24', NULL),
(296, 9, 'Lab activities', 'Oversee lab operations', 3, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(297, 10, 'Coordinate regarding the BD module', 'Coordinate business development module', 12, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL),
(298, 11, 'Day to day work', 'Handle daily operational tasks', 11, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', NULL);

--
-- Triggers `ProjectTasks`
--
DELIMITER $$
CREATE TRIGGER `projecttasks_after_delete` AFTER DELETE ON `ProjectTasks` FOR EACH ROW BEGIN
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
CREATE TRIGGER `projecttasks_after_insert` AFTER INSERT ON `ProjectTasks` FOR EACH ROW BEGIN
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
CREATE TRIGGER `projecttasks_after_update` AFTER UPDATE ON `ProjectTasks` FOR EACH ROW BEGIN
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
(138, 150, 4, 'Follow up with the vendor regarding the Raman Hall electrical work', 'Coordinate with vendor for electrical work in Raman Hall', 2, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(139, 151, 4, 'Follow up with the vendor regarding AC Installation', 'Coordinate with vendor for AC installation in Raman Hall', 2, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(140, 152, 4, 'Paint Work in Raman Hall', 'Manage painting of Raman Hall interiors', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(141, 153, 4, 'Windows & Roller Blinds (Raman Hall)', 'Install windows and roller blinds in Raman Hall', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(142, 154, 4, 'Storage Design (Raman Hall)', 'Design storage solutions for Raman Hall', 5, 'Pending', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(143, 155, 4, 'Collecting status about Renovation work', 'Track progress of Raman Hall renovation', 4, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(144, 156, 5, 'Prototype of Studio Table', 'Develop prototype for studio table', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(145, 157, 5, 'Mezzanine Floor (Studio)', 'Construct mezzanine floor in studio', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(146, 158, 5, 'Layout Design (Studio)', 'Plan layout for studio space', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(147, 159, 5, 'Storage Design (Studio)', 'Design storage solutions for studio', 5, 'Pending', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(148, 160, 5, 'Fabrication of Studio Tables', 'Fabricate tables for studio', 5, 'Pending', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(149, 161, 6, 'Welding room / spray painting room line vendor', 'Coordinate with vendor for welding/painting room setup', 10, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(150, 162, 6, 'Arranging the tables for the welding machine in the welding room', 'Set up tables for welding machines', 10, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(151, 163, 6, 'MIG welding installation', 'Install MIG welding equipment', 7, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(152, 164, 6, 'MIG welding Gas lines and Co2 cylinder', 'Set up gas lines and CO2 cylinder for MIG welding', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(153, 165, 6, 'TIG welding installation', 'Install TIG welding equipment', 7, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(154, 166, 6, 'TIG welding Gas lines and Argon cylinder', 'Set up gas lines and Argon cylinder for TIG welding', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(155, 167, 6, 'Plasma welding Air line', 'Set up air line for plasma welding', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(156, 168, 6, 'Welding Table and accessories for MIG, TIG and Plasma welding', 'Procure and set up welding tables and accessories', 7, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(157, 169, 6, 'Plasma Installation', 'Install plasma welding equipment', 7, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(158, 170, 7, 'Content Creation for Mechanical Prototyping', 'Develop content for mechanical prototyping module', 2, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(159, 171, 7, 'CMM Machine Procurement - Mechanical Prototyping', 'Procure CMM machine for prototyping lab', 2, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(160, 172, 7, 'Follow up with the vendor for CMM Installation & Training', 'Coordinate CMM installation and training', 2, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(161, 173, 7, 'Raw Material Procurement for 3D Printers - Mechanical Prototyping', 'Procure raw materials for 3D printers', 2, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(162, 174, 7, 'UTM Machine Procurement - Mechanical Prototyping', 'Procure UTM machine for prototyping lab', 2, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(163, 175, 7, 'Follow up with the vendor for UTM Installation & Training', 'Coordinate UTM installation and training', 2, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(164, 176, 7, 'Vacuum casting', 'Set up vacuum casting equipment', 7, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(165, 177, 7, 'Clay printer', 'Procure and set up clay printer', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(166, 178, 7, 'Ceramic printer', 'Procure and set up ceramic printer', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(167, 179, 7, 'LSS cutter', 'Procure and set up LSS cutter', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(168, 180, 7, 'CNC laser router', 'Procure and set up CNC laser router', 7, 'Pending', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(169, 181, 7, 'Procurement process for mechanical prototyping', 'Manage procurement for mechanical prototyping', 11, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(170, 182, 7, 'Coordinate regarding Mechanical Prototyping Module', 'Coordinate delivery of mechanical prototyping module', 12, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(171, 183, 7, 'Module lead tech support for mechanical prototyping', 'Provide technical support for prototyping module', 11, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(172, 184, 8, 'Procurements for Electronics Prototyping (EP)', 'Procure equipment for electronics prototyping', 3, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(173, 185, 8, 'Procurement process for design prototyping', 'Manage procurement for design prototyping', 11, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(174, 186, 9, 'Content creation room set up', 'Set up content creation room', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(175, 187, 9, 'Content creation facility AV set up', 'Install AV equipment for content creation', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(176, 188, 9, 'PC’s for Raman hall *17', 'Procure 17 PCs for Raman Hall', 7, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(177, 189, 9, 'PC for Content creation room', 'Procure PC for content creation room', 7, 'Pending', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(178, 190, 9, 'UPS for content creation room', 'Procure UPS for content creation room', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(179, 191, 9, 'Video conferencing system', 'Install video conferencing system', 7, 'Pending', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(180, 192, 9, 'Display units for virtual training', 'Procure display units for virtual training', 7, 'Pending', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(181, 193, 9, 'Content creation room', 'Manage content creation room setup', 3, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(182, 194, 10, 'Kit Design (Aesthetic & Semiotics)', 'Design kit for Aesthetics & Semiotics module', 5, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(183, 195, 10, 'Slides For Aesthetics & Semiotics Module', 'Prepare slides for Aesthetics & Semiotics', 5, 'Pending', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(184, 196, 10, 'Slides for Design Thinking & Methodology Module', 'Prepare slides for Design Thinking module', 5, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(185, 197, 10, 'Slide for Foundational Workshop (Aesthetics & Semiotics)', 'Prepare slide for Aesthetics workshop', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(186, 198, 10, 'Slide for Foundational Workshop (Design Thinking & Methodology)', 'Prepare slide for Design Thinking workshop', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(187, 199, 10, 'Video Script (Design Thinking & Methodology)', 'Write video script for Design Thinking', 5, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(188, 200, 10, 'Video Script (Aesthetics & Semiotics)', 'Write video script for Aesthetics', 5, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(189, 201, 10, 'Content Creation of Module 2', 'Develop content for Module 2', 9, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(190, 202, 10, 'Content Creation of Module 9', 'Develop content for Module 9', 9, 'Pending', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(191, 203, 10, 'Prepare Slide for the Ergonomics and Human Factors module', 'Prepare slides for Ergonomics module', 12, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(192, 204, 10, 'Prepare for delivery of the Ergonomics and Human Factors module', 'Coordinate delivery of Ergonomics module', 12, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(193, 205, 10, 'Prepare script for Pre-recorded videos', 'Write scripts for pre-recorded videos', 12, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(194, 206, 10, 'Complete Pre Recorded videos', 'Produce pre-recorded videos', 12, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(195, 207, 10, 'Complete training by the Professors', 'Coordinate professor-led training', 12, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(196, 208, 10, 'Pre-recorded videos for design and mechanical prototyping', 'Produce videos for prototyping modules', 7, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(197, 209, 10, 'Procurement process for Aesthetics and Semiotics', 'Manage procurement for Aesthetics module', 11, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(198, 210, 10, 'Procurement process for Ergonomics and Human Factors', 'Manage procurement for Ergonomics module', 11, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(199, 211, 10, 'Module lead tech support for aesthetics', 'Provide tech support for Aesthetics module', 11, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(200, 212, 10, 'Module lead tech support for ergonomics', 'Provide tech support for Ergonomics module', 11, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(201, 213, 11, '21st April - Event', 'Organize event on 21st April', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(202, 214, 11, 'Event Organising', 'Plan and execute events', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(203, 215, 11, 'Foundation Day Workshop', 'Organize Foundation Day workshop', 9, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(204, 216, 11, 'Organize forum for principals and deans of various college', 'Organize forum for college principals/deans', 15, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(205, 217, 11, 'Organize forum for Industry Professionals', 'Organize forum for industry professionals', 15, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(206, 218, 11, 'Organize forum for Startups and incubation centres', 'Organize forum for startups', 15, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(207, 219, 11, 'Arrange office visits by guest', 'Coordinate guest office visits', 15, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(208, 220, 11, 'Organise and Coordinate 1 day workshop', 'Organize 1-day workshop', 15, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(209, 221, 11, 'Arranging Collaterals', 'Prepare event collaterals', 4, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(210, 222, 11, 'Arranging Refreshments & Food arrangements for Meetings & Events', 'Organize food for events', 4, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(211, 223, 11, 'Organising Cake for Team Birthday & activities', 'Plan team birthday celebrations', 4, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(212, 224, 11, 'Event setup', 'Set up event venues', 11, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(213, 225, 11, 'Social Events & Organizing', 'Organize social events', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(214, 226, 11, 'Photographs + Event Updates', 'Capture and share event updates', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(215, 227, 12, 'Developing Intranet Web site and Online approval flow for 3D Printing services', 'Develop intranet for 3D printing services', 2, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(216, 228, 12, 'Youtube channel', 'Manage Youtube channel', 3, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(217, 229, 12, 'Website Developing', 'Develop organization website', 14, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(218, 230, 12, 'Collaterals', 'Create marketing collaterals', 14, 'Completed', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(219, 231, 12, 'Social Media Post', 'Create social media posts', 14, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(220, 232, 12, 'Video Editing', 'Edit videos for marketing', 14, 'Pending', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(221, 233, 12, 'Photography', 'Capture photos for marketing', 14, 'Pending', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(222, 234, 12, 'Prepare content for the website', 'Develop website content', 12, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(223, 235, 12, 'Coordinate with the website vendor', 'Liaise with website vendor', 12, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(224, 236, 12, 'Get approval for the website content', 'Obtain approval for website content', 12, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(225, 237, 12, 'Posting In LinkedIn', 'Post updates on LinkedIn', 15, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(226, 238, 12, 'Sending information mail', 'Send informational emails', 15, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(227, 239, 12, 'Electronic Marketing', 'Execute electronic marketing campaigns', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(228, 240, 12, 'Social Media (LinkedIn)', 'Manage LinkedIn presence', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(229, 241, 13, 'Finalizing Payment Gateway Service provider', 'Select payment gateway provider', 2, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(230, 242, 13, '8 Height adjustable Desks', 'Procure 8 height adjustable desks', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(231, 243, 13, '4 Height adjustable Desks', 'Procure 4 height adjustable desks', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(232, 244, 13, 'Procurement of RHINO 8 (Software)', 'Procure RHINO 8 software', 5, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(233, 245, 13, 'Procurement of Clipstudio Paint Pro (Software)', 'Procure Clipstudio Paint Pro software', 5, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(234, 246, 13, 'Procurement of Keyshot (Software)', 'Procure Keyshot software', 5, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(235, 247, 13, 'Virtual Training Facility Procurement', 'Procure virtual training equipment', 9, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(236, 248, 13, 'AR/VR Headset', 'Procure AR/VR headsets', 9, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(237, 249, 13, 'New item procurement - QR code', 'Procure items with QR code tracking', 10, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(238, 250, 13, 'Complete the procurement of the equipment', 'Finalize equipment procurement', 12, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(239, 251, 13, 'Procurement & Logistics', 'Manage procurement and logistics', 4, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(240, 252, 13, 'Equipment & Hardware Inventory', 'Maintain equipment inventory', 4, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(241, 253, 13, 'Stationery Inventory', 'Maintain stationery inventory', 4, 'Completed', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(242, 254, 13, 'Managing inventory', 'Oversee inventory management', 10, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(243, 255, 13, 'Procurement process for regular activities', 'Manage regular procurement', 11, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(244, 256, 13, 'Procurement', 'Execute procurement tasks', 8, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(245, 257, 13, 'Procurement', 'Execute procurement tasks', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(246, 258, 14, 'Hiring for Business Developer, System Admin, Digital Marketing Intern and Graphic Design Intern', 'Recruit for various roles', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(247, 259, 14, 'Creating SOP', 'Develop standard operating procedures', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(248, 260, 14, 'Organizational Chart', 'Create organizational chart', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(249, 261, 14, 'Performance Appraisal Policy', 'Develop appraisal policy', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(250, 262, 14, 'Grievance Cell', 'Establish grievance cell', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(251, 263, 14, 'Organizational Policy', 'Develop organizational policies', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(252, 264, 14, 'Vendor Interaction', 'Coordinate with vendors', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(253, 265, 14, 'Admin', 'Perform administrative tasks', 8, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(254, 266, 14, 'Collecting Salaries & Insurances details', 'Gather salary and insurance data', 4, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(255, 267, 15, 'Preparing financial report every week', 'Prepare weekly financial reports', 4, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(256, 268, 15, 'Preparing Budget every month Capex and Opex', 'Prepare monthly budget', 4, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(257, 269, 15, 'Collecting status - Statement of expenditure, Purchase Orders details, Expenditure details & Payment', 'Track expenditure and POs', 4, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(258, 270, 15, 'Making Reports - Visionary Forum', 'Prepare reports for Visionary Forum', 13, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(259, 271, 15, 'Maintain payment tracker', 'Track payments', 10, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(260, 272, 15, 'Report making', 'Generate reports', 15, 'Pending', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(261, 273, 16, 'Reach out to colleges', 'Contact colleges for collaboration', 15, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(262, 274, 16, 'Prepare Minutes of Meeting', 'Document meeting minutes', 15, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(263, 275, 16, 'MoU Preparation', 'Prepare MoUs', 15, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(264, 276, 16, 'MoU Sharing', 'Share MoUs with partners', 15, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(265, 277, 16, 'Prepare PPT', 'Create presentations', 15, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(266, 278, 16, 'Organise and coordinate meeting', 'Coordinate meetings', 15, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(267, 279, 16, 'Maintain College info table', 'Update college information', 15, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(268, 280, 16, 'Reach out to industries, Startups etc', 'Contact industries and startups', 15, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(269, 281, 16, 'Follow up on meetings and forum', 'Follow up on meetings', 15, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(270, 282, 16, 'Co ordinate with Prof. Vishal', 'Liaise with Prof. Vishal', 11, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(271, 283, 16, 'Co ordinate with Prof. NDS', 'Liaise with Prof. NDS', 11, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(272, 284, 16, 'Industries & Written Content', 'Create industry-related content', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(273, 285, 16, 'Engagement', 'Engage with stakeholders', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(274, 286, 16, 'Collaborations & Synergy', 'Foster collaborations', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(275, 287, 17, '3D printing for M.Des / M.Tech/ Ph.D students', 'Provide 3D printing services', 2, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(276, 288, 17, 'Managing machines - SF - (Metal AM, Wire EDM, metal laser cutter, CO2 laser cutter, FDM 3D printer, ', 'Manage 3D printing machines', 10, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(277, 289, 17, 'Work order follow up', 'Follow up on 3D printing work orders', 10, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(278, 290, 18, 'Industry 4.0 Experience & Insights', 'Develop Industry 4.0 insights', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(279, 291, 18, 'Manufacturing Techniques for Polymers', 'Document polymer manufacturing techniques', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(280, 292, 19, 'Policies', 'Develop organizational policies', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(281, 293, 19, 'Past Event Guidelines', 'Create event guidelines', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(282, 294, 19, 'Forum & Scripting', 'Prepare forum scripts', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(283, 295, 8, 'Project', 'Manage electronics prototyping project', 3, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(284, 296, 9, 'Lab activities', 'Oversee lab operations', 3, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(285, 297, 10, 'Coordinate regarding the BD module', 'Coordinate business development module', 12, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(286, 298, 11, 'Day to day work', 'Handle daily operational tasks', 11, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:06:30', 'INSERT', '2025-04-19 15:06:30'),
(287, 223, 11, 'Organising Cake for Team Birthday & activities', 'Plan team birthday celebrations', 4, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 15:13:14', 'UPDATE', '2025-04-19 15:13:14'),
(288, 150, 4, 'Follow up with the vendor regarding the Raman Hall electrical work', 'Coordinate with vendor for electrical work in Raman Hall', 2, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(289, 151, 4, 'Follow up with the vendor regarding AC Installation', 'Coordinate with vendor for AC installation in Raman Hall', 2, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(290, 152, 4, 'Paint Work in Raman Hall', 'Manage painting of Raman Hall interiors', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(291, 153, 4, 'Windows & Roller Blinds (Raman Hall)', 'Install windows and roller blinds in Raman Hall', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(292, 156, 5, 'Prototype of Studio Table', 'Develop prototype for studio table', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(293, 157, 5, 'Mezzanine Floor (Studio)', 'Construct mezzanine floor in studio', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(294, 158, 5, 'Layout Design (Studio)', 'Plan layout for studio space', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(295, 159, 5, 'Storage Design (Studio)', 'Design storage solutions for studio', 5, 'Pending', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(296, 160, 5, 'Fabrication of Studio Tables', 'Fabricate tables for studio', 5, 'Pending', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(297, 161, 6, 'Welding room / spray painting room line vendor', 'Coordinate with vendor for welding/painting room setup', 10, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(298, 162, 6, 'Arranging the tables for the welding machine in the welding room', 'Set up tables for welding machines', 10, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(299, 170, 7, 'Content Creation for Mechanical Prototyping', 'Develop content for mechanical prototyping module', 2, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(300, 171, 7, 'CMM Machine Procurement - Mechanical Prototyping', 'Procure CMM machine for prototyping lab', 2, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(301, 172, 7, 'Follow up with the vendor for CMM Installation & Training', 'Coordinate CMM installation and training', 2, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(302, 173, 7, 'Raw Material Procurement for 3D Printers - Mechanical Prototyping', 'Procure raw materials for 3D printers', 2, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(303, 174, 7, 'UTM Machine Procurement - Mechanical Prototyping', 'Procure UTM machine for prototyping lab', 2, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(304, 175, 7, 'Follow up with the vendor for UTM Installation & Training', 'Coordinate UTM installation and training', 2, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(305, 194, 10, 'Kit Design (Aesthetic & Semiotics)', 'Design kit for Aesthetics & Semiotics module', 5, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(306, 195, 10, 'Slides For Aesthetics & Semiotics Module', 'Prepare slides for Aesthetics & Semiotics', 5, 'Pending', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(307, 196, 10, 'Slides for Design Thinking & Methodology Module', 'Prepare slides for Design Thinking module', 5, 'Active', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(308, 197, 10, 'Slide for Foundational Workshop (Aesthetics & Semiotics)', 'Prepare slide for Aesthetics workshop', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(309, 198, 10, 'Slide for Foundational Workshop (Design Thinking & Methodology)', 'Prepare slide for Design Thinking workshop', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(310, 199, 10, 'Video Script (Design Thinking & Methodology)', 'Write video script for Design Thinking', 5, 'Completed', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(311, 200, 10, 'Video Script (Aesthetics & Semiotics)', 'Write video script for Aesthetics', 5, 'Active', 'Medium', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(312, 242, 13, '8 Height adjustable Desks', 'Procure 8 height adjustable desks', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(313, 243, 13, '4 Height adjustable Desks', 'Procure 4 height adjustable desks', 5, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 19:45:40', 'UPDATE', '2025-04-19 19:45:40'),
(314, 285, 16, 'Engagement', 'Engage with stakeholders', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 20:01:07', 'UPDATE', '2025-04-19 20:01:07'),
(315, 285, 16, 'Engagement', 'Engage with stakeholders', 1, 'Active', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-19 20:08:37', 'UPDATE', '2025-04-19 20:08:37'),
(316, 232, 12, 'Video Editing', 'Edit videos for marketing', 14, 'Pending', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-26 01:35:42', 'UPDATE', '2025-04-26 01:35:42'),
(317, 213, 11, '21st April - Event', 'Organize event on 21st April', 13, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-30 02:38:11', 'UPDATE', '2025-04-30 02:38:11'),
(318, 290, 18, 'Industry 4.0 Experience & Insights', 'Develop Industry 4.0 insights', 1, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-30 02:38:46', 'UPDATE', '2025-04-30 02:38:46'),
(319, 238, 12, 'Sending information mail', 'Send informational emails', 15, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-30 02:40:17', 'UPDATE', '2025-04-30 02:40:17'),
(320, 276, 16, 'MoU Sharing', 'Share MoUs with partners', 15, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-30 02:40:45', 'UPDATE', '2025-04-30 02:40:45'),
(321, 295, 8, 'Project', 'Manage electronics prototyping project', 3, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-30 02:41:24', 'UPDATE', '2025-04-30 02:41:24'),
(322, 184, 8, 'Procurements for Electronics Prototyping (EP)', 'Procure equipment for electronics prototyping', 3, 'Completed', 'High', NULL, NULL, '2025-04-19 15:06:30', '2025-04-30 02:41:35', 'UPDATE', '2025-04-30 02:41:35'),
(323, 248, 13, 'AR/VR Headset', 'Procure AR/VR headsets', 9, 'Completed', 'Low', NULL, NULL, '2025-04-19 15:06:30', '2025-04-30 02:42:01', 'UPDATE', '2025-04-30 02:42:01');

-- --------------------------------------------------------

--
-- Table structure for table `SATeamObservations`
--

CREATE TABLE `SATeamObservations` (
  `ObservationID` int(11) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `UserID` int(11) NOT NULL,
  `ObservationDate` date NOT NULL DEFAULT curdate(),
  `Details` text NOT NULL,
  `Category` varchar(50) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Role` varchar(50) DEFAULT 'Team Member'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`UserID`, `Username`, `Email`, `Role`) VALUES
(1, 'Apala Chakrabarti', 'apala@fsid-iisc.in', 'Team Member'),
(2, 'Calvin Samuel', 'calvin@fsid-iisc.in', 'Team Member'),
(3, 'Ananda Krishna', 'ananda.krishnan@fsid-iisc.in', 'Team Member'),
(4, 'Gayathri M', 'gayathri@fsid-iisc.in', 'Team Member'),
(5, 'HunnyBidlan', 'hsbidlan@gmail.com', 'Team Member'),
(6, 'K.JoshilRaj', 'joshil@fsid-iisc.in', 'Team Member'),
(7, 'LaveenKumar', 'Laveen@fsid-iisc.in', 'Team Member'),
(8, 'NishathSalma', 'nsalma@fsid-iisc.in', 'Team Member'),
(9, 'Puneeth K.S', 'puneeth@fsid-iisc.in', 'Team Member'),
(10, 'SivaS', 'Siva@fsid-iisc.in', 'Team Member'),
(11, 'Subramanian', 'subramanian@fsid-iisc.in', 'Team Member'),
(12, 'TSVV Murali', 'venkata@fsid-iisc.in', 'Team Member'),
(13, 'ChytraK', 'chytra.k@fsid-iisc.in', 'Team Member'),
(14, 'Samarth', 'Intern@fsid-iisc.in', 'Team Member'),
(15, 'Sakshita', 'sakshita@fsid-iisc.in', 'Team Member'),
(16, 'Prof. AC', 'ac123@iisc.ac.in', 'Faculties'),
(17, 'Prof. Vishal', 'singhv@iisc.ac.in', 'Faculties'),
(18, 'Prajwal Prabhu', 'prajwalp@iisc.ac.in', 'SA Team'),
(19, 'Uttam Jodawat', 'uttamjodawat@iisc.ac.in', 'SA Team'),
(20, 'DrNDS', 'nds@iisc.ac.in', 'Faculties'),
(21, 'ProfAD', 'ad@iisc.ac.in', 'Faculties'),
(22, 'ProfDS', 'ds@iisc.ac.in', 'Faculties'),
(23, 'ProfManishArora', 'manisharora@iisc.ac.in', 'Faculties'),
(24, 'ProfBGM', 'bgm@iisc.ac.in', 'Faculties'),
(25, 'ProfSMK', 'smk@iisc.ac.in', 'Faculties'),
(26, 'DrAbhijith', 'abhijith@iisc.ac.in', 'Faculties'),
(27, 'DM Office', 'officeDM@iisc.ac.in', 'Faculties'),
(28, 'Manjunath', 'manjunath@fsid-iisc.in', 'FSID'),
(29, 'GuruPrasad', 'guruprasad@fsid-iisc.in', 'FSID'),
(30, 'HarishN', 'harishn@fsid-iisc.in', 'FSID'),
(31, 'SandeshJ', 'sandeshj@fsid-iisc.in', 'FSID'),
(32, 'SureshKumarB', 'sureshkumarb@fsid-iisc.in', 'FSID'),
(33, 'Hrithik', 'hrithik@fsid-iisc.in', 'FSID'),
(34, 'DrSatheeshChandranC', 'satheeshchandranc@fsid-iisc.in', 'FSID'),
(35, 'Shivakumar', 'shivakumar@fsid-iisc.in', 'FSID'),
(36, 'Karthick', 'karthick@external.com', 'Externals'),
(37, 'DrAbhilashAP', 'abhilashap@external.com', 'Externals'),
(38, 'Lakshmi', 'lakshmi@external.com', 'Externals'),
(39, 'SreejithPM', 'sreejithpm@external.com', 'Externals'),
(40, 'Venu', 'venu@smartfactory.com', 'Smart Factory'),
(41, 'Nagaraj', 'nagaraj@smartfactory.com', 'Smart Factory'),
(42, 'Ravi', 'ravi@smartfactory.com', 'Smart Factory'),
(43, 'Amal', 'amal@smartfactory.com', 'Smart Factory'),
(44, 'Vishnu', 'vishnu@smartfactory.com', 'Smart Factory'),
(45, 'FabricationVendor', 'fabricationvendor@external.com', 'Externals'),
(46, 'FSIDTeam', 'fsid@team.com', 'Team');

-- --------------------------------------------------------

--
-- Table structure for table `WorkDiary`
--

CREATE TABLE `WorkDiary` (
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
  `IsPrivate` tinyint(1) DEFAULT 0,
  `TaskID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `WorkDiary`
--

INSERT INTO `WorkDiary` (`EntryID`, `UserID`, `EntryDate`, `TaskDescription`, `TaskStatus`, `AllocatedTime`, `ActualTime`, `DeviationReason`, `PersonalInsights`, `Commitments`, `GeneralObservations`, `ImprovementSuggestions`, `CreatedAt`, `UpdatedAt`, `IsPrivate`, `TaskID`) VALUES
(2, 9, '2025-04-25', 'AR/VR Headset', 'Completed', 2.00, 24.00, 'Got delayed in PO release due to unavailability of concerned authority to sign the PO at FSID', NULL, NULL, NULL, NULL, '2025-04-25 21:47:10', '2025-04-25 21:47:10', 0, NULL),
(7, 4, '2025-04-25', 'Preparing financial report every week', 'Completed', 4.00, 4.00, '11am to 4pm', NULL, NULL, NULL, NULL, '2025-04-25 22:41:46', '2025-04-25 22:41:46', 0, NULL),
(20, 14, '2025-04-28', 'Website Developing', 'In Progress', 7.00, 3.00, NULL, NULL, NULL, NULL, NULL, '2025-04-28 18:50:55', '2025-04-28 18:50:55', 0, NULL),
(30, 13, '2025-04-28', '21st April - Event', 'Completed', 1.00, 1.00, NULL, NULL, 'Completion of Digital Marketing Intern,Graphic Design Intern Screening and JD modification, ', NULL, NULL, '2025-04-28 21:30:42', '2025-04-28 21:30:42', 0, NULL),
(31, 13, '2025-04-28', 'Hiring for Business Developer, System Admin, Digital Marketing Intern and Graphic Design Intern', 'In Progress', 208.00, 200.00, NULL, NULL, NULL, NULL, NULL, '2025-04-28 21:33:28', '2025-04-28 21:33:28', 0, NULL),
(32, 13, '2025-04-28', 'Hiring for Business Developer, System Admin, Digital Marketing Intern and Graphic Design Intern', 'In Progress', 208.00, 200.00, NULL, NULL, NULL, NULL, NULL, '2025-04-28 21:35:47', '2025-04-28 21:35:47', 0, NULL),
(33, 12, '2025-04-28', 'Complete training by the Professors', 'In Progress', 3.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-28 21:38:21', '2025-04-28 21:38:21', 0, NULL),
(34, 12, '2025-04-28', 'Coordinate with the website vendor', 'In Progress', 3.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-28 21:39:25', '2025-04-28 21:39:25', 0, NULL),
(35, 12, '2025-04-28', 'Prepare content for the website', 'In Progress', 3.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-28 21:39:46', '2025-04-28 21:39:46', 0, NULL),
(36, 1, '2025-04-28', 'Procurement', 'In Progress', 126.00, 252.00, 'Delay in obtaining quotations and raising PO', NULL, 'Waiting for the equipment', 'The identification of vendors, and the approval and PO process takes a lot more time than expected. ', NULL, '2025-04-28 21:40:57', '2025-04-28 21:40:57', 0, NULL),
(37, 1, '2025-04-28', 'Industry 4.0 Experience & Insights', 'Completed', 4.00, 6.00, 'Unavailability of trainers since there was class through the day', NULL, NULL, NULL, NULL, '2025-04-28 21:41:59', '2025-04-28 21:41:59', 0, NULL),
(38, 1, '2025-04-28', 'Collaborations & Synergy', 'In Progress', 63.00, NULL, 'In progress, will update once we know the actual time', 'FW workshop structure planning', 'Refining my content and presentation according to agreed upon template', NULL, NULL, '2025-04-28 21:43:31', '2025-04-28 21:43:31', 0, NULL),
(39, 1, '2025-04-28', 'Captions and texts for posts on social media', 'In Progress', 0.50, 0.50, '-', NULL, NULL, NULL, NULL, '2025-04-28 21:44:34', '2025-04-28 21:44:34', 0, NULL),
(40, 15, '2025-04-28', 'Follow up on meetings and forum', 'In Progress', 2.00, 2.00, NULL, NULL, NULL, NULL, NULL, '2025-04-28 21:44:37', '2025-04-28 21:44:37', 0, NULL),
(41, 15, '2025-04-28', 'MoU Sharing- L&T', 'Completed', 0.50, 0.50, NULL, NULL, NULL, NULL, NULL, '2025-04-28 21:45:38', '2025-04-28 21:45:38', 0, NULL),
(42, 15, '2025-04-28', 'Sending information mail', 'Completed', 1.50, 1.50, NULL, NULL, NULL, NULL, NULL, '2025-04-28 21:46:35', '2025-04-28 21:46:35', 0, NULL),
(43, 15, '2025-04-28', 'Reach out to industries, Startups etc', 'In Progress', 0.50, 0.50, NULL, NULL, NULL, NULL, NULL, '2025-04-28 21:46:59', '2025-04-28 21:46:59', 0, NULL),
(44, 15, '2025-04-28', 'Reach out to colleges', 'In Progress', 2.00, 2.00, NULL, NULL, NULL, NULL, NULL, '2025-04-28 21:48:03', '2025-04-28 21:48:03', 0, NULL),
(45, 9, '2025-04-28', 'Content Creation of Module 2', 'In Progress', 6.00, 6.00, NULL, NULL, NULL, NULL, NULL, '2025-04-28 21:48:58', '2025-04-28 21:48:58', 0, NULL),
(46, 3, '2025-04-28', 'Project', 'Completed', 0.50, 0.50, 'None', 'None', 'None', 'None', 'None', '2025-04-28 22:05:01', '2025-04-28 22:05:01', 0, NULL),
(47, 3, '2025-04-28', 'Procurements for Electronics Prototyping (EP)', 'Completed', 0.50, 0.50, 'nil', 'nil', 'nil', 'nil', 'nil', '2025-04-28 22:07:08', '2025-04-28 22:07:08', 0, NULL),
(48, 10, '2025-04-28', 'Arranging the tables for the welding machine in the welding room', 'In Progress', 0.50, 1.00, 'None', 'None', 'None', 'None', 'None', '2025-04-28 22:58:28', '2025-04-28 22:58:28', 0, NULL),
(49, 11, '2025-04-28', 'Co ordinate with Prof. NDS', 'In Progress', 1.00, 1.50, 'vendor had to identify the supply for power and rating etc, which lead to the delay', 'Its a busy day today', 'procurement of klin', NULL, NULL, '2025-04-28 23:04:36', '2025-04-28 23:04:36', 0, NULL),
(50, 4, '2025-04-29', 'Preparing financial report every week', 'In Progress', 5.00, 5.00, '11.30am to 4.30pm', NULL, NULL, NULL, NULL, '2025-04-29 21:54:30', '2025-04-29 21:54:30', 0, NULL),
(51, 13, '2025-04-29', 'Hiring for Business Developer, System Admin, Digital Marketing Intern and Graphic Design Intern', 'In Progress', 56.00, NULL, 'Candidates not available for screening', NULL, NULL, NULL, NULL, '2025-04-29 21:56:27', '2025-04-29 21:56:27', 0, NULL),
(52, 3, '2025-04-29', 'smart irrigation system using arduino', 'Not Started', 0.50, 0.50, 'None', 'None', 'None', 'None', 'None', '2025-04-29 22:19:50', '2025-04-29 22:19:50', 0, NULL),
(53, 13, '2025-04-29', 'Organizational Policy', 'In Progress', 80.00, NULL, 'Working on Appraisal forms and reworking on JD\'s', NULL, NULL, NULL, NULL, '2025-04-29 22:22:19', '2025-04-29 22:22:19', 0, NULL),
(54, 9, '2025-04-29', 'Content Creation of Module 2', 'In Progress', 8.00, 2.00, 'Attending Practice sessions, daily meeting and Mechanical Prototyping class', NULL, NULL, NULL, NULL, '2025-04-29 22:22:51', '2025-04-29 22:22:51', 0, NULL),
(55, 10, '2025-04-29', 'Managing machines - SF - (Metal AM, Wire EDM, metal laser cutter, CO2 laser cutter, FDM 3D printer, ', 'In Progress', 4.00, 4.00, 'None', 'None', 'None', 'None', 'None', '2025-04-29 22:35:15', '2025-04-29 22:35:15', 0, NULL),
(59, 4, '2025-04-30', 'Preparing financial report every week', 'Completed', 3.00, 3.00, '', '', '', '', '', '2025-04-30 21:00:50', '2025-04-30 21:00:50', 0, 267),
(60, 4, '2025-04-30', 'Procurement & Logistics', 'In Progress', 5.00, 5.00, '', '', '', '', '', '2025-04-30 21:00:50', '2025-04-30 21:00:50', 0, 251),
(63, 12, '2025-04-30', 'Complete training by the Professors', 'In Progress', 3.00, 3.00, '', '', '', '', '', '2025-04-30 21:57:43', '2025-04-30 21:57:43', 0, 207),
(64, 12, '2025-04-30', 'Prepare content for the website', 'In Progress', 2.00, 2.00, '', '', '', '', '', '2025-04-30 21:57:43', '2025-04-30 21:57:43', 0, 234),
(65, 12, '2025-04-30', 'Coordinate with the website vendor', 'In Progress', 2.00, 2.00, '', '', '', '', '', '2025-04-30 21:57:43', '2025-04-30 21:57:43', 0, 235),
(66, 15, '2025-04-30', 'Follow up on meetings and forum', 'In Progress', 2.00, 2.00, '', '', '', '', '', '2025-04-30 22:02:08', '2025-04-30 22:02:08', 0, 281),
(67, 15, '2025-04-30', 'Organise and Coordinate 1 day workshop', 'In Progress', 1.50, 1.50, '', '', '', '', '', '2025-04-30 22:02:08', '2025-04-30 22:02:08', 0, 220),
(68, 15, '2025-04-30', 'Reach out to colleges', 'Not Started', 2.00, 2.00, '', '', '', '', '', '2025-04-30 22:02:08', '2025-04-30 22:02:08', 0, 273),
(69, 14, '2025-04-30', 'Social Media Post', 'Completed', 5.00, 4.00, '', '', 'We are preparing the Social media calendar', '', '', '2025-04-30 22:52:42', '2025-04-30 22:52:42', 0, 231),
(77, 3, '2025-04-30', 'Smart Soil Irrigation ', 'Completed', 6.00, 6.00, '', '', '', '', '', '2025-05-01 20:12:43', '2025-05-01 20:13:42', 0, NULL),
(78, 3, '2025-04-30', 'Youtube channel', 'In Progress', 2.00, 4.00, 'Channel is created and video upload is in progress', '', '', '', '', '2025-05-01 20:12:43', '2025-05-01 20:13:35', 0, 228),
(80, 15, '2025-05-02', 'Reach out to colleges', 'In Progress', 1.50, 1.50, '', '', '', '', '', '2025-05-02 22:30:50', '2025-05-02 22:30:50', 0, 273),
(81, 15, '2025-05-02', 'Organise and Coordinate 1 day workshop', 'In Progress', 1.00, 1.00, '', '', '', '', '', '2025-05-02 22:30:50', '2025-05-02 22:30:50', 0, 220),
(82, 15, '2025-05-02', 'Follow up on meetings and forum', 'In Progress', 1.50, 1.50, '', '', '', '', '', '2025-05-02 22:30:50', '2025-05-02 22:30:50', 0, 281),
(83, 15, '2025-05-02', 'Organise and coordinate meeting', 'In Progress', 2.00, 2.00, '', '', '', '', '', '2025-05-02 22:30:50', '2025-05-02 22:30:50', 0, 278),
(88, 11, '2025-05-02', 'Day to day work', 'Completed', 3.00, 3.00, '', '', '', '', '', '2025-05-02 22:52:04', '2025-05-02 22:52:04', 0, 298);

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
-- Indexes for table `Projects`
--
ALTER TABLE `Projects`
  ADD PRIMARY KEY (`ProjectID`);

--
-- Indexes for table `ProjectTasks`
--
ALTER TABLE `ProjectTasks`
  ADD PRIMARY KEY (`TaskID`),
  ADD KEY `OwnerID` (`OwnerID`),
  ADD KEY `ProjectID` (`ProjectID`);

--
-- Indexes for table `projecttasks_audit_log`
--
ALTER TABLE `projecttasks_audit_log`
  ADD PRIMARY KEY (`AuditLogID`);

--
-- Indexes for table `SATeamObservations`
--
ALTER TABLE `SATeamObservations`
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
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `WorkDiary`
--
ALTER TABLE `WorkDiary`
  ADD PRIMARY KEY (`EntryID`),
  ADD KEY `idx_entry_date` (`EntryDate`),
  ADD KEY `idx_user_id` (`UserID`),
  ADD KEY `WorkDiary_ibfk_3` (`TaskID`);

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
  MODIFY `SubmissionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `privatediaryentries`
--
ALTER TABLE `privatediaryentries`
  MODIFY `PrivateEntryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `Projects`
--
ALTER TABLE `Projects`
  MODIFY `ProjectID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ProjectTasks`
--
ALTER TABLE `ProjectTasks`
  MODIFY `TaskID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=299;

--
-- AUTO_INCREMENT for table `projecttasks_audit_log`
--
ALTER TABLE `projecttasks_audit_log`
  MODIFY `AuditLogID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=324;

--
-- AUTO_INCREMENT for table `SATeamObservations`
--
ALTER TABLE `SATeamObservations`
  MODIFY `ObservationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `taskdependencies`
--
ALTER TABLE `taskdependencies`
  MODIFY `DependencyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `WorkDiary`
--
ALTER TABLE `WorkDiary`
  MODIFY `EntryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `diarysubmissions`
--
ALTER TABLE `diarysubmissions`
  ADD CONSTRAINT `diarysubmissions_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `privatediaryentries`
--
ALTER TABLE `privatediaryentries`
  ADD CONSTRAINT `privatediaryentries_ibfk_1` FOREIGN KEY (`WorkDiaryEntryID`) REFERENCES `WorkDiary` (`EntryID`) ON DELETE CASCADE;

--
-- Constraints for table `ProjectTasks`
--
ALTER TABLE `ProjectTasks`
  ADD CONSTRAINT `ProjectTasks_ibfk_1` FOREIGN KEY (`OwnerID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `ProjectTasks_ibfk_2` FOREIGN KEY (`ProjectID`) REFERENCES `Projects` (`ProjectID`) ON DELETE CASCADE;

--
-- Constraints for table `SATeamObservations`
--
ALTER TABLE `SATeamObservations`
  ADD CONSTRAINT `SATeamObservations_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `WorkDiary`
--
ALTER TABLE `WorkDiary`
  ADD CONSTRAINT `WorkDiary_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `WorkDiary_ibfk_3` FOREIGN KEY (`TaskID`) REFERENCES `ProjectTasks` (`TaskID`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
