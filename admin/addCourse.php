<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

if(isset($_REQUEST['courseSubmitBtn'])){
    // Checking for empty fields
    if(empty($_REQUEST['course_name']) || empty($_REQUEST['course_desc']) ||
        empty($_REQUEST['course_author']) || empty($_REQUEST['course_duration']) ||
        empty($_REQUEST['course_price']) || empty($_REQUEST['course_original_price'])){
        $msg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2">Fill All Fields</div>';
    } else {
        $course_name = $_REQUEST['course_name'];
        $course_desc = $_REQUEST['course_desc'];
        $course_author = $_REQUEST['course_author'];
        $course_duration = $_REQUEST['course_duration'];
        $course_price = $_REQUEST['course_price'];
        $course_original_price = $_REQUEST['course_original_price'];
        
        // Handle file upload
        $target_dir = "../image/courseimg/";
        $target_file = $target_dir . basename($_FILES["course_img"]["name"]);
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        if (move_uploaded_file($_FILES["course_img"]["tmp_name"], $target_file)) {
            // Use prepared statement to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO course (course_name, course_desc, course_author,
                    course_img, course_duration, course_price, course_original_price)
                    VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $course_name, $course_desc, $course_author, 
                    $target_file, $course_duration, $course_price, $course_original_price);
            
            if ($stmt->execute()) {
                $msg = '<div class="alert alert-success col-sm-6 ml-5 mt-2">Course added successfully</div>';
            } else {
                $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Error: ' . $stmt->error . '</div>';
            }
            $stmt->close();
        } else {
            $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Sorry, there was an error uploading your file.</div>';
        }
    }
}
?>

<!-- Rest of your HTML form remains the same -->

<div class="col-sm-6 mt-5 mx-3 jumbotron">
    <h3 class="text-center">Add New Course</h3>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="course_name">Course Name</label>
            <input type="text" class="form-control" 
            id="course_name" name="course_name">
        </div>
        <div class="form-group">
            <label for="course_name">Course Description</label>
            <textarea class="form-control" name="course_desc" 
            id="course_desc" row="2"></textarea>
        </div>
        <div class="form-group">
            <label for="course_author">Author</label>
            <input type="text" class="form-control" 
            id="course_author" name="course_author">
        </div>
        <div class="form-group">
            <label for="course_duration">Course Duration</label>
            <input type="text" class="form-control" 
            id="course_duration" name="course_duration">
        </div>
        <div class="form-group">
            <label for="course_original_price">Course Original Price</label>
            <input type="text" class="form-control" 
            id="course_original_price" name="course_original_price">
        </div>
        <div class="form-group">
            <label for="course_price">Course selling Price</label>
            <input type="text" class="form-control" 
            id="course_price" name="course_price">
        </div>
        <div class="form-group">
            <label for="course_img">Course Image</label>
            <input type="file" class="form-control" 
            id="course_img" name="course_img">
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-danger"
            id="courseSubmitBtn" name="courseSubmitBtn">Submit</button>
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