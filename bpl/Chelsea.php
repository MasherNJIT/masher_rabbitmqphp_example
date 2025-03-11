#!/usr/bin/php
<?php
$apiKey = "485649";
$teamName = "Chelsea";
$apiUrl = "https://www.thesportsdb.com/api/v1/json/{$apiKey}/searchplayers.php?t=" . urlencode($teamName);

$results = file_get_contents($apiUrl);

$arrayCode = json_decode($results, true);

if ($arrayCode === null) {
    die("Error: Unable to retrieve data. Check API key or endpoint.");
}

$filteredPlayers = [];

if (!empty($arrayCode['player'])) {
    foreach ($arrayCode['player'] as $player) {
        if (isset($player['strPosition']) &&
            strpos($player['strTeam'], "Women") === false &&
            strpos($player['strTeam'], "U21") === false &&
            strpos($player['strTeam'], "U23") === false &&
            $player['strPosition'] !== "Manager") {

            $filteredPlayers[] = [
                'name' => $player['strPlayer'] ?? "",
                'position' => $player['strPosition'] ?? "",
		'idPlayer' => $player['idPlayer'] ?? "",
		 'team_name' => $player['strTeam'] ?? ""

            ];
        }
    }
}

echo "<pre>";
print_r($filteredPlayers);
echo "</pre>";
?>

