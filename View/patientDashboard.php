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
$reservations = getReservationsByPatient($username);

$totalReservations = count($reservations);
$pendingCount = countReservationsByStatus($username, 'Pending');
$approvedCount = countReservationsByStatus($username, 'Approved');

$recentReservations = getRecentReservations($username, 5);
$nextReservation = getNextUpcomingReservation($username);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BloodLine | Dashboard</title>

    <link rel="stylesheet" href="patientDashboard.css">

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

            <a href="patientDashboard.php" class="active">
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
                    OVERVIEW
                </p>

                <h2>
                    Welcome, <?php echo htmlspecialchars($patient["name"] ?? $username); ?>
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

        <section class="stats-grid">

            <div class="stat-card">
                <span class="stat-label">Blood Group</span>
                <strong class="stat-value"><?php echo htmlspecialchars($patient["bloodGroup"] ?? "-"); ?></strong>
            </div>

            <div class="stat-card">
                <span class="stat-label">Total Reservations</span>
                <strong class="stat-value"><?php echo $totalReservations; ?></strong>
            </div>

            <div class="stat-card">
                <span class="stat-label">Pending</span>
                <strong class="stat-value"><?php echo $pendingCount; ?></strong>
            </div>

            <div class="stat-card">
                <span class="stat-label">Approved</span>
                <strong class="stat-value"><?php echo $approvedCount; ?></strong>
            </div>

        </section>

        <section class="quick-actions">

            <a href="patientSearchBlood.php" class="action-card">
                <span class="action-icon">♥</span>
                <div>
                    <strong>Search Blood</strong>
                    <p>Find available blood near you.</p>
                </div>
            </a>

            <a href="patientReserveBlood.php" class="action-card">
                <span class="action-icon">+</span>
                <div>
                    <strong>Reserve Blood</strong>
                    <p>Reserve blood from a hospital.</p>
                </div>
            </a>

            <a href="patientReservations.php" class="action-card">
                <span class="action-icon">▣</span>
                <div>
                    <strong>My Reservations</strong>
                    <p>Track the status of your requests.</p>
                </div>
            </a>

        </section>

        <?php if ($nextReservation !== null) { ?>

            <section class="upcoming-reservation-card">

                <div class="upcoming-icon">📅</div>

                <div class="upcoming-details">

                    <p class="upcoming-label">
                        <?php echo (strtotime($nextReservation["neededDate"]) >= strtotime(date('Y-m-d'))) ? "Upcoming Blood Reservation" : "Most Recent Reservation"; ?>
                    </p>

                    <h3>
                        <?php echo htmlspecialchars($nextReservation["bloodGroup"]); ?> &middot;
                        <?php echo htmlspecialchars($nextReservation["numberOfBags"]); ?> bag(s) on
                        <?php echo htmlspecialchars(date('d M, Y', strtotime($nextReservation["neededDate"]))); ?>
                    </h3>

                    <p class="upcoming-hospital">
                        🏥 <?php echo htmlspecialchars($nextReservation["hospitalName"]); ?>
                        — <?php echo htmlspecialchars($nextReservation["hospitalLocation"]); ?>
                    </p>

                </div>

                <span class="badge badge-<?php echo strtolower($nextReservation["approval"]); ?>">
                    <?php echo htmlspecialchars($nextReservation["approval"]); ?>
                </span>

            </section>

        <?php } ?>

        <section class="recent-section">

            <div class="recent-header">
                <h3>Recent Reservations</h3>
                <a href="patientReservations.php">View all</a>
            </div>

            <?php if (empty($recentReservations)) { ?>

                <p class="empty-state">
                    You haven't made any reservations yet.
                </p>

            <?php } else { ?>

                <table class="recent-table">

                    <thead>
                        <tr>
                            <th>Blood Group</th>
                            <th>Bags</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($recentReservations as $r) { ?>

                            <tr>
                                <td><?php echo htmlspecialchars($r["bloodGroup"] ?? ""); ?></td>
                                <td><?php echo htmlspecialchars($r["numberOfBags"] ?? ""); ?></td>
                                <td><?php echo htmlspecialchars($r["reservationTime"] ?? ""); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($r["approval"] ?? "pending"); ?>">
                                        <?php echo htmlspecialchars($r["approval"] ?? "Pending"); ?>
                                    </span>
                                </td>
                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            <?php } ?>

        </section>

    </main>

</div>

</body>

</html>
