<?php

session_start();

if (isset($_SESSION["isLoggedIn"]) && $_SESSION["isLoggedIn"] === true) {
    Header("Location: patientDashboard.php");
    exit();
}

$loginError = $_SESSION["loginError"] ?? "";
$username = $_SESSION["loginUsername"] ?? "";
$registrationSuccess = $_SESSION["registrationSuccess"] ?? "";
$registeredUsername = $_SESSION["registeredUsername"] ?? "";

unset($_SESSION["loginError"]);
unset($_SESSION["loginUsername"]);
unset($_SESSION["registrationSuccess"]);
unset($_SESSION["registeredUsername"]);

if ($username === "" && $registeredUsername !== "") {
    $username = $registeredUsername;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BloodLine | Patient Login</title>

    <link rel="stylesheet" href="patientLogin.css">

</head>

<body>

<div class="page-container">

    <!-- LEFT SIDE -->

    <div class="left-section">

        <div class="brand-area">

            <div class="blood-drop">
                ♥
            </div>

            <h1>
                BLOOD<span>LINE</span>
            </h1>

            <p>
                Your blood can give someone<br>
                another chance at life.
            </p>

        </div>

        <div class="left-bottom">

            <div class="info-item">
                <strong>Find Blood</strong>
                <span>Search for available blood near you.</span>
            </div>

            <div class="info-item">
                <strong>Reserve Blood</strong>
                <span>Reserve available blood from a hospital.</span>
            </div>

            <div class="info-item">
                <strong>Save Lives</strong>
                <span>Be part of the BloodLine community.</span>
            </div>

        </div>

    </div>


    <!-- RIGHT SIDE -->

    <div class="right-section">

        <div class="login-card">

            <div class="form-header">

                <p class="small-title">WELCOME BACK</p>

                <h2>Patient Login</h2>

                <p class="subtitle">
                    Log in to search and reserve blood.
                </p>

            </div>

            <?php if ($registrationSuccess != "") { ?>

                <p class="success-banner">
                    <?php echo htmlspecialchars($registrationSuccess); ?>
                </p>

            <?php } ?>

            <?php if ($loginError != "") { ?>

                <p class="error-box">
                    <?php echo htmlspecialchars($loginError); ?>
                </p>

            <?php } ?>

            <form
                action="../Controller/patientLoginValidation.php"
                method="post"
            >

                <div class="form-row">

                    <div class="input-group full-width">

                        <label for="username">
                            Username
                        </label>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Enter your username"
                            value="<?php echo htmlspecialchars($username); ?>"
                        >

                    </div>

                </div>

                <div class="form-row">

                    <div class="input-group full-width">

                        <label for="password">
                            Password
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                        >

                    </div>

                </div>

                <button
                    type="submit"
                    class="register-button"
                >
                    Login
                </button>

                <div class="login-link">

                    Don't have an account?

                    <a href="patientRegistration.php">
                        Register
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

</body>

</html>
