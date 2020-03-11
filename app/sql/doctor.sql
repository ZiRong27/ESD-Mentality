-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 08, 2020 at 06:53 AM
-- Server version: 5.7.23
-- PHP Version: 7.2.10
CREATE Database esd_doctor;
USE esd_doctor;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `doctor`
--

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

DROP TABLE IF EXISTS `doctor`;
CREATE TABLE IF NOT EXISTS `doctor` (
  `doctor_id` varchar(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `dob` varchar(15) NOT NULL,
  `experience` varchar(300) NOT NULL,
  `specialisation` varchar(300) NOT NULL,
  `price` varchar(5) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  PRIMARY KEY (`doctor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`doctor_id`, `name`, `gender`, `dob`, `experience`, `specialisation`, `price`, `username`, `password`) VALUES
('1', 'John Smith', 'Male', '1968-11-01', 'Senior Psychologist', 'Behavioural, Social, Children Psychology', '130', 'johnsmith', 'js1'),
('2', 'Rosa Fernandez', 'Female', '1970-07-15', 'Counsellor', 'Educational, Cognitive Psychology', '95', 'rosafernandez', 'rf1'),
('3', 'Tanya Lee', 'Female', '1973-03-10', 'Senior Counsellor', 'Developmental Psychology', '110', 'tanyalee', 'tl1'),
('4', 'Thomas Ng', 'Male', '1975-11-06', 'Counsellor', 'Behavioural, Social Psychology', '95', 'thomasng', 'tn1'),
('5', 'Julia Lim', 'Female', '1970-06-18', 'Senior Psychologist', 'Developmental, Social Psychology', '130', 'julialim', 'jl1');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
