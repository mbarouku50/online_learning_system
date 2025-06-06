<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION)) {
    session_start();
}

include('./stuInclude/header.php');
include_once(__DIR__ . '/../dbconnection.php');

// Check database connection
if (!isset($conn) || !$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['is_login'])) {
    echo "<script>location.href='../index.php';</script>";
    exit;
}

$stuemail = $_SESSION['stuemail'] ?? '';
$course_id = filter_var($_GET['course_id'] ?? 0, FILTER_VALIDATE_INT);

if ($course_id === 0) {
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
        border-bottom: 2px solid #1cc88a;
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
    
    .assignment-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        padding: 20px;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .assignment-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
    
    .assignment-title {
        font-size: 1.2rem;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .assignment-meta {
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
    
    .assignment-desc {
        color: #6c757d;
        margin-bottom: 15px;
    }
    
    .no-assignments {
        text-align: center;
        padding: 40px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .no-assignments i {
        font-size: 3rem;
        color: #6c757d;
        margin-bottom: 20px;
    }
    
    .no-assignments p {
        font-size: 1.2rem;
        color: #6c757d;
    }
    
    .view-btn {
        background-color: #4e73df;
        color: white;
        padding: 8px 20px;
        border-radius: 4px;
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.3s;
        margin-right: 10px;
    }
    
    .view-btn:hover {
        background-color: #2e59d9;
        color: white;
    }
    
    .download-btn {
        background-color: #36b9cc;
        color: white;
        padding: 8px 20px;
        border-radius: 4px;
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.3s;
    }
    
    .download-btn:hover {
        background-color: #2c9faf;
        color: white;
    }
    
    .file-info {
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }
    
    .file-info i {
        margin-right: 10px;
        font-size: 1.5rem;
        color: #4e73df;
    }
    
    .file-details {
        flex-grow: 1;
    }
    
    .file-name {
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .file-size {
        color: #6c757d;
        font-size: 0.85rem;
    }
</style>

<div class="content-container">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4 class="page-header">
                    Course Assignments
                    <a href="myCourse.php" class="back-btn">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Courses
                    </a>
                </h4>
                
                <?php
                // Fetch course name
                $course_sql = "SELECT course_name FROM course WHERE course_id = ?";
                $course_stmt = $conn->prepare($course_sql);
                if (!$course_stmt) {
                    echo '<div class="alert alert-danger">Course query preparation failed: ' . $conn->error . '</div>';
                    exit;
                }
                $course_stmt->bind_param("i", $course_id);
                $course_stmt->execute();
                $course_result = $course_stmt->get_result();
                $course_name = $course_result->fetch_assoc()['course_name'] ?? 'Unknown Course';
                $course_stmt->close();
                
                echo "<h5>Course: $course_name</h5>";
                
                // Fetch assignments
                $sql = "SELECT assign_id, assign_title, assign_desc, assign_file, due_date
                        FROM assignments
                        WHERE course_id = ?
                        ORDER BY due_date ASC";
                
                $stmt = $conn->prepare($sql);
                
                if ($stmt) {
                    $stmt->bind_param("i", $course_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Ensure the file path points to the correct directory
                            $file_path = "../admin/assignment_uploads/" . $row['assign_file'];
                            $file_size = file_exists($file_path) ? filesize($file_path) : 0;
                            $file_size_formatted = $file_size > 0 ? formatFileSize($file_size) : 'Unknown size';
                            // Format dates
                            $due_date = date('F j, Y, g:i a', strtotime($row['due_date']));
                            $current_time = time();
                            $due_time = strtotime($row['due_date']);
                            $is_late = ($current_time > $due_time) ? ' (Late)' : '';
                            
                            echo '<div class="assignment-card">
                                    <h5 class="assignment-title">' . htmlspecialchars($row['assign_title']) . '</h5>
                                    <div class="assignment-meta">
                                        <div class="meta-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>Due Date: ' . $due_date . $is_late . '</span>
                                        </div>
                                    </div>
                                    
                                    <div class="file-info">
                                        <i class="fas fa-file-alt"></i>
                                        <div class="file-details">
                                            <div class="file-name">Assignment File: ' . htmlspecialchars($row['assign_file']) . '</div>
                                            <div class="file-size">Size: ' . $file_size_formatted . '</div>
                                        </div>
                                    </div>
                                    
                                    <p class="assignment-desc">' . htmlspecialchars($row['assign_desc']) . '</p>
                                    
                                    <div class="btn-group">';
                            
                            // View/Download buttons for assignment file
                            echo '<a href="view_assignment.php?file=' . urlencode($row['assign_file']) . '" class="view-btn" target="_blank">
                                    <i class="fas fa-eye mr-2"></i>View
                                  </a>';
                            echo '<a href="../admin/assignment_uploads/' . htmlspecialchars($row['assign_file']) . '" class="download-btn" download>
                                    <i class="fas fa-download mr-2"></i>Download
                                  </a>';
                            
                            echo '</div>
                                </div>';
                        }
                    } else {
                        echo '<div class="no-assignments">
                                <i class="fas fa-tasks"></i>
                                <p>No assignments found for this course.</p>
                              </div>';
                    }
                    
                    $stmt->close();
                } else {
                    echo '<div class="alert alert-danger">Assignment query preparation failed: ' . $conn->error . '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
// Function to format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        return $bytes . ' bytes';
    } elseif ($bytes == 1) {
        return '1 byte';
    } else {
        return '0 bytes';
    }
}

include('./stuInclude/footer.php');
?>