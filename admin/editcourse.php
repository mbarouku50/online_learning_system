<?php
if(!isset($_SESSION)){
    session_start();
}

include("./admininclude/header.php");
include("../dbconnection.php");

if(isset($_SESSION['is_admin_login'])){
    $adminEmail = $_SESSION['admin_email'];

}else{
    echo "<script> location.href='../index.php'; </script>";
}

// Update logic
if (isset($_POST['requpdate'])) {
    // Check for empty fields (trim to avoid whitespace issues)
    if (
        empty(trim($_POST['course_id'])) ||
        empty(trim($_POST['course_name'])) ||
        empty(trim($_POST['course_desc'])) ||
        empty(trim($_POST['course_author'])) ||
        empty(trim($_POST['course_duration'])) ||
        empty(trim($_POST['course_price'])) ||
        empty(trim($_POST['course_original_price']))
    ) {
        $msg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2" role="alert">Please fill all fields.</div>';
    } else {
        // Assign user values to variables
        $course_id = trim($_POST['course_id']);
        $course_name = trim($_POST['course_name']);
        $course_desc = trim($_POST['course_desc']);
        $course_author = trim($_POST['course_author']);
        $course_duration = trim($_POST['course_duration']);
        $course_price = trim($_POST['course_price']);
        $course_original_price = trim($_POST['course_original_price']);

        // Handle file upload
        $cimg = null;
        if (!empty($_FILES['course_img']['name'])) {
            $cimg = '../image/courseimg/' . time() . '_' . basename($_FILES['course_img']['name']);
            // Move uploaded file to destination
            if (!move_uploaded_file($_FILES['course_img']['tmp_name'], $cimg)) {
                $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert">Failed to upload image.</div>';
                $cimg = null; // Reset if upload fails
            }
        }

        // Use prepared statement to prevent SQL injection
        $sql = "UPDATE course SET course_name = ?, course_desc = ?, course_author = ?, 
                course_duration = ?, course_price = ?, course_original_price = ?" .
                ($cimg ? ", course_img = ?" : "") . " WHERE course_id = ?";
        
        $stmt = $conn->prepare($sql);
        if ($cimg) {
            $stmt->bind_param(
                "ssssddss",
                $course_name,
                $course_desc,
                $course_author,
                $course_duration,
                $course_price,
                $course_original_price,
                $cimg,
                $course_id
            );
        } else {
            $stmt->bind_param(
                "ssssdds",
                $course_name,
                $course_desc,
                $course_author,
                $course_duration,
                $course_price,
                $course_original_price,
                $course_id
            );
        }

        if ($stmt->execute()) {
            $msg = '<div class="alert alert-success col-sm-6 ml-5 mt-2" role="alert">Updated successfully.</div>';
        } else {
            $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert">Unable to update.</div>';
        }
        $stmt->close();
    }
}
?>

<div class="col-sm-6 mt-5 mx-3 jumbotron">
    <h3 class="text-center">Update Course Details</h3>
    <?php
    if(isset($_REQUEST['view'])){
        $sql = "SELECT * FROM course WHERE course_id = {$_REQUEST['id']}";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
    }
    ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="course_id">Course ID</label>
            <input type="text" class="form-control" 
            id="course_id" name="course_id" 
            value="<?php if(isset($row['course_id'])){ echo $row['course_id']; }?>"
        readonly></div>
        <div class="form-group">
            <label for="course_name">Course Name</label>
            <input type="text" class="form-control" 
            id="course_name" name="course_name" 
            value="<?php if(isset($row['course_name'])){ echo $row['course_name']; }?>"
        ></div>
        <div class="form-group">
            <label for="course_name">Course Description</label>
            <textarea class="form-control" name="course_desc" 
            id="course_desc" row="2">
            <?php if(isset($row['course_desc'])){ echo $row['course_desc']; }?>
        </textarea>
        </div>
        <div class="form-group">
            <label for="course_author">Author</label>
            <input type="text" class="form-control" 
            id="course_author" name="course_author"
            value="<?php if(isset($row['course_author'])){ echo $row['course_author']; }?>"
        ></div>
        <div class="form-group">
            <label for="course_duration">Course Duration</label>
            <input type="text" class="form-control" 
            id="course_duration" name="course_duration"
            value="<?php if(isset($row['course_duration'])){ echo $row['course_duration']; }?>"
        ></div>
        <div class="form-group">
            <label for="course_original_price">Course Original Price</label>
            <input type="text" class="form-control" 
            id="course_original_price" name="course_original_price"
            value="<?php if(isset($row['course_original_price'])){ echo $row['course_original_price']; }?>"
        ></div>
        <div class="form-group">
            <label for="course_price">Course selling Price</label>
            <input type="text" class="form-control" 
            id="course_price" name="course_price"
            value="<?php if(isset($row['course_price'])){ echo $row['course_price']; }?>"
        ></div>
        <div class="form-group">
            <label for="course_img">Course Image</label>
            <img src="<?php if(isset($row['course_img'])){echo $row['course_img']; }?>" alt=""
            class="img-thumbnail">
            <input type="file" class="form-control" 
            id="course_img" name="course_img">
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-danger"
            id="requpdate" name="requpdate">Update</button>
            <a href="courses.php" class="btn btn-secondary">Close</a>
        </div>
        <?php if(isset($msg)) {echo $msg;} ?>
    </form>
</div>

</div><!-- div row close frome header -->
</div><!-- div container-fluid close frome header -->

<?php
include("./admininclude/footer.php");
?>