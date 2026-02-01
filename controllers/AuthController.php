<?php
require_once __DIR__ . "/../models/AuthModel.php";
require_once __DIR__ . "/../models/SessionManager.php";
require_once __DIR__ . "/../config.php";

if (!class_exists('AuthModel')) {
    die("Error: AuthModel class not found.");
}

$authModel = new AuthModel();

// Handle Admin Login
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($authModel->verifyAdmin($username, $password)) {
        header("Location: ../index.php?page=admin_dashboard");
        exit();
    } else {
        $errorMessage = "Invalid admin credentials.";
    }
}

// Handle Client Login
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['event_code'])) {
    $eventCode = $_POST['event_code'];

    if ($authModel->verifyClient($eventCode)) {
        header("Location: ../index.php?page=client_dashboard");
        exit();
    } else {
        $errorMessage = "Invalid event code.";
    }
}

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === "logout") {
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); // ✅ Only start a session if one isn't already active
    }
    
    session_destroy();
    header("Location: ../index.php?page=auth");
    exit();
}
if (isset($_GET['auto_login']) && $_GET['auto_login'] === "true" && isset($_GET['client'])) {
    if (SessionManager::isAdminLoggedIn()) {
        $_SESSION['client_folder'] = $_GET['client'];
        header("Location: ../index.php?page=client_dashboard");
        exit();
    } else {
        header("Location: ../index.php?page=auth&type=admin");
        exit();
    }
}



// Load login view
include __DIR__ . "/../views/authView.php";