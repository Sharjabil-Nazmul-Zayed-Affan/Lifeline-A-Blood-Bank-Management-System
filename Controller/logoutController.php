<?php
session_start();

$_SESSION = array();

if (session_id() != "" || isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}
session_destroy();

setcookie('admin_user', '', time() - 3600, "/");

header("Location: ../View/login.php?msg=" . urlencode("You have logged out successfully."));
exit();
?>
