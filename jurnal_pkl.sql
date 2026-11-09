-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2026 at 03:28 AM
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
-- Database: `jurnal_pkl`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('hadir','izin','sakit','alpha') NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `date`, `status`, `note`, `created_at`) VALUES
(1, 1, '2026-11-07', 'hadir', 'Hadirrr!', '2026-11-07 02:09:12'),
(2, 2, '2026-11-07', 'hadir', 'Hadir!!', '2026-11-07 02:09:30'),
(3, 3, '2026-11-07', 'hadir', 'Hadirrr!', '2026-11-07 05:57:06'),
(4, 3, '2026-11-09', 'hadir', 'Hadirrr!', '2026-11-09 01:36:08'),
(5, 4, '2026-11-09', 'hadir', 'Hadirrr!', '2026-11-09 01:40:58'),
(6, 2, '2026-11-09', 'hadir', 'Hadirrr!', '2026-11-09 01:41:18'),
(7, 5, '2026-11-09', 'hadir', 'Hadirrr!', '2026-11-09 01:41:37'),
(8, 1, '2026-11-09', 'hadir', 'Hadir!!', '2026-11-09 01:41:54'),
(9, 6, '2026-11-09', 'hadir', 'Hadirrr!', '2026-11-09 02:03:52');

-- --------------------------------------------------------

--
-- Table structure for table `journals`
--

CREATE TABLE `journals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text DEFAULT NULL,
  `activity_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journals`
--

INSERT INTO `journals` (`id`, `user_id`, `title`, `content`, `activity_date`, `created_at`) VALUES
(3, 1, 'Pengecekan Server', 'adalahapokoknya', '2026-11-09', '2026-11-09 01:57:23');

-- --------------------------------------------------------

--
-- Table structure for table `journal_photos`
--

CREATE TABLE `journal_photos` (
  `id` int(11) NOT NULL,
  `journal_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journal_photos`
--

INSERT INTO `journal_photos` (`id`, `journal_id`, `filename`) VALUES
(13, 3, 'jurnal_3_1794189443_0.png'),
(14, 3, 'jurnal_3_1794189444_1.jpg'),
(15, 3, 'jurnal_3_1794189444_2.jpg'),
(16, 3, 'jurnal_3_1794189444_3.jpg'),
(17, 3, 'jurnal_3_1794189444_4.jpg'),
(18, 3, 'jurnal_3_1794189444_5.jpg'),
(19, 3, 'jurnal_3_1794189444_6.jpg'),
(20, 3, 'jurnal_3_1794189444_7.jpg'),
(21, 3, 'jurnal_3_1794189444_8.jpg'),
(22, 3, 'jurnal_3_1794189444_9.jpg'),
(23, 3, 'jurnal_3_1794189444_10.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `content`, `created_at`) VALUES
(1, 2, 'haiii', '2026-11-07 01:53:20'),
(2, 2, 'haii', '2026-11-07 01:53:33'),
(3, 1, 'haiih', '2026-11-07 01:53:54'),
(4, 1, 'asyesyehh', '2026-11-07 05:51:11'),
(5, 3, 'JOSJISSSSSSS', '2026-11-07 05:55:15'),
(6, 1, 'oyy', '2026-11-09 01:58:02'),
(7, 1, 'asyesyehh', '2026-11-09 01:58:09'),
(8, 3, 'hihhu', '2026-11-09 01:58:36'),
(9, 4, 'gredaggreduGG', '2026-11-09 01:59:08'),
(10, 5, 'ekhemm', '2026-11-09 01:59:57'),
(11, 1, 'wallawwee', '2026-11-09 02:15:05');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `teacher_name` varchar(100) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `status` enum('todo','doing','done') DEFAULT 'todo',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `description`, `teacher_name`, `deadline`, `status`, `created_by`, `created_at`) VALUES
(2, 'input data barang', 'ss', 'Fauzi', '2026-11-06', 'done', 1, '2026-11-07 05:55:56'),
(3, 'Membuat Dokumentasi File', 'Kerjakan Anak-Anakk', 'Pak fauzi', '2026-11-09', 'done', 6, '2026-11-09 02:03:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('member','teacher') DEFAULT 'member',
  `photo` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `jurusan` varchar(100) DEFAULT NULL,
  `sekolah` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `photo`, `bio`, `jurusan`, `sekolah`, `created_at`) VALUES
(1, 'Farrel Imaduddin Al Ashari', 'farrel', '$2y$10$FMQRdH0ecCbZZVWtN2n7/u1YZN/gr7X98Er4NG4sqDAWCXlZwmB6S', 'member', 'user_1_1794017614.jpg', '#EPEPDIHATISAMPAIMATI', 'RPL', 'SMKS ALMAMUN SUMEDANG', '2026-11-04 06:19:21'),
(2, 'Zahran Fauzan ZamZami', 'zahran', '$2y$10$FMQRdH0ecCbZZVWtN2n7/u1YZN/gr7X98Er4NG4sqDAWCXlZwmB6S', 'member', 'user_2_1794190331.jpg', 'Adalahpokoknya', 'RPL', 'SMKS AL MAMUN SUMEDANG', '2026-11-04 06:19:21'),
(3, 'Saif Paqihul Kamal', 'saif', '$2y$10$FMQRdH0ecCbZZVWtN2n7/u1YZN/gr7X98Er4NG4sqDAWCXlZwmB6S', 'member', 'user_3_1794190359.jpg', 'PaqihGanteng', 'RPL', 'SMKS AL MAMUN SUMEDANG', '2026-11-04 06:19:21'),
(4, 'Lupi Maulana', 'lupi', '$2y$10$FMQRdH0ecCbZZVWtN2n7/u1YZN/gr7X98Er4NG4sqDAWCXlZwmB6S', 'member', 'user_4_1794190390.jpg', 'GredaGGreduG Is Priority', 'RPL', 'SMKS AL MAMUN SUMEDANG', '2026-11-04 06:19:21'),
(5, 'Hamzah Alafi Iskandar', 'hamzah', '$2y$10$FMQRdH0ecCbZZVWtN2n7/u1YZN/gr7X98Er4NG4sqDAWCXlZwmB6S', 'member', 'user_5_1794018069.jpg', 'PencintaSholawatAbizz', 'RPL', 'SMKS AL MAMUN SUMEDANG', '2026-11-04 06:19:21'),
(6, 'Fauzi Abdul Aziz Basyri', 'guru', '$2y$10$FMQRdH0ecCbZZVWtN2n7/u1YZN/gr7X98Er4NG4sqDAWCXlZwmB6S', 'teacher', 'user_6_1794018772.jpg', 'Asyesyehh', '-', 'SMKS ALMAMUN SUMEDANG', '2026-11-04 06:19:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_date` (`user_id`,`date`);

--
-- Indexes for table `journals`
--
ALTER TABLE `journals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `journal_photos`
--
ALTER TABLE `journal_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_id` (`journal_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

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
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `journals`
--
ALTER TABLE `journals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `journal_photos`
--
ALTER TABLE `journal_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journals`
--
ALTER TABLE `journals`
  ADD CONSTRAINT `journals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journal_photos`
--
ALTER TABLE `journal_photos`
  ADD CONSTRAINT `journal_photos_ibfk_1` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
