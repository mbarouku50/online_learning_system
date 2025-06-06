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

// Fetch student ID
$stu_sql = "SELECT stud_id FROM student WHERE stuemail = ?";
$stu_stmt = $conn->prepare($stu_sql);
$stu_stmt->bind_param("s", $stuemail);
$stu_stmt->execute();
$stu_result = $stu_stmt->get_result();
$stu_row = $stu_result->fetch_assoc();
$stu_id = $stu_row['stu_id'] ?? 0;
$stu_stmt->close();
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
        border-bottom: 2px solid #f6c23e;
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
    
    .quiz-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        padding: 20px;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .quiz-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
    
    .quiz-title {
        font-size: 1.2rem;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .quiz-meta {
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
    
    .quiz-desc {
        color: #6c757d;
        margin-bottom: 15px;
    }
    
    .no-quizzes {
        text-align: center;
        padding: 40px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .no-quizzes i {
        font-size: 3rem;
        color: #6c757d;
        margin-bottom: 20px;
    }
    
    .no-quizzes p {
        font-size: 1.2rem;
        color: #6c757d;
    }
    
    .take-quiz-btn {
        background-color: #1cc88a;
        color: white;
        padding: 8px 20px;
        border-radius: 4px;
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.3s;
    }
    
    .take-quiz-btn:hover {
        background-color: #17a673;
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
    
    .status-not-available {
        background-color: #f0f0f0;
        color: #6c757d;
    }
</style>

<div class="content-container">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4 class="page-header">
                    Course Quizzes
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
                $course_row = $course_result->fetch_assoc();
                $course_name = $course_row['course_name'] ?? 'Unknown Course';
                $course_stmt->close();
                
                echo "<h5>Course: $course_name</h5>";
                
                // Fetch quizzes for this course
                $sql = "SELECT q.quiz_id, q.quiz_title, q.quiz_desc, q.time_limit, q.passing_score,
                        COUNT(qq.question_id) AS total_questions,
                        (SELECT COUNT(*) FROM quiz_results qr 
                         WHERE qr.quiz_id = q.quiz_id AND qr.stud_id = ?) AS attempts
                        FROM quizzes q
                        LEFT JOIN quiz_questions qq ON q.quiz_id = qq.quiz_id
                        WHERE q.course_id = ?
                        GROUP BY q.quiz_id
                        ORDER BY q.quiz_id DESC";
                
                $stmt = $conn->prepare($sql);
                
                if ($stmt) {
                    $stmt->bind_param("ii", $stu_id, $course_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $quiz_id = $row['quiz_id'];
                            $attempts = $row['attempts'];
                            $total_questions = $row['total_questions'];
                            $time_limit = $row['time_limit'] > 0 ? $row['time_limit'] . ' minutes' : 'No time limit';
                            
                            // Determine quiz status and action button
                            $status = '';
                            $status_class = '';
                            $action_btn = '';
                            
                            if ($attempts > 0) {
                                // Student has attempted this quiz
                                $status = 'Completed';
                                $status_class = 'status-completed';
                                $action_btn = '<a href="quiz_results.php?quiz_id='.$quiz_id.'" class="view-results-btn">
                                                <i class="fas fa-chart-bar mr-2"></i>View Results
                                              </a>';
                            } else {
                                // Student hasn't attempted yet
                                $status = 'Available';
                                $status_class = 'status-available';
                                $action_btn = '<a href="take_quiz.php?quiz_id='.$quiz_id.'" class="take-quiz-btn">
                                                <i class="fas fa-pencil-alt mr-2"></i>Take Quiz
                                              </a>';
                            }
                            
                            echo '<div class="quiz-card">
                                    <h5 class="quiz-title">'.$row['quiz_title'].'</h5>
                                    <div class="quiz-meta">
                                        <div class="meta-item">
                                            <i class="fas fa-question-circle"></i>
                                            <span>Questions: '.$total_questions.'</span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-clock"></i>
                                            <span>Time Limit: '.$time_limit.'</span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-trophy"></i>
                                            <span>Passing Score: '.$row['passing_score'].'%</span>
                                        </div>
                                        <div class="meta-item">
                                            <span class="status-badge '.$status_class.'">'.$status.'</span>
                                        </div>
                                    </div>
                                    <p class="quiz-desc">'.$row['quiz_desc'].'</p>
                                    <div>'.$action_btn.'</div>
                                </div>';
                        }
                    } else {
                        echo '<div class="no-quizzes">
                                <i class="fas fa-question-circle"></i>
                                <p>No quizzes found for this course.</p>
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