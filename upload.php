<?php
session_start();
if (!isset($_SESSION['client_folder'])) {
    die("Unauthorized access.");
}

$folder = "./files/" . $_SESSION['client_folder'];
if (!is_dir($folder)) mkdir($folder, 0755, true);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $filePath = $folder . "/" . basename($_FILES["file"]["name"]);
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $filePath)) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "File upload failed.";
    }
}
?>