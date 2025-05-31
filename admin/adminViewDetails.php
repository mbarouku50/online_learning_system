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

// Fetch student details
$student_query = $conn->prepare("SELECT * FROM student WHERE stuemail = ?");
$student_query->bind_param("s", $order['stuemail']);
$student_query->execute();
$student_result = $student_query->get_result();
$student = $student_result->fetch_assoc();
$student_query->close();

// Fetch course details
$course_query = $conn->prepare("SELECT * FROM course WHERE course_id = ?");
$course_query->bind_param("i", $order['course_id']);
$course_query->execute();
$course_result = $course_query->get_result();
$course = $course_result->fetch_assoc();
$course_query->close();
?>

<style>
    .details-container {
        padding: 30px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .details-header {
        color: #2c3e50;
        border-bottom: 2px solid #4e73df;
        padding-bottom: 10px;
        margin-bottom: 30px;
    }
    
    .details-section {
        margin-bottom: 30px;
    }
    
    .section-title {
        color: #4e73df;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 5px;
        border-bottom: 1px solid #eee;
    }
    
    .detail-row {
        display: flex;
        margin-bottom: 15px;
    }
    
    .detail-label {
        font-weight: 600;
        width: 200px;
        color: #6c757d;
    }
    
    .detail-value {
        flex: 1;
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
    
    .student-photo {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e3e6f0;
        margin-bottom: 20px;
    }
    
    .course-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    
    /* Print-specific styles */
    .print-button {
        margin-left: 10px;
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            background-color: white;
            color: black;
            font-size: 12pt;
        }
        
        .details-container {
            box-shadow: none;
            padding: 0;
            margin: 0;
        }
        
        .student-photo, .course-image {
            max-width: 100px;
            max-height: 100px;
        }
        
        .detail-row {
            page-break-inside: avoid;
        }
        
        .section-title {
            page-break-after: avoid;
        }
    }
</style>

<div class="col-sm-9 mt-5">
    <div class="details-container">
        <h3 class="details-header">Order Details</h3>
        
        <div class="text-end mb-4 no-print">
            <a href="adminPaymentStatus.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Payments
            </a>
            <button onclick="printDetails()" class="btn btn-primary print-button">
                <i class="fas fa-print me-2"></i>Print Details
            </button>
        </div>
        
        <!-- Student Information Section -->
        <div class="details-section">
            <h4 class="section-title">Student Information</h4>
            
            <div class="text-center mb-4">
                <?php if(!empty($student['stu_img'])): ?>
                    <img src="<?php echo htmlspecialchars($student['stu_img']); ?>" class="student-photo" alt="Student Photo">
                <?php else: ?>
                    <div class="student-photo bg-light d-flex align-items-center justify-content-center">
                        <i class="fas fa-user fa-4x text-muted"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-row">
                        <div class="detail-label">Full Name:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($student['studname'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Email:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($order['stuemail']); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Registration No:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($student['studreg'] ?? 'N/A'); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-row">
                        <div class="detail-label">Occupation:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($student['stu_occ'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Phone:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($student['stu_phone'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Address:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($student['stu_address'] ?? 'N/A'); ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Information Section -->
        <div class="details-section">
            <h4 class="section-title">Order Information</h4>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-row">
                        <div class="detail-label">Order ID:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($order['order_id']); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Order Date:</div>
                        <div class="detail-value"><?php echo date('F j, Y', strtotime($order['order_date'])); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Status:</div>
                        <div class="detail-value">
                            <span class="status-badge status-<?php echo htmlspecialchars($order['status']); ?>">
                                <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-row">
                        <div class="detail-label">Amount Paid:</div>
                        <div class="detail-value">Tsh <?php echo number_format($order['course_price'], 2); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Payment Response:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($order['respomsg']); ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Course Information Section -->
        <div class="details-section">
            <h4 class="section-title">Course Information</h4>
            
            <div class="row">
                <div class="col-md-4">
                    <?php if(!empty($course['course_img'])): ?>
                        <img src="<?php echo htmlspecialchars($course['course_img']); ?>" class="course-image" alt="Course Image">
                    <?php else: ?>
                        <div class="course-image bg-light d-flex align-items-center justify-content-center">
                            <i class="fas fa-book fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <div class="detail-row">
                        <div class="detail-label">Course ID:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($course['course_id'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Course Name:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($course['course_name'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Author:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($course['course_author'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Duration:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($course['course_duration'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Description:</div>
                        <div class="detail-value"><?php echo htmlspecialchars($course['course_desc'] ?? 'N/A'); ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center back-btn no-print">
            <a href="adminPaymentStatus.php" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Payments
            </a>
        </div>
    </div>
</div>

<script>
    // Function to handle printing with better formatting
    function printDetails() {
        // Store original title
        const originalTitle = document.title;
        
        // Set print title
        document.title = "Order Details - " + document.querySelector('.detail-value').textContent;
        
        // Print the document
        window.print();
        
        // Restore original title
        document.title = originalTitle;
    }
</script>

<?php
include("./admininclude/footer.php");
?>