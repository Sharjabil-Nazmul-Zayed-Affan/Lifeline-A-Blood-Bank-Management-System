<?php
session_start();
require_once '../Model/adminModel.php';

$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || isset($_REQUEST['ajax']) 
          || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if (!isset($_SESSION['admin_user'])) {
    if (isset($_COOKIE['admin_user'])) {
        $_SESSION['admin_user'] = $_COOKIE['admin_user'];
    } else {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Session expired. Please login again.', 'redirect' => 'adminLogin.php']);
            exit();
        } else {
            header("Location: ../View/adminLogin.php?error=" . urlencode("Please login first!"));
            exit();
        }
    }
}

$admin = $_SESSION['admin_user'];
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

switch ($action) {

    case 'approve_create':
        if (isset($_REQUEST['request_id'])) {
            $requestId = intval($_REQUEST['request_id']);
            $success = approveHospitalCreateRequest($conn, $requestId, $admin);

            if ($success) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Hospital account approved and created successfully!',
                        'redirect' => 'adminDashboard.php'
                    ]);
                    exit();
                } else {
                    header("Location: ../View/adminDashboard.php?msg=" . urlencode("Hospital account approved and created successfully!"));
                    exit();
                }
            } else {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'Failed to approve hospital account.']);
                    exit();
                } else {
                    header("Location: ../View/adminDashboard.php?error=" . urlencode("Failed to approve hospital account."));
                    exit();
                }
            }
        }
        break;

    case 'reject_create':
        if (isset($_REQUEST['request_id'])) {
            $requestId = intval($_REQUEST['request_id']);
            $reason = isset($_REQUEST['rejection_reason']) && !empty(trim($_REQUEST['rejection_reason'])) 
                      ? trim($_REQUEST['rejection_reason']) 
                      : 'Rejected by Admin';

            $success = rejectHospitalCreateRequest($conn, $requestId, $admin, $reason);

            if ($success) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Hospital registration request rejected.',
                        'redirect' => 'adminDashboard.php'
                    ]);
                    exit();
                } else {
                    header("Location: ../View/adminDashboard.php?msg=" . urlencode("Hospital registration request rejected."));
                    exit();
                }
            } else {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'Failed to reject request.']);
                    exit();
                } else {
                    header("Location: ../View/adminDashboard.php?error=" . urlencode("Failed to reject request."));
                    exit();
                }
            }
        }
        break;

    case 'approve_edit':
        if (isset($_REQUEST['update_request_id'])) {
            $requestId = intval($_REQUEST['update_request_id']);
            $success = approveHospitalEditRequest($conn, $requestId, $admin);

            if ($success) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Hospital profile changes approved and updated!',
                        'redirect' => 'adminDashboard.php'
                    ]);
                    exit();
                } else {
                    header("Location: ../View/adminDashboard.php?msg=" . urlencode("Hospital profile changes approved and updated!"));
                    exit();
                }
            } else {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update hospital changes.']);
                    exit();
                } else {
                    header("Location: ../View/adminDashboard.php?error=" . urlencode("Failed to update hospital changes."));
                    exit();
                }
            }
        }
        break;

    case 'reject_edit':
        if (isset($_REQUEST['update_request_id'])) {
            $requestId = intval($_REQUEST['update_request_id']);
            $reason = isset($_REQUEST['rejection_reason']) && !empty(trim($_REQUEST['rejection_reason'])) 
                      ? trim($_REQUEST['rejection_reason']) 
                      : 'Rejected by Admin';

            $success = rejectHospitalEditRequest($conn, $requestId, $admin, $reason);

            if ($success) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Hospital profile edit request rejected.',
                        'redirect' => 'adminDashboard.php'
                    ]);
                    exit();
                } else {
                    header("Location: ../View/adminDashboard.php?msg=" . urlencode("Hospital profile edit request rejected."));
                    exit();
                }
            } else {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'Failed to reject edit request.']);
                    exit();
                } else {
                    header("Location: ../View/adminDashboard.php?error=" . urlencode("Failed to reject edit request."));
                    exit();
                }
            }
        }
        break;

    case 'delete':
        $tin = isset($_REQUEST['tin']) ? trim($_REQUEST['tin']) : '';
        if (!empty($tin)) {
            $success = deleteHospital($conn, $tin);

            if ($success) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Hospital (TIN: ' . $tin . ') and all its information were deleted successfully!'
                    ]);
                    exit();
                } else {
                    header("Location: ../View/adminDashboard.php?msg=" . urlencode("Hospital deleted successfully!"));
                    exit();
                }
            } else {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'Failed to delete hospital from database.']);
                    exit();
                } else {
                    header("Location: ../View/adminDashboard.php?error=" . urlencode("Failed to delete hospital."));
                    exit();
                }
            }
        }
        break;

    default:
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid action requested.']);
            exit();
        } else {
            header("Location: ../View/adminDashboard.php");
            exit();
        }
}
?>
