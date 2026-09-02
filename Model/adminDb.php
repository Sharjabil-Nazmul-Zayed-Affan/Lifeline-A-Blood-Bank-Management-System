<?php
// ==========================================
// Model/adminDb.php - Database Connection
// ==========================================

$host     = "localhost";
$user     = "root";
$password = "";
// NOTE: Change database name if yours is different in phpMyAdmin (e.g. 'lifeline' or 'blood_bank')
$dbname   = "lifeline"; 

// Create connection
$conn = mysqli_connect($host, $user, $password, $dbname);

// Check connection
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>
