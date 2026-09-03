<?php
session_start();
require_once '../Model/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tin = mysqli_real_escape_string($conn, $_POST['hospital_tin'] ?? '');

    if (!empty($tin)) {
        $sql = "SELECT * FROM hospital WHERE H_TIN = '$tin'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            $_SESSION['isLoggedIn'] = true;
            $_SESSION['hospital_tin'] = $row['H_TIN'];
            $_SESSION['hospital_name'] = $row['H_Name'];
            
            header("Location: ../View/dashboard.php");
            exit();
        } else {
            echo "<script>alert('Hospital TIN not found!'); window.location.href='../View/login.html';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Please enter Hospital TIN!'); window.location.href='../View/login.html';</script>";
        exit();
    }
} else {
    header("Location: ../View/login.html");
    exit();
}
?>