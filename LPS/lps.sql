-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2025 at 09:06 AM
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
-- Database: `lps`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `feasibility` enum('Feasible','Not Feasible') NOT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `task_id`, `user_id`, `feasibility`, `comments`) VALUES
(1, 1, 11, 'Feasible', 'All equipment ready'),
(2, 1, 12, 'Not Feasible', 'Need more crew'),
(3, 2, 11, 'Feasible', 'On track'),
(4, 2, 12, 'Feasible', 'Good to go'),
(5, 3, 13, 'Not Feasible', 'Material delay'),
(6, 3, 14, 'Feasible', 'Ready'),
(7, 4, 13, 'Feasible', 'No issues'),
(8, 4, 14, 'Feasible', 'All set'),
(9, 5, 15, 'Feasible', 'Team prepared'),
(10, 5, 16, 'Not Feasible', 'Weather concern'),
(11, 6, 15, 'Feasible', 'On schedule'),
(12, 6, 16, 'Feasible', 'Good'),
(13, 7, 17, 'Not Feasible', 'Pipe shortage'),
(14, 7, 18, 'Feasible', 'Ready'),
(15, 8, 17, 'Feasible', 'No problems'),
(16, 8, 18, 'Feasible', 'All clear'),
(17, 9, 19, 'Feasible', 'Team ready'),
(18, 9, 20, 'Feasible', 'On track'),
(19, 10, 19, 'Not Feasible', 'Need sealant'),
(20, 10, 20, 'Feasible', 'Good to go'),
(21, 1, 11, 'Not Feasible', 'too much work');

-- --------------------------------------------------------

--
-- Table structure for table `milestones`
--

