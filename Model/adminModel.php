<?php
require_once 'db.php';

// Admin Login
function checkAdminLogin($conn, $username, $password) {
    $username = mysqli_real_escape_string($conn, trim($username));
    $password = mysqli_real_escape_string($conn, trim($password));

    $query = "SELECT * FROM admin WHERE A_Username = '$username' AND Password = '$password'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        return mysqli_fetch_assoc($result);
    }
    return false;
}

// Count of Pending Account Create Requests
function getPendingCreateCount($conn) {
    $query = "SELECT COUNT(*) AS total FROM hospital_registration_request WHERE Request_Status = 'Pending'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    return 0;
}

// Count of Pending Profile Edit Requests
function getPendingEditCount($conn) {
    $query = "SELECT COUNT(*) AS total FROM hospital_update_request WHERE Request_Status = 'Pending'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    return 0;
}

// List of Approved Active Hospitals
function getApprovedHospitals($conn) {
    $query = "SELECT * FROM hospital ORDER BY Created_Date DESC";
    $result = mysqli_query($conn, $query);
    $hospitals = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $hospitals[] = $row;
        }
    }
    return $hospitals;
}

// List of Pending Hospital Create Requests
function getPendingCreateRequests($conn) {
    $query = "SELECT * FROM hospital_registration_request WHERE Request_Status = 'Pending' ORDER BY Request_Date DESC";
    $result = mysqli_query($conn, $query);
    $requests = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $requests[] = $row;
        }
    }
    return $requests;
}

// Single Create Request by Request_ID
function getCreateRequestById($conn, $id) {
    $id = intval($id);
    $query = "SELECT * FROM hospital_registration_request WHERE Request_ID = $id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) == 1) {
        return mysqli_fetch_assoc($result);
    }
    return false;
}

// Approve Hospital Account Creation
function approveHospitalCreateRequest($conn, $requestId, $adminUsername) {
    $requestId = intval($requestId);
    $adminUsername = mysqli_real_escape_string($conn, $adminUsername);

    $req = getCreateRequestById($conn, $requestId);
    if (!$req) {
        return false;
    }

    $tin   = mysqli_real_escape_string($conn, $req['H_TIN']);
    $name  = mysqli_real_escape_string($conn, $req['H_Name']);
    $email = mysqli_real_escape_string($conn, $req['H_Email']);
    $phone = mysqli_real_escape_string($conn, $req['H_Phone_Number']);
    $addr  = mysqli_real_escape_string($conn, $req['H_Address']);

    // Check if hospital with this TIN already exists
    $checkQuery = "SELECT * FROM hospital WHERE H_TIN = '$tin'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $insertQuery = "UPDATE hospital SET 
                        H_Name = '$name', 
                        H_Email = '$email', 
                        H_Phone_Number = '$phone', 
                        H_Address = '$addr', 
                        Is_Active = 1 
                        WHERE H_TIN = '$tin'";
    } else {
       
        $insertQuery = "INSERT INTO hospital (H_TIN, H_Name, H_Email, H_Phone_Number, H_Address, Is_Active) 
                        VALUES ('$tin', '$name', '$email', '$phone', '$addr', 1)";
    }

    $insertSuccess = mysqli_query($conn, $insertQuery);

    if ($insertSuccess) {
        $updateReq = "UPDATE hospital_registration_request 
                      SET Request_Status = 'Approved', 
                          Review_Date = NOW(), 
                          Reviewed_By = '$adminUsername' 
                      WHERE Request_ID = $requestId";
        return mysqli_query($conn, $updateReq);
    }
    return false;
}

// Reject Hospital Account Creation
function rejectHospitalCreateRequest($conn, $requestId, $adminUsername, $reason = "Rejected by admin") {
    $requestId = intval($requestId);
    $adminUsername = mysqli_real_escape_string($conn, $adminUsername);
    $reason = mysqli_real_escape_string($conn, $reason);

    $updateReq = "UPDATE hospital_registration_request 
                  SET Request_Status = 'Rejected', 
                      Review_Date = NOW(), 
                      Reviewed_By = '$adminUsername',
                      Rejection_Reason = '$reason'
                  WHERE Request_ID = $requestId";
    return mysqli_query($conn, $updateReq);
}

