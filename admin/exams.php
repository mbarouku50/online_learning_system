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

// Get course ID from URL
$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : 0;

// Fetch course details
$course_sql = "SELECT course_name FROM course WHERE course_id = $course_id";
$course_result = $conn->query($course_sql);
$course_row = $course_result->fetch_assoc();
$course_name = $course_row['course_name'] ?? 'Unknown Course';

// Handle form submission
if(isset($_POST['submit'])){
    $exam_title = $_POST['exam_title'];
    $exam_desc = $_POST['exam_desc'];
    $total_questions = $_POST['total_questions'];
    $time_limit = $_POST['time_limit'];
    $passing_score = $_POST['passing_score'];
    $exam_date = $_POST['exam_date'];
    
    $sql = "INSERT INTO exams (course_id, exam_title, exam_desc, total_questions, time_limit, passing_score, exam_date) 
            VALUES ('$course_id', '$exam_title', '$exam_desc', '$total_questions', '$time_limit', '$passing_score', '$exam_date')";
    
    if($conn->query($sql) === TRUE){
        $exam_id = $conn->insert_id;
        $msg = '<div class="alert alert-success">Exam created successfully! <a href="add_exam_questions.php?exam_id='.$exam_id.'">Add questions now</a></div>';
    } else {
        $msg = '<div class="alert alert-danger">Error creating exam: ' . $conn->error . '</div>';
    }
}

// Handle delete action
if(isset($_REQUEST['delete'])){
    $exam_id = $_REQUEST['id'];
    $sql = "DELETE FROM exams WHERE exam_id = $exam_id";
    if($conn->query($sql) === TRUE){
        $msg = '<div class="alert alert-success">Exam deleted successfully!</div>';
    } else {
        $msg = '<div class="alert alert-danger">Error deleting exam: ' . $conn->error . '</div>';
    }
}
?>
<div class="col-sm-9 mt-5">
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h3 class="mt-4"><?php echo $course_name; ?> - Exams</h3>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="courses.php">Courses</a></li>
                    <li class="breadcrumb-item active">Exams</li>
                </ol>
                
                <?php if(isset($msg)) echo $msg; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-plus-circle mr-1"></i>
                                Schedule New Exam
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="form-group">
                                        <label for="exam_title">Exam Title</label>
                                        <input type="text" class="form-control" id="exam_title" name="exam_title" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="exam_desc">Description</label>
                                        <textarea class="form-control" id="exam_desc" name="exam_desc" rows="3" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="total_questions">Total Questions</label>
                                        <input type="number" class="form-control" id="total_questions" name="total_questions" min="1" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="time_limit">Time Limit (minutes)</label>
                                        <input type="number" class="form-control" id="time_limit" name="time_limit" min="1" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="passing_score">Passing Score (%)</label>
                                        <input type="number" class="form-control" id="passing_score" name="passing_score" min="1" max="100" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="exam_date">Exam Date & Time</label>
                                        <input type="datetime-local" class="form-control" id="exam_date" name="exam_date" required>
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-primary">Schedule Exam</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-file-alt mr-1"></i>
                                Upcoming Exams
                            </div>
                            <div class="card-body">
                                <?php
                                $sql = "SELECT * FROM exams WHERE course_id = $course_id AND exam_date > NOW() ORDER BY exam_date ASC";
                                $result = $conn->query($sql);
                                
                                if($result->num_rows > 0){
                                    while($row = $result->fetch_assoc()){
                                        echo '<div class="exam-item mb-3 p-3 border rounded">';
                                        echo '<h5>' . htmlspecialchars($row['exam_title']) . '</h5>';
                                        echo '<p>' . htmlspecialchars($row['exam_desc']) . '</p>';
                                        echo '<p><strong>Date:</strong> ' . date('M j, Y g:i A', strtotime($row['exam_date'])) . '</p>';
                                        echo '<p><strong>Duration:</strong> ' . $row['time_limit'] . ' minutes</p>';
                                        echo '<p><strong>Questions:</strong> ' . $row['total_questions'] . '</p>';
                                        echo '<p><strong>Passing Score:</strong> ' . $row['passing_score'] . '%</p>';
                                        
                                        echo '<div class="btn-group">';
                                        echo '<a href="add_exam_questions.php?exam_id='.$row['exam_id'].'" class="btn btn-sm btn-info mr-2">Add Questions</a>';
                                        echo '<a href="view_exam_questions.php?exam_id='.$row['exam_id'].'" class="btn btn-sm btn-secondary mr-2">View Questions</a>';
                                        echo '<form method="POST" class="d-inline">';
                                        echo '<input type="hidden" name="id" value="' . $row['exam_id'] . '">';
                                        echo '<button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this exam?\')">Delete</button>';
                                        echo '</form>';
                                        echo '</div>';
                                        
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<p>No upcoming exams found for this course.</p>';
                                }
                                
                                // Past exams section
                                $past_sql = "SELECT * FROM exams WHERE course_id = $course_id AND exam_date <= NOW() ORDER BY exam_date DESC";
                                $past_result = $conn->query($past_sql);
                                
                                if($past_result->num_rows > 0){
                                    echo '<div class="mt-4">';
                                    echo '<h5>Past Exams</h5>';
                                    echo '<div class="list-group">';
                                    
                                    while($past_row = $past_result->fetch_assoc()){
                                        echo '<div class="list-group-item list-group-item-action flex-column align-items-start">';
                                        echo '<div class="d-flex w-100 justify-content-between">';
                                        echo '<h6 class="mb-1">' . htmlspecialchars($past_row['exam_title']) . '</h6>';
                                        echo '<small>' . date('M j, Y', strtotime($past_row['exam_date'])) . '</small>';
                                        echo '</div>';
                                        echo '<a href="exam_results.php?exam_id='.$past_row['exam_id'].'" class="btn btn-sm btn-outline-primary mt-2">View Results</a>';
                                        echo '</div>';
                                    }
                                    
                                    echo '</div>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
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