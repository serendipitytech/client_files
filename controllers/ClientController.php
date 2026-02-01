<?php
require_once __DIR__ . "/../models/SessionManager.php";

// If no client folder is set, redirect to login
if (!isset($_SESSION['client_folder'])) {
    header("Location: index.php?page=auth&type=client");
    exit();
}

// Get the client folder - use __DIR__ for reliable path resolution
$clientFolder = $_SESSION['client_folder'];
$folderPath = __DIR__ . "/../files/$clientFolder";
$notesFilePath = "$folderPath/notes.json";

// Retrieve files
$files = is_dir($folderPath) ? array_diff(scandir($folderPath), ['.', '..']) : [];

// Retrieve notes
$notes = [];
if (file_exists($notesFilePath)) {
    $notes = json_decode(file_get_contents($notesFilePath), true);
}

// Load the client dashboard view
include __DIR__ . "/../views/dashboardView.php";
?>