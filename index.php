<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/models/SessionManager.php";

// Check if user is logged in
$isAdmin = SessionManager::isAdminLoggedIn();
$isClient = isset($_SESSION['client_folder']);

// Determine the page to load
$page = $_GET['page'] ?? 'auth';
$pageTitle = "Welcome";

// Routing based on user role and requested page
if ($isAdmin && ($page === 'auth' || $page === 'client_dashboard')) {
    header("Location: index.php?page=admin_dashboard");
    exit();
} elseif ($isClient && ($page === 'auth' || $page === 'admin_dashboard')) {
    header("Location: index.php?page=client_dashboard");
    exit();
}

include "views/header.php"; // Load global header

// Load the appropriate view
switch ($page) {
    case 'admin_dashboard':
        if (!$isAdmin) {
            header("Location: index.php?page=auth");
            exit();
        }
        include "controllers/AdminController.php";
        break;

    case 'client_dashboard':
        if (!$isClient) {
            header("Location: index.php?page=auth");
            exit();
        }
        include "controllers/ClientController.php";
        break;

    default:
        include "controllers/AuthController.php"; // Default to login page
        break;
}

include __DIR__ . "/views/footer.php";
?>