<?php
$db_host = "localhost";
$db_user = "phpmyadmin";
$db_password = "m";
$db_name = "03_2481_01_01_2023";

//create connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

//check connection
if($conn->connect_error){
    die("connection failed");
}

function log_action($action, $username, $details = "") {
    $log_file = 'logs/user_actions_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] User: $username | Action: $action | Details: $details\n";
    
    // Create logs directory if it doesn't exist
    if (!file_exists('logs')) {
        mkdir('logs', 0777, true);
    }
    
    // Write to log file
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}


?>
