-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 30, 2025 at 03:52 PM
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
-- Database: `animal_sanctuary`
--

-- --------------------------------------------------------

--
-- Table structure for table `animal`
--

CREATE TABLE `animal` (
  `animal_id` int(4) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `DOB` date DEFAULT NULL,
  `Species` text NOT NULL,
  `Intake_type` int(11) NOT NULL,
  `Gender` int(11) NOT NULL,
  `Release_Date` date DEFAULT NULL,
  `Diet` text NOT NULL,
  `Intake_Date` date NOT NULL,
  `enclosure_id` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `animal`
--

INSERT INTO `animal` (`animal_id`, `Name`, `DOB`, `Species`, `Intake_type`, `Gender`, `Release_Date`, `Diet`, `Intake_Date`, `enclosure_id`) VALUES
(101, 'Simba', '2018-05-14', 'Lion', 1, 1, NULL, 'Carnivore', '2022-01-10', 2),
(102, 'Nala', '2019-07-22', 'Lion', 1, 2, NULL, 'Carnivore', '2022-01-15', 2),
(103, 'Dumbo', '2015-09-09', 'Elephant', 1, 1, NULL, 'Herbivore', '2021-11-05', 2),
(104, 'Kiki', '2020-03-17', 'Giraffe', 2, 2, '2026-06-30', 'Herbivore', '2022-03-12', 2),
(105, 'Baloo', '2016-12-01', 'Bear', 1, 1, NULL, 'Omnivore', '2023-02-20', 3),
(106, 'Zara', '2017-06-11', 'Zebra', 2, 2, '2026-08-15', 'Herbivore', '2022-04-25', 4),
(107, 'Raja', '2021-01-03', 'Tiger', 1, 1, NULL, 'Carnivore', '2023-01-15', 1),
(108, 'Momo', '2019-10-10', 'Monkey', 2, 1, '2025-12-31', 'Omnivore', '2023-05-18', 1);

-- --------------------------------------------------------

--
-- Table structure for table `breeding`
--

CREATE TABLE `breeding` (
  `breeding_id` int(5) NOT NULL,
  `mating_date` date NOT NULL,
  `mother_id` int(4) NOT NULL,
  `father_id` int(4) NOT NULL,
  `due_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cleans`
--

CREATE TABLE `cleans` (
  `username` varchar(30) NOT NULL,
  `enclosure_id` int(3) NOT NULL,
  `clean_time` time NOT NULL,
  `clean_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enclosure`
--

CREATE TABLE `enclosure` (
  `enclosure_id` int(3) NOT NULL,
  `Type` varchar(7) NOT NULL,
  `Habitat` text NOT NULL,
  `capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enclosure`
--

INSERT INTO `enclosure` (`enclosure_id`, `Type`, `Habitat`, `capacity`) VALUES
(1, 'Indoor', 'Rainforest Habitat', 5),
(2, 'Outdoor', 'Savannah Plains', 6),
(3, 'Indoor', 'Mountain Habitat', 4),
(4, 'Outdoor', 'River Wetlands', 5);

-- --------------------------------------------------------

--
-- Table structure for table `feeds`
--

CREATE TABLE `feeds` (
  `username` varchar(30) NOT NULL,
  `animal_id` int(4) NOT NULL,
  `feed_time` time NOT NULL,
  `feed_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `health_issues`
--

CREATE TABLE `health_issues` (
  `animal_id` int(4) NOT NULL,
  `health_issue` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `item_name` varchar(100) NOT NULL,
  `last_restocked` date NOT NULL,
  `quantity` int(6) NOT NULL,
  `item_type` varchar(7) NOT NULL,
  `unit` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicine`
--

CREATE TABLE `medicine` (
  `animal_id` int(4) NOT NULL,
  `medicine_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offspring`
--

CREATE TABLE `offspring` (
  `breeding_id` int(5) NOT NULL,
  `offspring_id` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shift`
--

CREATE TABLE `shift` (
  `start` time NOT NULL,
  `end` time NOT NULL,
  `date` date NOT NULL,
  `username` varchar(30) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `Username` varchar(30) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone_no` int(11) NOT NULL,
  `email` varchar(60) NOT NULL,
  `job` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `manager_username` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`Username`, `name`, `phone_no`, `email`, `job`, `password`, `manager_username`) VALUES
('Alice_W', 'Alice Walker', 987654321, 'alice@example.com', 'Caretaker', 'passwordAlice1', 'John_Doe'),
('Bob_K', 'Bob Kennedy', 564738291, 'bob@example.com', 'Veterinarian', 'passwordBob1', 'John_Doe'),
('John_Doe', 'John Doe', 1234567890, 'john@example.com', 'Manager', '1234abcde', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `animal`
--
ALTER TABLE `animal`
  ADD PRIMARY KEY (`animal_id`),
  ADD KEY `enclosure_id` (`enclosure_id`);

--
-- Indexes for table `breeding`
--
ALTER TABLE `breeding`
  ADD PRIMARY KEY (`breeding_id`),
  ADD KEY `mother` (`mother_id`),
  ADD KEY `father` (`father_id`);

--
-- Indexes for table `cleans`
--
ALTER TABLE `cleans`
  ADD PRIMARY KEY (`username`,`enclosure_id`,`clean_time`,`clean_date`),
  ADD KEY `fk_enclosure_id_clean` (`enclosure_id`);

--
-- Indexes for table `enclosure`
--
ALTER TABLE `enclosure`
  ADD PRIMARY KEY (`enclosure_id`);

--
-- Indexes for table `feeds`
--
ALTER TABLE `feeds`
  ADD PRIMARY KEY (`username`,`animal_id`,`feed_time`,`feed_date`);

--
-- Indexes for table `health_issues`
--
ALTER TABLE `health_issues`
  ADD PRIMARY KEY (`animal_id`,`health_issue`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`item_name`);

--
-- Indexes for table `medicine`
--
ALTER TABLE `medicine`
  ADD PRIMARY KEY (`animal_id`,`medicine_name`),
  ADD KEY `animal_id_2` (`animal_id`,`medicine_name`);

--
-- Indexes for table `offspring`
--
ALTER TABLE `offspring`
  ADD PRIMARY KEY (`breeding_id`,`offspring_id`),
  ADD KEY `offspringid` (`offspring_id`);

--
-- Indexes for table `shift`
--
ALTER TABLE `shift`
  ADD PRIMARY KEY (`date`,`username`),
  ADD KEY `staff shift time` (`username`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`Username`),
  ADD KEY `manager` (`manager_username`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `animal`
--
ALTER TABLE `animal`
  ADD CONSTRAINT `animal_ibfk_1` FOREIGN KEY (`enclosure_id`) REFERENCES `enclosure` (`enclosure_id`);

--
-- Constraints for table `breeding`
--
ALTER TABLE `breeding`
  ADD CONSTRAINT `father` FOREIGN KEY (`father_id`) REFERENCES `animal` (`animal_id`),
  ADD CONSTRAINT `mother` FOREIGN KEY (`mother_id`) REFERENCES `animal` (`animal_id`);

--
-- Constraints for table `cleans`
--
ALTER TABLE `cleans`
  ADD CONSTRAINT `fk_enclosure_id_clean` FOREIGN KEY (`enclosure_id`) REFERENCES `enclosure` (`enclosure_id`),
  ADD CONSTRAINT `fk_username_clean` FOREIGN KEY (`username`) REFERENCES `staff` (`Username`);

--
-- Constraints for table `health_issues`
--
ALTER TABLE `health_issues`
  ADD CONSTRAINT `health` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`animal_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `medicine`
--
ALTER TABLE `medicine`
  ADD CONSTRAINT `medicine_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`animal_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `offspring`
--
ALTER TABLE `offspring`
  ADD CONSTRAINT `breedingid` FOREIGN KEY (`breeding_id`) REFERENCES `breeding` (`breeding_id`),
  ADD CONSTRAINT `offspringid` FOREIGN KEY (`offspring_id`) REFERENCES `animal` (`animal_id`);

--
-- Constraints for table `shift`
--
ALTER TABLE `shift`
  ADD CONSTRAINT `staff shift time` FOREIGN KEY (`username`) REFERENCES `staff` (`Username`);

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `manager` FOREIGN KEY (`manager_username`) REFERENCES `staff` (`Username`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
