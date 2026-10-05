-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 05, 2026 at 02:40 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ticketsystemdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `priority` enum('low','medium','high') COLLATE utf8mb4_unicode_ci DEFAULT 'medium',
  `status` enum('pending','in_progress','completed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `title`, `description`, `priority`, `status`, `due_date`, `created_at`, `updated_at`) VALUES
(3, 1, 'Build auth module', 'Login, logout, and session handling.', 'high', 'completed', '2026-09-12', '2026-10-05 14:17:28', '2026-10-05 14:17:28'),
(4, 1, 'Create task model', 'CRUD operations with ownership guards.', 'medium', 'completed', '2026-09-18', '2026-10-05 14:17:28', '2026-10-05 14:17:28'),
(6, 1, 'Style the UI', 'CSS design system, responsive layout.', 'low', 'completed', '2026-09-30', '2026-10-05 14:17:28', '2026-10-05 14:17:28'),
(7, 1, 'Write unit tests', 'PHPUnit tests for models and auth.', 'high', 'pending', '2026-10-20', '2026-10-05 14:17:28', '2026-10-05 14:17:28'),
(8, 1, 'Add input validation', 'Server-side validation for all POST handlers.', 'medium', 'pending', '2026-10-22', '2026-10-05 14:17:28', '2026-10-05 14:17:28'),
(9, 1, 'Security audit', 'Review prepared statements and XSS escaping.', 'high', 'pending', '2026-10-28', '2026-10-05 14:17:28', '2026-10-05 14:17:28'),
(10, 1, 'Deploy to staging', 'Push to staging server and run smoke tests.', 'low', 'pending', '2026-11-01', '2026-10-05 14:17:28', '2026-10-05 14:17:28'),
(11, 2, 'Review requirements', 'Read and annotate the project specification.', 'medium', 'completed', '2026-09-10', '2026-10-05 14:17:28', '2026-10-05 14:17:28'),
(12, 2, 'Create wireframes', 'Sketch UI flow for login and dashboard.', 'medium', 'completed', '2026-09-15', '2026-10-05 14:17:28', '2026-10-05 14:17:28'),
(13, 2, 'Set up local environment', 'Configure Laragon, PHP, and MySQL.', 'high', 'completed', '2026-09-18', '2026-10-05 14:17:28', '2026-10-05 14:17:28'),
(14, 2, 'Implement task list', 'CRUD operations for tasks.', 'high', 'completed', '2026-09-28', '2026-10-05 14:17:28', '2026-10-05 14:17:28'),
(15, 2, 'Add filtering feature', 'Filter tasks by status and priority.', 'medium', 'pending', '2026-10-15', '2026-10-05 14:17:28', '2026-10-05 14:17:28'),
(16, 2, 'Write API documentation', 'Document all endpoints and expected responses.', 'low', 'pending', '2026-10-25', '2026-10-05 14:17:28', '2026-10-05 14:17:28'),
(18, 1, 'gjgjjj222', 'jjjjjjjjjjjjjjjjjjjjjjjjgh22222', 'low', 'completed', '2026-10-05', '2026-10-05 14:35:57', '2026-10-05 14:36:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'john', 'admin1@example.com', '$2y$10$0QYHm9XmKuFILB0O/T0BdeSOet7e5QeOyd5wAmvv9x/FOeZ9rth4a', '2026-10-05 14:17:28'),
(2, 'Admin Two', 'admin2@example.com', '$2y$10$0QYHm9XmKuFILB0O/T0BdeSOet7e5QeOyd5wAmvv9x/FOeZ9rth4a', '2026-10-05 14:17:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tasks_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_tasks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
