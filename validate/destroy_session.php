<?php

function destroySession() {
    if (isset($_COOKIE['session_id'])) {
        $pdo = new PDO("mysql:host=localhost;dbname=mydatabase", "username", "password");
        $stmt = $pdo->prepare("DELETE FROM sessions WHERE session_id = ?");
        $stmt->execute([$_COOKIE['session_id']]);

        setcookie("session_id", "", time() - 3600, "/"); // Expire cookie
    }
}

?>
