-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 08, 2025 at 11:07 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sample-php`
--

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `role` varchar(20) DEFAULT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `email`, `password`, `username`, `role`, `isDeleted`) VALUES
(1, 'admin@example.com', '$2y$12$osPwh0o06JQGPTW.J86DP.kV1gPL3pvgNcJptfuq3RLwF57rGdW5S', 'Admin User', 'admin', 0),
(2, 'sarishma@example.com', '$2y$10$0G8Ld8PzSpo/fbNagtLSHOFQCmBlXId5rngYnOY.qOvpj8z0IIk5i', 'Sarishma Ghale', 'staff', 1),
(4, 'user@example.com', '$2y$10$mcAANa0vQ48FHezRTDyYxObRx6FkdWTTi5Lx6z2VGqhM87GdMhe9e', 'Sarishma Ghale', 'staff', 1),
(5, 'abc@example.com', '$2y$10$urHmDgGW0CEjhPkQj7PzOOx9UW6vsH3A9wGm2OFdoQhrDLekfg.KG', 'Second User', 'staff', 1),
(7, 'saru@example.com', '$2y$10$u2u0JX.e9ftHocQAyFbTK.ODdutJ5myRaNS9JfzbxZTCN.YevGQNe', 'Sarishma Ghale', 'staff', 1),
(8, 'sg@example.com', '$2y$12$ftVjPcL4f92HBhSbuaWx6ef0vaExq7mHVISvDhllhVbXyHFfKady6', 'Sarishma Ghale', 'staff', 0),
(9, 'user1@example.com', '$2y$10$JW/yp89d8/DUYb0Fx9mKPuko8k/RPu4UfhKiXKSUNT0o7w2Fyp8qC', 'User 1', 'staff', 1),
(12, 'user2@example.com', '$2y$12$Zjg950fNQzVjKQtBvHmPzOpDoipw5VqMl9n.qzpSmbRPu/PKoeXyi', 'User 2', 'staff', 0);

-- --------------------------------------------------------

--
-- Table structure for table `employee_details`
--

CREATE TABLE `employee_details` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_details`
--

INSERT INTO `employee_details` (`id`, `employee_id`, `address`, `phone`, `dob`, `profile_image`) VALUES
(1, 8, 'Chitwan', '9876543210', '2002-07-25', 'pfp/TjseMDEcXSB1LUA8AqxHklL6NCXA15n8hWqSEYJc.png'),
(2, 1, 'Chitwan', '9012345678', '2023-06-13', 'pfp/MpczL00f8kQcBXd1cSBkJ75gny1PwvltMELtvyQn.jpg'),
(3, 9, 'Nepal', '9807656789', '2025-12-01', NULL),
(4, 12, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `employee_details`
--
ALTER TABLE `employee_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_employee` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `employee_details`
--
ALTER TABLE `employee_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employee_details`
--
ALTER TABLE `employee_details`
  ADD CONSTRAINT `fk_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
