<?php

session_start();

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
    Header("Location: ../View/patientLogin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Header("Location: ../View/patientProfile.php");
    exit();
}

require_once "../Model/patientModel.php";

$username = $_SESSION["loggedInUsername"];

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$bloodGroup = trim($_POST["bloodGroup"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$address = trim($_POST["address"] ?? "");
$dob = trim($_POST["dob"] ?? "");
$currentPassword = $_POST["currentPassword"] ?? "";
$newPassword = $_POST["newPassword"] ?? "";
$confirmNewPassword = $_POST["confirmNewPassword"] ?? "";

$errors = [];
$validBloodGroups = ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];

$patient = getPatientByUsername($username);

if ($patient === null) {
    Header("Location: ../Controller/patientLogout.php");
    exit();
}

if ($name === "") {
    $errors["nameError"] = "Name is required.";
}

if ($email === "") {
    $errors["emailError"] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors["emailError"] = "Enter a valid email address.";
} elseif (patientEmailExists($email, $username)) {
    $errors["emailError"] = "This email is already used by another account.";
}

if ($phone === "") {
    $errors["phoneError"] = "Phone number is required.";
} elseif (!preg_match("/^01[0-9]{9}$/", $phone)) {
    $errors["phoneError"] = "Enter a valid 11-digit phone number (e.g. 01XXXXXXXXX).";
}

if ($bloodGroup === "" || !in_array($bloodGroup, $validBloodGroups)) {
    $errors["bloodGroupError"] = "Please select a valid blood group.";
}

if ($gender === "") {
    $errors["genderError"] = "Please select a gender.";
}

if ($address === "") {
    $errors["addressError"] = "Address is required.";
}

if ($dob !== "") {
    $dobTimestamp = strtotime($dob);
    if ($dobTimestamp === false) {
        $errors["dobError"] = "Enter a valid date of birth.";
    } elseif ($dobTimestamp > time()) {
        $errors["dobError"] = "Date of birth cannot be in the future.";
    } elseif ((new DateTime($dob))->diff(new DateTime())->y > 120) {
        $errors["dobError"] = "Please enter a valid date of birth.";
    }
}

$wantsPasswordChange = ($currentPassword !== "" || $newPassword !== "" || $confirmNewPassword !== "");

if ($wantsPasswordChange) {
    if (!verifyPatientPassword($currentPassword, $patient["password"])) {
        $errors["currentPasswordError"] = "Current password is incorrect.";
    }
    if (strlen($newPassword) < 6) {
        $errors["newPasswordError"] = "New password must be at least 6 characters.";
    }
    if ($newPassword !== $confirmNewPassword) {
        $errors["confirmNewPasswordError"] = "New passwords do not match.";
    }
}

if (!empty($errors)) {
    foreach ($errors as $key => $value) {
        $_SESSION[$key] = $value;
    }
    Header("Location: ../View/patientProfile.php");
    exit();
}

// Image upload (optional — only replaces the photo if a new file was chosen)
if (isset($_FILES["fileupload"]) && $_FILES["fileupload"]["error"] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES["fileupload"]["error"] !== UPLOAD_ERR_OK) {
        $_SESSION["imageError"] = "There was a problem uploading the image.";
        Header("Location: ../View/patientProfile.php");
        exit();
    }

    $allowedExtensions = ["jpg", "jpeg", "png"];
    $originalName = $_FILES["fileupload"]["name"];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions)) {
        $_SESSION["imageError"] = "Only JPG, JPEG or PNG images are allowed.";
        Header("Location: ../View/patientProfile.php");
        exit();
    }

    if ($_FILES["fileupload"]["size"] > 2 * 1024 * 1024) {
        $_SESSION["imageError"] = "Image must be smaller than 2MB.";
        Header("Location: ../View/patientProfile.php");
        exit();
    }

    $uploadDir = "../View/uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $imageFileName = "patient_" . time() . "_" . mt_rand(1000, 9999) . "." . $extension;

    if (!move_uploaded_file($_FILES["fileupload"]["tmp_name"], $uploadDir . $imageFileName)) {
        $_SESSION["imageError"] = "Failed to save the uploaded image.";
        Header("Location: ../View/patientProfile.php");
        exit();
    }

    updatePatientPhoto($username, $imageFileName);
}

updatePatientProfile($username, $name, $phone, $bloodGroup, $address, $email, ($dob !== "" ? $dob : null));

if ($wantsPasswordChange) {
    updatePatientPassword($username, $newPassword);
}

$_SESSION["profileSuccess"] = "Your profile has been updated.";

Header("Location: ../View/patientProfile.php");
exit();
