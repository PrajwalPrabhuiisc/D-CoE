-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2025 at 05:19 PM
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
-- Database: `new_survey`
--

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `question_text` varchar(500) NOT NULL,
  `variable_code` varchar(10) DEFAULT NULL,
  `example` varchar(1000) DEFAULT NULL,
  `category` enum('analog','hardware','software','human') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `question_text`, `variable_code`, `example`, `category`) VALUES
(1, 'How available should this analog source be?', 'A', 'Blueprints should be available on-site.', 'analog'),
(2, 'How reliable is this analog source?', 'B', 'Accurate blueprints reduce errors.', 'analog'),
(3, 'How available should this hardware be?', 'A', 'Safety shoes must be on-site.', 'hardware'),
(4, 'How reliable is this hardware?', 'B', 'Shoes must meet safety standards.', 'hardware'),
(5, 'How available should this software be?', 'A', 'Software should be accessible remotely.', 'software'),
(6, 'How accurate is this software?', 'B', 'Accurate data is critical.', 'software'),
(7, 'How often do you communicate with this person?', 'A', 'Daily updates may be needed.', 'human'),
(8, 'How trustable is their information?', 'B', 'Reliable info ensures progress.', 'human'),
(9, 'How central is this analog source?', 'C', 'Blueprints are key to projects.', 'analog'),
(10, 'How often do you update this hardware?', 'C', 'Regular checks ensure functionality.', 'hardware'),
(11, 'How central is this software?', 'C', 'Software drives project management.', 'software'),
(12, 'How easy is it to contact this person?', 'C', 'Quick access improves coordination.', 'human'),
(13, 'How accurate should this analog be?', 'D', 'Precision avoids rework.', 'analog'),
(14, 'How often do you back up this hardware?', 'D', 'Backups prevent downtime.', 'hardware'),
(15, 'How updated are you on this software?', 'D', 'Staying updated boosts efficiency.', 'software'),
(16, 'How well-defined are their roles?', 'D', 'Clear roles improve teamwork.', 'human'),
(17, 'How useful is this analog source?', 'E', 'Useful tools aid execution.', 'analog'),
(18, 'How durable is this hardware?', 'E', 'Durable tools last longer.', 'hardware'),
(19, 'How intuitive is this software?', 'E', 'Ease of use saves time.', 'software'),
(20, 'How skilled is this person?', 'E', 'Skills impact project success.', 'human');

-- --------------------------------------------------------

--
-- Table structure for table `question_options`
--

