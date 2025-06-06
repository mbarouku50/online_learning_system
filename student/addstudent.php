<?php
// Enable error reporting and log errors
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Set JSON header
header('Content-Type: application/json');

// Start output buffering
ob_start();

// Include database connection
try {
    $dbFile = __DIR__ . '/../dbconnection.php';
    if (!file_exists($dbFile)) {
        throw new Exception('Database connection file not found: ' . $dbFile);
    }
    require_once $dbFile;
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to include database connection: ' . $e->getMessage()
    ]);
    exit;
}

// Check database connection
if (!isset($conn) || $conn->connect_error) {
    ob_end_clean();
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . ($conn->connect_error ?? 'Connection not established')
    ]);
    exit;
}

// Handle student registration
try {
    // Check for stusignup
    if (!isset($_POST['stusignup'])) {
        throw new Exception('Invalid request: stusignup missing');
    }

    // Validate required fields
    $required = ['studname', 'studreg', 'stuemail', 'stupass'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst($field) . ' is required');
        }
    }

    // Sanitize inputs
    $studname = $conn->real_escape_string(trim($_POST['studname']));
    $studreg = $conn->real_escape_string(trim($_POST['studreg']));
    $stuemail = $conn->real_escape_string(trim($_POST['stuemail']));
    $stupass = $_POST['stupass'];

    log_action('debug', 'system', "Processing registration for: $studname ($stuemail)");

    // Validate email
    if (!filter_var($stuemail, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Check if email exists
    $checkEmail = $conn->prepare('SELECT stuemail FROM student WHERE stuemail = ?');
    $checkEmail->bind_param('s', $stuemail);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        throw new Exception('Email already registered');
    }
    $checkEmail->close();

    // Hash password
    $hashedPass = password_hash($stupass, PASSWORD_DEFAULT);

    // Insert new student
    $stmt = $conn->prepare('INSERT INTO student (studname, studreg, stuemail, stupass) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $studname, $studreg, $stuemail, $hashedPass);

    if ($stmt->execute()) {
        $newStudentId = $conn->insert_id;
        log_action('registration_success', $stuemail, "New student registered (ID: $newStudentId)");
        ob_end_clean();
        echo json_encode([
            'status' => 'success',
            'message' => 'Registration successful! You can now login.'

        ]);
    } else {
        log_action('database_error', $stuemail, $errorMsg);
        throw new Exception('Registration failed: ' . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
     log_action('error', isset($stuemail) ? $stuemail : 'unknown', $e->getMessage());
    ob_end_clean();
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}


// Close connection
$conn->close();
exit;
?>