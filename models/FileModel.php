<?php
class FileModel {
    private $folderPath;
    private $notesFilePath;

    public function __construct($clientFolder) {
        $this->folderPath = "./files/$clientFolder";
        $this->notesFilePath = $this->folderPath . "/notes.json";
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
        $targetFile = $this->folderPath . "/" . basename($file["name"]);
        if (!is_dir($this->folderPath)) mkdir($this->folderPath, 0755, true);

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            $notesData = $this->getNotes();
            $notesData[basename($file["name"])] = $notes;
            file_put_contents($this->notesFilePath, json_encode($notesData, JSON_PRETTY_PRINT));
            return ['success' => true, 'message' => "File uploaded successfully.", 'file' => basename($file["name"])];
        }

        return ['success' => false, 'message' => "Error uploading file."];
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