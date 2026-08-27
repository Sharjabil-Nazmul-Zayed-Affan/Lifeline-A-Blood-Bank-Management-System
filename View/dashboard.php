<?php
// ==========================================
// View/dashboard.php - Admin Dashboard
// ==========================================

session_start();
require_once '../Model/adminModel.php';

// Auth Guard: Check Session or Cookie
if (!isset($_SESSION['admin_user'])) {
    if (isset($_COOKIE['admin_user'])) {
        $_SESSION['admin_user'] = $_COOKIE['admin_user'];
    } else {
        header("Location: login.php?error=" . urlencode("Please login to access the dashboard."));
        exit();
    }
}

$admin = $_SESSION['admin_user'];

// Fetch counts & approved hospital list from Model
$pendingCreateCount = getPendingCreateCount($conn);
$pendingEditCount   = getPendingEditCount($conn);
$approvedHospitals  = getApprovedHospitals($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Lifeline Blood Bank</title>
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

    <!-- Navigation Bar -->
    <header class="navbar">
        <h2>Lifeline Admin Dashboard</h2>
        <div class="user-info">
            <span>Welcome, <strong><?php echo htmlspecialchars($admin); ?></strong></span>
            <a href="../Controller/logoutController.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <div class="container">

        <!-- Notification Messages -->
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Approval Navigation Buttons & Count Badges -->
        <div class="action-cards">
            <!-- 1. Hospital Account Create Approval Button -->
            <div class="action-card">
                <div>
                    <h3>Hospital Account Creation Approvals</h3>
                    <p>Review new hospital registrations waiting for approval</p>
                </div>
                <div style="text-align: right;">
                    <span class="badge <?php echo ($pendingCreateCount == 0) ? 'zero' : ''; ?>">
                        Pending: <?php echo $pendingCreateCount; ?>
                    </span>
                    <div style="margin-top: 10px;">
                        <a href="hospital_create_approvals.php" class="btn btn-primary">View Approvals</a>
                    </div>
                </div>
            </div>

            <!-- 2. Hospital Edit Approval Button -->
            <div class="action-card">
                <div>
                    <h3>Hospital Profile Edit Approvals</h3>
                    <p>Review requested changes to hospital information</p>
                </div>
                <div style="text-align: right;">
                    <span class="badge <?php echo ($pendingEditCount == 0) ? 'zero' : ''; ?>">
                        Pending: <?php echo $pendingEditCount; ?>
                    </span>
                    <div style="margin-top: 10px;">
                        <a href="hospital_edit_approvals.php" class="btn btn-primary">View Edit Requests</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- List of Approved Hospitals -->
        <div class="card">
            <div class="card-header">
                <h3>Approved Hospitals List (Active in System)</h3>
                <span>Total: <?php echo count($approvedHospitals); ?> Hospitals</span>
            </div>

            <?php if (empty($approvedHospitals)): ?>
                <p style="text-align: center; color: #777; padding: 20px;">No approved hospitals found in the database.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>TIN Number</th>
                            <th>Hospital Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($approvedHospitals as $hospital): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($hospital['H_TIN']); ?></strong></td>
                                <td><?php echo htmlspecialchars($hospital['H_Name']); ?></td>
                                <td><?php echo htmlspecialchars($hospital['H_Email']); ?></td>
                                <td><?php echo htmlspecialchars($hospital['H_Phone_Number']); ?></td>
                                <td><?php echo htmlspecialchars($hospital['H_Address']); ?></td>
                                <td>
                                    <!-- Delete Button that removes hospital and its accesses -->
                                    <a href="../Controller/hospitalController.php?action=delete&tin=<?php echo urlencode($hospital['H_TIN']); ?>" 
                                       class="btn btn-delete" 
                                       onclick="return confirm('Are you sure you want to delete <?php echo htmlspecialchars($hospital['H_Name']); ?> (TIN: <?php echo htmlspecialchars($hospital['H_TIN']); ?>)? This will delete all its information and access!');">
                                        Delete
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
