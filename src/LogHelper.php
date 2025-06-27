<?php
namespace App;

class LogHelper {
    public static function log($db, $action, $details) {
        $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'guest';
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $stmt = $db->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('issss', $user_id, $username, $action, $details, $ip);
        $stmt->execute();
        $stmt->close();
    }
} 