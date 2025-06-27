<?php

namespace App\Controllers;

use App\LogHelper;

class AuthController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function showLoginForm($error = null)
    {
        // This allows us to pass an error message to the view
        require_once __DIR__ . '/../../views/login.php';
    }

    public function login()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            return $this->showLoginForm('Username and password are required.');
        }

        // Fetch user from the database
        $stmt = $this->db->prepare("SELECT user_id, user, pass, is_admin, is_active FROM auth_tb WHERE user = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        $stmt->close();

        if ($user) {
            // User found, now verify password
            if (password_verify($password, $user['pass'])) {
                // Modern hash verification successful
                LogHelper::log($this->db, 'Login', "User $username logged in successfully.");
                $this->establishSession($user);
            } elseif (md5($password) === $user['pass']) {
                // Legacy MD5 verification successful. Upgrade the hash now.
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $this->db->prepare("UPDATE auth_tb SET pass = ? WHERE user_id = ?");
                $updateStmt->bind_param("si", $newHash, $user['user_id']);
                $updateStmt->execute();
                $updateStmt->close();
                LogHelper::log($this->db, 'Login', "User $username logged in (MD5 upgraded).");
                $this->establishSession($user);
            } else {
                // Both checks failed
                return $this->showLoginForm('Invalid username or password.');
            }
        } else {
            // User not found
            return $this->showLoginForm('Invalid username or password.');
        }
    }
    
    private function establishSession($user)
    {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['user'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        header('Location: ' . BASE_PATH . '/');
        exit();
    }

    public function logout()
    {
        LogHelper::log($this->db, 'Logout', 'User logged out.');
        session_unset();
        session_destroy();
        header('Location: ' . BASE_PATH . '/login');
        exit();
    }
} 