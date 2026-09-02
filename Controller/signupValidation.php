<?php
session_start();
require_once '../Model/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $h_name    = trim($_POST['hospitalName'] ?? '');
    $h_tin     = trim($_POST['hospitalTIN'] ?? '');
    $h_email   = trim($_POST['hospitalEmail'] ?? '');
    $h_address = trim($_POST['hospitalAddress'] ?? '');
    
    $phone_number   = $_POST['hospitalPhone'] ?? 'N/A';
    $request_status = 'Pending';
    $request_date   = date('Y-m-d H:i:s');

    if (!empty($h_name) && !empty($h_tin) && !empty($h_email) && !empty($h_address)) {
        
        $h_name    = mysqli_real_escape_string($conn, $h_name);
        $h_tin     = mysqli_real_escape_string($conn, $h_tin);
        $h_email   = mysqli_real_escape_string($conn, $h_email);
        $h_address = mysqli_real_escape_string($conn, $h_address);

        $checkTin = $conn->query("SELECT H_TIN FROM hospital_registration_request WHERE H_TIN = '$h_tin'");
        
        if ($checkTin && $checkTin->num_rows > 0) {
            echo "<script>alert('This Hospital TIN is already submitted for registration!'); window.location.href='../View/signup.html';</script>";
            exit();
        }

        $sql = "INSERT INTO hospital_registration_request (H_TIN, H_Name, H_Email, H_Phone_Number, H_Address, Request_Status, Request_Date) 
                VALUES ('$h_tin', '$h_name', '$h_email', '$phone_number', '$h_address', '$request_status', '$request_date')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration Request Submitted Successfully!'); window.location.href='../View/login.html';</script>";
            exit();
        } else {
            echo "Database Insert Error: " . $conn->error;
        }

    } else {
        echo "<script>alert('Please fill in all required fields!'); window.location.href='../View/signup.html';</script>";
        exit();
    }
} else {
    header("Location: ../View/signup.html");
    exit();
}
?>