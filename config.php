<?php
session_start();

// Default Admin Credentials (Change Later)
define("ADMIN_USERNAME", "admin");
define("ADMIN_PASSWORD", "admin");

// Check if Admin is Logged In
function isAdminLoggedIn() {
    return isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true;
}
?>