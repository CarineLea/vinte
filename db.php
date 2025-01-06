<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vinted";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Uncomment the following line for debugging
// echo "Connected successfully";
?> 