<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/SessionManager.php";

class AuthModel {
    private $clientsFile;

    public function __construct() {
        $this->clientsFile = CLIENTS_FILE;
    }

    public function verifyAdmin($username, $password) {
        if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
            $_SESSION["admin_logged_in"] = true;
            return true;
        }
        return false;
    }

    public function verifyClient($eventCode) {
        if (!file_exists($this->clientsFile)) return false;
        $clients = json_decode(file_get_contents($this->clientsFile), true);

        foreach ($clients as $client) {
            if ($client['event_code'] === $eventCode) {
                $_SESSION["client_folder"] = $client['folder_name'];
                return true;
            }
        }
        return false;
    }
}
?>