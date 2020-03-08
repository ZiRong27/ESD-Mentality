-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 08, 2020 at 06:52 AM
-- Server version: 5.7.23
-- PHP Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `appointment`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

DROP TABLE IF EXISTS `appointment`;
CREATE TABLE IF NOT EXISTS `appointment` (
  `appointment_id` varchar(11) NOT NULL,
  `doctor_id` varchar(11) NOT NULL,
  `patient_id` varchar(11) NOT NULL,
  `date` varchar(15) NOT NULL,
  `time` varchar(12) NOT NULL,
  PRIMARY KEY (`appointment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appointment_id`, `doctor_id`, `patient_id`, `date`, `time`) VALUES
('1', '2', '1', '2020-03-02', '10:30:00'),
('10', '1', '5', ' 2020-03-05', '15:00:00 '),
('11', '5', '10', '2020-03-04', '10:00:00'),
('12', '5', '10', '2020-03-04', '14:00:00'),
('13', '1', '5', '2020-02-20', '11:00:00'),
('14', '1', '5', '2020-02-27', '11:00:00'),
('2', '3', '2', '2020-03-02', '14:30:00'),
('3', '4', '3', '2020-03-02', '15:00:00'),
('4', '5', '4', '2020-03-02', '09:30:00'),
('5', '2', '1', ' 2020-03-06', '10:30:00 '),
('6', '2', '9', ' 2020-03-02', '14:30:00 '),
('7', '3', '8', ' 2020-03-06', '11:00:00 '),
('8', '3', '7', ' 2020-03-06', '13:30:00 '),
('9', '1', '6', ' 2020-03-05', '10:30:00 ');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
