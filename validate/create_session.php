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
?>
