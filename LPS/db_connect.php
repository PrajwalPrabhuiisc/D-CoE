<?php
// Database connection settings
$host = 'localhost';
$dbname = 'lps_db';
$username = 'root'; // Default XAMPP MySQL user (adjust if changed)
$password = '';     // Default XAMPP MySQL password (adjust if changed)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
