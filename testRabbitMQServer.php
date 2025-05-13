#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require 'vendor/autoload.php';
use Twilio\Rest\Client;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

function sendSMS($phoneNum)
{

$sid = $_ENV['TWILIO_SID'];
$token = $_ENV['TWILIO_TOKEN'];
$services = $_ENV['TWILIO_SERVICES'];
$twilio = new Client($sid, $token);
$verification = $twilio->verify->v2->services($services)
				   ->verifications
				   ->create("+1" . $phoneNum, "sms");
}


function verifyTFA($code, $phone)
{
	
$sid = $_ENV['TWILIO_SID'];
$token = $_ENV['TWILIO_TOKEN'];
$services = $_ENV["TWILIO_SERVICES"];

$twilio = new Client($sid, $token);
$verification = $twilio->verify->v2->services($services)
                                   ->verificationChecks
                                   ->create([ "to" => "+1". $phone,
                                              "code" => $code]);

if ($verification->status === "approved") {
        return array("returnCode" => "1", "message" => "Two-factor authentication successful");
    } else {
        return array("returnCode" => "0", "message" => "Two-factor authentication failed");
    }
}


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
            return array("returnCode" => '1', 'user_id' => $userID);
        } else {
            return array("returnCode" => '0', 'message' => "Invalid input");
        }
    } else {
        return array("returnCode" => '0', 'message' => "Invalid username");
    }
}
function doTwoFactor($randCode)

{
    $mysqli = require __DIR__ . "/database.php";
    
    $sql = "SELECT rand_num FROM 2fa WHERE rand_num = ? ORDER BY id DESC LIMIT 1";
    $stmt = $mysqli->stmt_init();
    if (!$stmt->prepare($sql)) {
        return array("returnCode" => "0", "message" => "stmt prepare issue");
    }
    $stmt->bind_param("i", $randCode);
    if (!$stmt->execute()) {
        return array("returnCode" => "0", "message" => "Select failed");
    }
    $stmt->bind_result($retrievedRandNum);
    $stmt->fetch();
    $stmt->close();

    if ($retrievedRandNum === $randCode) {
        $delSQL = "DELETE FROM 2fa WHERE rand_num = ?";
        $stmt2 = $mysqli->stmt_init();
        if (!$stmt2->prepare($delSQL)) {
            return array("returnCode" => "0", "message" => "stmt prepare issue during delete");
        }
        $stmt2->bind_param("i", $randCode);
        if (!$stmt2->execute()) {
            return array("returnCode" => "0", "message" => "Delete failed");
        }
        $stmt2->close();

        return array("returnCode" => "1", "message" => "Two-factor authentication successful");
    } else {
        return array("returnCode" => "0", "message" => "Two-factor authentication failed");
    }
}


