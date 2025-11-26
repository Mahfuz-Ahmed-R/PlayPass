<?php
$conn = new mysqli("localhost", "root", "", "backend");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>