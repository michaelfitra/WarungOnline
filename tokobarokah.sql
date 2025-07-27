-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 27, 2025 at 06:51 PM
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
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('diproses','siap_dijemput','selesai') DEFAULT 'diproses'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_date`, `total_amount`, `status`) VALUES
(1, 1, '2025-07-27 13:23:14', 75000.00, 'selesai'),
(2, 2, '2025-07-27 13:37:21', 63500.00, 'selesai'),
(3, 2, '2025-07-27 13:54:33', 58500.00, 'diproses'),
(4, 1, '2025-07-27 22:29:18', 115500.00, 'diproses');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 5, 3, 10000.00),
(2, 1, 15, 1, 20000.00),
(3, 1, 4, 1, 25000.00),
(4, 2, 3, 1, 20000.00),
(5, 2, 2, 2, 15000.00),
(6, 2, 5, 1, 10000.00),
(7, 2, 1, 1, 3500.00),
(8, 3, 3, 1, 20000.00),
(9, 3, 5, 1, 10000.00),
(10, 3, 1, 1, 3500.00),
(11, 3, 4, 1, 25000.00),
(12, 4, 3, 1, 20000.00),
(13, 4, 4, 3, 25000.00),
(14, 4, 1, 3, 3500.00),
(15, 4, 5, 1, 10000.00);

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
(5, 'Biskuit Coklat', 'Biskuit renyah dengan isian coklat', 10000.00, 'biskuit_coklat.jpg', 120, 'Makanan'),
(15, 'test berubah', 'test joki', 20000.00, '687b64fb1518b.jpeg', 11, 'Dapur & Bahan Masak');

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
(1, 'admin@tokobarokah.com', '$2y$10$fsFo.4Vn2pq7tr7Cn3UjfOiLSiWdIJD55qygM2IgQ/CiKd9btyICi', 'admin', '2025-07-19 08:15:25'),
(2, 'mikel', '$2y$10$exJijHeqbkcEUo8YQL1KM.Rzom8XGDF0rwbUHZQeiTU24raIEOgzi', 'admin', '2025-07-27 06:37:05'),
(3, 'dipo', '$2y$10$TQ19LjY3h3/iNNsAi3ugsuy6nRSTmciePPsU0dFk/v3GotladWLhW', 'user', '2025-07-27 08:14:34'),
(4, 'sandi', '$2y$10$xqwMBN89dRJxuVxd4ejbMOFyUtz3NJyrmLKkpjuXgB9hx4YBEc5N2', 'user', '2025-07-27 08:28:03'),
(5, 'test', '$2y$10$LhIqVXiwA6T7.FHkZKO0L.3.GZkYi.EZ8kSJECv.A8/A9jw5meu2u', 'user', '2025-07-27 08:28:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

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
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `produk` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
