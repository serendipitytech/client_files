<?php
function isJsonRequest() {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    return (
        stripos($contentType, 'application/json') !== false ||
        stripos($accept, 'application/json') !== false ||
        $_SERVER['REQUEST_METHOD'] === 'POST'
    );
}

function handleError($errno, $errstr, $errfile, $errline) {
    $errorMessage = "Error [$errno]: $errstr in $errfile on line $errline";
    error_log($errorMessage);

    if (ini_get("display_errors")) {
        if (isJsonRequest()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $errorMessage]);
        } else {
            echo "<p style='color: red;'>An error occurred. Please try again later.</p>";
        }
    }
}

function handleException($exception) {
    $errorMessage = "Exception: " . $exception->getMessage();
    error_log($errorMessage);

    if (ini_get("display_errors")) {
        if (isJsonRequest()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $errorMessage]);
        } else {
            echo "<p style='color: red;'>An error occurred. Please try again later.</p>";
        }
    }
}

set_error_handler("handleError");
set_exception_handler("handleException");
?>