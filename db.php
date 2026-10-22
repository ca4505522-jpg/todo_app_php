<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'todo_app';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>