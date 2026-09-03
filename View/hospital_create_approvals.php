<?php
// ==========================================
// View/hospital_create_approvals.php
// Hospital Account Creation Approval List
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
$pendingRequests = getPendingCreateRequests($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Account Creation Approvals - Lifeline</title>
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
                <h3>Pending Hospital Account Creation Approvals</h3>
                <span class="badge <?php echo (count($pendingRequests) == 0) ? 'zero' : ''; ?>">
                    <?php echo count($pendingRequests); ?> Pending
                </span>
            </div>

            <?php if (empty($pendingRequests)): ?>
                <p style="text-align: center; color: #777; padding: 30px;">
                    There are currently no pending hospital registration requests.
                </p>
                <div style="text-align: center; margin-top: 15px;">
                    <a href="dashboard.php" class="btn btn-back">Return to Dashboard</a>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Hospital Name</th>
                            <th>TIN Number</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Request Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingRequests as $req): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($req['Request_ID']); ?></td>
                                <td><strong><?php echo htmlspecialchars($req['H_Name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($req['H_TIN']); ?></td>
                                <td><?php echo htmlspecialchars($req['H_Email']); ?></td>
                                <td><?php echo htmlspecialchars($req['H_Phone_Number']); ?></td>
                                <td><?php echo htmlspecialchars($req['Request_Date']); ?></td>
                                <td>
                                    <!-- View Approval Button taking to View Account Approval Page -->
                                    <a href="view_create_approval.php?id=<?php echo urlencode($req['Request_ID']); ?>" class="btn btn-view">
                                        View Approval
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
