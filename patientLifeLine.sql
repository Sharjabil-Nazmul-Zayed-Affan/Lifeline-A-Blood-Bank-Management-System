-- Life Line Blood Bank Database Schema
-- Database: life_line

CREATE DATABASE IF NOT EXISTS `life_line` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `life_line`;

-- --------------------------------------------------------
-- Table structure for table `Blood`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Blood` (
    `Blood_Group` VARCHAR(5) NOT NULL,
    PRIMARY KEY (`Blood_Group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `Donor`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Donor` (
    `D_Username` VARCHAR(30) NOT NULL,
    `D_Name` VARCHAR(50) NOT NULL,
    `D_Password` VARCHAR(255) NOT NULL,
    `D_Date_of_Birth` DATE NULL,
    `D_Gender` VARCHAR(10) NULL,
    `Blood_Group` VARCHAR(5) NULL,
    `D_Mobile_Number` VARCHAR(15) NULL,
    `D_Address` VARCHAR(100) NULL,
    `D_Email` VARCHAR(50) NULL,
    `Total_Given_Bloodbags` INT DEFAULT 0,
    `Last_Date_of_Donation` DATE NULL,
    PRIMARY KEY (`D_Username`),
    CONSTRAINT `fk_donor_blood_group` FOREIGN KEY (`Blood_Group`) REFERENCES `Blood` (`Blood_Group`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `Blood_Bag`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Blood_Bag` (
    `Blood_Bag_Id` INT NOT NULL AUTO_INCREMENT,
    `Blood_Group` VARCHAR(5) NULL,
    `Number_of_Bags` INT NOT NULL DEFAULT 1,
    `D_Username` VARCHAR(30) NULL,
    `Date_Blood_Added` DATE NULL,
    PRIMARY KEY (`Blood_Bag_Id`),
    CONSTRAINT `fk_bloodbag_blood_group` FOREIGN KEY (`Blood_Group`) REFERENCES `Blood` (`Blood_Group`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_bloodbag_donor` FOREIGN KEY (`D_Username`) REFERENCES `Donor` (`D_Username`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `Admin`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Admin` (
    `A_Username` VARCHAR(30) NOT NULL,
    `Password` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`A_Username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `Patient`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Patient` (
    `P_Username` VARCHAR(30) NOT NULL,
    `P_Password` VARCHAR(255) NOT NULL,
    `P_Date_of_Birth` DATE NULL,
    `P_Name` VARCHAR(50) NULL,
    `P_Mobile_Number` VARCHAR(15) NULL,
    `P_Blood_Group` VARCHAR(5) NULL,
    `P_Address` VARCHAR(100) NULL,
    `P_Email` VARCHAR(50) NULL,
    `P_Gender` VARCHAR(10) NULL,
    `P_Photo` VARCHAR(255) NULL,
    PRIMARY KEY (`P_Username`),
    CONSTRAINT `fk_patient_blood_group` FOREIGN KEY (`P_Blood_Group`) REFERENCES `Blood` (`Blood_Group`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `Reservation`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Reservation` (
    `Reserve_ID` INT NOT NULL AUTO_INCREMENT,
    `P_Username` VARCHAR(30) NOT NULL,
    `Approval` VARCHAR(10) DEFAULT 'Pending',
    `Reservation_Time` DATETIME NOT NULL,
    `Blood_Group` VARCHAR(5) NULL,
    `R_Number_of_Bags` INT NOT NULL DEFAULT 1,
    `Needed_Date` DATE NULL,
    `Hospital_Name` VARCHAR(100) NULL,
    `Hospital_Location` VARCHAR(100) NULL,
    `Stock_Deducted` TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`Reserve_ID`),
    CONSTRAINT `fk_reservation_patient` FOREIGN KEY (`P_Username`) REFERENCES `Patient` (`P_Username`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_reservation_blood_group` FOREIGN KEY (`Blood_Group`) REFERENCES `Blood` (`Blood_Group`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Sample Data Insertion
-- --------------------------------------------------------

-- Insert all 8 blood groups
INSERT INTO `Blood` (`Blood_Group`) VALUES
('A+'),
('A-'),
('B+'),
('B-'),
('AB+'),
('AB-'),
('O+'),
('O-')
ON DUPLICATE KEY UPDATE `Blood_Group` = VALUES(`Blood_Group`);

-- Insert sample donor (plain text password: rahim123)
INSERT INTO `Donor` (`D_Username`, `D_Name`, `D_Password`, `D_Date_of_Birth`, `D_Gender`, `Blood_Group`, `D_Mobile_Number`, `D_Address`, `D_Email`, `Total_Given_Bloodbags`, `Last_Date_of_Donation`) VALUES
('rahim01', 'Abdur Rahim', 'rahim123', '1995-05-12', 'Male', 'A+', '01711122233', 'Dhaka, Bangladesh', 'rahim01@example.com', 5, '2025-01-10')
ON DUPLICATE KEY UPDATE `D_Name` = VALUES(`D_Name`);

-- Insert sample blood bags
INSERT INTO `Blood_Bag` (`Blood_Bag_Id`, `Blood_Group`, `Number_of_Bags`, `D_Username`, `Date_Blood_Added`) VALUES
(1, 'A+', 5, 'rahim01', '2025-01-10'),
(2, 'B+', 3, NULL, '2025-02-15'),
(3, 'O+', 7, NULL, '2025-03-01'),
(4, 'AB+', 2, NULL, '2025-01-20'),
(5, 'A-', 4, NULL, '2025-04-10'),
(6, 'O-', 6, NULL, '2025-02-28'),
(7, 'B-', 1, NULL, '2025-05-05'),
(8, 'AB-', 3, NULL, '2025-03-15')
ON DUPLICATE KEY UPDATE `Number_of_Bags` = VALUES(`Number_of_Bags`);

-- Insert sample admin (password: admin123)
INSERT INTO `Admin` (`A_Username`, `Password`) VALUES
('admin01', 'admin123')
ON DUPLICATE KEY UPDATE `Password` = VALUES(`Password`);

-- Insert sample patient (jahan01, password: jahan123)
-- Note: Patient::verifyPassword handles both bcrypt-hashed passwords and plain text legacy passwords
INSERT INTO `Patient` (`P_Username`, `P_Password`, `P_Date_of_Birth`, `P_Name`, `P_Mobile_Number`, `P_Blood_Group`, `P_Address`, `P_Email`) VALUES
('jahan01', 'jahan123', '1998-08-20', 'Nusrat Jahan', '01899988877', 'A+', 'Chittagong, Bangladesh', 'jahan01@example.com')
ON DUPLICATE KEY UPDATE `P_Name` = VALUES(`P_Name`);

-- Insert sample reservation
INSERT INTO `Reservation` (`Reserve_ID`, `P_Username`, `Approval`, `Reservation_Time`, `Needed_Date`, `Blood_Group`, `R_Number_of_Bags`, `Hospital_Name`, `Hospital_Location`) VALUES
(1, 'jahan01', 'Pending', NOW(), CURDATE(), 'A+', 1, 'Square Hospitals Ltd.', 'Panthapath, Dhaka')
ON DUPLICATE KEY UPDATE `Approval` = VALUES(`Approval`);


-- Approval stock handling: Pending -> Approved deducts stock; Rejected keeps stock unchanged.
-- Hospital mapping matches Model/patientBloodModel.php: MOD(Blood_Bag_Id, 10) maps to the hospital directory order.
DROP PROCEDURE IF EXISTS patientDeductApprovedReservationStock;
DELIMITER $$
CREATE PROCEDURE patientDeductApprovedReservationStock(IN p_hospital VARCHAR(100), IN p_blood_group VARCHAR(5), IN p_quantity INT)
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE v_id INT;
    DECLARE v_available INT;
    DECLARE v_take INT;
    DECLARE v_remaining INT DEFAULT p_quantity;
    DECLARE cur CURSOR FOR
        SELECT Blood_Bag_Id, Number_of_Bags
        FROM Blood_Bag
        WHERE Blood_Group = p_blood_group
          AND Number_of_Bags > 0
          AND (
              (MOD(Blood_Bag_Id, 10) = 0 AND p_hospital = 'Dhaka Medical College Hospital') OR
              (MOD(Blood_Bag_Id, 10) = 1 AND p_hospital = 'Square Hospitals Ltd.') OR
              (MOD(Blood_Bag_Id, 10) = 2 AND p_hospital = 'Chittagong General Hospital') OR
              (MOD(Blood_Bag_Id, 10) = 3 AND p_hospital = 'Rajshahi Medical College Hospital') OR
              (MOD(Blood_Bag_Id, 10) = 4 AND p_hospital = 'Sylhet MAG Osmani Medical College') OR
              (MOD(Blood_Bag_Id, 10) = 5 AND p_hospital = 'Khulna Medical College Hospital') OR
              (MOD(Blood_Bag_Id, 10) = 6 AND p_hospital = 'Comilla Medical College Hospital') OR
              (MOD(Blood_Bag_Id, 10) = 7 AND p_hospital = 'Rangpur Medical College Hospital') OR
              (MOD(Blood_Bag_Id, 10) = 8 AND p_hospital = 'Apollo Hospitals Dhaka') OR
              (MOD(Blood_Bag_Id, 10) = 9 AND p_hospital = 'Barisal Sher-e-Bangla Medical College')
          )
        ORDER BY Blood_Bag_Id ASC;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    IF p_quantity <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Reservation quantity must be greater than zero';
    END IF;

    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO v_id, v_available;
        IF done = 1 OR v_remaining <= 0 THEN
            LEAVE read_loop;
        END IF;
        SET v_take = LEAST(v_available, v_remaining);
        UPDATE Blood_Bag SET Number_of_Bags = Number_of_Bags - v_take WHERE Blood_Bag_Id = v_id;
        SET v_remaining = v_remaining - v_take;
    END LOOP;
    CLOSE cur;

    IF v_remaining > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Not enough blood stock for this approved reservation';
    END IF;
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS patientReservationApprovalStock;
DELIMITER $$
CREATE TRIGGER patientReservationApprovalStock
BEFORE UPDATE ON Reservation
FOR EACH ROW
BEGIN
    IF OLD.Approval <> 'Approved' AND NEW.Approval = 'Approved' AND NEW.Stock_Deducted = 0 THEN
        CALL patientDeductApprovedReservationStock(NEW.Hospital_Name, NEW.Blood_Group, NEW.R_Number_of_Bags);
        SET NEW.Stock_Deducted = 1;
    END IF;
END$$
DELIMITER ;
