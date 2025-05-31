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
    exit;
}
?>

<style>
    .search-container {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .course-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .course-header {
        background-color: #2c3e50;
        color: white;
        border-radius: 8px 8px 0 0;
        padding: 15px 20px;
    }
    
    .table-responsive {
        border-radius: 0 0 8px 8px;
        overflow: hidden;
    }
    
    .table thead {
        background-color: #4e73df;
        color: white;
    }
    
    .table th {
        border: none;
    }
    
    .table td, .table th {
        vertical-align: middle;
        padding: 12px 15px;
    }
    
    .text-truncate {
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .btn-action {
        padding: 5px 10px;
        border-radius: 4px;
        margin: 0 3px;
    }
    
    .fixed-action-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
    }
    
    .box {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        transition: all 0.3s;
    }
    
    .box:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 12px rgba(0,0,0,0.3);
    }
    
    .alert {
        border-radius: 8px;
    }
</style>

<div class="col-sm-9 mt-5">
    <div class="row">
        <div class="col-sm-12">
            <div class="search-container">
                <form action="" method="GET" class="form-inline">
                    <div class="form-group mr-3">
                        <label for="checkid" class="mr-2">Enter Course ID:</label>
                        <input type="text" class="form-control" id="checkid" name="checkid" style="width: 150px;">
                    </div>
                    <button type="submit" class="btn btn-danger">Search</button>
                </form>
            </div>

            <?php 
            // Security improvement: Use prepared statements
            if(isset($_REQUEST['checkid'])) {
                $course_id = $_REQUEST['checkid'];
                
                // Validate course ID
                $sql = "SELECT course_id, course_name FROM course WHERE course_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $course_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $_SESSION['course_id'] = $row['course_id'];
                    $_SESSION['course_name'] = $row['course_name'];
                    ?>
                    
                    <div class="card course-card">
                        <div class="card-header course-header">
                            <h4 class="mb-0">
                                <i class="fas fa-book mr-2"></i>
                                Course ID: <?php echo htmlspecialchars($row['course_id']); ?> 
                                | Course Name: <?php echo htmlspecialchars($row['course_name']); ?>
                            </h4>
                        </div>
                        <div class="card-body p-0">
                            <?php
                            $sql = "SELECT * FROM lesson WHERE course_id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $course_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            if($result->num_rows > 0) {
                                echo '<div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th width="15%">Lesson ID</th>
                                                    <th width="30%">Lesson Name</th>
                                                    <th width="40%">Lesson Link</th>
                                                    <th width="15%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                
                                while($lesson = $result->fetch_assoc()) {
                                    echo '<tr>
                                            <th scope="row">'.htmlspecialchars($lesson["lesson_id"]).'</th>
                                            <td>'.htmlspecialchars($lesson["lesson_name"]).'</td>
                                            <td class="text-truncate">
                                                <a href="'.htmlspecialchars($lesson["lesson_link"]).'" target="_blank" title="'.htmlspecialchars($lesson["lesson_link"]).'">
                                                    '.htmlspecialchars($lesson["lesson_link"]).'
                                                </a>
                                            </td>
                                            <td class="text-center"> 
                                                <form action="editlesson.php" method="POST" class="d-inline">
                                                    <input type="hidden" name="id" value="'.htmlspecialchars($lesson["lesson_id"]).'">
                                                    <button type="submit" class="btn btn-info btn-action" name="view" value="view" title="Edit">
                                                        <i class="fas fa-pen"></i>
                                                    </button>
                                                </form>
                                                <form action="" method="POST" class="d-inline">
                                                    <input type="hidden" name="id" value="'.htmlspecialchars($lesson["lesson_id"]).'">
                                                    <button type="submit" class="btn btn-danger btn-action" name="delete" value="Delete" title="Delete" 
                                                        onclick="return confirm(\'Are you sure you want to delete this lesson?\')">
                                                        <i class="far fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>';
                                }
                                
                                echo '</tbody>
                                    </table>
                                </div>';
                            } else {
                                echo '<div class="alert alert-info m-3">No lessons found for this course.</div>';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <?php
                } else {
                    echo '<div class="alert alert-dark mt-4" role="alert">Course not found!</div>';
                }
                
                // Handle lesson deletion
                if(isset($_POST['delete'])) {
                    $lesson_id = $_POST['id'];
                    $sql = "DELETE FROM lesson WHERE lesson_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $lesson_id);
                    
                    if($stmt->execute()) {
                        echo '<div class="alert alert-success mt-3">Lesson deleted successfully!</div>';
                        echo '<meta http-equiv="refresh" content="1">';
                    } else {
                        echo '<div class="alert alert-danger mt-3">Unable to delete lesson: '.htmlspecialchars($conn->error).'</div>';
                    }
                }
            }
            ?>
        </div>
    </div>
</div>

<?php
if(isset($_SESSION['course_id'])) {
    echo '<div class="fixed-action-btn">
            <a class="btn btn-danger box rounded-circle shadow" href="./addLesson.php" title="Add New Lesson">
                <i class="fas fa-plus fa-lg"></i>
            </a>
        </div>';
}
?>

<?php
include("./admininclude/footer.php");
?>