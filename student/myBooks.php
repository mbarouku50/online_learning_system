<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION)) {
    session_start();
}

include('./stuInclude/header.php');
include_once(__DIR__ . '/../dbconnection.php');

if (!isset($_SESSION['is_login'])) {
    echo "<script>location.href='../index.php';</script>";
    exit;
}

$stuemail = $_SESSION['stuemail'] ?? '';
$stud_id = $_SESSION['stud_id'] ?? 0;
?>

<style>
    .books-container {
        padding: 20px;
        background-color: #f8f9fa;
    }
    
    .books-header {
        margin-bottom: 30px;
        color: #2c3e50;
        font-weight: 600;
        border-bottom: 2px solid #4e73df;
        padding-bottom: 10px;
    }
    
    .book-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .book-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
    
    .book-img-container {
        height: 300px;
        overflow: hidden;
    }
    
    .book-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }
    
    .book-card:hover .book-img {
        transform: scale(1.05);
    }
    
    .book-body {
        padding: 20px;
    }
    
    .book-title {
        font-size: 1.4rem;
        color: #2c3e50;
        margin-bottom: 15px;
    }
    
    .book-author {
        color: #6c757d;
        margin-bottom: 10px;
        font-style: italic;
    }
    
    .book-desc {
        color: #6c757d;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .book-meta {
        margin-bottom: 15px;
    }
    
    .book-price {
        font-weight: 600;
        margin-bottom: 15px;
        font-size: 1.2rem;
        color: #28a745;
    }
    
    .read-btn {
        display: inline-block;
        background-color: #4e73df;
        color: white;
        padding: 8px 20px;
        border-radius: 4px;
        text-decoration: none;
        transition: background-color 0.3s;
    }
    
    .read-btn:hover {
        background-color: #2e59d9;
        color: white;
    }
    
    .no-books {
        text-align: center;
        padding: 40px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .no-books i {
        font-size: 3rem;
        color: #6c757d;
        margin-bottom: 20px;
    }
    
    .no-books p {
        font-size: 1.2rem;
        color: #6c757d;
    }
    
    .error-message {
        color: #e74a3b;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
</style>

<div class="books-container">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4 class="books-header">My Books</h4>
                
                <?php
                if (empty($stud_id)) {
                    echo '<div class="error-message">Student ID not found in session.</div>';
                } else {
                    $sql = "SELECT bp.purchase_id, b.id AS book_id, b.book_title, b.author, b.description,
                            b.price, b.image_path, b.department, bp.purchase_date, bp.status
                            FROM book_purchases AS bp
                            JOIN books AS b ON b.id = bp.book_id
                            WHERE bp.student_id = ? AND bp.status = 'completed'";
                    
                    $stmt = $conn->prepare($sql);
                    
                    if ($stmt) {
                        $stmt->bind_param("i", $stud_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                ?>
                                <div class="book-card">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="book-img-container">
                                                <?php
                                                $image_url = !empty($row['image_path']) ? "../" . $row['image_path'] : '';
                                                if (!empty($image_url) && file_exists(__DIR__ . '/../' . $row['image_path'])): ?>
                                                    <img src="<?php echo htmlspecialchars($image_url); ?>" 
                                                         alt="<?php echo htmlspecialchars($row['book_title']); ?>" 
                                                         class="book-img">
                                                <?php else: ?>
                                                    <div class="book-img-container" style="background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-book fa-5x" style="color: #6c757d;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="book-body">
                                                <h5 class="book-title"><?php echo htmlspecialchars($row['book_title']); ?></h5>
                                                <p class="book-author">by <?php echo htmlspecialchars($row['author']); ?></p>
                                                <p class="book-desc"><?php echo htmlspecialchars($row['description']); ?></p>
                                                
                                                <div class="book-meta">
                                                    <small><strong>Department:</strong> <?php echo htmlspecialchars(str_replace('_', ' ', $row['department'])); ?></small>
                                                    <small><strong>Purchased on:</strong> <?php echo date('F j, Y', strtotime($row['purchase_date'])); ?></small>
                                                </div>
                                                
                                                <div class="book-price">
                                                    Tsh <?php echo number_format($row['price'], 2); ?>
                                                </div>
                                                
                                                <a href="readbook.php?book_id=<?php echo $row['book_id']; ?>" class="read-btn">
                                                    <i class="fas fa-book-open mr-2"></i> Read Book
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="no-books">
                                    <i class="fas fa-book-open"></i>
                                    <p>You haven\'t purchased any books yet or your purchases are still pending.</p>
                                  </div>';
                        }
                        
                        $stmt->close();
                    } else {
                        echo '<div class="error-message">Database query preparation failed: ' . htmlspecialchars($conn->error) . '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
include('./stuInclude/footer.php');
?>