<?php
$servername = "localhost";
$username = "root";
$password = "Puh0nsoftdev";
$dbname = "cse4_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>