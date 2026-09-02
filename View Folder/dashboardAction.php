<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $hospital_tin = $_SESSION['hospital_tin'] ?? '';
    $blood_group  = trim($_POST['bloodGroup'] ?? '');
    $quantity     = intval($_POST['quantity'] ?? 0);
    $donor_uname  = trim($_POST['donorUsername'] ?? $_POST['d_username'] ?? '');
    $date_added   = date('Y-m-d');

    if (!empty($hospital_tin) && !empty($blood_group) && $quantity > 0) {

        $blood_group = mysqli_real_escape_string($conn, $blood_group);
        $donor_uname = mysqli_real_escape_string($conn, $donor_uname);

        $d_user_val = !empty($donor_uname) ? "'$donor_uname'" : "NULL";

        $getMaxId = $conn->query("SELECT MAX(Blood_Bag_Id) AS max_id FROM blood_bag");
        $row = $getMaxId->fetch_assoc();
        $next_id = ($row['max_id'] !== null) ? $row['max_id'] + 1 : 1;

        $sql = "INSERT INTO blood_bag (Blood_Bag_Id, Blood_Group, Number_of_Bags, D_Username, Date_Blood_Added, H_TIN, Status) 
                VALUES ('$next_id', '$blood_group', '$quantity', $d_user_val, '$date_added', '$hospital_tin', 'Available')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Blood Bag Added Successfully!'); window.location.href='dashboard.php';</script>";
            exit();
        } else {
            echo "<script>alert('Failed to add blood bag: " . $conn->error . "'); window.location.href='dashboard.php';</script>";
            exit();
        }

    } else {
        echo "<script>alert('Please fill in all required fields!'); window.location.href='dashboard.php';</script>";
        exit();
    }

} else {
    header("Location: dashboard.php");
    exit();
}
?>