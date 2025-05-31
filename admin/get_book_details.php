<?php
session_start();
include("../dbconnection.php");

if (!isset($_SESSION['is_admin_login'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized access']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = (int)$_POST['book_id'];
    $table_name = filter_input(INPUT_POST, 'table_name', FILTER_SANITIZE_STRING);
    
    $allowed_tables = [
        'ict_books', 'metrology_books', 'business_books', 
        'procurement_books', 'marketing_books', 
        'accountancy_books', 'business_admin_books'
    ];
    
    if (!in_array($table_name, $allowed_tables)) {
        die(json_encode(['status' => 'error', 'message' => 'Invalid table name']));
    }
    
    $sql = "SELECT * FROM `$table_name` WHERE book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
        echo json_encode(['status' => 'success', 'data' => $book]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Book not found']);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>