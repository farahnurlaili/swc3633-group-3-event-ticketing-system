-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2026 at 01:16 PM
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
-- Database: `event_ticketing`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  `booked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `event_id`, `quantity`, `total_price`, `status`, `booked_at`) VALUES
(1, 4, 1, 2, 300.00, 'confirmed', '2026-10-15 18:06:36'),
(2, 5, 1, 3, 450.00, 'confirmed', '2026-10-15 18:06:36'),
(3, 6, 2, 3, 240.00, 'confirmed', '2026-10-15 18:06:36'),
(4, 4, 3, 4, 100.00, 'pending', '2026-10-15 18:06:36'),
(5, 5, 4, 2, 80.00, 'cancelled', '2026-10-15 18:06:36'),
(6, 7, 5, 2, 120.00, 'confirmed', '2026-10-15 18:06:36'),
(7, 6, 1, 1, 150.00, 'confirmed', '2026-10-15 18:06:36'),
(8, 8, 2, 1, 80.00, 'confirmed', '2026-10-15 20:23:41'),
(9, 4, 3, 1, 25.00, 'confirmed', '2026-10-16 11:14:02');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(10) UNSIGNED NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`) VALUES
(1, 'Concert', 'Live music performances and festivals'),
(2, 'Seminar', 'Talks, conferences and professional workshops'),
(3, 'Sports', 'Tournaments and sporting events'),
(4, 'Arts', 'Cultural shows, theatre and exhibitions'),
(5, 'Comedy', 'Stand-up and live comedy shows');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `venue_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `organiser_id` int(10) UNSIGNED NOT NULL,
  `event_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `ticket_price` decimal(10,2) NOT NULL,
  `total_tickets` int(10) UNSIGNED NOT NULL,
  `available_tickets` int(10) UNSIGNED NOT NULL,
  `status` enum('draft','published','cancelled') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `title`, `description`, `venue_id`, `category_id`, `organiser_id`, `event_date`, `start_time`, `end_time`, `ticket_price`, `total_tickets`, `available_tickets`, `status`, `created_at`) VALUES
(1, 'Rock Fest 2026', 'Outdoor rock festival with local and regional bands.', 1, 1, 2, '2026-12-12', '19:00:00', '23:00:00', 150.00, 500, 494, 'published', '2026-10-15 18:06:36'),
(2, 'Tech Innovators Summit', 'One-day conference on AI, cloud and software engineering.', 2, 2, 2, '2026-11-20', '09:00:00', '17:00:00', 80.00, 200, 196, 'published', '2026-10-15 18:06:36'),
(3, 'Charity Futsal Cup', 'Inter-university futsal tournament in aid of local charities.', 3, 3, 3, '2026-11-28', '10:00:00', '18:00:00', 25.00, 300, 295, 'published', '2026-10-15 18:06:36'),
(4, 'Festival Seni Warisan', 'Traditional dance, music and craft showcase.', 4, 4, 3, '2026-12-05', '18:00:00', '22:00:00', 40.00, 250, 250, 'published', '2026-10-15 18:06:36'),
(5, 'Stand-Up Comedy Night', 'An evening of stand-up with five up-and-coming comedians.', 5, 5, 2, '2026-11-15', '20:00:00', '22:30:00', 60.00, 120, 118, 'published', '2026-10-15 18:06:36'),
(6, 'Startup Pitch Day', 'Early-stage startups pitch to investors. Details to be finalised.', 2, 2, 3, '2027-01-18', '09:30:00', '16:00:00', 20.00, 100, 100, 'draft', '2026-10-15 18:06:36'),
(8, 'Learn JavaScript', '', 5, 2, 2, '2026-10-22', '09:00:00', '17:00:00', 20.00, 150, 150, 'published', '2026-10-16 11:12:01');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(10) UNSIGNED NOT NULL,
  `booking_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('card','online_banking','ewallet','cash') NOT NULL,
  `status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `transaction_ref` varchar(50) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL
) ;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `amount`, `method`, `status`, `transaction_ref`, `paid_at`) VALUES
(1, 1, 300.00, 'card', 'paid', 'TXN-20261001-0001', '2026-10-01 10:15:00'),
(2, 2, 450.00, 'online_banking', 'paid', 'TXN-20261002-0002', '2026-10-02 14:30:00'),
(3, 3, 240.00, 'ewallet', 'paid', 'TXN-20261003-0003', '2026-10-03 09:05:00'),
(4, 4, 100.00, 'online_banking', 'pending', 'TXN-20261004-0004', NULL),
(5, 5, 80.00, 'card', 'refunded', 'TXN-20261004-0005', '2026-10-04 16:45:00'),
(6, 6, 120.00, 'ewallet', 'paid', 'TXN-20261005-0006', '2026-10-05 11:20:00'),
(7, 7, 150.00, 'card', 'paid', 'TXN-20261006-0007', '2026-10-06 19:40:00'),
(8, 8, 80.00, 'online_banking', 'paid', 'TXN-20261015-0008', '2026-10-16 04:23:41'),
(9, 9, 25.00, 'card', 'paid', 'TXN-20261016-0009', '2026-10-16 19:14:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','organiser','customer') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password_hash`, `phone`, `role`, `created_at`) VALUES
(1, 'Aisyah Rahman', 'admin@eventhub.my', '$2b$10$TSPAb5uLRvDB3USHAoScyuIG3veGglRDMIk5YJ/9Bktqc27/l8.2G', '012-3456789', 'admin', '2026-10-15 18:06:36'),
(2, 'Daniel Lim', 'daniel@eventhub.my', '$2b$10$TSPAb5uLRvDB3USHAoScyuIG3veGglRDMIk5YJ/9Bktqc27/l8.2G', '013-1112233', 'organiser', '2026-10-15 18:06:36'),
(3, 'Nurul Huda', 'nurul@eventhub.my', '$2b$10$TSPAb5uLRvDB3USHAoScyuIG3veGglRDMIk5YJ/9Bktqc27/l8.2G', '014-5556677', 'organiser', '2026-10-15 18:06:36'),
(4, 'Farid Hakim', 'farid@example.com', '$2b$10$TSPAb5uLRvDB3USHAoScyuIG3veGglRDMIk5YJ/9Bktqc27/l8.2G', '011-2345678', 'customer', '2026-10-15 18:06:36'),
(5, 'Tan Mei Ling', 'meiling@example.com', '$2b$10$TSPAb5uLRvDB3USHAoScyuIG3veGglRDMIk5YJ/9Bktqc27/l8.2G', '016-7778899', 'customer', '2026-10-15 18:06:36'),
(6, 'Arun Kumar', 'arun@example.com', '$2b$10$TSPAb5uLRvDB3USHAoScyuIG3veGglRDMIk5YJ/9Bktqc27/l8.2G', '017-3334455', 'customer', '2026-10-15 18:06:36'),
(7, 'Siti Aminah', 'siti@example.com', '$2b$10$TSPAb5uLRvDB3USHAoScyuIG3veGglRDMIk5YJ/9Bktqc27/l8.2G', '019-8889900', 'customer', '2026-10-15 18:06:36'),
(8, 'alya', 'alya@gmail.com', '$2y$10$9HsDoP3tfwRB.VFo/v3ToeS9su6q3jZG0v6pNwtxwmtjThcS6a7su', NULL, 'customer', '2026-10-15 19:04:34');

-- --------------------------------------------------------

--
-- Table structure for table `venues`
--

CREATE TABLE `venues` (
  `venue_id` int(10) UNSIGNED NOT NULL,
  `venue_name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(60) NOT NULL,
  `capacity` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `venues`
