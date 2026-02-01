<?php
require_once __DIR__ . "/../config.php";

class AdminModel {
    private $clientsFile;

    public function __construct() {
        $this->clientsFile = __DIR__ . "/../clients.json"; // ✅ Updated to point to root
    }
    
    public function getClients() {
        if (!file_exists($this->clientsFile)) {
            error_log("⚠️ clients.json not found at: " . $this->clientsFile);
            return [];
        }
    
        $fileContents = file_get_contents($this->clientsFile);
        if ($fileContents === false) {
            error_log("⚠️ Failed to read clients.json.");
            return [];
        }
    
        $clients = json_decode($fileContents, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("⚠️ JSON decode error: " . json_last_error_msg());
            return [];
        }
    
        return is_array($clients) ? $clients : [];
    }

    // Save updated clients list
    private function saveClients($clients) {
        file_put_contents($this->clientsFile, json_encode($clients, JSON_PRETTY_PRINT));
    }

    // Add or update a client
    public function saveClient($clientName, $eventName, $eventDate, $folderName = null) {
        $clients = $this->getClients();

        if ($folderName) {
            // Update existing client
            foreach ($clients as &$client) {
                if ($client['folder_name'] === $folderName) {
                    $client['client_name'] = $clientName;
                    $client['event_name'] = $eventName;
                    $client['event_date'] = $eventDate;
                    break;
                }
            }
        } else {
            // Add new client
            $folderName = strtolower(str_replace(' ', '_', $clientName));
            $clients[] = [
                'client_name' => $clientName,
                'event_name' => $eventName,
                'event_date' => $eventDate,
                'event_code' => bin2hex(random_bytes(4)),
                'folder_name' => $folderName
            ];
        }

        $this->saveClients($clients);
        return true;
    }

    // Delete a client
    public function deleteClient($folderName) {
        $clients = $this->getClients();
        $clients = array_filter($clients, fn($client) => $client['folder_name'] !== $folderName);
        $this->saveClients($clients);
        return true;
    }

    // Get client file info
    public function getClientFileInfo($folderName) {
        $folderPath = __DIR__ . "/../files/$folderName";
        $fileInfo = [
            'count' => 0,
            'last_edit' => "Never"
        ];
    
        if (is_dir($folderPath)) {
            $files = array_diff(scandir($folderPath), ['.', '..']);
            
            $lastEditTime = 0;
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) !== 'json') {
                    $fileInfo['count']++;
                    $filePath = "$folderPath/$file";
                    $fileEditTime = filemtime($filePath);
                    if ($fileEditTime > $lastEditTime) {
                        $lastEditTime = $fileEditTime;
                    }
                }
            }
    
            if ($lastEditTime > 0) {
                $fileInfo['last_edit'] = $this->timeAgo($lastEditTime);
            }
        }
    
        return $fileInfo;
    }

    // Convert timestamp to "Today", "Yesterday", or "X days ago"
private function timeAgo($timestamp) {
    $diff = time() - $timestamp;
    $days = floor($diff / 86400);

    if ($days < 1) {
        return "Today";
    } elseif ($days == 1) {
        return "Yesterday";
    } else {
        return "$days days ago";
    }
}
}
?>