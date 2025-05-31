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

// Handle delete action
if(isset($_REQUEST['delete'])){
    // Use prepared statement to prevent SQL injection
    $sql = "DELETE FROM feedback WHERE f_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_REQUEST['id']);
    
    if($stmt->execute()){
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                Feedback deleted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        echo '<meta http-equiv="refresh" content="2;URL=feedback.php" />';
    } else {
        echo '<div class="alert alert-danger">Unable to delete feedback: '.$conn->error.'</div>';
    }
    $stmt->close();
}
?>

<style>
    .feedback-container {
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .feedback-header {
        background-color: #2c3e50;
        color: white;
        padding: 12px 20px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    
    .feedback-table {
        width: 100%;
    }
    
    .feedback-table th {
        background-color: #4e73df;
        color: white;
        padding: 12px;
    }
    
    .feedback-table td {
        padding: 12px;
        vertical-align: middle;
    }
    
    .feedback-content {
        max-width: 400px;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    
    .action-btn {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.9rem;
    }
    
    .add-feedback-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        transition: all 0.3s;
    }
    
    .add-feedback-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 12px rgba(0,0,0,0.3);
    }
    
    .no-feedback {
        text-align: center;
        padding: 40px;
        color: #6c757d;
    }
    
    .no-feedback i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #dee2e6;
    }
</style>

<div class="col-sm-9 mt-5">
    <div class="feedback-container">
        <h3 class="feedback-header">Feedback List</h3>
        
        <?php
        $sql = "SELECT f.*, s.studname, s.stuemail 
                FROM feedback f 
                LEFT JOIN student s ON f.stud_id = s.stud_id 
                ORDER BY f.f_id DESC";
        $result = $conn->query($sql);
        
        if($result->num_rows > 0){
        ?>
        <div class="table-responsive">
            <table class="table feedback-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Content</th>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while($row = $result->fetch_assoc()){
                        echo '<tr>';
                        echo '<td>'.$row['f_id'].'</td>';
                        echo '<td class="feedback-content">'.htmlspecialchars($row['f_content']).'</td>';
                        echo '<td>'.htmlspecialchars($row['studname'] ?? 'Unknown').'</td>';
                        echo '<td>'.htmlspecialchars($row['stuemail'] ?? '').'</td>';
                        
                        echo '<td>';
                        echo '
                        <form action="" method="POST" class="d-inline">
                            <input type="hidden" name="id" value="'.$row["f_id"].'">
                            <button type="submit" class="btn btn-danger action-btn" name="delete" value="delete"
                                onclick="return confirm(\'Are you sure you want to delete this feedback?\')">
                                <i class="far fa-trash-alt"></i> Delete
                            </button>
                        </form>
                        ';
                        echo '</td>';
                        echo '</tr>';
                    } 
                    ?>
                </tbody>
            </table>
        </div>
        <?php 
        } else {
            echo '<div class="no-feedback">
                    <i class="fas fa-comment-slash"></i>
                    <h4>No feedback found</h4>
                    <p>There are no feedback submissions to display.</p>
                  </div>';
        }
        ?>
    </div>
</div>



<?php
include("./admininclude/footer.php");
?>