<?php
function handleError($errno, $errstr, $errfile, $errline) {
    $errorMessage = "Error [$errno]: $errstr in $errfile on line $errline";
    error_log($errorMessage); // Log the error
    if (ini_get("display_errors")) {
        echo "<p style='color: red;'>An error occurred. Please try again later.</p>";
    }
}

function handleException($exception) {
    $errorMessage = "Exception: " . $exception->getMessage();
    error_log($errorMessage); // Log the exception
    if (ini_get("display_errors")) {
        echo "<p style='color: red;'>An error occurred. Please try again later.</p>";
    }
}

set_error_handler("handleError");
set_exception_handler("handleException");
?>