<?php
session_start();
require_once '../Model/db.php';

if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: ../View/login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hospital_tin  = mysqli_real_escape_string($conn, $_POST['hospital_tin'] ?? $_SESSION['hospital_tin'] ?? '');
    $hospital_name = mysqli_real_escape_string($conn, trim($_POST['hospitalName'] ?? ''));
    $address       = mysqli_real_escape_string($conn, trim($_POST['address'] ?? ''));
    $email         = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));

    if (!empty($hospital_tin) && !empty($hospital_name) && !empty($address) && !empty($email)) {
        
        $sql = "UPDATE hospital SET H_Name = '$hospital_name', H_Address = '$address', H_Email = '$email' WHERE H_TIN = '$hospital_tin'";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['hospital_name'] = $hospital_name;
            echo "<script>alert('Profile updated successfully!'); window.location.href='../View/dashboard.php';</script>";
            exit();
        } else {
            echo "<script>alert('Failed to update profile: " . $conn->error . "'); window.location.href='../View/editprofile.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Please fill in all required fields!'); window.location.href='../View/editprofile.php';</script>";
        exit();
    }
} else {
    header("Location: ../View/dashboard.php");
    exit();
}
?>