<?php
$host = 'localhost';  // Database host
$dbname = 'duty_tracker';  // Database name
$username = 'root';  // Database username
$password = '';  // Database password (empty for XAMPP default)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>