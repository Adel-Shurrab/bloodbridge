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
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `governorate_id` bigint(20) UNSIGNED DEFAULT NULL,
  `org_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `license_number` varchar(255) NOT NULL,
  `license_document_path` varchar(255) DEFAULT NULL,
  `responsible_person_name` varchar(255) NOT NULL,
  `responsible_person_position` varchar(255) DEFAULT NULL,
  `responsible_person_email` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) NOT NULL,
  `contact_phone` varchar(255) NOT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `opening_time` time DEFAULT NULL,
  `closing_time` time DEFAULT NULL,
  `working_days` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`working_days`)),
  `daily_capacity` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `total_request_created` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_donation_verified` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `approval_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `organizations`
--

INSERT INTO `organizations` (`id`, `user_id`, `governorate_id`, `org_name`, `description`, `license_number`, `license_document_path`, `responsible_person_name`, `responsible_person_position`, `responsible_person_email`, `contact_email`, `contact_phone`, `lat`, `lng`, `street_address`, `opening_time`, `closing_time`, `working_days`, `daily_capacity`, `approved_at`, `rejection_reason`, `total_request_created`, `total_donation_verified`, `approval_status`, `approved_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'مستشفى الشفاء الطبي', 'المجمع الطبي الأكبر في قطاع غزة، يقدم خدمات طبية متكاملة للطوارئ والجراحة.', 'ORG-GAZA-001', NULL, 'د. محمد الأحمد', 'مدير المجمع', 'org1@example.com', 'org1@example.com', '0599000010', NULL, NULL, 'الشارع الرئيسي - غزة', NULL, NULL, '[\"Saturday\",\"Sunday\",\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"]', 200, NULL, NULL, 0, 0, 1, NULL, NULL, '2026-01-13 07:59:29', '2026-01-13 07:59:29'),
(2, 3, 2, 'مجمع ناصر الطبي', 'مركز طبي حيوي يخدم المنطقة الجنوبية، متخصص في أمراض القلب والجراحة العامة.', 'ORG-KHAN-002', NULL, 'د. خالد اليوسف', 'المدير الطبي', 'org2@example.com', 'org2@example.com', '0599000011', NULL, NULL, 'الشارع الرئيسي - خانيونس', '08:00:00', '20:00:00', '[\"Sunday\",\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\"]', 120, NULL, NULL, 0, 0, 1, NULL, NULL, '2026-01-13 07:59:29', '2026-01-13 07:59:29'),
(3, 4, 1, 'مركز صحي الرمال', 'مركز رعاية أولية يقدم خدمات التطعيم والعيادات الخارجية لسكان غزة.', 'ORG-REMAL-003', NULL, 'أ. سارة حسن', 'رئيسة المركز', 'org3@example.com', 'org3@example.com', '0599000012', NULL, NULL, 'الشارع الرئيسي - غزة', '07:30:00', '15:30:00', '[\"Sunday\",\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\"]', 80, NULL, NULL, 0, 0, 1, NULL, NULL, '2026-01-13 07:59:29', '2026-01-13 07:59:29'),
(4, 5, 4, 'جمعية أصدقاء المريض', 'مؤسسة خيرية طبية تهدف لتوفير رعاية صحية بأسعار رمزية للمواطنين.', 'ORG-DEIR-004', NULL, 'د. محمود سعيد', 'المدير التنفيذي', 'org4@example.com', 'org4@example.com', '0599000013', NULL, NULL, 'الشارع الرئيسي - دير البلح', NULL, NULL, '[\"Saturday\",\"Sunday\",\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"]', 150, NULL, NULL, 0, 0, 1, NULL, NULL, '2026-01-13 07:59:30', '2026-01-13 07:59:30'),
(5, 6, 5, 'مستشفى الهلال الإماراتي', 'مستشفى متخصص في رعاية الأمومة والطفولة في المنطقة الجنوبية.', 'ORG-RAFAH-005', NULL, 'د. منى العبد', 'مديرة المستشفى', 'org5@example.com', 'org5@example.com', '0599000014', NULL, NULL, 'الشارع الرئيسي - رفح', '08:00:00', '16:00:00', '[\"Saturday\",\"Sunday\",\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\"]', 100, NULL, NULL, 0, 0, 1, NULL, NULL, '2026-01-13 07:59:30', '2026-01-13 07:59:30'),
(6, 17, NULL, 'مؤسسة قيد الانتظار 0', NULL, 'PEND-000', NULL, 'فرد/مؤسسة معلقة 0', 'مدير', 'pending0@example.com', 'pending0@example.com', '0598771249', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 0, 0, NULL, NULL, '2026-01-13 07:59:34', '2026-01-13 07:59:34'),
(7, 18, NULL, 'مؤسسة قيد الانتظار 1', NULL, 'PEND-001', NULL, 'فرد/مؤسسة معلقة 1', 'مدير', 'pending1@example.com', 'pending1@example.com', '0598878835', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 0, 0, NULL, NULL, '2026-01-13 07:59:34', '2026-01-13 07:59:34'),
(8, 19, NULL, 'مؤسسة قيد الانتظار 2', NULL, 'PEND-002', NULL, 'فرد/مؤسسة معلقة 2', 'مدير', 'pending2@example.com', 'pending2@example.com', '0598565695', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 0, 0, NULL, NULL, '2026-01-13 07:59:34', '2026-01-13 07:59:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `organizations_license_number_unique` (`license_number`),
  ADD UNIQUE KEY `organizations_contact_email_unique` (`contact_email`),
  ADD UNIQUE KEY `organizations_contact_phone_unique` (`contact_phone`),
  ADD KEY `organizations_user_id_foreign` (`user_id`),
  ADD KEY `organizations_governorate_id_foreign` (`governorate_id`),
  ADD KEY `organizations_approved_by_foreign` (`approved_by`),
  ADD KEY `organizations_org_name_index` (`org_name`),
  ADD KEY `organizations_approval_status_index` (`approval_status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `organizations`
--
ALTER TABLE `organizations`
  ADD CONSTRAINT `organizations_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `organizations_governorate_id_foreign` FOREIGN KEY (`governorate_id`) REFERENCES `governorates` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `organizations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
