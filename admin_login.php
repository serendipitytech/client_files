<?php
require "config.php";

// If already logged in, go to admin panel
if (isAdminLoggedIn()) {
    header("Location: admin.php");
    exit();
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION["admin_logged_in"] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Menu</h2>
        <a href="index.php">Home</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Admin Login</h1>
        
        <form action="" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <?php if (isset($error)): ?><p class="text-red-500"><?php echo $error; ?></p><?php endif; ?>
        </form>
    </div>

</body>
</html>