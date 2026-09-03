<?php
// ==========================================
// View/view_create_approval.php
// View Details of Hospital Account Approval
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
$requestId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$request = getCreateRequestById($conn, $requestId);

if (!$request) {
    header("Location: hospital_create_approvals.php?error=" . urlencode("Request not found."));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Account Creation Approval - Lifeline</title>
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

    <!-- Navbar -->
    <header class="navbar">
        <h2>Lifeline Admin Portal</h2>
        <div class="user-info">
            <span>Welcome, <strong><?php echo htmlspecialchars($admin); ?></strong></span>
            <a href="hospital_create_approvals.php" class="logout-btn" style="background-color: transparent; color: #fff; border: 1px solid #fff;">Back to List</a>
            <a href="../Controller/logoutController.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <div class="container">

        <div class="card">
            <div class="card-header">
                <h3>Hospital Account Creation Request Details (#<?php echo htmlspecialchars($request['Request_ID']); ?>)</h3>
                <span class="badge">Status: <?php echo htmlspecialchars($request['Request_Status']); ?></span>
            </div>

            <!-- Hospital Details Grid -->
            <div class="details-grid">
                
                <div class="detail-item">
                    <label>Hospital Name</label>
                    <p><?php echo htmlspecialchars($request['H_Name']); ?></p>
                </div>

                <div class="detail-item">
                    <label>TIN Number</label>
                    <p><strong><?php echo htmlspecialchars($request['H_TIN']); ?></strong></p>
                    
                    <!-- Verify TIN Number Button taking admin to Bangladesh Tax TIN Portal -->
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

            <!-- Form with Approve and Reject Buttons -->
            <form action="../Controller/hospitalController.php" method="POST" style="margin-top: 25px;">
                <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['Request_ID']); ?>">

                <div class="form-group">
                    <label for="rejection_reason">Rejection Note (Optional if rejecting):</label>
                    <input type="text" id="rejection_reason" name="rejection_reason" placeholder="Enter reason if rejecting request">
                </div>

                <div class="btn-actions">
                    <!-- Approve Button -->
                    <button type="submit" name="action" value="approve_create" class="btn btn-approve" onclick="return confirm('Confirm approving this hospital account?');">
                        Approve Account
                    </button>

                    <!-- Reject Button -->
                    <button type="submit" name="action" value="reject_create" class="btn btn-reject" onclick="return confirm('Are you sure you want to reject this hospital account request?');">
                        Reject Account
                    </button>

                    <a href="hospital_create_approvals.php" class="btn btn-back">Cancel</a>
                </div>
            </form>

        </div>

    </div>

</body>
</html>
