-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 13, 2025 at 12:27 PM
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
-- Database: `ligtastalk`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `account_id` int(11) NOT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `role` enum('User','Staff','Admin') NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`account_id`, `id_number`, `email`, `password`, `name`, `role`, `profile_pic`) VALUES
(2, '22117053', 'hatdog123@gmail.com', '$2y$10$HZJ7f4NXLwH7IFe3RDL5uum6RH/Zzq4R2vmIIH3IZ5QKMLx85QPXm', 'Kaezer', 'User', NULL),
(3, '22117054', 'hatdog321@gmail.com', '$2y$10$pozcB46dMcuSQCeWHjM.wuTdqiLQGOaChzz8WN0RvPBS9gRkYV0jy', 'KIZIR', 'User', NULL),
(4, '1', 'hamburger@gmail.com', '$2y$10$Iwit003y1Dn/YgiboPnWKeOfBTOUNapJTxZdihYrnlFuHG0KlQ3mi', 'Mark Reyes', 'Admin', 'default.jpg'),
(5, '2', 'test@gmail.com', '$2y$10$MIYeofm/P.kzk8LJXJ0W5.ShuOuN5Ejse/myFbmPTUVwUY03v4GGG', 'Staff', 'Staff', 'default.jpg'),
(6, '123', 'testing@gmail.com', '$2y$10$moWuVmGQQk1tx70aROH/GuyCaAx1bsswDfAfbGuwSZ5Nyh.Z/kpGS', 'Pro', 'Staff', 'default.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `message_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`message_id`, `ticket_id`, `sender_id`, `content`, `timestamp`) VALUES
(10, 4, 2, 'Pls help', '2025-10-13 16:26:01'),
(11, 4, 4, 'what happened', '2025-10-13 16:26:20');

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

CREATE TABLE `permission` (
  `perm_id` int(11) NOT NULL,
  `perm_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permission`
--

INSERT INTO `permission` (`perm_id`, `perm_type`) VALUES
(2, 'ADD_PARTICIPANT'),
(1, 'CLAIM_TICKET'),
(3, 'MANAGE_ACCOUNTS');

-- --------------------------------------------------------

--
-- Table structure for table `preticketquestion`
--

CREATE TABLE `preticketquestion` (
  `question_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suggestion`
--

CREATE TABLE `suggestion` (
  `suggestion_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `details` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `anonymous` tinyint(1) DEFAULT 0,
  `upvotes` int(11) DEFAULT 0,
  `downvotes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suggestion`
--

INSERT INTO `suggestion` (`suggestion_id`, `account_id`, `details`, `created_at`, `anonymous`, `upvotes`, `downvotes`) VALUES
(3, 2, 'make the UI more cleaner', '2025-10-05 10:44:20', 0, 0, 1),
(4, 2, 'enable us to send images and files on Tickets', '2025-10-05 10:45:23', 1, 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `ticket_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `is_anonymous` tinyint(1) DEFAULT 0,
  `status` enum('Open','In-progress','Closed','Resolved','Reopened') DEFAULT 'Open',
  `created_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket`
--

INSERT INTO `ticket` (`ticket_id`, `created_by`, `assigned_to`, `title`, `category`, `details`, `is_anonymous`, `status`, `created_at`) VALUES
(1, 2, NULL, 'I got harassed by #', 'Harassment', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 0, 'Open', '2025-10-13 16:18:58'),
(2, 2, 4, 'Lorep Ipsum', 'Bullying', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 1, 'In-progress', '2025-10-13 16:24:39'),
(3, 2, 4, 'I got bullied by #', 'Bullying', 'He\'s saying that...', 0, 'Reopened', '2025-10-13 16:25:15'),
(4, 2, NULL, 'My wallet got stolen', 'Vandalism or Theft', '# stole my wallet', 1, 'Closed', '2025-10-13 17:27:10');

-- --------------------------------------------------------

--
-- Table structure for table `ticketuser`
--

CREATE TABLE `ticketuser` (
  `ticket_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `role_ticket` enum('User','Staff','Admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_log`
--

CREATE TABLE `ticket_log` (
  `log_id` int(11) NOT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `real_user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket_log`
--

INSERT INTO `ticket_log` (`log_id`, `ticket_id`, `real_user_id`, `ip_address`, `user_agent`, `created_at`) VALUES
(9, 1, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-13 16:18:58'),
(10, 2, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-13 16:22:49'),
(11, 3, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-13 16:23:41'),
(12, 4, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-13 16:25:49');

-- --------------------------------------------------------

--
-- Table structure for table `userperm`
--

CREATE TABLE `userperm` (
  `account_id` int(11) NOT NULL,
  `perm_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vote`
--

CREATE TABLE `vote` (
  `vote_id` int(11) NOT NULL,
  `suggestion_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote_type` enum('Upvote','Downvote') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vote`
--

INSERT INTO `vote` (`vote_id`, `suggestion_id`, `user_id`, `vote_type`) VALUES
(20, 4, 2, 'Upvote'),
(21, 3, 2, 'Downvote'),
(22, 4, 3, 'Upvote');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `permission`
--
ALTER TABLE `permission`
  ADD PRIMARY KEY (`perm_id`),
  ADD UNIQUE KEY `perm_type` (`perm_type`);

--
-- Indexes for table `preticketquestion`
--
ALTER TABLE `preticketquestion`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indexes for table `suggestion`
--
ALTER TABLE `suggestion`
  ADD PRIMARY KEY (`suggestion_id`),
  ADD KEY `user_id` (`account_id`);

--
-- Indexes for table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `ticketuser`
--
ALTER TABLE `ticketuser`
  ADD PRIMARY KEY (`ticket_id`,`account_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `ticket_log`
--
ALTER TABLE `ticket_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_ticketlog_ticket` (`ticket_id`),
  ADD KEY `fk_ticketlog_user` (`real_user_id`);

--
-- Indexes for table `userperm`
--
ALTER TABLE `userperm`
  ADD PRIMARY KEY (`account_id`,`perm_id`),
  ADD KEY `perm_id` (`perm_id`);

--
-- Indexes for table `vote`
--
ALTER TABLE `vote`
  ADD PRIMARY KEY (`vote_id`),
  ADD UNIQUE KEY `suggestion_id` (`suggestion_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `permission`
--
ALTER TABLE `permission`
  MODIFY `perm_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `preticketquestion`
--
ALTER TABLE `preticketquestion`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suggestion`
--
ALTER TABLE `suggestion`
  MODIFY `suggestion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ticket_log`
--
ALTER TABLE `ticket_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `vote`
--
ALTER TABLE `vote`
  MODIFY `vote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `account` (`account_id`) ON DELETE CASCADE;

--
-- Constraints for table `preticketquestion`
--
ALTER TABLE `preticketquestion`
  ADD CONSTRAINT `preticketquestion_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`) ON DELETE CASCADE;

--
-- Constraints for table `suggestion`
--
ALTER TABLE `suggestion`
  ADD CONSTRAINT `suggestion_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`) ON DELETE SET NULL;

--
-- Constraints for table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `account` (`account_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `account` (`account_id`) ON DELETE SET NULL;

--
-- Constraints for table `ticketuser`
--
ALTER TABLE `ticketuser`
  ADD CONSTRAINT `ticketuser_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticketuser_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`) ON DELETE CASCADE;

--
-- Constraints for table `ticket_log`
--
ALTER TABLE `ticket_log`
  ADD CONSTRAINT `fk_ticketlog_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ticketlog_user` FOREIGN KEY (`real_user_id`) REFERENCES `account` (`account_id`) ON DELETE SET NULL;

--
-- Constraints for table `userperm`
--
ALTER TABLE `userperm`
  ADD CONSTRAINT `userperm_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `userperm_ibfk_2` FOREIGN KEY (`perm_id`) REFERENCES `permission` (`perm_id`) ON DELETE CASCADE;

--
-- Constraints for table `vote`
--
ALTER TABLE `vote`
  ADD CONSTRAINT `vote_ibfk_1` FOREIGN KEY (`suggestion_id`) REFERENCES `suggestion` (`suggestion_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vote_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `account` (`account_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
