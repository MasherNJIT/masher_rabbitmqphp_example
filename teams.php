#!/usr/bin/php
<?php
$apiUrl = "https://www.thesportsdb.com/api/v1/json/3/search_all_teams.php?l=English%20Premier%20League";

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$results = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
curl_close($ch);

if ($httpCode !== 200 || !$results) {
    die("Error: Failed to fetch API data. HTTP Code: $httpCode");
}

$arrayCode = json_decode($results, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("JSON Decode Error: " . json_last_error_msg());
}

$filteredTeams = [];
$index = 1;

if (!empty($arrayCode['teams'])) {
    foreach ($arrayCode['teams'] as $teams) {
        if (isset($teams['idTeam'])) {
            $filteredTeams[$index] = [
                'teamName' => $teams['strTeam'] ?? "",
                'team_id_api' => (int) $teams['idTeam'], 
                'stadium' => $teams['strStadium'] ?? "",
                'league' => $teams['strLeague'] ?? ""
            ];
            $index++;
        }
    }
} else {
    die("Error: No teams found in the API response.");
}

echo "<pre>";
print_r($filteredTeams);
echo "</pre>";
echo $httpCode;
?>
