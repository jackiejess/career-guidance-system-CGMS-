<?php
$host = "localhost";  // Change if using a remote server
$user = "root";       // Default XAMPP username
$pass = "";           // Default XAMPP password (empty)
$dbname = "career";   // Your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session
session_start();
?>
