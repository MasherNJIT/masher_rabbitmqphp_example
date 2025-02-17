<?php

$host = '192.168.192.71';
$username = 'evan';
$password = 'evan';
$database = 'it490';

$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "Connected successfully";

return $mysqli;

?>
