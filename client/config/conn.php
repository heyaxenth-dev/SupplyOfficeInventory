<?php 
// Connection config
$host = 'localhost';
$db   = 'soi_db';
$user = 'root';
$pass = ''; // Change this based on your XAMPP/MAMP/WAMP settings

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>