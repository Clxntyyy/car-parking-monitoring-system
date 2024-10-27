-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 26, 2024 at 05:30 PM
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
-- Database: `cpms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `history_tbl`
--

CREATE TABLE `history_tbl` (
  `history_id` int(11) NOT NULL,
  `entry_time` datetime NOT NULL,
  `exit_time` datetime DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `pslot_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parkingslots_tbl`
--

CREATE TABLE `parkingslots_tbl` (
  `pslot_id` int(11) NOT NULL,
  `slot_number` int(11) NOT NULL,
  `status` enum('available','occupied','reserved') DEFAULT 'available',
  `vehicle_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_tbl`
--

CREATE TABLE `payment_tbl` (
  `payment_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','gcash') NOT NULL,
  `payment_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_tbl`
--

CREATE TABLE `ticket_tbl` (
  `ticket_id` int(11) NOT NULL,
  `ticket_no` varchar(50) NOT NULL,
  `entry_time` datetime NOT NULL,
  `exit_time` datetime DEFAULT NULL,
  `is_overtime` tinyint(1) DEFAULT 0,
  `user_id` int(11) NOT NULL,
  `pslot_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_tbl`
--

CREATE TABLE `user_tbl` (
  `user_id` int(11) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `contact_no` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `access_level` enum('admin','customer','staff') DEFAULT 'customer',
  `plate_number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicletype_tbl`
--

CREATE TABLE `vehicletype_tbl` (
  `type_id` int(11) NOT NULL,
  `type_name` enum('car','tricycle','motor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_tbl`
--

CREATE TABLE `vehicle_tbl` (
  `vehicle_id` int(11) NOT NULL,
  `plate_number` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vehicle_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `history_tbl`
--
ALTER TABLE `history_tbl`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pslot_id` (`pslot_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `parkingslots_tbl`
--
ALTER TABLE `parkingslots_tbl`
  ADD PRIMARY KEY (`pslot_id`),
  ADD UNIQUE KEY `slot_number` (`slot_number`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payment_tbl`
--
ALTER TABLE `payment_tbl`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ticket_tbl`
--
ALTER TABLE `ticket_tbl`
  ADD PRIMARY KEY (`ticket_id`),
  ADD UNIQUE KEY `ticket_no` (`ticket_no`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pslot_id` (`pslot_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `user_tbl`
--
ALTER TABLE `user_tbl`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `plate_number` (`plate_number`);

--
-- Indexes for table `vehicletype_tbl`
--
ALTER TABLE `vehicletype_tbl`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `vehicle_tbl`
--
ALTER TABLE `vehicle_tbl`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD UNIQUE KEY `plate_number` (`plate_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vehicle_type_id` (`vehicle_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `history_tbl`
--
ALTER TABLE `history_tbl`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parkingslots_tbl`
--
ALTER TABLE `parkingslots_tbl`
  MODIFY `pslot_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_tbl`
--
ALTER TABLE `payment_tbl`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_tbl`
--
ALTER TABLE `ticket_tbl`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_tbl`
--
ALTER TABLE `user_tbl`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicletype_tbl`
--
ALTER TABLE `vehicletype_tbl`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicle_tbl`
--
ALTER TABLE `vehicle_tbl`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `history_tbl`
--
ALTER TABLE `history_tbl`
  ADD CONSTRAINT `history_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_tbl` (`user_id`),
  ADD CONSTRAINT `history_tbl_ibfk_2` FOREIGN KEY (`pslot_id`) REFERENCES `parkingslots_tbl` (`pslot_id`),
  ADD CONSTRAINT `history_tbl_ibfk_3` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle_tbl` (`vehicle_id`);

--
-- Constraints for table `parkingslots_tbl`
--
ALTER TABLE `parkingslots_tbl`
  ADD CONSTRAINT `parkingslots_tbl_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle_tbl` (`vehicle_id`),
  ADD CONSTRAINT `parkingslots_tbl_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user_tbl` (`user_id`);

--
-- Constraints for table `payment_tbl`
--
ALTER TABLE `payment_tbl`
  ADD CONSTRAINT `payment_tbl_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket_tbl` (`ticket_id`),
  ADD CONSTRAINT `payment_tbl_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user_tbl` (`user_id`);

--
-- Constraints for table `ticket_tbl`
--
ALTER TABLE `ticket_tbl`
  ADD CONSTRAINT `ticket_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_tbl` (`user_id`),
  ADD CONSTRAINT `ticket_tbl_ibfk_2` FOREIGN KEY (`pslot_id`) REFERENCES `parkingslots_tbl` (`pslot_id`),
  ADD CONSTRAINT `ticket_tbl_ibfk_3` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle_tbl` (`vehicle_id`);

--
-- Constraints for table `vehicle_tbl`
--
ALTER TABLE `vehicle_tbl`
  ADD CONSTRAINT `vehicle_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_tbl` (`user_id`),
  ADD CONSTRAINT `vehicle_tbl_ibfk_2` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicletype_tbl` (`type_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
