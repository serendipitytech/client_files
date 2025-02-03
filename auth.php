<?php
require "config.php";
require "error_handler.php"; // Include the error handler

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
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $type === 'client' ? 'Client Login' : 'Admin Login'; ?></title>
    <link rel="stylesheet" type="text/css" href="styles.css"> <!-- Ensure your CSS file is linked here -->
</head>
<body>
    <div class="sidebar">
        <div class="menu">
            <h2>Menu</h2>
            <a href="auth.php?type=admin" class="btn">Admin Login</a>
            <a href="auth.php?type=client" class="btn">Client Login</a>
        </div>
    </div>
    <div class="main-content">
        <h2><?php echo $type === 'client' ? 'Client Login' : 'Admin Login'; ?></h2>
        <div class="form-container">
            <form method="post" action="auth.php?type=<?php echo $type; ?>">
                <?php if ($type === 'client'): ?>
                    <label for="client">Select Client:</label>
                    <select name="client" id="client">
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo htmlspecialchars($client['folder_name']); ?>">
                                <?php echo htmlspecialchars($client['client_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <br>
                    <label for="event_code">Event Code:</label>
                    <input type="text" name="event_code" id="event_code" required>
                    <br>
                    <input type="submit" name="client_login" value="Login">
                <?php else: ?>
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" required>
                    <br>
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                    <br>
                    <input type="submit" name="admin_login" value="Login">
                <?php endif; ?>
            </form>
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>