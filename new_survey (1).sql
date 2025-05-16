-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2025 at 09:04 AM
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
(1, 'How frequently were you expecting to communicate with this team member based on the organizational plan?', 'A', 'The organizational plan expected daily check-ins with the sales manager to align on client priorities via email or calls.', 'human'),
(2, 'How frequently do you actually communicate with this team member in practice?', 'B', 'In practice, communication with the sales manager is only weekly during team meetings due to their travel schedule.', 'human'),
(3, 'How easy were you expecting it to be to access this team member when needed based on the organizational plan?', 'C', 'The organizational plan expected the IT support lead to be very easy to reach via chat or phone for urgent technical issues.', 'human'),
(4, 'How easy is it actually to access this team member when needed in practice?', 'D', 'In practice, accessing the IT support lead is difficult due to their involvement in multiple projects, with responses delayed by hours.', 'human'),
(5, 'To what extent were you expecting this team member’s specialized roles and responsibilities to be clearly defined and supported through training based on the organizational plan?', 'E', 'The organizational plan expected the HR coordinator’s role in onboarding to be completely defined with training on new hiring protocols.', 'human'),
(6, 'To what extent is this team member actually performing their specialized roles and responsibilities with adequate training support in practice?', 'F', 'In practice, the HR coordinator’s role is only moderately supported, as training was minimal, leading to inconsistent onboarding processes.', 'human'),
(7, 'How important were you expecting the information shared by this team member to be for trustworthy?', 'G', 'The organizational plan expected the finance analyst’s budget forecasts to be very important and trustworthy for strategic planning.', 'human'),
(8, 'How trustworthy is the information actually shared by this team member in practice?', 'H', 'In practice, the finance analyst’s forecasts are only slightly trustworthy due to occasional data errors, requiring verification.', 'human'),
(9, 'How frequently were you expecting to enhance this team member’s capabilities through training or knowledge sharing?', 'I', 'The organizational plan expected frequent workshops to train the marketing associate on new campaign strategies.', 'human'),
(10, 'How frequently do you actually enhance this team member’s capabilities through training or knowledge sharing in practice?', 'J', 'In practice, training for the marketing associate is rare due to budget constraints, limiting their strategic contributions.', 'human'),
(11, 'How frequently were you expecting to be updated about changes in this team member’s duties, job status, or responsibilities based on the organizational plan?', 'K', 'The organizational plan expected monthly updates from HR about changes in the team lead’s responsibilities, such as new project assignments.', 'human'),
(12, 'How frequently are you actually updated about changes in this team member’s duties, job status, or responsibilities in practice?', 'L', 'In practice, updates about the team lead’s responsibilities are less than monthly, often learned through informal chats.', 'human'),
(13, 'To what extent were you expecting a plan to ensure team members know who to contact for needed information and how to reach them (e.g., via intranet, org charts) based on the organizational plan?', 'M', 'The organizational plan expected definitely yes for an intranet directory listing all team members, roles, and contacts for quick reference.', 'human'),
(14, 'To what extent do team members actually know who to contact for needed information and how to reach them (e.g., via intranet, org charts) in practice?', 'N', 'In practice, the intranet directory is probably no help, as it’s outdated and lacks new hires’ details.', 'human'),
(15, 'To what extent were you expecting a substitute team member to be trained to handle this team member’s responsibilities if they leave based on the organizational plan?', 'O', 'The organizational plan expected probably yes for a backup sales rep to be trained to handle the primary rep’s client portfolio.', 'human'),
(16, 'To what extent is a substitute team member actually trained to handle this team member’s responsibilities if they leave in practice?', 'P', 'In practice, there is definitely no trained backup, risking client disruptions if the primary rep leaves.', 'human'),
(17, 'To what extent were you expecting information shared with this team member to be free of loss or misinterpretation based on the organizational plan?', 'Q', 'The organizational plan expected meeting notes shared with the project manager to be very important and free of misinterpretation for task alignment.', 'human'),
(18, 'To what extent is the information actually shared with this team member free of loss or misinterpretation in practice?', 'R', 'In practice, meeting notes are only slightly free of misinterpretation due to unclear verbal briefings, causing task confusion.', 'human'),
(19, 'To what extent were you expecting this information system to be accessible (e.g., via authenticated remote access on mobile, web, or office computers) based on organizational requirements?', 'S', 'The organizational plan expected the CRM software (e.g., Salesforce) to be highly accessible via authenticated web and mobile access for sales managers working remotely.', 'software'),
(20, 'To what extent is this information system actually accessible (e.g., via authenticated remote access on mobile, web, or office computers) in practice?', 'T', 'In practice, the CRM software is only moderately accessible due to recurring single sign-on issues, hindering remote access.', 'software'),
(21, 'To what extent were you expecting this information system to support your organizational roles and responsibilities (e.g., customer tracking, reporting) based on the organizational plan?', 'U', 'The organizational plan expected the ERP system (e.g., SAP) to largely automate inventory tracking and financial reporting for the accounting team.', 'software'),
(22, 'To what extent does this information system actually support your organizational roles and responsibilities (e.g., customer tracking, reporting) in practice?', 'V', 'In practice, the ERP system only moderately supports these tasks due to limited integration with external vendors, requiring manual data entry.', 'software'),
(23, 'How important were you expecting the accuracy and reliability of this information system’s outputs (e.g., reports, data) to be for meeting organizational requirements?', 'W', 'The organizational plan expected the accounting software (e.g., QuickBooks) to produce very important, accurate financial reports for budget planning.', 'software'),
(24, 'How accurate and reliable are this information system’s outputs (e.g., reports, data) actually in practice?', 'X', 'In practice, the accounting software’s reports are only slightly accurate due to data import errors, requiring manual corrections.', 'software'),
(25, 'How frequently were you expecting to be updated about new features, versions, or changes in this information system based on the organizational plan?', 'Y', 'The organizational plan expected frequent notifications about updates to the collaboration platform (e.g., Microsoft Teams) to ensure employees use new productivity features.', 'software'),
(26, 'How frequently are you actually updated about new features, versions, or changes in this information system in practice?', 'Z', 'In practice, employees are rarely updated about Teams updates, often missing new features like enhanced file sharing.', 'software'),
(27, 'To what extent were you expecting automated backup mechanisms (e.g., cloud storage, servers) to be in place for this information system to prevent data loss based on the organizational plan?', 'AA', 'The organizational plan expected definitely yes for cloud backups of the CRM system to prevent customer data loss.', 'software'),
(28, 'To what extent are automated backup mechanisms (e.g., cloud storage, servers) actually in place and managed for this information system in practice?', 'AB', 'In practice, backups are probably no, as the CRM system’s cloud storage is inconsistently synced, risking data loss.', 'software'),
(29, 'To what extent were you expecting to know what information is available in this information system and how to access it (e.g., via menus, help files) based on the organizational plan?', 'AC', 'The organizational plan expected the marketing team to know very well how to navigate the CRM system to access customer data via its dashboards.', 'software'),
(30, 'To what extent do you actually know what information is available in this information system and how to access it (e.g., via menus, help files) in practice?', 'AD', 'In practice, the marketing team knows only moderately well, as the CRM’s menus are complex, making data retrieval difficult.', 'software'),
(31, 'To what extent were you expecting this information system to serve as a central hub for organizational data (e.g., integrating customer, financial data) based on the organizational plan?', 'AE', 'The organizational plan expected the ERP system to completely serve as a central hub, integrating all customer and financial data for the operations team.', 'software'),
(32, 'To what extent does this information system actually serve as a central hub for organizational data (e.g., integrating customer, financial data) in practice?', 'AF', 'In practice, the ERP system only moderately integrates data, as some departments still use separate spreadsheets.', 'software'),
(33, 'How frequently were you expecting this information system to be updated with new rules, libraries, or features to enhance its capabilities based on the organizational plan?', 'AG', 'The organizational plan expected frequent updates to the HR software’s compliance libraries to support the HR team’s regulatory tasks.', 'software'),
(34, 'How frequently is this information system actually updated with new rules, libraries, or features to enhance its capabilities in practice?', 'AH', 'In practice, the HR software is rarely updated, with outdated libraries limiting its usefulness for new regulations.', 'software'),
(35, 'To what extent were you expecting data entered into or retrieved from this information system to be free of loss or misinterpretation (e.g., correct data formats) based on the organizational plan?', 'AI', 'The organizational plan expected data entered into the CRM system to be very important and free of errors for accurate customer analytics by the sales team.', 'software'),
(36, 'To what extent is data entered into or retrieved from this information system actually free of loss or misinterpretation (e.g., correct data formats) in practice?', 'AJ', 'In practice, data is only slightly free of errors, as the CRM misinterprets imported formats, leading to incorrect analytics.', 'software'),
(37, 'To what extent were you expecting this hardware to be available for use (e.g., in offices, data centers, or remotely) based on organizational requirements?', 'AK', 'The organizational plan expected the office printer to be highly available in all departments for daily printing needs.', 'hardware'),
(38, 'To what extent is this hardware actually available for use (e.g., in offices, data centers, or remotely) in practice?', 'AL', 'In practice, the office printer is only moderately available due to frequent network connectivity issues, causing delays in printing.', 'hardware'),
(39, 'To what extent were you expecting this hardware to support or perform your organizational roles and responsibilities (e.g., data storage, authentication) based on the organizational plan?', 'AM', 'The organizational plan expected the biometric scanner to largely perform employee attendance tracking for the HR team, reducing manual logs.', 'hardware'),
(40, 'To what extent does this hardware actually support or perform your organizational roles and responsibilities (e.g., data storage, authentication) in practice?', 'AN', 'In practice, the biometric scanner only moderately supports attendance tracking due to slow processing, requiring manual backups.', 'hardware'),
(41, 'How important were you expecting the accuracy and reliability of this hardware (e.g., meeting performance or security standards) to be for fulfilling organizational requirements?', 'AO', 'The organizational plan expected the server to be very important and reliable for secure data storage, trusted by the IT team.', 'hardware'),
(42, 'How accurate and reliable is this hardware (e.g., meeting performance or security standards) actually in practice?', 'AP', 'In practice, the server is only slightly reliable due to occasional outages, requiring frequent IT interventions.', 'hardware'),
(43, 'How frequently were you expecting to be updated about maintenance, firmware, or new features of this hardware based on the organizational plan?', 'AQ', 'The organizational plan expected frequent updates on the conference room projector’s firmware to ensure compatibility with new devices.', 'hardware'),
(44, 'How frequently are you actually updated about maintenance, firmware, or new features of this hardware in practice?', 'AR', 'In practice, the administrative staff is rarely updated about projector firmware, leading to compatibility issues during presentations.', 'hardware'),
(45, 'To what extent were you expecting backup hardware or personnel to be available in case of this hardware’s failure based on the organizational plan?', 'AS', 'The organizational plan expected probably yes for a backup server to be available in case the primary server fails.', 'hardware'),
(46, 'To what extent is backup hardware or personnel actually available in case of this hardware’s failure in practice?', 'AT', 'In practice, there is definitely no backup server, risking data access during outages.', 'hardware'),
(47, 'To what extent were you expecting to know where this hardware is located and how to access it (e.g., via IT inventories or asset logs) based on the organizational plan?', 'AU', 'The organizational plan expected the IT staff to know very well where the office printer is located via the IT asset management system.', 'hardware'),
(48, 'To what extent do you actually know where this hardware is located and how to access it (e.g., via IT inventories or asset logs) in practice?', 'AV', 'In practice, the IT staff knows only moderately well, as the asset management system is outdated, making it hard to locate printers.', 'hardware'),
(49, 'To what extent were you expecting training or updates to enhance your ability to utilize this hardware effectively (e.g., new features, maintenance skills) based on the organizational plan?', 'AW', 'The organizational plan expected to a large extent that employees receive training on new features of the biometric scanner to improve attendance tracking.', 'hardware'),
(50, 'To what extent have you actually received training or updates to enhance your ability to utilize this hardware effectively (e.g., new features, maintenance skills) in practice?', 'AX', 'In practice, training is to a small extent, with employees unaware of new scanner features, reducing efficiency.', 'hardware'),
(51, 'To what extent were you expecting the information provided by this hardware (e.g., status, performance feedback) to be accurate and free of misinterpretation based on the organizational plan?', 'AY', 'The organizational plan expected the server’s performance feedback (e.g., uptime reports) to be very important and accurate for IT monitoring.', 'hardware'),
(52, 'To what extent is the information provided by this hardware (e.g., status, performance feedback) actually accurate and free of misinterpretation in practice?', 'AZ', 'In practice, the feedback is only slightly accurate due to network interference, leading to misinterpretation of server status.', 'hardware'),
(53, 'To what extent were you expecting this hardware to be central to meeting organizational requirements (e.g., critical for operations, data management) based on the organizational plan?', 'BA', 'The organizational plan expected the server to be essential for managing critical business data across departments.', 'hardware'),
(54, 'To what extent is this hardware actually central to meeting organizational requirements (e.g., critical for operations, data management) in practice?', 'BB', 'In practice, the server is only moderately central due to reliance on cloud alternatives, reducing its critical role.', 'hardware');

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
(1, 1, 'Daily', 1),
(2, 1, 'Weekly', 2),
(3, 1, 'Biweekly', 3),
(4, 1, 'Monthly', 4),
(5, 1, 'Less than monthly', 5),
(6, 2, 'Daily', 1),
(7, 2, 'Weekly', 2),
(8, 2, 'Biweekly', 3),
(9, 2, 'Monthly', 4),
(10, 2, 'Less than monthly', 5),
(11, 3, 'Very easy', 1),
(12, 3, 'Easy', 2),
(13, 3, 'Neutral', 3),
(14, 3, 'Difficult', 4),
(15, 3, 'Very difficult', 5),
(16, 4, 'Very easy', 1),
(17, 4, 'Easy', 2),
(18, 4, 'Neutral', 3),
(19, 4, 'Difficult', 4),
(20, 4, 'Very difficult', 5),
(21, 5, 'Not at all', 1),
(22, 5, 'To a small extent', 2),
(23, 5, 'To a moderate extent', 3),
(24, 5, 'To a large extent', 4),
(25, 5, 'Completely', 5),
(26, 6, 'Not at all', 1),
(27, 6, 'To a small extent', 2),
(28, 6, 'To a moderate extent', 3),
(29, 6, 'To a large extent', 4),
(30, 6, 'Completely', 5),
(31, 7, 'Not important', 1),
(32, 7, 'Slightly important', 2),
(33, 7, 'Neutral', 3),
(34, 7, 'Important', 4),
(35, 7, 'Very important', 5),
(36, 8, 'Not trustworthy', 1),
(37, 8, 'Slightly trustworthy', 2),
(38, 8, 'Neutral', 3),
(39, 8, 'Trustworthy', 4),
(40, 8, 'Very trustworthy', 5),
(41, 9, 'Never', 1),
(42, 9, 'Rarely', 2),
(43, 9, 'Occasionally', 3),
(44, 9, 'Frequently', 4),
(45, 9, 'Very frequently', 5),
(46, 10, 'Never', 1),
(47, 10, 'Rarely', 2),
(48, 10, 'Occasionally', 3),
(49, 10, 'Frequently', 4),
(50, 10, 'Very frequently', 5),
(51, 11, 'Daily', 1),
(52, 11, 'Weekly', 2),
(53, 11, 'Biweekly', 3),
(54, 11, 'Monthly', 4),
(55, 11, 'Less than monthly', 5),
(56, 12, 'Daily', 1),
(57, 12, 'Weekly', 2),
(58, 12, 'Biweekly', 3),
(59, 12, 'Monthly', 4),
(60, 12, 'Less than monthly', 5),
(61, 13, 'Definitely no', 1),
(62, 13, 'Probably no', 2),
(63, 13, 'Not sure', 3),
(64, 13, 'Probably yes', 4),
(65, 13, 'Definitely yes', 5),
(66, 14, 'Definitely no', 1),
(67, 14, 'Probably no', 2),
(68, 14, 'Not sure', 3),
(69, 14, 'Probably yes', 4),
(70, 14, 'Definitely yes', 5),
(71, 15, 'Definitely no', 1),
(72, 15, 'Probably no', 2),
(73, 15, 'Not sure', 3),
(74, 15, 'Probably yes', 4),
(75, 15, 'Definitely yes', 5),
(76, 16, 'Definitely no', 1),
(77, 16, 'Probably no', 2),
(78, 16, 'Not sure', 3),
(79, 16, 'Probably yes', 4),
(80, 16, 'Definitely yes', 5),
(81, 17, 'Not important', 1),
(82, 17, 'Slightly important', 2),
(83, 17, 'Neutral', 3),
(84, 17, 'Important', 4),
(85, 17, 'Very important', 5),
(86, 18, 'Not at all', 1),
(87, 18, 'Slightly', 2),
(88, 18, 'Neutral', 3),
(89, 18, 'Mostly', 4),
(90, 18, 'Completely', 5),
(91, 19, 'Not required', 1),
(92, 19, 'Slightly required', 2),
(93, 19, 'Moderately required', 3),
(94, 19, 'Highly required', 4),
(95, 19, 'Essential', 5),
(96, 20, 'Not accessible', 1),
(97, 20, 'Slightly accessible', 2),
(98, 20, 'Moderately accessible', 3),
(99, 20, 'Highly accessible', 4),
(100, 20, 'Fully accessible', 5),
(101, 21, 'Not at all', 1),
(102, 21, 'To a small extent', 2),
(103, 21, 'To a moderate extent', 3),
(104, 21, 'To a large extent', 4),
(105, 21, 'Completely', 5),
(106, 22, 'Not at all', 1),
(107, 22, 'To a small extent', 2),
(108, 22, 'To a moderate extent', 3),
(109, 22, 'To a large extent', 4),
(110, 22, 'Completely', 5),
(111, 23, 'Not important', 1),
(112, 23, 'Slightly important', 2),
(113, 23, 'Neutral', 3),
(114, 23, 'Important', 4),
(115, 23, 'Very important', 5),
(116, 24, 'Not accurate', 1),
(117, 24, 'Slightly accurate', 2),
(118, 24, 'Neutral', 3),
(119, 24, 'Accurate', 4),
(120, 24, 'Very accurate', 5),
(121, 25, 'Never', 1),
(122, 25, 'Rarely', 2),
(123, 25, 'Occasionally', 3),
(124, 25, 'Frequently', 4),
(125, 25, 'Very frequently', 5),
(126, 26, 'Never', 1),
(127, 26, 'Rarely', 2),
(128, 26, 'Occasionally', 3),
(129, 26, 'Frequently', 4),
(130, 26, 'Very frequently', 5),
(131, 27, 'Definitely no', 1),
(132, 27, 'Probably no', 2),
(133, 27, 'Not sure', 3),
(134, 27, 'Probably yes', 4),
(135, 27, 'Definitely yes', 5),
(136, 28, 'Definitely no', 1),
(137, 28, 'Probably no', 2),
(138, 28, 'Not sure', 3),
(139, 28, 'Probably yes', 4),
(140, 28, 'Definitely yes', 5),
(141, 29, 'Not at all', 1),
(142, 29, 'Slightly', 2),
(143, 29, 'Moderately', 3),
(144, 29, 'Well', 4),
(145, 29, 'Very well', 5),
(146, 30, 'Not at all', 1),
(147, 30, 'Slightly', 2),
(148, 30, 'Moderately', 3),
(149, 30, 'Well', 4),
(150, 30, 'Very well', 5),
(151, 31, 'Not at all', 1),
(152, 31, 'To a small extent', 2),
(153, 31, 'To a moderate extent', 3),
(154, 31, 'To a large extent', 4),
(155, 31, 'Completely', 5),
(156, 32, 'Not at all', 1),
(157, 32, 'To a small extent', 2),
(158, 32, 'To a moderate extent', 3),
(159, 32, 'To a large extent', 4),
(160, 32, 'Completely', 5),
(161, 33, 'Never', 1),
(162, 33, 'Rarely', 2),
(163, 33, 'Occasionally', 3),
(164, 33, 'Frequently', 4),
(165, 33, 'Very frequently', 5),
(166, 34, 'Never', 1),
(167, 34, 'Rarely', 2),
(168, 34, 'Occasionally', 3),
(169, 34, 'Frequently', 4),
(170, 34, 'Very frequently', 5),
(171, 35, 'Not important', 1),
(172, 35, 'Slightly important', 2),
(173, 35, 'Neutral', 3),
(174, 35, 'Important', 4),
(175, 35, 'Very important', 5),
(176, 36, 'Not at all', 1),
(177, 36, 'Slightly', 2),
(178, 36, 'Neutral', 3),
(179, 36, 'Mostly', 4),
(180, 36, 'Completely', 5),
(181, 37, 'Not required', 1),
(182, 37, 'Slightly required', 2),
(183, 37, 'Moderately required', 3),
(184, 37, 'Highly required', 4),
(185, 37, 'Essential', 5),
(186, 38, 'Not available', 1),
(187, 38, 'Slightly available', 2),
(188, 38, 'Moderately available', 3),
(189, 38, 'Highly available', 4),
(190, 38, 'Fully available', 5),
(191, 39, 'Not at all', 1),
(192, 39, 'To a small extent', 2),
(193, 39, 'To a moderate extent', 3),
(194, 39, 'To a large extent', 4),
(195, 39, 'Completely', 5),
(196, 40, 'Not at all', 1),
(197, 40, 'To a small extent', 2),
(198, 40, 'To a moderate extent', 3),
(199, 40, 'To a large extent', 4),
(200, 40, 'Completely', 5),
(201, 41, 'Not important', 1),
(202, 41, 'Slightly important', 2),
(203, 41, 'Neutral', 3),
(204, 41, 'Important', 4),
(205, 41, 'Very important', 5),
(206, 42, 'Not reliable', 1),
(207, 42, 'Slightly reliable', 2),
(208, 42, 'Neutral', 3),
(209, 42, 'Reliable', 4),
(210, 42, 'Very reliable', 5),
(211, 43, 'Never', 1),
(212, 43, 'Rarely', 2),
(213, 43, 'Occasionally', 3),
(214, 43, 'Frequently', 4),
(215, 43, 'Very frequently', 5),
(216, 44, 'Never', 1),
(217, 44, 'Rarely', 2),
(218, 44, 'Occasionally', 3),
(219, 44, 'Frequently', 4),
(220, 44, 'Very frequently', 5),
(221, 45, 'Definitely no', 1),
(222, 45, 'Probably no', 2),
(223, 45, 'Not sure', 3),
(224, 45, 'Probably yes', 4),
(225, 45, 'Definitely yes', 5),
(226, 46, 'Definitely no', 1),
(227, 46, 'Probably no', 2),
(228, 46, 'Not sure', 3),
(229, 46, 'Probably yes', 4),
(230, 46, 'Definitely yes', 5),
(231, 47, 'Not at all', 1),
(232, 47, 'Slightly', 2),
(233, 47, 'Moderately', 3),
(234, 47, 'Well', 4),
(235, 47, 'Very well', 5),
(236, 48, 'Not at all', 1),
(237, 48, 'Slightly', 2),
(238, 48, 'Moderately', 3),
(239, 48, 'Well', 4),
(240, 48, 'Very well', 5),
(241, 49, 'Not at all', 1),
(242, 49, 'To a small extent', 2),
(243, 49, 'To a moderate extent', 3),
(244, 49, 'To a large extent', 4),
(245, 49, 'Completely', 5),
(246, 50, 'Not at all', 1),
(247, 50, 'To a small extent', 2),
(248, 50, 'To a moderate extent', 3),
(249, 50, 'To a large extent', 4),
(250, 50, 'Completely', 5),
(251, 51, 'Not important', 1),
(252, 51, 'Slightly important', 2),
(253, 51, 'Neutral', 3),
(254, 51, 'Important', 4),
(255, 51, 'Very important', 5),
(256, 52, 'Not at all', 1),
(257, 52, 'Slightly', 2),
(258, 52, 'Neutral', 3),
(259, 52, 'Mostly', 4),
(260, 52, 'Completely', 5),
(261, 53, 'Not at all', 1),
(262, 53, 'Slightly central', 2),
(263, 53, 'Moderately central', 3),
(264, 53, 'Highly central', 4),
(265, 53, 'Essential', 5),
(266, 54, 'Not at all', 1),
(267, 54, 'Slightly central', 2),
(268, 54, 'Moderately central', 3),
(269, 54, 'Highly central', 4),
(270, 54, 'Essential', 5);

