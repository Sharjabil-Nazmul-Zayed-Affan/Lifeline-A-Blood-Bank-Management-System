<?php
session_start();
require_once '../Model/adminModel.php';

if (!isset($_SESSION['admin_user'])) {
    if (isset($_COOKIE['admin_user'])) {
        $_SESSION['admin_user'] = $_COOKIE['admin_user'];
    } else {
        header("Location: adminLogin.php?error=" . urlencode("Please login first."));
        exit();
    }
}

$admin = $_SESSION['admin_user'];
$requestId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$request = getEditRequestById($conn, $requestId);

if (!$request) {
    header("Location: adminHospitalEditApprovals.php?error=" . urlencode("Edit request not found."));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Edit Approval - Lifeline</title>
    <link rel="stylesheet" href="css/adminStyle.css">
</head>
<body>

    <header class="navbar">
        <h2>Lifeline Admin Portal</h2>
        <div class="user-info">
            <span>Welcome, <strong><?php echo htmlspecialchars($admin); ?></strong></span>
            <a href="adminHospitalEditApprovals.php" class="logout-btn" style="background-color: transparent; color: #fff; border: 1px solid #fff;">Back to List</a>
            <a href="../Controller/adminLogoutController.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <div class="container">

        <div id="ajaxAlert" style="display: none;"></div>

        <div class="card">
            <div class="card-header">
                <h3>Hospital Profile Update Request Details (#<?php echo htmlspecialchars($request['Update_Request_ID']); ?>)</h3>
                <span class="badge">Status: <?php echo htmlspecialchars($request['Request_Status']); ?></span>
            </div>

            <div class="details-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 20px;">
                <div class="detail-item">
                    <label>Hospital TIN</label>
                    <p><strong><?php echo htmlspecialchars($request['H_TIN']); ?></strong></p>
                    <a href="https://secure.incometax.gov.bd/TINHome" target="_blank" class="btn btn-verify">
                        Verify TIN number &rarr;
                    </a>
                </div>

                <div class="detail-item">
                    <label>Requested Date</label>
                    <p><?php echo htmlspecialchars($request['Request_Date']); ?></p>
                </div>
            </div>

            <h4 style="margin-top: 15px; color: #990000;">Comparison of Profile Changes:</h4>
            <table class="compare-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Information Field</th>
                        <th style="width: 37.5%;">Current Profile Details</th>
                        <th style="width: 37.5%;">Requested New Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Hospital Name</strong></td>
                        <td class="compare-old"><?php echo htmlspecialchars($request['Current_Name'] ? $request['Current_Name'] : 'N/A'); ?></td>
                        <td class="compare-new"><?php echo htmlspecialchars($request['New_H_Name']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Official Email</strong></td>
                        <td class="compare-old"><?php echo htmlspecialchars($request['Current_Email'] ? $request['Current_Email'] : 'N/A'); ?></td>
                        <td class="compare-new"><?php echo htmlspecialchars($request['New_H_Email']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Phone Number</strong></td>
                        <td class="compare-old"><?php echo htmlspecialchars($request['Current_Phone'] ? $request['Current_Phone'] : 'N/A'); ?></td>
                        <td class="compare-new"><?php echo htmlspecialchars($request['New_H_Phone_Number']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Address</strong></td>
                        <td class="compare-old"><?php echo htmlspecialchars($request['Current_Address'] ? $request['Current_Address'] : 'N/A'); ?></td>
                        <td class="compare-new"><?php echo htmlspecialchars($request['New_H_Address']); ?></td>
                    </tr>
                </tbody>
            </table>

            <form action="../Controller/adminHospitalController.php" method="POST" style="margin-top: 25px;">
                <input type="hidden" name="update_request_id" value="<?php echo htmlspecialchars($request['Update_Request_ID']); ?>">

                <div class="form-group">
                    <label for="rejection_reason">Rejection Note (Optional if rejecting):</label>
                    <input type="text" id="rejection_reason" name="rejection_reason" placeholder="Enter reason if rejecting changes">
                </div>

                <div class="btn-actions">
                    <button type="button" class="btn btn-approve" onclick="handleApprovalAjax(event, 'approve_edit');">
                        Approve Edit
                    </button>

                    <button type="button" class="btn btn-reject" onclick="handleApprovalAjax(event, 'reject_edit');">
                        Reject Edit
                    </button>

                    <a href="adminHospitalEditApprovals.php" class="btn btn-back">Cancel</a>
                </div>
            </form>

        </div>

    </div>

    <script src="js/adminAjax.js"></script>

</body>
</html>
