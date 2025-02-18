<?php
echo "registering..";
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
  $msg = "resgistration request sent";
}

$request = array();
$registration = array(); 

$request['type'] = "register";
$request['email']=$_POST['email'];
$request['f_name']=$_POST['f_name'];
$request['l_name']=$_POST['l_name'];
$request['username']=$_POST['username'];
$request['password'] = $_POST['password'];


$request['message'] = $msg;
$response = $client->send_request($request);
//$response = $client->publish($request);



if($response['returnCode'] == 1) //This picks up return code 
//if the front-end recieves a message from the MQ with a return code of 1, it means the registration is successful 
{
  header("Location: index.php"); 
}
else if ($response['returnCode'] == 0) //returns user back to login page if registration is a failure
{
  echo $response;
  header("Location: register.php");
}

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;
?>