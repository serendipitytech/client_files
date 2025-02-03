<?php
require "config.php";
require "error_handler.php"; // Include the error handler

// Define the getClientFileInfo function
function getClientFileInfo($folderName) {
    $folderPath = "./files/$folderName";
    $fileInfo = [
        'count' => 0,
        'last_edit' => 'N/A'
    ];

    if (is_dir($folderPath)) {
        $files = array_diff(scandir($folderPath), ['.', '..']);
        
        $lastEditTime = 0;
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'json') {
                $fileInfo['count']++;
                $filePath = "$folderPath/$file";
                $fileEditTime = filemtime($filePath);
                if ($fileEditTime > $lastEditTime) {
                    $lastEditTime = $fileEditTime;
                }
            }
        }

        if ($lastEditTime > 0) {
            $fileInfo['last_edit'] = date("Y-m-d H:i:s", $lastEditTime);
        }
    }

    return $fileInfo;
}

// Handle logout request
if (isset($_GET['action']) && $_GET['action'] === "logout") {
    logoutUser();
}

// Redirect to login if the admin isn't logged in
if (!isAdminLoggedIn()) {
    header("Location: auth.php?type=admin");
    exit();
}

$clientsFile = "clients.json";
$clients = file_exists($clientsFile) ? json_decode(file_get_contents($clientsFile), true) : [];

// Ensure clients.json is properly formatted
if (!is_array($clients)) {
    $clients = [];
}

// Sort clients by event date (earliest first)
usort($clients, function ($a, $b) {
    return strtotime($a['event_date']) - strtotime($b['event_date']);
});

// Filter clients based on upcoming or past events
$showPastEvents = isset($_GET['show_past']) && $_GET['show_past'] === 'true';
$currentDate = date("Y-m-d");
$filteredClients = array_filter($clients, function ($client) use ($showPastEvents, $currentDate) {
    return $showPastEvents ? true : strtotime($client['event_date']) >= strtotime($currentDate);
});

// Handle adding or editing a client
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['client_name'])) {
    header("Content-Type: application/json");

    $clientName = $_POST['client_name'] ?? '';
    $eventName = $_POST['event_name'] ?? '';
    $eventDate = $_POST['event_date'] ?? '';
    $folderName = $_POST['folder_name'] ?? ''; // If set, we are editing

    // Add or update client
    if ($folderName) {
        // Update existing client
        foreach ($clients as &$client) {
            if ($client['folder_name'] === $folderName) {
                $client['client_name'] = $clientName;
                $client['event_name'] = $eventName;
                $client['event_date'] = $eventDate;
                break;
            }
        }
    } else {
        // Add new client
        $folderName = strtolower(str_replace(' ', '_', $clientName));
        $clients[] = [
            'client_name' => $clientName,
            'event_name' => $eventName,
            'event_date' => $eventDate,
            'event_code' => bin2hex(random_bytes(4)),
            'folder_name' => $folderName
        ];
    }

    file_put_contents($clientsFile, json_encode($clients, JSON_PRETTY_PRINT));
    header("Location: admin.php");
    exit();
}

// Handle deleting a client
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_client'])) {
    $folderName = $_POST['folder_name'] ?? '';

    if ($folderName) {
        // Remove the client with the specified folder name
        $clients = array_filter($clients, function ($client) use ($folderName) {
            return $client['folder_name'] !== $folderName;
        });

        file_put_contents($clientsFile, json_encode($clients, JSON_PRETTY_PRINT));
    }

    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<body>
    <?php include 'admin_dashboard_content.php'; ?>

    
</body>
</html>