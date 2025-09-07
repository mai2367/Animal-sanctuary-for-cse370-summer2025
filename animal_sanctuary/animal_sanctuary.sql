-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 06, 2025 at 08:11 PM
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
(108, 'Momo', '2019-10-10', 'Monkey', 2, 1, '2025-12-31', 'Omnivore', '2023-05-18', 1),
(111, 'Boris', '2025-09-01', 'Bear', 1, 1, NULL, 'Omnivore', '2025-09-06', 3),
(114, 'Effie', '2022-02-26', 'Monkey', 1, 2, NULL, 'Omnivore', '2025-09-06', 1),
(115, 'BB', '2025-10-09', 'Lion', 2, 1, NULL, 'Carnivore', '2025-10-09', 3);

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

--
-- Dumping data for table `breeding`
--

INSERT INTO `breeding` (`breeding_id`, `mating_date`, `mother_id`, `father_id`, `due_date`) VALUES
(1, '2025-09-06', 102, 101, '2025-10-11');

-- --------------------------------------------------------

--
-- Table structure for table `clean_assignments`
--

CREATE TABLE `clean_assignments` (
  `username` varchar(30) NOT NULL,
  `enclosure_id` int(3) NOT NULL,
  `shift_type_id` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clean_assignments`
--

INSERT INTO `clean_assignments` (`username`, `enclosure_id`, `shift_type_id`) VALUES
('Alice_W', 4, 1),
('Alice_W', 4, 3),
('Bob_K', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `enclosure`
--

CREATE TABLE `enclosure` (
  `enclosure_id` int(3) NOT NULL,
  `Type` varchar(7) NOT NULL,
  `Habitat` text NOT NULL,
  `capacity` int(11) NOT NULL,
  `NAME` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enclosure`
--

INSERT INTO `enclosure` (`enclosure_id`, `Type`, `Habitat`, `capacity`, `NAME`) VALUES
(1, 'Indoor', 'Rainforest Habitat', 5, 'Rainforest 1'),
(2, 'Outdoor', 'Savannah Plains', 6, 'Savannah Plains 1'),
(3, 'Indoor', 'Mountain Habitat', 4, 'Mountain Habitat 1'),
(4, 'Outdoor', 'River Wetlands', 5, 'River Wetlands 1');

-- --------------------------------------------------------

--
-- Table structure for table `feed_assignments`
--

CREATE TABLE `feed_assignments` (
  `username` varchar(30) NOT NULL,
  `animal_id` int(4) NOT NULL,
  `shift_type_id` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feed_assignments`
--

INSERT INTO `feed_assignments` (`username`, `animal_id`, `shift_type_id`) VALUES
('Alice_W', 104, 2),
('Alice_W', 105, 1),
('Alice_W', 108, 1),
('Bob_K', 107, 1),
('Bob_K', 111, 1);

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

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`item_name`, `last_restocked`, `quantity`, `item_type`, `unit`) VALUES
('Chicken', '2025-09-06', 10, 'Food', 'kg'),
('Napa', '2025-09-06', 40, 'medicin', 'tablet');

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

--
-- Dumping data for table `offspring`
--

INSERT INTO `offspring` (`breeding_id`, `offspring_id`) VALUES
(1, 115);

-- --------------------------------------------------------

--
-- Table structure for table `shift_types`
--

CREATE TABLE `shift_types` (
  `shift_type_id` int(1) NOT NULL,
  `period` enum('WEEKDAY','WEEKEND') NOT NULL,
  `time_slot` enum('MORNING','NIGHT') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shift_types`
--

INSERT INTO `shift_types` (`shift_type_id`, `period`, `time_slot`) VALUES
(1, 'WEEKDAY', 'MORNING'),
(2, 'WEEKDAY', 'NIGHT'),
(3, 'WEEKEND', 'MORNING'),
(4, 'WEEKEND', 'NIGHT');

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
  `manager_username` varchar(30) DEFAULT NULL,
  `ismanager` enum('Yes','No') NOT NULL DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`Username`, `name`, `phone_no`, `email`, `job`, `password`, `manager_username`, `ismanager`) VALUES
('Alice_W', 'Alice Walker', 987654321, 'alice@example.com', 'Caretaker', 'passwordAlice1', 'John_Doe', 'No'),
('Apurbo_S', 'Apurbo Saha', 2147483647, 'apurbo@gmail.com', 'Caretaker', '123456', NULL, 'No'),
('Bob_K', 'Bob Kennedy', 564738291, 'bob@example.com', 'Veterinarian', 'passwordBob1', 'John_Doe', 'No'),
('Caleb_H', 'Caleb Hayes', 2147483647, 'calebhayes@gmail.com', 'Caretaker', '13568901', NULL, 'No'),
('John_Doe', 'John Doe', 1234567890, 'john@example.com', 'Manager', '1234abcd', NULL, 'Yes');

-- --------------------------------------------------------

--
-- Table structure for table `staff_shift_map`
--

CREATE TABLE `staff_shift_map` (
  `username` varchar(30) NOT NULL,
  `shift_type_id` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_shift_map`
--

INSERT INTO `staff_shift_map` (`username`, `shift_type_id`) VALUES
('Alice_W', 1),
('Alice_W', 3),
('Bob_K', 1);

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
-- Indexes for table `clean_assignments`
--
ALTER TABLE `clean_assignments`
  ADD PRIMARY KEY (`username`,`enclosure_id`,`shift_type_id`),
  ADD KEY `idx_clean_user` (`username`),
  ADD KEY `idx_clean_enc` (`enclosure_id`),
  ADD KEY `fk_ca_type` (`shift_type_id`);

--
-- Indexes for table `enclosure`
--
ALTER TABLE `enclosure`
  ADD PRIMARY KEY (`enclosure_id`),
  ADD UNIQUE KEY `NAME` (`NAME`);

--
-- Indexes for table `feed_assignments`
--
ALTER TABLE `feed_assignments`
  ADD PRIMARY KEY (`animal_id`,`shift_type_id`),
  ADD KEY `idx_feed_user` (`username`),
  ADD KEY `fk_fa_type` (`shift_type_id`);

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
-- Indexes for table `shift_types`
--
ALTER TABLE `shift_types`
  ADD PRIMARY KEY (`shift_type_id`),
  ADD UNIQUE KEY `period` (`period`,`time_slot`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`Username`),
  ADD KEY `manager` (`manager_username`);

--
-- Indexes for table `staff_shift_map`
--
ALTER TABLE `staff_shift_map`
  ADD PRIMARY KEY (`username`,`shift_type_id`),
  ADD KEY `fk_ssm_type` (`shift_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `shift_types`
--
ALTER TABLE `shift_types`
  MODIFY `shift_type_id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
-- Constraints for table `clean_assignments`
--
ALTER TABLE `clean_assignments`
  ADD CONSTRAINT `fk_ca_enclosure` FOREIGN KEY (`enclosure_id`) REFERENCES `enclosure` (`enclosure_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ca_type` FOREIGN KEY (`shift_type_id`) REFERENCES `shift_types` (`shift_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ca_user` FOREIGN KEY (`username`) REFERENCES `staff` (`Username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `feed_assignments`
--
ALTER TABLE `feed_assignments`
  ADD CONSTRAINT `fk_fa_animal` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`animal_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fa_type` FOREIGN KEY (`shift_type_id`) REFERENCES `shift_types` (`shift_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fa_user` FOREIGN KEY (`username`) REFERENCES `staff` (`Username`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `manager` FOREIGN KEY (`manager_username`) REFERENCES `staff` (`Username`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `staff_shift_map`
--
ALTER TABLE `staff_shift_map`
  ADD CONSTRAINT `fk_ssm_type` FOREIGN KEY (`shift_type_id`) REFERENCES `shift_types` (`shift_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ssm_user` FOREIGN KEY (`username`) REFERENCES `staff` (`Username`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
