-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Mar 21, 2020 at 07:44 PM
-- Server version: 5.7.25
-- PHP Version: 7.3.1
CREATE Database IF NOT EXISTS `esd_appointment`;
USE esd_appointment;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `esd_appointment`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--
DROP TABLE IF EXISTS `appointment`;
CREATE TABLE `appointment` (
  `appointment_id` int(11) NOT NULL,
  `doctor_id` varchar(11) NOT NULL,
  `patient_id` varchar(11) NOT NULL,
  `date` varchar(15) NOT NULL,
  `time` varchar(12) NOT NULL,
  `payment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appointment_id`, `doctor_id`, `patient_id`, `date`, `time`, `payment_id`) VALUES
(19, '2', '1', '2020-04-04', '10:00 AM', 41),
(20, '3', '2', '2020-04-04', '11:00 AM', 30),
(21, '2', '3', '2020-04-04', '12:00 PM', 41),
(22, '1', '1', '2020-04-04', '14:00 PM', 20),
(23, '2', '2', '2020-04-04', '8:00 AM', 41),
(24, '2', '1', '2020-04-04', '9:00 AM', 41);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appointment_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;


DROP TABLE IF EXISTS `history`;
CREATE TABLE `history` (
  `appointment_id` int(11) NOT NULL,
  `doctor_id` varchar(11) NOT NULL,
  `patient_id` varchar(11) NOT NULL,
  `date` varchar(15) NOT NULL,
  `time` varchar(12) NOT NULL,
  `payment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `history` (`appointment_id`, `doctor_id`, `patient_id`, `date`, `time`, `payment_id`) VALUES
(119, '2', '1', '2020-04-04', '10:00 AM', 41),
(210, '3', '2', '2020-04-04', '11:00 AM', 30),
(211, '2', '3', '2020-04-04', '12:00 PM', 41),
(212, '1', '1', '2020-04-04', '14:00 PM', 20),
(213, '2', '2', '2020-04-04', '8:00 AM', 41),
(214, '2', '1', '2020-04-04', '9:00 AM', 41);
