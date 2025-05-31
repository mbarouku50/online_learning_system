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
$purchase_query = $conn->prepare("SELECT bp.*, b.book_title, b.author, b.description, b.image_path,
                                 s.studname, s.stuemail, s.studreg
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

// Determine status class
$status_class = '';
switch($purchase['status']) {
    case 'completed':
        $status_class = 'status-completed';
        break;
    case 'pending':
        $status_class = 'status-pending';
        break;
    case 'failed':
        $status_class = 'status-failed';
        break;
}
?>

<style>
    .view-container {
        padding: 30px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .view-header {
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
    
    .back-btn {
        margin-top: 30px;
    }
    
    .detail-card {
        margin-bottom: 30px;
    }
    
    .detail-row {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .detail-label {
        font-weight: 600;
        color: #2c3e50;
    }
    
    .book-image {
        max-width: 100%;
        height: auto;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .book-image-container {
        text-align: center;
        margin-bottom: 20px;
    }
</style>

<div class="col-sm-9 mt-5">
    <div class="view-container">
        <h3 class="view-header">Book Purchase Details</h3>
        
        <div class="mb-4">
            <a href="adminBookPaymentStatus.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Book Purchases
            </a>
        </div>
        
        <!-- Purchase Summary -->
        <div class="card detail-card">
            <div class="card-body">
                <h5 class="card-title">Transaction Details</h5>
                <div class="row detail-row">
                    <div class="col-md-6">
                        <span class="detail-label">Purchase ID:</span> <?php echo htmlspecialchars($purchase['purchase_id']); ?>
                    </div>
                    <div class="col-md-6">
                        <span class="detail-label">Status:</span> 
                        <span class="status-badge <?php echo $status_class; ?>">
                            <?php echo ucfirst(htmlspecialchars($purchase['status'])); ?>
                        </span>
                    </div>
                </div>
                <div class="row detail-row">
                    <div class="col-md-6">
                        <span class="detail-label">Purchase Date:</span> <?php echo date('d/m/Y H:i', strtotime($purchase['purchase_date'])); ?>
                    </div>
                    <div class="col-md-6">
                        <span class="detail-label">Payment Method:</span> <?php echo ucfirst(htmlspecialchars($purchase['payment_method'])); ?>
                    </div>
                </div>
                <div class="row detail-row">
                    <div class="col-md-6">
                        <span class="detail-label">Amount:</span> Tsh <?php echo number_format($purchase['price'], 2); ?>
                    </div>
                    <div class="col-md-6">
                        <span class="detail-label">Response Message:</span> <?php echo htmlspecialchars($purchase['respomsg'] ?? 'N/A'); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Student Details -->
        <div class="card detail-card">
            <div class="card-body">
                <h5 class="card-title">Student Information</h5>
                <div class="row detail-row">
                    <div class="col-md-6">
                        <span class="detail-label">Student ID:</span> <?php echo htmlspecialchars($purchase['student_id']); ?>
                    </div>
                    <div class="col-md-6">
                        <span class="detail-label">Registration No:</span> <?php echo htmlspecialchars($purchase['studreg']); ?>
                    </div>
                </div>
                <div class="row detail-row">
                    <div class="col-md-6">
                        <span class="detail-label">Name:</span> <?php echo htmlspecialchars($purchase['studname']); ?>
                    </div>
                    <div class="col-md-6">
                        <span class="detail-label">Email:</span> <?php echo htmlspecialchars($purchase['stuemail']); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Book Details -->
        <div class="card detail-card">
            <div class="card-body">
                <h5 class="card-title">Book Information</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="book-image-container">
                            <?php if(!empty($purchase['image_path'])): ?>
                                <img src="../<?php echo htmlspecialchars($purchase['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($purchase['book_title']); ?>" 
                                     class="book-image">
                            <?php else: ?>
                                <i class="fas fa-book fa-5x" style="color: #6c757d;"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row detail-row">
                            <div class="col-12">
                                <span class="detail-label">Book ID:</span> <?php echo htmlspecialchars($purchase['book_id']); ?>
                            </div>
                        </div>
                        <div class="row detail-row">
                            <div class="col-12">
                                <span class="detail-label">Title:</span> <?php echo htmlspecialchars($purchase['book_title']); ?>
                            </div>
                        </div>
                        <div class="row detail-row">
                            <div class="col-12">
                                <span class="detail-label">Author:</span> <?php echo htmlspecialchars($purchase['author']); ?>
                            </div>
                        </div>
                        <div class="row detail-row">
                            <div class="col-12">
                                <span class="detail-label">Description:</span><br>
                                <?php echo nl2br(htmlspecialchars($purchase['description'])); ?>
                            </div>
                        </div>
                        <div class="row detail-row">
                            <div class="col-12">
                                <span class="detail-label">Price:</span> Tsh <?php echo number_format($purchase['price'], 2); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <a href="adminBookPaymentStatus.php" class="btn btn-secondary back-btn">
            <i class="fas fa-arrow-left me-2"></i>Back to Book Purchases
        </a>
    </div>
</div>

<?php
include("./admininclude/footer.php");
?>