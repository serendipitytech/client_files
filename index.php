<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Menu</h2>
        <a href="index.php">Home</a>
        <a href="admin_login.php">Admin Login</a>
        <a href="login.php">User Login</a>
        <?php if (isset($_SESSION["admin_logged_in"])): ?>
            <a href="admin.php">Admin Panel</a>
            <a href="logout.php" style="background-color: red;">Logout</a>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Welcome to the File Management System</h1>
        <p>Please select an option from the menu.</p>
    </div>
</body>
</html>