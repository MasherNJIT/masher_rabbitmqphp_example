<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="main.css">
    <title>Register</title>
</head>

<nav>
        <ul>
        <li><a href="index.php">Login</a></li>
        </ul>
</nav>

<body>
<div>
    <h1>Register</h1>
</div>
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
        <input type="submit" value="register" class="button"/>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $errors = [];
        
        // Sanitize and validate inputs
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $f_name = htmlspecialchars(trim($_POST["f_name"]));
        $l_name = htmlspecialchars(trim($_POST["l_name"]));
        $username = htmlspecialchars(trim($_POST["username"]));
        $password = trim($_POST["password"]);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        if (empty($f_name) || empty($l_name)) {
            $errors[] = "First and last name are required.";
        }
        if (empty($username)) {
            $errors[] = "Username is required.";
        }
        if (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters long.";
        }
        
        
    }
    ?>
</body>
</html>
