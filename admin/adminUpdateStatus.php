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

// Get order ID from URL parameter
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if(!$order_id) {
    echo "<script> location.href='adminPaymentStatus.php'; </script>";
    exit;
}

// Fetch order details
$order_query = $conn->prepare("SELECT * FROM courseorder WHERE order_id = ?");
$order_query->bind_param("s", $order_id);
$order_query->execute();
$order_result = $order_query->get_result();

if($order_result->num_rows == 0) {
    echo "<script> location.href='adminPaymentStatus.php'; </script>";
    exit;
}

$order = $order_result->fetch_assoc();
$order_query->close();

// Handle form submission
$update_success = false;
if(isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $response_msg = $_POST['response_msg'];
    
    $update_query = $conn->prepare("UPDATE courseorder SET status = ?, respomsg = ? WHERE order_id = ?");
    $update_query->bind_param("sss", $new_status, $response_msg, $order_id);
    
    if($update_query->execute()) {
        $update_success = true;
        // Refresh order data
        $order['status'] = $new_status;
        $order['respomsg'] = $response_msg;
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
    
    .alert-success {
        background-color: #d1fae5;
        color: #1cc88a;
        border-color: #b8f0d4;
    }
</style>

<div class="col-sm-9 mt-5">
    <div class="update-container">
        <h3 class="update-header">Update Payment Status</h3>
        
        <?php if($update_success): ?>
        <div class="alert alert-success mb-4">
            Status updated successfully!
        </div>
        <?php endif; ?>
        
        <div class="mb-4">
            <a href="adminPaymentStatus.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Payments
            </a>
        </div>
        
        <!-- Order Summary -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Order Summary</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
                        <p><strong>Student Email:</strong> <?php echo htmlspecialchars($order['stuemail']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Amount:</strong> Tsh <?php echo number_format($order['course_price'], 2); ?></p>
                        <p><strong>Current Status:</strong> 
                            <span class="status-badge status-<?php echo htmlspecialchars($order['status']); ?>">
                                <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Update Form -->
        <form method="POST">
            <div class="form-group">
                <label for="status" class="form-label">New Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="failed" <?php echo $order['status'] == 'failed' ? 'selected' : ''; ?>>Failed</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="response_msg" class="form-label">Response Message</label>
                <textarea class="form-control" id="response_msg" name="response_msg" rows="3"><?php echo htmlspecialchars($order['respomsg']); ?></textarea>
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