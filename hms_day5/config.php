<?php
$servername = "127.0.0.1:3307";
$username = "root";
$password = "";
$database = "hospital_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
// echo "✅ Connected successfully";
?>
