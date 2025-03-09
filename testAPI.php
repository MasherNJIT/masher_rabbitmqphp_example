<?php


$apiKey = "485649";  // Replace with your API key

// Fetch data from TheSportsDB API
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "https://www.thesportsdb.com/api/v2/json/livescore/Soccer");
curl_setopt($curl, CURLOPT_HTTPGET, 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-API-KEY: ' . $apiKey));

$result = curl_exec($curl);
curl_close($curl);

// Decode JSON response
$json = json_decode($result, true);

// Send to RabbitMQ
$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$request = array();
$request['type'] = "LiveScores";
$request['data'] = $json;  // Send the full API response
$response = $client->send_request($request);

echo "Sent live scores to RabbitMQ:\n";
print_r($response);
?>

