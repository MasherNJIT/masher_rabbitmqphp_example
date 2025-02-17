#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

/*
function doLogin($uname,$passwd)
{
    $mysqli = require __DIR__ . "/database.php";
    $sql = sprintf('SELECT password FROM user_login WHERE email = "%s"',
            $mysqli->real_escape_string($uname));
    $result = $mysqli->query($sql);
    if ($result && $user = $result->fetch_assoc()) {
            if ($passwd == $user["password"]) {
                    return array("returnCode" => '1',
                            'message' => "Login Successful");
                } else {
                        return array("returnCode" => '0',
                                'message' => "wrong password");
        }
    } else {
            return array("returnCode" => '0', 'message' => "user not found");
    }
}
 */

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
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

