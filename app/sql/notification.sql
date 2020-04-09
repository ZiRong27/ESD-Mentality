-- Host: localhost:8889
-- Generation Time: Apr 09, 2020 at 11:07 AM
-- Server version: 5.7.25
-- PHP Version: 7.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `esd_notification`
--
CREATE DATABASE IF NOT EXISTS `esd_notification` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `esd_notification`;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `patient_id` int(11) NOT NULL,
  `correlation_id` varchar(36) NOT NULL,
  `message` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`patient_id`, `correlation_id`, `message`) VALUES
(1, '98c4db53-ba7a-4f17-9978-4ace5caca850', 'hi');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`correlation_id`);
