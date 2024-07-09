<?php
require_once 'UnauthorizedAccessException.php';

function handleException($exception) {
    if ($exception instanceof UnauthorizedAccessException) {
        header('Location: ../access_denied.php');
        exit;
    } else {
        echo 'An error occurred: ' . $exception->getMessage();
        // Optionally log the error or take other actions
    }
}

set_exception_handler('handleException');
?>
