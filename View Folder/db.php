<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "lifeline__a_blood_bank_mangement_system"; // Double underscore after lifeline

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>