<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "promanager";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexiune eșuată: " . $conn->connect_error);
}
?>