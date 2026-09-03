<?php
// Model/patientModel.php - Database operations for Patient entity

require_once __DIR__ . '/patientDb.php';

function getPatientByUsername($username) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM Patient WHERE P_Username = ? LIMIT 1");
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    return [
        'username'   => $row['P_Username'],
        'password'   => $row['P_Password'],
        'name'       => $row['P_Name'],
        'dob'        => $row['P_Date_of_Birth'],
        'gender'     => $row['P_Gender'] ?? 'Male',
        'bloodGroup' => $row['P_Blood_Group'],
        'phone'      => $row['P_Mobile_Number'],
        'address'    => $row['P_Address'],
        'email'      => $row['P_Email'],
        'photo'      => $row['P_Photo'] ?? null
    ];
}

function patientEmailExists($email, $excludeUsername = null) {
    $db = getDBConnection();
    if ($excludeUsername !== null) {
        $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM Patient WHERE LOWER(P_Email) = LOWER(?) AND P_Username != ?");
        $stmt->execute([$email, $excludeUsername]);
    } else {
        $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM Patient WHERE LOWER(P_Email) = LOWER(?)");
        $stmt->execute([$email]);
    }
    $row = $stmt->fetch();
    return $row && ((int)$row['cnt'] > 0);
}

function patientUsernameExists($username) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM Patient WHERE P_Username = ?");
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    return $row && ((int)$row['cnt'] > 0);
}

function registerPatient($username, $password, $name, $phone, $bloodGroup, $address, $email, $dob = null, $photo = null) {
    $db = getDBConnection();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO Patient (P_Username, P_Password, P_Name, P_Date_of_Birth, P_Mobile_Number, P_Blood_Group, P_Address, P_Email, P_Photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([$username, $hashedPassword, $name, $dob, $phone, $bloodGroup, $address, $email, $photo]);
    if ($result) {
        syncSQLFile();
    }
    return $result;
}

function updatePatientProfile($username, $name, $phone, $bloodGroup, $address, $email, $dob = null) {
    $db = getDBConnection();
    $stmt = $db->prepare("UPDATE Patient SET P_Name = ?, P_Mobile_Number = ?, P_Blood_Group = ?, P_Address = ?, P_Email = ?, P_Date_of_Birth = COALESCE(?, P_Date_of_Birth) WHERE P_Username = ?");
    $result = $stmt->execute([$name, $phone, $bloodGroup, $address, $email, $dob, $username]);
    if ($result) {
        syncSQLFile();
    }
    return $result;
}

function updatePatientPassword($username, $newPassword) {
    $db = getDBConnection();
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE Patient SET P_Password = ? WHERE P_Username = ?");
    $result = $stmt->execute([$hashed, $username]);
    if ($result) {
        syncSQLFile();
    }
    return $result;
}

function updatePatientPhoto($username, $photoFileName) {
    $db = getDBConnection();
    $stmt = $db->prepare("UPDATE Patient SET P_Photo = ? WHERE P_Username = ?");
    $result = $stmt->execute([$photoFileName, $username]);
    if ($result) {
        syncSQLFile();
    }
    return $result;
}

function verifyPatientPassword($inputPassword, $storedPassword) {
    if (password_verify($inputPassword, $storedPassword)) {
        return true;
    }
    // Support seed plain-text passwords (e.g. jahan123)
    return ($inputPassword === $storedPassword);
}
?>
