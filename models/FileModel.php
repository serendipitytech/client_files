<?php
class FileModel {
    private $folderPath;
    private $notesFilePath;
    private $statusFilePath;
    private $commentsFilePath;
    private $clientFolder;

    public function __construct($clientFolder) {
        $this->clientFolder = $clientFolder;
        $this->folderPath = __DIR__ . "/../files/$clientFolder";
        $this->notesFilePath = $this->folderPath . "/notes.json";
        $this->statusFilePath = $this->folderPath . "/file_status.json";
        $this->commentsFilePath = $this->folderPath . "/comments.json";
    }

    // Get client info from clients.json
    public function getClientInfo() {
        $clientsFile = __DIR__ . "/../clients.json";
        if (!file_exists($clientsFile)) {
            return null;
        }

        $clients = json_decode(file_get_contents($clientsFile), true);
        if (!is_array($clients)) {
            return null;
        }

        foreach ($clients as $client) {
            if (isset($client['folder_name']) && $client['folder_name'] === $this->clientFolder) {
                return $client;
            }
        }

        return null;
    }

    // Get all file statuses
    public function getFileStatuses() {
        if (!file_exists($this->statusFilePath)) {
            return [];
        }
        return json_decode(file_get_contents($this->statusFilePath), true) ?? [];
    }

    // Get status for a specific file
    public function getFileStatus($filename) {
        $statuses = $this->getFileStatuses();
        return $statuses[$filename] ?? ['status' => 'pending', 'updated_at' => null, 'updated_by' => null];
    }

    // Update file status (admin only)
    public function updateFileStatus($filename, $status, $updatedBy = 'admin') {
        $validStatuses = ['pending', 'approved', 'needs_revision'];
        if (!in_array($status, $validStatuses, true)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }

        $statuses = $this->getFileStatuses();
        $statuses[$filename] = [
            'status' => $status,
            'updated_at' => date('c'),
            'updated_by' => $updatedBy
        ];

        if (!is_dir($this->folderPath)) {
            mkdir($this->folderPath, 0755, true);
        }

        file_put_contents($this->statusFilePath, json_encode($statuses, JSON_PRETTY_PRINT));
        return ['success' => true, 'message' => 'Status updated', 'status' => $statuses[$filename]];
    }

    // Get all comments
    public function getAllComments() {
        if (!file_exists($this->commentsFilePath)) {
            return [];
        }
        return json_decode(file_get_contents($this->commentsFilePath), true) ?? [];
    }

    // Get comments for a specific file
    public function getFileComments($filename) {
        $comments = $this->getAllComments();
        return $comments[$filename] ?? [];
    }

    // Add a comment to a file
    public function addComment($filename, $authorType, $authorName, $message) {
        $message = trim($message);
        if (empty($message)) {
            return ['success' => false, 'message' => 'Comment cannot be empty'];
        }

        $validAuthorTypes = ['admin', 'client'];
        if (!in_array($authorType, $validAuthorTypes, true)) {
            return ['success' => false, 'message' => 'Invalid author type'];
        }

        $comments = $this->getAllComments();
        if (!isset($comments[$filename])) {
            $comments[$filename] = [];
        }

        $newComment = [
            'id' => uniqid('comment_', true),
            'author_type' => $authorType,
            'author_name' => $authorName,
            'message' => $message,
            'timestamp' => date('c')
        ];

        $comments[$filename][] = $newComment;

        if (!is_dir($this->folderPath)) {
            mkdir($this->folderPath, 0755, true);
        }

        file_put_contents($this->commentsFilePath, json_encode($comments, JSON_PRETTY_PRINT));
        return ['success' => true, 'message' => 'Comment added', 'comment' => $newComment];
    }

    // Get comment count for a file
    public function getCommentCount($filename) {
        return count($this->getFileComments($filename));
    }

    // Load files
    public function getFiles() {
        return is_dir($this->folderPath) ? array_diff(scandir($this->folderPath), ['.', '..']) : [];
    }

    // Get notes
    public function getNotes() {
        return file_exists($this->notesFilePath) ? json_decode(file_get_contents($this->notesFilePath), true) : [];
    }

    // Upload File
    public function uploadFile($file, $notes) {
        // Check for upload errors
        if (!isset($file["error"]) || $file["error"] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => "File exceeds upload_max_filesize",
                UPLOAD_ERR_FORM_SIZE => "File exceeds MAX_FILE_SIZE",
                UPLOAD_ERR_PARTIAL => "File was only partially uploaded",
                UPLOAD_ERR_NO_FILE => "No file was uploaded",
                UPLOAD_ERR_NO_TMP_DIR => "Missing temporary folder",
                UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
                UPLOAD_ERR_EXTENSION => "Upload stopped by extension"
            ];
            $errorCode = $file["error"] ?? -1;
            $message = $errorMessages[$errorCode] ?? "Unknown upload error (code: $errorCode)";
            return ['success' => false, 'message' => $message];
        }

        // Validate file name
        if (empty($file["name"])) {
            return ['success' => false, 'message' => "Invalid file name"];
        }

        $targetFile = $this->folderPath . "/" . basename($file["name"]);

        // Create directory if it doesn't exist
        if (!is_dir($this->folderPath)) {
            if (!mkdir($this->folderPath, 0755, true)) {
                return ['success' => false, 'message' => "Failed to create upload directory"];
            }
        }

        // Check directory is writable
        if (!is_writable($this->folderPath)) {
            return ['success' => false, 'message' => "Upload directory is not writable"];
        }

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            $notesData = $this->getNotes();
            $notesData[basename($file["name"])] = $notes;
            file_put_contents($this->notesFilePath, json_encode($notesData, JSON_PRETTY_PRINT));
            return ['success' => true, 'message' => "File uploaded successfully.", 'file' => basename($file["name"])];
        }

        return ['success' => false, 'message' => "Failed to move uploaded file"];
    }

    // Delete File
    public function deleteFile($fileName) {
        $fileToDelete = $this->folderPath . "/" . basename($fileName);
        if (file_exists($fileToDelete)) {
            unlink($fileToDelete);
            return ['success' => true, 'message' => "File deleted successfully."];
        }
        return ['success' => false, 'message' => "File not found."];
    }
}
?>