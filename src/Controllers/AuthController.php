<?php
/**
 * AuthController.php
 *
 * Handles authentication: login, logout, and session management for the LCN Management System.
 * - Uses bcrypt for password hashing (upgrades legacy MD5 on login)
 * - Logs all login/logout actions
 * - Establishes user session
 */

namespace App\Controllers;

use App\LogHelper;

class AuthController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * showLoginForm($error = null)
     * Shows the login form, optionally with an error message.
     */
    public function showLoginForm($error = null)
    {
        // This allows us to pass an error message to the view
        require_once __DIR__ . '/../../views/login.php';
    }

    /**
     * login()
     * Handles login form submission. Verifies password, upgrades MD5 hashes, logs in user.
     */
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

        if ($user && (int)$user['is_active'] === 1) {
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
    
    /**
     * establishSession($user)
     * Sets session variables for the logged-in user and redirects to dashboard.
     */
    private function establishSession($user)
    {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['user'];
        $_SESSION['is_admin'] = $user['is_admin'];
        $_SESSION['changed_frequencies'] = [];
        $_SESSION['permissions'] = [];
        if (!(int)$user['is_admin']) {
            $stmt = $this->db->prepare('SELECT permission_key FROM user_permission_tb WHERE user_id = ?');
            $stmt->bind_param('i', $user['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $_SESSION['permissions'][] = $row['permission_key'];
            }
            $stmt->close();
        }
        
        header('Location: ' . BASE_PATH . '/');
        exit();
    }

    /**
     * logout()
     * Logs out the user, destroys session, and logs the action.
     */
    public function logout()
    {
        LogHelper::log($this->db, 'Logout', 'User logged out.');
        session_unset();
        session_destroy();
        header('Location: ' . BASE_PATH . '/login');
        exit();
    }
}
