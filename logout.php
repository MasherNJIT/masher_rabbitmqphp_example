<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

session_start();
$userID = $_SESSION['user_id'];

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

$request = array();

$request['type'] = "logout";
$request['user_id'] = $userID;

$response = $client->send_request($request);

if ($response['returnCode'] == 1)
{
session_unset();
session_destroy();
header("Location: index.php");
}
else
{
echo $response['message'];
}

echo "client recieved response" . PHP_EOL;
print_r($response);

echo $argv[0] . " END" . PHP_EOL;
?>
