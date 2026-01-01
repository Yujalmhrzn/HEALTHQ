-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 12, 2025 at 03:34 PM
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
-- Database: `healthq`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appointment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `fullname` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appointment_id`, `user_id`, `hospital_id`, `date_time`, `specialization`, `fullname`, `phone`, `status`) VALUES
(66, 18, 1, '2025-01-24 23:05:00', 'Internal Medicine', 'Yuwa Shrestha', '9823016150', 'approved'),
(67, 18, 1, '2025-01-15 20:27:00', 'Internal Medicine', 'Yuwa Shrestha', '9823016150', 'approved'),
(68, 18, 1, '2025-01-21 10:06:00', 'Internal Medicine', 'Sanil Maharjan', '9823016150', 'approved'),
(69, 18, 1, '2025-01-31 02:19:00', 'Obstetrics and Gynecology', 'Sanil Maharjan', '1111111111', 'pending'),
(70, 18, 2, '2025-01-24 19:59:00', 'General Surgery', 'Sanil Maharjan', '9823016150', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `approved`
--

CREATE TABLE `approved` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `approved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approved`
--

INSERT INTO `approved` (`id`, `appointment_id`, `fullname`, `phone`, `date_time`, `specialization`, `status`, `approved_at`) VALUES
(1, 54, 'pali mhrjn', '9861988832', '2025-01-10 23:20:00', 'Pediatrics', 'approved', '2025-01-06 16:36:28'),
(2, 55, 'asddd', '9861988832', '2025-01-08 23:26:00', 'Internal Medicine', 'approved', '2025-01-06 16:40:54'),
(3, 60, 'pali mhrjn', '9861988832', '2025-01-09 10:01:00', 'Radiology', 'approved', '2025-01-07 04:16:29'),
(4, 62, 'sama', '0000000000', '2025-01-09 11:05:00', 'Pediatrics', 'approved', '2025-01-07 05:19:21'),
(5, 61, 'binod', '1234667890', '2025-01-08 11:42:00', 'General Surgery', 'approved', '2025-01-07 05:19:26'),
(6, 61, 'binod', '1234667890', '2025-01-08 11:42:00', 'General Surgery', 'approved', '2025-01-07 05:30:08'),
(7, 61, 'binod', '1234667890', '2025-01-08 11:42:00', 'General Surgery', 'approved', '2025-01-07 05:31:05'),
(8, 66, 'Yuwa Shrestha', '9823016150', '2025-01-24 23:05:00', 'Internal Medicine', 'approved', '2025-01-08 13:16:28'),
(9, 67, 'Yuwa Shrestha', '9823016150', '2025-01-15 20:27:00', 'Internal Medicine', 'approved', '2025-01-09 04:15:54'),
(10, 68, 'Sanil Maharjan', '9823016150', '2025-01-21 10:06:00', 'Internal Medicine', 'approved', '2025-01-12 14:20:22');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `specialty` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `fullname`, `email`, `phone`, `specialty`) VALUES
(12, 'Dr. John Doe', 'John1@gmail.com', '1111111111', 4),
(13, 'Dr. Sarah Smith', 'Sarah2@gmail.com', '2222222222', 5),
(14, 'Dr. Emily Davi', 'Emily3@gmail.com', '3333333333', 6),
(15, 'Dr. Michael Brown', 'Michael4@gmail.com', '4444444444', 7),
(16, 'Dr. Olivia Wilson', 'Olivia5@gmail.com', '5555555555', 8),
(17, 'Dr. William Johnson', 'William6@gmail.com', '6666666666', 9),
(18, 'Dr. James Anderson', 'James7@gmail.com', '7777777777', 10),
(19, 'Dr. Ava Lee', 'Ava8@gmail.com', '8888888888', 11),
(20, 'Dr. Isabella Clark', 'Isabella9@gmail.com', '9999999999', 12),
(21, 'Dr. Ethan Harris', 'Ethan10@gmail.com', '1010101010', 13),
(22, 'Dr. Lucas Lewis', 'Lucas11@gmail.com', '0101010101', 14),
(23, 'Dr. Mia Walker', 'Mia12@gmail.com', '1212121212', 15);

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `hospital_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`hospital_id`, `name`) VALUES
(1, 'City Hospital'),
(2, 'General Health Clinic'),
(3, 'Community Medical Center'),
(4, 'St. John\'s Hospital'),
(5, 'Mercy Regional Hospital');

-- --------------------------------------------------------

--
-- Table structure for table `rejected`
--

CREATE TABLE `rejected` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `rejected_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rejected`
--

INSERT INTO `rejected` (`id`, `appointment_id`, `fullname`, `phone`, `date_time`, `specialization`, `status`, `rejected_at`, `feedback`) VALUES
(9, 63, 'ram', '9861988832', '2025-01-09 01:17:00', 'Internal Medicine', 'rejected', '2025-01-07 05:32:01', 'kokokokoko'),
(10, 65, 'sumiran ', '9861988832', '2025-01-07 12:56:00', 'Internal Medicine', 'rejected', '2025-01-07 07:11:29', 'no doc');

-- --------------------------------------------------------

--
-- Table structure for table `specializations`
--

CREATE TABLE `specializations` (
  `id` int(11) NOT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `specializations`
--

INSERT INTO `specializations` (`id`, `hospital_id`, `name`) VALUES
(2, 5, 'Pediatrics'),
(4, 1, 'Internal Medicine'),
(5, 1, 'Obstetrics and Gynecology'),
(6, 1, 'Dermatology'),
(7, 2, 'Pediatrics'),
(8, 2, 'Radiology'),
(9, 2, 'General Surgery'),
(10, 3, 'Ophthalmology'),
(11, 3, 'Family Medicine'),
(12, 4, 'Chest Medicine'),
(13, 4, 'Anesthesia'),
(14, 4, 'Pathology'),
(15, 5, 'ENT');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `type` enum('user','admin','doctor') NOT NULL,
  `hospital_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `fullname`, `phone`, `type`, `hospital_name`) VALUES
(1, 'sanilmaharjan@gmail.com', 'sanilmaharjan', 'sanilmaharjan', '9823016150', 'admin', NULL),
(18, 'yuwa@gmail.com', 'yuwa@123', 'yuwa', '9823016150', 'user', NULL),
(23, 'yujal@gmail.com', 'yujalmaharjan', 'Yujal Maharjan', '1111111111', 'admin', 'City Hospital'),
(24, 'yujala@gmail.com', 'yujala', 'yujala', '1111111111', 'admin', 'General Health Clinic');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `hospital_id` (`hospital_id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `approved`
--
ALTER TABLE `approved`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD KEY `fk_specialty` (`specialty`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`hospital_id`);

--
-- Indexes for table `rejected`
--
ALTER TABLE `rejected`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `specializations`
--
ALTER TABLE `specializations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_id` (`hospital_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `approved`
--
ALTER TABLE `approved`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `hospital_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rejected`
--
ALTER TABLE `rejected`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `specializations`
--
ALTER TABLE `specializations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`),
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`specialty`) REFERENCES `specializations` (`id`),
  ADD CONSTRAINT `fk_specialty` FOREIGN KEY (`specialty`) REFERENCES `specializations` (`id`);

--
-- Constraints for table `specializations`
--
ALTER TABLE `specializations`
  ADD CONSTRAINT `specializations_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
