<?php
require_once __DIR__ . "/../models/SessionManager.php";
require_once __DIR__ . "/../models/AdminModel.php";

// Restrict access to admins only
if (!SessionManager::isAdminLoggedIn()) {
    header("Location: ../index.php?page=auth&type=admin");
    exit();
}

$adminModel = new AdminModel();
$clients = $adminModel->getClients();

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['client_name'])) {
        $adminModel->saveClient($_POST['client_name'], $_POST['event_name'], $_POST['event_date'], $_POST['folder_name'] ?? null);
    } elseif (isset($_POST['delete_client'])) {
        if ($adminModel->deleteClient($_POST['folder_name'])) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete client."]);
        }
        exit();
    }

    header("Location: ../index.php?page=admin_dashboard");
    exit();
}

// Load the admin dashboard view
include __DIR__ . "/../views/adminDashboardView.php";