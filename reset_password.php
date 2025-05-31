<?php
header('Content-Type: application/json');
require_once 'dbconnection.php';

$token = $_POST['token'] ?? '';
$newPassword = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Basic validation
if(empty($token) || empty($newPassword) || empty($confirmPassword)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

if($newPassword !== $confirmPassword) {
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
    exit;
}

if(strlen($newPassword) < 6) {
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters']);
    exit;
}

// Verify token
$stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or expired token']);
    exit;
}

$resetRequest = $result->fetch_assoc();
$email = $resetRequest['email'];
$userType = $resetRequest['user_type'];

// Hash the new password
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Update password in the appropriate table
if($userType === 'admin') {
    $stmt = $conn->prepare("UPDATE admin SET admin_pass = ? WHERE admin_email = ?");
} else {
    $stmt = $conn->prepare("UPDATE student SET stupass = ? WHERE stuemail = ?");
}

$stmt->bind_param("ss", $hashedPassword, $email);
$updateSuccess = $stmt->execute();

if(!$updateSuccess) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
    exit;
}

// Delete the used token
$stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();

echo json_encode([
    'status' => 'success',
    'message' => 'Password updated successfully. You can now login with your new password.'
]);
?>