<?php

session_start();

$usernameError = $_SESSION["usernameError"] ?? "";
$fullNameError = $_SESSION["fullNameError"] ?? "";
$emailError = $_SESSION["emailError"] ?? "";
$phoneError = $_SESSION["phoneError"] ?? "";
$passwordError = $_SESSION["passwordError"] ?? "";
$confirmPasswordError = $_SESSION["confirmPasswordError"] ?? "";
$bloodGroupError = $_SESSION["bloodGroupError"] ?? "";
$genderError = $_SESSION["genderError"] ?? "";
$addressError = $_SESSION["addressError"] ?? "";
$dobError = $_SESSION["dobError"] ?? "";
$imageError = $_SESSION["imageError"] ?? "";
$termsError = $_SESSION["termsError"] ?? "";
$registrationSuccess = $_SESSION["registrationSuccess"] ?? "";

$username = $_SESSION["username"] ?? "";
$fullName = $_SESSION["fullName"] ?? "";
$email = $_SESSION["email"] ?? "";
$phone = $_SESSION["phone"] ?? "";
$bloodGroup = $_SESSION["bloodGroup"] ?? "";
$gender = $_SESSION["gender"] ?? "";
$address = $_SESSION["address"] ?? "";
$dob = $_SESSION["dob"] ?? "";

unset($_SESSION["usernameError"]);
unset($_SESSION["fullNameError"]);
unset($_SESSION["emailError"]);
unset($_SESSION["phoneError"]);
unset($_SESSION["passwordError"]);
unset($_SESSION["confirmPasswordError"]);
unset($_SESSION["bloodGroupError"]);
unset($_SESSION["genderError"]);
unset($_SESSION["addressError"]);
unset($_SESSION["dobError"]);
unset($_SESSION["imageError"]);
unset($_SESSION["termsError"]);
unset($_SESSION["registrationSuccess"]);

