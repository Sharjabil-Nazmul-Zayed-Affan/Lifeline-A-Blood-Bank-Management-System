<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: login.html");
    exit();
}

$hospital_tin = mysqli_real_escape_string($conn, $_SESSION['hospital_tin'] ?? '');

$sql = "SELECT 
            d.*,
            b.Blood_Group AS BagBloodGroup,
            SUM(b.Number_of_Bags) AS TotalBags,
            MAX(b.Date_Blood_Added) AS LastDate
        FROM blood_bag b
        INNER JOIN donor d ON b.D_Username = d.D_Username
        WHERE b.H_TIN = '$hospital_tin'
        GROUP BY d.D_Username";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Management</title>
    <link rel="stylesheet" href="dashboardstyle.css">
    <style>
        .btn-issue {
            background-color: #ffc107;
            color: #000 !important;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
            display: inline-block;
            margin-right: 5px;
        }
        .btn-issue:hover {
            background-color: #e0a800;
        }
    </style>
</head>
<body>

    <div class="cntr">
        <fieldset>
            <legend>Donor Management</legend>

            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="editprofile.php">Edit Profile</a>
                <a href="donorlist.php" class="active">Donor List</a>
                <a href="logout.php">Logout</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Blood Group</th>
                        <th>Mobile</th>
                        <th>Address</th>
                        <th>Total Bags</th>
                        <th>Last Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php 
                                $uname   = $row['D_Username'] ?? $row['Username'] ?? '';
                                $name    = $row['D_Name'] ?? $row['Name'] ?? 'N/A';
                                $gender  = $row['D_Gender'] ?? $row['Gender'] ?? 'N/A';
                                $bg      = $row['BagBloodGroup'] ?? $row['D_BloodGroup'] ?? $row['BloodGroup'] ?? 'N/A';
                                $mobile  = $row['D_Mobile'] ?? $row['Mobile'] ?? $row['D_Phone'] ?? $row['Phone'] ?? 'N/A';
                                $address = $row['D_Address'] ?? $row['Address'] ?? 'N/A';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($uname); ?></td>
                                <td><?php echo htmlspecialchars($name); ?></td>
                                <td><?php echo htmlspecialchars($gender); ?></td>
                                <td class="bold-blood"><?php echo htmlspecialchars($bg); ?></td>
                                <td><?php echo htmlspecialchars($mobile); ?></td>
                                <td><?php echo htmlspecialchars($address); ?></td>
                                <td><?php echo htmlspecialchars($row['TotalBags'] ?? 0); ?></td>
                                <td><?php echo htmlspecialchars($row['LastDate'] ?? 'N/A'); ?></td>
                                <td>
                                    <a href="issueBag.php?username=<?php echo urlencode($uname); ?>" 
                                       class="btn-issue" 
                                       onclick="return confirm('Do you want to deduct 1 blood bag?');">-1 Bag</a>

                                    <a href="deleteDonor.php?id=<?php echo urlencode($uname); ?>" 
                                       class="btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this donor?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align:center;">No donor records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </fieldset>
    </div>

</body>
</html>