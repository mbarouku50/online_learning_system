<?php
session_start();
include("./dbconnection.php");

header('Content-Type: application/json');

$purchase_id = isset($_GET['purchase_id']) ? (int)$_GET['purchase_id'] : 0;
if ($purchase_id <= 0) {
    die(json_encode(['error' => 'Invalid purchase ID']));
}

// Verify student owns this purchase
if (!isset($_SESSION['stud_id'])) {
    die(json_encode(['error' => 'Not authorized']));
}

$sql = "SELECT status FROM book_purchases WHERE id = ? AND student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $purchase_id, $_SESSION['stud_id']);
$stmt->execute();
$result = $stmt->get_result();
$purchase = $result->fetch_assoc();
$stmt->close();

if (!$purchase) {
    die(json_encode(['error' => 'Purchase not found']));
}

echo json_encode(['status' => $purchase['status']]);
?>