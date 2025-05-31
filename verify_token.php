<?php
header('Content-Type: application/json');
require_once 'dbconnection.php';

$token = $_GET['token'] ?? '';

// Verify token exists and isn't expired
$stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or expired token']);
    exit;
}

$resetRequest = $result->fetch_assoc();

echo json_encode([
    'status' => 'success',
    'email' => $resetRequest['email'],
    'user_type' => $resetRequest['user_type']
]);
?>