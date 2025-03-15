<?php
include('partials/nav.php');
session_start();

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
<h1>Home</h1>
</div>

<div>
<h2>My Leagues</h2>
</div>
