<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: login.html");
    exit();
}

$hospital_tin = $_SESSION['hospital_tin'] ?? 'N/A';
$hospital_name = $_SESSION['hospital_name'] ?? 'Hospital';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Dashboard</title>
    <link rel="stylesheet" href="dashboardstyle.css">
    <style>
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
            background-color: #fff;
        }
        select:focus {
            border-color: #dc3545;
            outline: none;
        }
    </style>
</head>
<body>

    <div class="cntr">
        <fieldset>
            <legend>Hospital Dashboard</legend>

            <div class="nav-links">
                <a href="dashboard.php" class="active">Dashboard</a>
                <a href="editprofile.php">Edit Profile</a>
                <a href="donorlist.php">Donor List</a>
                <a href="logout.php">Logout</a>
            </div>

            <div style="margin-bottom: 20px;">
                Welcome, <strong><?php echo htmlspecialchars($hospital_name); ?></strong> (TIN: <?php echo htmlspecialchars($hospital_tin); ?>)
            </div>

            <form action="dashboardAction.php" method="POST">
                <table>
                    <tr>
                        <td class="bold">Blood Group:</td>
                        <td>
                            <select name="bloodGroup" required>
                                <option value="" disabled selected>Select Blood Group</option>
                                <option value="A+">A+</option>
                                <option value="B+">B+</option>
                                <option value="AB+">AB+</option>
                                <option value="O+">O+</option>
                                <option value="A-">A-</option>
                                <option value="B-">B-</option>
                                <option value="AB-">AB-</option>
                                <option value="O-">O-</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold">Quantity (Bags):</td>
                        <td><input type="number" name="quantity" min="1" placeholder="e.g. 2" required /></td>
                    </tr>
                    <tr>
                        <td class="bold">Donor Username:</td>
                        <td><input type="text" name="donorUsername" placeholder="Optional" /></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center; padding-top: 15px;">
                            <button type="submit">Add Blood Bag</button>
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
    </div>

</body>
</html>