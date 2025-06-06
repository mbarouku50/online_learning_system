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
$course_id = $_GET['course_id'] ?? 0;

if (empty($course_id)) {
    echo "<script>location.href='myCourse.php';</script>";
    exit;
}
?>

<style>
    .content-container {
        padding: 20px;
        background-color: #f8f9fa;
        min-height: calc(100vh - 150px);
    }
    
    .page-header {
        margin-bottom: 30px;
        color: #2c3e50;
        font-weight: 600;
        border-bottom: 2px solid #e74a3b;
        padding-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .back-btn {
        background-color: #4e73df;
        color: white;
        padding: 8px 15px;
        border-radius: 4px;
        text-decoration: none;
        transition: background-color 0.3s;
    }
    
    .back-btn:hover {
        background-color: #2e59d9;
        color: white;
    }
    
    .exam-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        padding: 20px;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .exam-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
    
    .exam-title {
        font-size: 1.2rem;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .exam-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        color: #6c757d;
    }
    
    .meta-item i {
        margin-right: 5px;
    }
    
    .exam-desc {
        color: #6c757d;
        margin-bottom: 15px;
    }
    
    .no-exams {
        text-align: center;
        padding: 40px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .no-exams i {
        font-size: 3rem;
        color: #6c757d;
        margin-bottom: 20px;
    }
    
    .no-exams p {
        font-size: 1.2rem;
        color: #6c757d;
    }
    
    .take-exam-btn {
        background-color: #e74a3b;
        color: white;
        padding: 8px 20px;
        border-radius: 4px;
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.3s;
    }
    
    .take-exam-btn:hover {
        background-color: #be2617;
        color: white;
    }
    
    .view-results-btn {
        background-color: #4e73df;
        color: white;
        padding: 8px 20px;
        border-radius: 4px;
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.3s;
    }
    
    .view-results-btn:hover {
        background-color: #2e59d9;
        color: white;
    }
    
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
    }
    
    .status-available {
        background-color: #d1fae5;
        color: #1cc88a;
    }
    
    .status-completed {
        background-color: #e0e7ff;
        color: #4e73df;
    }
    
    .status-expired {
        background-color: #f8e3da;
        color: #e74a3b;
    }
</style>

<div class="content-container">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4 class="page-header">
                    Course Exams
                    <a href="myCourse.php" class="back-btn">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Courses
                    </a>
                </h4>
                
                <?php
                // Fetch course name
                $course_sql = "SELECT course_name FROM course WHERE course_id = ?";
                $course_stmt = $conn->prepare($course_sql);
                $course_stmt->bind_param("i", $course_id);
                $course_stmt->execute();
                $course_result = $course_stmt->get_result();
                $course_name = $course_result->fetch_assoc()['course_name'] ?? 'Unknown Course';
                $course_stmt->close();
                
                echo "<h5>Course: $course_name</h5>";
                
                // Fetch exams
                $sql = "SELECT e.exam_id, e.title, e.description, e.available_from, 
                        e.available_to, e.time_limit, e.max_marks, 
                        a.attempt_id, a.score, a.completed_at
                        FROM exams e
                        LEFT JOIN exam_attempts a ON e.exam_id = a.exam_id 
                        AND a.student_email = ?
                        WHERE e.course_id = ?
                        ORDER BY e.available_from DESC";
                
                $stmt = $conn->prepare($sql);
                
                if ($stmt) {
                    $stmt->bind_param("si", $stuemail, $course_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $current_time = time();
                            $available_from = strtotime($row['available_from']);
                            $available_to = strtotime($row['available_to']);
                            
                            $status = '';
                            $status_class = '';
                            $action_btn = '';
                            
                            if ($row['attempt_id']) {
                                $status = 'Completed';
                                $status_class = 'status-completed';
                                $action_btn = '<a href="exam_results.php?attempt_id='.$row['attempt_id'].'" class="view-results-btn">
                                                <i class="fas fa-chart-bar mr-2"></i>View Results
                                              </a>';
                            } elseif ($current_time < $available_from) {
                                $status = 'Not Available Yet';
                                $status_class = 'status-expired';
                            } elseif ($current_time > $available_to) {
                                $status = 'Expired';
                                $status_class = 'status-expired';
                            } else {
                                $status = 'Available';
                                $status_class = 'status-available';
                                $action_btn = '<a href="take_exam.php?exam_id='.$row['exam_id'].'" class="take-exam-btn">
                                                <i class="fas fa-pencil-alt mr-2"></i>Take Exam
                                              </a>';
                            }
                            
                            $available_from_formatted = date('F j, Y, g:i a', $available_from);
                            $available_to_formatted = date('F j, Y, g:i a', $available_to);
                            $time_limit = $row['time_limit'] > 0 ? $row['time_limit'].' minutes' : 'No time limit';
                            
                            echo '<div class="exam-card">
                                    <h5 class="exam-title">'.$row['title'].'</h5>
                                    <div class="exam-meta">
                                        <div class="meta-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>Available: '.$available_from_formatted.' to '.$available_to_formatted.'</span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-clock"></i>
                                            <span>Time Limit: '.$time_limit.'</span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-star"></i>
                                            <span>Max Marks: '.$row['max_marks'].'</span>
                                        </div>
                                        <div class="meta-item">
                                            <span class="status-badge '.$status_class.'">'.$status.'</span>
                                        </div>';
                                        
                            if ($row['score'] !== null) {
                                echo '<div class="meta-item">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Score: '.$row['score'].'/'.$row['max_marks'].'</span>
                                      </div>';
                            }
                            
                            echo '</div>
                                  <p class="exam-desc">'.$row['description'].'</p>
                                  <div>'.$action_btn.'</div>
                                </div>';
                        }
                    } else {
                        echo '<div class="no-exams">
                                <i class="fas fa-file-alt"></i>
                                <p>No exams found for this course.</p>
                              </div>';
                    }
                    
                    $stmt->close();
                } else {
                    echo '<div class="alert alert-danger">Database query preparation failed.</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
include('./stuInclude/footer.php');
?>