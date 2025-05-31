<?php
header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'dbconnection.php';

// Verify database connection
if (!isset($conn) || $conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Email is required']);
    exit;
}

try {
    // Check email existence
    $query = "SELECT 'student' as user_type FROM student WHERE stuemail = ? 
              UNION 
              SELECT 'admin' as user_type FROM admin WHERE admin_email = ?";
    
    if (!$stmt = $conn->prepare($query)) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    if (!$stmt->bind_param("ss", $email, $email)) {
        throw new Exception("Binding parameters failed");
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email not found']);
        exit;
    }

    $user = $result->fetch_assoc();
    $userType = $user['user_type'];

    // Generate token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Clean old tokens
    $deleteQuery = "DELETE FROM password_resets WHERE email = ?";
    if (!$deleteStmt = $conn->prepare($deleteQuery)) {
        throw new Exception("Delete prepare failed: " . $conn->error);
    }
    $deleteStmt->bind_param("s", $email);
    $deleteStmt->execute();

    // Insert new token
    $insertQuery = "INSERT INTO password_resets (email, token, user_type, expires_at) VALUES (?, ?, ?, ?)";
    if (!$insertStmt = $conn->prepare($insertQuery)) {
        throw new Exception("Insert prepare failed: " . $conn->error);
    }
    $insertStmt->bind_param("ssss", $email, $token, $userType, $expires);
    
    if (!$insertStmt->execute()) {
        throw new Exception("Insert failed: " . $insertStmt->error);
    }

    // For testing - in production, implement email sending
    $resetLink = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . 
                $_SERVER['HTTP_HOST'] . 
                "/reset-password.php?token=$token";
    
    // For testing only - remove in production
    error_log("Password reset link for $email: $resetLink");
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'Password reset link sent to your email',
        'dev_link' => $resetLink // Remove in production
    ]);

} catch (Exception $e) {
    error_log("Password reset error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred. Please try again later.',
        'debug' => $e->getMessage() // Remove in production
    ]);
}
?>