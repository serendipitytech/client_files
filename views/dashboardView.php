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

// Helper function for status display
function getStatusInfo($status) {
    $statusMap = [
        'pending' => ['label' => 'Pending', 'class' => 'status-pending', 'icon' => 'fa-clock'],
        'approved' => ['label' => 'Approved', 'class' => 'status-approved', 'icon' => 'fa-check-circle'],
        'needs_revision' => ['label' => 'Needs Revision', 'class' => 'status-revision', 'icon' => 'fa-exclamation-circle']
    ];
    return $statusMap[$status] ?? $statusMap['pending'];
}

// Helper function to check if file is previewable
function getPreviewType($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $videoExts = ['mp4', 'mov', 'webm'];
    $audioExts = ['mp3', 'wav', 'ogg', 'm4a'];
    $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

    if (in_array($ext, $videoExts)) return 'video';
    if (in_array($ext, $audioExts)) return 'audio';
    if (in_array($ext, $imageExts)) return 'image';
    return null;
}

// Get variables with defaults
$clientInfo = $clientInfo ?? null;
$fileStatuses = $fileStatuses ?? [];
$fileComments = $fileComments ?? [];
$isAdmin = $isAdmin ?? false;
?>

<!-- Client Info Header -->
<?php if ($clientInfo): ?>
<div class="client-info-header">
    <div class="client-info-main">
        <h1 class="client-name"><?php echo htmlspecialchars($clientInfo['client_name']); ?></h1>
        <p class="event-name"><?php echo htmlspecialchars($clientInfo['event_name']); ?></p>
    </div>
    <div class="client-info-right">
        <?php if ($isAdmin): ?>
        <button onclick="openUploadModal()" class="btn">
            <i class="fas fa-cloud-arrow-up"></i>
            Upload File
        </button>
        <?php endif; ?>
        <div class="client-info-badges">
            <span class="info-badge info-badge-date">
                <i class="fas fa-calendar"></i>
                <?php echo date('M j, Y', strtotime($clientInfo['event_date'])); ?>
            </span>
            <span class="info-badge info-badge-code">
                <i class="fas fa-key"></i>
                <?php echo htmlspecialchars($clientInfo['event_code']); ?>
            </span>
        </div>
    </div>
</div>
<?php else: ?>
<div class="page-header">
    <h1>Your Files</h1>
    <p>View and manage your uploaded files</p>
