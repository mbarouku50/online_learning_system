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

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get course ID from URL
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// Fetch course details
$course_sql = "SELECT course_name FROM course WHERE course_id = $course_id";
$course_result = $conn->query($course_sql);
$course_row = $course_result->fetch_assoc();
$course_name = $course_row['course_name'] ?? 'Unknown Course';

// Initialize message variable
$msg = '';

// Handle form submission
if(isset($_POST['submit'])){
    // Validate and sanitize inputs
    $assign_title = mysqli_real_escape_string($conn, $_POST['assign_title']);
    $assign_desc = mysqli_real_escape_string($conn, $_POST['assign_desc']);
    $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);
    
    // File upload handling
    $target_dir = "../assignment_uploads/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    // Check if directory is writable
    if (!is_writable($target_dir)) {
        $msg = '<div class="alert alert-danger">Upload directory is not writable. Please check permissions.</div>';
    } else {
        $original_file_name = basename($_FILES["assign_file"]["name"]);
        $file_extension = strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));
        
        // Generate unique filename to prevent overwrites
        $new_file_name = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9-_\.]/', '', $original_file_name);
        $target_file = $target_dir . $new_file_name;
        
        // Check file size (5MB max)
        if ($_FILES["assign_file"]["size"] > 5000000) {
            $msg = '<div class="alert alert-warning">Sorry, your file is too large (max 5MB).</div>';
        }
        
        // Allow certain file formats
        $allowed_types = array("pdf", "doc", "docx", "txt", "zip");
        if(!in_array($file_extension, $allowed_types)) {
            $msg = '<div class="alert alert-warning">Sorry, only PDF, DOC, DOCX, TXT & ZIP files are allowed.</div>';
        }
        
        // If no errors, proceed with upload
        if(empty($msg)) {
            if (move_uploaded_file($_FILES["assign_file"]["tmp_name"], $target_file)) {
                // Insert into database
                $sql = "INSERT INTO assignments (course_id, assign_title, assign_desc, assign_file, due_date, created_at) 
                        VALUES (?, ?, ?, ?, ?, NOW())";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("issss", $course_id, $assign_title, $assign_desc, $new_file_name, $due_date);
                
                if($stmt->execute()){
                    $msg = '<div class="alert alert-success">Assignment added successfully!</div>';
                } else {
                    // Delete the uploaded file if DB insert fails
                    unlink($target_file);
                    $msg = '<div class="alert alert-danger">Error adding assignment: ' . $conn->error . '</div>';
                }
                $stmt->close();
            } else {
                $error = $_FILES["assign_file"]["error"];
                $upload_errors = [
                    UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize in php.ini',
                    UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE in HTML form',
                    UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                    UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
                ];
                $error_msg = isset($upload_errors[$error]) ? $upload_errors[$error] : 'Unknown error';
                $msg = '<div class="alert alert-danger">File upload failed: ' . $error_msg . '</div>';
            }
        }
    }
}

// Handle delete action
if(isset($_REQUEST['delete'])){
    $assign_id = intval($_REQUEST['id']);
    
    // First get the filename to delete from server
    $sql = "SELECT assign_file FROM assignments WHERE assign_id = $assign_id";
    $result = $conn->query($sql);
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $file_to_delete = "../assignment_uploads/" . $row['assign_file'];
        
        // Delete from database
        $sql = "DELETE FROM assignments WHERE assign_id = $assign_id";
        if($conn->query($sql)) {
            // Delete the file
            if(file_exists($file_to_delete)) {
                unlink($file_to_delete);
            }
            $msg = '<div class="alert alert-success">Assignment deleted successfully!</div>';
        } else {
            $msg = '<div class="alert alert-danger">Error deleting assignment: ' . $conn->error . '</div>';
        }
    }
}
?>

<div class="col-sm-9 mt-5">
    <div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h3 class="mt-4"><?php echo htmlspecialchars($course_name); ?> - Assignments</h3>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="courses.php">Courses</a></li>
                    <li class="breadcrumb-item active">Assignments</li>
                </ol>
                
                <?php if(isset($msg)) echo $msg; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-plus-circle mr-1"></i>
                                Add New Assignment
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="assign_title">Assignment Title</label>
                                        <input type="text" class="form-control" id="assign_title" name="assign_title" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="assign_desc">Description</label>
                                        <textarea class="form-control" id="assign_desc" name="assign_desc" rows="3" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="assign_file">Assignment File</label>
                                        <input type="file" class="form-control-file" id="assign_file" name="assign_file" required>
                                        <small class="form-text text-muted">Allowed formats: PDF, DOC, DOCX, TXT, ZIP (Max 5MB)</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="due_date">Due Date</label>
                                        <input type="datetime-local" class="form-control" id="due_date" name="due_date" required>
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-primary">Upload Assignment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-tasks mr-1"></i>
                                Current Assignments
                            </div>
                            <div class="card-body">
                                <?php
                                $sql = "SELECT * FROM assignments WHERE course_id = $course_id ORDER BY due_date DESC";
                                $result = $conn->query($sql);
                                
                                if($result->num_rows > 0){
                                    while($row = $result->fetch_assoc()){
                                        echo '<div class="assignment-item mb-3 p-3 border rounded">';
                                        echo '<h5>' . htmlspecialchars($row['assign_title']) . '</h5>';
                                        echo '<p>' . htmlspecialchars($row['assign_desc']) . '</p>';
                                        echo '<p><strong>Due:</strong> ' . date('M j, Y g:i A', strtotime($row['due_date'])) . '</p>';
                                        echo '<p><strong>Posted:</strong> ' . date('M j, Y', strtotime($row['created_at'])) . '</p>';
                                        
                                        // View button instead of download
                                        $file_path = "../assignment_uploads/" . htmlspecialchars($row['assign_file']);
                                        $file_extension = strtolower(pathinfo($row['assign_file'], PATHINFO_EXTENSION));
                                        
                                        // Different view options based on file type
                                        if($file_extension == 'pdf') {
                                            // For PDFs, use PDF viewer
                                            echo '<a href="view_assignment.php?file=' . urlencode($row['assign_file']) . '" class="btn btn-sm btn-info mr-2" target="_blank">View PDF</a>';
                                        } elseif(in_array($file_extension, ['doc', 'docx'])) {
                                            // For Word docs, use Google Docs viewer
                                            echo '<a href="https://docs.google.com/viewer?url=' . urlencode((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '../admin/assignment_uploads/' . urlencode($row['assign_file'])) . '" class="btn btn-sm btn-info mr-2" target="_blank">View Document</a>';
                                        } elseif($file_extension == 'txt') {
                                            // For text files, display directly
                                            echo '<a href="view_text.php?file=' . urlencode($row['assign_file']) . '" class="btn btn-sm btn-info mr-2" target="_blank">View Text</a>';
                                        } else {
                                            // For other formats (like zip), show message
                                            echo '<span class="btn btn-sm btn-secondary mr-2">File must be downloaded to view</span>';
                                        }
                                        
                                        echo '<form method="POST" class="d-inline">';
                                        echo '<input type="hidden" name="id" value="' . $row['assign_id'] . '">';
                                        echo '<button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this assignment?\')">Delete</button>';
                                        echo '</form>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<p>No assignments found for this course.</p>';
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