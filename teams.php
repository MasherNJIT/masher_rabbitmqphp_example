#!/usr/bin/php
<?php
// Define API endpoint with the correct format
$apiKey = "485649";
$teamName = "Manchester City"; // Properly formatted team name
$apiUrl = "https://www.thesportsdb.com/api/v1/json/3/search_all_teams.php?l=English%20Premier%20League";

// Get the results from the API
$results = file_get_contents($apiUrl);

// Decode the JSON response
$arrayCode = json_decode($results, true);


// Array to hold filtered players' information
$filteredTeams = [];

$index = 1;

if (!empty($arrayCode['teams'])) {
	foreach ($arrayCode['teams'] as $teams) {
		// Ensure 'strPosition' exists and filter out unwanted teams and the manager
		if (isset($teams['idTeam'])) {
			$filteredTeams[$index] = [
				'teamName' => $teams['strTeam'] ?? "",
				'team_id_api' => $teams['idTeam'] ?? "",
				'stadium' => $teams['strStadium'] ?? "",
				'league' => $teams['strLeague'] ?? ""
			];
			$index++;
		}
	}
}

// Output formatted array structure
echo "<pre>";
print_r($filteredTeams);
echo "</pre>";
?>

