<?php
class SessionManager {
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function isAdminLoggedIn() {
        return isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true;
    }

    public static function isClientLoggedIn() {
        return isset($_SESSION["client_folder"]);
    }

    public static function logoutUser() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(); // Only start if needed
        }
        
        $_SESSION = []; // Clear all session variables
        session_unset(); // Unset session variables
        session_destroy(); // Destroy the session
        setcookie(session_name(), '', time() - 42000, '/'); // Clear session cookie

        // Redirect to index after logout
        header("Location: ../index.php");
        exit();
    }
}

// Ensure the session starts once at the top of the file
SessionManager::startSession();
?>