</div>
<?php endif; ?>

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
            $fileAbsPath = __DIR__ . "/../files/$clientFolder/$file";
            $fileSize = file_exists($fileAbsPath) ? filesize($fileAbsPath) : 0;
            $fileStatus = $fileStatuses[$file] ?? ['status' => 'pending'];
            $statusInfo = getStatusInfo($fileStatus['status']);
            $commentCount = isset($fileComments[$file]) ? count($fileComments[$file]) : 0;
            $previewType = getPreviewType($file);
            $filePath = "files/" . htmlspecialchars($clientFolder . '/' . $file);
        ?>
            <div class="file-card">
                <div class="file-card-header">
                    <div class="file-card-icon-group">
                        <div class="file-card-icon">
                            <i class="fas <?php echo getFileIcon($file); ?>"></i>
                        </div>
                        <?php if ($previewType): ?>
                        <button class="preview-btn" onclick="openPreview('<?php echo htmlspecialchars($filePath, ENT_QUOTES); ?>', '<?php echo $previewType; ?>', '<?php echo htmlspecialchars($file, ENT_QUOTES); ?>')" title="Preview">
                            <i class="fas fa-play"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                    <span class="file-status-badge <?php echo $statusInfo['class']; ?>">
                        <i class="fas <?php echo $statusInfo['icon']; ?>"></i>
                        <?php echo $statusInfo['label']; ?>
                    </span>
                </div>
                <div class="file-card-name"><?php echo htmlspecialchars($file); ?></div>
                <div class="file-card-meta"><?php echo human_filesize($fileSize); ?></div>
                <?php if (!empty($notes[$file])): ?>
                    <p style="font-size: 0.875rem; color: var(--color-text-secondary); margin-top: var(--space-sm);">
                        <?php echo htmlspecialchars($notes[$file]); ?>
                    </p>
                <?php endif; ?>

                <div class="status-control">
                    <label>Status:</label>
                    <select class="status-select" onchange="updateFileStatus('<?php echo htmlspecialchars($file, ENT_QUOTES); ?>', this.value)">
                        <option value="pending" <?php echo $fileStatus['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo $fileStatus['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="needs_revision" <?php echo $fileStatus['status'] === 'needs_revision' ? 'selected' : ''; ?>>Needs Revision</option>
                    </select>
                </div>

                <div class="file-card-actions">
                    <a href="<?php echo $filePath; ?>" download class="btn btn-secondary" style="flex: 1; padding: var(--space-sm) var(--space-md);">
                        <i class="fas fa-download"></i>
                        Download
                    </a>
                    <button onclick="openCommentsModal('<?php echo htmlspecialchars($file, ENT_QUOTES); ?>')" class="btn btn-secondary comment-btn" style="padding: var(--space-sm) var(--space-md);">
                        <i class="fas fa-comment"></i>
                        <?php if ($commentCount > 0): ?>
                        <span class="comment-count"><?php echo $commentCount; ?></span>
                        <?php endif; ?>
                    </button>
                    <?php if ($isAdmin): ?>
                    <button onclick="confirmDeleteFile('<?php echo htmlspecialchars($file); ?>')" class="btn btn-secondary" style="padding: var(--space-sm) var(--space-md); color: var(--color-danger);">
                        <i class="fas fa-trash"></i>
                    </button>
                    <?php endif; ?>
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
                        <p class="text-muted" style="font-size: 0.875rem;">Maximum file size: 500MB</p>
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

<!-- Comments Modal -->
<div id="commentsModal" class="modal">
    <div class="modal-content modal-comments">
        <span class="close" onclick="closeCommentsModal()">&times;</span>
        <h2><i class="fas fa-comments"></i> Comments</h2>
        <p id="commentsFileName" class="comments-file-label"></p>

        <div id="commentsContainer" class="comments-container">
            <div class="comments-loading">
                <i class="fas fa-spinner fa-spin"></i> Loading comments...
            </div>
        </div>

        <form id="commentForm" class="comment-form">
            <textarea id="commentInput" name="message" placeholder="Write a comment..." rows="2" required></textarea>
            <button type="submit" class="btn">
                <i class="fas fa-paper-plane"></i>
                Send
            </button>
        </form>
    </div>
</div>

<!-- Preview Modal (Lightbox) -->
<div id="previewModal" class="modal preview-modal">
    <div class="preview-modal-content">
        <button class="preview-close" onclick="closePreview()">
            <i class="fas fa-times"></i>
        </button>
        <div class="preview-filename" id="previewFileName"></div>
        <div class="preview-media-container" id="previewContainer">
            <!-- Media content injected here -->
        </div>
    </div>
</div>

<style>
/* Client Info Header */
.client-info-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: var(--space-xl);
    padding: var(--space-xl);
    background: var(--color-bg-secondary);
    border-radius: var(--radius-xl);
    border: 1px solid var(--color-border);
    box-shadow: var(--shadow-sm);
}

.client-info-main {
    display: flex;
    flex-direction: column;
    gap: var(--space-xs);
}

.client-name {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--color-text-primary);
    margin: 0;
}

.event-name {
    font-size: 1rem;
    color: var(--color-text-secondary);
    margin: 0;
}

.client-info-badges {
    display: flex;
    gap: var(--space-sm);
    flex-wrap: wrap;
}

.info-badge {
    display: inline-flex;
    align-items: center;
    gap: var(--space-xs);
    padding: var(--space-sm) var(--space-md);
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: var(--radius-full);
    background: var(--color-bg-primary);
    color: var(--color-text-secondary);
}

.info-badge i {
    font-size: 0.75rem;
}

.info-badge-date {
    background: rgba(52, 199, 89, 0.12);
    color: var(--color-success);
}

.info-badge-code {
    background: var(--color-accent-light);
    color: var(--color-accent);
}

/* Client Info Right (badges + upload button) */
.client-info-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: var(--space-md);
}

/* File Card Header */
.file-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: var(--space-md);
}

/* File Card Icon Group (icon + play button) */
.file-card-icon-group {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
}

/* Preview/Play Button */
.preview-btn {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: var(--radius-full);
    background: var(--color-accent);
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--transition-fast);
    box-shadow: var(--shadow-sm);
}

.preview-btn:hover {
    background: var(--color-accent-hover);
    transform: scale(1.1);
    box-shadow: var(--shadow-md);
}

.preview-btn i {
    font-size: 0.75rem;
    margin-left: 2px;
}

/* File Status Badge */
.file-status-badge {
    display: inline-flex;
    align-items: center;
    gap: var(--space-xs);
    padding: var(--space-xs) var(--space-sm);
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: var(--radius-full);
}

.file-status-badge i {
    font-size: 0.625rem;
}

.status-pending {
    background: rgba(255, 159, 10, 0.12);
    color: var(--color-warning);
}

.status-approved {
    background: rgba(52, 199, 89, 0.12);
    color: var(--color-success);
}

.status-revision {
    background: var(--color-danger-light);
    color: var(--color-danger);
}

