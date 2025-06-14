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
?>

<style>
    .course-container {
        padding: 20px;
        background-color: #f8f9fa;
    }
    
    .course-header {
        margin-bottom: 30px;
        color: #2c3e50;
        font-weight: 600;
        border-bottom: 2px solid #4e73df;
        padding-bottom: 10px;
    }
    
    .course-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
    
    .course-img-container {
        height: 200px;
        overflow: hidden;
    }
    
    .course-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }
    
    .course-card:hover .course-img {
        transform: scale(1.05);
    }
    
    .course-body {
        padding: 20px;
    }
    
    .course-title {
        font-size: 1.4rem;
        color: #2c3e50;
        margin-bottom: 15px;
    }
    
    .course-desc {
        color: #6c757d;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .course-meta {
        margin-bottom: 15px;
    }
    
    .course-meta small {
        display: block;
        color: #6c757d;
        margin-bottom: 5px;
    }
    
    .course-price {
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .original-price {
        text-decoration: line-through;
        color: #6c757d;
        margin-right: 10px;
    }
    
    .current-price {
        color: #e74a3b;
        font-size: 1.2rem;
    }
    
    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }
    
    .btn {
        display: inline-block;
        padding: 8px 20px;
        border-radius: 4px;
        text-decoration: none;
        transition: all 0.3s;
        font-weight: 500;
        border: none;
    }
    
    .watch-btn {
        background-color: #4e73df;
        color: white;
    }
    
    .watch-btn:hover {
        background-color: #2e59d9;
        color: white;
    }
    
    .assignment-btn {
        background-color: #1cc88a;
        color: white;
    }
    
    .assignment-btn:hover {
        background-color: #17a673;
        color: white;
    }
    
    .quiz-btn {
        background-color: #f6c23e;
        color: #000;
    }
    
    .quiz-btn:hover {
        background-color: #dda20a;
        color: #000;
    }
    
    .exam-btn {
        background-color: #e74a3b;
        color: white;
    }
    
    .exam-btn:hover {
        background-color: #be2617;
        color: white;
    }
    
    .pending-btn {
        display: inline-block;
        background-color: #f6c23e;
        color: #000;
        padding: 8px 20px;
        border-radius: 4px;
        text-decoration: none;
    }
    
    .no-courses {
        text-align: center;
        padding: 40px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .no-courses i {
        font-size: 3rem;
        color: #6c757d;
        margin-bottom: 20px;
    }
    
    .no-courses p {
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
    
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
        margin-bottom: 15px;
    }
    
    .status-completed {
        background-color: #d1fae5;
        color: #1cc88a;
    }
    
    .status-pending {
        background-color: #f8e3da;
        color: #e74a3b;
    }
</style>

<div class="course-container">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4 class="course-header">My Courses</h4>
                
                <?php
                if (empty($stuemail)) {
                    echo '<div class="error-message">Student email not found in session.</div>';
                } else {
                    $sql = "SELECT co.order_id, c.course_id, c.course_name, c.course_duration, c.course_desc,
                            c.course_img, c.course_author, c.course_original_price, c.course_price, co.status
                            FROM courseorder AS co 
                            JOIN course AS c ON c.course_id = co.course_id 
                            WHERE co.stuemail = ?";
                    
                    $stmt = $conn->prepare($sql);
                    
                    if ($stmt) {
                        $stmt->bind_param("s", $stuemail);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $status_class = ($row['status'] == 'completed') ? 'status-completed' : 'status-pending';
                                ?>
                                <div class="course-card">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="course-img-container">
                                                <img src="<?php echo htmlspecialchars($row['course_img']); ?>" alt="<?php echo htmlspecialchars($row['course_name']); ?>" class="course-img">
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="course-body">
                                                <span class="status-badge <?php echo $status_class; ?>">
                                                    <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                                                </span>
                                                
                                                <h5 class="course-title"><?php echo htmlspecialchars($row['course_name']); ?></h5>
                                                <p class="course-desc"><?php echo htmlspecialchars($row['course_desc']); ?></p>
                                                
                                                <div class="course-meta">
                                                    <small><strong>Duration:</strong> <?php echo htmlspecialchars($row['course_duration']); ?></small>
                                                    <small><strong>Instructor:</strong> <?php echo htmlspecialchars($row['course_author']); ?></small>
                                                </div>
                                                
                                                <div class="course-price">
                                                    <span class="original-price">Tsh <?php echo number_format($row['course_original_price']); ?></span>
                                                    <span class="current-price">Tsh <?php echo number_format($row['course_price']); ?></span>
                                                </div>
                                                
                                                <?php if ($row['status'] == 'completed'): ?>
                                                    <div class="action-buttons">
                                                        <a href="watchcourse.php?course_id=<?php echo $row['course_id']; ?>" class="btn watch-btn">
                                                            <i class="fas fa-play-circle mr-2"></i> Watch Course
                                                        </a>
                                                        <a href="assignments.php?course_id=<?php echo $row['course_id']; ?>" class="btn assignment-btn">
                                                            <i class="fas fa-tasks mr-2"></i> Assignments
                                                        </a>
                                                        <a href="quizzes.php?course_id=<?php echo $row['course_id']; ?>" class="btn quiz-btn">
                                                            <i class="fas fa-question-circle mr-2"></i> Quizzes
                                                        </a>
                                                        <a href="exams.php?course_id=<?php echo $row['course_id']; ?>" class="btn exam-btn">
                                                            <i class="fas fa-file-alt mr-2"></i> Exams
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="pending-btn">
                                                        <i class="fas fa-clock mr-2"></i> Pending Approval
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="no-courses">
                                    <i class="fas fa-book-open"></i>
                                    <p>You haven\'t enrolled in any courses yet.</p>
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