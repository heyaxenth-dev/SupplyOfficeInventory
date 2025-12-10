-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2025 at 02:42 PM
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
-- Database: `soi_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `stock_number` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `unit_of_measure` varchar(50) DEFAULT NULL,
  `unit_value` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `status` enum('In Stock','Low Stock','Out of Stock') DEFAULT 'In Stock',
  `last_restocked` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `item_name`, `description`, `stock_number`, `category`, `unit_of_measure`, `unit_value`, `quantity`, `status`, `last_restocked`, `created_at`, `updated_at`) VALUES
(1, 'Acrylic Color', '', 'AC9899', 'Color', 'set', 120.00, 0, 'In Stock', '2025-12-07', '2025-12-07 15:55:27', '2025-12-10 12:58:09'),
(2, 'Acrylic Color', '', 'AC9875', 'Color', 'set', 120.00, 5, 'In Stock', '2025-12-07', '2025-12-07 15:55:33', '2025-12-07 15:55:33'),
(3, 'Acrylic Color', '', 'AC0580', 'Color', 'set', 120.00, 5, 'In Stock', '2025-12-07', '2025-12-07 15:57:52', '2025-12-07 15:57:52'),
(4, 'Acrylic Color', '', 'AC1848', 'Color', 'set', 120.00, 5, 'In Stock', '2025-12-07', '2025-12-07 15:58:44', '2025-12-07 15:58:44'),
(5, 'Battery AAA rechargeable', 'N/A', 'BA8017', 'Batteries', 'pc', 50.00, 6, 'In Stock', '2025-12-08', '2025-12-08 06:39:42', '2025-12-08 06:39:42'),
(6, 'Adapter', 'N/A', 'AD6008', 'Electronic', 'pc', 35.00, 10, 'In Stock', '2025-12-09', '2025-12-09 05:49:58', '2025-12-09 05:49:58'),
(7, 'Adding Machine Paper', 'Testing', 'AD3292', 'Papers', 'roll', 50.00, 50, 'In Stock', '2025-12-10', '2025-12-10 11:42:47', '2025-12-10 11:42:47');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `role`, `department`, `email`, `password`, `created_at`) VALUES
(1, 'Hya Cynth Dojillo', 'Admin', 'IT Department', 'hyacynth.mulaveintern@gmail.com', '$2y$10$MNU6qYPXoAip0mE1SLIc..D7Lm4FSEi1SUvaUDHekyyawAe59juFG', '2025-06-07 14:56:38'),
(2, 'Yangyang Dojillo', 'Staff', 'IT Department', 'hyacynth.dev@gmail.com', '$2y$10$fbp5bYZtwTYlIlOd0m7AZ.fr01t4MgbeA3.iaLbNdRMgRtMI1l8bu', '2025-12-10 13:03:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_stock_number` (`stock_number`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