// List of Pending Hospital Edit Requests
function getPendingEditRequests($conn) {
    $query = "SELECT ur.*, h.H_Name AS Current_H_Name 
              FROM hospital_update_request ur 
              LEFT JOIN hospital h ON ur.H_TIN = h.H_TIN 
              WHERE ur.Request_Status = 'Pending' 
              ORDER BY ur.Request_Date DESC";
    $result = mysqli_query($conn, $query);
    $requests = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $requests[] = $row;
        }
    }
    return $requests;
}

// Single Edit Request by Update_Request_ID
function getEditRequestById($conn, $id) {
    $id = intval($id);
    $query = "SELECT ur.*, 
                     h.H_Name AS Current_Name, 
                     h.H_Email AS Current_Email, 
                     h.H_Phone_Number AS Current_Phone, 
                     h.H_Address AS Current_Address 
              FROM hospital_update_request ur 
              LEFT JOIN hospital h ON ur.H_TIN = h.H_TIN 
              WHERE ur.Update_Request_ID = $id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) == 1) {
        return mysqli_fetch_assoc($result);
    }
    return false;
}

// Approve Hospital Profile Edit
function approveHospitalEditRequest($conn, $requestId, $adminUsername) {
    $requestId = intval($requestId);
    $adminUsername = mysqli_real_escape_string($conn, $adminUsername);

    $req = getEditRequestById($conn, $requestId);
    if (!$req) {
        return false;
    }

    $tin      = mysqli_real_escape_string($conn, $req['H_TIN']);
    $newName  = mysqli_real_escape_string($conn, $req['New_H_Name']);
    $newEmail = mysqli_real_escape_string($conn, $req['New_H_Email']);
    $newPhone = mysqli_real_escape_string($conn, $req['New_H_Phone_Number']);
    $newAddr  = mysqli_real_escape_string($conn, $req['New_H_Address']);

    // Update the hospital table
    $updateHospital = "UPDATE hospital 
                       SET H_Name = '$newName', 
                           H_Email = '$newEmail', 
                           H_Phone_Number = '$newPhone', 
                           H_Address = '$newAddr' 
                       WHERE H_TIN = '$tin'";
    $hospitalUpdated = mysqli_query($conn, $updateHospital);

    if ($hospitalUpdated) {
        // Update request status to Approved
        $updateReq = "UPDATE hospital_update_request 
                      SET Request_Status = 'Approved', 
                          Review_Date = NOW(), 
                          Reviewed_By = '$adminUsername' 
                      WHERE Update_Request_ID = $requestId";
        return mysqli_query($conn, $updateReq);
    }
    return false;
}

// Reject Hospital Profile Edit
function rejectHospitalEditRequest($conn, $requestId, $adminUsername, $reason = "Rejected by admin") {
    $requestId = intval($requestId);
    $adminUsername = mysqli_real_escape_string($conn, $adminUsername);
    $reason = mysqli_real_escape_string($conn, $reason);

    $updateReq = "UPDATE hospital_update_request 
                  SET Request_Status = 'Rejected', 
                      Review_Date = NOW(), 
                      Reviewed_By = '$adminUsername',
                      Rejection_Reason = '$reason'
                  WHERE Update_Request_ID = $requestId";
    return mysqli_query($conn, $updateReq);
}

// Delete Hospital and all associated records safely
function deleteHospital($conn, $tin) {
    $tin = mysqli_real_escape_string($conn, $tin);

    // Delete child rows to prevent foreign key errors
    mysqli_query($conn, "DELETE FROM reservation WHERE H_TIN = '$tin'");
    mysqli_query($conn, "DELETE FROM blood_bag WHERE H_TIN = '$tin'");
    mysqli_query($conn, "UPDATE donor SET Last_Donation_Hospital_TIN = NULL WHERE Last_Donation_Hospital_TIN = '$tin'");
    mysqli_query($conn, "DELETE FROM hospital_update_request WHERE H_TIN = '$tin'");

    // Delete hospital
    $deleteHospital = "DELETE FROM hospital WHERE H_TIN = '$tin'";
    return mysqli_query($conn, $deleteHospital);
}
?>
