#!/usr/bin/php
<?php

$db1 = 'mysql';
$db2 = 'it490';
$mydb = new mysqli('192.168.192.71','bobby','bobby','mysql');

if ($mydb->errno != 0)
{
        echo "failed to connect to database: ". $mydb->error . PHP_EOL;
        exit(0);
}

echo "successfully connected to database: ".$db1.PHP_EOL;

$checkuser = 'bobby';

$query1 = "select user from user where user = ?";
$stmt = $mydb->prepare($query1);
$stmt->bind_param("s", $checkuser);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
	echo "User: $checkuser exists\n";
} else {
        echo "User does not exist\n";
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

$t_api_table = 'api_teams';
$p_api_table = 'api_players';


$query4 = "CREATE TABLE IF NOT EXISTS ".$t_api_table." (
	team_id INT PRIMARY KEY AUTO_INCREMENT,
	team_name VARCHAR(255) NULL UNIQUE,
	team_id_api INT NOT NULL,
	stadium VARCHAR(255),
	league VARCHAR(255)
    )";
if ( $mydb->query($query4)== TRUE){
        echo "table: ".$t_api_table." created succesfully\n";
} else {
        echo "Error: " . $mydb->error;
}

$query5 = "CREATE TABLE IF NOT EXISTS ".$p_api_table." (
	player_id INT PRIMARY KEY AUTO_INCREMENT,
	player_name VARCHAR(255) NOT NULL,
	player_id_api INT NOT NULL,
	player_posistion VARCHAR(255) NOT NULL,
	team_id INT NOT NULL,
	goals_scored INT,
	pass_percent INT,
	clean_sheets INT,
	point_earned INT,
	FOREIGN KEY (team_id) REFERENCES api_teams(team_id) ON DELETE CASCADE    
	)";
if ( $mydb->query($query5)== TRUE){
        echo "table: ".$p_api_table." created succesfully\n";
} else {
        echo "Error: " . $mydb->error;
}


$mydb->close();
?>
