<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: login.html");
    exit();
}

$hospital_tin = mysqli_real_escape_string($conn, $_SESSION['hospital_tin'] ?? '');
$donor_username = mysqli_real_escape_string($conn, $_GET['username'] ?? '');

if (!empty($hospital_tin) && !empty($donor_username)) {
    // 1. Get latest record for this donor and hospital
    $checkSql = "SELECT Blood_Bag_Id, Number_of_Bags FROM blood_bag 
                 WHERE H_TIN = '$hospital_tin' AND D_Username = '$donor_username' 
                 ORDER BY Date_Blood_Added DESC, Blood_Bag_Id DESC LIMIT 1";
    
    $res = $conn->query($checkSql);

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $bag_id = $row['Blood_Bag_Id'];
        $bags = intval($row['Number_of_Bags']);

        if ($bags > 1) {
            // Deduct 1 bag
            $updateSql = "UPDATE blood_bag SET Number_of_Bags = Number_of_Bags - 1 WHERE Blood_Bag_Id = '$bag_id'";
            $conn->query($updateSql);
        } else {
            // If only 1 bag left, delete the entry
            $deleteSql = "DELETE FROM blood_bag WHERE Blood_Bag_Id = '$bag_id'";
            $conn->query($deleteSql);
        }

        echo "<script>alert('1 Blood bag deducted successfully!'); window.location.href='donorlist.php';</script>";
        exit();
    } else {
        echo "<script>alert('No record found for this donor!'); window.location.href='donorlist.php';</script>";
        exit();
    }
} else {
    header("Location: donorlist.php");
    exit();
}
?>