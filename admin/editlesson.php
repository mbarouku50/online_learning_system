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

// Update logic
if (isset($_POST['requpdate'])) {
    // Check for empty fields (trim to avoid whitespace issues)
    if (
        empty(trim($_POST['lesson_id'])) ||
        empty(trim($_POST['lesson_name'])) ||
        empty(trim($_POST['lesson_desc'])) ||
        empty(trim($_POST['course_id'])) ||
        empty(trim($_POST['course_name']))
    ) {
        $msg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2" role="alert">Please fill all fields.</div>';
    } else {
        // Assign user values to variables
        $lesson_id = trim($_POST['lesson_id']);
        $lesson_name = trim($_POST['lesson_name']);
        $lesson_desc = trim($_POST['lesson_desc']);
        $course_id = trim($_POST['course_id']);
        $course_name = trim($_POST['course_name']);
        $current_lesson_link = trim($_POST['current_lesson_link']); // Get current lesson link

        // Initialize lesson link variable
        $lesson_link = $current_lesson_link;

        // Handle file upload only if a new file is provided
        if (!empty($_FILES["lesson_link"]["name"])) {
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
                        // Delete old file if it exists and is different from new one
                        if (!empty($current_lesson_link) && file_exists($current_lesson_link) && $current_lesson_link != $target_file) {
                            unlink($current_lesson_link);
                        }
                        $lesson_link = $target_file;
                    } else {
                        $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Failed to upload file.</div>';
                    }
                }
            }
        }

        if (!isset($msg)) {
            // Use prepared statement to update data
            $sql = "UPDATE lesson SET lesson_name = ?, lesson_desc = ?, lesson_link = ? WHERE lesson_id = ?";
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("sssi", $lesson_name, $lesson_desc, $lesson_link, $lesson_id);
                
                if ($stmt->execute()) {
                    $msg = '<div class="alert alert-success col-sm-6 ml-5 mt-2" role="alert">Updated successfully.</div>';
                } else {
                    $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert">Unable to update: ' . $stmt->error . '</div>';
                }
                $stmt->close();
            } else {
                $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert">Database error: ' . $conn->error . '</div>';
            }
        }
    }
}
?>

<div class="col-sm-6 mt-5 mx-3 jumbotron">
    <h3 class="text-center">Update Lesson Details</h3>
    <?php
    if(isset($_REQUEST['view'])){
        $sql = "SELECT * FROM lesson WHERE lesson_id = {$_REQUEST['id']}";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
    }
    ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="current_lesson_link" value="<?php if(isset($row['lesson_link'])){ echo $row['lesson_link']; }?>">
        <div class="form-group">
            <label for="lesson_id">Lesson ID</label>
            <input type="text" class="form-control" 
            id="lesson_id" name="lesson_id" 
            value="<?php if(isset($row['lesson_id'])){ echo $row['lesson_id']; }?>"
            readonly>
        </div>
        <div class="form-group">
            <label for="lesson_name">Lesson Name</label>
            <input type="text" class="form-control" 
            id="lesson_name" name="lesson_name" 
            value="<?php if(isset($row['lesson_name'])){ echo $row['lesson_name']; }?>"
            required>
        </div>
        <div class="form-group">
            <label for="lesson_desc">Lesson Description</label>
            <textarea class="form-control" name="lesson_desc" 
            id="lesson_desc" rows="2" required><?php if(isset($row['lesson_desc'])){ echo $row['lesson_desc']; }?></textarea>
        </div>
        <div class="form-group">
            <label for="course_id">Course ID</label>
            <input type="text" class="form-control" 
            id="course_id" name="course_id"
            value="<?php if(isset($row['course_id'])){ echo $row['course_id']; }?>"
            readonly>
        </div>
        <div class="form-group">
            <label for="course_name">Course Name</label>
            <input type="text" class="form-control" 
            id="course_name" name="course_name"
            value="<?php if(isset($row['course_name'])){ echo $row['course_name']; }?>"
            readonly>
        </div>
        <div class="form-group">
            <label for="lesson_link">Lesson Video</label>
            <div class="embed-responsive embed-responsive-16by9 mb-2">
                <video controls class="embed-responsive-item" width="520" height="280" class="border rounded">
                    <source src="<?php if(isset($row['lesson_link'])) {echo $row['lesson_link']; }?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <small class="text-muted">Current file: <?php if(isset($row['lesson_link'])) { echo basename($row['lesson_link']); }?></small>
            <input type="file" class="form-control-file mt-2" 
            id="lesson_link" name="lesson_link" accept="video/mp4,video/avi,video/mov,video/wmv">
            <small class="form-text text-muted">Leave blank to keep current video (Max 100MB)</small>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-danger"
            id="requpdate" name="requpdate">Update</button>
            <a href="lessons.php" class="btn btn-secondary">Close</a>
        </div>
        <?php if(isset($msg)) {echo $msg;} ?>
    </form>
</div>

</div><!-- div row close from header -->
</div><!-- div container-fluid close from header -->

<?php
include("./admininclude/footer.php");
?>