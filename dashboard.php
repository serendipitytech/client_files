<?php
require "config.php";
require "error_handler.php"; // Include the error handler

// Handle logout request
if (isset($_GET['action']) && $_GET['action'] === "logout") {
    logoutUser();
}

// Check if the user is an admin accessing a client
if (isset($_GET['admin_access']) && $_GET['admin_access'] === "true" && isAdminLoggedIn()) {
    $clientFolder = $_GET['client'] ?? null;
    if (!$clientFolder) {
        die("Invalid client access.");
    }
    $_SESSION['client_folder'] = $clientFolder; // Grant access as the client
} elseif (!isClientLoggedIn()) {
    header("Location: auth.php?type=client");
    exit();
}

$clientFolder = $_SESSION['client_folder'];
$folderPath = "./files/$clientFolder";
$notesFilePath = "$folderPath/notes.json";
$files = is_dir($folderPath) ? array_diff(scandir($folderPath), ['.', '..']) : [];

// Load existing notes
$notes = [];
if (file_exists($notesFilePath)) {
    $notes = json_decode(file_get_contents($notesFilePath), true);
}

$errorMessages = [];

// Function to format file sizes
function human_filesize($bytes, $decimals = 2) {
    $size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $size[$factor];
}

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["file"])) {
    error_log("File upload initiated."); // Debugging log
    $targetDir = $folderPath . "/";
    $targetFile = $targetDir . basename($_FILES["file"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $notesText = $_POST['notes'] ?? '';

    // Check if file already exists
    if (file_exists($targetFile) && !isset($_POST['replace'])) {
        echo json_encode(['success' => false, 'messages' => ["Sorry, file already exists."]]);
        exit();
    }

    // Check file size
    if ($_FILES["file"]["size"] > 500000000) { // 500MB limit
        $errorMessages[] = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowedTypes = ["jpg", "png", "jpeg", "gif", "pdf", "mp4", "avi", "mov"];
    if (!in_array($fileType, $allowedTypes)) {
        $errorMessages[] = "Sorry, only JPG, JPEG, PNG, GIF, PDF, MP4, AVI, & MOV files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $errorMessages[] = "Sorry, your file was not uploaded.";
    } else {
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true); // Create the directory if it doesn't exist
        }
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
            // Save notes to the JSON file
            $notes[basename($_FILES["file"]["name"])] = $notesText;
            file_put_contents($notesFilePath, json_encode($notes, JSON_PRETTY_PRINT));
            $successMessage = "The file ". htmlspecialchars(basename($_FILES["file"]["name"])). " has been uploaded.";
            error_log("File uploaded successfully: " . $targetFile); // Debugging log
            echo json_encode(['success' => true, 'message' => $successMessage, 'file' => basename($_FILES["file"]["name"]), 'size' => human_filesize($_FILES["file"]["size"]), 'notes' => $notesText]);
            exit();
        } else {
            $errorMessages[] = "Sorry, there was an error uploading your file.";
            error_log("Error uploading file: " . $_FILES["file"]["error"]); // Debugging log
        }
    }

    if (!empty($errorMessages)) {
        error_log("File upload errors: " . implode(", ", $errorMessages)); // Debugging log
        echo json_encode(['success' => false, 'messages' => $errorMessages]);
        exit();
    }
}

// Handle file deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_file"])) {
    $fileToDelete = $folderPath . "/" . basename($_POST["delete_file"]);
    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
        unset($notes[basename($fileToDelete)]);
        file_put_contents($notesFilePath, json_encode($notes, JSON_PRETTY_PRINT));
        echo json_encode(['success' => true, 'message' => "File deleted successfully."]);
    } else {
        echo json_encode(['success' => false, 'message' => "File not found."]);
    }
    exit();
}
?>

<script>
function closeUploadModal() { 
    document.getElementById("uploadModal").style.display = "none"; 
    resetUploadForm();
}
</script>