function doRegister($fname, $lname, $email, $uname, $passwd, $phone)
{
   $passhash = password_hash($passwd, PASSWORD_DEFAULT);	
   $mysqli = require __DIR__ . "/database.php";

   // STMT 1 // CHECKS EXISTING USER
   
   $sql = "SELECT username FROM user_login WHERE username = ?";
   $stmt = $mysqli->stmt_init();
   if ($stmt->prepare($sql)){
       $stmt->bind_param("s", $uname);
       $stmt->execute();
       $stmt->store_result();

       if ($stmt->num_rows > 0) {
	   return array("returncode" => "0", "message" => 'Username exists');
       }
   } else {		   
	return array("returncode" => "0", "message" => 'Error preparing statement.');
   }

   // STMT 2 // INSERTING INTO USERS TABLE
   
   $sql1 = "INSERT INTO user_login (f_name, l_name, phone, email, username, password, created_at)
	    VALUES (?, ?, ?, ?, ?, ?, ?)";	   
   $stmt1 = $mysqli->stmt_init();	   
   if (!$stmt1->prepare($sql1)) {   	   
       return array("returnCode" => "0", "message" => 'statement prepare error');	   
   }

//   if ($stmt1->execute()) {
   
       $d = time();	   
       $stmt1->bind_param("ssssssi", $fname, $lname, $phone, $email, $uname, $passhash, $d);
       if ($stmt1->execute()) {
	   // SENDS EMAIL //		       
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
	     $mail->Body    = '<h1>Hello!</h1><p>Welcome!!</><p> <p>registration success</p>';	$mail->send();			   
	     echo 'Email sent successfully!';		   
            } catch (Exception $e) {			   
	        echo "Error: {$mail->ErrorInfo}";		   
	    }		   
 	   // SENDS EMAIL //	
	     sendSMS($phone);	
             return array ("returnCode" => "1", "message" => 'success', "phone" => $phone);
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
		$stmt->bind_param("siss", $player['player_name'], $player['player_id_api'], $player['player_position'], 					  $player['team_name']);
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

function doValidate($userID)
{

	$mysqli = require __DIR__ . "/database.php";
	$sql = "SELECT * FROM sessions WHERE user_id = ?";

	$stmt = $mysqli->prepare($sql);
	if (!$stmt) {
    	    return array("returnCode" => "0", "message" => "Statement prepare error");
	}

	$stmt->bind_param("i", $userID); 
	if (!$stmt->execute()) {
    	    return array("returnCode" => "0", "message" => "Statement execution failed");
	}

	$result = $stmt->get_result();
    	   if ($row = $result->fetch_assoc()) {
               $currentEpoch = time();
               if ($currentEpoch >= $row['session_start'] && $currentEpoch <= $row['session_expire']) {
            	   return array("returnCode" => "1", "message" => "Session is valid");
	     } else {
	           doLogout($userID);
                   return array("returnCode" => "0", "message" => "Session has expired or is not started yet");
        	}
    	 } else {
               return array("returnCode" => "0", "message" => "No session found for user");
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

function doLogout($userID)
{

	$mysqli = require __DIR__ . "/database.php";
	$sql = "DELETE FROM sessions WHERE user_id = ?";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param('i', $userID);

        if ($stmt->execute()) {
            return array("returnCode" => "1", "message" => 'delete worked');
        } else {
	    return array("returnCode" => "0", "message" => 'delete failed');
	}
}


function createTeam($userID, $name)
{

	$mysqli = require __DIR__ . "/database.php";
	$sql = "INSERT INTO team_players (user_id, team_name) VALUES (?, ?)";

	$stmt = $mysqli->stmt_init();

	if (!$stmt->prepare($sql)) {
	    return array("returnCode" => "0", "message" => 'stmt error');
	}

	$stmt->bind_param("is", $userID, $name);
	if ($stmt->execute()) {
	    return array("returnCode" => "1");
	} else {
	    return array("returnCode" => "0");
        }	    
}

function createLeague($leagueName, $passwd, $ownerName, $ownerID)
{
	$passhash = password_hash($passwd, PASSWORD_DEFAULT);
	$mysqli = require __DIR__ . "/database.php";
	$sql = 'INSERT INTO league_name (league_name, league_password_hash, league_owner, owner_id)
				 VALUES (?, ?, ?, ?)';
	$stmt = $mysqli->stmt_init();
	if (!$stmt->prepare($sql)) {
            return array("returnCode" => "0", "message" => 'stmt error');
        }
	$stmt->bind_param("sssi", $leagueName, $passhash, $ownerName, $ownerID);
        if ($stmt->execute()) {
            return array("returnCode" => "1");
        } else {
            return array("returnCode" => "0");
        }
}

function showLeagues()
{

   $mysqli = require __DIR__ . "/database.php";
   $sql = "SELECT * FROM league_name";

   $result = $mysqli->query($sql);
     if (!$result) {
         return array("returnCode" => "0", "message" => 'select failed');
       }

        $leagues = [];
        while ($row = $result->fetch_assoc()) {
            $leagues[] = $row;
         }

        return json_encode($leagues);
}

function joinLeague($league, $passwd)
{

    $mysqli = require __DIR__ . "/database.php";
    $sql = "SELECT league_id, league_password_hash FROM league_name WHERE league_name = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $league);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($league = $result->fetch_assoc()) {
        $leagueID = $league['league_id'];
        if (password_verify($passwd, $league["league_password_hash"])) {
            return array("returnCode" => '1', 'league_id' => $leagueID);
        } else {
            return array("returnCode" => '0', 'message' => "Invalid input");
        }
    } else {
        return array("returnCode" => '0', 'message' => "Invalid username");
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
	    return doValidate($request['user_id']);
    case "register":
            return doRegister($request['f_name'], $request['l_name'], $request['email'], 
	                      $request['username'], $request['password'], $request['phone']);
    case "APIplayers":
	    return doPlayers($request['players']);
    case "APIteams":
	    return doTeams($request['teams']);
    case "SelectTeams":
	    return getTeams();
    case "SelectPlayers":
	    return getPlayers();
    case "show_leagues":
	    return showLeagues();
    case "logout":
	    return doLogout($request['user_id']);
    case "create_team":
	    return createTeam($request['user_id'], $request['team_name']);
    case "twoFA":
	    return sendSMS($request['phone']);
    case "logout":
	    return doLogout($request['user_id']);
    case "verifytfa":
	    return verifyTFA($request['code'], $request['phone']);
    case "create_league":
	    return createLeague($request['lname'], $request['lpass'], $request['ownerName'], $request['owner_id']);
    case "join_league":
	    return joinLeague($request['lname'], $request['lpass']); 
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

