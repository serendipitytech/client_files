<?php
require "config.php";

$type = isset($_GET['type']) ? $_GET['type'] : 'admin'; // Default to admin login
$error = "";

// Read clients from JSON file
$clientsFile = "clients.json";
$clients = file_exists($clientsFile) ? json_decode(file_get_contents($clientsFile), true) : [];

// Process login request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['admin_login'])) {
        // Admin Login
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

        if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
            session_regenerate_id(true); // Regenerate session ID to prevent session fixation
            $_SESSION["admin_logged_in"] = true;
            header("Location: admin.php");
            exit();
        } else {
            $error = "Invalid admin credentials!";
        }
    } elseif (isset($_POST['client_login'])) {
        // Client Login
        $selectedClient = filter_input(INPUT_POST, 'client', FILTER_SANITIZE_SPECIAL_CHARS);
        $eventCode = filter_input(INPUT_POST, 'event_code', FILTER_SANITIZE_SPECIAL_CHARS);

        foreach ($clients as $client) {
            if ($client['folder_name'] === $selectedClient && $client['event_code'] === $eventCode) {
                session_regenerate_id(true); // Regenerate session ID to prevent session fixation
                $_SESSION['client_folder'] = $client['folder_name'];
                header("Location: dashboard.php");
                exit();
            }
        }
        $error = "Invalid event code!";
    }
}
?>