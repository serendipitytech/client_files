<?php
// Helper function for file sizes
if (!function_exists('human_filesize')) {
    function human_filesize($bytes, $decimals = 1) {
        $size = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f %s", $bytes / pow(1024, $factor), $size[$factor]);
    }
}

// Helper function for file icons
function getFileIcon($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $icons = [
        'pdf' => 'fa-file-pdf',
        'doc' => 'fa-file-word', 'docx' => 'fa-file-word',
        'xls' => 'fa-file-excel', 'xlsx' => 'fa-file-excel',
        'ppt' => 'fa-file-powerpoint', 'pptx' => 'fa-file-powerpoint',
        'jpg' => 'fa-file-image', 'jpeg' => 'fa-file-image', 'png' => 'fa-file-image', 'gif' => 'fa-file-image', 'webp' => 'fa-file-image',
        'mp4' => 'fa-file-video', 'mov' => 'fa-file-video', 'avi' => 'fa-file-video',
        'mp3' => 'fa-file-audio', 'wav' => 'fa-file-audio',
        'zip' => 'fa-file-zipper', 'rar' => 'fa-file-zipper', '7z' => 'fa-file-zipper',
        'txt' => 'fa-file-lines',
        'html' => 'fa-file-code', 'css' => 'fa-file-code', 'js' => 'fa-file-code',
    ];
    return $icons[$ext] ?? 'fa-file';
}
?>

<div class="page-header">
    <h1>Your Files</h1>
    <p>View and manage your uploaded files</p>
</div>

<!-- Files Display -->
<?php if (empty($files) || count(array_filter($files, fn($f) => pathinfo($f, PATHINFO_EXTENSION) !== 'json')) === 0): ?>
    <div class="table-container">
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-cloud-arrow-up"></i>
            </div>
            <h3>No files yet</h3>
            <p>Upload your first file to get started</p>
            <button onclick="openUploadModal()" class="btn" style="margin-top: var(--space-lg);">
                <i class="fas fa-plus"></i>
                Upload File
            </button>
        </div>
    </div>
<?php else: ?>
    <!-- File Grid View -->
    <div class="file-grid">
        <?php foreach ($files as $file):
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') continue;
            $filePath = "../files/$clientFolder/$file";
            $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
        ?>
            <div class="file-card">
                <div class="file-card-icon">
                    <i class="fas <?php echo getFileIcon($file); ?>"></i>
                </div>
                <div class="file-card-name"><?php echo htmlspecialchars($file); ?></div>
                <div class="file-card-meta"><?php echo human_filesize($fileSize); ?></div>
                <?php if (!empty($notes[$file])): ?>
                    <p style="font-size: 0.875rem; color: var(--color-text-secondary); margin-top: var(--space-sm);">
                        <?php echo htmlspecialchars($notes[$file]); ?>
                    </p>
                <?php endif; ?>
                <div class="file-card-actions">
                    <a href="../files/<?php echo htmlspecialchars($clientFolder . '/' . $file); ?>" download class="btn btn-secondary" style="flex: 1; padding: var(--space-sm) var(--space-md);">
                        <i class="fas fa-download"></i>
                        Download
                    </a>
                    <button onclick="confirmDeleteFile('<?php echo htmlspecialchars($file); ?>')" class="btn btn-secondary" style="padding: var(--space-sm) var(--space-md); color: var(--color-danger);">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Upload Modal -->
<div id="uploadModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeUploadModal()">&times;</span>
        <h2>Upload File</h2>

        <div id="uploadMessages"></div>

        <form id="uploadForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file">Select File</label>
                <div class="file-drop-zone" id="dropZone">
                    <input type="file" name="file" id="file" required style="display: none;">
                    <div class="drop-zone-content">
                        <i class="fas fa-cloud-arrow-up" style="font-size: 2rem; color: var(--color-accent); margin-bottom: var(--space-md);"></i>
                        <p style="margin-bottom: var(--space-xs);">Drag and drop or <span style="color: var(--color-accent); cursor: pointer;" onclick="document.getElementById('file').click()">browse</span></p>
                        <p class="text-muted" style="font-size: 0.875rem;">Maximum file size: 50MB</p>
                    </div>
                    <div class="file-selected hidden" id="fileSelected">
                        <i class="fas fa-file"></i>
                        <span id="selectedFileName"></span>
                        <button type="button" onclick="clearFile()" style="background: none; border: none; cursor: pointer; color: var(--color-text-tertiary);">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Notes (optional)</label>
                <textarea name="notes" id="notes" placeholder="Add any notes about this file..." rows="3"></textarea>
            </div>

            <div style="display: flex; gap: var(--space-md); margin-top: var(--space-md);">
                <button type="button" class="btn btn-secondary" onclick="closeUploadModal()" style="flex: 1;">
                    Cancel
                </button>
                <button type="submit" class="btn" style="flex: 1;" id="uploadBtn">
                    <i class="fas fa-cloud-arrow-up"></i>
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteFileModal" class="modal">
    <div class="modal-content" style="text-align: center;">
        <div style="width: 64px; height: 64px; background: var(--color-danger-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-lg);">
            <i class="fas fa-trash" style="font-size: 1.5rem; color: var(--color-danger);"></i>
        </div>
        <h2 style="margin-bottom: var(--space-sm);">Delete File?</h2>
        <p style="margin-bottom: var(--space-xl);">This action cannot be undone.</p>
        <div style="display: flex; gap: var(--space-md);">
            <button class="btn btn-secondary" onclick="closeDeleteFileModal()" style="flex: 1;">
                Cancel
            </button>
            <button class="btn btn-danger" onclick="executeDeleteFile()" style="flex: 1;">
                <i class="fas fa-trash"></i>
                Delete
            </button>
        </div>
    </div>
