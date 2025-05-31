<?php
// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set Tanzania timezone
date_default_timezone_set('Africa/Dar_es_Salaam');

// Start session with security settings before any output
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure' => false, // Set to true if using HTTPS
    'cookie_httponly' => true,
    'use_strict_mode' => true
]);

if (!isset($_SESSION['is_login'])) {
    header("Location: loginorsignup.php");
    exit();
}

// Define project path
$project_path = __DIR__;

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
$required_session_vars = ['stuemail', 'course_id', 'course_name', 'course_price'];
$missing_vars = [];
foreach ($required_session_vars as $var) {
    if (!isset($_SESSION[$var])) {
        $missing_vars[] = $var;
    }
}

if (!empty($missing_vars)) {
    // Redirect to coursedetails.php with error
    $_SESSION['checkout_error'] = "Missing required data: " . implode(", ", $missing_vars);
    header("Location: coursedetails.php?id=" . ($_SESSION['course_id'] ?? ''));
    exit();
}

$stuemail = $_SESSION['stuemail'];
$course_id = (int)$_SESSION['course_id'];
$course_name = $_SESSION['course_name'];
$course_price = (float)$_SESSION['course_price'];

// Validate course price
if ($course_price <= 0) {
    die("Invalid course price: " . htmlspecialchars($course_price));
}

// Generate secure order ID
$order_id = "ORDS" . bin2hex(random_bytes(6));

// Process payment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proceed_payment'])) {
    try {
        // Begin transaction for atomic operation
        $conn->begin_transaction();
        
        $status = "Pending";
        $respomsg = "Payment initiated";
        $order_date = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO courseorder 
                (order_id, stuemail, course_id, status, respomsg, course_price, order_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        // Bind parameters with proper types
        $bound = $stmt->bind_param(
            "ssisssd",
            $order_id,
            $stuemail,
            $course_id,
            $status,
            $respomsg,
            $course_price,
            $order_date
        );
        
        if (!$bound) {
            throw new Exception("Bind failed: " . $stmt->error);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        // Commit transaction
        $conn->commit();
        
        // Clear session data
        unset($_SESSION['course_id']);
        unset($_SESSION['course_price']);
        unset($_SESSION['course_name']);
        
        // Redirect to success page
        header("Location: index.php?payment=success&order_id=" . urlencode($order_id));
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
                <a href='courses.php' class='btn btn-secondary mt-3'>Back to Courses</a>
            </div>");
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | E-Learning</title>
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
        .course-header {
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
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="payment-container">
            <div class="course-header text-center">
                <h2 class="mb-3">Complete Your Enrollment</h2>
                <h4 class="text-muted"><?= htmlspecialchars($course_name) ?></h4>
            </div>
            
            <form method="post" class="needs-validation" novalidate>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Order ID</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($order_id) ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Student Email</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($stuemail) ?>" readonly>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Amount to Pay</label>
                    <div class="input-group">
                        <span class="input-group-text">Tsh</span>
                        <input type="text" class="form-control amount-display" 
                               value="<?= number_format($course_price, 2) ?>" readonly>
                    </div>
                </div>
                
                <div class="d-grid gap-3 d-md-flex justify-content-md-end mt-5">
                    <a href="courses.php" class="btn btn-outline-secondary me-md-2">
                        <i class="fas fa-arrow-left me-2"></i> Back to Courses
                    </a>
                    <button type="submit" name="proceed_payment" class="btn btn-primary btn-payment">
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