<?php
// Start session (if needed for redirections)
session_start();

// Redirect logged-in users to the appropriate dashboard
if (isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"]) {
    header("Location: adminDashboard.php");
    exit();
} elseif (isset($_SESSION["client_folder"])) {
    header("Location: clientDashboard.php");
    exit();
}

// Load the index view
include "../views/indexView.php";
?>