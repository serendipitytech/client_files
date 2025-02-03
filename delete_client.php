<?php
require "config.php";
require "error_handler.php"; // Include the error handler

if (!isAdminLoggedIn()) {
    header("Location: auth.php?type=admin");
    exit();
}

$folderName = $_GET['folder'] ?? '';

if ($folderName) {
    $clientsFile = "clients.json";
    $clients = file_exists($clientsFile) ? json_decode(file_get_contents($clientsFile), true) : [];

    // Remove the client with the specified folder name
    $clients = array_filter($clients, function ($client) use ($folderName) {
        return $client['folder_name'] !== $folderName;
    });

    file_put_contents($clientsFile, json_encode($clients, JSON_PRETTY_PRINT));
}

header("Location: admin.php");
exit();
?>