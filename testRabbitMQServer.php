#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function doLogin($uname, $passwd, $sesStart) {
    $mysqli = require __DIR__ . "/database.php";
    
    $sql = "SELECT user_id, password FROM user_login WHERE username = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $uname);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
	    $userID = $user['user_id'];
	    $exp_date = $sesStart + 3600;

        if (password_verify($passwd, $user["password"])) {
            $insertSql = "INSERT INTO sessions (user_id, session_start, session_expires) VALUES (?, ?, ?)";
            $insertStmt = $mysqli->prepare($insertSql);
            $insertStmt->bind_param("iii", $userID, $sesStart, $exp_date);
            $insertStmt->execute();
            return array("returnCode" => '1', 'message' => "Login Successful");
        } else {
            return array("returnCode" => '0', 'message' => "Invalid input");
        }
    } else {
        return array("returnCode" => '0', 'message' => "Invalid username");
    }
}

function doRegister($fname, $lname, $email, $uname, $passwd)
{
   $passhash = password_hash($passwd, PASSWORD_DEFAULT);	
   $mysqli = require __DIR__ . "/database.php";
   $sql = "INSERT INTO user_login (f_name, l_name, email, username, password, created_at)
	   VALUES (?, ?, ?, ?, ?, ?)";
   $stmt = $mysqli->stmt_init();
   if (!$stmt->prepare($sql)) {
      return array("returnCode" => "0", "message" => 'statement prepare error');
   }
   $d = time();
   $stmt->bind_param("sssssi", $fname, $lname, $email, $uname, $passhash, $d);
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

function doValidate(/*$userID,*/ $sessionData, $sesStart)
{

$mysqli = require __DIR__ . "/database.php";
$sql = "INSERT INTO sessions (session_data, session_start)
	VALUES (?, ?)";

$stmt = $mysqli->stmt_init();
   if (!$stmt->prepare($sql)) {
      return array("returnCode" => "0", "message" => 'statement prepare error');
   }
$stmt->bind_param("si",/* $userID*/$sessionData, $sesStart);
if ($stmt->execute()) {
      return array ("returnCode" => "1", "message" => 'success');
   } else {
       return array("returnCode" => "0", "message" => 'statement execution failed');
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
      return doLogin($request['username'],$request['password'], $request['session']);
    case "validate_session":
	    return doValidate($request['session_data'], $request['session_start']);
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