</div>

<style>
.file-drop-zone {
    border: 2px dashed var(--color-border-strong);
    border-radius: var(--radius-lg);
    padding: var(--space-xl);
    text-align: center;
    transition: all var(--transition-fast);
    cursor: pointer;
}

.file-drop-zone:hover,
.file-drop-zone.dragover {
    border-color: var(--color-accent);
    background: var(--color-accent-light);
}

.file-drop-zone .drop-zone-content {
    pointer-events: none;
}

.file-selected {
    display: flex;
    align-items: center;
    gap: var(--space-md);
    padding: var(--space-md);
    background: var(--color-bg-primary);
    border-radius: var(--radius-md);
}

.file-selected i:first-child {
    color: var(--color-accent);
}

.file-selected span {
    flex: 1;
    text-align: left;
    font-weight: 500;
}
</style>

<script>
let fileToDelete = null;

function openUploadModal() {
    document.getElementById("uploadModal").style.display = "flex";
}

function closeUploadModal() {
    document.getElementById("uploadModal").style.display = "none";
    document.getElementById("uploadForm").reset();
    clearFile();
    document.getElementById("uploadMessages").innerHTML = "";
}

function confirmDeleteFile(filename) {
    fileToDelete = filename;
    document.getElementById("deleteFileModal").style.display = "flex";
}

function closeDeleteFileModal() {
    document.getElementById("deleteFileModal").style.display = "none";
    fileToDelete = null;
}

function executeDeleteFile() {
    if (!fileToDelete) return;

    fetch("controllers/DashboardController.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "delete_file=" + encodeURIComponent(fileToDelete)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert("Error: " + (data.message || "Could not delete file"));
        }
        closeDeleteFileModal();
    })
    .catch(error => {
        alert("Error deleting file");
        closeDeleteFileModal();
    });
}

// File input handling
const fileInput = document.getElementById('file');
const dropZone = document.getElementById('dropZone');
const dropContent = dropZone.querySelector('.drop-zone-content');
const fileSelected = document.getElementById('fileSelected');
const selectedFileName = document.getElementById('selectedFileName');

fileInput.addEventListener('change', handleFileSelect);
dropZone.addEventListener('click', () => fileInput.click());

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('dragover');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('dragover');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    if (e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        handleFileSelect();
    }
});

function handleFileSelect() {
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        selectedFileName.textContent = file.name;
        dropContent.classList.add('hidden');
        fileSelected.classList.remove('hidden');
    }
}

function clearFile() {
    fileInput.value = '';
    dropContent.classList.remove('hidden');
    fileSelected.classList.add('hidden');
}

// Upload form submission
document.getElementById('uploadForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const messagesDiv = document.getElementById('uploadMessages');
    const uploadBtn = document.getElementById('uploadBtn');

    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';

    try {
        const response = await fetch('controllers/DashboardController.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            messagesDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> File uploaded successfully!</div>';
            setTimeout(() => location.reload(), 1000);
        } else {
            messagesDiv.innerHTML = '<div class="alert alert-error"><i class="fas fa-circle-exclamation"></i> ' + (data.message || 'Upload failed') + '</div>';
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="fas fa-cloud-arrow-up"></i> Upload';
        }
    } catch (error) {
        messagesDiv.innerHTML = '<div class="alert alert-error"><i class="fas fa-circle-exclamation"></i> Upload failed. Please try again.</div>';
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="fas fa-cloud-arrow-up"></i> Upload';
    }
});

// Close modals on outside click
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});

// Close modals on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.style.display = 'none';
        });
    }
});
</script>
