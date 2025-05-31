<?php
if (!isset($_SESSION)) {
    session_start();
}

include("./admininclude/header.php");
include("../dbconnection.php");

if (!isset($_SESSION['is_admin_login'])) {
    header("Location: ../index.php");
    exit();
}

$admin_email = $_SESSION['admin_email'];
$passmsg = '';

if (isset($_POST['adminPassUpdatebtn'])) {
    $admin_pass = $_POST['admin_pass'];

    if (empty($admin_pass)) {
        $passmsg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2" role="alert">Please fill all fields</div>';
    } else {
        // Check if admin exists
        $sql = "SELECT * FROM admin WHERE admin_email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $admin_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // Update the password (plain text)
            $sql = "UPDATE admin SET admin_pass = ? WHERE admin_email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $admin_pass, $admin_email);

            if ($stmt->execute()) {
                $passmsg = '<div class="alert alert-success col-sm-6 ml-5 mt-2" role="alert">Password updated successfully</div>';
            } else {
                $passmsg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert">Unable to update password</div>';
            }
        } else {
            $passmsg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert">Admin not found</div>';
        }

        $stmt->close();
    }
}
?>

<div class="col-sm-9 mt-5">
    <div class="row">
        <div class="col-sm-6">
            <form action="adminChangePassword.php" method="POST" class="mt-5 mx-5">
                <div class="form-group">
                    <label for="inputEmail">Email</label>
                    <input type="email" class="form-control" id="inputEmail" value="<?php echo htmlspecialchars($admin_email); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="inputnewpassword">New Password</label>
                    <input type="password" class="form-control" id="inputnewpassword" placeholder="New Password" name="admin_pass">
                </div>
                <button type="submit" class="btn btn-danger mr-4 mt-4" name="adminPassUpdatebtn">Update</button>
                <button type="reset" class="btn btn-secondary mt-4">Reset</button>
                <?php if (!empty($passmsg)) { echo $passmsg; } ?>
            </form>
        </div>
    </div>
</div>

<?php
include("./admininclude/footer.php");
$conn->close();
?>