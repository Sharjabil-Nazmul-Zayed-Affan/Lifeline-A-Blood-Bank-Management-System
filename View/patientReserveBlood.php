<?php

session_start();

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
    Header("Location: patientLogin.php");
    exit();
}

$username = $_SESSION["loggedInUsername"] ?? "";

require_once "../Model/patientModel.php";
require_once "../Model/patientBloodModel.php";

$patient = getPatientByUsername($username);

$bloodId = $_GET["bloodId"] ?? "";
$selectedBlood = null;
if (!empty($bloodId)) {
    $selectedBlood = getBloodById($bloodId);
}

$selectedHospital = $_GET["hospital"] ?? "";

$quantityError = $_SESSION["quantityError"] ?? "";
$reservationError = $_SESSION["reservationError"] ?? "";
$dateError = $_SESSION["dateError"] ?? "";
$hospitalError = $_SESSION["hospitalError"] ?? "";

unset($_SESSION["quantityError"]);
unset($_SESSION["reservationError"]);
unset($_SESSION["dateError"]);
unset($_SESSION["hospitalError"]);

// Fetch blood inventory summary
$bloodInventory = getAllAvailableBlood();
$bloodGroups = ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];
$hospitalsWithStock = getHospitalsWithStock();

// Build a map of hospital name -> blood groups actually available at that
// hospital, so the "Select Blood Group" dropdown can be filtered on the
// client side to only show groups the chosen hospital actually has in stock.
$hospitalGroupedStock = getAvailableBloodGroupedByHospital();
$hospitalBloodGroupMap = [];
foreach ($hospitalGroupedStock as $h) {
    $groups = [];
    foreach ($h["bloodGroups"] as $bg) {
        $groups[] = ["group" => $bg["bloodGroup"], "qty" => (int)$bg["quantity"]];
    }
    $hospitalBloodGroupMap[$h["hospitalName"]] = $groups;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BloodLine | Reserve Blood</title>

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

            <a href="patientReserveBlood.php" class="active">
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
                    BLOOD RESERVATION
                </p>

                <h2>
                    Reserve Blood
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

        <div class="reserve-page-single">

            <!-- RESERVATION FORM -->
            <section class="reservation-container">

                <div class="reservation-header">

                    <p>
                        REQUEST FORM
                    </p>

                    <h1>
                        Make a Blood Reservation
                    </h1>

                    <span>
                        Specify the blood group, quantity, and the date you need it reserved.
                    </span>

                </div>

                <?php if ($reservationError != "") { ?>
                    <div class="error-box">
                        <?php echo htmlspecialchars($reservationError); ?>
                    </div>
                <?php } ?>

                <form
                    action="../Controller/patientReserveBlood.php"
                    method="post"
                    class="reservation-form"
                    id="reservationForm"
                >

                    <!-- HOSPITAL SELECTION -->
                    <div class="form-group">

                        <label for="hospitalName">
                            Select Hospital
                        </label>

                        <select id="hospitalName" name="hospitalName" required onchange="updateBloodGroupOptions()">
                            <option value="">-- Choose Hospital --</option>
                            <?php
                            foreach ($hospitalsWithStock as $h) {
                                $isSelected = ($selectedHospital === $h["name"]) ? "selected" : "";
                            ?>
                                <option value="<?php echo htmlspecialchars($h["name"]); ?>" <?php echo $isSelected; ?>>
                                    <?php echo htmlspecialchars($h["name"]); ?> — <?php echo htmlspecialchars($h["location"]); ?> (<?php echo (int)$h["totalBags"]; ?> bags total)
                                </option>
                            <?php } ?>
                        </select>

                        <?php if (!empty($hospitalError)) { ?>
                            <p class="error">
                                <?php echo htmlspecialchars($hospitalError); ?>
                            </p>
                        <?php } ?>

                        <span class="help-text">
                            Blood group availability can vary by hospital — we'll confirm stock when you submit.
                        </span>

                    </div>

                    <!-- BLOOD GROUP SELECTION -->
                    <div class="form-group">

                        <label for="bloodGroup">
                            Select Blood Group
                        </label>

                        <select id="bloodGroup" name="bloodGroup" required>
                            <option value="">-- Choose Hospital First --</option>
                        </select>

                        <span class="help-text">
                            Only blood groups actually in stock at the selected hospital are shown.
                        </span>

                    </div>

                    <!-- REQUIRED BAGS -->
                    <div class="form-group">

                        <label for="quantity">
                            Required Quantity (Bags)
                        </label>

                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            min="1"
                            max="10"
                            placeholder="e.g. 1, 2"
                            required
                        >

                        <?php if ($quantityError != "") { ?>
                            <p class="error">
                                <?php echo htmlspecialchars($quantityError); ?>
                            </p>
                        <?php } ?>

                    </div>

                    <!-- RESERVATION DATE (WHEN NEEDED) -->
                    <div class="form-group">

                        <label for="neededDate">
                            Reservation Date (When do you need the blood?)
                        </label>

                        <input
                            type="date"
                            id="neededDate"
                            name="neededDate"
                            min="<?php echo date('Y-m-d'); ?>"
                            value="<?php echo date('Y-m-d'); ?>"
                            required
                        >

                        <?php if ($dateError != "") { ?>
                            <p class="error">
                                <?php echo htmlspecialchars($dateError); ?>
                            </p>
                        <?php } ?>

                        <span class="help-text">
                            Select the target date for blood collection/transfusion.
                        </span>

                    </div>

                    <!-- REASON -->
                    <div class="form-group">

                        <label for="reason">
                            Reason for Blood Request
                        </label>

                        <textarea
                            id="reason"
                            name="reason"
                            rows="3"
                            placeholder="Enter medical reason, patient condition, or hospital requirements"
                            required
                        ></textarea>

                    </div>

                    <div class="notice">

                        <strong>
                            Important Note
                        </strong>

                        <p>
                            Your reservation will be submitted as <b>Pending</b>. The blood bank team will process and approve your request.
                        </p>

                    </div>

                    <div class="form-actions">

                        <button
                            type="submit"
                            class="reserve-button"
                        >
                            Confirm Reservation
                        </button>

                    </div>

                </form>

            </section>

        </div>

    </main>

</div>



<script>
const hospitalBloodGroupMap = <?php echo json_encode($hospitalBloodGroupMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

function updateBloodGroupOptions() {
    const hospitalSelect = document.getElementById('hospitalName');
    const bloodGroupSelect = document.getElementById('bloodGroup');
    const hospitalName = hospitalSelect.value;
    const groups = hospitalBloodGroupMap[hospitalName] || [];
    const selectedGroup = <?php echo json_encode($selectedBlood['bloodGroup'] ?? ''); ?>;

    bloodGroupSelect.innerHTML = '';

    if (!hospitalName) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = '-- Choose Hospital First --';
        bloodGroupSelect.appendChild(option);
        return;
    }

    if (groups.length === 0) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = '-- No Blood Available --';
        bloodGroupSelect.appendChild(option);
        return;
    }

    const placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.textContent = '-- Select Blood Group --';
    bloodGroupSelect.appendChild(placeholder);

    groups.forEach(item => {
        const option = document.createElement('option');
        option.value = item.group;
        option.textContent = item.group + ' (' + item.qty + ' bags available)';
        if (item.group === selectedGroup) {
            option.selected = true;
        }
        bloodGroupSelect.appendChild(option);
    });
}

document.addEventListener('DOMContentLoaded', updateBloodGroupOptions);
</script>

</body>

</html>
