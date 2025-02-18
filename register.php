<!DOCTYPE html>
<link rel="stylesheet" href="main.css"; ?>
<h1>Register</h1>
<form action="registration.php" method="POST">
    <div> 
        <label for="email">Email</label>
        <input type="email" name="email" required />
    </div>
    <div>
        <label for="f_name">First Name</label>
        <input type="text" id="f_name" name="f_name" required />
    </div>
    <div>
        <label for="l_name">Last Name</label>
        <input type="text" id="l_name" name="l_name" required />
    </div>
    <div>
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required />
    </div>
    <div>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />
    </div>
    <input type="submit" value="register"/>
</form>
</html>