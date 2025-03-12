<?php
include('partials/nav.php');
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
$request['type'] = "SelectPlayers";
$response = $client->send_request($request);
?>

<link rel="stylesheet" href="main.css"; ?>

<div>
<h1>Players</h1>
</div>

<div>
<h2>View Players</h2>
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