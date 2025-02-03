<?php
session_start();

// Default Admin Credentials (Change Later)
define("ADMIN_USERNAME", "admin");
define("ADMIN_PASSWORD_HASH", '$2y$10$ThM6Ulgn1BXpaNikK/pXV.iKsut5IluINM.wUiYQQXaoyO0Af8kAK'); // Hashed version of "admin"

// Check if Admin is Logged In
function isAdminLoggedIn() {
    return isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true;
}

// Check if Client is Logged In
function isClientLoggedIn() {
    return isset($_SESSION["client_folder"]);
}

// Logout function (Replaces logout.php)
function logoutUser() {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>