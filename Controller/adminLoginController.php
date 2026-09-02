<?php
// ==========================================
// Controller/adminLoginController.php
// Supports both AJAX and standard POST login
// ==========================================

session_start();
require_once '../Model/adminModel.php';

$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || isset($_POST['ajax']) 
          || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $remember = isset($_POST['remember_me']) && ($_POST['remember_me'] == '1' || $_POST['remember_me'] == 'on');

    // 1. Validation - check empty
    if (empty($username) || empty($password)) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Please enter both username and password.']);
            exit();
        } else {
            header("Location: ../View/adminLogin.php?error=" . urlencode("Please enter both username and password."));
            exit();
        }
    }

    // 2. Validate against Database (Model)
    $admin = checkAdminLogin($conn, $username, $password);

    if ($admin) {
        // Set Session
        $_SESSION['admin_user'] = $admin['A_Username'];

        // Handle Cookie (Remember Me)
        if ($remember) {
            setcookie('admin_user', $admin['A_Username'], time() + (86400 * 7), "/"); // 7 days
        } else {
            setcookie('admin_user', '', time() - 3600, "/");
        }

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success', 
                'message' => 'Login successful! Redirecting...', 
                'redirect' => 'adminDashboard.php'
            ]);
            exit();
        } else {
            header("Location: ../View/adminDashboard.php");
            exit();
        }
    } else {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid Username or Password!']);
            exit();
        } else {
            header("Location: ../View/adminLogin.php?error=" . urlencode("Invalid Username or Password!"));
            exit();
        }
    }
} else {
    header("Location: ../View/adminLogin.php");
    exit();
}
?>
