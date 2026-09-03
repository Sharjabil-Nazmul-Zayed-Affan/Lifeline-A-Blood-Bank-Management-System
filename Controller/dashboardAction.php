<?php
session_start();
require_once '../Model/db.php';

if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: ../View/login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hospital_tin = $_SESSION['hospital_tin'] ?? '';
    $blood_group  = mysqli_real_escape_string($conn, $_POST['bloodGroup'] ?? '');
    $quantity     = intval($_POST['quantity'] ?? 0);
    $donor_uname  = mysqli_real_escape_string($conn, trim($_POST['donorUsername'] ?? ''));

    if (!empty($blood_group) && $quantity > 0) {
        $today = date('Y-m-d');
        
        $sql = "INSERT INTO blood_bag (H_TIN, Blood_Group, Number_of_Bags, D_Username, Date_Blood_Added) 
                VALUES ('$hospital_tin', '$blood_group', '$quantity', '$donor_uname', '$today')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Blood bag added successfully!'); window.location.href='../View/dashboard.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error: " . $conn->error . "'); window.location.href='../View/dashboard.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Please fill required fields properly!'); window.location.href='../View/dashboard.php';</script>";
        exit();
    }
} else {
    header("Location: ../View/dashboard.php");
    exit();
}
?>