#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('bpl/Arsenal.php');
require_once('bpl/Aston_Villa.php');
require_once('bpl/Bournemouth.php');
require_once('bpl/Brentford.php');
require_once('bpl/Brighton.php');
require_once('bpl/Chelsea.php');
require_once('bpl/Crystal_Palace.php');
require_once('bpl/Everton.php');
require_once('bpl/Fulham.php');
require_once('bpl/Ipswich_Town.php');
require_once('bpl/Leicester_City.php');
require_once('bpl/Liverpool.php');
require_once('bpl/Manchester_City.php');
require_once('bpl/Manchester_United.php');
require_once('bpl/Newcastle.php');
require_once('bpl/Nottingham.php');
require_once('bpl/Southampton.php');
require_once('bpl/Tottenham.php');
require_once('bpl/West_Ham.php');
require_once('bpl/Wolves.php');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

$apiKey = "485649";

// Get all Premier League teams dynamically
$apiUrl = "https://www.thesportsdb.com/api/v1/json/{$apiKey}/search_all_teams.php?l=English%20Premier%20League";
$results = file_get_contents($apiUrl);
$teamsData = json_decode($results, true);

if ($teamsData === null || empty($teamsData['teams'])) {
    die("Error: Unable to retrieve team data. Check API key or endpoint.");
}

$allPlayers = [];

foreach ($teamsData['teams'] as $team) {
    $teamName = $team['strTeam'] ?? "";

    if (!empty($teamName)) {
        $playersUrl = "https://www.thesportsdb.com/api/v1/json/{$apiKey}/searchplayers.php?t=" . urlencode($teamName);
        $playerResults = file_get_contents($playersUrl);
        $playersData = json_decode($playerResults, true);

        if (!empty($playersData['player'])) {
            foreach ($playersData['player'] as $player) {
                if (isset($player['strPosition']) &&
                    strpos($player['strTeam'], "Women") === false &&
                    strpos($player['strTeam'], "U21") === false &&
                    strpos($player['strTeam'], "U23") === false &&
                    $player['strPosition'] !== "Manager") {

                    $allPlayers[] = [
                        'name'     => $player['strPlayer'] ?? "",
                        'position' => $player['strPosition'] ?? "",
                        'idPlayer' => $player['idPlayer'] ?? "",
                        'idTeam'   => $player['idTeam'] ?? ""
                    ];
                }
            }
            echo "Fetched players from: $teamName" . PHP_EOL;
        }
    }
}

// Send all player data in one request
$request = [
    'type'    => "APIplayers",
    'players' => $allPlayers
];

$response = $client->send_request($request);

echo "Sent all player data in one request." . PHP_EOL;
print_r($response);

echo $argv[0] . " END" . PHP_EOL;
?>
                                                            
