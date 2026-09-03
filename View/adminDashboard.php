<?php
session_start();
require_once '../Model/adminModel.php';

if (!isset($_SESSION['admin_user'])) {
    if (isset($_COOKIE['admin_user'])) {
        $_SESSION['admin_user'] = $_COOKIE['admin_user'];
    } else {
        header("Location: adminLogin.php?error=" . urlencode("Please login to access the dashboard."));
        exit();
    }
}

$admin = $_SESSION['admin_user'];

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
    <link rel="stylesheet" href="css/adminStyle.css">
</head>
<body>

    <header class="navbar">
        <h2>Lifeline Admin Dashboard</h2>
        <div class="user-info">
            <span>Welcome, <strong><?php echo htmlspecialchars($admin); ?></strong></span>
            <a href="../Controller/adminLogoutController.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <div class="container">

        <div id="ajaxAlert" style="display: none;"></div>

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

        <div class="action-cards">
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
                        <a href="adminHospitalCreateApprovals.php" class="btn btn-primary">View Approvals</a>
                    </div>
                </div>
            </div>

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
                        <a href="adminHospitalEditApprovals.php" class="btn btn-primary">View Edit Requests</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Approved Hospitals List (Active in System)</h3>
                <span id="totalHospitalCount">Total: <?php echo count($approvedHospitals); ?> Hospitals</span>
            </div>

            <div class="search-bar">
                <input type="text" id="searchHospitalInput" placeholder="Quick Search by Hospital Name, TIN, Phone or Address..." onkeyup="searchHospitals()">
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
                                    <button type="button" 
                                            class="btn btn-delete" 
                                            onclick="deleteHospitalAjax('<?php echo htmlspecialchars($hospital['H_TIN']); ?>', '<?php echo htmlspecialchars($hospital['H_Name']); ?>', this)">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>

    <script src="js/adminAjax.js"></script>

</body>
</html>
