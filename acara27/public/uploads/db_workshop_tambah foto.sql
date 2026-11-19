-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 18, 2026 at 08:34 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_workshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id` int NOT NULL,
  `judul` varchar(150) NOT NULL,
  `penulis` varchar(100) NOT NULL,
  `tahun_terbit` year NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id`, `judul`, `penulis`, `tahun_terbit`, `created_at`) VALUES
(2, 'Laskar Pelangi', 'Andrea Hirata', 2019, '2026-10-21 04:40:10'),
(3, 'Cantik Itu Luka', 'Eka Kurniawan', 2022, '2026-10-21 04:40:10'),
(4, 'Perahu Kertas', 'Dee Lestari', 2018, '2026-10-21 04:40:10'),
(5, 'Bumi', 'Tere Liye', 2014, '2026-10-21 04:40:10'),
(6, 'Pulang', 'Tere Liye', 2015, '2026-10-21 04:40:10');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` int NOT NULL,
  `nim` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `prodi` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `nim`, `nama`, `prodi`, `created_at`) VALUES
(8, '2201001', 'Budi Santoso', 'Teknik Informatika', '2026-10-21 13:24:43'),
(9, '2201002', 'Andi Saputra', 'Teknik Informatika', '2026-10-21 13:24:43'),
(10, '2201003', 'Siti Nurhaliza', 'Teknik Informatika', '2026-10-21 13:24:43'),
(13, '240100301', 'Nindia Tri Anggraini Update', 'Teknik Informatika', '2026-10-22 15:01:04'),
(14, '23010', 'Mahasiswa Uji', 'Teknik Informatika', '2026-10-24 15:54:48'),
(16, '12345678', 'ubah', 'ubah', '2026-11-14 16:44:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `created_at`, `role`) VALUES
(1, 'Nindia Tri Anggraini', 'nindia@example.com', '$2y$10$BRVFKKg.gfnAFtVRHZ1HJ.wmQfujbr9xKLtldDhBYROQqMkuj6wZu', '2026-10-22 01:22:38', 'user'),
(4, 'Nindia Anggraini', 'nindiaanggraini@example.com', '$2y$10$BbBM9pGfWmfjslpUaH1KUO57TggzoKD068o1Kw1tGz9aS8tmQIQ5G', '2026-10-22 01:37:46', 'user'),
(7, 'nindi', 'nindi@example.com', '$2y$10$qXLH6PhsKhLBY7PHB0uJVuTsy3HbjkPpUc7RyFkHG7YWGa7oIVJyK', '2026-10-22 02:37:51', 'user'),
(8, 'Nindia Anggraini', 'nindia.baru@example.com', '$2y$10$UhpNlGdbasuB6C0qfaknh.zUbnxSraLdfVi9NpCqcchpepJ1q4bT.', '2026-10-22 03:00:39', 'user'),
(9, 'Nindia Anggraini', 'nindiaanggraini@gmail.com', '$2y$10$qJ4t1l4ocvlhkBNBSD4FLeecpvFeMVvL7mF0zot/asxgBKbfLuTfy', '2026-10-23 14:46:56', 'user'),
(11, 'Nindia Anggraini', 'nindia.valid@gmail.com', '$2y$10$zsqRgI9Zhj6v1s4.WaGm2./z7zcNhsU/DHNDjEsZCBvOHkf6esI06', '2026-10-23 15:25:34', 'user'),
(13, 'Nindia Anggraini', 'nindia.cantik@gmail.com', '$2y$10$8IzVeukw46i.JMTmIW4NiuM6hW0dmEwoYf/lJ1OVivUA.uH48xcU2', '2026-10-23 17:09:53', 'user'),
(14, 'Admin Workshop', 'admin@example.com', '$2y$10$oQF6q4vC3qCTkwcTJWSaluApQHJfslbUm7j.COSwL62IcyXWTadq2', '2026-11-14 16:25:39', 'admin'),
(15, 'User Workshop', 'user@example.com', '$2y$10$3QkWI4r1P4mEmPYEDnTInOMhHeb1zSjxb9GFWD/vV8XFGndpcjJku', '2026-11-14 16:25:39', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
