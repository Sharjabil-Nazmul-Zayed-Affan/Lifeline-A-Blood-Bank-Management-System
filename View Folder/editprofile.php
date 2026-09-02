<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: login.html");
    exit();
}

$hospital_tin = $_SESSION['hospital_tin'] ?? '';
$hospital_name = $_SESSION['hospital_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="dashboardstyle.css">
</head>
<body>

    <div class="cntr">
        <fieldset>
            <legend>Edit Hospital Profile</legend>

            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="editprofile.php" class="active">Edit Profile</a>
                <a href="donorlist.php">Donor List</a>
                <a href="logout.php">Logout</a>
            </div>

            <form action="editprofileValidation.php" method="POST">
                <input type="hidden" name="hospital_tin" value="<?php echo htmlspecialchars($hospital_tin); ?>">
                <table>
                    <tr>
                        <td class="bold">Hospital Name:</td>
                        <td><input type="text" name="hospitalName" value="<?php echo htmlspecialchars($hospital_name); ?>" required /></td>
                    </tr>
                    <tr>
                        <td class="bold">Address:</td>
                        <td><input type="text" name="address" placeholder="Enter Address" required /></td>
                    </tr>
                    <tr>
                        <td class="bold">Email:</td>
                        <td><input type="email" name="email" placeholder="Enter Email" required /></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center; padding-top: 15px;">
                            <button type="submit">Save Changes</button>
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
    </div>

</body>
</html>