/* Status Control (Admin) */
.status-control {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    margin-top: var(--space-md);
    padding-top: var(--space-md);
    border-top: 1px solid var(--color-border);
}

.status-control label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--color-text-tertiary);
}

.status-select {
    flex: 1;
    padding: var(--space-sm) var(--space-md);
    font-size: 0.875rem;
    background: var(--color-bg-primary);
    border: 1px solid var(--color-border-strong);
    border-radius: var(--radius-md);
    cursor: pointer;
}

.status-select:focus {
    outline: none;
    border-color: var(--color-accent);
    box-shadow: 0 0 0 3px var(--color-accent-light);
}

/* Comment Button */
.comment-btn {
    position: relative;
}

.comment-count {
    position: absolute;
    top: -6px;
    right: -6px;
    min-width: 18px;
    height: 18px;
    padding: 0 var(--space-xs);
    font-size: 0.625rem;
    font-weight: 700;
    color: white;
    background: var(--color-accent);
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Comments Modal */
.modal-comments {
    max-width: 560px;
}

.comments-file-label {
    font-size: 0.875rem;
    color: var(--color-text-tertiary);
    margin-bottom: var(--space-md);
    padding-bottom: var(--space-md);
    border-bottom: 1px solid var(--color-border);
}

.comments-container {
    max-height: 320px;
    overflow-y: auto;
    margin-bottom: var(--space-lg);
    padding-right: var(--space-sm);
}

.comments-loading {
    text-align: center;
    padding: var(--space-xl);
    color: var(--color-text-tertiary);
}

.comments-empty {
    text-align: center;
    padding: var(--space-xl);
    color: var(--color-text-tertiary);
}

.comment {
    padding: var(--space-md);
    margin-bottom: var(--space-sm);
    border-radius: var(--radius-md);
    background: var(--color-bg-primary);
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--space-xs);
}

.comment-author {
    font-size: 0.875rem;
    font-weight: 600;
}

.comment-time {
    font-size: 0.75rem;
    color: var(--color-text-tertiary);
}

.comment-message {
    font-size: 0.9375rem;
    color: var(--color-text-primary);
    line-height: 1.5;
    white-space: pre-wrap;
    word-break: break-word;
}

.comment-admin {
    border-left: 3px solid var(--color-accent);
}

.comment-admin .comment-author {
    color: var(--color-accent);
}

.comment-client {
    border-left: 3px solid var(--color-success);
}

.comment-client .comment-author {
    color: var(--color-success);
}

/* Comment Form */
.comment-form {
    display: flex;
    gap: var(--space-sm);
    align-items: flex-end;
}

.comment-form textarea {
    flex: 1;
    min-height: 48px;
    resize: none;
}

.comment-form .btn {
    padding: var(--space-md);
}

/* File Drop Zone */
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

/* Preview Modal (Lightbox) */
.preview-modal {
    background: rgba(0, 0, 0, 0.9);
}

.preview-modal-content {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.preview-close {
    position: fixed;
    top: var(--space-lg);
    right: var(--space-lg);
    width: 48px;
    height: 48px;
    border: none;
    border-radius: var(--radius-full);
    background: rgba(255, 255, 255, 0.1);
    color: white;
    font-size: 1.25rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--transition-fast);
    z-index: 1001;
}

.preview-close:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
}

.preview-filename {
    color: white;
    font-size: 1rem;
    font-weight: 500;
    margin-bottom: var(--space-lg);
    text-align: center;
    max-width: 80vw;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.preview-media-container {
    display: flex;
    align-items: center;
    justify-content: center;
    max-width: 85vw;
    max-height: 80vh;
}

.preview-media-container video,
.preview-media-container audio,
.preview-media-container img {
    max-width: 100%;
    max-height: 75vh;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-xl);
}

.preview-media-container video {
    background: #000;
}

.preview-media-container audio {
    width: 400px;
    max-width: 85vw;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .client-info-header {
        flex-direction: column;
        gap: var(--space-md);
    }

    .client-info-badges {
        width: 100%;
    }

    .client-info-right {
        width: 100%;
        align-items: stretch;
    }

    .preview-close {
        top: var(--space-md);
        right: var(--space-md);
        width: 40px;
        height: 40px;
    }
}
</style>

<script>
let fileToDelete = null;
let currentCommentFile = null;

function openUploadModal() {
    document.getElementById("uploadModal").style.display = "flex";
}

