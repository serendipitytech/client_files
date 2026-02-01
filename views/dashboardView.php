<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Files</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="menu">
        <h2>Client Menu</h2>
        <button onclick="openUploadModal()" class="btn">Upload Files</button>
        <form action="../controllers/AuthController.php?action=logout" method="post">
            <button type="submit" class="btn logout-btn">Logout</button>
        </form>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <h1>Available Files</h1>
    <table id="filesTable">
        <thead>
        <tr>
            <th>File Name</th>
            <th>Size</th>
            <th>Notes</th>
            <th>Download</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($files as $file): ?>
                <?php if (pathinfo($file, PATHINFO_EXTENSION) !== 'json'): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($file); ?></td>
                        <td><?php echo human_filesize(filesize("../files/$clientFolder/$file")); ?></td>
                        <td><?php echo htmlspecialchars($notes[$file] ?? ''); ?></td>
                        <td><a href="../files/<?php echo $clientFolder . '/' . htmlspecialchars($file); ?>" download>Download</a></td>
                        <td><button onclick="deleteFile('<?php echo htmlspecialchars($file); ?>')">Delete</button></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeUploadModal()">&times;</span>
        <h2>Upload File</h2>
        <div id="uploadErrorMessages" style="color: red;"></div>
        <div id="uploadSuccessMessage" style="color: green;"></div>
        <form id="uploadForm" enctype="multipart/form-data">
            <input type="file" name="file" id="file" required>
            <textarea name="notes" id="notes" placeholder="Enter notes about the file" rows="4" style="width: 100%;"></textarea>
            <button type="submit" class="btn">Upload File</button>
        </form>
    </div>
</div>

</body>
</html>