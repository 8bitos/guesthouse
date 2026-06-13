-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2026 at 08:16 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.5.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `guesthouse`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_no` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `guest_name` varchar(255) NOT NULL,
  `guest_email` varchar(255) NOT NULL,
  `guest_phone` varchar(255) NOT NULL,
  `guest_country` varchar(255) NOT NULL,
  `special_requests` text DEFAULT NULL,
  `addons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`addons`)),
  `include_breakfast` tinyint(1) NOT NULL DEFAULT 0,
  `include_extra_bed` tinyint(1) NOT NULL DEFAULT 0,
  `late_checkout` tinyint(1) NOT NULL DEFAULT 0,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `nights` int(11) NOT NULL,
  `guests` int(11) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(12,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL DEFAULT 'Transfer Bank',
  `payment_proof` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `invoice_no`, `user_id`, `room_id`, `guest_name`, `guest_email`, `guest_phone`, `guest_country`, `special_requests`, `addons`, `include_breakfast`, `include_extra_bed`, `late_checkout`, `check_in`, `check_out`, `nights`, `guests`, `subtotal`, `discount`, `tax`, `total_price`, `payment_method`, `payment_proof`, `status`, `created_at`, `updated_at`, `parent_id`) VALUES
(1, 'BGH-202606-5206', 2, 3, 'Yoga', 'yogatriana423@gmail.com', '+6281236280027', 'Indonesia', NULL, '[]', 0, 0, 0, '2026-06-13', '2026-06-16', 3, 2, 1050000.00, 0.00, 105000.00, 1155000.00, 'Transfer Bank', 'proofs/bFLaRUo27xvUyM0iEWy3Bb0av1DJi8ywMT4YhBGF.jpg', 'confirmed', '2026-06-12 22:05:27', '2026-06-12 22:11:50', NULL),
(2, 'BGH-202606-1792', 2, 7, 'Yoga', 'yogatriana423@gmail.com', '+6281236280027', 'Indonesia', NULL, '[]', 0, 0, 0, '2026-06-13', '2026-06-15', 2, 2, 1100000.00, 0.00, 110000.00, 1210000.00, 'Transfer Bank', 'proofs/FyTRMLoEJzFn7DOWjrL5QlhZKMa7xT8LYKd887hk.jpg', 'pending', '2026-06-12 22:11:09', '2026-06-12 22:11:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `resolution` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `user_id`, `booking_id`, `subject`, `description`, `status`, `resolution`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, 'Broken AC', 'Tolong Segera', 'pending', NULL, '2026-06-12 08:55:25', '2026-06-12 08:55:25');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `icon` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `icon`, `title`, `description`, `created_at`, `updated_at`) VALUES
(1, 'wifi', 'Wifi', 'Fast and reliable internet access', '2026-06-12 10:07:04', '2026-06-12 20:51:12'),
(2, 'ac_unit', 'AC', 'Comfortable air conditioned rooms', '2026-06-12 20:51:53', '2026-06-12 20:51:53'),
(3, 'tv', 'TV', 'In room entertainment television', '2026-06-12 20:52:28', '2026-06-12 20:52:28'),
(4, 'pool', 'Swimming Pool', 'Refreshing outdoor swimming pool', '2026-06-12 20:52:58', '2026-06-12 20:52:58'),
(5, 'restaurant', 'Shared Kitchen', 'Shared kitchen for guest convenience', '2026-06-12 20:53:21', '2026-06-12 20:53:21'),
(6, 'local_parking', 'Parking', 'Free parking for all guests', '2026-06-12 20:53:44', '2026-06-12 20:53:44');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `galleries`
--

CREATE TABLE `galleries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `image` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `order_index` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `galleries`
--

INSERT INTO `galleries` (`id`, `image`, `caption`, `order_index`, `created_at`, `updated_at`) VALUES
(3, 'gallery/cu3qPJCb3nd2LfvJEXnHUUIGWc3byxyBVsrpj5JP.jpg', NULL, 0, '2026-06-12 21:35:55', '2026-06-12 21:35:55'),
(4, 'gallery/uN3kkvVkhcIwgyb9nbSuVj07s7EGNTof2lJgzfTO.jpg', NULL, 0, '2026-06-12 21:36:41', '2026-06-12 21:36:41'),
(7, 'gallery/kWvelPGPKHZcQZcWPJJBZiYQ4y8tOEcrwBsyvA3p.jpg', NULL, 0, '2026-06-12 21:41:29', '2026-06-12 21:41:29'),
(8, 'gallery/uTpdPsiPi6VzZ3wuNEUy6jCGWsfZIulzGjME2rAN.jpg', NULL, 1, '2026-06-12 21:43:19', '2026-06-12 21:43:19'),
(9, 'gallery/dmgnfzAnAM58894tHBEPayrqX88dtWLOaDEkNial.jpg', NULL, 1, '2026-06-12 21:44:40', '2026-06-12 21:44:40'),
(10, 'gallery/c3kTixlGMJzObm9Z0PbKbubb1uLpWnIEK694hNWt.jpg', NULL, 2, '2026-06-12 21:47:09', '2026-06-12 21:47:09'),
(11, 'gallery/OcZm0EZoV0y4tIb8dDIsGuguydOOOXwtlyEgloOa.jpg', NULL, 2, '2026-06-12 21:47:25', '2026-06-12 21:47:25'),
(12, 'gallery/ZHMDySsItOREY0edie52JCg3nCEtxQwLsZYR6mJp.jpg', NULL, 3, '2026-06-12 21:48:29', '2026-06-12 21:48:29');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_06_05_040651_add_details_to_users_table', 1),
(5, '2026_06_05_041740_create_rooms_table', 1),
(6, '2026_06_05_043105_create_cms_tables', 1),
(7, '2026_06_05_061246_create_bookings_table', 1),
(8, '2026_06_06_034505_add_addons_to_bookings_table', 1),
(9, '2026_06_06_035428_add_addon_options_to_rooms_table', 1),
(10, '2026_06_06_050905_create_complaints_table', 1),
(11, '2026_06_10_013523_rename_adults_to_guests_in_bookings_table', 1),
(12, '2026_06_10_083219_add_parent_id_to_bookings_table', 1),
(13, '2026_06_10_084853_remove_unique_from_invoice_no_in_bookings_table', 1),
(14, '2026_06_11_165352_add_size_and_addons_to_rooms_table', 1),
(15, '2026_06_11_165449_add_addons_to_bookings_table', 1),
(16, '2026_06_11_171414_change_size_to_string_in_rooms_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `capacity` int(11) NOT NULL,
  `size` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'tersedia',
  `image` varchar(255) DEFAULT NULL,
  `addons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`addons`)),
  `allow_breakfast` tinyint(1) NOT NULL DEFAULT 1,
  `allow_extra_bed` tinyint(1) NOT NULL DEFAULT 1,
  `allow_late_checkout` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `type`, `price`, `capacity`, `size`, `description`, `status`, `image`, `addons`, `allow_breakfast`, `allow_extra_bed`, `allow_late_checkout`, `created_at`, `updated_at`) VALUES
