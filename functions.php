<?php

function startSession($userId = null) {
    $sessionId = bin2hex(random_bytes(32));  // Generate secure session ID
    $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1-hour expiry

    $pdo = new PDO("mysql:host=localhost;dbname=mydatabase", "username", "password");
    $stmt = $pdo->prepare("INSERT INTO sessions (session_id, user_id, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$sessionId, $userId, $expiresAt]);

    setcookie("session_id", $sessionId, time() + 3600, "/", "", false, true); // Secure cookie

    return $sessionId;
}

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

function destroySession() {
    if (isset($_COOKIE['session_id'])) {
        $pdo = new PDO("mysql:host=localhost;dbname=mydatabase", "username", "password");
        $stmt = $pdo->prepare("DELETE FROM sessions WHERE session_id = ?");
        $stmt->execute([$_COOKIE['session_id']]);

        setcookie("session_id", "", time() - 3600, "/"); // Expire cookie
    }
}

?>