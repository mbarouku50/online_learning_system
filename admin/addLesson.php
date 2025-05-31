<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Attempt to override PHP upload limits (may not work on all servers)
ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '100M');

if (!isset($_SESSION)) {
    session_start();
}

include("./admininclude/header.php");
include("../dbconnection.php");

if (!isset($_SESSION['is_admin_login'])) {
    header("Location: ../index.php");
    exit();
}

$msg = '';

if (isset($_POST['lessonSubmitBtn'])) {
    // Check for empty fields
    if (empty($_POST['lesson_name']) || empty($_POST['lesson_desc']) ||
        empty($_POST['course_id']) || empty($_POST['course_name'])) {
        $msg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2">Fill All Fields</div>';
    } elseif (!isset($_FILES['lesson_link']) || $_FILES['lesson_link']['error'] == UPLOAD_ERR_NO_FILE) {
        $msg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2">Please select a video file</div>';
    } else {
        $lesson_name = $_POST['lesson_name'];
        $lesson_desc = $_POST['lesson_desc'];
        $course_id = $_POST['course_id'];
        $course_name = $_POST['course_name'];

        // Handle file upload
        $target_dir = "../lessonvid/";
        $file_name = basename($_FILES["lesson_link"]["name"]);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = ['mp4', 'avi', 'mov', 'wmv']; // Allowed video extensions
        $max_file_size = 100 * 1024 * 1024; // 100MB in bytes

        // Generate a unique file name to avoid conflicts
        $unique_file_name = uniqid() . '_' . str_replace(' ', '_', $file_name);
        $target_file = $target_dir . $unique_file_name;

        // Validate file
        if (!in_array($file_ext, $allowed_exts)) {
            $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Invalid file type. Only MP4, AVI, MOV, and WMV are allowed.</div>';
        } elseif ($_FILES["lesson_link"]["size"] > $max_file_size) {
            $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">File is too large. Maximum size is 100MB.</div>';
        } elseif ($_FILES["lesson_link"]["error"] == UPLOAD_ERR_INI_SIZE) {
            $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">File is too large. Maximum allowed size is ' . ini_get('upload_max_filesize') . '. Contact the administrator to increase the limit.</div>';
        } elseif ($_FILES["lesson_link"]["error"] != UPLOAD_ERR_OK) {
            $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">File upload error: ' . $_FILES["lesson_link"]["error"] . '</div>';
        } else {
            // Create directory if it doesn't exist
            if (!file_exists($target_dir)) {
                if (!mkdir($target_dir, 0777, true)) {
                    $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Failed to create directory.</div>';
                }
            }

            // Check directory permissions
            if (!is_writable($target_dir)) {
                $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Directory is not writable. Check permissions.</div>';
            } else {
                // Move the uploaded file
                if (move_uploaded_file($_FILES["lesson_link"]["tmp_name"], $target_file)) {
                    // Use prepared statement to insert data
                    $stmt = $conn->prepare("INSERT INTO lesson (lesson_name, lesson_desc, lesson_link, course_id, course_name) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $lesson_name, $lesson_desc, $target_file, $course_id, $course_name);

                    if ($stmt->execute()) {
                        $msg = '<div class="alert alert-success col-sm-6 ml-5 mt-2">Lesson added successfully</div>';
                    } else {
                        $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Database error: ' . $stmt->error . '</div>';
                    }
                    $stmt->close();
                } else {
                    $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Error uploading file. Check server logs for details.</div>';
                }
            }
        }
    }
}
?>

<div class="col-sm-6 mt-5 mx-3 jumbotron">
    <h3 class="text-center">Add New Lesson</h3>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="course_id">Course ID</label>
            <input type="text" class="form-control" id="course_id" name="course_id" 
                value="<?php if (isset($_SESSION['course_id'])) { echo htmlspecialchars($_SESSION['course_id']); } ?>" readonly>
        </div>
        <div class="form-group">
            <label for="course_name">Course Name</label>
            <input type="text" class="form-control" id="course_name" name="course_name" 
                value="<?php if (isset($_SESSION['course_name'])) { echo htmlspecialchars($_SESSION['course_name']); } ?>" readonly>
        </div>
        <div class="form-group">
            <label for="lesson_name">Lesson Name</label>
            <input type="text" class="form-control" id="lesson_name" name="lesson_name">
        </div>
        <div class="form-group">
            <label for="lesson_desc">Lesson Description</label>
            <textarea class="form-control" name="lesson_desc" id="lesson_desc" rows="2"></textarea>
        </div>
        <div class="form-group">
            <label for="lesson_link">Lesson Video Link</label>
            <input type="file" class="form-control-file" id="lesson_link" name="lesson_link" accept="video/*">
            <small class="form-text text-muted">Allowed types: MP4, AVI, MOV, WMV. Max size: 100MB.</small>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-danger" id="lessonSubmitBtn" name="lessonSubmitBtn">Submit</button>
            <a href="lessons.php" class="btn btn-secondary">Close</a>
        </div>
        <?php if (!empty($msg)) { echo $msg; } ?>
    </form>
</div>

</div>
</div>

<?php
include("./admininclude/footer.php");
$conn->close();
?>