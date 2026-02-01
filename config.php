<?php
// Only start a session if one isn’t already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Default Admin Credentials (Change Later)
define("ADMIN_USERNAME", "admin");
define("ADMIN_PASSWORD_HASH", '$2y$10$ThM6Ulgn1BXpaNikK/pXV.iKsut5IluINM.wUiYQQXaoyO0Af8kAK'); // Hashed version of "admin"

// Path to clients.json
define("CLIENTS_FILE", "../clients.json");
?>