-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2026 at 12:18 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lifeline: a blood bank mangement system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `A_Username` varchar(30) NOT NULL,
  `Password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`A_Username`, `Password`) VALUES
('admin01', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `blood`
--

CREATE TABLE `blood` (
  `Blood_Group` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blood`
--

INSERT INTO `blood` (`Blood_Group`) VALUES
('A+'),
('A-'),
('AB+'),
('AB-'),
('B+'),
('B-'),
('O+'),
('O-');

-- --------------------------------------------------------

--
-- Table structure for table `blood_bag`
--

CREATE TABLE `blood_bag` (
  `Blood_Bag_Id` int(11) NOT NULL,
  `Blood_Group` varchar(5) DEFAULT NULL,
  `Number_of_Bags` int(11) DEFAULT NULL,
  `D_Username` varchar(30) DEFAULT NULL,
  `Date_Blood_Added` date DEFAULT NULL,
  `H_TIN` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blood_bag`
--

INSERT INTO `blood_bag` (`Blood_Bag_Id`, `Blood_Group`, `Number_of_Bags`, `D_Username`, `Date_Blood_Added`, `H_TIN`) VALUES
(3, 'A+', 1, 'rahim01', '2025-01-10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `donor`
--

CREATE TABLE `donor` (
  `D_Username` varchar(30) NOT NULL,
  `D_Name` varchar(50) NOT NULL,
  `D_Password` varchar(50) NOT NULL,
  `D_Date_of_Birth` date DEFAULT NULL,
  `D_Gender` varchar(10) DEFAULT NULL,
  `Blood_Group` varchar(5) DEFAULT NULL,
  `D_Mobile_Number` varchar(15) DEFAULT NULL,
  `D_Address` varchar(100) DEFAULT NULL,
  `D_Email` varchar(50) DEFAULT NULL,
  `Total_Given_Bloodbags` int(11) DEFAULT 0,
  `Last_Date_of_Donation` date DEFAULT NULL,
  `Last_Donation_Hospital_TIN` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donor`
--

INSERT INTO `donor` (`D_Username`, `D_Name`, `D_Password`, `D_Date_of_Birth`, `D_Gender`, `Blood_Group`, `D_Mobile_Number`, `D_Address`, `D_Email`, `Total_Given_Bloodbags`, `Last_Date_of_Donation`, `Last_Donation_Hospital_TIN`) VALUES
('rahim01', 'Rahim Uddin', 'rahim123', '1998-05-15', 'Male', 'A+', '01712345678', 'Dhaka', 'rahim@gmail.com', 3, '2025-01-10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hospital`
--

CREATE TABLE `hospital` (
  `H_TIN` varchar(30) NOT NULL,
  `H_Name` varchar(100) NOT NULL,
  `H_Email` varchar(100) NOT NULL,
  `H_Phone_Number` varchar(20) NOT NULL,
  `H_Address` varchar(200) NOT NULL,
  `Is_Active` tinyint(1) DEFAULT 1,
  `Created_Date` timestamp NOT NULL DEFAULT current_timestamp(),
  `Updated_Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospital`
--

INSERT INTO `hospital` (`H_TIN`, `H_Name`, `H_Email`, `H_Phone_Number`, `H_Address`, `Is_Active`, `Created_Date`, `Updated_Date`) VALUES
('123456789', 'Square Hospital', 'square@gmail.com', '01712345678', 'Dhaka', 1, '2026-08-26 22:16:35', '2026-08-26 22:16:35');

-- --------------------------------------------------------

--
-- Table structure for table `hospital_registration_request`
--

CREATE TABLE `hospital_registration_request` (
  `Request_ID` int(11) NOT NULL,
  `H_TIN` varchar(30) NOT NULL,
  `H_Name` varchar(100) NOT NULL,
  `H_Email` varchar(100) NOT NULL,
  `H_Phone_Number` varchar(20) NOT NULL,
  `H_Address` varchar(200) NOT NULL,
  `Request_Status` varchar(20) DEFAULT 'Pending',
  `Request_Date` timestamp NOT NULL DEFAULT current_timestamp(),
  `Review_Date` timestamp NULL DEFAULT NULL,
  `Reviewed_By` varchar(30) DEFAULT NULL,
  `Rejection_Reason` varchar(255) DEFAULT NULL
) ;

--
-- Dumping data for table `hospital_registration_request`
--

INSERT INTO `hospital_registration_request` (`Request_ID`, `H_TIN`, `H_Name`, `H_Email`, `H_Phone_Number`, `H_Address`, `Request_Status`, `Request_Date`, `Review_Date`, `Reviewed_By`, `Rejection_Reason`) VALUES
(1, '123456789', 'Square Hospital', 'square@gmail.com', '01712345678', 'Dhaka', 'Approved', '2026-08-26 22:15:44', '2026-08-26 22:17:02', 'admin01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hospital_update_request`
--

CREATE TABLE `hospital_update_request` (
  `Update_Request_ID` int(11) NOT NULL,
  `H_TIN` varchar(30) NOT NULL,
  `New_H_Name` varchar(100) NOT NULL,
  `New_H_Email` varchar(100) NOT NULL,
  `New_H_Phone_Number` varchar(20) NOT NULL,
  `New_H_Address` varchar(200) NOT NULL,
  `Request_Status` varchar(20) DEFAULT 'Pending',
  `Request_Date` timestamp NOT NULL DEFAULT current_timestamp(),
  `Review_Date` timestamp NULL DEFAULT NULL,
  `Reviewed_By` varchar(30) DEFAULT NULL,
  `Rejection_Reason` varchar(255) DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `P_Username` varchar(30) NOT NULL,
  `P_Password` varchar(50) NOT NULL,
  `P_Date_of_Birth` date DEFAULT NULL,
  `P_Name` varchar(50) DEFAULT NULL,
  `P_Mobile_Number` varchar(15) DEFAULT NULL,
  `P_Blood_Group` varchar(5) DEFAULT NULL,
  `P_Address` varchar(100) DEFAULT NULL,
  `P_Email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`P_Username`, `P_Password`, `P_Date_of_Birth`, `P_Name`, `P_Mobile_Number`, `P_Blood_Group`, `P_Address`, `P_Email`) VALUES
('jahan01', 'jahan123', '2001-08-20', 'Jahan Alam', '01898765432', 'A+', 'Chittagong', 'jahan@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `Reserve_ID` int(11) NOT NULL,
  `P_Username` varchar(30) DEFAULT NULL,
  `Approval` varchar(10) DEFAULT NULL,
  `Reservation_Time` datetime DEFAULT NULL,
  `Blood_Group` varchar(5) DEFAULT NULL,
  `R_Number_of_Bags` int(11) DEFAULT NULL,
  `H_TIN` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`A_Username`);

--
-- Indexes for table `blood`
--
ALTER TABLE `blood`
  ADD PRIMARY KEY (`Blood_Group`);

--
-- Indexes for table `blood_bag`
--
ALTER TABLE `blood_bag`
  ADD PRIMARY KEY (`Blood_Bag_Id`),
  ADD KEY `Blood_Group` (`Blood_Group`),
  ADD KEY `D_Username` (`D_Username`),
  ADD KEY `FK_Blood_Bag_Hospital` (`H_TIN`);

--
-- Indexes for table `donor`
--
ALTER TABLE `donor`
  ADD PRIMARY KEY (`D_Username`),
  ADD KEY `Blood_Group` (`Blood_Group`),
  ADD KEY `FK_Donor_Last_Hospital` (`Last_Donation_Hospital_TIN`);

--
-- Indexes for table `hospital`
--
ALTER TABLE `hospital`
  ADD PRIMARY KEY (`H_TIN`),
  ADD UNIQUE KEY `H_Email` (`H_Email`);

--
-- Indexes for table `hospital_registration_request`
--
ALTER TABLE `hospital_registration_request`
  ADD PRIMARY KEY (`Request_ID`),
  ADD KEY `Reviewed_By` (`Reviewed_By`);

--
-- Indexes for table `hospital_update_request`
--
ALTER TABLE `hospital_update_request`
  ADD PRIMARY KEY (`Update_Request_ID`),
  ADD KEY `H_TIN` (`H_TIN`),
  ADD KEY `Reviewed_By` (`Reviewed_By`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`P_Username`),
  ADD KEY `P_Blood_Group` (`P_Blood_Group`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`Reserve_ID`),
  ADD KEY `P_Username` (`P_Username`),
  ADD KEY `Blood_Group` (`Blood_Group`),
  ADD KEY `FK_Reservation_Hospital` (`H_TIN`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hospital_registration_request`
--
ALTER TABLE `hospital_registration_request`
  MODIFY `Request_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_update_request`
--
ALTER TABLE `hospital_update_request`
  MODIFY `Update_Request_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blood_bag`
--
ALTER TABLE `blood_bag`
  ADD CONSTRAINT `FK_Blood_Bag_Hospital` FOREIGN KEY (`H_TIN`) REFERENCES `hospital` (`H_TIN`),
  ADD CONSTRAINT `blood_bag_ibfk_1` FOREIGN KEY (`Blood_Group`) REFERENCES `blood` (`Blood_Group`),
  ADD CONSTRAINT `blood_bag_ibfk_2` FOREIGN KEY (`D_Username`) REFERENCES `donor` (`D_Username`);

--
-- Constraints for table `donor`
--
ALTER TABLE `donor`
  ADD CONSTRAINT `FK_Donor_Last_Hospital` FOREIGN KEY (`Last_Donation_Hospital_TIN`) REFERENCES `hospital` (`H_TIN`),
  ADD CONSTRAINT `donor_ibfk_1` FOREIGN KEY (`Blood_Group`) REFERENCES `blood` (`Blood_Group`);

--
-- Constraints for table `hospital_registration_request`
--
ALTER TABLE `hospital_registration_request`
  ADD CONSTRAINT `hospital_registration_request_ibfk_1` FOREIGN KEY (`Reviewed_By`) REFERENCES `admin` (`A_Username`);

--
-- Constraints for table `hospital_update_request`
--
ALTER TABLE `hospital_update_request`
  ADD CONSTRAINT `hospital_update_request_ibfk_1` FOREIGN KEY (`H_TIN`) REFERENCES `hospital` (`H_TIN`),
  ADD CONSTRAINT `hospital_update_request_ibfk_2` FOREIGN KEY (`Reviewed_By`) REFERENCES `admin` (`A_Username`);

--
-- Constraints for table `patient`
--
ALTER TABLE `patient`
  ADD CONSTRAINT `patient_ibfk_1` FOREIGN KEY (`P_Blood_Group`) REFERENCES `blood` (`Blood_Group`);

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `FK_Reservation_Hospital` FOREIGN KEY (`H_TIN`) REFERENCES `hospital` (`H_TIN`),
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`P_Username`) REFERENCES `patient` (`P_Username`),
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`Blood_Group`) REFERENCES `blood` (`Blood_Group`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
