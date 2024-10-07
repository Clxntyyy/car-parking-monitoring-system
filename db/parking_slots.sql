-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2024 at 07:23 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `carparking_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `parking_slots`
--

CREATE TABLE `parking_slots` (
  `id` int(11) NOT NULL,
  `slot_number` varchar(10) NOT NULL,
  `is_occupied` tinyint(1) NOT NULL DEFAULT 0,
  `user_id` int(11) DEFAULT NULL,
  `plate_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking_slots`
--

INSERT INTO `parking_slots` (`id`, `slot_number`, `is_occupied`, `user_id`, `plate_number`) VALUES
(1, 'LP1', 0, NULL, NULL),
(2, 'LP2', 0, NULL, NULL),
(3, 'LP3', 0, NULL, NULL),
(4, 'LP4', 0, NULL, NULL),
(5, 'LP5', 0, NULL, NULL),
(6, 'LP6', 1, 4, 'ASDF-123'),
(7, 'LP7', 1, 3, 'ASD-123'),
(8, 'LP8', 0, NULL, NULL),
(9, 'LP9', 0, NULL, NULL),
(10, 'LP10', 0, NULL, NULL),
(11, 'LP11', 0, NULL, NULL),
(12, 'RP1', 0, NULL, NULL),
(13, 'RP2', 0, NULL, NULL),
(14, 'RP3', 0, NULL, NULL),
(15, 'RP4', 0, NULL, NULL),
(16, 'RP5', 0, NULL, NULL),
(17, 'RP6', 0, NULL, NULL),
(18, 'RP7', 0, NULL, NULL),
(19, 'CP1', 0, NULL, NULL),
(20, 'CP2', 0, NULL, NULL),
(21, 'CP3', 0, NULL, NULL),
(22, 'CP4', 0, NULL, NULL),
(23, 'CP5', 0, NULL, NULL),
(24, 'CP6', 0, NULL, NULL),
(25, 'CP7', 0, NULL, NULL),
(26, 'CP8', 0, NULL, NULL),
(27, 'CP9', 0, NULL, NULL),
(28, 'CP10', 0, NULL, NULL),
(29, 'CP11', 0, NULL, NULL),
(30, 'CP12', 0, NULL, NULL),
(31, 'CP13', 0, NULL, NULL),
(32, 'CP14', 0, NULL, NULL),
(33, 'CP15', 0, NULL, NULL),
(34, 'CP16', 0, NULL, NULL),
(35, 'CP17', 0, NULL, NULL),
(36, 'CP18', 0, NULL, NULL),
(37, 'CP19', 0, NULL, NULL),
(38, 'CP20', 0, NULL, NULL),
(39, 'CP21', 0, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `parking_slots`
--
ALTER TABLE `parking_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `parking_slots`
--
ALTER TABLE `parking_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `parking_slots`
--
ALTER TABLE `parking_slots`
  ADD CONSTRAINT `parking_slots_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
