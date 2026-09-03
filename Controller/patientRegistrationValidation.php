<?php

session_start();

require_once "../Model/patientModel.php";

function backToRegistration($errors, $sticky) {
    foreach ($errors as $key => $value) {
        $_SESSION[$key] = $value;
    }
    foreach ($sticky as $key => $value) {
        $_SESSION[$key] = $value;
    }
    Header("Location: ../View/patientRegistration.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Header("Location: ../View/patientRegistration.php");
    exit();
}

$username = trim($_POST["username"] ?? "");
$fullName = trim($_POST["fullName"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$bloodGroup = trim($_POST["bloodGroup"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$address = trim($_POST["address"] ?? "");
$dob = trim($_POST["dob"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";
$terms = $_POST["terms"] ?? "";

$sticky = [
    "username" => $username,
    "fullName" => $fullName,
    "email" => $email,
    "phone" => $phone,
    "bloodGroup" => $bloodGroup,
    "gender" => $gender,
    "address" => $address,
    "dob" => $dob,
];

$errors = [];

$validBloodGroups = ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];
$minimumRegistrationAge = 15;

// Username (chosen by the patient; this becomes their permanent login username)
if ($username === "") {
    $errors["usernameError"] = "Username is required.";
} elseif (strlen($username) < 3 || strlen($username) > 30) {
    $errors["usernameError"] = "Username must be between 3 and 30 characters.";
} elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
    $errors["usernameError"] = "Username can only contain letters, numbers, and underscores.";
} elseif (patientUsernameExists($username)) {
    $errors["usernameError"] = "This username is already taken. Please choose another.";
}

// Full name
if ($fullName === "") {
    $errors["fullNameError"] = "Full name is required.";
} elseif (strlen($fullName) < 3) {
    $errors["fullNameError"] = "Name must be at least 3 characters.";
}

// Email
if ($email === "") {
    $errors["emailError"] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors["emailError"] = "Enter a valid email address.";
} elseif (patientEmailExists($email)) {
    $errors["emailError"] = "This email is already registered.";
}

// Phone
if ($phone === "") {
    $errors["phoneError"] = "Phone number is required.";
} elseif (!preg_match("/^01[0-9]{9}$/", $phone)) {
    $errors["phoneError"] = "Enter a valid 11-digit phone number (e.g. 01XXXXXXXXX).";
}

// Blood group
if ($bloodGroup === "") {
    $errors["bloodGroupError"] = "Please select a blood group.";
} elseif (!in_array($bloodGroup, $validBloodGroups)) {
    $errors["bloodGroupError"] = "Please select a valid blood group.";
}

// Gender
if ($gender === "") {
    $errors["genderError"] = "Please select a gender.";
}

// Address
if ($address === "") {
    $errors["addressError"] = "Address is required.";
}

// Date of birth (required, and patient must be at least 15 years old)
if ($dob === "") {
    $errors["dobError"] = "Date of birth is required.";
} else {
    $dobTimestamp = strtotime($dob);
    if ($dobTimestamp === false) {
        $errors["dobError"] = "Enter a valid date of birth.";
    } elseif ($dobTimestamp > time()) {
        $errors["dobError"] = "Date of birth cannot be in the future.";
    } else {
        $age = (new DateTime($dob))->diff(new DateTime())->y;
        if ($age > 120) {
            $errors["dobError"] = "Please enter a valid date of birth.";
        } elseif ($age < $minimumRegistrationAge) {
            $errors["dobError"] = "You must be at least " . $minimumRegistrationAge . " years old to register.";
        }
    }
}

// Password
if ($password === "") {
    $errors["passwordError"] = "Password is required.";
} elseif (strlen($password) < 6) {
    $errors["passwordError"] = "Password must be at least 6 characters.";
}

// Confirm password
if ($confirmPassword === "") {
    $errors["confirmPasswordError"] = "Please confirm your password.";
} elseif ($password !== "" && $password !== $confirmPassword) {
    $errors["confirmPasswordError"] = "Passwords do not match.";
}

// Terms
$termsAccepted = ($terms === "on");
if (!$termsAccepted) {
    $_SESSION["termsError"] = "You must agree to the terms and conditions.";
} else {
    unset($_SESSION["termsError"]);
}

// The patient's own chosen username is used as-is for login (validated and
// checked for uniqueness above). It is permanent and cannot be changed later
// from My Profile.
$loginUsername = $username;

// Image upload
$imageFileName = null;
if (isset($_FILES["fileupload"]) && $_FILES["fileupload"]["error"] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES["fileupload"]["error"] !== UPLOAD_ERR_OK) {
        $errors["imageError"] = "There was a problem uploading the image.";
    } else {
        $allowedExtensions = ["jpg", "jpeg", "png"];
        $originalName = $_FILES["fileupload"]["name"];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            $errors["imageError"] = "Only JPG, JPEG or PNG images are allowed.";
        } elseif ($_FILES["fileupload"]["size"] > 2 * 1024 * 1024) {
            $errors["imageError"] = "Image must be smaller than 2MB.";
        } else {
            $uploadDir = "../View/uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $imageFileName = "patient_" . time() . "_" . mt_rand(1000, 9999) . "." . $extension;
            if (!move_uploaded_file($_FILES["fileupload"]["tmp_name"], $uploadDir . $imageFileName)) {
                $errors["imageError"] = "Failed to save the uploaded image.";
                $imageFileName = null;
            }
        }
    }
}

if (!empty($errors) || !$termsAccepted) {
    backToRegistration($errors, $sticky);
}

// Passed validation, create the account in database
registerPatient($loginUsername, $password, $fullName, $phone, $bloodGroup, $address, $email, $dob, $imageFileName);

$_SESSION["registrationSuccess"] = "Account created successfully! Your login username is \"" . $loginUsername . "\". Please log in.";
$_SESSION["registeredUsername"] = $loginUsername;

Header("Location: ../View/patientLogin.php");
exit();
