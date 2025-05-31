<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION)) {
    session_start();
}

include("./admininclude/header.php");
include("../dbconnection.php");

if (isset($_SESSION['is_admin_login'])) {
    $adminEmail = $_SESSION['admin_email'];
} else {
    echo "<script> location.href='../index.php'; </script>";
}

if (isset($_POST['newStusubmitBtn'])) {
    // Checking for empty fields
    if (
        empty(trim($_POST['studname'])) ||
        empty(trim($_POST['stuemail'])) ||
        empty(trim($_POST['stupass'])) ||
        empty(trim($_POST['stu_occ']))
    ) {
        $msg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2">Please fill all fields.</div>';
    } else {
        // Assign form values to variables
        $studname = trim($_POST['studname']);
        $stuemail = trim($_POST['stuemail']);
        $stupass = trim($_POST['stupass']); // Consider hashing the password
        $stu_occ = trim($_POST['stu_occ']);

        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO student (studname, stuemail, stupass, stu_occ)
         VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $studname, $stuemail, $stupass, $stu_occ);

        if ($stmt->execute()) {
            $msg = '<div class="alert alert-success col-sm-6 ml-5 mt-2">Student added successfully.</div>';
        } else {
            $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Error: ' . $stmt->error . '</div>';
        }
        $stmt->close();
    }
}
?>

<div class="col-sm-6 mt-5 mx-3 jumbotron">
    <h3 class="text-center">Add New Student</h3>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="studname">Name</label>
            <input type="text" class="form-control" id="studname" name="studname">
        </div>
        <div class="form-group">
            <label for="stuemail">Email</label>
            <input type="text" class="form-control" id="stuemail" name="stuemail">
        </div>
        <div class="form-group">
            <label for="stupass">Password</label>
            <input type="text" class="form-control" id="stupass" name="stupass">
        </div>
        <div class="form-group">
            <label for="stu_occ">Occupation</label>
            <input type="text" class="form-control" id="stu_occ" name="stu_occ">
        </div>
        <div class="tex-center">
            <button type="submit" class="btn btn-danger" id="newStusubmitBtn"
            name="newStusubmitBtn">Submit</button>
            <a href="student.php" class="btn btn-secondary">Close</a>
        </div>
        <?php if(isset($msg)){
            echo $msg;
        }?>
    </form>
</div>

</div><!-- div row close from header -->
</div><!-- div container-fluid close frome header -->

<?php
include("./admininclude/footer.php");
?>