#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('teams.php');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

$apiKey = "485649";
$apiUrl = "https://www.thesportsdb.com/api/v1/json/{$apiKey}/search_all_teams.php?l=English%20Premier%20League";

$results = file_get_contents($apiUrl);
$teamsData = json_decode($results, true);

if ($teamsData === null || empty($teamsData['teams'])) {
    die("Error: Unable to retrieve team data. Check API key or endpoint.");
}

$allTeams = [];

foreach ($teamsData['teams'] as $team) {
    $allTeams[] = [
        'team_name'   => $team['strTeam'] ?? "",
        'team_id_api' => $team['idTeam'] ?? "",
        'stadium'     => $team['strStadium'] ?? "",
        'league'      => $team['strLeague'] ?? "",
    ];
}


$request = [
    'type'  => "APIteams",
    'teams' => $allTeams
];

$response = $client->send_request($request);

echo "Sent all team data in one request." . PHP_EOL;
print_r($response);

echo $argv[0] . " END" . PHP_EOL;
?>

