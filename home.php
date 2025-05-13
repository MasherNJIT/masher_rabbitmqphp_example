<?php
include('partials/nav.php');
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    echo "Welcome to your profile, " . $_SESSION['username'];
    echo "Your user ID is: " . $_SESSION['user_id'];
} else {
    header("Location: index.php");
    exit();
}

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
$request['type'] = "show_leagues";
$response = $client->send_request($request);

?>

<div>
<h1>Home</h1>
</div>


<link rel="stylesheet" href="main.css"; ?>

<div>
<h2>Leagues Available</h2>
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
