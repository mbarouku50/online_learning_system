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

// Get quiz ID from URL
$quiz_id = isset($_GET['quiz_id']) ? $_GET['quiz_id'] : 0;

// Fetch quiz details
$quiz_sql = "SELECT quiz_title, course_id FROM quizzes WHERE quiz_id = ?";
$quiz_stmt = $conn->prepare($quiz_sql);
$quiz_stmt->bind_param("i", $quiz_id);
$quiz_stmt->execute();
$quiz_result = $quiz_stmt->get_result();
$quiz_row = $quiz_result->fetch_assoc();
$quiz_title = $quiz_row['quiz_title'] ?? 'Unknown Quiz';
$course_id = $quiz_row['course_id'] ?? 0;

// Fetch course details
$course_sql = "SELECT course_name FROM course WHERE course_id = ?";
$course_stmt = $conn->prepare($course_sql);
$course_stmt->bind_param("i", $course_id);
$course_stmt->execute();
$course_result = $course_stmt->get_result();
$course_row = $course_result->fetch_assoc();
$course_name = $course_row['course_name'] ?? 'Unknown Course';

// Handle delete action
if(isset($_REQUEST['delete'])){
    $question_id = $_REQUEST['id'];
    $sql = "DELETE FROM questions WHERE question_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $question_id);
    if($stmt->execute()){
        $msg = '<div class="alert alert-success">Question deleted successfully!</div>';
    } else {
        $msg = '<div class="alert alert-danger">Error deleting question: ' . $conn->error . '</div>';
    }
}
?>
<div class="col-sm-9 mt-5">
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h3 class="mt-4"><?php echo htmlspecialchars($course_name); ?> - <?php echo htmlspecialchars($quiz_title); ?> - Questions</h3>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="courses.php">Courses</a></li>
                    <li class="breadcrumb-item"><a href="quizzes.php?course_id=<?php echo $course_id; ?>">Quizzes</a></li>
                    <li class="breadcrumb-item active">Questions</li>
                </ol>
                
                <?php if(isset($msg)) echo $msg; ?>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-question-circle mr-1"></i>
                        Questions
                        <a href="add_questions.php?quiz_id=<?php echo $quiz_id; ?>" class="btn btn-primary btn-sm float-right">Add Question</a>
                    </div>
                    <div class="card-body">
                        <?php
                        $sql = "SELECT * FROM questions WHERE quiz_id = ? ORDER BY sequence_number ASC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $quiz_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if($result->num_rows > 0){
                            while($row = $result->fetch_assoc()){
                                echo '<div class="question-item mb-3 p-3 border rounded">';
                                echo '<h5>' . htmlspecialchars($row['question_text']) . '</h5>';
                                echo '<p><strong>Type:</strong> ' . htmlspecialchars($row['question_type']) . '</p>';
                                echo '<p><strong>Points:</strong> ' . $row['points'] . '</p>';
                                echo '<p><strong>Sequence:</strong> ' . $row['sequence_number'] . '</p>';
                                
                                // Fetch options
                                $option_sql = "SELECT option_text, is_correct FROM question_options WHERE question_id = ? ORDER BY option_id";
                                $option_stmt = $conn->prepare($option_sql);
                                $option_stmt->bind_param("i", $row['question_id']);
                                $option_stmt->execute();
                                $option_result = $option_stmt->get_result();
                                
                                echo '<p><strong>Options:</strong></p>';
                                $option_letters = ['A', 'B', 'C', 'D'];
                                $option_index = 0;
                                while($option_row = $option_result->fetch_assoc()){
                                    $correct_mark = $option_row['is_correct'] ? ' (Correct)' : '';
                                    echo '<p><strong>' . $option_letters[$option_index] . ':</strong> ' . htmlspecialchars($option_row['option_text']) . $correct_mark . '</p>';
                                    $option_index++;
                                }
                                
                                echo '<form method="POST" class="d-inline">';
                                echo '<input type="hidden" name="id" value="' . $row['question_id'] . '">';
                                echo '<button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this question?\')">Delete</button>';
                                echo '</form>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>No questions found for this quiz.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php
include("./admininclude/footer.php");
?>