(3, 'Budget 2nd', 'Budget Double Room', 350000.00, 2, '30', '(2nd floor) The spacious double room features a private bathroom, air conditioning, a minibar, as well as a seating area with a flat-screen TV. This double room has a tea and coffee maker, a wardrobe, a TV and an outdoor dining area. The unit offers 1 bed.', 'tersedia', 'rooms/EgchCwCqAiTIgXUkKZk73zout5anZR8MGO19JWyi.jpg', '[{\"name\":\"Breakfast\",\"price\":\"50000\",\"type\":\"per_guest_per_night\",\"description\":\"Enable breakfast addon\"},{\"name\":\"Late Check-out\",\"price\":\"100000\",\"type\":\"flat_fee\",\"description\":\"Enable late check-out\"}]', 1, 0, 1, '2026-06-12 09:59:04', '2026-06-12 21:49:22'),
(5, 'Standard 2nd', 'Standard Double Room', 450000.00, 2, '35', '(2nd floor) The spacious double room features a private bathroom, air conditioning, a minibar, as well as a seating area with a flat-screen TV. The double room provides a tea and coffee maker, a wardrobe, a safe deposit box, a TV, as well as city views. The unit offers 1 bed.', 'tersedia', 'rooms/NmIZCUbgSbt36OJIEUIr62J2xbAGMiV6ZXUHUpTP.jpg', '[{\"name\":\"Breakfast\",\"price\":\"50000\",\"type\":\"per_guest_per_night\",\"description\":\"Enable breakfast addon\"},{\"name\":\"Late Check-out\",\"price\":\"100000\",\"type\":\"flat_fee\",\"description\":\"Enable late check-out\"}]', 1, 0, 1, '2026-06-12 10:03:20', '2026-06-12 21:49:44'),
(7, 'Deluxe 2nd', 'Deluxe Double Room', 550000.00, 2, '40', '(2nd floor) The spacious double room features a private bathroom, air conditioning, a minibar, as well as a seating area with a flat-screen TV. The double room provides a tea and coffee maker, a wardrobe, a safe deposit box, a TV, as well as city views. The unit offers 1 bed.', 'tersedia', 'rooms/KmLesTStD7fuxOK8QL3z2ufHNvM5idlnvxL1JaIf.jpg', '[{\"name\":\"Breakfast\",\"price\":\"50000\",\"type\":\"per_guest_per_night\",\"description\":\"Enable breakfast addon\"},{\"name\":\"Late Check-out\",\"price\":\"100000\",\"type\":\"flat_fee\",\"description\":\"Enable late check-out\"}]', 1, 0, 1, '2026-06-12 21:06:21', '2026-06-12 21:49:56'),
(9, 'Superior 2nd', 'Superior King Room', 650000.00, 2, '45', '(2nd floor) The spacious double room features air conditioning, a minibar, a balcony with city views as well as a private bathroom boasting a walk-in shower. The unit has 1 bed.', 'tersedia', 'rooms/U75DZhulZuY8OJRexwp3F1jwDLqpManzb6iOarZc.jpg', '[{\"name\":\"Breakfast\",\"price\":\"50000\",\"type\":\"per_guest_per_night\",\"description\":\"Enable breakfast addon\"},{\"name\":\"Late Check-out\",\"price\":\"100000\",\"type\":\"flat_fee\",\"description\":\"Enable late check-out\"}]', 1, 0, 1, '2026-06-12 21:09:09', '2026-06-12 21:50:13'),
(11, 'Budget 3rd', 'Budget Double Room', 350000.00, 2, '30', '(3rd floor) The spacious double room features a private bathroom, air conditioning, a minibar, as well as a seating area with a flat-screen TV. This double room has a tea and coffee maker, a wardrobe, a TV and an outdoor dining area. The unit offers 1 bed.', 'tersedia', 'rooms/2crTDxeY063sAaMX9Z7ajrkbFzh6mNti2lmxOevb.jpg', '[{\"name\":\"Breakfast\",\"price\":\"50000\",\"type\":\"per_guest_per_night\",\"description\":\"Enable breakfast addon\"},{\"name\":\"Late Check-out\",\"price\":\"100000\",\"type\":\"flat_fee\",\"description\":\"Enable late check-out\"}]', 1, 0, 1, '2026-06-12 21:30:49', '2026-06-12 21:50:31'),
(12, 'Standard 3rd', 'Standard Double Room', 450000.00, 2, '35', '(3rd floor) The spacious double room features a private bathroom, air conditioning, a minibar, as well as a seating area with a flat-screen TV. The double room provides a tea and coffee maker, a wardrobe, a safe deposit box, a TV, as well as city views. The unit offers 1 bed.', 'tersedia', 'rooms/ZV17yTys7qft4FacSacZsGedtDyCWJq3FebiaFJj.jpg', '[{\"name\":\"Breakfast\",\"price\":\"50000\",\"type\":\"per_guest_per_night\",\"description\":\"Enable breakfast addon\"},{\"name\":\"Late Check-out\",\"price\":\"100000\",\"type\":\"flat_fee\",\"description\":\"Enable late check-out\"}]', 1, 0, 1, '2026-06-12 21:33:12', '2026-06-12 21:50:47'),
(13, 'Deluxe 3rd', 'Deluxe Double Room', 550000.00, 2, '40', '(3rd floor) The spacious double room features a private bathroom, air conditioning, a minibar, as well as a seating area with a flat-screen TV. The double room provides a tea and coffee maker, a wardrobe, a safe deposit box, a TV, as well as city views. The unit offers 1 bed.', 'tersedia', 'rooms/lyUGpdDwQhvW8cffOKfAGC98oDQXziLuRYfjke3M.jpg', '[{\"name\":\"Breakfast\",\"price\":\"50000\",\"type\":\"per_guest_per_night\",\"description\":\"Enable breakfast addon\"},{\"name\":\"Late Check-out\",\"price\":\"100000\",\"type\":\"flat_fee\",\"description\":\"Enable late check-out\"}]', 1, 0, 1, '2026-06-12 21:33:55', '2026-06-12 21:50:58'),
(14, 'Superior 3rd', 'Superior King Room', 650000.00, 2, '45', '(3rd floor) The spacious double room features air conditioning, a minibar, a balcony with city views as well as a private bathroom boasting a walk-in shower. The unit has 1 bed.', 'tersedia', 'rooms/EpObFaGik9rfWdF6cd65ek1oS69IPMcYywK2GTsL.jpg', '[{\"name\":\"Breakfast\",\"price\":\"50000\",\"type\":\"per_guest_per_night\",\"description\":\"Enable breakfast addon\"},{\"name\":\"Late Check-out\",\"price\":\"100000\",\"type\":\"flat_fee\",\"description\":\"Enable late check-out\"}]', 1, 0, 1, '2026-06-12 21:34:53', '2026-06-12 21:51:11');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('if2t2aJFVNIENfbO6LjWA9qdauTRkFn8zz5RTom3', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'eyJfdG9rZW4iOiJ2SnFTejA3YWNDN2ZSdkV5UFppTlJxQURYMEdSVGFXTTNXc2FKSkVRIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAiLCJyb3V0ZSI6ImhvbWUifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjF9', 1781331275);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`) VALUES
('about_desc', 'Experience comfortable hospitality in the heart of Kuta. Bagus Guest House offers modern facilities, cozy accommodations, and friendly service, providing the perfect place to relax while exploring Bali.', '2026-06-12 10:05:40', '2026-06-12 10:19:22'),
('about_title', 'About Us', '2026-06-12 10:05:40', '2026-06-12 10:05:40'),
('about_vision', 'To become a trusted guest house in Kuta, providing comfortable accommodations, excellent service, and memorable stay experiences for every guest.', '2026-06-12 10:05:40', '2026-06-12 21:59:40'),
('about_why_list', 'Comfortable and affordable accommodations\r\nprofessional and friendly staff\r\nStrategic location near Kuta attractions\r\nComplete facilities for a pleasant stay', '2026-06-12 10:05:40', '2026-06-12 21:59:40'),
('hero_image', 'hero/uYGtvH4IV0cSCbrRR4FjVkByVdb73oMpetzzZRLF.jpg', '2026-06-12 10:05:40', '2026-06-12 10:05:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'pelanggan',
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `address`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin Bagus', 'bagusguesthouse01@gmail.com', NULL, '$2y$12$sfxniIxCx24dmNnWvzUF.eRy8PzxpzDCaGXZD65ZhLM24hKjsCYDa', 'admin', '+6282169911168', 'Kuta, Bali', NULL, '2026-06-12 08:34:47', '2026-06-12 21:53:54'),
(2, 'Yoga', 'yogatriana423@gmail.com', NULL, '$2y$12$RrE3ijWyqWX3eySVPG4f..7duMd6lmbJFT63zf33YM8VRAeL7oqUG', 'pelanggan', '+6281236280027', 'Indonesia', NULL, '2026-06-12 08:36:03', '2026-06-12 22:10:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_user_id_foreign` (`user_id`),
  ADD KEY `bookings_room_id_foreign` (`room_id`),
  ADD KEY `bookings_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complaints_user_id_foreign` (`user_id`),
  ADD KEY `complaints_booking_id_foreign` (`booking_id`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `galleries`
--
ALTER TABLE `galleries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `galleries`
--
ALTER TABLE `galleries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `complaints_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
