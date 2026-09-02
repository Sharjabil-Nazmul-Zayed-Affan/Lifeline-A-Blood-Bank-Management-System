<?php
session_start();
require_once '../Model/db.php';

$patientId = $_POST["patientId"] ?? "";
$action = $_POST["action"] ?? "";

if (!empty($patientId) && $action === "deletePatient") {
    $sql = "DELETE FROM patient WHERE P_Username = '$patientId'";[cite: 1, 18]
    $conn->query($sql);
}

header("Location: patientlist.html");
exit();
?>