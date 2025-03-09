#!/usr/bin/php

<?php
// Define API endpoint with the correct format
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
?>

