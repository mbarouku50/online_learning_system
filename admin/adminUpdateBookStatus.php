<?php
if(!isset($_SESSION)){
    session_start();
}

include("./admininclude/header.php");
include("../dbconnection.php");

if(isset($_SESSION['is_admin_login'])){
    $adminEmail = $_SESSION['admin_email'];
} else {
    echo "<script> location.href='../index.php'; </script>";
}

// Get purchase ID from URL parameter
$purchase_id = isset($_GET['purchase_id']) ? (int)$_GET['purchase_id'] : 0;

if(!$purchase_id) {
    echo "<script> location.href='adminBookPaymentStatus.php'; </script>";
    exit;
}

// Fetch purchase details
$purchase_query = $conn->prepare("SELECT bp.*, b.book_title, s.studname, s.stuemail 
                                FROM book_purchases bp
                                JOIN books b ON bp.book_id = b.id
                                JOIN student s ON bp.student_id = s.stud_id
                                WHERE bp.purchase_id = ?");
$purchase_query->bind_param("i", $purchase_id);
$purchase_query->execute();
$purchase_result = $purchase_query->get_result();

if($purchase_result->num_rows == 0) {
    echo "<script> location.href='adminBookPaymentStatus.php'; </script>";
    exit;
}

$purchase = $purchase_result->fetch_assoc();
$purchase_query->close();

// Handle form submission
// Handle form submission
if(isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    
    $update_query = $conn->prepare("UPDATE book_purchases SET status = ? WHERE purchase_id = ?");
    $update_query->bind_param("si", $new_status, $purchase_id); // Changed "ssi" to "si"
    
    if($update_query->execute()) {
        // Redirect immediately after successful update
        header("Location:adminBookPaymentStatus.php");
        exit();
    } else {
        // Add error handling to see what went wrong
        echo "Error updating record: " . $conn->error;
    }
    $update_query->close();
}
?>

<style>
    .update-container {
        padding: 30px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .update-header {
        color: #2c3e50;
        border-bottom: 2px solid #4e73df;
        padding-bottom: 10px;
        margin-bottom: 30px;
    }
    
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    
    .status-pending {
        background-color: #f8e3da;
        color: #e74a3b;
    }
    
    .status-completed {
        background-color: #d1fae5;
        color: #1cc88a;
    }
    
    .status-failed {
        background-color: #e9ecef;
        color: #858796;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .back-btn {
        margin-top: 30px;
    }
    
    .purchase-details {
        margin-bottom: 30px;
    }
    
    .detail-row {
        margin-bottom: 10px;
    }
    
    .detail-label {
        font-weight: 600;
        color: #2c3e50;
    }
</style>

<div class="col-sm-9 mt-5">
    <div class="update-container">
        <h3 class="update-header">Update Book Purchase Status</h3>
        
        <div class="mb-4">
            <a href="adminBookPaymentStatus.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Book Purchases
            </a>
        </div>
        
        <!-- Purchase Summary -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Purchase Details</h5>
                <div class="purchase-details">
                    <div class="row detail-row">
                        <div class="col-md-6">
                            <span class="detail-label">Purchase ID:</span> <?php echo htmlspecialchars($purchase['purchase_id']); ?>
                        </div>
                        <div class="col-md-6">
                            <span class="detail-label">Book:</span> <?php echo htmlspecialchars($purchase['book_id'].' - '.$purchase['book_title']); ?>
                        </div>
                    </div>
                    <div class="row detail-row">
                        <div class="col-md-6">
                            <span class="detail-label">Student:</span> <?php echo htmlspecialchars($purchase['student_id'].' - '.$purchase['studname']); ?>
                        </div>
                        <div class="col-md-6">
                            <span class="detail-label">Email:</span> <?php echo htmlspecialchars($purchase['stuemail']); ?>
                        </div>
                    </div>
                    <div class="row detail-row">
                        <div class="col-md-6">
                            <span class="detail-label">Amount:</span> Tsh <?php echo number_format($purchase['price'], 2); ?>
                        </div>
                        <div class="col-md-6">
                            <span class="detail-label">Payment Method:</span> <?php echo ucfirst(htmlspecialchars($purchase['payment_method'])); ?>
                        </div>
                    </div>
                    <div class="row detail-row">
                        <div class="col-md-6">
                            <span class="detail-label">Purchase Date:</span> <?php echo date('d/m/Y H:i', strtotime($purchase['purchase_date'])); ?>
                        </div>
                        <div class="col-md-6">
                            <span class="detail-label">Current Status:</span> 
                            <span class="status-badge status-<?php echo htmlspecialchars($purchase['status']); ?>">
                                <?php echo ucfirst(htmlspecialchars($purchase['status'])); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Update Form -->
        <form method="POST">
            <div class="form-group">
                <label for="status" class="form-label">New Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="completed" <?php echo $purchase['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="pending" <?php echo $purchase['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="failed" <?php echo $purchase['status'] == 'failed' ? 'selected' : ''; ?>>Failed</option>
                </select>
            </div>
            
            <button type="submit" name="update_status" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Update Status
            </button>
        </form>
    </div>
</div>

<?php
include("./admininclude/footer.php");
?>