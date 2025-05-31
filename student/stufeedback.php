<?php
if(!isset($_SESSION)){
    session_start();
}
include('./stuInclude/header.php');
include_once(__DIR__ . '/../dbconnection.php');

if (!isset($_SESSION['is_login'])) {
    echo "<script>location.href='../index.php';</script>";
    exit;
}

// Get student email from session
$stuemail = $_SESSION['stuemail'];
$passmsg = '';

// First get the student ID
$sql = "SELECT * FROM student WHERE stuemail = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $stuemail);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $stud_id = $row["stud_id"];
}
$stmt->close();

// Check if form was submitted
if(isset($_POST["submitFeedbackBtn"])){
    if(empty($_POST['f_content'])){
        $passmsg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2">Please fill in all fields</div>';
    } else {
        $fcontent = $_POST["f_content"];
        
        // Use prepared statement to prevent SQL injection
        $sql = "INSERT INTO feedback (f_content, stud_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $fcontent, $stud_id);
        
        if($stmt->execute()){
            $passmsg = '<div class="alert alert-success col-sm-6 ml-5 mt-2">Feedback submitted successfully</div>';
        } else {
            $passmsg = '<div class="alert alert-danger col-sm-6 mt-2">Unable to submit feedback: ' . $conn->error . '</div>';
        }
        $stmt->close();
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form class="profile-form" method="POST">
                <h3 class="mb-4 text-center text-primary">Write Feedback</h3>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="stuId">Student ID</label>
                            <input type="text" class="form-control" id="stuId" 
                            value="<?php echo htmlspecialchars($stud_id ?? ''); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label for="f_content">Write feedback</label>
                            <textarea name="f_content" id="f_content" class="form-control" rows="5" required></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4" name="submitFeedbackBtn">
                        <i class="fas fa-paper-plane mr-2"></i> Send Feedback
                    </button>
                </div>
                
                <?php if (isset($passmsg)) echo $passmsg; ?>
            </form>
        </div>
    </div>
</div>

<?php
include('./stuInclude/footer.php');
?>