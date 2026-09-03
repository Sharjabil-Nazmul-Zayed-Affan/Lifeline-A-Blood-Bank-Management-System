CREATE TABLE Blood (
    Blood_Group VARCHAR(5) PRIMARY KEY
);

CREATE TABLE Donor (
    D_Username VARCHAR(30) PRIMARY KEY,
    D_Name VARCHAR(50) NOT NULL,
    D_Password VARCHAR(50) NOT NULL,
    D_Date_of_Birth DATE,
    D_Gender VARCHAR(10),
    Blood_Group VARCHAR(5),
    D_Mobile_Number VARCHAR(15),
    D_Address VARCHAR(100),
    D_Email VARCHAR(50),
    Total_Given_Bloodbags INT DEFAULT 0,
    Last_Date_of_Donation DATE,
    FOREIGN KEY (Blood_Group) REFERENCES Blood(Blood_Group)
);

CREATE TABLE Blood_Bag (
    Blood_Bag_Id INT PRIMARY KEY,
    Blood_Group VARCHAR(5),
    Number_of_Bags INT,
    D_Username VARCHAR(30),
    Date_Blood_Added DATE,
    FOREIGN KEY (Blood_Group) REFERENCES Blood(Blood_Group)
);

CREATE TABLE Admin (
    A_Username VARCHAR(30) PRIMARY KEY,
    Password VARCHAR(50) NOT NULL
);

CREATE TABLE Patient (
    P_Username VARCHAR(30) PRIMARY KEY,
    P_Password VARCHAR(50) NOT NULL,
    P_Date_of_Birth DATE,
    P_Name VARCHAR(50),
    P_Mobile_Number VARCHAR(15),
    P_Blood_Group VARCHAR(5),
    P_Address VARCHAR(100),
    P_Email VARCHAR(50),
    P_Photo VARCHAR(255),
    FOREIGN KEY (P_Blood_Group) REFERENCES Blood(Blood_Group)
);

CREATE TABLE Reservation (
    Reserve_ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    P_Username VARCHAR(30),
    Approval VARCHAR(10),
    Reservation_Time DATETIME,
    Needed_Date DATE,
    Blood_Group VARCHAR(5),
    R_Number_of_Bags INT,
    Hospital_Name VARCHAR(100),
    Hospital_Location VARCHAR(100),
    FOREIGN KEY (P_Username) REFERENCES Patient(P_Username),
    FOREIGN KEY (Blood_Group) REFERENCES Blood(Blood_Group)
);

INSERT INTO Blood (Blood_Group) VALUES ('A+');
INSERT INTO Blood (Blood_Group) VALUES ('A-');
INSERT INTO Blood (Blood_Group) VALUES ('AB+');
INSERT INTO Blood (Blood_Group) VALUES ('AB-');
INSERT INTO Blood (Blood_Group) VALUES ('B+');
INSERT INTO Blood (Blood_Group) VALUES ('B-');
INSERT INTO Blood (Blood_Group) VALUES ('O+');
INSERT INTO Blood (Blood_Group) VALUES ('O-');

INSERT INTO Donor (D_Username, D_Name, D_Password, D_Date_of_Birth, D_Gender, Blood_Group, D_Mobile_Number, D_Address, D_Email, Total_Given_Bloodbags, Last_Date_of_Donation)
VALUES ('rahim01', 'Abdur Rahim', 'rahim123', '1995-05-12', 'Male', 'A+', '01711122233', 'Dhaka, Bangladesh', 'rahim01@example.com', 5, '2025-01-10');

INSERT INTO Blood_Bag (Blood_Bag_Id, Blood_Group, Number_of_Bags, D_Username, Date_Blood_Added)
VALUES (1, 'A+', 5, 'rahim01', '2025-01-10');
INSERT INTO Blood_Bag (Blood_Bag_Id, Blood_Group, Number_of_Bags, D_Username, Date_Blood_Added)
VALUES (2, 'B+', 3, NULL, '2025-02-15');
INSERT INTO Blood_Bag (Blood_Bag_Id, Blood_Group, Number_of_Bags, D_Username, Date_Blood_Added)
VALUES (3, 'O+', 7, NULL, '2025-03-01');
INSERT INTO Blood_Bag (Blood_Bag_Id, Blood_Group, Number_of_Bags, D_Username, Date_Blood_Added)
VALUES (4, 'AB+', 2, NULL, '2025-01-20');
INSERT INTO Blood_Bag (Blood_Bag_Id, Blood_Group, Number_of_Bags, D_Username, Date_Blood_Added)
VALUES (5, 'A-', 4, NULL, '2025-04-10');
INSERT INTO Blood_Bag (Blood_Bag_Id, Blood_Group, Number_of_Bags, D_Username, Date_Blood_Added)
VALUES (6, 'O-', 6, NULL, '2025-02-28');
INSERT INTO Blood_Bag (Blood_Bag_Id, Blood_Group, Number_of_Bags, D_Username, Date_Blood_Added)
VALUES (7, 'B-', 1, NULL, '2025-05-05');
INSERT INTO Blood_Bag (Blood_Bag_Id, Blood_Group, Number_of_Bags, D_Username, Date_Blood_Added)
VALUES (8, 'AB-', 3, NULL, '2025-03-15');

INSERT INTO Admin (A_Username, Password) VALUES ('admin01', 'admin123');

INSERT INTO Patient (P_Username, P_Password, P_Date_of_Birth, P_Name, P_Mobile_Number, P_Blood_Group, P_Address, P_Email, P_Gender, P_Photo)
VALUES ('jahan01', 'jahan123', '1998-08-20', 'Nusrat Jahan', '01899988877', 'A+', 'Chittagong, Bangladesh', 'jahan01@example.com', NULL, 'patient_1788373289_9369.jpg');

INSERT INTO Patient (P_Username, P_Password, P_Date_of_Birth, P_Name, P_Mobile_Number, P_Blood_Group, P_Address, P_Email, P_Gender, P_Photo)
VALUES ('shihab', '$2y$10$m55AvAN57NePYFzG.QM1duFjJLQ/Id4P.oVLziQ/IUP6RCIGc0he.', '1995-07-05', 'Shihab Hasan', '01794569667', 'AB-', 'Kuratoli', 'shihabz0098@gmail.com', NULL, 'patient_1788408329_4333.png');

INSERT INTO Reservation (Reserve_ID, P_Username, Approval, Reservation_Time, Needed_Date, Blood_Group, R_Number_of_Bags, Hospital_Name, Hospital_Location, Stock_Deducted)
VALUES (1, 'jahan01', 'Pending', '2026-08-27 10:01:41', '2026-08-30', 'A+', 1, NULL, NULL, 0);

INSERT INTO Reservation (Reserve_ID, P_Username, Approval, Reservation_Time, Needed_Date, Blood_Group, R_Number_of_Bags, Hospital_Name, Hospital_Location, Stock_Deducted)
VALUES (15, 'shihab', 'Pending', '2026-09-03 10:07:06', '2026-09-10', 'AB+', 1, 'Sylhet MAG Osmani Medical College', 'Sylhet', 0);

