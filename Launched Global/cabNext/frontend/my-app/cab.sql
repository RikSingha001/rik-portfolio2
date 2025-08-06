-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2025 at 10:02 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cab`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `travel_date` date DEFAULT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `vehicle_number` varchar(50) DEFAULT NULL,
  `guest_name` varchar(100) DEFAULT NULL,
  `guest_contact` varchar(20) DEFAULT NULL,
  `guest_location` text DEFAULT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `reference_name` varchar(100) DEFAULT NULL,
  `trip` varchar(255) DEFAULT NULL,
  `invoice_number` varchar(100) DEFAULT NULL,
  `pickup_time` varchar(50) DEFAULT NULL,
  `drop_time` varchar(50) DEFAULT NULL,
  `assoc_vendor` varchar(100) DEFAULT NULL,
  `op_km` varchar(20) DEFAULT NULL,
  `total_km` varchar(20) DEFAULT NULL,
  `toll_parking` varchar(20) DEFAULT NULL,
  `night` varchar(20) DEFAULT NULL,
  `total_amount` varchar(20) DEFAULT NULL,
  `fuel_office` varchar(20) DEFAULT NULL,
  `fuel_cash` varchar(20) DEFAULT NULL,
  `road_tax` varchar(20) DEFAULT NULL,
  `expenses` varchar(20) DEFAULT NULL,
  `adv_office` varchar(20) DEFAULT NULL,
  `location_link` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `aadhar_number` varchar(20) NOT NULL,
  `pan_number` varchar(20) NOT NULL,
  `license_number` varchar(50) NOT NULL,
  `vehicle_number` varchar(20) NOT NULL,
  `vehicle_model` varchar(100) NOT NULL,
  `vehicle_usage` varchar(50) NOT NULL,
  `vehicle_mileage` decimal(5,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `user_id`, `name`, `email`, `password`, `phone`, `aadhar_number`, `pan_number`, `license_number`, `vehicle_number`, `vehicle_model`, `vehicle_usage`, `vehicle_mileage`, `created_at`) VALUES
(1, 2, 'qq', 'qqw@q', 'qq', 'qq', 'qq', 'qq', 'qq', 'qq', 'qq', '1', 11.00, '2025-06-26 15:19:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `aadhar_number` varchar(20) NOT NULL,
  `role` enum('user','driver','vendor') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `aadhar_number`, `role`, `created_at`) VALUES
(1, 'aa', 'aa@aa', 'aaa', 'aaa', 'aaa', 'user', '2025-06-26 15:06:40'),
(2, 'qq', 'qqw@q', 'qq', 'qq', 'qq', 'driver', '2025-06-26 15:19:09'),
(3, 'ss', 'ss@ss', 'ss', 'ss', 'ss', 'vendor', '2025-06-26 15:38:17');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `aadhar_number` varchar(20) NOT NULL,
  `pan_number` varchar(20) NOT NULL,
  `license_number` varchar(50) NOT NULL,
  `vehicle_number` varchar(20) NOT NULL,
  `vehicle_model` varchar(100) NOT NULL,
  `vehicle_usage` varchar(50) NOT NULL,
  `vehicle_mileage` decimal(5,2) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `company_email` varchar(100) NOT NULL,
  `company_licence` varchar(100) NOT NULL,
  `company_address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_labor`
--

CREATE TABLE `vendor_labor` (
  `id` int(11) NOT NULL,
  `employeeID` varchar(50) NOT NULL,
  `driver_name` varchar(100) NOT NULL,
  `date_of_join` date NOT NULL,
  `password` varchar(255) NOT NULL,
  `vehicleType` varchar(50) NOT NULL,
  `vehicleNumber` varchar(50) NOT NULL,
  `vehicleModel` varchar(100) NOT NULL,
  `availability` varchar(100) NOT NULL,
  `condition_check_status` varchar(100) NOT NULL,
  `vehicleInsurance` varchar(100) NOT NULL,
  `panNumber` varchar(20) NOT NULL,
  `aadharNumber` varchar(20) NOT NULL,
  `licenseNumber` varchar(50) NOT NULL,
  `phoneNumber` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `salary` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `bankAccountNumber` varchar(50) NOT NULL,
  `ifscCode` varchar(20) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `company_email` varchar(100) NOT NULL,
  `company_licence` varchar(100) NOT NULL,
  `company_address` text NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `company_contact` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_labor`
--

INSERT INTO `vendor_labor` (`id`, `employeeID`, `driver_name`, `date_of_join`, `password`, `vehicleType`, `vehicleNumber`, `vehicleModel`, `availability`, `condition_check_status`, `vehicleInsurance`, `panNumber`, `aadharNumber`, `licenseNumber`, `phoneNumber`, `email`, `address`, `salary`, `department`, `bankAccountNumber`, `ifscCode`, `owner_name`, `company_email`, `company_licence`, `company_address`, `company_name`, `company_contact`, `created_at`) VALUES
(1, 'aa', 'aa', '2025-06-26', 'aa', 'aaa', 'aa', 'a', 'aa', 'aa', 'aaa', 'aa', 'aa', 'aa', '66', 'aa@a', 'aa', 'aa', 'aa', 'aa', 'aa', 'aa', 'aa@aa', 'aa', 'aa', 'aa', 'aa', '2025-06-27 07:49:42');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_registration`
--

CREATE TABLE `vendor_registration` (
  `id` int(11) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `company_email` varchar(100) NOT NULL,
  `company_licence` varchar(100) NOT NULL,
  `company_address` text NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_registration`
--

INSERT INTO `vendor_registration` (`id`, `owner_name`, `company_email`, `company_licence`, `company_address`, `company_name`, `created_at`) VALUES
(1, 'ss', 'ss@ss', 'ss', 'ss', 'ss', '2025-06-26 15:36:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vendor_labor`
--
ALTER TABLE `vendor_labor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendor_registration`
--
ALTER TABLE `vendor_registration`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vendor_labor`
--
ALTER TABLE `vendor_labor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vendor_registration`
--
ALTER TABLE `vendor_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