CREATE TABLE `question_options` (
  `option_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` varchar(500) NOT NULL,
  `option_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_options`
--

INSERT INTO `question_options` (`option_id`, `question_id`, `option_text`, `option_order`) VALUES
(1, 1, 'Not available', 1),
(2, 1, 'Low availability', 2),
(3, 1, 'Moderate availability', 3),
(4, 1, 'High availability', 4),
(5, 1, 'Very high availability', 5),
(6, 3, 'Not available', 1),
(7, 3, 'Low availability', 2),
(8, 3, 'Moderate availability', 3),
(9, 3, 'High availability', 4),
(10, 3, 'Very high availability', 5),
(11, 5, 'Not required', 1),
(12, 5, 'Low requirement', 2),
(13, 5, 'Moderate requirement', 3),
(14, 5, 'High requirement', 4),
(15, 5, 'Very high requirement', 5),
(16, 7, 'Daily', 1),
(17, 7, 'Weekly', 2),
(18, 7, 'Biweekly', 3),
(19, 7, 'Monthly', 4),
(20, 7, 'Rarely', 5),
(21, 8, 'Not Trustable', 1),
(22, 8, 'Slightly Trustable', 2),
(23, 8, 'Moderately Trustable', 3),
(24, 8, 'Very Trustable', 4),
(25, 8, 'Completely Trustable', 5),
(26, 12, 'Very Difficult', 1),
(27, 12, 'Somewhat Difficult', 2),
(28, 12, 'Neutral', 3),
(29, 12, 'Somewhat Easy', 4),
(30, 12, 'Very Easy', 5),
(31, 16, 'Not Defined', 1),
(32, 16, 'Poorly Defined', 2),
(33, 16, 'Moderately Defined', 3),
(34, 16, 'Well Defined', 4),
(35, 16, 'Very Well Defined', 5),
(36, 20, 'Not Skilled', 1),
(37, 20, 'Slightly Skilled', 2),
(38, 20, 'Moderately Skilled', 3),
(39, 20, 'Highly Skilled', 4),
(40, 20, 'Expert', 5);

-- --------------------------------------------------------

--
-- Table structure for table `responses`
--

CREATE TABLE `responses` (
  `response_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `responses`
--

INSERT INTO `responses` (`response_id`, `user_id`, `question_id`, `option_id`, `timestamp`) VALUES
(1, 1, 1, 4, '2025-03-25 07:52:03'),
(2, 2, 1, 3, '2025-03-25 07:52:03'),
(3, 3, 1, 5, '2025-03-25 07:52:03'),
(4, 4, 1, 2, '2025-03-25 07:52:03'),
(5, 5, 3, 4, '2025-03-25 07:52:03'),
(6, 6, 3, 5, '2025-03-25 07:52:03'),
(7, 7, 3, 3, '2025-03-25 07:52:03'),
(8, 8, 3, 1, '2025-03-25 07:52:03'),
(9, 9, 5, 4, '2025-03-25 07:52:03'),
(10, 10, 5, 5, '2025-03-25 07:52:03'),
(11, 11, 5, 3, '2025-03-25 07:52:03'),
(12, 12, 5, 2, '2025-03-25 07:52:03'),
(13, 13, 7, 1, '2025-03-25 07:52:03'),
(14, 14, 7, 2, '2025-03-25 07:52:03'),
(15, 15, 7, 3, '2025-03-25 07:52:03'),
(16, 16, 7, 4, '2025-03-25 07:52:03'),
(17, 17, 1, 5, '2025-03-25 07:52:03'),
(18, 18, 3, 4, '2025-03-25 07:52:03'),
(19, 19, 5, 5, '2025-03-25 07:52:03'),
(20, 20, 7, 5, '2025-03-25 07:52:03'),
(21, 1, 7, 16, '2025-03-25 09:16:04'),
(22, 1, 5, 11, '2025-03-25 09:16:04'),
(23, 1, 7, 17, '2025-03-25 09:16:04'),
(24, 1, 1, 3, '2025-03-25 09:16:04'),
(25, 3, 7, 16, '2025-03-25 09:33:03'),
(26, 3, 7, 17, '2025-03-25 09:33:03'),
(27, 3, 7, 16, '2025-03-25 09:48:19'),
(28, 3, 5, 12, '2025-03-25 09:48:19'),
(29, 3, 7, 18, '2025-03-25 09:48:19'),
(30, 3, 7, 16, '2025-03-25 09:48:19'),
(31, 3, 7, 19, '2025-03-25 09:48:19'),
(32, 3, 3, 9, '2025-03-25 09:48:19'),
(33, 3, 3, 7, '2025-03-25 09:48:19'),
(34, 2, 7, 16, '2025-03-25 09:54:00'),
(35, 2, 7, 18, '2025-03-25 09:54:00'),
(36, 2, 7, 16, '2025-03-25 09:54:00'),
(37, 2, 7, 18, '2025-03-25 09:54:00'),
(38, 2, 5, 12, '2025-03-25 09:54:00'),
(39, 2, 5, 12, '2025-03-25 09:54:00'),
(40, 2, 3, 7, '2025-03-25 09:54:00'),
(41, 2, 3, 7, '2025-03-25 09:54:00'),
(42, 2, 1, 1, '2025-03-25 09:54:00'),
(43, 2, 7, 16, '2025-03-25 10:32:05'),
(44, 2, 7, 17, '2025-03-25 10:32:06'),
(45, 2, 7, 16, '2025-03-25 10:32:06'),
(46, 2, 7, 17, '2025-03-25 10:32:06'),
(47, 2, 5, 12, '2025-03-25 10:32:06'),
(48, 2, 5, 12, '2025-03-25 10:32:06'),
(49, 2, 3, 6, '2025-03-25 10:32:06'),
(50, 3, 7, 16, '2025-03-26 01:26:15'),
(51, 3, 7, 17, '2025-03-26 01:26:15'),
(52, 3, 5, 11, '2025-03-26 01:26:15'),
(53, 3, 5, 13, '2025-03-26 01:26:15'),
(54, 3, 3, 6, '2025-03-26 01:26:15'),
(55, 3, 3, 8, '2025-03-26 01:26:15'),
(56, 3, 1, 1, '2025-03-26 01:26:15'),
(57, 3, 1, 2, '2025-03-26 01:26:15'),
(58, 1, 7, 16, '2025-03-26 01:52:07'),
(59, 1, 7, 18, '2025-03-26 01:52:07'),
(60, 1, 7, 16, '2025-03-26 01:52:07'),
(61, 1, 7, 18, '2025-03-26 01:52:07'),
(62, 1, 7, 17, '2025-03-26 01:52:07'),
(63, 1, 7, 19, '2025-03-26 01:52:07'),
(64, 1, 7, 20, '2025-03-26 01:52:07'),
(65, 1, 7, 16, '2025-03-26 01:52:07'),
(66, 1, 7, 18, '2025-03-26 01:52:07'),
(67, 1, 7, 17, '2025-03-26 01:52:07'),
(68, 1, 7, 19, '2025-03-26 01:52:07'),
(69, 1, 7, 20, '2025-03-26 01:52:07'),
(70, 1, 5, 11, '2025-03-26 01:52:07'),
(71, 1, 5, 13, '2025-03-26 01:52:07'),
(72, 1, 5, 11, '2025-03-26 01:52:07'),
(73, 1, 5, 13, '2025-03-26 01:52:07'),
(74, 1, 3, 6, '2025-03-26 01:52:07'),
(75, 1, 3, 8, '2025-03-26 01:52:07'),
(76, 1, 3, 6, '2025-03-26 01:52:07'),
(77, 1, 3, 8, '2025-03-26 01:52:07'),
(78, 1, 3, 6, '2025-03-26 01:52:07'),
(79, 1, 3, 8, '2025-03-26 01:52:07'),
(80, 1, 3, 6, '2025-03-26 01:52:07'),
(81, 1, 3, 8, '2025-03-26 01:52:07'),
(82, 1, 3, 6, '2025-03-26 01:52:07'),
(83, 1, 3, 8, '2025-03-26 01:52:07'),
(84, 1, 1, 1, '2025-03-26 01:52:07'),
(85, 1, 7, 17, '2025-03-26 05:56:51'),
(86, 1, 7, 19, '2025-03-26 05:56:51'),
(87, 1, 7, 17, '2025-03-26 05:56:51'),
(88, 1, 5, 13, '2025-03-26 05:56:51'),
(89, 1, 3, 9, '2025-03-26 05:56:51'),
(90, 1, 1, 4, '2025-03-26 05:56:51'),
(91, 3, 7, 18, '2025-03-26 06:07:43'),
(92, 3, 7, 19, '2025-03-26 06:07:43'),
(93, 3, 5, 11, '2025-03-26 06:07:43'),
(94, 3, 3, 6, '2025-03-26 06:07:43'),
(95, 3, 1, 2, '2025-03-26 06:07:43'),
(96, 10, 7, 16, '2025-03-26 09:44:15'),
(97, 10, 8, 21, '2025-03-26 09:44:15'),
(98, 10, 12, 26, '2025-03-26 09:44:15'),
(99, 10, 16, 31, '2025-03-26 09:44:15'),
(100, 10, 20, 36, '2025-03-26 09:44:15'),
(101, 10, 7, 17, '2025-03-26 09:44:15'),
(102, 10, 8, 22, '2025-03-26 09:44:15'),
(103, 10, 12, 27, '2025-03-26 09:44:15'),
(104, 10, 16, 32, '2025-03-26 09:44:15'),
(105, 10, 20, 37, '2025-03-26 09:44:15'),
(106, 10, 7, 18, '2025-03-26 09:44:15'),
(107, 10, 8, 23, '2025-03-26 09:44:15'),
(108, 10, 12, 28, '2025-03-26 09:44:15'),
(109, 10, 16, 33, '2025-03-26 09:44:15'),
(110, 10, 20, 39, '2025-03-26 09:44:15'),
(111, 10, 5, 11, '2025-03-26 09:44:15'),
(112, 10, 3, 6, '2025-03-26 09:44:15'),
(113, 10, 1, 1, '2025-03-26 09:44:15'),
(114, 4, 5, 12, '2025-03-26 09:45:50'),
(115, 4, 5, 14, '2025-03-26 09:45:50'),
(116, 4, 3, 7, '2025-03-26 09:45:50'),
(117, 4, 1, 2, '2025-03-26 09:45:50'),
(118, 4, 5, 11, '2025-03-27 06:25:09'),
(119, 4, 5, 13, '2025-03-27 06:25:09'),
(120, 4, 5, 11, '2025-03-27 06:25:09'),
(121, 4, 5, 13, '2025-03-27 06:25:09'),
(122, 4, 5, 13, '2025-03-27 06:25:09'),
(123, 4, 5, 13, '2025-03-27 06:25:09'),
(124, 4, 5, 13, '2025-03-27 06:25:09'),
(125, 4, 1, 1, '2025-03-27 06:25:09'),
(126, 7, 7, 16, '2025-03-27 06:49:14'),
(127, 7, 8, 21, '2025-03-27 06:49:14'),
(128, 7, 12, 26, '2025-03-27 06:49:14'),
(129, 7, 16, 32, '2025-03-27 06:49:14'),
(130, 7, 20, 36, '2025-03-27 06:49:14'),
(131, 7, 7, 18, '2025-03-27 06:49:14'),
(132, 7, 8, 23, '2025-03-27 06:49:14'),
(133, 7, 12, 27, '2025-03-27 06:49:14'),
(134, 7, 16, 34, '2025-03-27 06:49:14'),
(135, 7, 20, 38, '2025-03-27 06:49:14'),
(136, 7, 1, 1, '2025-03-27 06:49:14');

-- --------------------------------------------------------

--
-- Table structure for table `tools`
--

CREATE TABLE `tools` (
  `tool_id` int(11) NOT NULL,
  `tool_name` varchar(500) NOT NULL,
  `tool_type` enum('software','hardware','analog') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tools`
--

INSERT INTO `tools` (`tool_id`, `tool_name`, `tool_type`) VALUES
(1, 'AutoCAD', 'software'),
(13, 'Blueprint', 'analog'),
(16, 'Excel', 'software'),
(18, 'Keyboard', 'hardware'),
(6, 'Laptop', 'hardware'),
(15, 'Marker', 'analog'),
(7, 'Monitor', 'hardware'),
(10, 'Mouse', 'hardware'),
(3, 'MS Word', 'software'),
(12, 'Notebook', 'analog'),
(11, 'Pen', 'analog'),
(14, 'Pencil', 'analog'),
(5, 'Photoshop', 'software'),
(8, 'Printer', 'hardware'),
(19, 'Projector', 'analog'),
(2, 'Revit', 'software'),
(9, 'Safety Shoes', 'hardware'),
(17, 'SketchUp', 'software'),
(4, 'SolidWorks', 'software'),
(20, 'Tablet', 'hardware');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(500) NOT NULL,
  `last_name` varchar(500) NOT NULL,
  `designation` varchar(500) DEFAULT NULL,
  `email_id` varchar(500) NOT NULL,
  `department` varchar(500) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `designation`, `email_id`, `department`, `timestamp`) VALUES
(1, 'John', 'Doe', 'Project Manager', 'john.doe@example.com', 'Engineering', '2025-03-25 07:52:03'),
(2, 'Jane', 'Smith', 'Designer', 'jane.smith@example.com', 'Design', '2025-03-25 07:52:03'),
(3, 'Alice', 'Johnson', 'Developer', 'alice.j@example.com', 'IT', '2025-03-25 07:52:03'),
(4, 'Bob', 'Brown', 'Architect', 'bob.brown@example.com', 'Architecture', '2025-03-25 07:52:03'),
(5, 'Emma', 'Wilson', 'Coordinator', 'emma.wilson@example.com', 'Operations', '2025-03-25 07:52:03'),
(6, 'Michael', 'Davis', 'Engineer', 'michael.d@example.com', 'Engineering', '2025-03-25 07:52:03'),
(7, 'Sarah', 'Taylor', 'Analyst', 'sarah.t@example.com', 'Analytics', '2025-03-25 07:52:03'),
(8, 'David', 'Lee', 'Consultant', 'david.lee@example.com', 'Consulting', '2025-03-25 07:52:03'),
(9, 'Laura', 'Martinez', 'Supervisor', 'laura.m@example.com', 'Management', '2025-03-25 07:52:03'),
(10, 'Chris', 'Garcia', 'Technician', 'chris.g@example.com', 'Field Tech', '2025-03-25 07:52:03'),
(11, 'Emily', 'Clark', 'Project Engineer', 'emily.c@example.com', 'Engineering', '2025-03-25 07:52:03'),
(12, 'Tom', 'Rodriguez', 'Designer', 'tom.r@example.com', 'Design', '2025-03-25 07:52:03'),
(13, 'Olivia', 'Lopez', 'Developer', 'olivia.l@example.com', 'IT', '2025-03-25 07:52:03'),
(14, 'James', 'Hernandez', 'Manager', 'james.h@example.com', 'Management', '2025-03-25 07:52:03'),
(15, 'Sophia', 'Moore', 'Coordinator', 'sophia.m@example.com', 'Operations', '2025-03-25 07:52:03'),
(16, 'Daniel', 'Walker', 'Architect', 'daniel.w@example.com', 'Architecture', '2025-03-25 07:52:03'),
(17, 'Mia', 'Perez', 'Analyst', 'mia.p@example.com', 'Analytics', '2025-03-25 07:52:03'),
(18, 'Ethan', 'Gonzalez', 'Consultant', 'ethan.g@example.com', 'Consulting', '2025-03-25 07:52:03'),
(19, 'Isabella', 'Adams', 'Engineer', 'isabella.a@example.com', 'Engineering', '2025-03-25 07:52:03'),
(20, 'Liam', 'Nelson', 'Technician', 'liam.n@example.com', 'Field Tech', '2025-03-25 07:52:03');

-- --------------------------------------------------------

--
-- Table structure for table `user_people`
--

CREATE TABLE `user_people` (
  `user_people_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_people`
--

INSERT INTO `user_people` (`user_people_id`, `user_id`, `person_id`, `timestamp`) VALUES
(1, 1, 2, '2025-03-25 07:52:03'),
(2, 1, 3, '2025-03-25 07:52:03'),
(3, 2, 4, '2025-03-25 07:52:03'),
(4, 3, 5, '2025-03-25 07:52:03'),
(5, 4, 6, '2025-03-25 07:52:03'),
(6, 5, 7, '2025-03-25 07:52:03'),
(7, 6, 8, '2025-03-25 07:52:03'),
(8, 7, 9, '2025-03-25 07:52:03'),
(9, 8, 10, '2025-03-25 07:52:03'),
(10, 9, 11, '2025-03-25 07:52:03'),
(11, 10, 12, '2025-03-25 07:52:03'),
(12, 11, 13, '2025-03-25 07:52:03'),
(13, 12, 14, '2025-03-25 07:52:03'),
(14, 13, 15, '2025-03-25 07:52:03'),
(15, 14, 16, '2025-03-25 07:52:03'),
(16, 15, 17, '2025-03-25 07:52:03'),
(17, 16, 18, '2025-03-25 07:52:03'),
(18, 17, 19, '2025-03-25 07:52:03'),
(19, 18, 20, '2025-03-25 07:52:03'),
(20, 19, 1, '2025-03-25 07:52:03');

-- --------------------------------------------------------

--
-- Table structure for table `user_tools`
--

CREATE TABLE `user_tools` (
  `user_tool_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tool_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_tools`
--

INSERT INTO `user_tools` (`user_tool_id`, `user_id`, `tool_id`, `timestamp`) VALUES
(1, 1, 1, '2025-03-25 07:52:03'),
(2, 1, 6, '2025-03-25 07:52:03'),
(3, 2, 3, '2025-03-25 07:52:03'),
(4, 2, 11, '2025-03-25 07:52:03'),
(5, 3, 4, '2025-03-25 07:52:03'),
(6, 3, 6, '2025-03-25 07:52:03'),
(7, 4, 2, '2025-03-25 07:52:03'),
(8, 4, 13, '2025-03-25 07:52:03'),
(9, 5, 16, '2025-03-25 07:52:03'),
(10, 5, 6, '2025-03-25 07:52:03'),
(11, 6, 9, '2025-03-25 07:52:03'),
(12, 6, 11, '2025-03-25 07:52:03'),
(13, 7, 3, '2025-03-25 07:52:03'),
(14, 7, 12, '2025-03-25 07:52:03'),
(15, 8, 5, '2025-03-25 07:52:03'),
(16, 8, 6, '2025-03-25 07:52:03'),
(17, 9, 7, '2025-03-25 07:52:03'),
(18, 9, 14, '2025-03-25 07:52:03'),
(19, 10, 18, '2025-03-25 07:52:03'),
(20, 10, 15, '2025-03-25 07:52:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`);

--
-- Indexes for table `question_options`
--
ALTER TABLE `question_options`
  ADD PRIMARY KEY (`option_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `responses`
--
ALTER TABLE `responses`
  ADD PRIMARY KEY (`response_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `option_id` (`option_id`);

--
-- Indexes for table `tools`
--
ALTER TABLE `tools`
  ADD PRIMARY KEY (`tool_id`),
  ADD UNIQUE KEY `tool_name` (`tool_name`,`tool_type`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_people`
--
ALTER TABLE `user_people`
  ADD PRIMARY KEY (`user_people_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`person_id`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `user_tools`
--
ALTER TABLE `user_tools`
  ADD PRIMARY KEY (`user_tool_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`tool_id`),
  ADD KEY `tool_id` (`tool_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `question_options`
--
ALTER TABLE `question_options`
  MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `responses`
--
ALTER TABLE `responses`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `tools`
--
ALTER TABLE `tools`
  MODIFY `tool_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `user_people`
--
ALTER TABLE `user_people`
  MODIFY `user_people_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `user_tools`
--
ALTER TABLE `user_tools`
  MODIFY `user_tool_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `question_options`
--
ALTER TABLE `question_options`
  ADD CONSTRAINT `question_options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`);

--
-- Constraints for table `responses`
--
ALTER TABLE `responses`
  ADD CONSTRAINT `responses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `responses_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`),
  ADD CONSTRAINT `responses_ibfk_3` FOREIGN KEY (`option_id`) REFERENCES `question_options` (`option_id`);

--
-- Constraints for table `user_people`
--
ALTER TABLE `user_people`
  ADD CONSTRAINT `user_people_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_people_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_tools`
--
ALTER TABLE `user_tools`
  ADD CONSTRAINT `user_tools_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_tools_ibfk_2` FOREIGN KEY (`tool_id`) REFERENCES `tools` (`tool_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
