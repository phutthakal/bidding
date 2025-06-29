-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2025 at 04:44 PM
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
-- Database: `bidding`
--

-- --------------------------------------------------------

--
-- Table structure for table `bids`
--

CREATE TABLE `bids` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `created_at`) VALUES
(1, 'Daiichipacking', '2025-06-26 07:40:34'),
(2, 'GGO', '2025-06-26 07:44:59');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('open','closed','winner_declared') DEFAULT 'open',
  `created_at` datetime DEFAULT current_timestamp(),
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bidding_start` datetime NOT NULL,
  `bidding_end` datetime NOT NULL,
  `minimum_bid` decimal(10,2) DEFAULT NULL,
  `winner_id` int(11) DEFAULT NULL,
  `update_price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `seller_id`, `title`, `description`, `image_url`, `status`, `created_at`, `price`, `bidding_start`, `bidding_end`, `minimum_bid`, `winner_id`, `update_price`, `quantity`, `unit`) VALUES
(1, 1, 'การทำแอพ', '/////////////////////////////////////////////////////////////////////////////////////////////////////////', '../uploads/1750901771-Screenshot 2024-08-16 094154.png', 'closed', '2025-06-26 08:36:11', 60000.00, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, NULL, NULL, 1, ''),
(2, 1, 'ม้วนฟิลม', '**********************************************', '../uploads/1750901864-Screenshot 2024-09-18 080646.png,../uploads/1750901864-Screenshot 2024-08-19 111219.png', 'closed', '2025-06-26 08:37:44', 50000.00, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, NULL, NULL, 1, ''),
(3, 1, 'หีบ', '55555', '../uploads/1750903182-Screenshot 2025-06-23 163638.png', 'closed', '2025-06-26 08:59:42', 555555.00, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, NULL, NULL, 1, ''),
(4, 1, 'test', 'test', '../uploads/1750904674-Screenshot 2025-06-23 163638.png', 'closed', '2025-06-26 09:24:34', 50000.00, '2025-06-26 09:24:00', '2025-06-26 09:30:00', NULL, NULL, NULL, 1, ''),
(5, 1, 'yyy', 'yyy', '../uploads/1750904700-Screenshot 2025-06-19 121530.png', 'closed', '2025-06-26 09:25:00', 7000.00, '2025-06-27 09:24:00', '2025-06-28 09:27:00', NULL, NULL, NULL, 1, ''),
(6, 1, 'hhh', 'hhh', '../uploads/1750904976-Screenshot 2025-04-09 134003.png', 'closed', '2025-06-26 09:29:36', 5555.00, '2025-06-25 09:29:00', '2025-06-27 09:29:00', 1000.00, 1, 2000.00, 1, ''),
(7, 1, 'พพ', 'พพ', NULL, 'closed', '2025-06-26 10:29:30', 5000.00, '2025-06-26 10:29:00', '2025-06-26 10:35:00', 1000.00, NULL, NULL, 1, ''),
(8, 1, 'ิิิแแแ', 'แแแ', NULL, 'closed', '2025-06-26 10:31:19', 5000.00, '2025-06-26 10:29:00', '2025-06-26 10:35:00', 1000.00, NULL, NULL, 1, ''),
(9, 1, 'kkk', 'kkk', NULL, 'closed', '2025-06-26 10:32:49', 5555.00, '2025-06-26 10:29:00', '2025-06-26 10:34:00', 1000.00, NULL, NULL, 1, ''),
(10, 1, 'bbb', 'bbb', NULL, 'closed', '2025-06-26 10:33:20', 5000.00, '2025-06-26 10:33:00', '2025-06-26 10:35:00', 500.00, NULL, NULL, 1, ''),
(11, 1, 'jj', 'jj', NULL, 'closed', '2025-06-26 10:41:37', 9000.00, '2025-06-26 10:41:00', '2025-06-26 10:44:00', 600.00, NULL, NULL, 1, ''),
(12, 1, 'ppp', 'jj', NULL, 'closed', '2025-06-26 10:45:32', 5200.00, '2025-06-26 10:41:00', '2025-06-26 11:47:00', 900.00, 1, NULL, 1, ''),
(13, 1, '6666', '66666', '../uploads/1750909716-Screenshot 2025-06-19 113534.png,../uploads/1750909716-Screenshot 2025-06-23 163638.png,../uploads/1750909716-Screenshot 2025-06-19 144703.png', 'closed', '2025-06-26 10:48:36', 6000.00, '2025-06-26 10:48:00', '2025-06-26 10:50:00', 900.00, NULL, NULL, 1, ''),
(14, 1, 'rrr', 'rrr', '../uploads/1750910325-Screenshot 2025-06-23 163638.png,../uploads/1750910325-Screenshot 2025-06-19 144703.png,../uploads/1750910325-Screenshot 2025-06-19 113446.png', 'closed', '2025-06-26 10:58:45', 5000.00, '2025-06-26 10:58:00', '2025-06-26 11:00:00', 600.00, NULL, NULL, 1, ''),
(15, 1, 'test', 'test', '../uploads/1750911713-Screenshot 2025-06-23 163638.png,../uploads/1750911713-Screenshot 2025-06-19 144703.png,../uploads/1750911713-Screenshot 2025-06-19 121830.png', 'closed', '2025-06-26 11:21:53', 6000.00, '2025-06-26 11:21:00', '2025-06-26 00:30:00', 200.00, NULL, NULL, 1, ''),
(16, 1, 'test', 'test', '../uploads/1750911884-Screenshot 2025-06-19 121830.png,../uploads/1750911884-Screenshot 2025-06-19 113534.png,../uploads/1750911884-Screenshot 2025-06-19 113446.png', 'closed', '2025-06-26 11:24:44', 6000.00, '2025-06-26 11:23:00', '2025-06-26 12:30:00', 200.00, 1, 200.00, 1, ''),
(17, 1, 'test', 'test', '', 'closed', '2025-06-26 13:34:21', 5000.00, '2025-06-26 13:33:00', '2025-06-26 13:36:00', 100.00, NULL, NULL, 22, ''),
(18, 1, 'test', 'test', '', 'open', '2025-06-29 21:22:52', 100000.00, '2025-06-29 21:22:00', '2025-06-30 00:22:00', 13000.00, NULL, NULL, 22, ''),
(19, 1, 'test', 'test', '../uploads/1751207917-Image 23 พ.ค. 2568 21_56_55.png,../uploads/1751207917-Logo_dai.jpg,../uploads/1751207917-S__81108994.jpg', 'open', '2025-06-29 21:38:37', 50000.00, '2025-06-29 21:38:00', '2025-06-30 21:38:00', 2000.00, NULL, NULL, 10, 'ม้วน'),
(20, 1, 'GG', 'GG', '../uploads/1751208054-Logo_dai.jpg', 'open', '2025-06-29 21:40:54', 60000.00, '2025-06-29 21:40:00', '2025-07-01 21:40:00', 10000.00, NULL, NULL, 30, 'ชิ้น');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','buyer','seller') NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password_hash`, `role`, `phone`, `company_id`, `created_at`) VALUES
(1, 'phutthakal', 'thmmavises', 'phutthakal@dai-ichipack.com', '$2y$10$TniCgT7z4r2wzGM680C1TuPYxjWUiJfl/jzNXpXwsbeN1tTwcChxy', 'admin', '123456789', 1, '2025-06-26 07:40:35'),
(2, 'phutthakal', 'thmmavises', 'flook@gmail.com', '$2y$10$WcneiN4J7uQ4qDohnTiklOvToCiJ3CXbByOkJ8Pdg6Vh29AVA7sCe', 'seller', '123456789', 2, '2025-06-26 07:44:59');

-- --------------------------------------------------------

--
-- Table structure for table `winners`
--

CREATE TABLE `winners` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bid_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bids`
--
ALTER TABLE `bids`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `winners`
--
ALTER TABLE `winners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `bid_id` (`bid_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bids`
--
ALTER TABLE `bids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `winners`
--
ALTER TABLE `winners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bids`
--
ALTER TABLE `bids`
  ADD CONSTRAINT `bids_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `bids_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Constraints for table `winners`
--
ALTER TABLE `winners`
  ADD CONSTRAINT `winners_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `winners_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `winners_ibfk_3` FOREIGN KEY (`bid_id`) REFERENCES `bids` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
