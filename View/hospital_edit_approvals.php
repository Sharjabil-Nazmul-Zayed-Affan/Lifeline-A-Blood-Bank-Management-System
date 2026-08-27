<?php
// ==========================================
// View/hospital_edit_approvals.php
// List of Hospital Profile Edit Requests
// ==========================================

session_start();
require_once '../Model/adminModel.php';

// Auth Guard
if (!isset($_SESSION['admin_user'])) {
    if (isset($_COOKIE['admin_user'])) {
        $_SESSION['admin_user'] = $_COOKIE['admin_user'];
    } else {
        header("Location: login.php?error=" . urlencode("Please login first."));
        exit();
    }
}

$admin = $_SESSION['admin_user'];
$pendingEditRequests = getPendingEditRequests($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Edit Approvals - Lifeline</title>
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

    <!-- Navbar -->
    <header class="navbar">
        <h2>Lifeline Admin Portal</h2>
        <div class="user-info">
            <span>Welcome, <strong><?php echo htmlspecialchars($admin); ?></strong></span>
            <a href="dashboard.php" class="logout-btn" style="background-color: transparent; color: #fff; border: 1px solid #fff;">Back to Dashboard</a>
            <a href="../Controller/logoutController.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <div class="container">

        <div class="card">
            <div class="card-header">
                <h3>Pending Hospital Profile Edit Requests</h3>
                <span class="badge <?php echo (count($pendingEditRequests) == 0) ? 'zero' : ''; ?>">
                    <?php echo count($pendingEditRequests); ?> Pending
                </span>
            </div>

            <?php if (empty($pendingEditRequests)): ?>
                <p style="text-align: center; color: #777; padding: 30px;">
                    There are currently no pending hospital edit requests.
                </p>
                <div style="text-align: center; margin-top: 15px;">
                    <a href="dashboard.php" class="btn btn-back">Return to Dashboard</a>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Update ID</th>
                            <th>Hospital TIN</th>
                            <th>Current Name</th>
                            <th>Requested New Name</th>
                            <th>New Phone</th>
                            <th>Request Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingEditRequests as $req): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($req['Update_Request_ID']); ?></td>
                                <td><strong><?php echo htmlspecialchars($req['H_TIN']); ?></strong></td>
                                <td><?php echo htmlspecialchars($req['Current_H_Name'] ? $req['Current_H_Name'] : 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($req['New_H_Name']); ?></td>
                                <td><?php echo htmlspecialchars($req['New_H_Phone_Number']); ?></td>
                                <td><?php echo htmlspecialchars($req['Request_Date']); ?></td>
                                <td>
                                    <!-- View Edit Request Button taking to View Edit Approval Page -->
                                    <a href="view_edit_approval.php?id=<?php echo urlencode($req['Update_Request_ID']); ?>" class="btn btn-view">
                                        View Edit Request
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>