CREATE TABLE `milestones` (
  `milestone_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `due_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `milestones`
--

INSERT INTO `milestones` (`milestone_id`, `name`, `due_date`, `created_by`) VALUES
(1, 'Foundation Complete', '2025-06-30', 1),
(2, 'Structure Complete', '2025-09-30', 1),
(3, 'Electrical Install', '2025-12-31', 2),
(4, 'Plumbing Install', '2025-11-30', 2),
(5, 'Roofing Complete', '2026-03-31', 3),
(6, 'Interior Finish', '2026-06-30', 3),
(7, 'Exterior Finish', '2026-05-31', 4),
(8, 'Landscaping', '2026-07-31', 4),
(9, 'HVAC Install', '2025-12-15', 5),
(10, 'Windows Install', '2026-01-15', 5),
(11, 'Foundation Phase 2', '2025-07-15', 6),
(12, 'Structure Phase 2', '2025-10-15', 6),
(13, 'Electrical Phase 2', '2026-01-31', 7),
(14, 'Plumbing Phase 2', '2025-12-15', 7),
(15, 'Roofing Phase 2', '2026-04-30', 8),
(16, 'Interior Phase 2', '2026-07-31', 8),
(17, 'Exterior Phase 2', '2026-06-15', 9),
(18, 'Landscaping Phase 2', '2026-08-31', 9),
(19, 'HVAC Phase 2', '2026-01-15', 10),
(20, 'Windows Phase 2', '2026-02-28', 10);

-- --------------------------------------------------------

--
-- Table structure for table `revisions`
--

CREATE TABLE `revisions` (
  `revision_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `old_data` varchar(255) DEFAULT NULL,
  `new_data` varchar(255) DEFAULT NULL,
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `revisions`
--

INSERT INTO `revisions` (`revision_id`, `task_id`, `old_data`, `new_data`, `reason`) VALUES
(1, 1, '2025-04-14', '2025-04-15', 'Feedback: crew issue'),
(2, 2, '2025-04-20', '2025-04-21', 'Adjusted schedule'),
(3, 3, '2025-07-02', '2025-07-01', 'Material delay'),
(4, 4, '2025-07-12', '2025-07-11', 'Team request'),
(5, 5, '2025-10-02', '2025-10-01', 'Feedback: ready early'),
(6, 6, '2025-10-07', '2025-10-06', 'Schedule tweak'),
(7, 7, '2025-09-02', '2025-09-01', 'Pipe availability'),
(8, 8, '2025-09-07', '2025-09-06', 'Feedback: ready'),
(9, 9, '2025-12-02', '2025-12-01', 'Team ready'),
(10, 10, '2025-12-09', '2025-12-08', 'Sealant delay'),
(11, 11, '2026-01-02', '2026-01-01', 'Schedule change'),
(12, 12, '2026-01-12', '2026-01-11', 'Feedback: ready'),
(13, 13, '2026-02-02', '2026-02-01', 'Material issue'),
(14, 14, '2026-02-12', '2026-02-11', 'Team request'),
(15, 15, '2026-04-02', '2026-04-01', 'Feedback: ready'),
(16, 16, '2026-04-07', '2026-04-06', 'Schedule tweak'),
(17, 17, '2025-11-02', '2025-11-01', 'Unit availability'),
(18, 18, '2025-11-07', '2025-11-06', 'Feedback: ready'),
(19, 19, '2025-12-02', '2025-12-01', 'Team ready'),
(20, 20, '2025-12-07', '2025-12-06', 'Frame delay'),
(21, 1, '2025-04-15', '2025-04-15', 'not possible'),
(22, 11, '2026-01-01', '2026-01-01', 'need more time'),
(23, 21, '2025-04-15', '2025-04-15', 'asasa'),
(24, 1, '2025-04-15', '2025-04-15', 'asasa'),
(25, 1, '{\"start_date\":\"2025-04-15\",\"end_date\":\"2025-05-04\"}', '{\"start_date\":\"2025-04-15\",\"end_date\":\"2025-05-02\"}', 'asasa'),
(26, 1, '{\"start_date\":\"2025-04-15\",\"end_date\":\"2025-05-02\"}', '{\"start_date\":\"2025-04-15\",\"end_date\":\"2025-05-31\"}', 'asasa'),
(27, 1, '2025-04-15', '2025-04-15', 'getting it done quickly');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL,
  `milestone_id` int(11) DEFAULT NULL,
  `task_name` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `assigned_team` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`task_id`, `milestone_id`, `task_name`, `start_date`, `end_date`, `assigned_team`, `created_by`) VALUES
(1, 1, 'Excavate Site', '2025-04-15', '2025-05-02', 'TeamA', 1),
(2, 1, 'Pour Concrete', '2025-04-21', '2025-04-25', 'TeamA', 1),
(3, 2, 'Frame Walls', '2025-07-01', '2025-07-10', 'TeamB', 2),
(4, 2, 'Install Beams', '2025-07-11', '2025-07-15', 'TeamB', 2),
(5, 3, 'Run Wiring', '2025-10-01', '2025-10-05', 'TeamC', 3),
(6, 3, 'Install Panels', '2025-10-06', '2025-10-10', 'TeamC', 3),
(7, 4, 'Lay Pipes', '2025-09-01', '2025-09-05', 'TeamD', 4),
(8, 4, 'Test Plumbing', '2025-09-06', '2025-09-10', 'TeamD', 4),
(9, 5, 'Install Shingles', '2025-12-01', '2025-12-07', 'TeamA', 5),
(10, 5, 'Seal Roof', '2025-12-08', '2025-12-10', 'TeamA', 5),
(11, 6, 'Drywall Install', '2026-01-01', '2026-01-10', 'TeamB', 6),
(12, 6, 'Paint Walls', '2026-01-11', '2026-01-15', 'TeamB', 6),
(13, 7, 'Brick Exterior', '2026-02-01', '2026-02-10', 'TeamC', 7),
(14, 7, 'Siding Install', '2026-02-11', '2026-02-15', 'TeamC', 7),
(15, 8, 'Plant Trees', '2026-04-01', '2026-04-05', 'TeamD', 8),
(16, 8, 'Lay Sod', '2026-04-06', '2026-04-10', 'TeamD', 8),
(17, 9, 'Install Units', '2025-11-01', '2025-11-05', 'TeamA', 9),
(18, 9, 'Test HVAC', '2025-11-06', '2025-11-10', 'TeamA', 9),
(19, 10, 'Frame Windows', '2025-12-01', '2025-12-05', 'TeamB', 10),
(20, 10, 'Seal Windows', '2025-12-06', '2025-12-10', 'TeamB', 10),
(21, 5, 'Review Code', '2025-04-15', '2025-04-20', 'teamc', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Planner','SA_Team') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`) VALUES
(1, 'planner1', 'pass123', 'Planner'),
(2, 'planner2', 'pass123', 'Planner'),
(3, 'planner3', 'pass123', 'Planner'),
(4, 'planner4', 'pass123', 'Planner'),
(5, 'planner5', 'pass123', 'Planner'),
(6, 'planner6', 'pass123', 'Planner'),
(7, 'planner7', 'pass123', 'Planner'),
(8, 'planner8', 'pass123', 'Planner'),
(9, 'planner9', 'pass123', 'Planner'),
(10, 'planner10', 'pass123', 'Planner'),
(11, 'sa1', 'pass123', 'SA_Team'),
(12, 'sa2', 'pass123', 'SA_Team'),
(13, 'sa3', 'pass123', 'SA_Team'),
(14, 'sa4', 'pass123', 'SA_Team'),
(15, 'sa5', 'pass123', 'SA_Team'),
(16, 'sa6', 'pass123', 'SA_Team'),
(17, 'sa7', 'pass123', 'SA_Team'),
(18, 'sa8', 'pass123', 'SA_Team'),
(19, 'sa9', 'pass123', 'SA_Team'),
(20, 'sa10', 'pass123', 'SA_Team');

-- --------------------------------------------------------

--
-- Table structure for table `weekly_plans`
--

CREATE TABLE `weekly_plans` (
  `weekly_plan_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `week_start_date` date DEFAULT NULL,
  `commit_status` enum('Pending','Committed') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weekly_plans`
--

INSERT INTO `weekly_plans` (`weekly_plan_id`, `task_id`, `week_start_date`, `commit_status`) VALUES
(1, 1, '2025-04-14', 'Committed'),
(2, 2, '2025-04-21', 'Pending'),
(3, 3, '2025-07-01', 'Pending'),
(4, 4, '2025-07-08', 'Pending'),
(5, 5, '2025-10-01', 'Pending'),
(6, 6, '2025-10-08', 'Pending'),
(7, 7, '2025-09-01', 'Committed'),
(8, 8, '2025-09-08', 'Pending'),
(9, 9, '2025-12-01', 'Pending'),
(10, 10, '2025-12-08', 'Pending'),
(11, 11, '2026-01-01', 'Pending'),
(12, 12, '2026-01-08', 'Pending'),
(13, 13, '2026-02-01', 'Pending'),
(14, 14, '2026-02-08', 'Pending'),
(15, 15, '2026-04-01', 'Pending'),
(16, 16, '2026-04-08', 'Pending'),
(17, 17, '2025-11-01', 'Committed'),
(18, 18, '2025-11-08', 'Pending'),
(19, 19, '2025-12-01', 'Pending'),
(20, 20, '2025-12-08', 'Pending'),
(21, 21, '2025-04-14', 'Pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `milestones`
--
ALTER TABLE `milestones`
  ADD PRIMARY KEY (`milestone_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `revisions`
--
ALTER TABLE `revisions`
  ADD PRIMARY KEY (`revision_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `milestone_id` (`milestone_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `weekly_plans`
--
ALTER TABLE `weekly_plans`
  ADD PRIMARY KEY (`weekly_plan_id`),
  ADD KEY `task_id` (`task_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `milestones`
--
ALTER TABLE `milestones`
  MODIFY `milestone_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `revisions`
--
ALTER TABLE `revisions`
  MODIFY `revision_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `weekly_plans`
--
ALTER TABLE `weekly_plans`
  MODIFY `weekly_plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`),
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `milestones`
--
ALTER TABLE `milestones`
  ADD CONSTRAINT `milestones_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `revisions`
--
ALTER TABLE `revisions`
  ADD CONSTRAINT `revisions_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`milestone_id`) REFERENCES `milestones` (`milestone_id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `weekly_plans`
--
ALTER TABLE `weekly_plans`
  ADD CONSTRAINT `weekly_plans_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
