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
?>

<style>
    .payment-container {
        padding: 20px;
    }
    
    .payment-header {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 30px;
        padding-bottom: 10px;
        border-bottom: 2px solid #4e73df;
    }
    
    .filter-section {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    
    .payment-table {
        background-color: white;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .payment-table th {
        background-color: #4e73df;
        color: white;
    }
    
    .status-pending {
        color: #e74a3b;
        font-weight: 600;
    }
    
    .status-completed {
        color: #1cc88a;
        font-weight: 600;
    }
    
    .status-failed {
        color: #858796;
        font-weight: 600;
    }
    
    .action-btn {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.9rem;
    }
    
    .pagination {
        justify-content: center;
    }
    
    .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    
    .page-link {
        color: #4e73df;
    }
</style>

<div class="col-sm-9 mt-5">
    <div class="payment-container">
        <h3 class="payment-header">Book Purchase Payment Status</h3>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="status-filter" class="form-label">Status</label>
                        <select class="form-select" id="status-filter" name="status">
                            <option value="">All</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date-from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date-from" name="date_from">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date-to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date-to" name="date_to">
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <button type="reset" class="btn btn-outline-secondary ms-2">Reset</button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Payment Status Table -->
        <div class="table-responsive payment-table">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Purchase ID</th>
                        <th>Book ID</th>
                        <th>Student ID</th>
                        <th>Amount</th>
                        <th>Purchase Date</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Build query based on filters
                    $where = [];
                    $params = [];
                    $types = '';
                    
                    if(isset($_GET['status']) && !empty($_GET['status'])) {
                        $where[] = "status = ?";
                        $params[] = $_GET['status'];
                        $types .= 's';
                    }
                    
                    if(isset($_GET['date_from']) && !empty($_GET['date_from'])) {
                        $where[] = "purchase_date >= ?";
                        $params[] = $_GET['date_from'];
                        $types .= 's';
                    }
                    
                    if(isset($_GET['date_to']) && !empty($_GET['date_to'])) {
                        $where[] = "purchase_date <= ?";
                        $params[] = $_GET['date_to'];
                        $types .= 's';
                    }
                    
                    $where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';
                    
                    $sql = "SELECT bp.*, b.book_title, s.studname 
                            FROM book_purchases bp
                            JOIN books b ON bp.book_id = b.id
                            JOIN student s ON bp.student_id = s.stud_id
                            $where_clause 
                            ORDER BY purchase_date DESC LIMIT 15";
                    $stmt = $conn->prepare($sql);
                    
                    if(count($params) > 0) {
                        $stmt->bind_param($types, ...$params);
                    }
                    
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $status_class = '';
                            switch($row['status']) {
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
                            
                            echo '<tr>
                                    <td>'.$row['purchase_id'].'</td>
                                    <td>'.$row['book_id'].' ('.htmlspecialchars($row['book_title']).')</td>
                                    <td>'.$row['student_id'].' ('.htmlspecialchars($row['studname']).')</td>
                                    <td>Tsh '.number_format($row['price'], 2).'</td>
                                    <td>'.date('d/m/Y', strtotime($row['purchase_date'])).'</td>
                                    <td>'.ucfirst($row['payment_method']).'</td>
                                    <td class="'.$status_class.'">'.ucfirst($row['status']).'</td>
                                    <td>
                                        <a href="adminViewBookPurchase.php?purchase_id='.$row['purchase_id'].'" class="btn btn-sm btn-info action-btn" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="adminUpdateBookStatus.php?purchase_id='.$row['purchase_id'].'" class="btn btn-sm btn-warning action-btn" title="Update Status">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>';
                        }
                    } else {
                        echo '<tr>
                                <td colspan="8" class="text-center py-4">No book purchase records found</td>
                              </tr>';
                    }
                    $stmt->close();
                    ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination mt-4">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php
include("./admininclude/footer.php");
?>