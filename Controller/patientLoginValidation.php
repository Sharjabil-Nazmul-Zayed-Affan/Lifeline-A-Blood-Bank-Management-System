<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Header("Location: ../View/patientLogin.php");
    exit();
}

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

$_SESSION["loginUsername"] = $username;

if ($username === "" || $password === "") {
    $_SESSION["loginError"] = "Please enter both username and password.";
    Header("Location: ../View/patientLogin.php");
    exit();
}

require_once "../Model/patientModel.php";

$matchedUser = getPatientByUsername($username);

if ($matchedUser === null || !verifyPatientPassword($password, $matchedUser["password"])) {
    $_SESSION["loginError"] = "Invalid username or password.";
    Header("Location: ../View/patientLogin.php");
    exit();
}

// Success
session_regenerate_id(true);
$_SESSION["isLoggedIn"] = true;
$_SESSION["loggedInUsername"] = $matchedUser["username"];
unset($_SESSION["loginError"]);
unset($_SESSION["loginUsername"]);

Header("Location: ../View/patientDashboard.php");
exit();
