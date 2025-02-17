<!DOCTYPE html>
<html lang="en">

<link rel="stylesheet" href="main.css"; ?>
<h1>Login</h1>
<form action="login.php" method="POST">
    <div>
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required />
    </div> 
    <div> 
        <label for="password">Password</label>
        <input type="password" id="password" name="password" />
    </div>
    <input type="submit" id="login" value="login" />
</form>
</html>