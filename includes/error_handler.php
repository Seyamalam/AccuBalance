<?php
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $error = [
        'type' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // Log error
    error_log(json_encode($error) . "\n", 3, "logs/error.log");
    
    if (ini_get('display_errors')) {
        printf("<div class='error-message'>Error: %s</div>", $errstr);
    }
    
    return true;
}

set_error_handler('customErrorHandler'); 