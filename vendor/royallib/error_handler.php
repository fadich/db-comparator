<?php

set_error_handler('error_handler');

function error_handler($severity, $message, $filename, $lineno) {
    if (error_reporting() == 0) {
        return;
    }
    if (error_reporting() & $severity) {
        displayError(new ErrorException($message, 0, $severity, $filename, $lineno));
    }
}

function displayError($throwable)
{
    if ($throwable instanceof \Throwable) {
        return "<b>" . get_class($throwable) . "</b> with message: \n" . $throwable->getMessage() . ".\n"
            . $throwable->getFile() . ":" . $throwable->getLine() . "\n" . $this->displayError($throwable->getPrevious());
    }
    return '';
}