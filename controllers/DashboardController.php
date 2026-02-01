<?php
require_once "../models/FileModel.php";
require_once "../config.php";
require_once "../error_handler.php";

// Ensure session is active
session_start();
$clientFolder = $_SESSION['client_folder'] ?? null;
$fileModel = new FileModel($clientFolder);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_FILES["file"])) {
        echo json_encode($fileModel->uploadFile($_FILES["file"], $_POST['notes'] ?? ''));
        exit();
    } elseif (isset($_POST["delete_file"])) {
        echo json_encode($fileModel->deleteFile($_POST["delete_file"]));
        exit();
    }
}

// Retrieve files and notes for display
$files = $fileModel->getFiles();
$notes = $fileModel->getNotes();

// Load the view
include "../views/dashboardView.php";
?>