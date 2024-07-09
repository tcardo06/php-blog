<?php
require_once 'UnauthorizedAccessException.php';
require_once 'NormalTerminationException.php';

function handleException($exception) {
    if ($exception instanceof UnauthorizedAccessException) {
        header('Location: ../access_denied.php');
        exit;
    } elseif ($exception instanceof NormalTerminationException) {
        echo json_encode($exception->getData());
        exit;
    } else {
        echo 'An error occurred: ' . $exception->getMessage();
    }
}

set_exception_handler('handleException');
?>
