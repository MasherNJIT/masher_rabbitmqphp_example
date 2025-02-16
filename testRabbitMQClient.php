#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "login request sent";
}

$request = array();
$registration = array(); 
/*
$request['type'] = "Login";
$request['username'] = "steve";
$request['password'] = "password";
^Use for testing purposes only without login page^
*/ 
//Sending Login
$request['type'] = "login";
$request['username'] = $_POST['username'];
$request['password'] = $_POST['password'];


$request['message'] = $msg;
$response = $client->send_request($request);
//$response = $client->publish($request);



if($response['returnCode'] == 1) //This picks up return code 
//if the front-end recieves a message from the MQ with a return code of 1, it means the login is successful 
{
  header("Location: index.php"); 
  //echo "Heres the username" .$request['username'].   "and heres the password"  .$request['password']; //NOTE: this is just testing to make sure that hte username and password went over
}
else if ($response['returnCode'] == 0) //returns user back to login page if login is a failure 
{
  header("Location: index.html");
}

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

