<?php


// Set Tanzania timezone
date_default_timezone_set('Africa/Dar_es_Salaam');

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400,
        'cookie_secure' => false, // Set to true if using HTTPS
        'cookie_httponly' => true,
        'use_strict_mode' => true
    ]);
}

// Define project path and configuration
define('BASE_URL', "http://localhost/online_learning_system/");
define('BASE_PATH', "/var/www/html/online_learning_system/");

// Function to get proper image path
function getImagePath($image_path) {
    if (empty($image_path)) {
        return false;
    }
    $clean_path = ltrim(str_replace(['../', './'], '', $image_path), '/');
    $server_path = BASE_PATH . $clean_path;
    if (file_exists($server_path)) {
        return BASE_URL . $clean_path;
    }
    return false;
}

// Define project path
$project_path = __DIR__;

// Redirect if not logged in
if (!isset($_SESSION['is_login'])) {
    header("Location: loginorsignup.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

// Database connection - with enhanced error handling
try {
    include($project_path . '/dbconnection.php');
    
    // Verify connection
    if (!$conn || $conn->connect_errno) {
        throw new Exception("Database connection failed: " . ($conn->connect_error ?? "Unknown error"));
    }
    
    // Test connection
    if (!$conn->query("SELECT 1")) {
        throw new Exception("Database connection is not active");
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Validate all required session data exists before proceeding
if (!isset($_SESSION['stud_id']) || !isset($_SESSION['stuemail'])) {
    die("<div class='alert alert-danger'>Student information missing. Please login again.</div>");
}

$stud_id = (int)$_SESSION['stud_id'];
$stuemail = $_SESSION['stuemail'];

// Get book details
$book_id = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;
if ($book_id <= 0) {
    die("<div class='alert alert-danger'>Invalid book ID.</div>");
}

// Generate a reference number for display only (not stored in database)
$reference_number = "REF-" . bin2hex(random_bytes(4));

// Process payment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['purchase'])) {
    try {
        // Begin transaction for atomic operation
        $conn->begin_transaction();
        
        // Get book details again to ensure consistency
        $sql = "SELECT id, book_title, price FROM books WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();
        $stmt->close();
        
        if (!$book) {
            throw new Exception("Book not found");
        }
        
        $status = "pending";
        $purchase_date = date('Y-m-d H:i:s');
        $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'mpesa';
        $price = (float)$book['price'];
        
        // Validate payment method
        $valid_methods = ['mpesa', 'card', 'tigopesa', 'halopesa', 'airtelmoney'];
        if (!in_array($payment_method, $valid_methods)) {
            $payment_method = 'mpesa';
        }
        
        // Modified to match your table structure
        $sql = "INSERT INTO book_purchases 
                (student_id, book_id, price, purchase_date, payment_method, status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        // Bind parameters with proper types
        $bound = $stmt->bind_param(
            "iidsss",
            $stud_id,
            $book_id,
            $price,
            $purchase_date,
            $payment_method,
            $status
        );
        
        if (!$bound) {
            throw new Exception("Bind failed: " . $stmt->error);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        // Commit transaction
        $conn->commit();
        
        // Redirect to success page
        header("Location: index.php?payment=success");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if (isset($conn) && method_exists($conn, 'rollback')) {
            $conn->rollback();
        }
        
        // Log detailed error
        error_log("Payment Error [" . date('Y-m-d H:i:s') . "]: " . $e->getMessage());
        
        // User-friendly error message
        die("<div class='alert alert-danger text-center mt-5'>
                <h4>Payment Processing Error</h4>
                <p>We encountered an error processing your payment.</p>
                <p><small>Error: " . htmlspecialchars($e->getMessage()) . "</small></p>
                <a href='books.php' class='btn btn-secondary mt-3'>Back to Books</a>
            </div>");
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
    }
}

// Get book details for display
$sql = "SELECT id, book_title, author, description, price, image_path, department 
        FROM books 
        WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("<div class='alert alert-danger'>Database error: " . $conn->error . "</div>");
}
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
$stmt->close();

if (!$book) {
    die("<div class='alert alert-danger'>Book not found.</div>");
}

// Get student details for display
$sql = "SELECT studname FROM student WHERE stud_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("<div class='alert alert-danger'>Database error: " . $conn->error . "</div>");
}
$stmt->bind_param("i", $stud_id);
$stmt->execute();
$student_result = $stmt->get_result();
$student = $student_result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Checkout | E-Learning</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .payment-container {
            max-width: 800px;
            margin: 3rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .book-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        .form-control[readonly] {
            background-color: #f8f9fa;
        }
        .btn-payment {
            padding: 10px 25px;
            font-weight: 600;
        }
        .amount-display {
            font-size: 1.2rem;
            font-weight: 700;
            color: #28a745;
        }
        .book-image-container {
            width: 200px;
            height: 250px;
            margin: 0 auto 20px;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .book-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .book-image-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
        .payment-method {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .payment-method:hover {
            border-color: #4e73df;
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.1);
        }
        .payment-method input {
            margin-right: 10px;
        }
        .payment-icon {
            font-size: 1.5rem;
            margin-right: 15px;
            color: #4e73df;
            width: 30px;
            text-align: center;
        }
        .payment-details h6 {
            font-weight: 600;
            margin-bottom: 3px;
        }
        .payment-details p {
            color: #6c757d;
            font-size: 0.85rem;
            margin-bottom: 0;
        }
        .reference-number {
            color: #4e73df;
            font-weight: 700;
        }
        @media (max-width: 576px) {
            .book-image-container {
                width: 150px;
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <?php 
    // Check if header hasn't been included yet to prevent duplicate session starts
    if (!function_exists('getHeaderIncluded')) {
        include("./templates/header.php");
    }
    ?>
    
    <div class="container py-5">
        <div class="payment-container">
            <div class="book-header text-center">
                <h2 class="mb-3">Complete Your Book Purchase</h2>
                <h4 class="text-muted"><?= htmlspecialchars($book['book_title'] ?? '') ?></h4>
            </div>
            
            <div class="text-center mb-4">
                <div class="book-image-container">
                    <?php 
                    $image_url = getImagePath($book['image_path'] ?? '');
                    if ($image_url): ?>
                        <img src="<?= htmlspecialchars($image_url) ?>" 
                             alt="<?= htmlspecialchars($book['book_title'] ?? '') ?>" 
                             class="book-image"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\"book-image-placeholder\"><i class=\"fas fa-book-open fa-3x\"></i></div>'">
                    <?php else: ?>
                        <div class="book-image-placeholder">
                            <i class="fas fa-book-open fa-3x"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <p class="text-muted">by <?= htmlspecialchars($book['author'] ?? '') ?></p>
            </div>
            
            <form method="post" class="needs-validation" novalidate>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Reference Number</label>
                        <input type="text" class="form-control reference-number" value="<?= htmlspecialchars($reference_number) ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Student Name</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($student['studname'] ?? '') ?>" readonly>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Amount to Pay</label>
                    <div class="input-group">
                        <span class="input-group-text">Tsh</span>
                        <input type="text" class="form-control amount-display" 
                               value="<?= isset($book['price']) ? number_format($book['price'], 2) : '0.00' ?>" readonly>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h5 class="mb-3">Select Payment Method</h5>
                    
                    <label class="payment-method">
                        <input type="radio" name="payment_method" value="mpesa" checked>
                        <div class="payment-icon"><i class="fas fa-mobile-alt"></i></div>
                        <div class="payment-details">
                            <h6>M-Pesa</h6>
                            <p>Pay via M-Pesa mobile money</p>
                        </div>
                    </label>
                    
                    <label class="payment-method">
                        <input type="radio" name="payment_method" value="card">
                        <div class="payment-icon"><i class="far fa-credit-card"></i></div>
                        <div class="payment-details">
                            <h6>Credit/Debit Card</h6>
                            <p>Secure payment with Visa/Mastercard</p>
                        </div>
                    </label>
                    
                    <label class="payment-method">
                        <input type="radio" name="payment_method" value="tigopesa">
                        <div class="payment-icon"><i class="fas fa-wallet"></i></div>
                        <div class="payment-details">
                            <h6>Tigo Pesa</h6>
                            <p>Pay from your Tigo Pesa wallet</p>
                        </div>
                    </label>
                    
                    <label class="payment-method">
                        <input type="radio" name="payment_method" value="halopesa">
                        <div class="payment-icon"><i class="fas fa-wallet"></i></div>
                        <div class="payment-details">
                            <h6>Halo Pesa</h6>
                            <p>Convenient Halo Pesa payment</p>
                        </div>
                    </label>
                    
                    <label class="payment-method">
                        <input type="radio" name="payment_method" value="airtelmoney">
                        <div class="payment-icon"><i class="fas fa-wallet"></i></div>
                        <div class="payment-details">
                            <h6>Airtel Money</h6>
                            <p>Quick Airtel Money payment</p>
                        </div>
                    </label>
                </div>
                
                <div class="d-grid gap-3 d-md-flex justify-content-md-end mt-5">
                    <a href="books.php" class="btn btn-outline-secondary me-md-2">
                        <i class="fas fa-arrow-left me-2"></i> Back to Books
                    </a>
                    <button type="submit" name="purchase" class="btn btn-primary btn-payment">
                        <i class="fas fa-lock me-2"></i> Complete Payment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Client-side validation
        (function() {
            'use strict';
            const form = document.querySelector('.needs-validation');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        })();
    </script>
</body>
</html>
<?php
$conn->close();
?>