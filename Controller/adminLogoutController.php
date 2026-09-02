<?php
// ==========================================
// Controller/adminLogoutController.php
// Clears session & cookie and redirects to login
// ==========================================

session_start();

// Destroy Session
$_SESSION = array();
if (session_id() != "" || isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}
session_destroy();

// Destroy Remember Me Cookie
setcookie('admin_user', '', time() - 3600, "/");

// Redirect to Login Page
header("Location: ../View/adminLogin.php?msg=" . urlencode("You have logged out successfully."));
exit();
?>
