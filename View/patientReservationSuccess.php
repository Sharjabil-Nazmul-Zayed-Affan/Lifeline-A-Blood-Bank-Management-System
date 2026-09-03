<?php

session_start();

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
    Header("Location: patientLogin.php");
    exit();
}

$username = $_SESSION["loggedInUsername"] ?? "";
$reservationId = $_SESSION["lastReservationId"] ?? null;
unset($_SESSION["lastReservationId"]);

require_once "../Model/patientModel.php";
require_once "../Model/patientReservationModel.php";
$patient = getPatientByUsername($username);
$reservation = ($reservationId !== null) ? getReservationById($reservationId) : null;

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BloodLine | Reservation Submitted</title>

    <link rel="stylesheet" href="patientReserveBlood.css">

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

            <a href="patientReservations.php" class="active">
                <span>▣</span>
                My Reservations
            </a>

            <a href="patientProfile.php">
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
                    BLOOD REQUEST
                </p>

                <h2>
                    Reservation Submitted
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

        <section class="success-container">

            <div class="success-icon">
                ✓
            </div>

            <h2>
                Your reservation has been submitted!
            </h2>

            <p>
                Your request is now <b>Pending</b>. The hospital will review
                and process it soon. You can track its status from
                My Reservations.
            </p>

            <?php if ($reservation !== null) { ?>

                <div class="success-summary">

                    <div>
                        <span>Reservation ID</span>
                        <strong>#<?php echo htmlspecialchars($reservation["reserveId"]); ?></strong>
                    </div>

                    <div>
                        <span>Blood Group</span>
                        <strong><?php echo htmlspecialchars($reservation["bloodGroup"]); ?></strong>
                    </div>

                    <div>
                        <span>Bags Requested</span>
                        <strong><?php echo htmlspecialchars($reservation["numberOfBags"]); ?></strong>
                    </div>

                    <div>
                        <span>Reservation Date</span>
                        <strong><?php echo htmlspecialchars($reservation["neededDate"] ?? ""); ?></strong>
                    </div>

                    <div>
                        <span>Hospital</span>
                        <strong><?php echo htmlspecialchars($reservation["hospitalName"]); ?></strong>
                    </div>

                    <div>
                        <span>Location</span>
                        <strong><?php echo htmlspecialchars($reservation["hospitalLocation"] ?? ""); ?></strong>
                    </div>

                </div>

            <?php } ?>

            <div class="form-actions">

                <a href="patientReservations.php" class="reserve-button">
                    View My Reservations
                </a>

                <a href="patientDashboard.php" class="cancel-button">
                    Back to Dashboard
                </a>

            </div>

        </section>

    </main>

</div>

</body>

</html>
