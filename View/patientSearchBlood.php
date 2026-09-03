<?php

session_start();

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
    Header("Location: patientLogin.php");
    exit();
}

$username = $_SESSION["loggedInUsername"] ?? "";

require_once "../Model/patientModel.php";
$patient = getPatientByUsername($username);

$searchPerformed = $_SESSION["searchPerformed"] ?? false;
$results = $_SESSION["searchResults"] ?? [];
$bloodGroup = $_SESSION["searchBloodGroup"] ?? "";
$location = $_SESSION["searchLocation"] ?? "";

unset($_SESSION["searchResults"]);
unset($_SESSION["searchBloodGroup"]);
unset($_SESSION["searchLocation"]);
unset($_SESSION["searchPerformed"]);

require_once "../Model/patientBloodModel.php";

// If no search has been performed yet, show everything available by default
if (!$searchPerformed) {
    $results = getAvailableBloodGroupedByHospital();
}

$validBloodGroups = ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BloodLine | Hospital Blood Availability</title>

    <link rel="stylesheet" href="patientSearchBlood.css">

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

            <a href="patientSearchBlood.php" class="active">
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
                    HOSPITAL DIRECTORY & AVAILABILITY
                </p>

                <h2>
                    Search Hospital Blood Stocks
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

        <!-- INFO BANNER: DIRECTS USER TO RESERVE BLOOD FOR BOOKINGS -->
        <div class="info-guide-banner">
            <div>
                <strong>🏥 Hospital Blood Inventory & Information</strong>
                <p>Browse live blood availability across hospitals and blood banks. To submit a reservation request with your required date, please visit the <b>Reserve Blood</b> section.</p>
            </div>
            <a href="patientReserveBlood.php" class="goto-reserve-btn">
                Go to Reserve Blood &rarr;
            </a>
        </div>

        <section class="search-panel">

            <form
                action="../Controller/patientSearchBlood.php"
                method="get"
                class="search-form"
            >

                <div class="input-group">

                    <label for="bloodGroup">Blood Group</label>

                    <select id="bloodGroup" name="bloodGroup">

                        <option value="">All Groups</option>

                        <?php foreach ($validBloodGroups as $g) { ?>

                            <option value="<?php echo $g; ?>" <?php if ($bloodGroup === $g) echo "selected"; ?>>
                                <?php echo $g; ?>
                            </option>

                        <?php } ?>

                    </select>

                </div>

                <div class="input-group">

                    <label for="location">Location / Hospital Name</label>

                    <input
                        type="text"
                        id="location"
                        name="location"
                        placeholder="e.g. Dhaka, Chittagong, Square Hospitals"
                        value="<?php echo htmlspecialchars($location); ?>"
                    >

                </div>

                <button type="submit" class="search-button">
                    Search
                </button>

            </form>

        </section>

        <?php if (empty($results)) { ?>

            <section class="not-found">

                <div class="not-found-icon">
                    ♥
                </div>

                <h2>
                    No Blood Found
                </h2>

                <p>
                    No hospital currently has blood matching your filter. Try searching for another blood group or location.
                </p>

            </section>

        <?php } else { ?>

            <section class="results-grid">

                <?php foreach ($results as $hospital) { ?>

                    <div class="hospital-card">

                        <div class="hospital-card-header">

                            <div class="hospital-icon">
                                🏥
                            </div>

                            <div class="hospital-info">
                                <h3>
                                    <?php echo htmlspecialchars($hospital["hospitalName"] ?? ""); ?>
                                </h3>
                                <p class="result-location">
                                    📍 <?php echo htmlspecialchars($hospital["location"] ?? ""); ?>
                                </p>
                            </div>

                            <div class="stock-status-badge">
                                <span class="status-indicator">●</span> In Stock
                            </div>

                        </div>

                        <div class="hospital-group-list">

                            <?php foreach ($hospital["bloodGroups"] as $bg) { ?>

                                <span class="group-pill">
                                    <strong><?php echo htmlspecialchars($bg["bloodGroup"] ?? ""); ?></strong>
                                    <?php echo htmlspecialchars($bg["quantity"] ?? 0); ?> bag(s)
                                </span>

                            <?php } ?>

                        </div>

                        <div class="hospital-card-footer">
                            <span><b><?php echo htmlspecialchars($hospital["totalBags"] ?? 0); ?></b> total bags across <?php echo count($hospital["bloodGroups"]); ?> blood group(s)</span>
                            <a href="patientReserveBlood.php?hospital=<?php echo urlencode($hospital["hospitalName"] ?? ""); ?>" class="goto-reserve-btn">
                                Reserve Here &rarr;
                            </a>
                        </div>

                    </div>

                <?php } ?>

            </section>

        <?php } ?>

    </main>

</div>

</body>

</html>
