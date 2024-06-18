-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2024 at 03:51 AM
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
-- Database: `promanager`
--

-- --------------------------------------------------------

--
-- Table structure for table `proiecte`
--

CREATE TABLE `proiecte` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `client` varchar(100) DEFAULT NULL,
  `nume_proiect` varchar(100) DEFAULT NULL,
  `cod_proiect` varchar(50) DEFAULT NULL,
  `data_inceput` date DEFAULT NULL,
  `data_sfarsit` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proiecte`
--

INSERT INTO `proiecte` (`id`, `user_id`, `client`, `nume_proiect`, `cod_proiect`, `data_inceput`, `data_sfarsit`) VALUES
(23, 10, 'Client ABC', 'Test', 'Test123', '2024-06-01', '2024-08-01'),
(24, 10, 'Client ABCD', 'Proiect Exemplu', 'Exemplu 123', '2024-06-01', '2024-09-01');

-- --------------------------------------------------------

--
-- Table structure for table `taskuri`
--

CREATE TABLE `taskuri` (
  `id` int(11) NOT NULL,
  `proiect_id` int(11) NOT NULL,
  `nume_task` varchar(255) NOT NULL,
  `ore` int(11) DEFAULT NULL,
  `ore_lucrate` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `taskuri`
--

INSERT INTO `taskuri` (`id`, `proiect_id`, `nume_task`, `ore`, `ore_lucrate`) VALUES
(52, 23, 'Planificare & Analiză', 10, 10),
(53, 23, 'Stabilire Cerințe', 15, 5),
(54, 23, 'Developement', 50, 25),
(55, 23, 'Design', 20, 10),
(56, 23, 'Testare', 25, 5),
(57, 23, 'Documentare', 5, 3),
(58, 24, 'Planificare & Analiză', 20, 7),
(59, 24, 'Stabilire Cerințe', 20, 13),
(60, 24, 'Developement', 25, 21);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `company` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `PASSWORD`, `company`) VALUES
(10, 'Sebastian', 'Laszlo', 'SebastianLaszlo@gmail.com', '$2y$10$8PXB05XEbxZaA5VJe6yN8./IwsphWmk.sj60FrjG3/XiTkjQAJwiO', 'GPP');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `proiecte`
--
ALTER TABLE `proiecte`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `taskuri`
--
ALTER TABLE `taskuri`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proiect_id` (`proiect_id`);

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
-- AUTO_INCREMENT for table `proiecte`
--
ALTER TABLE `proiecte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `taskuri`
--
ALTER TABLE `taskuri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `proiecte`
--
ALTER TABLE `proiecte`
  ADD CONSTRAINT `proiecte_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `taskuri`
--
ALTER TABLE `taskuri`
  ADD CONSTRAINT `taskuri_ibfk_1` FOREIGN KEY (`proiect_id`) REFERENCES `proiecte` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