--

INSERT INTO `venues` (`venue_id`, `venue_name`, `address`, `city`, `capacity`, `created_at`) VALUES
(1, 'Arena Seri Nusantara', '1 Jalan Stadium', 'Kuala Lumpur', 15000, '2026-10-15 18:06:36'),
(2, 'Pusat Konvensyen Harmoni', '88 Jalan Persidangan', 'Kuala Lumpur', 3000, '2026-10-15 18:06:36'),
(3, 'Kompleks Sukan Bestari', '12 Jalan Sukan', 'Shah Alam', 2500, '2026-10-15 18:06:36'),
(4, 'Panggung Budaya Warisan', '5 Jalan Seni', 'George Town', 800, '2026-10-15 18:06:36'),
(5, 'Dewan Komuniti Gemilang', '23 Lorong Gemilang', 'Johor Bahru', 400, '2026-10-15 18:06:36'),
(6, 'CityMark Hall', 'Jalan Pandan', 'Kuala Lumpur', 500, '2026-10-15 22:06:59'),
(7, 'Jaya Wedding Hall', 'Simpang Jaya', 'Perak', 1000, '2026-10-16 11:10:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `idx_bookings_user` (`user_id`),
  ADD KEY `idx_bookings_event` (`event_id`),
  ADD KEY `idx_bookings_status` (`status`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `uq_categories_name` (`category_name`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD UNIQUE KEY `uq_events_venue_slot` (`venue_id`,`event_date`,`start_time`),
  ADD KEY `fk_events_category` (`category_id`),
  ADD KEY `fk_events_organiser` (`organiser_id`),
  ADD KEY `idx_events_status` (`status`),
  ADD KEY `idx_events_date` (`event_date`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `uq_payments_booking` (`booking_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `uq_users_email` (`email`);

--
-- Indexes for table `venues`
--
ALTER TABLE `venues`
  ADD PRIMARY KEY (`venue_id`),
  ADD UNIQUE KEY `uq_venues_name_city` (`venue_name`,`city`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `venues`
--
ALTER TABLE `venues`
  MODIFY `venue_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_events_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_events_organiser` FOREIGN KEY (`organiser_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_events_venue` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`venue_id`) ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
