#!/usr/bin/php
<?php

$db1 = 'mysql';
$db2 = 'it490';
$mydb = new mysqli('127.0.0.1','bobby','12345','mysql');

if ($mydb->errno != 0)
{
        echo "failed to connect to database: ". $mydb->error . PHP_EOL;
        exit(0);
}

echo "successfully connected to database: ".$db1.PHP_EOL;

$checkuser = 'evan';

$query1 = "select user from user where user = ?";
$stmt = $mydb->prepare($query1);
$stmt->bind_param("s", $checkuser);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
	echo "User: $checkuser exists\n";
} else {
        echo "User dont exist\n";
}

$mydb->close();

$mydb = new mysqli('127.0.0.1','bobby','12345','it490');

if ($mydb->errno != 0)
{
        echo "failed to connect to database: ". $mydb->error . PHP_EOL;
        exit(0);
}

echo "successfully connected to database: ".$db2.PHP_EOL;


$query2 = "CREATE TABLE IF NOT EXISTS user_login(
	user_id INT PRIMARY KEY AUTO_INCREMENT,
	f_name VARCHAR(255) NOT NULL,
	l_name VARCHAR(255) NOT NULL,
	email VARCHAR(255) NOT NULL UNIQUE,
	username VARCHAR(255) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL,
	created_at INT(11) NOT NULL
	)";
if ( $mydb->query($query2)== TRUE){
	echo "table created succesfully\n";
} else {
	echo "Error: " . $mydb->error;
}

$query3 = "CREATE TABLE IF NOT EXISTS sessions (
	session_id INT PRIMARY KEY AUTO_INCREMENT,
	user_id INT NOT NULL,  
    	session_data TEXT, 
	session_start INT(11) NOT NULL,
	session_expires INT(11),
   	FOREIGN KEY (user_id) REFERENCES user_login(user_id) ON DELETE CASCADE
    )";
if ( $mydb->query($query3)== TRUE){
        echo "table created succesfully\n";
} else {
        echo "Error: " . $mydb->error;
}

$mydb->close();
?>
