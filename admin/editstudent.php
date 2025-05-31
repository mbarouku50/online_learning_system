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

// Update logic
if (isset($_POST['requpdate'])) {
    // Check for empty required fields
    if (
        empty(trim($_POST['stud_id'])) ||
        empty(trim($_POST['studname'])) ||
        empty(trim($_POST['studreg'])) ||
        empty(trim($_POST['stuemail'])) ||
        empty(trim($_POST['stu_occ']))
    ) {
        $msg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2" role="alert">Please fill all required fields.</div>';
    } else {
        // Assign form values to variables
        $stud_id = trim($_POST['stud_id']);
        $studname = trim($_POST['studname']);
        $studreg = trim($_POST['studreg']);
        $stuemail = trim($_POST['stuemail']);
        $stupass = !empty(trim($_POST['stupass'])) ? password_hash(trim($_POST['stupass']), PASSWORD_DEFAULT) : null;
        $stu_occ = trim($_POST['stu_occ']);

        // Use prepared statement
        if ($stupass) {
            $sql = "UPDATE student SET studname = ?, studreg = ?, stuemail = ?, stupass = ?, stu_occ = ? WHERE stud_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $studname, $studreg, $stuemail, $stupass, $stu_occ, $stud_id);
        } else {
            $sql = "UPDATE student SET studname = ?, studreg = ?, stuemail = ?, stu_occ = ? WHERE stud_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $studname, $studreg, $stuemail, $stu_occ, $stud_id);
        }

        if ($stmt->execute()) {
            $msg = '<div class="alert alert-success col-sm-6 ml-5 mt-2" role="alert">Student updated successfully.</div>';
        } else {
            $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert">Unable to update: ' . $stmt->error . '</div>';
        }
        $stmt->close();
    }
}
?>

<div class="col-sm-6 mt-5 mx-3 jumbotron">
    <h3 class="text-center">Update Course Details</h3>
    <?php
    if(isset($_REQUEST['view'])){
        $sql = "SELECT * FROM student WHERE stud_id = {$_REQUEST['id']}";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
    }
    ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="stud_id">ID</label>
            <input type="text" class="form-control" id="stud_id" name="stud_id"
            value="<?php if(isset($row['stud_id'])){echo $row['stud_id'];} ?>" readonly>
        </div>
        <div class="form-group">
            <label for="studname">Name</label>
            <input type="text" class="form-control" id="studname" name="studname"
            value="<?php if(isset($row['studname'])){echo $row['studname'];} ?>">
        </div>
        <div class="form-group">
            <label for="studreg">student reg.No:</label>
            <input type="text" class="form-control" id="studreg" name="studreg"
            value="<?php if(isset($row['studreg'])){echo $row['studreg'];} ?>">
        </div>
        <div class="form-group">
            <label for="stuemail">Email</label>
            <input type="text" class="form-control" id="stuemail" name="stuemail"
            value="<?php if(isset($row['stuemail'])){echo $row['stuemail'];} ?>">
        </div>
        <div class="form-group">
            <label for="stupass">Password</label>
            <input type="text" class="form-control" id="stupass" name="stupass"
            value="<?php if(isset($row['stupass'])){echo $row['stupass'];} ?>">
        </div>
        <div class="form-group">
            <label for="stu_occ">Occupation</label>
            <input type="text" class="form-control" id="stu_occ" name="stu_occ"
            value="<?php if(isset($row['stu_occ'])){echo $row['stu_occ'];} ?>">
        </div>
        <div class="tex-center">
            <button type="submit" class="btn btn-danger" id="requpdate"
            name="requpdate">Update</button>
            <a href="student.php" class="btn btn-secondary">Close</a>
        </div>
        <?php if(isset($msg)){
            echo $msg;
        }?>
    </form>
</div>

</div><!-- div row close frome header -->
</div><!-- div container-fluid close frome header -->

<?php
include("./admininclude/footer.php");
?>