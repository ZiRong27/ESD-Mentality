-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 08, 2020 at 06:53 AM
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
-- Database: `patient`
--

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

DROP TABLE IF EXISTS `patient`;
CREATE TABLE IF NOT EXISTS `patient` (
  `patient_id` varchar(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `dob` varchar(15) NOT NULL,
  `phone` varchar(8) NOT NULL,
  `salutation` varchar(5) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  PRIMARY KEY (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`patient_id`, `name`, `gender`, `dob`, `phone`, `salutation`, `username`, `password`) VALUES
('1', 'Sophie Ng', 'Female', '1989-02-10', '91131622', 'Ms', 'sophieng', 'sn1'),
('10', 'Dennis Brown', 'Male', '1990-05-06', '89832145', 'Mr', 'dennisbrown', 'db1'),
('2', 'Zoey Tan', 'Female', '1990-03-12', '98515347', 'Ms', 'zoeytan', 'zt1'),
('3', 'Sam Santiago', 'Male', '1992-03-06', '97632174', 'Mr', 'samsantiago', 'ss1'),
('4', 'Vincent Chua', 'Male', '1995-08-07', '81689240', 'Mr', 'vincentchua', 'vc1'),
('5', 'Jasmine Tan', 'Female', '1996-03-12', '90951589', 'Miss', 'jasminetan', 'jt1'),
('6', 'Jerald Chan', 'Male', '1997-03-18', '98761020', 'Mr', 'jeraldchan', 'jc1'),
('7', 'Helena Chavez', 'Female', '1997-07-10', '98082341', 'Miss', 'helenachavez', 'hc1'),
('8', 'Kelvin Tan', 'Male', '1990-01-20', '90175437', 'Mr', 'kelvintan', 'kt1'),
('9', 'Lilian Goh', 'Female', '1992-03-10', '89021254', 'Ms', 'liliangoh', 'lg1');

-- --------------------------------------------------------

--
-- Table structure for table `patient_allergies`
--

DROP TABLE IF EXISTS `patient_allergies`;
CREATE TABLE IF NOT EXISTS `patient_allergies` (
  `patient_id` varchar(11) NOT NULL,
  `allergies` varchar(1000) NOT NULL DEFAULT 'NIL',
  PRIMARY KEY (`patient_id`,`allergies`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient_allergies`
--

INSERT INTO `patient_allergies` (`patient_id`, `allergies`) VALUES
('1', 'NIL'),
('10', 'NIL'),
('2', 'paracetamol'),
('3', 'sertraline, escitalopram'),
('4', 'paracetamol, fluoxetine'),
('5', 'NIL'),
('6', 'paracetamol'),
('7', 'NIL'),
('8', 'NIL'),
('9', 'NIL');

-- --------------------------------------------------------

--
-- Table structure for table `patient_medical_history`
--

DROP TABLE IF EXISTS `patient_medical_history`;
CREATE TABLE IF NOT EXISTS `patient_medical_history` (
  `patient_id` varchar(11) NOT NULL,
  `medical_history` varchar(1000) NOT NULL DEFAULT 'NIL',
  PRIMARY KEY (`patient_id`),
  UNIQUE KEY `diagnosis` (`medical_history`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient_medical_history`
--

INSERT INTO `patient_medical_history` (`patient_id`, `medical_history`) VALUES
('3', 'Anemia'),
('6', 'Asthma'),
('5', 'Eczema'),
('4', 'High Blood Pressure'),
('10', 'Low white blood cell count'),
('9', 'Minor heart defect'),
('1', 'NIL'),
('8', 'Thalassemia'),
('2', 'Type 1 Diabetes'),
('7', 'Weak heart condition');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
