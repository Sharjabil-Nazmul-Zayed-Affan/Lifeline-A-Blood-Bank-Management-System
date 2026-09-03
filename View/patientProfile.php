<?php

session_start();

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
    Header("Location: patientLogin.php");
    exit();
}

$username = $_SESSION["loggedInUsername"] ?? "";

require_once "../Model/patientModel.php";
$patient = getPatientByUsername($username);

$nameError = $_SESSION["nameError"] ?? "";
$emailError = $_SESSION["emailError"] ?? "";
$phoneError = $_SESSION["phoneError"] ?? "";
$bloodGroupError = $_SESSION["bloodGroupError"] ?? "";
$genderError = $_SESSION["genderError"] ?? "";
$addressError = $_SESSION["addressError"] ?? "";
$dobError = $_SESSION["dobError"] ?? "";
$currentPasswordError = $_SESSION["currentPasswordError"] ?? "";
$newPasswordError = $_SESSION["newPasswordError"] ?? "";
$confirmNewPasswordError = $_SESSION["confirmNewPasswordError"] ?? "";
$imageError = $_SESSION["imageError"] ?? "";
$profileSuccess = $_SESSION["profileSuccess"] ?? "";

unset($_SESSION["nameError"]);
unset($_SESSION["emailError"]);
unset($_SESSION["phoneError"]);
unset($_SESSION["bloodGroupError"]);
unset($_SESSION["genderError"]);
unset($_SESSION["addressError"]);
unset($_SESSION["dobError"]);
unset($_SESSION["currentPasswordError"]);
unset($_SESSION["newPasswordError"]);
unset($_SESSION["confirmNewPasswordError"]);
unset($_SESSION["imageError"]);
unset($_SESSION["profileSuccess"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BloodLine | My Profile</title>

    <link rel="stylesheet" href="patientProfile.css">

</head>

<body>

<div class="dashboard">

    <aside class="sidebar">

        <div class="logo">

            <div class="logo-icon">
                ♥
            </div>

            <h1>
                BLOOD<span>LINE</span>
            </h1>

        </div>

        <div class="patient-mini">

            <?php if (!empty($patient["photo"])) { ?>
                <img src="uploads/<?php echo htmlspecialchars($patient["photo"]); ?>" alt="Profile photo" class="default-profile profile-photo">
            <?php } else { ?>
                <div class="default-profile">
                    <?php echo strtoupper(substr($username, 0, 1)); ?>
                </div>
            <?php } ?>

            <div>

                <h3>
                    <?php echo htmlspecialchars($patient["name"] ?? $username); ?>
                </h3>

                <p>
                    Patient
                </p>

            </div>

        </div>

        <nav class="sidebar-nav">

            <a href="patientDashboard.php">
                <span>⌂</span>
                Dashboard
            </a>

            <a href="patientSearchBlood.php">
                <span>♥</span>
                Search Blood
            </a>

            <a href="patientReserveBlood.php">
                <span>+</span>
                Reserve Blood
            </a>

            <a href="patientReservations.php">
                <span>▣</span>
                My Reservations
            </a>

            <a href="patientProfile.php" class="active">
                <span>◉</span>
                My Profile
            </a>

            <a href="../Controller/patientLogout.php" class="logout">
                <span>↪</span>
                Logout
            </a>

        </nav>

        <div class="sidebar-bottom">

            <a href="../Controller/patientLogout.php" class="logout">
                <span>↪</span>
                Logout
            </a>

        </div>

    </aside>

    <main class="main-content">

        <header class="topbar">

            <div>

                <p class="page-label">
                    ACCOUNT
                </p>

                <h2>
                    My Profile
                </h2>

            </div>

            <div class="top-profile">

                <?php if (!empty($patient["photo"])) { ?>
                    <img src="uploads/<?php echo htmlspecialchars($patient["photo"]); ?>" alt="Profile photo" class="top-profile-default profile-photo">
                <?php } else { ?>
                    <div class="top-profile-default">
                        <?php echo strtoupper(substr($username, 0, 1)); ?>
                    </div>
                <?php } ?>

                <div>

                    <strong>
                        <?php echo htmlspecialchars($username); ?>
                    </strong>

                    <span>
                        Patient
                    </span>

                </div>

            </div>

        </header>

        <section class="profile-container">

            <?php if ($profileSuccess != "") { ?>

                <div class="success-box">
                    <?php echo htmlspecialchars($profileSuccess); ?>
                </div>

            <?php } ?>

            <form
                action="../Controller/patientProfileValidation.php"
                method="post"
                class="profile-form"
                enctype="multipart/form-data"
            >

                <h3 class="section-title">Profile Photo</h3>

                <div class="form-row">

                    <div class="input-group full-width">

                        <label for="fileupload">
                            <?php echo !empty($patient["photo"]) ? "Change Profile Photo" : "Upload Profile Photo"; ?>
                        </label>

                        <input
                            type="file"
                            id="fileupload"
                            name="fileupload"
                            accept=".jpg,.jpeg,.png"
                        >

                        <?php if (!empty($imageError)) { ?><p class="error"><?php echo htmlspecialchars($imageError); ?></p><?php } ?>

                    </div>

                </div>

                <h3 class="section-title">Personal Information</h3>

                <div class="form-row">

                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" value="<?php echo htmlspecialchars($username); ?>" disabled>
                    </div>

                    <div class="input-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($patient["name"] ?? ""); ?>">
                        <?php if ($nameError != "") { ?><p class="error"><?php echo $nameError; ?></p><?php } ?>
                    </div>

                </div>

                <div class="form-row">

                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($patient["email"] ?? ""); ?>">
                        <?php if ($emailError != "") { ?><p class="error"><?php echo $emailError; ?></p><?php } ?>
                    </div>

                    <div class="input-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($patient["phone"] ?? ""); ?>">
                        <?php if ($phoneError != "") { ?><p class="error"><?php echo $phoneError; ?></p><?php } ?>
                    </div>

                </div>

                <div class="form-row">

                    <div class="input-group">

                        <label for="bloodGroup">Blood Group</label>

                        <select id="bloodGroup" name="bloodGroup">
                            <?php
                            $groups = ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];
                            $currentGroup = $patient["bloodGroup"] ?? "";
                            foreach ($groups as $g) {
                                $selected = ($g === $currentGroup) ? "selected" : "";
                                echo "<option value=\"$g\" $selected>$g</option>";
                            }
                            ?>
                        </select>

                        <?php if ($bloodGroupError != "") { ?><p class="error"><?php echo $bloodGroupError; ?></p><?php } ?>

                    </div>

                    <div class="input-group">

                        <label for="gender">Gender</label>

                        <select id="gender" name="gender">
                            <?php
                            $genders = ["Male", "Female", "Other"];
                            $currentGender = $patient["gender"] ?? "";
                            foreach ($genders as $g) {
                                $selected = ($g === $currentGender) ? "selected" : "";
                                echo "<option value=\"$g\" $selected>$g</option>";
                            }
                            ?>
                        </select>

                        <?php if ($genderError != "") { ?><p class="error"><?php echo $genderError; ?></p><?php } ?>

                    </div>

                </div>

                <div class="form-row">

                    <div class="input-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" max="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($patient["dob"] ?? ""); ?>">
                        <?php if (!empty($dobError)) { ?><p class="error"><?php echo htmlspecialchars($dobError); ?></p><?php } ?>
                    </div>

                    <div class="input-group">
                        <label>Age</label>
                        <input type="text" value="<?php echo !empty($patient["dob"]) ? (new DateTime($patient["dob"]))->diff(new DateTime())->y . " years" : "— (set date of birth)"; ?>" disabled>
                    </div>

                </div>

                <div class="form-row">
                    <div class="input-group full-width">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($patient["address"] ?? ""); ?></textarea>
                        <?php if ($addressError != "") { ?><p class="error"><?php echo $addressError; ?></p><?php } ?>
                    </div>

                </div>

                <h3 class="section-title">Change Password (optional)</h3>

                <div class="form-row">

                    <div class="input-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" id="currentPassword" name="currentPassword">
                        <?php if ($currentPasswordError != "") { ?><p class="error"><?php echo $currentPasswordError; ?></p><?php } ?>
                    </div>

                </div>

                <div class="form-row">

                    <div class="input-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" id="newPassword" name="newPassword">
                        <?php if ($newPasswordError != "") { ?><p class="error"><?php echo $newPasswordError; ?></p><?php } ?>
                    </div>

                    <div class="input-group">
                        <label for="confirmNewPassword">Confirm New Password</label>
                        <input type="password" id="confirmNewPassword" name="confirmNewPassword">
                        <?php if ($confirmNewPasswordError != "") { ?><p class="error"><?php echo $confirmNewPasswordError; ?></p><?php } ?>
                    </div>

                </div>

                <button type="submit" class="register-button">
                    Save Changes
                </button>

            </form>

        </section>

    </main>

</div>

</body>

</html>
