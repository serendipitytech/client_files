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
    if ($_FILES["file"]["size"] > 500000000) { // 50MB limit
        $errorMessages[] = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowedTypes = ["jpg", "png", "jpeg", "gif", "pdf", "mp4", "avi", "mov", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "txt"];
    if (!in_array($fileType, $allowedTypes)) {
        $errorMessages[] = "Sorry,file type not allowed. Contact event coordinator for help.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $errorMessages[] = "Sorry, your file was not uploaded.";
    // If everything is ok, try to upload file
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Files</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="menu">
        <h2>Client Menu</h2>
        <button onclick="openUploadModal()" class="btn">Upload Files</button>
        <form action="dashboard.php" method="get" style="width: 100%;">
            <input type="hidden" name="action" value="logout">
            <button type="submit" class="btn logout-btn" style="width: 100%;">Logout</button>
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
                    <td><?php echo human_filesize(filesize("$folderPath/$file")); ?></td>
                    <td><?php echo htmlspecialchars($notes[$file] ?? ''); ?></td>
                    <td><a href="<?php echo "$folderPath/$file"; ?>" download class="text-blue-500">Download</a></td>
                    <td>
                        <i class="fa-solid fa-eye preview-icon" onclick="openPreview('<?php echo htmlspecialchars($file); ?>')"></i>
                        <i class="fa-solid fa-trash icon-delete-btn" onclick="deleteFile('<?php echo htmlspecialchars($file); ?>')"></i>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
    </table>
</div>

<!-- Modal for Uploading Files -->
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
        <div id="progressContainer" style="display: none;">
            <progress id="uploadProgress" value="0" max="100" style="width: 100%;"></progress>
        </div>
        <div id="uploadOptions" style="display: none;">
            <button onclick="resetUploadForm()" class="btn">Upload File</button>
            <button onclick="closeUploadModal()" class="btn">Close</button>
        </div>
        <div id="replaceOptions" style="display: none;">
            <button onclick="replaceFile()" class="btn">Replace File</button>
            <button onclick="resetUploadForm()" class="btn">Cancel</button>
        </div>
    </div>
</div>

<script>
    function openPreview(fileName) {
    let fileExtension = fileName.split('.').pop().toLowerCase();
    let previewContainer = document.getElementById("previewContainer");
    let downloadLink = document.getElementById("downloadLink");

    let filePath = "./files/<?php echo $clientFolder; ?>/" + encodeURIComponent(fileName);

    previewContainer.innerHTML = ""; // Clear previous content
    downloadLink.href = filePath; // Set download link

    // Supported File Previews
    let imageTypes = ["jpg", "jpeg", "png", "gif", "webp"];
    let videoTypes = ["mp4", "webm", "ogg", "mov", "avi"];
    let audioTypes = ["mp3", "wav", "ogg"];
    let documentTypes = ["pdf"];

    if (imageTypes.includes(fileExtension)) {
        previewContainer.innerHTML = `<img src="${filePath}" alt="Preview" class="preview-media">`;
    } else if (videoTypes.includes(fileExtension)) {
        previewContainer.innerHTML = `<video controls class="preview-media"><source src="${filePath}" type="video/${fileExtension}">Your browser does not support the video tag.</video>`;
    } else if (audioTypes.includes(fileExtension)) {
        previewContainer.innerHTML = `<audio controls class="preview-media"><source src="${filePath}" type="audio/${fileExtension}">Your browser does not support the audio tag.</audio>`;
    } else if (documentTypes.includes(fileExtension)) {
        previewContainer.innerHTML = `<iframe src="${filePath}" class="preview-media"></iframe>`;
    } else {
        previewContainer.innerHTML = `<p>This file type cannot be previewed.</p>`;
    }

    document.getElementById("previewOverlay").style.display = "flex";
}

function closePreview() {
    document.getElementById("previewOverlay").style.display = "none";
}
// Open/Close Upload Modal
function openUploadModal() { 
    document.getElementById("uploadModal").style.display = "flex"; 
    document.getElementById("progressContainer").style.display = "none"; // Hide progress bar when modal opens
    document.getElementById("uploadProgress").value = 0;
}
function closeUploadModal() { 
    document.getElementById("uploadModal").style.display = "none"; 
}
function resetUploadForm() {
        let formUploadButton = document.querySelector("#uploadForm button[type='submit']");
        if (formUploadButton) formUploadButton.style.display = "block";
    document.getElementById("uploadForm").reset();
    document.getElementById("uploadSuccessMessage").innerHTML = "";
    document.getElementById("uploadErrorMessages").innerHTML = "";
    document.getElementById("progressContainer").style.display = "none";
    document.getElementById("uploadOptions").style.display = "none";
    document.getElementById("replaceOptions").style.display = "none";
}

// Handle form submission via AJAX with progress
document.getElementById("uploadForm").addEventListener("submit", function(event) {
    event.preventDefault();
    var formData = new FormData(this);
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "dashboard.php", true);

    // Update progress bar
    xhr.upload.addEventListener("progress", function(event) {
        if (event.lengthComputable) {
            var percentComplete = (event.loaded / event.total) * 100;
            document.getElementById("progressContainer").style.display = "block";
            document.getElementById("uploadProgress").value = percentComplete;
            console.log("Upload progress: " + percentComplete + "%"); // Debugging log
        }
    });

    xhr.onload = function () {
    console.log("Upload complete. Status: " + xhr.status); // Debugging log
    if (xhr.status === 200) {
        var response = JSON.parse(xhr.responseText);
        if (response.success) {
            document.getElementById("uploadSuccessMessage").innerHTML = response.message;
            document.getElementById("uploadErrorMessages").innerHTML = "";
            document.getElementById("progressContainer").style.display = "none";
            document.getElementById("uploadOptions").style.display = "block";
                let formUploadButton = document.querySelector("#uploadForm button[type='submit']");
                if (formUploadButton) formUploadButton.style.display = "block";
            document.getElementById("replaceOptions").style.display = "none";

            // Update the table with the new file
            var newRow = document.createElement("tr");
            newRow.innerHTML = "<td>" + response.file + "</td><td>" + response.size + "</td><td>" + response.notes + "</td><td><a href='./files/" + "<?php echo $clientFolder; ?>" + "/" + response.file + "' download class='text-blue-500'>Download</a></td><td><button class='icon-delete-btn' onclick='deleteFile(\"" + response.file + "\")'>&#128465;</button></td>";
            document.getElementById("filesTable").getElementsByTagName("tbody")[0].appendChild(newRow);
        } else if (response.messages.includes("Sorry, file already exists.")) {
            document.getElementById("uploadErrorMessages").innerHTML = response.messages.join("<br>");
            
            // Hide all upload-related buttons
            document.getElementById("uploadOptions").style.display = "none";
            let formUploadButton = document.querySelector("#uploadForm button[type='submit']");
            if (formUploadButton) formUploadButton.style.display = "none"; // Hide the form's upload button

            // Show replace options
            document.getElementById("replaceOptions").style.display = "block";

            // Hide progress bar completely
            document.getElementById("progressContainer").style.display = "none";
            document.getElementById("uploadProgress").value = 0; // Reset progress
        } else {
            document.getElementById("uploadErrorMessages").innerHTML = response.messages.join("<br>");
            document.getElementById("uploadOptions").style.display = "block";
                let formUploadButton = document.querySelector("#uploadForm button[type='submit']");
                if (formUploadButton) formUploadButton.style.display = "block";
            document.getElementById("replaceOptions").style.display = "none";

            // Ensure progress bar is only shown during an actual upload
            document.getElementById("progressContainer").style.display = "none";
            document.getElementById("uploadProgress").value = 0;
        }
    } else {
        alert("An error occurred while uploading the file.");
        document.getElementById("progressContainer").style.display = "none";
        console.log("Upload error. Status: " + xhr.status); // Debugging log
    }
};

    xhr.onerror = function () {
        alert("An error occurred while uploading the file.");
        document.getElementById("progressContainer").style.display = "none";
        console.log("Upload error."); // Debugging log
    };

    xhr.send(formData);
});

// Handle file replacement
function replaceFile() {
    var formData = new FormData(document.getElementById("uploadForm"));
    formData.append("replace", true);
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "dashboard.php", true);

    xhr.onload = function () {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                document.getElementById("uploadSuccessMessage").innerHTML = response.message;
                document.getElementById("uploadErrorMessages").innerHTML = "";
                document.getElementById("progressContainer").style.display = "none";
                document.getElementById("uploadOptions").style.display = "block";
                let formUploadButton = document.querySelector("#uploadForm button[type='submit']");
                if (formUploadButton) formUploadButton.style.display = "block";
                document.getElementById("replaceOptions").style.display = "none";

                // Remove the old file entry from the table
                let oldFileName = formData.get("file").name; // Get the name of the replaced file
                let tableRows = document.getElementById("filesTable").getElementsByTagName("tbody")[0].getElementsByTagName("tr");
                for (let i = 0; i < tableRows.length; i++) {
                    let fileNameCell = tableRows[i].getElementsByTagName("td")[0]; 
                    if (fileNameCell && fileNameCell.innerText.trim() === oldFileName) {
                        tableRows[i].parentNode.removeChild(tableRows[i]); // Remove old file entry
                        break;
                    }
                }

                // Add the new file entry to the table
                var newRow = document.createElement("tr");
                newRow.innerHTML = "<td>" + response.file + "</td><td>" + response.size + "</td><td>" + response.notes + "</td><td><a href='./files/" + "<?php echo $clientFolder; ?>" + "/" + response.file + "' download class='text-blue-500'>Download</a></td><td><button class='delete-btn' onclick='deleteFile(\"" + response.file + "\")'>&#128465;</button></td>";
                document.getElementById("filesTable").getElementsByTagName("tbody")[0].appendChild(newRow);
            } else {
                document.getElementById("uploadErrorMessages").innerHTML = response.messages.join("<br>");
                document.getElementById("uploadOptions").style.display = "none";
                document.getElementById("replaceOptions").style.display = "none";
            }
        } else {
            alert("An error occurred while replacing the file.");
            document.getElementById("progressContainer").style.display = "none";
            console.log("Upload error. Status: " + xhr.status); // Debugging log
        }
    };

    xhr.onerror = function () {
        alert("An error occurred while replacing the file.");
        document.getElementById("progressContainer").style.display = "none";
        console.log("Upload error."); // Debugging log
    };

    xhr.send(formData);
}
// Handle file deletion
function deleteFile(fileName) {
    if (confirm("Are you sure you want to delete this file?")) {
        var formData = new FormData();
        formData.append("delete_file", fileName);
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "dashboard.php", true);

        xhr.onload = function () {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert(response.message);
                    // Remove the file row from the table
                    var rows = document.getElementById("filesTable").getElementsByTagName("tbody")[0].getElementsByTagName("tr");
                    for (var i = 0; i < rows.length; i++) {
                        if (rows[i].getElementsByTagName("td")[0].innerText === fileName) {
                            rows[i].parentNode.removeChild(rows[i]);
                            break;
                        }
                    }
                } else {
                    alert(response.message);
                }
            } else {
                alert("An error occurred while deleting the file.");
            }
        };

        xhr.onerror = function () {
            alert("An error occurred while deleting the file.");
        };

        xhr.send(formData);
    }
}
</script>
<!-- Preview Overlay -->
<div id="previewOverlay" class="preview-overlay">
    <div class="preview-content">
        <span class="close-preview" onclick="closePreview()">&times;</span>
        <div id="previewContainer"></div>
        <a id="downloadLink" href="#" download class="btn download-btn">Download</a>
    </div>
</div>
</body>
</html>