<?php
// Model/patientDb.php - Database connection and SQL file auto-sync for LifeLine Blood Bank Management System

function getDBConnection() {
    static $conn = null;
    if ($conn === null) {
        $host = '127.0.0.1';
        $db   = ' lifeline: a blood bank mangement system';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $conn = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    return $conn;
}

/**
 * Automatically syncs the patientSQLQuery1.sql file with the live data from MySQL
 */
function syncSQLFile() {
    try {
        $db = getDBConnection();
        $sqlPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'patientSQLQuery1.sql';

        $out = "";
        $out .= "CREATE TABLE Blood (\n";
        $out .= "    Blood_Group VARCHAR(5) PRIMARY KEY\n";
        $out .= ");\n\n";

        $out .= "CREATE TABLE Donor (\n";
        $out .= "    D_Username VARCHAR(30) PRIMARY KEY,\n";
        $out .= "    D_Name VARCHAR(50) NOT NULL,\n";
        $out .= "    D_Password VARCHAR(50) NOT NULL,\n";
        $out .= "    D_Date_of_Birth DATE,\n";
        $out .= "    D_Gender VARCHAR(10),\n";
        $out .= "    Blood_Group VARCHAR(5),\n";
        $out .= "    D_Mobile_Number VARCHAR(15),\n";
        $out .= "    D_Address VARCHAR(100),\n";
        $out .= "    D_Email VARCHAR(50),\n";
        $out .= "    Total_Given_Bloodbags INT DEFAULT 0,\n";
        $out .= "    Last_Date_of_Donation DATE,\n";
        $out .= "    FOREIGN KEY (Blood_Group) REFERENCES Blood(Blood_Group)\n";
        $out .= ");\n\n";

        $out .= "CREATE TABLE Blood_Bag (\n";
        $out .= "    Blood_Bag_Id INT PRIMARY KEY,\n";
        $out .= "    Blood_Group VARCHAR(5),\n";
        $out .= "    Number_of_Bags INT,\n";
        $out .= "    D_Username VARCHAR(30),\n";
        $out .= "    Date_Blood_Added DATE,\n";
        $out .= "    FOREIGN KEY (Blood_Group) REFERENCES Blood(Blood_Group)\n";
        $out .= ");\n\n";

        $out .= "CREATE TABLE Admin (\n";
        $out .= "    A_Username VARCHAR(30) PRIMARY KEY,\n";
        $out .= "    Password VARCHAR(50) NOT NULL\n";
        $out .= ");\n\n";

        $out .= "CREATE TABLE Patient (\n";
        $out .= "    P_Username VARCHAR(30) PRIMARY KEY,\n";
        $out .= "    P_Password VARCHAR(50) NOT NULL,\n";
        $out .= "    P_Date_of_Birth DATE,\n";
        $out .= "    P_Name VARCHAR(50),\n";
        $out .= "    P_Mobile_Number VARCHAR(15),\n";
        $out .= "    P_Blood_Group VARCHAR(5),\n";
        $out .= "    P_Address VARCHAR(100),\n";
        $out .= "    P_Email VARCHAR(50),\n";
        $out .= "    P_Photo VARCHAR(255),\n";
        $out .= "    FOREIGN KEY (P_Blood_Group) REFERENCES Blood(Blood_Group)\n";
        $out .= ");\n\n";

        $out .= "CREATE TABLE Reservation (\n";
        $out .= "    Reserve_ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,\n";
        $out .= "    P_Username VARCHAR(30),\n";
        $out .= "    Approval VARCHAR(10),\n";
        $out .= "    Reservation_Time DATETIME,\n";
        $out .= "    Needed_Date DATE,\n";
        $out .= "    Blood_Group VARCHAR(5),\n";
        $out .= "    R_Number_of_Bags INT,\n";
        $out .= "    Hospital_Name VARCHAR(100),\n";
        $out .= "    Hospital_Location VARCHAR(100),\n";
        $out .= "    FOREIGN KEY (P_Username) REFERENCES Patient(P_Username),\n";
        $out .= "    FOREIGN KEY (Blood_Group) REFERENCES Blood(Blood_Group)\n";
        $out .= ");\n\n";

        // Blood
        $bloodRows = $db->query("SELECT * FROM Blood")->fetchAll();
        foreach ($bloodRows as $row) {
            $val = addslashes($row['Blood_Group']);
            $out .= "INSERT INTO Blood (Blood_Group) VALUES ('$val');\n";
        }
        $out .= "\n";

        // Donor
        $donorRows = $db->query("SELECT * FROM Donor")->fetchAll();
        foreach ($donorRows as $row) {
            $u = addslashes($row['D_Username']);
            $n = addslashes($row['D_Name']);
            $p = addslashes($row['D_Password']);
            $dob = $row['D_Date_of_Birth'] ? "'" . $row['D_Date_of_Birth'] . "'" : "NULL";
            $g = $row['D_Gender'] ? "'" . addslashes($row['D_Gender']) . "'" : "NULL";
            $bg = $row['Blood_Group'] ? "'" . addslashes($row['Blood_Group']) . "'" : "NULL";
            $mob = $row['D_Mobile_Number'] ? "'" . addslashes($row['D_Mobile_Number']) . "'" : "NULL";
            $addr = $row['D_Address'] ? "'" . addslashes($row['D_Address']) . "'" : "NULL";
            $em = $row['D_Email'] ? "'" . addslashes($row['D_Email']) . "'" : "NULL";
            $tot = (int)($row['Total_Given_Bloodbags'] ?? 0);
            $last = $row['Last_Date_of_Donation'] ? "'" . $row['Last_Date_of_Donation'] . "'" : "NULL";

            $out .= "INSERT INTO Donor (D_Username, D_Name, D_Password, D_Date_of_Birth, D_Gender, Blood_Group, D_Mobile_Number, D_Address, D_Email, Total_Given_Bloodbags, Last_Date_of_Donation)\n";
            $out .= "VALUES ('$u', '$n', '$p', $dob, $g, $bg, $mob, $addr, $em, $tot, $last);\n\n";
        }

        // Blood_Bag
        $bagRows = $db->query("SELECT * FROM Blood_Bag")->fetchAll();
        foreach ($bagRows as $row) {
            $id = (int)$row['Blood_Bag_Id'];
            $bg = $row['Blood_Group'] ? "'" . addslashes($row['Blood_Group']) . "'" : "NULL";
            $num = (int)$row['Number_of_Bags'];
            $du = $row['D_Username'] ? "'" . addslashes($row['D_Username']) . "'" : "NULL";
            $date = $row['Date_Blood_Added'] ? "'" . $row['Date_Blood_Added'] . "'" : "NULL";

            $out .= "INSERT INTO Blood_Bag (Blood_Bag_Id, Blood_Group, Number_of_Bags, D_Username, Date_Blood_Added)\n";
            $out .= "VALUES ($id, $bg, $num, $du, $date);\n";
        }
        $out .= "\n";

        // Admin
        $adminRows = $db->query("SELECT * FROM Admin")->fetchAll();
        foreach ($adminRows as $row) {
            $au = addslashes($row['A_Username']);
            $ap = addslashes($row['Password']);
            $out .= "INSERT INTO Admin (A_Username, Password) VALUES ('$au', '$ap');\n";
        }
        $out .= "\n";

        // Patient
        $patientRows = $db->query("SELECT * FROM Patient")->fetchAll();
        foreach ($patientRows as $row) {
            $pu = addslashes($row['P_Username']);
            $pp = addslashes($row['P_Password']);
            $pdob = $row['P_Date_of_Birth'] ? "'" . $row['P_Date_of_Birth'] . "'" : "NULL";
            $pn = $row['P_Name'] ? "'" . addslashes($row['P_Name']) . "'" : "NULL";
            $pm = $row['P_Mobile_Number'] ? "'" . addslashes($row['P_Mobile_Number']) . "'" : "NULL";
            $pbg = $row['P_Blood_Group'] ? "'" . addslashes($row['P_Blood_Group']) . "'" : "NULL";
            $paddr = $row['P_Address'] ? "'" . addslashes($row['P_Address']) . "'" : "NULL";
            $pem = $row['P_Email'] ? "'" . addslashes($row['P_Email']) . "'" : "NULL";
            $pph = !empty($row['P_Photo']) ? "'" . addslashes($row['P_Photo']) . "'" : "NULL";
            $pg = !empty($row['P_Gender']) ? "'" . addslashes($row['P_Gender']) . "'" : "NULL";

            $out .= "INSERT INTO Patient (P_Username, P_Password, P_Date_of_Birth, P_Name, P_Mobile_Number, P_Blood_Group, P_Address, P_Email, P_Gender, P_Photo)\n";
            $out .= "VALUES ('$pu', '$pp', $pdob, $pn, $pm, $pbg, $paddr, $pem, $pg, $pph);\n\n";
        }

        // Reservation
        $resRows = $db->query("SELECT * FROM Reservation")->fetchAll();
        foreach ($resRows as $row) {
            $rid = (int)$row['Reserve_ID'];
            $ru = "'" . addslashes($row['P_Username']) . "'";
            $rapp = "'" . addslashes($row['Approval'] ?? 'Pending') . "'";
            $rtime = "'" . $row['Reservation_Time'] . "'";
            $rdate = !empty($row['Needed_Date']) ? ("'" . $row['Needed_Date'] . "'") : "NULL";
            $rbg = "'" . addslashes($row['Blood_Group']) . "'";
            $rnum = (int)$row['R_Number_of_Bags'];
            $rhname = !empty($row['Hospital_Name']) ? ("'" . addslashes($row['Hospital_Name']) . "'") : "NULL";
            $rhloc = !empty($row['Hospital_Location']) ? ("'" . addslashes($row['Hospital_Location']) . "'") : "NULL";
            $rdeducted = (int)($row['Stock_Deducted'] ?? 0);

            $out .= "INSERT INTO Reservation (Reserve_ID, P_Username, Approval, Reservation_Time, Needed_Date, Blood_Group, R_Number_of_Bags, Hospital_Name, Hospital_Location, Stock_Deducted)\n";
            $out .= "VALUES ($rid, $ru, $rapp, $rtime, $rdate, $rbg, $rnum, $rhname, $rhloc, $rdeducted);\n\n";
        }

        file_put_contents($sqlPath, $out);
    } catch (Exception $e) {
        // Silently fail if file write fails
    }
}
?>
