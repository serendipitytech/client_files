<?php
session_start();

$clientsFile = "clients.json";
$clients = file_exists($clientsFile) ? json_decode(file_get_contents($clientsFile), true) : [];

$error = ""; // Default empty error message

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedClient = $_POST['client'];
    $eventCode = $_POST['event_code'];

    foreach ($clients as $client) {
        if ($client['folder_name'] === $selectedClient && $client['event_code'] === $eventCode) {
            $_SESSION['client_folder'] = $client['folder_name'];
            header("Location: dashboard.php");
            exit();
        }
    }

    $error = "Invalid event code! Please try again.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Navigation</h2>
        <a href="index.php">Home</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Client Login</h1>
        
        <form action="" method="POST">
            <label>Select Client:</label>
            <select name="client" required>
                <option value="">-- Select Client --</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?php echo $client['folder_name']; ?>"><?php echo htmlspecialchars($client['client_name']); ?></option>
                <?php endforeach; ?>
            </select>

            <label>Enter Event Code:</label>
            <input type="text" name="event_code" placeholder="Enter Event Code" required>

            <button type="submit">Login</button>

            <?php if (!empty($error)): ?>
                <p class="text-red-500"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
    </div>

</body>
</html>