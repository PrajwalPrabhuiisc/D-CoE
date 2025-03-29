-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2025 at 07:38 AM
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
(18, 'AutoCAD', 'software'),
(4, 'Canva', 'software'),
(13, 'ChatGPT', 'software'),
(19, 'CircuitIO', 'software'),
(10, 'CO2 laser cutting', 'hardware'),
(12, 'Copilot', 'software'),
(5, 'Figma', 'software'),
(17, 'Fusion 360', 'software'),
(22, 'JIRA', 'software'),
(30, 'Master CAM', 'software'),
(7, 'Matlab', 'software'),
(24, 'Mentimeter', 'software'),
(14, 'MS Office tools - Word, Excel, PPT', 'software'),
(20, 'NX', 'software'),
(16, 'Onedrive', 'software'),
(2, 'Outlook', 'software'),
(29, 'Pdf Reader', 'software'),
(28, 'Photoshop', 'software'),
(15, 'Power Automate', 'software'),
(11, 'Python', 'software'),
(27, 'Rhino', 'software'),
(3, 'Sharepoint', 'software'),
(9, 'Sheet Metal Laser Cutting Machine', 'hardware'),
(26, 'SketchUp', 'software'),
(25, 'Slicer or 3D print', 'software'),
(21, 'SolidWorks', 'software'),
(6, 'TinkerCad', 'software'),
(23, 'Trello', 'software'),
(1, 'WhatsApp', 'software'),
(8, 'Wire EDM', 'hardware');

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
(1, 'Apala ', 'Chakrabarti', 'Trainer', 'apala@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(2, 'Calvin', ' Samuel', 'AM Tech support', 'calvin@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(3, 'Chetan ', 'R', 'Technical support Engineer', 'chethanr@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(4, 'Gayathri ', 'M', 'Accountant', 'gayathri@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(5, 'Hunny ', 'Bidlan', 'In-house trainer', 'hsbidlan@gmail.com', 'COE', '0000-00-00 00:00:00'),
(6, 'K Joshil ', 'Raj', 'CEO', 'joshil@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(7, 'Laveen ', 'Kumar', 'In-house trainer', 'Laveen@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(8, 'Nishath', ' Salma', 'In-house trainer', 'nsalma@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(9, 'Puneeth ', 'S', 'In-house trainer', 'puneeth@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(10, 'Siva ', 'S', 'Technical support staff', 'Siva@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(11, 'Subramanian', '', 'Technical support', 'subramanian@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(12, 'TSVV ', 'Murali', 'In-house trainer', 'venkata@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(13, 'Chytra ', 'K', 'HR', 'chytra.k@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(14, 'Ishaan ', 'Jain', 'Intern', 'Intern@fsid-iisc.in', 'COE', '0000-00-00 00:00:00');

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
(1, 1, 2, '2025-03-29 06:23:08'),
(2, 1, 3, '2025-03-29 06:23:08'),
(3, 1, 4, '2025-03-29 06:23:08'),
(4, 1, 5, '2025-03-29 06:23:08'),
(5, 1, 6, '2025-03-29 06:23:08'),
(6, 1, 7, '2025-03-29 06:23:08'),
(7, 1, 8, '2025-03-29 06:23:08'),
(8, 1, 9, '2025-03-29 06:23:08'),
(9, 1, 10, '2025-03-29 06:23:08'),
(10, 1, 11, '2025-03-29 06:23:08'),
(11, 1, 12, '2025-03-29 06:23:08'),
(12, 1, 13, '2025-03-29 06:23:08'),
(13, 1, 14, '2025-03-29 06:23:08'),
(14, 2, 1, '2025-03-29 06:23:08'),
(15, 2, 3, '2025-03-29 06:23:08'),
(16, 2, 4, '2025-03-29 06:23:08'),
(17, 2, 5, '2025-03-29 06:23:08'),
(18, 2, 6, '2025-03-29 06:23:08'),
(19, 2, 7, '2025-03-29 06:23:08'),
(20, 2, 8, '2025-03-29 06:23:08'),
(21, 2, 9, '2025-03-29 06:23:08'),
(22, 2, 10, '2025-03-29 06:23:08'),
(23, 2, 11, '2025-03-29 06:23:08'),
(24, 2, 12, '2025-03-29 06:23:08'),
(25, 2, 13, '2025-03-29 06:23:08'),
(26, 2, 14, '2025-03-29 06:23:08'),
(27, 3, 1, '2025-03-29 06:23:08'),
(28, 3, 2, '2025-03-29 06:23:08'),
(29, 3, 4, '2025-03-29 06:23:08'),
(30, 3, 5, '2025-03-29 06:23:08'),
(31, 3, 6, '2025-03-29 06:23:08'),
(32, 3, 7, '2025-03-29 06:23:08'),
(33, 3, 8, '2025-03-29 06:23:08'),
(34, 3, 9, '2025-03-29 06:23:08'),
(35, 3, 10, '2025-03-29 06:23:08'),
(36, 3, 11, '2025-03-29 06:23:08'),
(37, 3, 12, '2025-03-29 06:23:08'),
(38, 3, 13, '2025-03-29 06:23:08'),
(39, 3, 14, '2025-03-29 06:23:08'),
(40, 4, 1, '2025-03-29 06:23:08'),
(41, 4, 2, '2025-03-29 06:23:08'),
(42, 4, 3, '2025-03-29 06:23:08'),
(43, 4, 5, '2025-03-29 06:23:08'),
(44, 4, 6, '2025-03-29 06:23:08'),
(45, 4, 7, '2025-03-29 06:23:08'),
(46, 4, 8, '2025-03-29 06:23:08'),
(47, 4, 9, '2025-03-29 06:23:08'),
(48, 4, 10, '2025-03-29 06:23:08'),
(49, 4, 11, '2025-03-29 06:23:08'),
(50, 4, 12, '2025-03-29 06:23:08'),
(51, 4, 13, '2025-03-29 06:23:08'),
(52, 4, 14, '2025-03-29 06:23:08'),
(53, 5, 1, '2025-03-29 06:23:08'),
(54, 5, 2, '2025-03-29 06:23:08'),
(55, 5, 3, '2025-03-29 06:23:08'),
(56, 5, 4, '2025-03-29 06:23:08'),
(57, 5, 6, '2025-03-29 06:23:08'),
(58, 5, 7, '2025-03-29 06:23:08'),
(59, 5, 8, '2025-03-29 06:23:08'),
(60, 5, 9, '2025-03-29 06:23:08'),
(61, 5, 10, '2025-03-29 06:23:08'),
(62, 5, 11, '2025-03-29 06:23:08'),
(63, 5, 12, '2025-03-29 06:23:08'),
(64, 5, 13, '2025-03-29 06:23:08'),
(65, 5, 14, '2025-03-29 06:23:08'),
(66, 6, 1, '2025-03-29 06:23:08'),
(67, 6, 2, '2025-03-29 06:23:08'),
(68, 6, 3, '2025-03-29 06:23:08'),
(69, 6, 4, '2025-03-29 06:23:08'),
(70, 6, 5, '2025-03-29 06:23:08'),
(71, 6, 7, '2025-03-29 06:23:08'),
(72, 6, 8, '2025-03-29 06:23:08'),
(73, 6, 9, '2025-03-29 06:23:08'),
(74, 6, 10, '2025-03-29 06:23:08'),
(75, 6, 11, '2025-03-29 06:23:08'),
(76, 6, 12, '2025-03-29 06:23:08'),
(77, 6, 13, '2025-03-29 06:23:08'),
(78, 6, 14, '2025-03-29 06:23:08'),
(79, 7, 1, '2025-03-29 06:23:08'),
(80, 7, 2, '2025-03-29 06:23:08'),
(81, 7, 3, '2025-03-29 06:23:08'),
(82, 7, 4, '2025-03-29 06:23:08'),
(83, 7, 5, '2025-03-29 06:23:08'),
(84, 7, 6, '2025-03-29 06:23:08'),
(85, 7, 8, '2025-03-29 06:23:08'),
(86, 7, 9, '2025-03-29 06:23:08'),
(87, 7, 10, '2025-03-29 06:23:08'),
(88, 7, 11, '2025-03-29 06:23:08'),
(89, 7, 12, '2025-03-29 06:23:08'),
(90, 7, 13, '2025-03-29 06:23:08'),
(91, 7, 14, '2025-03-29 06:23:08'),
(92, 8, 1, '2025-03-29 06:23:08'),
(93, 8, 2, '2025-03-29 06:23:08'),
(94, 8, 3, '2025-03-29 06:23:08'),
(95, 8, 4, '2025-03-29 06:23:08'),
(96, 8, 5, '2025-03-29 06:23:08'),
(97, 8, 6, '2025-03-29 06:23:08'),
(98, 8, 7, '2025-03-29 06:23:08'),
(99, 8, 9, '2025-03-29 06:23:08'),
(100, 8, 10, '2025-03-29 06:23:08'),
(101, 8, 11, '2025-03-29 06:23:08'),
(102, 8, 12, '2025-03-29 06:23:08'),
(103, 8, 13, '2025-03-29 06:23:08'),
(104, 8, 14, '2025-03-29 06:23:08'),
(105, 9, 1, '2025-03-29 06:23:08'),
(106, 9, 2, '2025-03-29 06:23:08'),
(107, 9, 3, '2025-03-29 06:23:08'),
(108, 9, 4, '2025-03-29 06:23:08'),
(109, 9, 5, '2025-03-29 06:23:08'),
(110, 9, 6, '2025-03-29 06:23:08'),
(111, 9, 7, '2025-03-29 06:23:08'),
(112, 9, 8, '2025-03-29 06:23:08'),
(113, 9, 10, '2025-03-29 06:23:08'),
(114, 9, 11, '2025-03-29 06:23:08'),
(115, 9, 12, '2025-03-29 06:23:08'),
(116, 9, 13, '2025-03-29 06:23:08'),
(117, 9, 14, '2025-03-29 06:23:08'),
(118, 10, 1, '2025-03-29 06:23:08'),
(119, 10, 2, '2025-03-29 06:23:08'),
(120, 10, 3, '2025-03-29 06:23:08'),
(121, 10, 4, '2025-03-29 06:23:08'),
(122, 10, 5, '2025-03-29 06:23:08'),
(123, 10, 6, '2025-03-29 06:23:08'),
(124, 10, 7, '2025-03-29 06:23:08'),
(125, 10, 8, '2025-03-29 06:23:08'),
(126, 10, 9, '2025-03-29 06:23:08'),
(127, 10, 11, '2025-03-29 06:23:08'),
(128, 10, 12, '2025-03-29 06:23:08'),
(129, 10, 13, '2025-03-29 06:23:08'),
(130, 10, 14, '2025-03-29 06:23:08'),
(131, 11, 1, '2025-03-29 06:23:08'),
(132, 11, 2, '2025-03-29 06:23:08'),
(133, 11, 3, '2025-03-29 06:23:08'),
(134, 11, 4, '2025-03-29 06:23:08'),
(135, 11, 5, '2025-03-29 06:23:08'),
(136, 11, 6, '2025-03-29 06:23:08'),
(137, 11, 7, '2025-03-29 06:23:08'),
(138, 11, 8, '2025-03-29 06:23:08'),
(139, 11, 9, '2025-03-29 06:23:08'),
(140, 11, 10, '2025-03-29 06:23:08'),
(141, 11, 12, '2025-03-29 06:23:08'),
(142, 11, 13, '2025-03-29 06:23:08'),
(143, 11, 14, '2025-03-29 06:23:08'),
(144, 12, 1, '2025-03-29 06:23:08'),
(145, 12, 2, '2025-03-29 06:23:08'),
(146, 12, 3, '2025-03-29 06:23:08'),
(147, 12, 4, '2025-03-29 06:23:08'),
(148, 12, 5, '2025-03-29 06:23:08'),
(149, 12, 6, '2025-03-29 06:23:08'),
(150, 12, 7, '2025-03-29 06:23:08'),
(151, 12, 8, '2025-03-29 06:23:08'),
(152, 12, 9, '2025-03-29 06:23:08'),
(153, 12, 10, '2025-03-29 06:23:08'),
(154, 12, 11, '2025-03-29 06:23:08'),
(155, 12, 13, '2025-03-29 06:23:08'),
(156, 12, 14, '2025-03-29 06:23:08'),
(157, 13, 1, '2025-03-29 06:23:08'),
(158, 13, 2, '2025-03-29 06:23:08'),
(159, 13, 3, '2025-03-29 06:23:08'),
(160, 13, 4, '2025-03-29 06:23:08'),
(161, 13, 5, '2025-03-29 06:23:08'),
(162, 13, 6, '2025-03-29 06:23:08'),
(163, 13, 7, '2025-03-29 06:23:08'),
(164, 13, 8, '2025-03-29 06:23:08'),
(165, 13, 9, '2025-03-29 06:23:08'),
(166, 13, 10, '2025-03-29 06:23:08'),
(167, 13, 11, '2025-03-29 06:23:08'),
(168, 13, 12, '2025-03-29 06:23:08'),
(169, 13, 14, '2025-03-29 06:23:08'),
(170, 14, 1, '2025-03-29 06:23:08'),
(171, 14, 2, '2025-03-29 06:23:08'),
(172, 14, 3, '2025-03-29 06:23:08'),
(173, 14, 4, '2025-03-29 06:23:08'),
(174, 14, 5, '2025-03-29 06:23:08'),
(175, 14, 6, '2025-03-29 06:23:08'),
(176, 14, 7, '2025-03-29 06:23:08'),
(177, 14, 8, '2025-03-29 06:23:08'),
(178, 14, 9, '2025-03-29 06:23:08'),
(179, 14, 10, '2025-03-29 06:23:08'),
(180, 14, 11, '2025-03-29 06:23:08'),
(181, 14, 12, '2025-03-29 06:23:08'),
(182, 14, 13, '2025-03-29 06:23:08');

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
(1, 1, 1, '2025-03-29 06:18:06'),
(2, 1, 2, '2025-03-29 06:18:06'),
(3, 1, 3, '2025-03-29 06:18:06'),
(4, 1, 4, '2025-03-29 06:18:06'),
(5, 1, 5, '2025-03-29 06:18:06'),
(6, 1, 6, '2025-03-29 06:18:06'),
(7, 1, 7, '2025-03-29 06:18:06'),
(8, 1, 8, '2025-03-29 06:18:06'),
(9, 1, 9, '2025-03-29 06:18:06'),
(10, 1, 10, '2025-03-29 06:18:06'),
(11, 2, 11, '2025-03-29 06:18:06'),
(12, 2, 12, '2025-03-29 06:18:06'),
(13, 2, 13, '2025-03-29 06:18:06'),
(14, 2, 14, '2025-03-29 06:18:06'),
(15, 2, 15, '2025-03-29 06:18:06'),
(16, 2, 16, '2025-03-29 06:18:06'),
(17, 2, 17, '2025-03-29 06:18:06'),
(18, 3, 18, '2025-03-29 06:18:06'),
(19, 3, 19, '2025-03-29 06:18:06'),
(20, 3, 20, '2025-03-29 06:18:06'),
(21, 3, 21, '2025-03-29 06:18:06'),
(22, 4, 22, '2025-03-29 06:18:06'),
(23, 4, 23, '2025-03-29 06:18:06'),
(24, 4, 24, '2025-03-29 06:18:06'),
(25, 5, 25, '2025-03-29 06:18:06'),
(26, 5, 26, '2025-03-29 06:18:06'),
(27, 5, 18, '2025-03-29 06:18:06'),
(28, 5, 27, '2025-03-29 06:18:06'),
(29, 5, 28, '2025-03-29 06:18:06'),
(30, 5, 29, '2025-03-29 06:18:06'),
(31, 5, 30, '2025-03-29 06:18:06'),
(32, 6, 1, '2025-03-29 06:18:06'),
(33, 6, 2, '2025-03-29 06:18:06'),
(34, 6, 3, '2025-03-29 06:18:06'),
(35, 6, 4, '2025-03-29 06:18:06'),
(36, 6, 5, '2025-03-29 06:18:06'),
(37, 6, 6, '2025-03-29 06:18:06'),
(38, 6, 7, '2025-03-29 06:18:06'),
(39, 7, 18, '2025-03-29 06:18:06'),
(40, 7, 19, '2025-03-29 06:18:06'),
(41, 7, 11, '2025-03-29 06:18:06'),
(42, 7, 12, '2025-03-29 06:18:06'),
(43, 7, 13, '2025-03-29 06:18:06'),
(44, 7, 14, '2025-03-29 06:18:06'),
(45, 7, 15, '2025-03-29 06:18:06'),
(46, 7, 16, '2025-03-29 06:18:06'),
(47, 8, 20, '2025-03-29 06:18:06'),
(48, 8, 21, '2025-03-29 06:18:06'),
(49, 9, 22, '2025-03-29 06:18:06'),
(50, 9, 23, '2025-03-29 06:18:06'),
(51, 9, 24, '2025-03-29 06:18:06'),
(52, 10, 25, '2025-03-29 06:18:06'),
(53, 10, 26, '2025-03-29 06:18:06'),
(54, 10, 18, '2025-03-29 06:18:06'),
(55, 10, 27, '2025-03-29 06:18:06'),
(56, 10, 28, '2025-03-29 06:18:06'),
(57, 10, 29, '2025-03-29 06:18:06'),
(58, 10, 30, '2025-03-29 06:18:06');

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
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tools`
--
ALTER TABLE `tools`
  MODIFY `tool_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_people`
--
ALTER TABLE `user_people`
  MODIFY `user_people_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT for table `user_tools`
--
ALTER TABLE `user_tools`
  MODIFY `user_tool_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

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
