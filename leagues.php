<?php
include('partials/nav.php');
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

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

<div class="button-container">
  <button>Join League</button>
  <button>Create League</button>
</div>

<div>
  <h2>View Leagues</h2>
  <?php
  print_r($response);
  ?>
</div>