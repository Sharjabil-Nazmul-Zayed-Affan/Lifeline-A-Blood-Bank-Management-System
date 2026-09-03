<?php

session_start();

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
    Header("Location: patientLogin.php");
    exit();
}

$username = $_SESSION["loggedInUsername"] ?? "";

require_once "../Model/patientModel.php";
require_once "../Model/patientReservationModel.php";

$patient = getPatientByUsername($username);

// Fetch patient reservations
$reservations = getReservationsByPatient($username);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BloodLine | My Reservations</title>

    <link rel="stylesheet" href="patientReservations.css">

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
                    HISTORY & STATUS
                </p>

                <h2>
                    My Reservations
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

        <!-- MY RESERVATIONS -->
        <section class="reservations-section reservations-page">

            <div class="reservations-header">

                <div>
                    <p class="sub-label">HISTORY & STATUS</p>
                    <h2>My Reservations</h2>
                </div>

                <span class="count-badge"><?php echo count($reservations); ?> Total</span>

            </div>

            <?php if (empty($reservations)) { ?>

                <div class="empty-reservations">
                    <div class="empty-icon">▣</div>
                    <h3>No Reservations Yet</h3>
                    <p>Head over to <a href="patientReserveBlood.php">Reserve Blood</a> to make your first blood reservation request.</p>
                </div>

            <?php } else { ?>

                <div class="reservations-cards-list">

                    <?php foreach ($reservations as $r) { ?>

                        <div class="res-card">

                            <div class="res-card-top">

                                <div class="res-blood-badge">
                                    <?php echo htmlspecialchars($r["bloodGroup"] ?? ""); ?>
                                </div>

                                <div class="res-details">
                                    <h4>Reservation #<?php echo htmlspecialchars($r["reserveId"] ?? ""); ?></h4>
                                    <p class="res-bags"><?php echo htmlspecialchars($r["numberOfBags"] ?? 1); ?> Bag(s)</p>
                                </div>

                                <div class="res-status">
                                    <span class="badge badge-<?php echo strtolower($r["approval"] ?? "pending"); ?>">
                                        <?php echo htmlspecialchars($r["approval"] ?? "Pending"); ?>
                                    </span>
                                </div>

                            </div>

                            <div class="res-hospital">
                                🏥 <?php echo htmlspecialchars($r["hospitalName"] ?? ""); ?>
                                <span class="res-hospital-location">— <?php echo htmlspecialchars($r["hospitalLocation"] ?? ""); ?></span>
                            </div>

                            <div class="res-card-dates">

                                <div class="date-item highlight">
                                    <span class="date-lbl">📅 Reservation Date:</span>
                                    <strong><?php echo htmlspecialchars($r["neededDate"] ?? date('Y-m-d', strtotime($r["reservationTime"]))); ?></strong>
                                </div>

                                <div class="date-item">
                                    <span class="date-lbl">Requested:</span>
                                    <span><?php echo htmlspecialchars($r["reservationTime"] ?? ""); ?></span>
                                </div>

                            </div>

                        </div>

                    <?php } ?>

                </div>

            <?php } ?>

        </section>

    </main>

</div>

</body>

</html>
