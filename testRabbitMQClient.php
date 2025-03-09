#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('soccerData.php');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "test message";
}


$apiKey = "485649";
$teamName = "Manchester City"; // Properly formatted team name
$apiUrl = "https://www.thesportsdb.com/api/v1/json/{$apiKey}/searchplayers.php?t=" . urlencode($teamName);

// Get the results from the API
$results = file_get_contents($apiUrl);

// Decode the JSON response
$arrayCode = json_decode($results, true);

// Check if the response is valid
if ($arrayCode === null) {
    die("Error: Unable to retrieve data. Check API key or endpoint.");
}

// Array to hold filtered players' information
$filteredPlayers = [];

if (!empty($arrayCode['player'])) {
    foreach ($arrayCode['player'] as $player) {
        // Ensure 'strPosition' exists and filter out unwanted teams and the manager
        if (isset($player['strPosition']) &&
            strpos($player['strTeam'], "Women") === false &&
            strpos($player['strTeam'], "U21") === false &&
            strpos($player['strTeam'], "U23") === false &&
            $player['strPosition'] !== "Manager") {

            // Store only relevant player details
            $filteredPlayers[] = [
                'name' => $player['strPlayer'] ?? "",
                'position' => $player['strPosition'] ?? "",
                'idPlayer' => $player['idPlayer'] ?? "",
                'idTeam' => $player['idTeam'] ?? ""
            ];
        }
    }
}

// Output formatted array structure
echo "<pre>";
print_r($filteredPlayers);
echo "</pre>";



$request = array();
$request['type'] = "APIplayers";
$request['name'] = $player['strPlayer'];
$request['position'] = $player['strPosition'];
$request['idPlayer'] = $player['idPlayer'];
$request['idTeam'] = $player['idTeam'];

$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

