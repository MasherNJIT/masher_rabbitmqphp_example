#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require 'vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();


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
      $mail = new PHPMailer(true);

      try {
    	$mail->isSMTP();
    	$mail->Host       = $_ENV['SMTP_HOST'];
    	$mail->SMTPAuth   = true;
    	$mail->Username   = $_ENV['SMTP_USER'];
    	$mail->Password   = $_ENV['SMTP_PASS'];
    	$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    	$mail->Port       = $_ENV['SMTP_PORT'];

    	$mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
    	$mail->addAddress($email, $fname);

    	$mail->isHTML(true);
    	$mail->Subject = 'Test Email';
	$mail->Body    = '<h1>Hello!</h1>
			  <p>registration success</p>';

    	$mail->send();
	echo 'Email sent successfully!';
		} catch (Exception $e) {
    	echo "Error: {$mail->ErrorInfo}";
		}
      return array ("returnCode" => "1", "message" => 'success');
   } else {
       if ($mysqli->errno === 1062) {
          return array ("returnCode" => "0", 'message' => "email taken");
       } else {
          return array ("returnCode" => "0", 'message' => "other error");
       }
   }

}

function doPlayers($APIplayers)
{

	$mysqli = require __DIR__ . "/database.php";
	$sql = "INSERT INTO api_players (player_name, player_id_api, player_position, team_name) VAlUES (?, ?, ?, ?)";
	$stmt = $mysqli->stmt_init();
	
	if (!$stmt->prepare($sql)) {
            return array("returnCode" => "0", "message" => 'statement prepare error');
	 }
	
	foreach ($APIplayers as $player) {
		$stmt->bind_param("siss", $player['player_name'], $player['player_id_api'], $player['player_position'], $player['team_name']);
	if (!$stmt->execute()) {
              return array("returnCode" => "0", "message" => 'Statement execution failed');
             }
        }
        return array("returnCode" => "1", "message" => 'Statement execution success');
}


function doTeams($APIdata)
{
	$mysqli = require __DIR__ . "/database.php";
	$sql = "INSERT INTO api_teams (team_name, team_id_api, stadium, league) VALUES (?, ?, ?, ?)";
	$stmt = $mysqli->stmt_init();

	if (!$stmt->prepare($sql)) {
    	   return array("returnCode" => "0", "message" => 'Statement prepare error');
	 }

	foreach ($APIdata as $team) {
    	   $stmt->bind_param("siss", $team['team_name'], $team['team_id_api'], $team['stadium'], $team['league']);
    
    	   if (!$stmt->execute()) {
              return array("returnCode" => "0", "message" => 'Statement execution failed');
    	     }
	}
	return array("returnCode" => "1", "message" => 'Statement execution success');
}

function doValidate($sessionData, $sesStart)
{

$mysqli = require __DIR__ . "/database.php";
$sql = "INSERT INTO sessions (session_data, session_start)
	VALUES (?, ?)";

$stmt = $mysqli->stmt_init();
   if (!$stmt->prepare($sql)) {
      return array("returnCode" => "0", "message" => 'statement prepare error');
   }
$stmt->bind_param("si", $sessionData, $sesStart);
if ($stmt->execute()) {
      return array ("returnCode" => "1", "message" => 'success');
   } else {
       return array("returnCode" => "0", "message" => 'statement execution failed');
   }

}

function getTeams()
{

	$mysqli = require __DIR__ . "/database.php";
	$sql = "SELECT * FROM api_teams";
	
	$result = $mysqli->query($sql);
	if (!$result) {
            return array("returnCode" => "0", "message" => 'select failed');
    	  }	

        $teams = [];
        while ($row = $result->fetch_assoc()) {
            $teams[] = $row;
         }

	return json_encode($teams);
}

function getPlayers()
{

        $mysqli = require __DIR__ . "/database.php";
        $sql = "SELECT * FROM api_players";

        $result = $mysqli->query($sql);
        if (!$result) {
            return array("returnCode" => "0", "message" => 'select failed');
          }

        $teams = [];
        while ($row = $result->fetch_assoc()) {
            $teams[] = $row;
         }

        return json_encode($teams);
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
    case "APIplayers":
	    return doPlayers($request['players']);
    case "APIteams":
	    return doTeams($request['teams']);
    case "SelectTeams":
	    return getTeams();
    case "SelectPlayers":
	    return getPlayers();
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

