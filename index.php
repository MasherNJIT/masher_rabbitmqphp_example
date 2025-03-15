<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="main.css"; ?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EPL Fantasy Soccer</title>
    <link href="bootstrap-5.3.3-dist/css/bootstrap.css" rel="stylesheet">
  </head>

<nav>
        <ul>
        <li><a href="register.php">Register</a></li>
        </ul>
</nav>

<div>
<h1>Welcome back!</h1>
<script src="bootstrap-5.3.3-dist/js/bootstrap.js"></script>
<p>Login or <a href="register.php">Register</a></p>
</div>

<form action="login.php" method="POST">
    <div>
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required />
    </div> 
    <div> 
        <label for="password">Password</label>
        <input type="password" id="password" name="password" />
    </div>
    <input type="submit" id="login" value="login" class="button"/>
</form>
</html>