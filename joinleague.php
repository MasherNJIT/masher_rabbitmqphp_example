<?php
//This is for creating a league
session_start();

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    //echo "Welcome to your profile, " . $_SESSION['username'];
    //echo "Your user ID is: " . $_SESSION['user_id'];
} else {
    header("Location: index.php");
    exit();
}

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

$request = array();

$request['type'] = "join_league";
$request['lname'] = $_POST['league_name'];
$request['lpass'] = $_POST['league_password'];

$response = $client->send_request($request);

if($response['returnCode'] == 1) //This picks up return code 
{
  header("Location: home.php"); 
}
else if ($response['returnCode'] == 0)
{
  echo $response;
  header("Location: leagues.php");
}
?>