-- --------------------------------------------------------

--
-- Table structure for table `responses`
--

CREATE TABLE `responses` (
  `response_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `subject_type` enum('person','software','hardware','analog') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `responses`
--

INSERT INTO `responses` (`response_id`, `user_id`, `question_id`, `option_id`, `subject_id`, `subject_type`) VALUES
(1, 7, 27, 131, 9, 'hardware'),
(2, 7, 28, 138, 9, 'hardware'),
(3, 7, 29, 143, 9, 'hardware'),
(4, 7, 30, 148, 9, 'hardware'),
(5, 7, 31, 151, 9, 'hardware'),
(6, 7, 32, 159, 9, 'hardware'),
(7, 7, 33, 164, 9, 'hardware'),
(8, 7, 34, 170, 9, 'hardware'),
(9, 7, 35, 175, 9, 'hardware'),
(10, 7, 36, 178, 9, 'hardware'),
(11, 7, 37, 184, 9, 'hardware'),
(12, 7, 38, 188, 9, 'hardware'),
(13, 7, 39, 193, 9, 'hardware'),
(14, 7, 40, 197, 9, 'hardware'),
(15, 7, 41, 203, 9, 'hardware'),
(16, 7, 42, 208, 9, 'hardware'),
(17, 7, 43, 212, 9, 'hardware'),
(18, 3, 27, 131, 10, 'hardware'),
(19, 3, 28, 138, 10, 'hardware'),
(20, 3, 29, 143, 10, 'hardware'),
(21, 3, 30, 148, 10, 'hardware'),
(22, 3, 31, 152, 10, 'hardware'),
(23, 3, 32, 157, 10, 'hardware'),
(24, 3, 33, 163, 10, 'hardware'),
(25, 3, 34, 169, 10, 'hardware'),
(26, 3, 35, 173, 10, 'hardware'),
(27, 3, 36, 178, 10, 'hardware'),
(28, 3, 37, 184, 10, 'hardware'),
(29, 3, 38, 188, 10, 'hardware'),
(30, 3, 39, 192, 10, 'hardware'),
(31, 3, 40, 199, 10, 'hardware'),
(32, 3, 41, 203, 10, 'hardware'),
(33, 3, 42, 208, 10, 'hardware'),
(34, 3, 43, 213, 10, 'hardware'),
(35, 6, 1, 1, 3, 'person'),
(36, 6, 2, 6, 3, 'person'),
(37, 6, 3, 13, 3, 'person'),
(38, 6, 4, 16, 3, 'person'),
(39, 6, 5, 22, 3, 'person'),
(40, 6, 6, 28, 3, 'person'),
(41, 6, 7, 33, 3, 'person'),
(42, 6, 8, 38, 3, 'person'),
(43, 6, 9, 43, 3, 'person'),
(44, 6, 10, 49, 3, 'person'),
(45, 6, 11, 54, 3, 'person'),
(46, 6, 12, 59, 3, 'person'),
(47, 6, 13, 63, 3, 'person'),
(48, 6, 14, 68, 3, 'person'),
(49, 6, 15, 74, 3, 'person'),
(50, 6, 1, 1, 13, 'person'),
(51, 6, 2, 7, 13, 'person'),
(52, 6, 3, 11, 13, 'person'),
(53, 6, 4, 19, 13, 'person'),
(54, 6, 5, 21, 13, 'person'),
(55, 6, 6, 27, 13, 'person'),
(56, 6, 7, 32, 13, 'person'),
(57, 6, 8, 40, 13, 'person'),
(58, 6, 9, 44, 13, 'person'),
(59, 6, 10, 48, 13, 'person'),
(60, 6, 11, 53, 13, 'person'),
(61, 6, 12, 57, 13, 'person'),
(62, 6, 13, 63, 13, 'person'),
(63, 6, 14, 68, 13, 'person'),
(64, 6, 15, 75, 13, 'person');

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
(56, '2axis CNC', 'hardware'),
(33, '3D Scanner', 'hardware'),
(55, 'Ace designer jober C3', 'hardware'),
(40, 'Adobe Illustrator', 'software'),
(41, 'Adobe Photoshop', 'software'),
(66, 'Anaconda Navigator', 'software'),
(70, 'Arduino UNO', 'hardware'),
(51, 'Auto UTM Machine', 'hardware'),
(18, 'AutoCAD', 'software'),
(43, 'Bambu Lab Slicer', 'software'),
(4, 'Canva', 'software'),
(13, 'ChatGPT', 'software'),
(19, 'CircuitIO', 'software'),
(10, 'CO2 laser cutting', 'hardware'),
(36, 'Cobot', 'hardware'),
(12, 'Copilot', 'software'),
(64, 'diligent tools', 'hardware'),
(54, 'Einscan', 'hardware'),
(57, 'Emco 4+1axis sinumerik', 'hardware'),
(39, 'FDM 3D Printer', 'hardware'),
(5, 'Figma', 'software'),
(63, 'Foam cutter', 'hardware'),
(61, 'FRP casting', 'hardware'),
(17, 'Fusion 360', 'software'),
(42, 'Grasshopper(Rhino)', 'software'),
(35, 'Haptic device', 'hardware'),
(22, 'JIRA', 'software'),
(53, 'LinkedIn', 'software'),
(65, 'Linux', 'software'),
(30, 'Master CAM', 'software'),
(7, 'Matlab', 'software'),
(24, 'Mentimeter', 'software'),
(38, 'metalAM', 'hardware'),
(62, 'Mini lathe', 'hardware'),
(52, 'Miro', 'software'),
(49, 'MJP 3D Printer', 'hardware'),
(14, 'MS Office tools - Word, Excel, PPT', 'software'),
(48, 'MSLA 3D Printer', 'hardware'),
(20, 'NX', 'software'),
(32, 'NX CAD', 'software'),
(16, 'Onedrive', 'software'),
(68, 'Oscilloscope', 'hardware'),
(2, 'Outlook', 'software'),
(29, 'Pdf Reader', 'software'),
(28, 'Photoshop', 'software'),
(50, 'Portable CMM', 'hardware'),
(15, 'Power Automate', 'software'),
(44, 'Prusa Slicer', 'software'),
(11, 'Python', 'software'),
(67, 'Raspberry Pi', 'hardware'),
(69, 'RFID Tag reader', 'hardware'),
(27, 'Rhino', 'software'),
(3, 'Sharepoint', 'software'),
(9, 'Sheet Metal Laser Cutting Machine', 'hardware'),
(26, 'SketchUp', 'software'),
(25, 'Slicer or 3D print', 'software'),
(45, 'Snapmaker Slicer', 'software'),
(21, 'SolidWorks', 'software'),
(31, 'Teams Meeting', 'software'),
(6, 'TinkerCad', 'software'),
(23, 'Trello', 'software'),
(37, 'Ultimaker - slicing', 'software'),
(60, 'Vacuum casting', 'hardware'),
(59, 'Vacuum forming', 'hardware'),
(58, 'VMC fanuk', 'hardware'),
(1, 'WhatsApp', 'software'),
(8, 'Wire EDM', 'hardware'),
(34, 'Zoho', 'software');

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
(3, 'Ananda', 'Krishnan', 'Technical support Engineer', 'ananda.krishnan@fsid-iisc.in\r\n', 'COE', '0000-00-00 00:00:00'),
(4, 'Gayathri ', 'M', 'Accountant', 'gayathri@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(5, 'Hunny ', 'Bidlan', 'In-house trainer', 'hsbidlan@gmail.com', 'COE', '0000-00-00 00:00:00'),
(6, 'K Joshil ', 'Raj', 'CEO', 'joshil@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(7, 'Laveen ', 'Kumar', 'In-house trainer', 'Laveen@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(8, 'Nishath', ' Salma', 'Admin', 'nsalma@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(9, 'Puneeth ', 'S', 'In-house trainer', 'puneeth@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(10, 'Siva ', 'S', 'Technical support staff', 'Siva@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(11, 'Subramanian', '', 'Technical support', 'subramanian@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(12, 'TSVV ', 'Murali', 'In-house trainer', 'venkata@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(13, 'Chytra ', 'K', 'HR', 'chytra.k@fsid-iisc.in', 'COE', '0000-00-00 00:00:00'),
(14, 'Samarth \r\n', 'Goel', 'Intern', 'samarth.goel@fsid-iisc.ac.in\r\n', 'COE', '0000-00-00 00:00:00'),
(15, 'Sakshitha', 'V', 'Marketing ', 'sakshitha.v@fsid-iisc.in', 'COE', '2025-04-15 15:35:42');

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
(1, 15, 6, '2025-04-15 16:07:26'),
(2, 15, 13, '2025-04-15 16:07:26'),
(3, 15, 14, '2025-04-15 16:07:26'),
(4, 7, 1, '2025-04-15 16:08:11'),
(5, 7, 2, '2025-04-15 16:08:11'),
(6, 7, 4, '2025-04-15 16:08:11'),
(7, 7, 5, '2025-04-15 16:08:11'),
(8, 7, 6, '2025-04-15 16:08:11'),
(9, 7, 8, '2025-04-15 16:08:11'),
(10, 7, 9, '2025-04-15 16:08:11'),
(11, 7, 10, '2025-04-15 16:08:11'),
(12, 7, 11, '2025-04-15 16:08:11'),
(13, 7, 12, '2025-04-15 16:08:11'),
(14, 7, 13, '2025-04-15 16:08:11'),
(15, 7, 14, '2025-04-15 16:08:11'),
(16, 7, 3, '2025-04-15 16:08:11'),
(17, 10, 7, '2025-04-15 16:08:44'),
(18, 10, 11, '2025-04-15 16:08:44'),
(19, 10, 6, '2025-04-15 16:08:44'),
(20, 10, 13, '2025-04-15 16:08:44'),
(21, 10, 4, '2025-04-15 16:08:44'),
(22, 10, 5, '2025-04-15 16:08:44'),
(23, 9, 6, '2025-04-15 16:09:04'),
(24, 9, 4, '2025-04-15 16:09:04'),
(25, 9, 8, '2025-04-15 16:09:04'),
(26, 9, 7, '2025-04-15 16:09:04'),
(27, 9, 12, '2025-04-15 16:09:04'),
(28, 9, 1, '2025-04-15 16:09:04'),
(29, 4, 1, '2025-04-15 16:09:29'),
(30, 4, 2, '2025-04-15 16:09:29'),
(31, 4, 5, '2025-04-15 16:09:29'),
(32, 4, 6, '2025-04-15 16:09:29'),
(33, 4, 7, '2025-04-15 16:09:29'),
(34, 4, 8, '2025-04-15 16:09:29'),
(35, 4, 9, '2025-04-15 16:09:29'),
(36, 4, 10, '2025-04-15 16:09:29'),
(37, 4, 11, '2025-04-15 16:09:29'),
(38, 4, 12, '2025-04-15 16:09:29'),
(39, 4, 13, '2025-04-15 16:09:29'),
(40, 4, 14, '2025-04-15 16:09:29'),
(41, 4, 3, '2025-04-15 16:09:29'),
(42, 4, 15, '2025-04-15 16:09:29'),
(43, 14, 6, '2025-04-15 16:13:04'),
(44, 14, 1, '2025-04-15 16:13:04'),
(45, 13, 9, '2025-04-15 16:16:03'),
(46, 13, 15, '2025-04-15 16:16:03'),
(47, 13, 4, '2025-04-15 16:16:03'),
(48, 13, 14, '2025-04-15 16:16:03'),
(49, 13, 1, '2025-04-15 16:16:03'),
(50, 13, 6, '2025-04-15 16:16:03'),
(51, 5, 1, '2025-04-16 07:37:35'),
(52, 5, 2, '2025-04-16 07:37:35'),
(53, 5, 4, '2025-04-16 07:37:35'),
(54, 5, 6, '2025-04-16 07:37:35'),
(55, 5, 7, '2025-04-16 07:37:35'),
(56, 5, 8, '2025-04-16 07:37:35'),
(57, 5, 9, '2025-04-16 07:37:35'),
(58, 5, 10, '2025-04-16 07:37:35'),
(59, 5, 11, '2025-04-16 07:37:35'),
(60, 5, 12, '2025-04-16 07:37:35'),
(61, 5, 13, '2025-04-16 07:37:35'),
(62, 5, 14, '2025-04-16 07:37:35'),
(63, 5, 3, '2025-04-16 07:37:35'),
(64, 5, 15, '2025-04-16 07:39:33'),
(65, 2, 6, '2025-04-16 07:42:01'),
(66, 2, 5, '2025-04-16 07:42:01'),
(67, 2, 7, '2025-04-16 07:42:01'),
(68, 12, 6, '2025-04-16 09:31:28'),
(69, 12, 14, '2025-04-16 09:31:28'),
(70, 12, 11, '2025-04-16 09:31:28'),
(71, 12, 10, '2025-04-16 09:31:28'),
(72, 12, 9, '2025-04-16 09:31:28'),
(73, 12, 7, '2025-04-16 09:31:28'),
(74, 12, 1, '2025-04-16 09:31:28'),
(75, 12, 8, '2025-04-16 09:31:28'),
(76, 11, 6, '2025-04-16 09:48:30'),
(77, 11, 9, '2025-04-16 09:48:30'),
(78, 11, 12, '2025-04-16 09:48:30'),
(79, 11, 5, '2025-04-16 09:48:30'),
(80, 11, 7, '2025-04-16 09:48:30'),
(81, 11, 1, '2025-04-16 09:48:30'),
(82, 11, 10, '2025-04-16 09:48:30'),
(83, 11, 2, '2025-04-16 09:48:30'),
(84, 11, 4, '2025-04-16 09:48:30'),
(85, 11, 8, '2025-04-16 09:48:30'),
(86, 11, 13, '2025-04-16 09:48:30'),
(87, 11, 3, '2025-04-16 09:48:30'),
(88, 1, 13, '2025-04-16 10:09:40'),
(89, 1, 14, '2025-04-16 10:09:40'),
(90, 1, 15, '2025-04-16 10:09:40'),
(91, 1, 4, '2025-04-16 10:09:40'),
(92, 1, 6, '2025-04-16 10:09:40'),
(93, 1, 3, '2025-04-16 10:09:40'),
(94, 1, 12, '2025-04-16 10:09:40'),
(95, 1, 9, '2025-04-16 10:09:40'),
(96, 1, 7, '2025-04-16 10:09:40'),
(97, 1, 5, '2025-04-16 10:09:40'),
(98, 3, 6, '2025-04-16 10:13:40'),
(99, 3, 1, '2025-04-16 10:13:40'),
(100, 3, 15, '2025-04-16 10:13:40'),
(101, 3, 13, '2025-04-16 10:13:40'),
(102, 3, 7, '2025-04-16 10:13:40'),
(103, 3, 12, '2025-04-16 10:13:40'),
(104, 3, 8, '2025-04-16 10:13:40'),
(105, 8, 1, '2025-04-18 04:29:03'),
(106, 8, 2, '2025-04-18 04:29:03'),
(107, 8, 4, '2025-04-18 04:29:03'),
(108, 8, 5, '2025-04-18 04:29:03'),
(109, 8, 6, '2025-04-18 04:29:03'),
(110, 8, 9, '2025-04-18 04:29:03'),
(111, 8, 10, '2025-04-18 04:29:03'),
(112, 8, 11, '2025-04-18 04:29:03'),
(113, 8, 12, '2025-04-18 04:29:03'),
(114, 8, 13, '2025-04-18 04:29:03'),
(115, 8, 14, '2025-04-18 04:29:03'),
(116, 8, 3, '2025-04-18 04:29:03');

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
(1, 15, 14, '2025-04-15 16:07:44'),
(2, 15, 13, '2025-04-15 16:07:44'),
(3, 15, 1, '2025-04-15 16:07:44'),
(4, 15, 31, '2025-04-15 16:07:44'),
(5, 7, 32, '2025-04-15 16:08:29'),
(6, 7, 25, '2025-04-15 16:08:29'),
(7, 7, 14, '2025-04-15 16:08:29'),
(8, 7, 34, '2025-04-15 16:08:29'),
(9, 7, 33, '2025-04-15 16:08:29'),
(10, 7, 35, '2025-04-15 16:08:29'),
(11, 7, 10, '2025-04-15 16:08:29'),
(12, 7, 36, '2025-04-15 16:08:29'),
(13, 7, 9, '2025-04-15 16:08:29'),
(14, 10, 21, '2025-04-15 16:08:53'),
(15, 10, 30, '2025-04-15 16:08:53'),
(16, 10, 18, '2025-04-15 16:08:53'),
(17, 10, 2, '2025-04-15 16:08:53'),
(18, 10, 37, '2025-04-15 16:08:53'),
(19, 10, 1, '2025-04-15 16:08:53'),
(20, 10, 4, '2025-04-15 16:08:53'),
(21, 10, 13, '2025-04-15 16:08:53'),
(22, 10, 14, '2025-04-15 16:08:53'),
(23, 10, 8, '2025-04-15 16:08:53'),
(24, 10, 10, '2025-04-15 16:08:53'),
(25, 10, 9, '2025-04-15 16:08:53'),
(26, 10, 38, '2025-04-15 16:08:53'),
(27, 10, 39, '2025-04-15 16:08:53'),
(28, 9, 14, '2025-04-15 16:09:13'),
(29, 9, 13, '2025-04-15 16:09:13'),
(30, 9, 1, '2025-04-15 16:09:13'),
(31, 4, 14, '2025-04-15 16:09:43'),
(32, 4, 34, '2025-04-15 16:09:43'),
(33, 4, 1, '2025-04-15 16:09:43'),
(34, 4, 13, '2025-04-15 16:09:43'),
(35, 14, 40, '2025-04-15 16:13:14'),
(36, 14, 5, '2025-04-15 16:13:14'),
(37, 14, 4, '2025-04-15 16:13:14'),
(38, 14, 41, '2025-04-15 16:13:14'),
(39, 13, 14, '2025-04-15 16:16:03'),
(40, 13, 13, '2025-04-15 16:16:03'),
(41, 13, 1, '2025-04-15 16:16:03'),
(42, 5, 18, '2025-04-16 07:37:45'),
(43, 5, 14, '2025-04-16 07:37:45'),
(44, 5, 28, '2025-04-16 07:37:45'),
(45, 5, 42, '2025-04-16 07:37:45'),
(46, 5, 13, '2025-04-16 07:37:45'),
(73, 2, 14, '2025-04-16 07:59:55'),
(83, 2, 15, '2025-04-16 08:01:19'),
(84, 2, 43, '2025-04-16 08:02:18'),
(86, 2, 44, '2025-04-16 08:04:40'),
(99, 2, 45, '2025-04-16 08:06:11'),
(100, 2, 26, '2025-04-16 08:06:11'),
(101, 2, 18, '2025-04-16 08:06:11'),
(102, 2, 28, '2025-04-16 08:06:11'),
(103, 2, 42, '2025-04-16 08:06:11'),
(104, 2, 13, '2025-04-16 08:06:11'),
(107, 2, 39, '2025-04-16 08:08:23'),
(108, 2, 48, '2025-04-16 08:09:11'),
(109, 2, 49, '2025-04-16 08:10:21'),
(110, 2, 10, '2025-04-16 08:10:21'),
(111, 2, 50, '2025-04-16 08:10:21'),
(113, 2, 51, '2025-04-16 08:12:57'),
(114, 12, 4, '2025-04-16 09:32:52'),
(115, 12, 52, '2025-04-16 09:32:52'),
(116, 12, 14, '2025-04-16 09:32:52'),
(117, 12, 32, '2025-04-16 09:32:52'),
(118, 12, 13, '2025-04-16 09:32:52'),
(119, 11, 14, '2025-04-16 09:48:01'),
(120, 11, 13, '2025-04-16 09:48:01'),
(121, 11, 4, '2025-04-16 09:48:01'),
(122, 11, 1, '2025-04-16 09:48:01'),
(123, 11, 53, '2025-04-16 09:48:01'),
(124, 11, 34, '2025-04-16 09:48:01'),
(125, 11, 32, '2025-04-16 09:48:01'),
(126, 11, 45, '2025-04-16 09:48:01'),
(127, 11, 54, '2025-04-16 09:48:01'),
(128, 11, 26, '2025-04-16 09:48:01'),
(129, 11, 55, '2025-04-16 09:48:01'),
(130, 11, 56, '2025-04-16 09:48:01'),
(131, 11, 57, '2025-04-16 09:48:01'),
(132, 11, 58, '2025-04-16 09:48:01'),
(133, 11, 10, '2025-04-16 09:48:01'),
(134, 11, 59, '2025-04-16 09:48:01'),
(135, 11, 60, '2025-04-16 09:48:01'),
(136, 11, 61, '2025-04-16 09:48:01'),
(137, 11, 62, '2025-04-16 09:48:01'),
(138, 11, 63, '2025-04-16 09:48:01'),
(139, 11, 33, '2025-04-16 09:48:01'),
(140, 1, 14, '2025-04-16 10:09:52'),
(141, 1, 13, '2025-04-16 10:09:52'),
(142, 1, 1, '2025-04-16 10:09:52'),
(143, 1, 7, '2025-04-16 10:09:52'),
(144, 1, 4, '2025-04-16 10:09:52'),
(145, 1, 64, '2025-04-16 10:09:52'),
(146, 3, 65, '2025-04-16 10:15:14'),
(147, 3, 14, '2025-04-16 10:15:14'),
(148, 3, 1, '2025-04-16 10:15:14'),
(149, 3, 2, '2025-04-16 10:15:14'),
(150, 3, 66, '2025-04-16 10:15:14'),
(151, 3, 11, '2025-04-16 10:15:14'),
(152, 3, 67, '2025-04-16 10:15:14'),
(153, 3, 68, '2025-04-16 10:15:14'),
(154, 3, 69, '2025-04-16 10:15:14'),
(155, 3, 70, '2025-04-16 10:15:14'),
(156, 8, 14, '2025-04-18 04:30:54'),
(157, 8, 13, '2025-04-18 04:30:54'),
(158, 8, 1, '2025-04-18 04:30:54'),
(159, 8, 2, '2025-04-18 04:30:54');

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
  ADD UNIQUE KEY `user_id` (`user_id`,`question_id`,`subject_id`,`subject_type`);

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
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `question_options`
--
ALTER TABLE `question_options`
  MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=271;

--
-- AUTO_INCREMENT for table `responses`
--
ALTER TABLE `responses`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `tools`
--
ALTER TABLE `tools`
  MODIFY `tool_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_people`
--
ALTER TABLE `user_people`
  MODIFY `user_people_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `user_tools`
--
ALTER TABLE `user_tools`
  MODIFY `user_tool_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `question_options`
--
ALTER TABLE `question_options`
  ADD CONSTRAINT `question_options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`);

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
