#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('teams.php');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

$apiKey = "485649";
$teamsUrl = "https://www.thesportsdb.com/api/v1/json/{$apiKey}/search_all_teams.php?l=English%20Premier%20League";

// Get teams from API
$teamsResults = file_get_contents($teamsUrl);
$teamsData = json_decode($teamsResults, true);

if ($teamsData === null || empty($teamsData['teams'])) {
    die("Error: Unable to retrieve team data. Check API key or endpoint.");
}

$allPlayers = [];

foreach ($teamsData['teams'] as $team) {
    $teamId = $team['idTeam'] ?? null;
    if (!$teamId) {
        continue; // Skip if no valid team ID
    }

    // Fetch players for this team
    $playersUrl = "https://www.thesportsdb.com/api/v1/json/{$apiKey}/lookup_all_players.php?id={$teamId}";
    $playersResults = file_get_contents($playersUrl);
    $playersData = json_decode($playersResults, true);

    if (!isset($playersData['player']) || empty($playersData['player'])) {
        continue; // Skip if no players found
    }

    // Store players for this team
    foreach ($playersData['player'] as $player) {
        $allPlayers[] = [
            'player_name'   => $player['strPlayer'] ?? "",
            'player_id_api' => $player['idPlayer'] ?? "",
            'player_position' => $player['strPosition'] ?? "",
            'team_id'       => $teamId,
        ];
    }
}

// Create request with all players
$request = [
    'type'    => "APIplayers",
    'players' => $allPlayers
];

// Send the request once with all players
$response = $client->send_request($request);

echo "Sent all player data in one request." . PHP_EOL;
print_r($response);

echo $argv[0] . " END" . PHP_EOL;
?>

