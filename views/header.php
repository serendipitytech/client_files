<?php
$pageTitle = $pageTitle ?? "Client Files";
$isAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];
$isClient = isset($_SESSION['client_folder']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Client Files</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="fas fa-folder-open"></i>
        </div>
        <span class="sidebar-brand-text">Client Files</span>
    </div>

    <div class="menu">
        <h2>Menu</h2>

        <?php if (!$isAdmin && !$isClient): ?>
            <!-- Show Login Buttons on Index Page -->
            <a href="index.php?page=auth&type=client" class="btn">
                <i class="fas fa-user"></i>
                <span>Client Login</span>
            </a>
            <a href="index.php?page=auth&type=admin" class="btn">
                <i class="fas fa-shield-halved"></i>
                <span>Admin Login</span>
            </a>
        <?php elseif ($isClient && !$isAdmin): ?>
            <!-- Client Sidebar -->
            <a href="index.php?page=client_dashboard" class="btn">
                <i class="fas fa-folder"></i>
                <span>My Files</span>
            </a>
            <button onclick="openUploadModal()" class="btn btn-primary">
                <i class="fas fa-cloud-arrow-up"></i>
                <span>Upload Files</span>
            </button>
            <a href="controllers/AuthController.php?action=logout" class="btn logout-btn">
                <i class="fas fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        <?php elseif ($isAdmin): ?>
            <!-- Admin Sidebar -->
            <a href="index.php?page=admin_dashboard" class="btn">
                <i class="fas fa-users"></i>
                <span>All Clients</span>
            </a>
            <button onclick="openClientModal()" class="btn btn-primary">
                <i class="fas fa-user-plus"></i>
                <span>New Client</span>
            </button>
            <?php if ($isClient): ?>
                <a href="index.php?page=admin_dashboard" class="btn">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Admin</span>
                </a>
            <?php endif; ?>
            <a href="controllers/AuthController.php?action=logout" class="btn logout-btn">
                <i class="fas fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="main-content">