// Preview Modal Functions
function openPreview(filePath, mediaType, fileName) {
    const modal = document.getElementById('previewModal');
    const container = document.getElementById('previewContainer');
    const fileNameEl = document.getElementById('previewFileName');

    fileNameEl.textContent = fileName;

    let mediaHtml = '';
    if (mediaType === 'video') {
        mediaHtml = `<video controls autoplay>
            <source src="${filePath}" type="video/mp4">
            Your browser does not support video playback.
        </video>`;
    } else if (mediaType === 'audio') {
        mediaHtml = `<audio controls autoplay>
            <source src="${filePath}">
            Your browser does not support audio playback.
        </audio>`;
    } else if (mediaType === 'image') {
        mediaHtml = `<img src="${filePath}" alt="${fileName}">`;
    }

    container.innerHTML = mediaHtml;
    modal.style.display = 'flex';

    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function closePreview() {
    const modal = document.getElementById('previewModal');
    const container = document.getElementById('previewContainer');

    // Stop any playing media
    const video = container.querySelector('video');
    const audio = container.querySelector('audio');
    if (video) video.pause();
    if (audio) audio.pause();

    container.innerHTML = '';
    modal.style.display = 'none';

    // Restore body scroll
    document.body.style.overflow = '';
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

// Status Update (Admin only)
function updateFileStatus(filename, status) {
    fetch("controllers/DashboardController.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "update_status=" + encodeURIComponent(status) + "&filename=" + encodeURIComponent(filename)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert("Error: " + (data.message || "Could not update status"));
        }
    })
    .catch(error => {
        alert("Error updating status");
    });
}

// Comments Modal
function openCommentsModal(filename) {
    currentCommentFile = filename;
    document.getElementById("commentsFileName").textContent = filename;
    document.getElementById("commentsModal").style.display = "flex";
    loadComments(filename);
}

function closeCommentsModal() {
    document.getElementById("commentsModal").style.display = "none";
    currentCommentFile = null;
    document.getElementById("commentInput").value = "";
}

function loadComments(filename) {
    const container = document.getElementById("commentsContainer");
    container.innerHTML = '<div class="comments-loading"><i class="fas fa-spinner fa-spin"></i> Loading comments...</div>';

    fetch("controllers/DashboardController.php?get_comments=" + encodeURIComponent(filename))
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderComments(data.comments);
        } else {
            container.innerHTML = '<div class="comments-empty">Error loading comments</div>';
        }
    })
    .catch(error => {
        container.innerHTML = '<div class="comments-empty">Error loading comments</div>';
    });
}

function renderComments(comments) {
    const container = document.getElementById("commentsContainer");

    if (!comments || comments.length === 0) {
        container.innerHTML = '<div class="comments-empty"><i class="fas fa-comment-slash"></i><p>No comments yet. Start the conversation!</p></div>';
        return;
    }

    container.innerHTML = comments.map(comment => {
        const date = new Date(comment.timestamp);
        const timeStr = date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        const authorClass = comment.author_type === 'admin' ? 'comment-admin' : 'comment-client';

        return `
            <div class="comment ${authorClass}">
                <div class="comment-header">
                    <span class="comment-author">${escapeHtml(comment.author_name)}</span>
                    <span class="comment-time">${timeStr}</span>
                </div>
                <div class="comment-message">${escapeHtml(comment.message)}</div>
            </div>
        `;
    }).join('');

    container.scrollTop = container.scrollHeight;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Comment form submission
document.getElementById('commentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!currentCommentFile) return;

    const input = document.getElementById('commentInput');
    const message = input.value.trim();

    if (!message) return;

    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch("controllers/DashboardController.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "add_comment=" + encodeURIComponent(message) + "&filename=" + encodeURIComponent(currentCommentFile)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = "";
            loadComments(currentCommentFile);
        } else {
            alert("Error: " + (data.message || "Could not add comment"));
        }
    })
    .catch(error => {
        alert("Error adding comment");
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send';
    });
});

// File input handling (only if upload elements exist)
const fileInput = document.getElementById('file');
const dropZone = document.getElementById('dropZone');

if (fileInput && dropZone) {
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

    window.clearFile = function() {
        fileInput.value = '';
        dropContent.classList.remove('hidden');
        fileSelected.classList.add('hidden');
    };
}

function clearFile() {
    if (window.clearFile) {
        window.clearFile();
    }
}

// Upload form submission
const uploadForm = document.getElementById('uploadForm');
if (uploadForm) {
    uploadForm.addEventListener('submit', async (e) => {
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
}

// Close modals on outside click
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            if (modal.id === 'previewModal') {
                closePreview();
            } else {
                modal.style.display = 'none';
                if (modal.id === 'commentsModal') {
                    currentCommentFile = null;
                }
            }
        }
    });
});

// Close modals on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        // Check if preview modal is open
        const previewModal = document.getElementById('previewModal');
        if (previewModal && previewModal.style.display === 'flex') {
            closePreview();
            return;
        }
        document.querySelectorAll('.modal').forEach(modal => {
            modal.style.display = 'none';
        });
        currentCommentFile = null;
    }
});
</script>