unset($_SESSION["username"]);
unset($_SESSION["fullName"]);
unset($_SESSION["email"]);
unset($_SESSION["phone"]);
unset($_SESSION["bloodGroup"]);
unset($_SESSION["gender"]);
unset($_SESSION["address"]);
unset($_SESSION["dob"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BloodLine | Patient Registration</title>

    <link rel="stylesheet" href="patientRegistration.css">

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

        <div class="registration-card">

            <div class="form-header">

                <p class="small-title">WELCOME TO BLOODLINE</p>

                <h2>Create Patient Account</h2>

                <p class="subtitle">
                    Register now to search and reserve blood.
                </p>

            </div>

            <?php if ($registrationSuccess != "") { ?>

                <p class="success-banner">
                    <?php echo htmlspecialchars($registrationSuccess); ?>
                </p>

            <?php } ?>

            <form
                action="../Controller/patientRegistrationValidation.php"
                method="post"
                enctype="multipart/form-data"
            >

                <!-- PROFILE IMAGE -->

                <div class="profile-upload">

                    <div class="profile-circle">

                        <span>+</span>

                    </div>

                    <div class="upload-content">

                        <label for="fileupload">
                            Upload Profile Image
                        </label>

                        <p>
                            JPG, JPEG or PNG
                        </p>

                        <input
                            type="file"
                            id="fileupload"
                            name="fileupload"
                            accept=".jpg,.jpeg,.png"
                        >

                        <?php if ($imageError != "") { ?>

                            <p class="error">
                                <?php echo $imageError; ?>
                            </p>

                        <?php } ?>

                    </div>

                </div>


                <!-- USERNAME + FULL NAME -->

                <div class="form-row">

                    <div class="input-group">

                        <label for="username">
                            Username
                        </label>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="<?php echo htmlspecialchars($username); ?>"
                        >

                        <?php if ($usernameError != "") { ?>

                            <p class="error">
                                <?php echo $usernameError; ?>
                            </p>

                        <?php } ?>

                    </div>

                    <div class="input-group">

                        <label for="fullName">
                            Full Name
                        </label>

                        <input
                            type="text"
                            id="fullName"
                            name="fullName"
                            placeholder="Enter your full name"
                            value="<?php echo htmlspecialchars($fullName); ?>"
                        >

                        <?php if ($fullNameError != "") { ?>

                            <p class="error">
                                <?php echo $fullNameError; ?>
                            </p>

                        <?php } ?>

                    </div>

                </div>


                <!-- DATE OF BIRTH -->

                <div class="form-row">

                    <div class="input-group full-width">

                        <label for="dob">
                            Date of Birth
                        </label>

                        <input
                            type="date"
                            id="dob"
                            name="dob"
                            max="<?php echo date('Y-m-d'); ?>"
                            value="<?php echo htmlspecialchars($dob ?? ""); ?>"
                            required
                        >

                        <?php if (!empty($dobError)) { ?>

                            <p class="error">
                                <?php echo htmlspecialchars($dobError); ?>
                            </p>

                        <?php } ?>

                    </div>

                </div>


                <!-- EMAIL + PHONE -->

                <div class="form-row">

                    <div class="input-group">

                        <label for="email">
                            Email Address
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="example@gmail.com"
                            value="<?php echo htmlspecialchars($email); ?>"
                        >

                        <?php if ($emailError != "") { ?>

                            <p class="error">
                                <?php echo $emailError; ?>
                            </p>

                        <?php } ?>

                    </div>


                    <div class="input-group">

                        <label for="phone">
                            Phone Number
                        </label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            placeholder="01XXXXXXXXX"
                            value="<?php echo htmlspecialchars($phone); ?>"
                        >

                        <?php if ($phoneError != "") { ?>

                            <p class="error">
                                <?php echo $phoneError; ?>
                            </p>

                        <?php } ?>

                    </div>

                </div>


                <!-- BLOOD GROUP + GENDER -->

                <div class="form-row">

                    <div class="input-group">

                        <label for="bloodGroup">
                            Blood Group
                        </label>

                        <select
                            id="bloodGroup"
                            name="bloodGroup"
                        >

                            <option value="">
                                Select Blood Group
                            </option>

                            <option value="A+" <?php if ($bloodGroup == "A+") echo "selected"; ?>>
                                A+
                            </option>

                            <option value="A-" <?php if ($bloodGroup == "A-") echo "selected"; ?>>
                                A-
                            </option>

                            <option value="B+" <?php if ($bloodGroup == "B+") echo "selected"; ?>>
                                B+
                            </option>

                            <option value="B-" <?php if ($bloodGroup == "B-") echo "selected"; ?>>
                                B-
                            </option>

                            <option value="AB+" <?php if ($bloodGroup == "AB+") echo "selected"; ?>>
                                AB+
                            </option>

                            <option value="AB-" <?php if ($bloodGroup == "AB-") echo "selected"; ?>>
                                AB-
                            </option>

                            <option value="O+" <?php if ($bloodGroup == "O+") echo "selected"; ?>>
                                O+
                            </option>

                            <option value="O-" <?php if ($bloodGroup == "O-") echo "selected"; ?>>
                                O-
                            </option>

                        </select>

                        <?php if ($bloodGroupError != "") { ?>

                            <p class="error">
                                <?php echo $bloodGroupError; ?>
                            </p>

                        <?php } ?>

                    </div>


                    <div class="input-group">

                        <label for="gender">
                            Gender
                        </label>

                        <select
                            id="gender"
                            name="gender"
                        >

                            <option value="">
                                Select Gender
                            </option>

                            <option value="Male" <?php if ($gender == "Male") echo "selected"; ?>>
                                Male
                            </option>

                            <option value="Female" <?php if ($gender == "Female") echo "selected"; ?>>
                                Female
                            </option>

                            <option value="Other" <?php if ($gender == "Other") echo "selected"; ?>>
                                Other
                            </option>

                        </select>

                        <?php if ($genderError != "") { ?>

                            <p class="error">
                                <?php echo $genderError; ?>
                            </p>

                        <?php } ?>

                    </div>

                </div>


                <!-- ADDRESS -->

                <div class="form-row">

                    <div class="input-group full-width">

                        <label for="address">
                            Address
                        </label>

                        <textarea
                            id="address"
                            name="address"
                            placeholder="Enter your address"
                            rows="3"
                        ><?php echo htmlspecialchars($address); ?></textarea>

                        <?php if ($addressError != "") { ?>

                            <p class="error">
                                <?php echo $addressError; ?>
                            </p>

                        <?php } ?>

                    </div>

                </div>


                <!-- PASSWORD -->

                <div class="form-row">

                    <div class="input-group">

                        <label for="password">
                            Password
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Create a password"
                        >

                        <?php if ($passwordError != "") { ?>

                            <p class="error">
                                <?php echo $passwordError; ?>
                            </p>

                        <?php } ?>

                    </div>


                    <div class="input-group">

                        <label for="confirmPassword">
                            Confirm Password
                        </label>

                        <input
                            type="password"
                            id="confirmPassword"
                            name="confirmPassword"
                            placeholder="Confirm your password"
                        >

                        <?php if ($confirmPasswordError != "") { ?>

                            <p class="error">
                                <?php echo $confirmPasswordError; ?>
                            </p>

                        <?php } ?>

                    </div>

                </div>


                <!-- TERMS -->

                <div class="terms">

                    <input
                        type="checkbox"
                        id="terms"
                        name="terms"
                        required
                    >

                    <label for="terms">
                        I agree to the BloodLine terms and conditions.
                    </label>

                </div>

                <?php if ($termsError != "") { ?>

                    <p class="error">
                        <?php echo htmlspecialchars($termsError); ?>
                    </p>

                <?php } ?>


                <!-- SUBMIT -->

                <button
                    type="submit"
                    class="register-button"
                >
                    Create Patient Account
                </button>


                <div class="login-link">

                    Already have an account?

                    <a href="patientLogin.php">
                        Login
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

</body>

</html>