<?php
$conn = new mysqli("localhost", "root", "", "registration_event");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>