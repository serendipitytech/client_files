<?php
session_start();
if (!isset($_SESSION['client_folder'])) {
    die("Unauthorized access.");
}

$folder = "./files/" . $_SESSION['client_folder'];
$files = array_diff(scandir($folder), array('..', '.'));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Files</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Client Menu</h2>
        <a href="dashboard.php">Files</a>
        <a href="logout.php" style="background-color: red;">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Available Files</h1>

        <table>
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Size</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($file); ?></td>
                        <td><?php echo round(filesize("$folder/$file") / 1024, 2) . " KB"; ?></td>
                        <td><a href="<?php echo "$folder/$file"; ?>" download class="text-blue-500">Download</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>