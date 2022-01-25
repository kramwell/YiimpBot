-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 19, 2022 at 06:36 PM
-- Server version: 5.7.36
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tg_ympbot`
--

-- --------------------------------------------------------

--
-- Table structure for table `poolwallets`
--

DROP TABLE IF EXISTS `poolwallets`;
CREATE TABLE IF NOT EXISTS `poolwallets` (
  `poolAddress` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `from_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `scheme` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `timeNow` int(12) NOT NULL,
  `walletAddress` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `unique_id` int(5) NOT NULL AUTO_INCREMENT,
  `currency` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`unique_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tempchatpool`
--

DROP TABLE IF EXISTS `tempchatpool`;
CREATE TABLE IF NOT EXISTS `tempchatpool` (
  `poolAddress` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `from_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `scheme` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `timeNow` int(12) NOT NULL,
  `walletAddress` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `currency` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`from_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
