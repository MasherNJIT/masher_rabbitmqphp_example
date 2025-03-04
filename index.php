<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="main.css"; ?>

<nav>
        <ul>
        <li><a href="register.php">Register</a></li>
        </ul>
    </nav>

<div>
<h1>Welcome back!</h1>
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