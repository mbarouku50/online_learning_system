<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
if (!isset($_SESSION)) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['is_login'])) {
    header("Location: ../index.php");
    exit;
}

// Get the file parameter and sanitize it
$file = isset($_GET['file']) ? basename(trim($_GET['file'])) : '';

if (empty($file)) {
    http_response_code(400);
    echo "Error: No file specified.";
    exit;
}

// Define the directory where assignment files are stored
$base_dir = __DIR__ . '/../admin/assignment_uploads/';
$file_path = $base_dir . $file;

// Log the file path for debugging
error_log("Attempting to access file: $file_path", 3, '/var/www/html/online_learning_system/error.log');

// Check if the directory exists
if (!is_dir($base_dir)) {
    http_response_code(500);
    echo "Error: Assignment directory not found.";
    exit;
}

// Check if the file exists
if (!file_exists($file_path)) {
    http_response_code(404);
    echo "Error: File '$file' not found in assignment_uploads directory.";
    exit;
}

// Check if the file is readable
if (!is_readable($file_path)) {
    http_response_code(403);
    echo "Error: File '$file' is not readable.";
    exit;
}

// Determine the MIME type
$extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
$mime_types = [
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'txt' => 'text/plain',
    'jpg' => 'image/jpeg',
    'png' => 'image/png'
];
$mime_type = isset($mime_types[$extension]) ? $mime_types[$extension] : 'application/octet-stream';

// Serve the file
header('Content-Type: ' . $mime_type);
header('Content-Disposition: inline; filename="' . basename($file_path) . '"');
header('Content-Length: ' . filesize($file_path));
readfile($file_path);
exit;
?>