<?php
include('partials/nav.php');


if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    echo "Welcome to your profile, " . $_SESSION['username'];
    echo "Your user ID is: " . $_SESSION['user_id'];
} else {
    header("Location: index.php");
    exit();
}


?>

<link rel="stylesheet" href="main.css"; ?>

<div>
<h1>Rules</h1>
</div>

<div>
<h2>
How the League Will Work
</h2>
<ul>
<li>To create a league you will need to have a minimum of 4 players in the league.</li>
<li>Each player will need to create a team consisting of 15 players, a starting 11 and 4 subs.</li>
<li>You will only be able to enter a team once all positions have been filled</li>
<li>Once a match starts you will not be able to make any susbstitutions until the games are over!</li>
</ul>
</div>