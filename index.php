<?php 
include("./templates/header.php");
include("./dbconnection.php");
?>
<style>
    /* Video Background */
    .vid-parent {
        position: relative;
        height: 60vh;
        overflow: hidden;
    }
    
    .vid-parent video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .vid-overlay{
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    background-color: #225470;
    z-index: 1;
    opacity: 0.8;
}
    
    .vid-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: white;
        z-index: 1;
    }
    
    .my-content {
        font-size: 3rem;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        margin-bottom: 1rem;
    }
    
    /* Text Banner */
    .txt-banner {
        background: var(--primary-color);
        color: white;
        padding: 1.5rem 0;
    }
    
    .bottom-banner {
        text-align: center;
    }
    
    .bottom-banner h5 {
        margin: 0;
        font-weight: 500;
    }
    
    /* Courses Section */
    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        margin-bottom: 20px;
    }
    
    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }
    
    .card-img-top {
        height: 200px;
        object-fit: cover;
    }
    
    .card-footer {
        background: white;
        border-top: none;
    }
    
    /* Testimonials */
    .feedback-content {
        background: white;
        padding: 30px;
        border-radius: 10px;
        height: 100%;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }
    
    .feedback-content:hover {
        transform: translateY(-5px);
    }
    
    .feedback-text {
        color: var(--dark-color);
        font-style: italic;
        margin-bottom: 20px;
    }
    
    .imag img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        float: left;
        margin-right: 15px;
    }
    
    .testimonial-prof {
        padding-top: 10px;
    }
    
    .card-name {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 0;
    }
    
    /* Social Links */
    .social-hover {
        transition: all 0.3s;
        display: inline-block;
        padding: 10px;
    }
    
    .social-hover:hover {
        transform: translateY(-3px);
        color: var(--accent-color) !important;
    }
    .feedback-content {
        background: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .feedback-text {
        font-style: italic;
        color: var(--dark-color);
        line-height: 1.6;
        font-size: 1.1rem;
    }
    
    .card-name {
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .carousel-control-prev, 
    .carousel-control-next {
        width: 5%;
    }
    
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-color: var(--primary-color);
        border-radius: 50%;
        padding: 15px;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .my-content {
            font-size: 2rem;
        }
        
        .vid-content small {
            font-size: 1rem;
        }
    }
</style>

<!-- Video Background -->
<div class="container-fluid remove-vid-marg p-0">
    <div class="vid-parent">
        <video playsinline autoplay muted loop>
            <source src="video/online.mp4" type="video/mp4">
        </video>
        <div class="vid-overlay"></div>
        <div class="vid-content">
            <h1 class="my-content">Welcome to CBE E-Learning</h1>
            <small class="my-content">Learn and Implement</small><br>
            <?php
                if(!isset($_SESSION['is_login'])) {
                    echo '<a href="#" class="btn btn-primary btn-lg mt-3" data-bs-toggle="modal" data-bs-target="#studRegModalCenter">Get Started</a>';
                } else {
                    echo '<a href="student/studentProfile.php" class="btn btn-primary btn-lg mt-3">My Profile</a>';
                }
            ?>
        </div>
    </div>
</div>

<!-- Text Banner -->
<div class="container-fluid txt-banner">
    <div class="row bottom-banner">
        <div class="col-md-3 col-sm-6">
            <h5><i class="fas fa-book-open mr-3"></i> 100+ Online Courses</h5>
        </div>
        <div class="col-md-3 col-sm-6">
            <h5><i class="fas fa-users mr-3"></i> Expert Instructors</h5>
        </div>
        <div class="col-md-3 col-sm-6">
            <h5><i class="fas fa-keyboard mr-3"></i> Lifetime Access</h5>
        </div>
        <div class="col-md-3 col-sm-6">
            <h5><i class="fas fa-dollar-sign mr-3"></i> Money Back Guarantee*</h5>
        </div>
    </div>
</div>

<!-- Popular Courses -->
<div class="container mt-5 mb-5">
    <h1 class="text-center mb-5">Popular Courses</h1>
    
    <div class="row">
        <?php 
        $sql = "SELECT * FROM course LIMIT 3";
        $result = $conn->query($sql);
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $course_id = $row['course_id'];
                echo '
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <img src="'.str_replace('..', '.', $row['course_img']).'" class="card-img-top" alt="'.$row['course_name'].'">
                        <div class="card-body">
                            <h5 class="card-title">'.$row['course_name'].'</h5>
                            <p class="card-text">'.$row['course_desc'].'</p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted"><del>'.$row['course_original_price'].'</del></span>
                                    <span class="h5 text-primary ml-2">'.$row['course_price'].'</span>
                                </div>
                                <a href="coursedetails.php?course_id='.$course_id.'" class="btn btn-primary">Enroll</a>
                            </div>
                        </div>
                    </div>
                </div>
                ';
            }
        } else {
            echo '<div class="col-12 text-center"><p>No courses available at the moment.</p></div>';
        }
        ?>
    </div>
    
    <div class="text-center mt-4">
        <a class="btn btn-primary btn-lg" href="courses.php">View All Courses</a>
    </div>
