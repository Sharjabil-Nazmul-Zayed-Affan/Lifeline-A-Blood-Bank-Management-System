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

$request = getCreateRequestById($conn, $requestId);

if (!$request) {
    header("Location: adminHospitalCreateApprovals.php?error=" . urlencode("Request not found."));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Account Creation Approval - Lifeline</title>
    <link rel="stylesheet" href="css/adminStyle.css">
</head>
<body>

    <header class="navbar">
        <h2>Lifeline Admin Portal</h2>
        <div class="user-info">
            <span>Welcome, <strong><?php echo htmlspecialchars($admin); ?></strong></span>
            <a href="adminHospitalCreateApprovals.php" class="logout-btn" style="background-color: transparent; color: #fff; border: 1px solid #fff;">Back to List</a>
            <a href="../Controller/adminLogoutController.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <div class="container">

        <div id="ajaxAlert" style="display: none;"></div>

        <div class="card">
            <div class="card-header">
                <h3>Hospital Account Creation Request Details (#<?php echo htmlspecialchars($request['Request_ID']); ?>)</h3>
                <span class="badge">Status: <?php echo htmlspecialchars($request['Request_Status']); ?></span>
            </div>

            <div class="details-grid">
                
                <div class="detail-item">
                    <label>Hospital Name</label>
                    <p><?php echo htmlspecialchars($request['H_Name']); ?></p>
                </div>

                <div class="detail-item">
                    <label>TIN Number</label>
                    <p><strong><?php echo htmlspecialchars($request['H_TIN']); ?></strong></p>
                    
                    <a href="https://secure.incometax.gov.bd/TINHome" target="_blank" class="btn btn-verify">
                        Verify TIN number &rarr;
                    </a>
                </div>

                <div class="detail-item">
                    <label>Official Email</label>
                    <p><?php echo htmlspecialchars($request['H_Email']); ?></p>
                </div>

                <div class="detail-item">
                    <label>Phone Number</label>
                    <p><?php echo htmlspecialchars($request['H_Phone_Number']); ?></p>
                </div>

                <div class="detail-item" style="grid-column: span 2;">
                    <label>Hospital Address</label>
                    <p><?php echo htmlspecialchars($request['H_Address']); ?></p>
                </div>

                <div class="detail-item">
                    <label>Request Submitted Date</label>
                    <p><?php echo htmlspecialchars($request['Request_Date']); ?></p>
                </div>

            </div>

            <form action="../Controller/adminHospitalController.php" method="POST" style="margin-top: 25px;">
                <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['Request_ID']); ?>">

                <div class="form-group">
                    <label for="rejection_reason">Rejection Note (Optional if rejecting):</label>
                    <input type="text" id="rejection_reason" name="rejection_reason" placeholder="Enter reason if rejecting request">
                </div>

                <div class="btn-actions">
                    <button type="button" class="btn btn-approve" onclick="handleApprovalAjax(event, 'approve_create');">
                        Approve Account
                    </button>

                    <button type="button" class="btn btn-reject" onclick="handleApprovalAjax(event, 'reject_create');">
                        Reject Account
                    </button>

                    <a href="adminHospitalCreateApprovals.php" class="btn btn-back">Cancel</a>
                </div>
            </form>

        </div>

    </div>

    <script src="js/adminAjax.js"></script>

</body>
</html>
