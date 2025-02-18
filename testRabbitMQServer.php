#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function doLogin($uname, $passwd) {
    $mysqli = require __DIR__ . "/database.php";
    //if (!$mysqli || !($mysqli instanceof mysqli)) {
    //    return array("returnCode" => '0', 'message' => "Database connection failed");
    // }

    $sql = "SELECT password FROM user_login WHERE username = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $uname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
	if ($passwd == $user["password"]) {
		return array("returnCode" => '1', 'message' => "Login Successful");
        } else {
		return array("returnCode" => '0', 'message' => "Wrong password");
        }
    } else {
	    return array("returnCode" => '0', 'message' => "User not found");
    }
}

function doRegister($fname, $lname, $email, $uname, $passwd)
{
   $passhash = password_hash($passwd, PASSWORD_DEFAULT);	
   $mysqli = require __DIR__ . "/database.php";
   $sql = "INSERT INTO user_login (f_name, l_name, email, password, created_at, username)
	   VALUES (?, ?, ?, ?, CURDATE(), ?)";
   $stmt = $mysqli->stmt_init();
   if (!$stmt->prepare($sql)) {
      return array("returnCode" => "0", "message" => 'statement prepare error');
   }

   $stmt->bind_param("sssss", $fname, $lname, $email, $uname, $passwd);
   if ($stmt->execute()) {
      return array ("returnCode" => "1", "message" => 'success');
   } else {
       if ($mysqli->errno === 1062) {
          return array ("returnCode" => "0", 'message' => "email taken");
       } else {
          return array ("returnCode" => "0", 'message' => "other error");
       }
   }

}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "login":
      return doLogin($request['username'],$request['password']);
    case "validate_session":
      return doValidate($request['sessionId']);
    case "register":
      return doRegister($request['f_name'], $request['l_name'], $request['email'], 
	  $request['username'], $request['password']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

