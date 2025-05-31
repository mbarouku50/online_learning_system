 <!-- start including header -->
<?php
include("./templates/header.php");
?>
<!-- end including header -->

 <!-- start contact us -->
<div class="container" id="contact">
    <h2 class="text-center" style="margin-top: 80px;">Contact Us</h2>
    <div class="row">
        <div class="col-md-8">
            <?php
            // Process form submission
            if(isset($_POST['submit'])) {
                // Database connection
                include("./dbconnection.php");
                
                // Get form data and sanitize
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                $subject = mysqli_real_escape_string($conn, $_POST['subject']);
                $email = mysqli_real_escape_string($conn, $_POST['email']);
                $message = mysqli_real_escape_string($conn, $_POST['message']);
                
                // Insert into separate tables
                $success = true;
                
                // 1. Insert into contacts table
                $sql1 = "INSERT INTO contacts (name, email, created_at) VALUES ('$name', '$email', NOW())";
                if(!$conn->query($sql1)) {
                    $success = false;
                    echo "<div class='alert alert-danger'>Error saving contact info: " . $conn->error . "</div>";
                }
                
                // 2. Insert into contact_subjects table
                $sql2 = "INSERT INTO contact_subjects (subject_name, created_at) VALUES ('$subject', NOW())";
                if($success && !$conn->query($sql2)) {
                    $success = false;
                    echo "<div class='alert alert-danger'>Error saving subject: " . $conn->error . "</div>";
                }
                
                // 3. Insert into contact_messages table
                $sql3 = "INSERT INTO contact_messages (email, message_content, created_at) VALUES ('$email', '$message', NOW())";
                if($success && !$conn->query($sql3)) {
                    $success = false;
                    echo "<div class='alert alert-danger'>Error saving message: " . $conn->error . "</div>";
                }
                
                if($success) {
                    echo "<div class='alert alert-success'>Thank you for contacting us! We'll get back to you soon.</div>";
                }
                
                $conn->close();
            }
            ?>
            
            <form action="" method="post">
                <div class="form-group">
                    <input type="text" class="form-control" name="name" placeholder="Name" required>
                </div>
                <br>
                <div class="form-group">
                    <input type="text" class="form-control" name="subject" placeholder="Subject" required>
                </div>
                <br>
                <div class="form-group">
                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                </div>
                <br>
                <div class="form-group">
                    <textarea class="form-control" name="message" placeholder="How can we help you?" style="height: 150px;" required></textarea>
                </div>
                <br>
                <input class="btn btn-primary" type="submit" value="Send" name="submit">
                <br><br>
            </form>
        </div>
        <div class="col-md-4 stripe text-white text-center">
            <h4>Online learning</h4>
            <p>CBE,
            Bibi Titi Mohamed Rd. 
            P. O. Box 1968, 
            Dar es Salaam.<br>
            phone:  +255627841861
            www.cbe.ac.tz
            </p>
        </div>
    </div>
</div>
<!-- end contact us -->


<?php include("./templates/footer.php"); ?>

