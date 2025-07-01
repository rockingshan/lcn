<?php
namespace App;

class LogHelper {
    public static function log($db, $action, $details) {
        $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'guest';
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        
        // Try without id field first (if it's auto-increment)
        try {
            $stmt = $db->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param('issss', $user_id, $username, $action, $details, $ip);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            // If that fails, try with id field as NULL
            $stmt = $db->prepare("INSERT INTO activity_log (id, user_id, username, action, details, ip_address, created_at) VALUES (NULL, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param('issss', $user_id, $username, $action, $details, $ip);
            $stmt->execute();
            $stmt->close();
        }
    }
} 