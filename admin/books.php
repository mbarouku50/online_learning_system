<?php
if (!isset($_SESSION)) {
    session_start();
}

include("./admininclude/header.php");
include("../dbconnection.php");

// Check if database connection is successful
if (!$conn) {
    die("<script>alert('Database connection failed: " . mysqli_connect_error() . "'); location.href='../index.php';</script>");
}

if (isset($_SESSION['is_admin_login'])) {
    $adminEmail = $_SESSION['admin_email'];
} else {
    echo "<script> location.href='../index.php'; </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .book-card {
            transition: transform 0.2s;
            margin-bottom: 1.5rem;
            height: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .book-img {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .action-buttons .btn {
            border-radius: 5px;
            font-weight: 500;
        }
        .badge-department {
            font-size: 0.75rem;
            padding: 5px 10px;
            border-radius: 50px;
        }
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
        }
        .card-text {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        .price-tag {
            font-weight: 600;
            color: #27ae60;
            font-size: 1rem;
            margin-top: 0.5rem;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #bdc3c7;
        }
        .card-details {
            margin-bottom: 10px;
        }
        .department-filter {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="col-sm-9 mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="page-header">
                <h3 class="page-title">Books Management</h3>
                <p class="text-muted">Manage all books in the library system</p>
            </div>
            
            <!-- Department Filter -->
            <div class="department-filter">
                <select class="form-control" id="departmentFilter">
                    <option value="all">All Departments</option>
                    <option value="ICT">ICT</option>
                    <option value="Metrology">Metrology</option>
                    <option value="Business">Business Studies</option>
                    <option value="Procurement">Procurement</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Accountancy">Accountancy</option>
                    <option value="Business_Admin">Business Administration</option>
                </select>
            </div>
            
            <!-- Add Book Button -->
            <div class="action-buttons mb-4">
                <a href="addbook.php" class="btn btn-primary">
                    <i class="fas fa-plus-circle mr-2"></i>Add New Book
                </a>
                <button class="btn btn-outline-secondary ml-2" id="refreshBtn">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
            </div>
            
            <!-- Books List -->
            <div class="row" id="booksContainer">
                <?php
                $sql = "SELECT b.*, c.course_name FROM books b LEFT JOIN course c ON b.course_id = c.course_id";
                $result = $conn->query($sql);
                $hasBooks = false;
                
                if ($result && $result->num_rows > 0) {
                    $hasBooks = true;
                    while ($row = $result->fetch_assoc()) {
                        $badgeColor = [
                            'ICT' => 'primary',
                            'Metrology' => 'info',
                            'Business' => 'success',
                            'Procurement' => 'warning',
                            'Marketing' => 'danger',
                            'Accountancy' => 'secondary',
                            'Business_Admin' => 'dark'
                        ][$row['department']];
                        ?>
                        <div class="col-md-4 mb-4 book-item" data-department="<?php echo htmlspecialchars($row['department']); ?>">
                            <div class="card book-card h-100">
                                <?php if (!empty($row['image_path'])): ?>
                                    <img src="../<?php echo htmlspecialchars($row['image_path']); ?>" class="card-img-top book-img" alt="Book Cover">
                                <?php else: ?>
                                    <div class="book-img bg-light d-flex align-items-center justify-content-center">
                                        <i class="fas fa-book-open fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($row['book_title']); ?></h5>
                                    <div class="card-details">
                                        <p class="card-text mb-1"><small class="text-muted">Author: <?php echo htmlspecialchars($row['author']); ?></small></p>
                                        <p class="card-text mb-1"><small class="text-muted">ISBN: <?php echo !empty($row['isbn']) ? htmlspecialchars($row['isbn']) : 'N/A'; ?></small></p>
                                        <?php if (!empty($row['course_name'])): ?>
                                            <p class="card-text mb-1"><small class="text-muted">Course: <?php echo htmlspecialchars($row['course_name']); ?></small></p>
                                        <?php endif; ?>
                                        <p class="card-text price-tag">Tsh <?php echo number_format($row['price'], 0); ?></p>
                                    </div>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="viewbook.php?book_id=<?php echo htmlspecialchars($row['id']); ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye mr-1"></i> View/Edit
                                            </a>
                                            <span class="badge badge-<?php echo $badgeColor; ?> badge-department">
                                                <?php echo str_replace('_', ' ', $row['department']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                
                if (!$hasBooks) {
                    echo '<div class="col-12 empty-state">
                            <i class="fas fa-book-open"></i>
                            <h4>No Books Found</h4>
                            <p>There are currently no books in the library. Add some books to get started.</p>
                            <a href="addbook.php" class="btn btn-primary">
                                <i class="fas fa-plus-circle mr-2"></i>Add First Book
                            </a>
                          </div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Department filter functionality
        $('#departmentFilter').change(function() {
            const department = $(this).val();
            
            if (department === 'all') {
                $('.book-item').show();
            } else {
                $('.book-item').hide();
                $(`.book-item[data-department="${department}"]`).show();
            }
        });
        
        // Refresh button functionality
        $('#refreshBtn').click(function() {
            location.reload();
        });
    });
</script>

<?php
include("./admininclude/footer.php");
$conn->close();
?>
</body>
</html>