</div>
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

<!-- Testimonials -->
<div class="container-fluid py-5" style="background-color: var(--primary-color);" id="Feedback">
    <div class="container">
        <h1 class="text-center text-white mb-5">Student's Feedback</h1>
        
        <!-- Carousel Wrapper -->
        <div id="feedbackCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                $sql = "SELECT s.studname, s.stu_occ, s.stu_img, f.f_content FROM student 
                        AS s JOIN feedback AS f ON s.stud_id = f.stud_id";
                $result = $conn->query($sql);
                if($result->num_rows > 0) {
                    $active = true;
                    while($row = $result->fetch_assoc()) {
                        $s_img = $row["stu_img"];
                        $n_img = str_replace('..', '.', $s_img);
                ?>
                <div class="carousel-item <?php echo $active ? 'active' : ''; ?>">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="feedback-content text-center p-4">
                                <img src="<?php echo $n_img ?>" alt="<?php echo $row['studname'] ?>" 
                                     class="rounded-circle mb-3" width="100" height="100">
                                <div class="feedback-text mb-3">
                                    <i class="fas fa-quote-left text-primary mr-2"></i>
                                    <?php echo $row['f_content']; ?>
                                    <i class="fas fa-quote-right text-primary ml-2"></i>
                                </div>
                                <h5 class="card-name mb-1"><?php echo $row['studname'] ?></h5>
                                <small class="text-muted"><?php echo $row['stu_occ'] ?></small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                        $active = false;
                    }
                } else {
                    echo '<div class="carousel-item active">
                            <div class="row justify-content-center">
                                <div class="col-lg-8 text-center text-white">
                                    <p>No feedback available yet.</p>
                                </div>
                            </div>
                          </div>';
                } 
                ?>
            </div>
            
            <!-- Carousel Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#feedbackCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#feedbackCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</div>

<!-- Social Media -->
<div class="container-fluid py-3" style="background-color: var(--secondary-color);">
    <div class="container">
        <div class="row text-center">
            <div class="col-6 col-md-3 mb-2 mb-md-0">
                <a class="text-white social-hover" href="#">
                    <i class="fab fa-facebook-f"></i> Facebook
                </a>
            </div>
            <div class="col-6 col-md-3 mb-2 mb-md-0">
                <a class="text-white social-hover" href="#">
                    <i class="fab fa-twitter"></i> Twitter
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a class="text-white social-hover" href="https://wa.me/255627841861">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a class="text-white social-hover" href="#">
                    <i class="fab fa-instagram"></i> Instagram
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="container-fluid py-4" style="background-color: #E9ECEF;">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5>About Us</h5>
                <p>CBE E-Learning provides universal access to quality education, offering notes, examinations, 
                and assignments online in partnership with top institutions.</p>
            </div>
            
            <div class="col-md-4 mb-4 mb-md-0">
                <h5>Categories</h5>
                <ul class="list-unstyled">
                    <li><a class="text-dark" href="#">Information Technology</a></li>
                    <li><a class="text-dark" href="#">Business Administration</a></li>
                    <li><a class="text-dark" href="#">Procurement</a></li>
                    <li><a class="text-dark" href="#">Metrology</a></li>
                    <li><a class="text-dark" href="#">Accounting</a></li>
                </ul>
            </div>
            
            <div class="col-md-4">
                <h5>Contact Us</h5>
                <address>
                    CBE, Bibi Titi Mohamed Rd.<br>
                    P.O. Box 1968, Dar es Salaam<br>
                    Phone: +255 627 841 861<br>
                    Website: www.cbe.ac.tz
                </address>
            </div>
        </div>
    </div>
</footer>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myCarousel = document.getElementById('feedbackCarousel');
        var carousel = new bootstrap.Carousel(myCarousel, {
            interval: 3000, // Change slide every 5 seconds
            pause: 'hover' // Pause on hover
        });
    });
</script>

<?php include("./templates/footer.php"); ?>