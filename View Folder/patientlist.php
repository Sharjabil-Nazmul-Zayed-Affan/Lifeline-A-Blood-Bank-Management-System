<?php
session_start();
require_once 'db.php';

$patientId = $_POST["patientId"] ?? "";
$action = $_POST["action"] ?? "";

if (!empty($patientId) && $action === "deletePatient") {
    // Delete patient using P_Username[cite: 1]
    $sql = "DELETE FROM patient WHERE P_Username = '$patientId'"; //[cite: 1]
    $conn->query($sql);
}

header("Location: patientlist.html");
exit();
?>