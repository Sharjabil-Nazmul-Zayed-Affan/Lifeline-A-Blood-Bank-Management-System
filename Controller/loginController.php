<?php
session_start();
require_once '../Model/adminModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $remember = isset($_POST['remember_me']);

    if (empty($username) || empty($password)) {
        header("Location: ../View/login.php?error=" . urlencode("Please enter both username and password."));
        exit();
    }

    $admin = checkAdminLogin($conn, $username, $password);

    if ($admin) {
        $_SESSION['admin_user'] = $admin['A_Username'];

        if ($remember) {
            setcookie('admin_user', $admin['A_Username'], time() + (86400 * 7), "/");
        } else {
            setcookie('admin_user', '', time() - 3600, "/");
        }

        header("Location: ../View/dashboard.php");
        exit();
    } else {
        header("Location: ../View/login.php?error=" . urlencode("Invalid Username or Password!"));
        exit();
    }
} else {
    header("Location: ../View/login.php");
    exit();
}
?>
