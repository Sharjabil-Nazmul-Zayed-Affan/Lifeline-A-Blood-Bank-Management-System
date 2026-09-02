<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $tin = trim($_POST['tin'] ?? $_POST['hospitalTIN'] ?? '');

    if (!empty($tin)) {
        $tin = mysqli_real_escape_string($conn, $tin);
        
        $sql = "SELECT * FROM hospital WHERE H_TIN = '$tin'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            $_SESSION['isLoggedIn'] = true;
            $_SESSION['hospital_tin'] = $row['H_TIN'];
            $_SESSION['hospital_name'] = $row['H_Name'];

            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Hospital TIN not found or not approved yet!'); window.location.href='login.html';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Please enter Hospital TIN!'); window.location.href='login.html';</script>";
        exit();
    }
} else {
    header("Location: login.html");
    exit();
}
?>