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
?>

<style>
    .course-list-container {
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }
    
    .course-list-header {
        background-color: #4e73df;
        color: white;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 25px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .course-table {
        background-color: white;
        border-radius: 5px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .course-table thead th {
        background-color: #2c3e50;
        color: white;
        border: none;
        padding: 15px;
    }
    
    .course-table tbody tr {
        transition: all 0.2s;
    }
    
    .course-table tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.05);
    }
    
    .course-table td, .course-table th {
        vertical-align: middle;
        padding: 12px 15px;
        border-top: 1px solid #e3e6f0;
    }
    
    .action-btn {
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 0.9rem;
        transition: all 0.2s;
        margin: 2px;
    }
    
    .edit-btn {
        background-color: #4e73df;
        color: white;
        border: none;
    }
    
    .edit-btn:hover {
        background-color: #2e59d9;
        transform: translateY(-2px);
    }
    
    .delete-btn {
        background-color: #e74a3b;
        color: white;
        border: none;
    }
    
    .delete-btn:hover {
        background-color: #be2617;
        transform: translateY(-2px);
    }
    
    .manage-btn {
        background-color: #1cc88a;
        color: white;
        border: none;
    }
    
    .manage-btn:hover {
        background-color: #17a673;
        transform: translateY(-2px);
    }
    
    .add-course-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        transition: all 0.3s;
    }
    
    .add-course-btn:hover {
        transform: scale(1.1) rotate(90deg);
        box-shadow: 0 6px 15px rgba(0,0,0,0.3);
    }
    
    .no-courses {
        padding: 40px;
        text-align: center;
        background-color: white;
        border-radius: 5px;
    }
    
    .no-courses i {
        font-size: 3rem;
        color: #6c757d;
        margin-bottom: 15px;
    }
    
    /* Dropdown styles */
    .dropdown-menu {
        border-radius: 5px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border: none;
    }
    
    .dropdown-item {
        padding: 8px 15px;
        transition: all 0.2s;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #4e73df;
    }
    
    .dropdown-divider {
        margin: 5px 0;
    }
</style>

<div class="col-sm-9 mt-5">
    <div class="course-list-container">
        <div class="course-list-header">
            <h4 class="mb-0"><i class="fas fa-book-open mr-2"></i> List of Courses</h4>
        </div>
        
        <?php
        $sql = "SELECT * FROM course";
        $result = $conn->query($sql);
        if($result->num_rows > 0){
        ?>
        <div class="table-responsive course-table">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Course ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Author</th>
                        <th scope="col">Duration</th>
                        <th scope="col">Price</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()){ ?>
                    <tr>
                        <th scope="row"><?php echo $row['course_id']; ?></th>
                        <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['course_author']); ?></td>
                        <td><?php echo htmlspecialchars($row['course_duration'] ?? 'N/A'); ?></td>
                        <td>Tsh <?php echo number_format($row['course_price'] ?? 0, 2); ?></td>
                        <td>
                            <form action="editcourse.php" method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?php echo $row['course_id']; ?>">
                                <button type="submit" class="btn edit-btn" name="view" value="view">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </form>
                            
                            <div class="dropdown d-inline">
                                <button class="btn manage-btn dropdown-toggle" type="button" id="manageDropdown<?php echo $row['course_id']; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-tasks"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="manageDropdown<?php echo $row['course_id']; ?>">
                                    <h6 class="dropdown-header">Manage Course</h6>
                                    <a class="dropdown-item" href="assignments.php?course_id=<?php echo $row['course_id']; ?>">
                                        <i class="fas fa-tasks mr-2"></i>Assignments
                                    </a>
                                    <a class="dropdown-item" href="quizzes.php?course_id=<?php echo $row['course_id']; ?>">
                                        <i class="fas fa-question-circle mr-2"></i>Quizzes
                                    </a>
                                    <a class="dropdown-item" href="exams.php?course_id=<?php echo $row['course_id']; ?>">
                                        <i class="fas fa-file-alt mr-2"></i>Exams
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="courseStudents.php?course_id=<?php echo $row['course_id']; ?>">
                                        <i class="fas fa-users mr-2"></i>Students
                                    </a>
                                </div>
                            </div>
                            
                            <form action="" method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?php echo $row['course_id']; ?>">
                                <button type="submit" class="btn delete-btn" name="delete" value="delete" 
                                    onclick="return confirm('Are you sure you want to delete this course?');">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
        <div class="no-courses">
            <i class="fas fa-book-open"></i>
            <h5>No Courses Found</h5>
            <p>Add your first course to get started</p>
        </div>
        <?php } 
        
        // Delete functionality
        if(isset($_REQUEST['delete'])){
            $sql = "DELETE FROM course WHERE course_id = {$_REQUEST['id']}";
            if($conn->query($sql) === TRUE){
                echo '<div class="alert alert-success">Course deleted successfully</div>';
                echo '<meta http-equiv="refresh" content="2;URL='.$_SERVER['PHP_SELF'].'" />';
            } else {
                echo '<div class="alert alert-danger">Unable to delete course: '.$conn->error.'</div>';
            }
        }
        ?>
    </div>
</div>

<!-- Floating Add Course Button -->
<a class="btn btn-danger add-course-btn" href="./addCourse.php">
    <i class="fas fa-plus fa-lg"></i>
</a>

<?php
include("./admininclude/footer.php");
?>