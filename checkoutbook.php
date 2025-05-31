<?php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include dependencies
include("./templates/header.php");
include("./dbconnection.php");

// Configuration
define('BASE_URL', "http://localhost/online_learning_system/");
define('BASE_PATH', "/var/www/html/online_learning_system/");

// Function to get proper image path
function getImagePath($image_path) {
    if (empty($image_path)) {
        return false;
    }
    $clean_path = ltrim(str_replace(['../', './'], '', $image_path), '/');
    $server_path = BASE_PATH . $clean_path;
    if (file_exists($server_path) && is_readable($server_path)) {
        return BASE_URL . $clean_path;
    }
    return false;
}

// Redirect if not logged in
if (!isset($_SESSION['is_login'])) {
    header("Location: loginorsignup.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

// Verify student information
if (!isset($_SESSION['stud_id']) || !isset($_SESSION['stuemail'])) {
    die("<div class='alert alert-danger'>Student information missing. Please login again.</div>");
}

// Get student information
$stud_id = isset($_GET['stud_id']) ? (int)$_GET['stud_id'] : (int)$_SESSION['stud_id'];
$stuemail = $_SESSION['stuemail'];

// Check database connection
if (!$conn) {
    die("<div class='alert alert-danger text-center'>Database connection failed: " . mysqli_connect_error() . "</div>");
}

// Verify student exists
$sql = "SELECT stud_id, studname, stuemail, studreg FROM student WHERE stud_id = ? AND stuemail = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("<div class='alert alert-danger'>Database error: " . $conn->error . "</div>");
}
$stmt->bind_param("is", $stud_id, $stuemail);
$stmt->execute();
$student_result = $stmt->get_result();

if ($student_result->num_rows !== 1) {
    $stmt->close();
    die("<div class='alert alert-danger'>Student verification failed. Please login again.</div>");
}

$student = $student_result->fetch_assoc();
$stmt->close();

// Get book details
$book_id = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;
if ($book_id <= 0) {
    die("<div class='alert alert-danger'>Invalid book ID.</div>");
}

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

// Generate unique order ID (for display only, actual payment ID will be generated on purchase)
$order_id = 'ORD-' . strtoupper(uniqid());

// Handle purchase
if (isset($_POST['purchase'])) {
    // Generate unique payment ID
    $payment_id = 'PAY-' . time() . '-' . $stud_id;
    $book_id = (int)$book['id'];
    $price = (float)$book['price'];
    $purchase_date = date('Y-m-d H:i:s');
    $payment_method = $_POST['payment_method'] ?? 'mpesa';
    $status = 'pending'; // Set initial status as pending
    
    // Validate payment method
    $valid_methods = ['mpesa', 'card', 'tigopesa', 'halopesa', 'airtelmoney'];
    if (!in_array($payment_method, $valid_methods)) {
        $payment_method = 'mpesa';
    }

    // Create purchase record with status
    $sql = "INSERT INTO book_purchases 
            (payment_id, student_id, book_id, price, purchase_date, payment_method, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("<div class='alert alert-danger'>Database error: " . $conn->error . "</div>");
    }
    
    $stmt->bind_param("siidsss", $payment_id, $stud_id, $book_id, $price, $purchase_date, $payment_method, $status);
    
    if ($stmt->execute()) {
        $purchase_id = $stmt->insert_id;
        $stmt->close();
        
        // Redirect to a processing page instead of direct download
        header("Location: paymentprocessing.php?purchase_id=$purchase_id");
        exit();
    } else {
        $error_message = "<div class='alert alert-danger'>Error processing purchase: " . $stmt->error . "</div>";
        $stmt->close();
    }
}
?>

<style>
    :root {
        --primary-color: #225470;
        --secondary-color: #2c3e50;
        --accent-color: #4e73df;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --warning-color: #ffc107;
    }

    .checkout-container {
        max-width: 900px;
        margin: 50px auto;
        padding: 40px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid #e0e0e0;
    }
    
    .checkout-header {
        text-align: center;
        margin-bottom: 40px;
        position: relative;
    }
    
    .checkout-header h2 {
        font-weight: 700;
        color: var(--primary-color);
        position: relative;
        display: inline-block;
    }
    
    .checkout-header h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: var(--accent-color);
        border-radius: 3px;
    }
    
    .book-checkout-card {
        display: flex;
        margin-bottom: 30px;
        border-bottom: 1px solid #eee;
        padding-bottom: 25px;
        flex-wrap: wrap;
    }
    
    .book-checkout-image-container {
        width: 180px;
        height: 240px;
        margin-right: 25px;
        flex-shrink: 0;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        overflow: hidden;
    }
    
    .book-checkout-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    
    .book-checkout-image-container:hover .book-checkout-image {
        transform: scale(1.03);
    }
    
    .book-checkout-image-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--secondary-color);
    }
    
    .book-checkout-details {
        flex: 1;
        min-width: 300px;
    }
    
    .book-checkout-details h3 {
        font-weight: 700;
        color: var(--secondary-color);
        margin-bottom: 5px;
    }
    
    .book-author {
        color: #6c757d;
        font-size: 1.1rem;
        margin-bottom: 15px;
    }
    
    .book-description {
        color: #555;
        line-height: 1.6;
        margin-bottom: 15px;
    }
    
    .department-badge {
        font-size: 0.9rem;
        padding: 6px 12px;
        background: var(--accent-color);
    }
    
    .price-display {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--success-color);
        margin: 30px 0;
        text-align: right;
        padding: 15px 20px;
        background: rgba(40, 167, 69, 0.1);
        border-radius: 8px;
        display: inline-block;
        float: right;
    }
    
    .payment-methods {
        margin: 40px 0;
        clear: both;
    }
    
    .payment-methods h5 {
        font-weight: 600;
        color: var(--secondary-color);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .payment-method {
        display: flex;
        align-items: center;
        padding: 18px 20px;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.3s;
        background: white;
    }
    
    .payment-method:hover {
        border-color: var(--accent-color);
        box-shadow: 0 5px 15px rgba(78, 115, 223, 0.1);
        transform: translateY(-2px);
    }
    
    .payment-method input {
        margin-right: 15px;
        transform: scale(1.2);
    }
    
    .payment-method i {
        font-size: 1.8rem;
        margin-right: 15px;
        color: var(--accent-color);
        width: 40px;
        text-align: center;
    }
    
    .payment-method-details {
        flex: 1;
    }
    
    .payment-method h6 {
        font-weight: 600;
        margin-bottom: 5px;
        color: var(--secondary-color);
    }
    
    .payment-method small {
        color: #6c757d;
    }
    
    .confirm-btn {
        background: var(--success-color);
        color: white;
        border: none;
        padding: 14px 35px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .confirm-btn:hover {
        background: #218838;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(40, 167, 69, 0.3);
    }
    
    .confirm-btn i {
        margin-left: 10px;
    }
    
    .back-btn {
        background: white;
        color: var(--secondary-color);
        border: 1px solid #ddd;
        padding: 14px 30px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .back-btn:hover {
        background: #f8f9fa;
        border-color: #ccc;
        transform: translateY(-2px);
    }
    
    .order-info {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 30px;
    }
    
    .order-info h5 {
        font-weight: 600;
        color: var(--secondary-color);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .info-row {
        display: flex;
        margin-bottom: 15px;
    }
    
    .info-label {
        font-weight: 600;
        color: var(--secondary-color);
        width: 180px;
    }
    
    .info-value {
        flex: 1;
        color: #555;
    }
    
    .order-id {
        color: var(--accent-color);
        font-weight: 700;
        letter-spacing: 1px;
    }
    
    @media (max-width: 768px) {
        .checkout-container {
            padding: 25px;
            margin: 20px auto;
        }
        
        .book-checkout-card {
            flex-direction: column;
        }
        
        .book-checkout-image-container {
            width: 100%;
            height: auto;
            margin-right: 0;
            margin-bottom: 20px;
        }
        
        .price-display {
            text-align: center;
            float: none;
            display: block;
            width: 100%;
        }
        
        .info-row {
            flex-direction: column;
        }
        
        .info-label {
            width: 100%;
            margin-bottom: 5px;
        }
    }
    
    @media (max-width: 576px) {
        .checkout-container {
            padding: 20px;
        }
        
        .d-md-flex {
            flex-direction: column;
        }
        
        .confirm-btn, .back-btn {
            width: 100%;
            margin-bottom: 10px;
        }
    }
</style>

<div class="container">
    <div class="checkout-container">
        <div class="checkout-header">
            <h2>Complete Your Purchase</h2>
            <p class="text-muted">Review your order and select payment method</p>
        </div>
        
        <?php if (isset($error_message)) echo $error_message; ?>
        
        <div class="order-info">
            <h5>Order Summary</h5>
            <div class="info-row">
                <div class="info-label">Order ID:</div>
                <div class="info-value order-id"><?php echo $order_id; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Date:</div>
                <div class="info-value"><?php echo date('F j, Y, g:i a'); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Customer:</div>
                <div class="info-value"><?php echo htmlspecialchars($student['studname']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value"><?php echo htmlspecialchars($student['stuemail']); ?></div>
            </div>
        </div>
        
        <div class="book-checkout-card">
            <div class="book-checkout-image-container">
                <?php 
                $image_url = getImagePath($book['image_path']);
                if ($image_url && file_exists(BASE_PATH . ltrim(str_replace(['../', './'], '', $book['image_path']), '/'))): ?>
                    <img src="<?php echo htmlspecialchars($image_url); ?>" 
                         alt="<?php echo htmlspecialchars($book['book_title']); ?>" 
                         class="book-checkout-image"
                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\"book-checkout-image-placeholder\"><i class=\"fas fa-book-open fa-3x\"></i></div>'">
                <?php else: ?>
                    <div class="book-checkout-image-placeholder">
                        <i class="fas fa-book-open fa-3x"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="book-checkout-details">
                <h3><?php echo htmlspecialchars($book['book_title']); ?></h3>
                <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
                <p class="book-description"><?php echo htmlspecialchars($book['description'] ?? 'No description available'); ?></p>
                <div class="mt-3">
                    <span class="badge department-badge">
                        <?php echo isset($book['department']) ? str_replace('_', ' ', htmlspecialchars($book['department'])) : 'General'; ?>
                    </span>
                </div>
            </div>
            
            <div class="price-display">
                Total: Tsh <?php echo number_format($book['price'], 0); ?>
            </div>
        </div>
        
        <form method="POST">
            <div class="payment-methods">
                <h5>Select Payment Method</h5>
                
                <label class="payment-method">
                    <input type="radio" name="payment_method" value="mpesa" checked>
                    <i class="fas fa-mobile-alt"></i>
                    <div class="payment-method-details">
                        <h6>M-Pesa</h6>
                        <small>Pay via M-Pesa mobile money. You'll receive a payment request on your phone.</small>
                    </div>
                </label>
                
                <label class="payment-method">
                    <input type="radio" name="payment_method" value="card">
                    <i class="far fa-credit-card"></i>
                    <div class="payment-method-details">
                        <h6>Credit/Debit Card</h6>
                        <small>Secure payment with Visa, Mastercard, or other major cards.</small>
                    </div>
                </label>
                
                <label class="payment-method">
                    <input type="radio" name="payment_method" value="tigopesa">
                    <i class="fas fa-wallet"></i>
                    <div class="payment-method-details">
                        <h6>Tigo Pesa</h6>
                        <small>Pay directly from your Tigo Pesa wallet.</small>
                    </div>
                </label>
                
                <label class="payment-method">
                    <input type="radio" name="payment_method" value="halopesa">
                    <i class="fas fa-wallet"></i>
                    <div class="payment-method-details">
                        <h6>Halo Pesa</h6>
                        <small>Convenient payment with Halo Pesa mobile money.</small>
                    </div>
                </label>
                
                <label class="payment-method">
                    <input type="radio" name="payment_method" value="airtelmoney">
                    <i class="fas fa-wallet"></i>
                    <div class="payment-method-details">
                        <h6>Airtel Money</h6>
                        <small>Quick payment using your Airtel Money account.</small>
                    </div>
                </label>
            </div>
            
            <div class="d-grid gap-3 d-md-flex justify-content-md-end mt-5">
                <a href="books.php" class="back-btn">
                    <i class="fas fa-arrow-left me-2"></i> Back to Books
                </a>
                <button type="submit" name="purchase" class="confirm-btn">
                    Complete Payment <i class="fas fa-lock"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<?php
include("./templates/footer.php");
$conn->close();
?>