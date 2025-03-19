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
<h1>EPL</h1>
</div>


<div>
<h2>Rankings</h2>
<table>
    <tr>
        <td>ranking</td>
    </tr>
    <tr>
        <td>ranking</td>
    </tr>
</table>
</div>