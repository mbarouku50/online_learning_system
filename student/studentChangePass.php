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

// Check if form was submitted
if(isset($_POST["stuPassUpdateBtn"])){
    if(empty($_POST['stuNewPass'])){
        $passmsg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2">Please enter a new password</div>';
    } else {
        // Hash the password for security
        $stupass = password_hash($_POST['stuNewPass'], PASSWORD_DEFAULT);
        
        // Use prepared statement to prevent SQL injection
        $sql = "UPDATE student SET stupass = ? WHERE stuemail = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $stupass, $stuemail);
        
        if($stmt->execute()){
            $passmsg = '<div class="alert alert-success col-sm-6 ml-5 mt-2">Password updated successfully</div>';
        } else {
            $passmsg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Unable to update password: ' . $conn->error . '</div>';
        }
        $stmt->close();
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form class="profile-form" method="POST">
                <h3 class="mb-4 text-center text-primary">Change Password</h3>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="inputEmail">Email</label>
                            <input type="text" class="form-control" id="inputEmail" 
                            value="<?php echo htmlspecialchars($stuemail ?? ''); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label for="inputnewpassword">New Password</label>
                            <input type="password" name="stuNewPass" id="inputnewpassword" class="form-control" 
                            placeholder="New Password" required minlength="8">
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4" name="stuPassUpdateBtn">
                        <i class="fas fa-key mr-2"></i> Update Password
                    </button>
                    <button type="reset" class="btn btn-secondary px-4">Reset</button>
                </div>
                
                <?php if (isset($passmsg)) echo $passmsg; ?>
            </form>
        </div>
    </div>
</div>

<?php
include('./stuInclude/footer.php');
?>