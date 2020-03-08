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
-- Database: `consultation`
--

-- --------------------------------------------------------

--
-- Table structure for table `consultation`
--

DROP TABLE IF EXISTS `consultation`;
CREATE TABLE IF NOT EXISTS `consultation` (
  `consultation_id` varchar(11) NOT NULL,
  `appointment_id` varchar(11) NOT NULL,
  `doctor_id` varchar(11) NOT NULL,
  `patient_id` varchar(11) NOT NULL,
  `diagnosis` text,
  `prescription` text,
  `notes` text,
  PRIMARY KEY (`consultation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `consultation`
--

INSERT INTO `consultation` (`consultation_id`, `appointment_id`, `doctor_id`, `patient_id`, `diagnosis`, `prescription`, `notes`) VALUES
('1', '1', '2', '1', 'Bipolar Disorder, Anxiety', 'Zoloft: 35mg', 'Patient is starting to display worsening symptoms of anxiety'),
('10', '10', '1', '5', 'Obsessive-Compulsive Disorder', 'Prozac: 60mg', 'Patient is responding well to the new round of medication'),
('11', '11', '5', '10', 'Depression', 'Prozac: 70mg', '*Self harm risk'),
('12', '12', '5', '10', 'Depression', 'Prozac: 70mg', 'Patient came in with suicidal thoughts but preventative measures have been taken. \r\n\r\n*Extreme risk patient'),
('13', '13', '1', '5', 'Obsessive-Compulsive Disorder', 'Zoloft: 30mg', NULL),
('14', '14', '1', '5', 'Obsessive-Compulsive Disorder', 'Prozac: 50mg', 'Patient\'s symptoms is not improving, will prescribe a different round of medication'),
('2', '2', '3', '2', 'Anxiety Disorder', 'Xanax: 5mg', NULL),
('3', '3', '4', '3', 'Anxiety Disorder, Insomnia', 'Xanax: 7mg', 'Patient\'s insomnia is getting worse, started to experience an increase in sleepless nights'),
('4', '4', '5', '4', 'Obsessive-Compulsive Disorder', 'Prozac: 60mg', 'Patient seems to be improving with the latest prescription of Prozac'),
('5', '5', '2', '1', 'Bipolar Disorder, Anxiety', 'Zoloft: 35mg', 'Worsening mood swings, will increase dosage of medication'),
('6', '6', '2', '9', 'Depression, Panic Disorder', 'Xanax: 7mg', 'Patient reacting well to the change in antidepressant from Prozac to Xanax'),
('7', '7', '3', '8', 'Anxiety Disorder', 'Xanax: 5mg', NULL),
('8', '8', '3', '7', 'Bipolar Disorder', 'Zoloft: 30mg', NULL),
('9', '9', '1', '6', 'Anxiety Disorder, Insomnia', 'Xanax: 10mg', 'Patient experienced more anxiety attacks this past week, which was caused by overthinking - will increase medication dosage');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
