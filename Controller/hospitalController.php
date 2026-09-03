<?php
session_start();
require_once '../Model/adminModel.php';

if (!isset($_SESSION['admin_user'])) {
    if (isset($_COOKIE['admin_user'])) {
        $_SESSION['admin_user'] = $_COOKIE['admin_user'];
    } else {
        header("Location: ../View/login.php?error=" . urlencode("Please login first!"));
        exit();
    }
}

$admin = $_SESSION['admin_user'];
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

switch ($action) {

    //  Approve Hospital Creation Request
    case 'approve_create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
            $requestId = intval($_POST['request_id']);
            $success = approveHospitalCreateRequest($conn, $requestId, $admin);

            if ($success) {
                header("Location: ../View/dashboard.php?msg=" . urlencode("Hospital account approved and created successfully!"));
            } else {
                header("Location: ../View/dashboard.php?error=" . urlencode("Failed to approve hospital account."));
            }
            exit();
        }
        break;

    // Reject Hospital Creation Request
    case 'reject_create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
            $requestId = intval($_POST['request_id']);
            $reason = isset($_POST['rejection_reason']) ? trim($_POST['rejection_reason']) : 'Rejected by Admin';
            
            $success = rejectHospitalCreateRequest($conn, $requestId, $admin, $reason);

            if ($success) {
                header("Location: ../View/dashboard.php?msg=" . urlencode("Hospital account request rejected."));
            } else {
                header("Location: ../View/dashboard.php?error=" . urlencode("Failed to reject request."));
            }
            exit();
        }
        break;

    // Approve Hospital Profile Edit Request
    case 'approve_edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_request_id'])) {
            $requestId = intval($_POST['update_request_id']);
            $success = approveHospitalEditRequest($conn, $requestId, $admin);

            if ($success) {
                header("Location: ../View/dashboard.php?msg=" . urlencode("Hospital profile changes approved and updated!"));
            } else {
                header("Location: ../View/dashboard.php?error=" . urlencode("Failed to approve hospital changes."));
            }
            exit();
        }
        break;

    // Reject Hospital Profile Edit Request
    case 'reject_edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_request_id'])) {
            $requestId = intval($_POST['update_request_id']);
            $reason = isset($_POST['rejection_reason']) ? trim($_POST['rejection_reason']) : 'Rejected by Admin';

            $success = rejectHospitalEditRequest($conn, $requestId, $admin, $reason);

            if ($success) {
                header("Location: ../View/dashboard.php?msg=" . urlencode("Hospital profile edit request rejected."));
            } else {
                header("Location: ../View/dashboard.php?error=" . urlencode("Failed to reject edit request."));
            }
            exit();
        }
        break;

    // Delete Hospital & all its information
    case 'delete':
        $tin = isset($_GET['tin']) ? trim($_GET['tin']) : (isset($_POST['tin']) ? trim($_POST['tin']) : '');
        if (!empty($tin)) {
            $success = deleteHospital($conn, $tin);
            if ($success) {
                header("Location: ../View/dashboard.php?msg=" . urlencode("Hospital and all related information deleted successfully!"));
            } else {
                header("Location: ../View/dashboard.php?error=" . urlencode("Failed to delete hospital."));
            }
            exit();
        }
        break;

    default:
        header("Location: ../View/dashboard.php");
        exit();
}
?>
