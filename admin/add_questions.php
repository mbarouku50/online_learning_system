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

// Handle form submission
if(isset($_POST['submit'])){
    $question_text = $_POST['question_text'];
    $question_type = $_POST['question_type'];
    $points = $_POST['points'];
    $sequence_number = $_POST['sequence_number'];
    $options = [$_POST['option_a'], $_POST['option_b'], $_POST['option_c'], $_POST['option_d']];
    $correct_option = $_POST['correct_option'];

    // Insert question
    $sql = "INSERT INTO questions (quiz_id, question_text, question_type, points, sequence_number) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issii", $quiz_id, $question_text, $question_type, $points, $sequence_number);
    
    if($stmt->execute()){
        $question_id = $conn->insert_id;
        
        // Insert options
        $option_sql = "INSERT INTO question_options (question_id, option_text, is_correct) VALUES (?, ?, ?)";
        $option_stmt = $conn->prepare($option_sql);
        
        foreach($options as $index => $option_text){
            $is_correct = ($index == $correct_option) ? 1 : 0;
            $option_stmt->bind_param("isi", $question_id, $option_text, $is_correct);
            $option_text = $option_text;
            $option_stmt->execute();
        }
        
        $msg = '<div class="alert alert-success">Question and options added successfully!</div>';
    } else {
        $msg = '<div class="alert alert-danger">Error adding question: ' . $conn->error . '</div>';
    }
}
?>
<div class="col-sm-9 mt-5">
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h3 class="mt-4"><?php echo htmlspecialchars($course_name); ?> - <?php echo htmlspecialchars($quiz_title); ?> - Add Questions</h3>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="courses.php">Courses</a></li>
                    <li class="breadcrumb-item"><a href="quizzes.php?course_id=<?php echo $course_id; ?>">Quizzes</a></li>
                    <li class="breadcrumb-item active">Add Questions</li>
                </ol>
                
                <?php if(isset($msg)) echo $msg; ?>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-plus-circle mr-1"></i>
                        Add New Question
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="question_text">Question Text</label>
                                <textarea class="form-control" id="question_text" name="question_text" rows="4" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="question_type">Question Type</label>
                                <select class="form-control" id="question_type" name="question_type" required>
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <!-- Add other types if needed -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="points">Points</label>
                                <input type="number" class="form-control" id="points" name="points" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="sequence_number">Sequence Number</label>
                                <input type="number" class="form-control" id="sequence_number" name="sequence_number" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="option_a">Option A</label>
                                <input type="text" class="form-control" id="option_a" name="option_a" required>
                            </div>
                            <div class="form-group">
                                <label for="option_b">Option B</label>
                                <input type="text" class="form-control" id="option_b" name="option_b" required>
                            </div>
                            <div class="form-group">
                                <label for="option_c">Option C</label>
                                <input type="text" class="form-control" id="option_c" name="option_c" required>
                            </div>
                            <div class="form-group">
                                <label for="option_d">Option D</label>
                                <input type="text" class="form-control" id="option_d" name="option_d" required>
                            </div>
                            <div class="form-group">
                                <label for="correct_option">Correct Option</label>
                                <select class="form-control" id="correct_option" name="correct_option" required>
                                    <option value="0">A</option>
                                    <option value="1">B</option>
                                    <option value="2">C</option>
                                    <option value="3">D</option>
                                </select>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary">Add Question</button>
                            <a href="view_questions.php?quiz_id=<?php echo $quiz_id; ?>" class="btn btn-secondary">View Questions</a>
                        </form>
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