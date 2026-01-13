-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 13, 2026 at 11:24 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bloodbridge_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `donors`
--

CREATE TABLE `donors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `governorate_id` bigint(20) UNSIGNED DEFAULT NULL,
  `national_id` varchar(9) NOT NULL COMMENT 'National/ID number',
  `gender` enum('male','female') NOT NULL,
  `birth_date` date DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL COMMENT 'Latitude for location',
  `lng` decimal(10,7) DEFAULT NULL COMMENT 'Longitude for location',
  `points` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Loyalty/reward points',
  `level` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Donor level/tier',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donors`
--

INSERT INTO `donors` (`id`, `user_id`, `governorate_id`, `national_id`, `gender`, `birth_date`, `lat`, `lng`, `points`, `level`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 7, 5, '100000001', 'male', '1987-01-13', 31.5460000, 34.4780000, 0, 1, NULL, '2026-01-13 07:59:30', '2026-01-13 07:59:30'),
(2, 8, 4, '100000002', 'female', '1992-01-13', 31.5770000, 34.5000000, 0, 1, NULL, '2026-01-13 07:59:31', '2026-01-13 07:59:31'),
(3, 9, 2, '100000003', 'male', '1989-01-13', 31.5640000, 34.4950000, 0, 1, NULL, '2026-01-13 07:59:31', '2026-01-13 07:59:31'),
(4, 10, 1, '100000004', 'female', '1999-01-13', 31.5490000, 34.4970000, 0, 1, NULL, '2026-01-13 07:59:31', '2026-01-13 07:59:31'),
(5, 11, 4, '100000005', 'male', '1998-01-13', 31.5450000, 34.4120000, 0, 1, NULL, '2026-01-13 07:59:32', '2026-01-13 07:59:32'),
(6, 12, 2, '100000006', 'female', '2005-01-13', 31.5910000, 34.4190000, 0, 1, NULL, '2026-01-13 07:59:32', '2026-01-13 07:59:32'),
(7, 13, 5, '100000007', 'male', '1988-01-13', 31.5120000, 34.4200000, 0, 1, NULL, '2026-01-13 07:59:32', '2026-01-13 07:59:32'),
(8, 14, 3, '100000008', 'male', '1993-01-13', 31.5660000, 34.4470000, 0, 1, NULL, '2026-01-13 07:59:32', '2026-01-13 07:59:32'),
(9, 15, 2, '100000009', 'male', '1991-01-13', 31.5620000, 34.4230000, 0, 1, NULL, '2026-01-13 07:59:33', '2026-01-13 07:59:33'),
(10, 16, 3, '100000010', 'female', '1991-01-13', 31.5790000, 34.4410000, 0, 1, NULL, '2026-01-13 07:59:33', '2026-01-13 07:59:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `donors`
--
ALTER TABLE `donors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `donors_national_id_unique` (`national_id`),
  ADD KEY `donors_governorate_id_foreign` (`governorate_id`),
  ADD KEY `donors_user_id_national_id_index` (`user_id`,`national_id`),
  ADD KEY `donors_gender_index` (`gender`),
  ADD KEY `donors_birth_date_index` (`birth_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `donors`
--
ALTER TABLE `donors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `donors`
--
ALTER TABLE `donors`
  ADD CONSTRAINT `donors_governorate_id_foreign` FOREIGN KEY (`governorate_id`) REFERENCES `governorates` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `donors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
