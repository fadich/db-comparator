<?php

set_error_handler('error_handler');

function error_handler($severity, $message, $filename, $lineno, $previous ) {
    if (error_reporting() == 0) {
        return;
    }
    echo '<pre>';
    if ($previous instanceof \Throwable) {
        displayError(new ErrorException($message, 0, $severity, $filename, $lineno, $previous));
    } else {
        displayError(new ErrorException($message, 0, $severity, $filename, $lineno));
    }
    exit();
}

function displayError($throwable) {
    if ($throwable instanceof \Throwable) {
        echo "<b>" . get_class($throwable) . "</b> with message: \n" . $throwable->getMessage() . ".\n"
            . $throwable->getFile() . ":" . $throwable->getLine() . "\n" . displayError($throwable->getPrevious());
    }
    echo '';
}
