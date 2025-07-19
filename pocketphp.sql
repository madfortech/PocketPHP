-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2025 at 03:46 PM
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
-- Database: `pocketphp`
--

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resets`
--

CREATE TABLE `resets` (
  `id` int(255) NOT NULL,
  `reset_token` varchar(255) NOT NULL,
  `reset_token_expires_at` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_used` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resets`
--

INSERT INTO `resets` (`id`, `reset_token`, `reset_token_expires_at`, `email`, `is_used`, `created_at`, `updated_at`) VALUES
(1, '246c722e0cdd57cfc16a25761fefa0e3e4d8d708192907c7508de3f6d6d7fdc5', '2025-07-17 13:46:47', 'mylapul@mailinator.com', 0, '2025-07-17 10:46:47', '2025-07-17 10:46:47');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email_verified_at` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `email_verified_at`, `created_at`, `updated_at`) VALUES
(1, 'Ira Wooten', 'mylapul@mailinator.com', '$2y$10$AhefxwguItFSc0mDd0s/8u3j0B74N01K37OckxMj8OlMukCibUhd.', '', '2025-07-17 10:09:46', '2025-07-17 10:09:46'),
(2, 'Emery Salas', 'wodela@mailinator.com', '$2y$10$iBon7kpdg90QtrKXovDtLO4CF2LS/KtQS2r1TKg3TxwQfGLtePP5u', '', '2025-07-17 11:07:47', '2025-07-17 11:07:47'),
(3, 'Drake Parks', 'nekeca@mailinator.com', '$2y$10$QtZszqSSq0AFgx2ezBTCuuexskZS8dx32dk9U151w1.61HTcNwaD2', '2025-07-17 16:43:34', '2025-07-17 11:11:43', '2025-07-17 11:11:43'),
(4, 'Marny Hart', 'wyheqica@mailinator.com', '$2y$10$A7fG/BZqcfo8pBD/6I0.NeKTqQt1mF08Kom76.2RBUT4ybZCKUptO', '', '2025-07-17 11:14:44', '2025-07-17 11:14:44'),
(5, 'Phelan Wood', 'tikikanar@mailinator.com', '$2y$10$GtYNDtYsmC6fO7jeW6H9FeJ3N6XmfN8cp9IxDReSvSDcO1esWHNZa', '', '2025-07-18 10:54:31', '2025-07-18 10:54:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resets`
--
ALTER TABLE `resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `resets`
--
ALTER TABLE `resets`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
