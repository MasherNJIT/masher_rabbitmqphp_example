<?php

function refreshSession($sessionId) {
    $newExpiry = date('Y-m-d H:i:s', time() + 3600); // Extend for another hour

    $pdo = new PDO("mysql:host=localhost;dbname=mydatabase", "username", "password");
    $stmt = $pdo->prepare("UPDATE sessions SET expires_at = ? WHERE session_id = ?");
    $stmt->execute([$newExpiry, $sessionId]);

    setcookie("session_id", $sessionId, time() + 3600, "/", "", false, true); // Update cookie
}

?>
