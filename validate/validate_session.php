<?php

function validateSession() {
    if (!isset($_COOKIE['session_id'])) {
        return false; // No session
    }

    $sessionId = $_COOKIE['session_id'];

    $pdo = new PDO("mysql:host=localhost;dbname=mydatabase", "username", "password");
    $stmt = $pdo->prepare("SELECT * FROM sessions WHERE session_id = ? AND expires_at > NOW()");
    $stmt->execute([$sessionId]);
    $session = $stmt->fetch();

    if ($session) {
        return $session;
    } else {
        return false; // Session expired or invalid
    }
}
?>
