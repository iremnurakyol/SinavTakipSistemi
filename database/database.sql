-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1:3306
-- Üretim Zamanı: 29 Nis 2026, 18:06:09
-- Sunucu sürümü: 9.1.0
-- PHP Sürümü: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `db_sinav_takip`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb3_turkish_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb3_turkish_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb3_turkish_ci NOT NULL,
  `role` enum('admin','student') COLLATE utf8mb3_turkish_ci DEFAULT 'student',
  `class_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `class_id` (`class_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_turkish_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `class_id`, `created_at`) VALUES
(1, 'Test', 'test@ornek.com', '$2y$10$5JKE2LoXoJGW5mcd7eexMeCbgfqpB1IIeKj7A/sA3rk14TvWR/6Sm', 'admin', NULL, '2026-04-29 16:52:15'),
(2, 'Ali Ogrenci', 'ali@test.com', '$2y$10$5JKE2LoXoJGW5mcd7eexMeCbgfqpB1IIeKj7A/sA3rk14TvWR/6Sm', 'student', NULL, '2026-04-29 18:05:39'),
(3, 'Ayşe Kara', 'ayse@test.com', '$2y$10$5JKE2LoXoJGW5mcd7eexMeCbgfqpB1IIeKj7A/sA3rk14TvWR/6Sm', 'student', NULL, '2026-04-29 18:05:39'),
(5, 'Ali Kara', 'ali@ornek.com', '$2y$10$u1xyuzpdxyXTAbQK0YQv1ellR6QUSrMgwB6b7Jy6sTGnqibtdfwBS', 'student', NULL, '2026-04-29 17:43:24');

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
