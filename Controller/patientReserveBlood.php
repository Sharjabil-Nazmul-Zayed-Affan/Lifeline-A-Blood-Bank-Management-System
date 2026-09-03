<?php

session_start();

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
    Header("Location: ../View/patientLogin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Header("Location: ../View/patientReserveBlood.php");
    exit();
}

require_once "../Model/patientBloodModel.php";
require_once "../Model/patientReservationModel.php";

$username = $_SESSION["loggedInUsername"];
$bloodGroup = trim($_POST["bloodGroup"] ?? "");
$bloodId = trim($_POST["bloodId"] ?? "");
$hospitalName = trim($_POST["hospitalName"] ?? "");
$quantity = $_POST["quantity"] ?? "";
$neededDate = trim($_POST["neededDate"] ?? $_POST["reservationDate"] ?? "");
$reason = trim($_POST["reason"] ?? "");

// If bloodId is provided, retrieve its blood group
if ($bloodId !== "" && $bloodGroup === "") {
    $blood = getBloodById($bloodId);
    if ($blood) {
        $bloodGroup = $blood["bloodGroup"];
    }
}

$validBloodGroups = ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];

if ($bloodGroup === "" || !in_array($bloodGroup, $validBloodGroups)) {
    $_SESSION["reservationError"] = "Please select a valid blood group.";
    Header("Location: ../View/patientReserveBlood.php");
    exit();
}

$hospitalDirectoryNames = array_column(getHospitalDirectory(), 'name');
if ($hospitalName === "" || !in_array($hospitalName, $hospitalDirectoryNames)) {
    $_SESSION["hospitalError"] = "Please select a valid hospital.";
    Header("Location: ../View/patientReserveBlood.php");
    exit();
}

$available = getGroupQuantityAtHospital($hospitalName, $bloodGroup);

if ($available <= 0) {
    $_SESSION["reservationError"] = "$hospitalName currently has no available bags for blood group $bloodGroup. Please choose another hospital.";
    Header("Location: ../View/patientReserveBlood.php");
    exit();
}

if (!is_numeric($quantity) || (int)$quantity <= 0) {
    $_SESSION["quantityError"] = "Please enter a valid quantity.";
    Header("Location: ../View/patientReserveBlood.php");
    exit();
}

$quantity = (int)$quantity;

if ($quantity > $available) {
    $_SESSION["quantityError"] = "Only $available bag(s) of $bloodGroup are available at $hospitalName.";
    Header("Location: ../View/patientReserveBlood.php");
    exit();
}

if ($neededDate === "") {
    $_SESSION["dateError"] = "Please select the date when you need the blood.";
    Header("Location: ../View/patientReserveBlood.php");
    exit();
}

if ($reason === "") {
    $_SESSION["reservationError"] = "Please enter a reason for your blood request.";
    Header("Location: ../View/patientReserveBlood.php");
    exit();
}


$hospitalLocation = getHospitalLocationByName($hospitalName);

// Record reservation with the user-selected reservation date and hospital
$reserveId = createReservation($username, $bloodGroup, $quantity, $neededDate, $hospitalName, $hospitalLocation);

$_SESSION["lastReservationId"] = $reserveId;
$_SESSION["reservationSuccessMsg"] = "Blood reservation submitted successfully for $neededDate!";

Header("Location: ../View/patientReservationSuccess.php");
exit();
