<?php
$conn = new mysqli('localhost', 'root', '', 'lps');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
