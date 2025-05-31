<?php
if (!isset($_SESSION)) {
    session_start();
}

include("./admininclude/header.php");
include("../dbconnection.php");

// Check database connection
if (!$conn) {
    die("<script>alert('Database connection failed: " . mysqli_connect_error() . "'); location.href='../index.php';</script>");
}

// Admin authentication
if (!isset($_SESSION['is_admin_login'])) {
    echo "<script> location.href='../index.php'; </script>";
    exit;
}

// Function to get proper image URL
function getImageUrl($dbPath) {
    if (empty($dbPath)) return false;
    
    // Remove any leading/trailing slashes or dots
    $cleanPath = trim($dbPath, '/.');
    
    // Check if path already contains Uploads/Books
    if (strpos($cleanPath, 'Uploads/Books/') === false) {
        $cleanPath = 'Uploads/Books/' . $cleanPath;
    }
    
    // Convert to web path
    return '/online_learning_system/' . $cleanPath;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books Management</title>
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
    .book-card { transition: transform 0.2s; margin-bottom: 1.5rem; }
        .book-img { height: 200px; object-fit: cover; }
        .broken-image { 
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 200px;
            color: #6c757d;
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
            <div class="department-filter mb-3">
                <select class="form-control" id="departmentFilter">
                    <option value="all">All Departments</option>
                    <option value="ICT">ICT</option>
                    <option value="Metrology">Metrology</option>
                    <option value="Business">Business Studies</option>
                </select>
            </div>
            
            <!-- Action Buttons -->
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
                
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $badgeColor = [
                            'ICT' => 'primary',
                            'Metrology' => 'info',
                            'Business' => 'success'
                        ][$row['department'] ?? 'secondary'];
                        
                        $imageUrl = getImageUrl($row['image_path']);
                        $imageExists = $imageUrl && file_exists($_SERVER['DOCUMENT_ROOT'] . $imageUrl);
                        ?>
                        <div class="col-md-4 mb-4 book-item" data-department="<?= htmlspecialchars($row['department']) ?>">
                            <div class="card book-card h-100">
                                <?php if ($imageUrl): ?>
                                    <?php if ($imageExists): ?>
                                        <img src="<?= $imageUrl ?>" class="card-img-top book-img" alt="Book Cover">
                                    <?php else: ?>
                                        <div class="broken-image">
                                            <div class="text-center">
                                                <i class="fas fa-image fa-3x mb-2"></i>
                                                <p>Image not found</p>
                                                <small><?= htmlspecialchars(basename($row['image_path'])) ?></small>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="broken-image">
                                        <i class="fas fa-book-open fa-3x"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($row['book_title']) ?></h5>
                                    <p class="card-text">Author: <?= htmlspecialchars($row['author']) ?></p>
                                    <p class="card-text">ISBN: <?= !empty($row['isbn']) ? htmlspecialchars($row['isbn']) : 'N/A' ?></p>
                                    <?php if (!empty($row['course_name'])): ?>
                                        <p class="card-text">Course: <?= htmlspecialchars($row['course_name']) ?></p>
                                    <?php endif; ?>
                                    <p class="price-tag">Tsh <?= number_format($row['price'], 0) ?></p>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="viewbook.php?book_id=<?= $row['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye mr-1"></i> View/Edit
                                        </a>
                                        <span class="badge badge-<?= $badgeColor ?>">
                                            <?= str_replace('_', ' ', $row['department']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="col-12 empty-state">
                            <i class="fas fa-book-open fa-3x mb-3"></i>
                            <h4>No Books Found</h4>
                            <p>There are currently no books in the library.</p>
                            <a href="addbook.php" class="btn btn-primary mt-2">
                                <i class="fas fa-plus-circle mr-2"></i>Add First Book
                            </a>
                          </div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function() {
    // Department filter
    $('#departmentFilter').change(function() {
        const department = $(this).val();
        $('.book-item').hide();
        if (department === 'all') {
            $('.book-item').show();
        } else {
            $(`.book-item[data-department="${department}"]`).show();
        }
    });
    
    // Refresh button
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