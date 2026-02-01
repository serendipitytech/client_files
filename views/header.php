<?php
$pageTitle = $pageTitle ?? "Welcome";
$isAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];
$isClient = isset($_SESSION['client_folder']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="menu">
        <h2>Navigation</h2>
        
        <?php if (!$isAdmin && !$isClient): ?>
            <!-- Show Login Buttons on Index Page -->
            <a href="index.php?page=auth&type=client" class="btn">Client Login</a>
            <a href="index.php?page=auth&type=admin" class="btn">Admin Login</a>
        <?php elseif ($isClient): ?>
            <!-- Client Sidebar -->
            <a href="index.php?page=client_dashboard" class="btn">Add Files</a>
            <a href="controllers/AuthController.php?action=logout" class="btn logout-btn">Logout</a>
        <?php elseif ($isAdmin): ?>
            <!-- Admin Sidebar -->
            <button onclick="openClientModal()" class="btn">Create Client</button>
            <a href="controllers/AuthController.php?action=logout" class="btn logout-btn">Logout</a>
        <?php endif; ?>
    </div>
</div>

<div class="main-content">