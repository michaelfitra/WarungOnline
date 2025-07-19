-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2025 at 10:18 AM
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
-- Database: `tokobarokah`
--

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `kategori` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama`, `deskripsi`, `harga`, `gambar`, `stok`, `kategori`) VALUES
(1, 'Mie Instan Rasa Ayam Bawang', 'Mie instan lezat dengan bumbu ayam bawang', 3500.00, 'mie_ayam_bawang.jpg', 100, 'Makanan'),
(2, 'Kopi Hitam Bubuk', 'Kopi hitam murni tanpa ampas', 15000.00, 'kopi_hitam.jpg', 50, 'Minuman'),
(3, 'Sabun Mandi Cair', 'Sabun mandi dengan aroma menyegarkan', 20000.00, 'sabun_mandi.jpg', 75, 'Kesehatan & Kebersihan'),
(4, 'Minyak Goreng Kemasan 1 Liter', 'Minyak goreng berkualitas baik', 25000.00, 'minyak_goreng.jpg', 40, 'Dapur & Bahan Masak'),
(5, 'Biskuit Coklat', 'Biskuit renyah dengan isian coklat', 10000.00, 'biskuit_coklat.jpg', 120, 'Makanan');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'admin@tokobarokah.com', '$2y$10$fsFo.4Vn2pq7tr7Cn3UjfOiLSiWdIJD55qygM2IgQ/CiKd9btyICi', 'admin', '2025-07-19 08:15:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
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
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
