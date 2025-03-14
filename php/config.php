<?php
$servername = "localhost";
$username = "root"; 
$password = "";
$database = "tcu_campus_pulse";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
