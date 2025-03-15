<?php
// CODE REFERENCED FROM: Mike Gabriel Ayson
session_start();

echo "logging in...";
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

if (isset($argv[1])) {
    $msg = $argv[1];
} else {
    $msg = "login request sent";
}

$username = $_POST['username'];
$password = $_POST['password'];
$d = time();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: home.php");
    exit();
}

$request = array();
$request['type'] = "login";
$request['username'] = $username;
$request['password'] = $password;
$request['session'] = $d;
$request['message'] = $msg;

$response = $client->send_request($request);

if ($response['returnCode'] == 1) {
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $username;
    $_SESSION['user_id'] = $response['user_id'];
  
    header("Location: home.php");
    exit();
} else if ($response['returnCode'] == 0) {
    header("Location: index.php");
    exit();
}

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;
?>

