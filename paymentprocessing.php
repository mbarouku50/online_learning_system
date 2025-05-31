<?php
session_start();
include("./templates/header.php");
include("./dbconnection.php");

// Verify purchase ID
$purchase_id = isset($_GET['purchase_id']) ? (int)$_GET['purchase_id'] : 0;
if ($purchase_id <= 0) {
    die("<div class='alert alert-danger'>Invalid purchase ID.</div>");
}

// Verify student is logged in and owns this purchase
if (!isset($_SESSION['stud_id']) || !isset($_SESSION['stuemail'])) {
    die("<div class='alert alert-danger'>Please login to view this page.</div>");
}

// Get purchase details
$sql = "SELECT bp.*, b.book_title, b.image_path 
        FROM book_purchases bp
        JOIN books b ON bp.book_id = b.id
        WHERE bp.id = ? AND bp.student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $purchase_id, $_SESSION['stud_id']);
$stmt->execute();
$result = $stmt->get_result();
$purchase = $result->fetch_assoc();
$stmt->close();

if (!$purchase) {
    die("<div class='alert alert-danger'>Purchase not found or access denied.</div>");
}
?>

<style>
    .processing-container {
        max-width: 800px;
        margin: 50px auto;
        padding: 30px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        text-align: center;
    }
    
    .processing-icon {
        font-size: 4rem;
        color: #4e73df;
        margin-bottom: 20px;
    }
    
    .processing-message {
        font-size: 1.2rem;
        margin-bottom: 30px;
    }
    
    .status-badge {
        font-size: 1rem;
        padding: 8px 15px;
        border-radius: 20px;
        margin: 10px 0;
    }
    
    .badge-pending {
        background-color: #fff3cd;
        color: #856404;
    }
</style>

<div class="container">
    <div class="processing-container">
        <div class="processing-icon">
            <i class="fas fa-spinner fa-spin"></i>
        </div>
        <h3>Processing Your Payment</h3>
        <div class="processing-message">
            <p>We're currently processing your payment for:</p>
            <h4><?php echo htmlspecialchars($purchase['book_title']); ?></h4>
            <span class="status-badge badge-pending">Status: <?php echo strtoupper($purchase['status']); ?></span>
        </div>
        
        <?php if ($purchase['status'] === 'pending'): ?>
            <div class="payment-instructions">
                <p>Please complete the payment on your mobile device or payment gateway.</p>
                <p>This page will automatically update when payment is confirmed.</p>
            </div>
            
            <script>
            // Check payment status every 5 seconds
            function checkPaymentStatus() {
                fetch('checkpayment.php?purchase_id=<?php echo $purchase_id; ?>')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status !== 'pending') {
                            if (data.status === 'completed') {
                                window.location.href = 'purchasedownload.php?purchase_id=<?php echo $purchase_id; ?>';
                            } else {
                                window.location.href = 'paymentfailed.php?purchase_id=<?php echo $purchase_id; ?>';
                            }
                        } else {
                            setTimeout(checkPaymentStatus, 5000);
                        }
                    });
            }
            
            // Start checking status after 5 seconds
            setTimeout(checkPaymentStatus, 5000);
            </script>
        <?php endif; ?>
    </div>
</div>

<?php include("./templates/footer.php"); ?>