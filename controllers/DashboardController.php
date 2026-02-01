<?php
require_once "../models/FileModel.php";
require_once "../models/SessionManager.php";
require_once "../config.php";
require_once "../error_handler.php";

// Session is started by SessionManager
$clientFolder = $_SESSION['client_folder'] ?? null;
$fileModel = new FileModel($clientFolder);
$isAdmin = SessionManager::isAdminLoggedIn();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    header('Content-Type: application/json');

    if (isset($_FILES["file"])) {
        echo json_encode($fileModel->uploadFile($_FILES["file"], $_POST['notes'] ?? ''));
        exit();
    } elseif (isset($_POST["delete_file"])) {
        echo json_encode($fileModel->deleteFile($_POST["delete_file"]));
        exit();
    } elseif (isset($_POST["update_status"])) {
        // Both admin and client can update status
        $filename = $_POST["filename"] ?? '';
        $status = $_POST["update_status"] ?? '';
        $updatedBy = $isAdmin ? 'admin' : 'client';
        echo json_encode($fileModel->updateFileStatus($filename, $status, $updatedBy));
        exit();
    } elseif (isset($_POST["add_comment"])) {
        // Add comment (both admin and client)
        $filename = $_POST["filename"] ?? '';
        $message = $_POST["add_comment"] ?? '';
        $authorType = $isAdmin ? 'admin' : 'client';
        $authorName = $isAdmin ? 'Staff' : 'Client';
        echo json_encode($fileModel->addComment($filename, $authorType, $authorName, $message));
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Get comments for a specific file
    if (isset($_GET["get_comments"])) {
        header('Content-Type: application/json');
        $filename = $_GET["get_comments"];
        echo json_encode([
            'success' => true,
            'comments' => $fileModel->getFileComments($filename)
        ]);
        exit();
    }
}

// Retrieve files and notes for display
$files = $fileModel->getFiles();
$notes = $fileModel->getNotes();
$clientInfo = $fileModel->getClientInfo();
$fileStatuses = $fileModel->getFileStatuses();
$fileComments = $fileModel->getAllComments();

// Load the view
include "../views/dashboardView.php";
?>