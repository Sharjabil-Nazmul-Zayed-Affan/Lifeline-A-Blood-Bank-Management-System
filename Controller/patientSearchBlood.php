<?php

session_start();

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
    Header("Location: ../View/patientLogin.php");
    exit();
}

require_once "../Model/patientBloodModel.php";

$bloodGroup = trim($_GET["bloodGroup"] ?? "");
$location = trim($_GET["location"] ?? "");

$results = getAvailableBloodGroupedByHospital($bloodGroup, $location);

$_SESSION["searchResults"] = $results;
$_SESSION["searchBloodGroup"] = $bloodGroup;
$_SESSION["searchLocation"] = $location;
$_SESSION["searchPerformed"] = true;

Header("Location: ../View/patientSearchBlood.php");
exit();
