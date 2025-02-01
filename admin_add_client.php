<?php
require "config.php";
if (!isAdminLoggedIn()) {
    echo json_encode(["success" => false, "error" => "Unauthorized"]);
    exit();
}

$clientsFile = "clients.json";

function generateEventCode($length = 8) {
    return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clientName = $_POST['client_name'];
    $eventName = $_POST['event_name'];
    $eventDate = $_POST['event_date'];
    $eventCode = generateEventCode();
    $folderName = preg_replace("/[^a-zA-Z0-9_]/", "_", strtolower($clientName));

    $clientFolder = "./files/$folderName";
    if (!is_dir($clientFolder)) {
        mkdir($clientFolder, 0755, true);
    }

    $clients = file_exists($clientsFile) ? json_decode(file_get_contents($clientsFile), true) : [];
    if (!is_array($clients)) {
        $clients = [];
    }

    $newClient = [
        "client_name" => $clientName,
        "event_name" => $eventName,
        "event_date" => $eventDate,
        "event_code" => $eventCode,
        "folder_name" => $folderName
    ];

    $clients[] = $newClient;
    file_put_contents($clientsFile, json_encode($clients, JSON_PRETTY_PRINT));

    echo json_encode(["success" => true, "client" => $newClient]);
    exit();
}

echo json_encode(["success" => false, "error" => "Invalid request"]);