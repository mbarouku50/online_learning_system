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

// Get counts from database
$course_count = 0;
$student_count = 0;
$sold_count = 0;

// Count total courses
$course_sql = "SELECT COUNT(*) AS count FROM course";
$course_result = $conn->query($course_sql);
if($course_result && $course_result->num_rows > 0) {
    $course_row = $course_result->fetch_assoc();
    $course_count = $course_row['count'];
}

// Count total students
$student_sql = "SELECT COUNT(*) AS count FROM student";
$student_result = $conn->query($student_sql);
if($student_result && $student_result->num_rows > 0) {
    $student_row = $student_result->fetch_assoc();
    $student_count = $student_row['count'];
}

// Count total sold courses
$sold_sql = "SELECT COUNT(*) AS count FROM courseorder";
$sold_result = $conn->query($sold_sql);
if($sold_result && $sold_result->num_rows > 0) {
    $sold_row = $sold_result->fetch_assoc();
    $sold_count = $sold_row['count'];
}

// Get recent orders
$order_sql = "SELECT * FROM courseorder ORDER BY order_date DESC LIMIT 5";
$order_result = $conn->query($order_sql);
?>
<style>
    .dashboard-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border: none;
        border-radius: 10px;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .courses-card {
        background: linear-gradient(45deg, #ff416c, #ff4b2b);
    }
    
    .students-card {
        background: linear-gradient(45deg, #11998e, #38ef7d);
    }
    
    .orders-card {
        background: linear-gradient(45deg, #4776E6, #8E54E9);
    }
    
    .card-header {
        border-bottom: none;
        background: rgba(255,255,255,0.1);
        font-weight: 600;
    }
    
    .card-title {
        font-size: 2.5rem;
        font-weight: 700;
    }
    
    .view-btn {
        background: rgba(255,255,255,0.2);
        border: none;
        border-radius: 50px;
        padding: 5px 20px;
        transition: all 0.3s;
    }
    
    .view-btn:hover {
        background: rgba(255,255,255,0.3);
    }
    
    .recent-orders {
        background: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .orders-header {
        background: #4e73df;
        color: white;
        border-radius: 5px 5px 0 0;
    }
    
    .table th {
        border-top: none;
    }
    
    .action-btn {
        transition: all 0.3s;
    }
    
    .action-btn:hover {
        transform: scale(1.1);
    }
</style>

<div class="col-sm-9 mt-5">
    <!-- Summary Cards -->
    <div class="row mx-1">
        <div class="col-md-4 mt-4">
            <div class="card text-white dashboard-card courses-card mb-3">
                <div class="card-header">Total Courses</div>
                <div class="card-body text-center">
                    <h4 class="card-title"><?php echo $course_count; ?></h4>
                    <a class="btn text-white view-btn" href="courses.php">
                        <i class="fas fa-book-open mr-2"></i>View Courses
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-4">
            <div class="card text-white dashboard-card students-card mb-3">
                <div class="card-header">Total Students</div>
                <div class="card-body text-center">
                    <h4 class="card-title"><?php echo $student_count; ?></h4>
                    <a class="btn text-white view-btn" href="student.php">
                        <i class="fas fa-users mr-2"></i>View Students
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-4">
            <div class="card text-white dashboard-card orders-card mb-3">
                <div class="card-header">Courses Sold</div>
                <div class="card-body text-center">
                    <h4 class="card-title"><?php echo $sold_count; ?></h4>
                    <a class="btn text-white view-btn" href="adminPaymentStatus.php">
                        <i class="fas fa-shopping-cart mr-2"></i>View Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Orders Table -->
    <div class="mx-1 mt-5 recent-orders">
        <div class="orders-header p-2">
            <h5 class="mb-0">Recent Orders</h5>
        </div>
        <div class="table-responsive p-3">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Order ID</th>
                        <th scope="col">Course</th>
                        <th scope="col">Student Email</th>
                        <th scope="col">Date</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($order_result && $order_result->num_rows > 0) {
                        while($row = $order_result->fetch_assoc()) {
                            // Get course name
                            $course_name = "N/A";
                            $course_sql = "SELECT course_name FROM course WHERE course_id = ".$row['course_id'];
                            $course_name_result = $conn->query($course_sql);
                            if($course_name_result && $course_name_result->num_rows > 0) {
                                $course_row = $course_name_result->fetch_assoc();
                                $course_name = $course_row['course_name'];
                            }
                            
                            // Status badge
                            $status_class = '';
                            switch(strtolower($row['status'])) {
                                case 'completed':
                                    $status_class = 'badge-success';
                                    break;
                                case 'pending':
                                    $status_class = 'badge-warning';
                                    break;
                                case 'failed':
                                    $status_class = 'badge-danger';
                                    break;
                                default:
                                    $status_class = 'badge-secondary';
                            }
                            
                            echo '<tr>
                                    <td>'.$row['order_id'].'</td>
                                    <td>'.htmlspecialchars($course_name).'</td>
                                    <td>'.htmlspecialchars($row['stuemail']).'</td>
                                    <td>'.date('d M Y', strtotime($row['order_date'])).'</td>
                                    <td>Tsh '.number_format($row['course_price'], 2).'</td>
                                    <td><span class="badge '.$status_class.'">'.ucfirst($row['status']).'</span></td>
                                    <td>
                                        <a href="adminViewDetails.php?order_id='.$row['order_id'].'" class="btn btn-sm btn-info action-btn" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger action-btn" title="Delete">
                                            <i class="far fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>';
                        }
                    } else {
                        echo '<tr>
                                <td colspan="7" class="text-center py-4">No recent orders found</td>
                              </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include("./admininclude/footer.php");
?>