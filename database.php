<?php

$host = '192.168.192.192';
$username = 'evan';
$password = 'evan';
$database = 'testdb';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully";

$conn->close();

?>
