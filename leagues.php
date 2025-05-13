<?php
include('partials/nav.php');
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    //echo "Welcome to your profile, " . $_SESSION['username'];
    //echo "Your user ID is: " . $_SESSION['user_id'];
} else {
    header("Location: index.php");
    exit();
}

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
$request['type'] = "SelectTeams";
$response = $client->send_request($request);
?>

<link rel="stylesheet" href="main.css" ; ?>
<div>
  <h1>Leagues</h1>
</div>

<div>
  <h2>Add to My Leagues</h2>
</div>


<div>
  <h3>Create A League</h1>
</div>

<form action="createleague.php" method="POST">
    <div>
        <label for="league_name">League Name</label>
        <input type="text" id="league_name" name="league_name" required />
    </div> 
    <div> 
        <label for="password">League Password</label>
        <input type="password" id="league_password" name="league_password" />
    </div>
    <input type="submit" id="create_league" value="create_league" class="button"/>
</form>

<div>
  <h3>Join A League</h1>
</div>

<form action="joinleague.php" method="POST">
    <div>
        <label for="league_name">League Name</label>
        <input type="text" id="league_name" name="league_name" required />
    </div> 
    <div> 
        <label for="password">League Password</label>
        <input type="password" id="league_password" name="league_password" />
    </div>
    <input type="submit" id="join_league" value="join_league" class="button"/>
</form>

<div>
  <h2>View Teams</h2>
  <?php
  $tabledata = json_decode($response);

    function build_table($array){

    $html = '<table>';
   
    $html .= '<tr>';
    foreach($array[0] as $key=>$value){
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
    $html .= '</tr>';

    foreach( $array as $key=>$value){
        $html .= '<tr>';
        foreach($value as $key2=>$value2){
            $html .= '<td>' . htmlspecialchars($value2) . '</td>';
        }
        $html .= '</tr>';
    }

    $html .= '</table>';
    return $html;
    }

  echo(build_table($tabledata));

    ?>
</div>
