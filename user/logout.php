<?php
require_once '../NormalTerminationException.php';
require_once '../error_handler.php';
session_start();

try {
    session_unset();
    session_destroy();
    throw new NormalTerminationException('Redirect', ['url' => '../public/index.php']);
} catch (NormalTerminationException $e) {
    if ($e->getData()['url']) {
        header('Location: ' . $e->getData()['url']);
        return;
    }
} catch (Exception $e) {
    error_log('An error occurred: ' . $e->getMessage());
    echo 'An error occurred. Please try again later.';
}
?>
