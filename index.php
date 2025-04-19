<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- bootstrap css -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- font awesome css -->
    <link rel="stylesheet" href="css/all.min.css">

    <!-- google-font -->
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">

    <!-- custom css -->
    <link rel="stylesheet" href="css/style.css">
    <title>CBE-E-LEARNING</title>
</head>
<body>
<!-- start navigation     -->
<nav class="navbar navbar-expand-sm navbar-dark bg-blue p1-5 fixed-top">
    <a class="navbar-brand" href="index.php">CBE</a>
    <span class="navbar-text">online-leaning</span>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <ul class="navbar-nav custom-nav p1-5">
        <li class="nav-item custom-nav-item"><a href="index.php" class="nav-link">Home</a></li>
        <li class="nav-item custom-nav-item"><a href="#" class="nav-link">courses</a></li>
        <li class="nav-item custom-nav-item"><a href="#" class="nav-link">payment</a></li>
        <li class="nav-item custom-nav-item"><a href="#" class="nav-link">My Profile</a></li>
        <li class="nav-item custom-nav-item"><a href="#" class="nav-link">Logout</a></li>
        <li class="nav-item custom-nav-item"><a href="#" class="nav-link">Login</a></li>
        <li class="nav-item custom-nav-item"><a href="#" class="nav-link">Signup</a></li>
        <li class="nav-item custom-nav-item"><a href="#" class="nav-link">Feedback</a></li>
        <li class="nav-item custom-nav-item"><a href="#" class="nav-link">Contact</a></li>
     </ul>
    </div>
</nav>
 <!-- end navigation     -->

 <!-- start video background -->
  <div class="container-fluid remove-vid-marg">
    <div class="vid-parent">
      <video playsinline autoplay muted loop>
        <source src="video/back.mp4" type="video/mp4">
      </video>
      <div class="vid-overlay"></div>
    </div>
    <div class="vid-content">
      <h1 class="my-content">Welcome to CBE online-learning</h1>
      <small class="my-content">Learn and Implement</small><br>
      <a href="#" class="btn btn-primary">Get Started</a>
    </div>
  </div>
  <!-- end video background -->

  <!-- start text banner -->
  <div class="container-fluid bg-blue txt-banner">
    <div class="row bottom-banner">
      <div class="col-sm">
        <h5><i class="fas fa-book-open mr-3"></i> 100+ Online Courses</h5>
      </div>
      <div class="col-sm">
        <h5><i class="fas fa-users mr-3"></i> Expert Instructors</h5>
      </div>
      <div class="col-sm">
        <h5><i class="fas fa-keyboard mr-3"></i> Lifetime Access</h5>
      </div>
      <div class="col-sm">
        <h5><i class="fa-solid fa-dollar-sign"></i> Money back Guarantee*</h5>
      </div>
    </div>
  </div>

   <!-- end text banner -->
  <!-- start most popular course -->
   <div class="container mt-5">
    <h1 class="text-center">popular course</h1>
    <!-- start most popular course 1st card deck -->
     <div class="card-deck mt-4">
        <a href="#" class="btn" style="text-align: left; padding:0px; margin:0px;">
          <div class="card">
            <img src="image/IT.jpeg" class="card-img-top" alt="IT" />
            <div class="card-body">
              <h5 class="card-title">Learn IT Easy way</h5>
              <p class="card-title">study information technology in easy way CIT,DIT,BIT all level</p>
            </div>
              <div class="card-footer">
                <p class="card-text d-inline">price: <small><del>Tsh.5000</del></small>
                <span class="font-weight-bolder">Tsh.2000</span></p>
                <a class="btn btn-primary text-white font-weight-bolder float-right" href="#">Enroll</a>
              </div>
          </div>
          <a href="#" class="btn" style="text-align: left; padding:0px; margin:0px;">
          <div class="card">
            <img src="image/metrology.jpg" class="card-img-top" alt="IT" />
            <div class="card-body">
              <h5 class="card-title">Learn Methrology Easy way</h5>
              <p class="card-title">study metrology and standardzation in easy way CMS,DMS,BMS all level</p>
            </div>
              <div class="card-footer">
                <p class="card-text d-inline">price: <small><del>Tsh.5000</del></small>
                <span class="font-weight-bolder">Tsh.2000</span></p>
                <a class="btn btn-primary text-white font-weight-bolder float-right" href="#">Enroll</a>
              </div>
          </div>
        </a>
        <a href="#" class="btn" style="text-align: left; padding:0px; margin:0px;">
          <div class="card">
            <img src="image/procure.jpeg" class="card-img-top" alt="IT" />
            <div class="card-body">
              <h5 class="card-title">Learn procurement Easy way</h5>
              <p class="card-title">study procurement in easy way CPS,DPS,BPS all level</p>
            </div>
              <div class="card-footer">
                <p class="card-text d-inline">price: <small><del>Tsh.5000</del></small>
                <span class="font-weight-bolder">Tsh.2000</span></p>
                <a class="btn btn-primary text-white font-weight-bolder float-right" href="#">Enroll</a>
              </div>
          </div>
        </a>
     </div>
    <!-- end most popular course 1st card deck -->
     <!-- start most popular course 2st card deck -->
     <div class="card-deck mt-4">
        <a href="#" class="btn" style="text-align: left; padding:0px; margin:0px;">
          <div class="card">
            <img src="image/BA.png" class="card-img-top" alt="BA" />
            <div class="card-body">
              <h5 class="card-title">Learn BA Easy way</h5>
              <p class="card-title">Busness Administration study in easy way CBA,DBA,BBA all level</p>
            </div>
              <div class="card-footer">
                <p class="card-text d-inline">price: <small><del>Tsh.5000</del></small>
                <span class="font-weight-bolder">Tsh.2000</span></p>
                <a class="btn btn-primary text-white font-weight-bolder float-right" href="#">Enroll</a>
              </div>
          </div>
        </a>
        <a href="#" class="btn" style="text-align: left; padding:0px; margin:0px;">
          <div class="card">
            <img src="image/BA.png" class="card-img-top" alt="BA" />
            <div class="card-body">
              <h5 class="card-title">Learn BA Easy way</h5>
              <p class="card-title">Busness Administration study in easy way CBA,DBA,BBA all level</p>
            </div>
              <div class="card-footer">
                <p class="card-text d-inline">price: <small><del>Tsh.5000</del></small>
                <span class="font-weight-bolder">Tsh.2000</span></p>
                <a class="btn btn-primary text-white font-weight-bolder float-right" href="#">Enroll</a>
              </div>
          </div>
        </a>
        <a href="#" class="btn" style="text-align: left; padding:0px; margin:0px;">
          <div class="card">
            <img src="image/BA.png" class="card-img-top" alt="BA" />
            <div class="card-body">
              <h5 class="card-title">Learn BA Easy way</h5>
              <p class="card-title">Busness Administration study in easy way CBA,DBA,BBA all level</p>
            </div>
              <div class="card-footer">
                <p class="card-text d-inline">price: <small><del>Tsh.5000</del></small>
                <span class="font-weight-bolder">Tsh.2000</span></p>
                <a class="btn btn-primary text-white font-weight-bolder float-right" href="#">Enroll</a>
              </div>
          </div>
        </a>
     </div>
    <!-- end most popular course 2st card deck -->
     <div class="text-center m-2">
      <a class="btn btn btn-primary" href="#">View All Course</a>
     </div>
   </div>
  <!-- end most popular course -->

  <!-- start contact us -->
   <div class="container" id="contact">
    <h2 class="text-center mb-4">Contact Us</h2>
      <div class="row">
        <div class="col-md-8">
          <form action="#" method="post">
            <input type="text" class="form-control" name="name" placeholder="Name"><br>
            <input type="text" class="form-control" name="subject" placeholder="subject"><br>
            <input type="email" class="form-control" name="email" placeholder="Email"><br>
            <textarea class="form-control" name="message" placeholder="How can we help you?" style="height: 150px;;"></textarea><br>
            <input class="btn btn-primary" type="submit" value="send" name="submit"><br><br>
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

    <!-- start student Testimonial -->
        <!-- start student Testimonial -->
        <div class="container-fluid mt-5" style="background-color: #225470; padding: 40px 0;" id="Feedback">
        <h1 class="text-center text-white p-4">Student's Feedback</h1>
        
        <div class="row1">
            <div class="row">
                <div class="col-lg-4">
                    <div class="feedback-content ">
                        <p class="feedback-text">
                            My life of School made me stronger and took me a step ahead for being an IT man.<br> 
                          
                            I am very grateful for the School for providing us the best of placement opportunities.
                        </p>
                        <div class="imag">
                          <img src="image/m.jpg" alt="">
                          <div class="testimonial-prof">
                        <h5 class="card-name">mbarouk</h5>
                        <small style="color: #fff;">Web Developer</small>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="feedback-content ">
                        <p class="feedback-text">
                        School is a place of learning, fun, culture, love, literature and many such life 
                        impacting activities. Studying at the School brought an added value in my life.
                        </p>
                        <div class="imag">
                          <img src="image/a.jpg" alt="">
                          <div class="testimonial-prof">
                        <h5 class="card-name">kan</h5>
                        <small style="color: #fff;">Busness Administrator</small>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="feedback-content ">
                        <p class="feedback-text">
                        I am grateful to School. Both the faculty and the Training & Placement Department.<br>
                        Due to the efforts
                        made by the faculty and placement unit, I was able to long a job in the second company.<br>
                        </p>
                        <div class="imag">
                          <img src="image/c.jpg" alt="">
                          <div class="testimonial-prof">
                        <h5 class="card-name">Xhidy</h5>
                        <small style="color: #fff;">pirot</small>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end student Testimonial -->
    <!-- start social follow -->
     <div class="container-fluid text-decoration: none; bg-blue ">
     <div class="row text-white text-center p-1">
      <div class="col-sm">
        <a class="text-white social-hover" href="#">
          <i class="fab fa-facebook-f"></i> Facebook</a>
      </div>
      <div class="col-sm">
        <a class="text-white social-hover" href="#">
          <i class="fab fa-twitter"></i> Twitter</a>
      </div>
      <div class="col-sm">
        <a class="text-white social-hover" href="#">
          <i class="fab fa-whatsapp"></i> Whatsapp</a>
      </div>
      <div class="col-sm">
        <a class="text-white social-hover" href="#">
          <i class="fab fa-instagram"></i> Instagram</a>
      </div>
     </div>
     </div>
    <!-- end social follow -->

    <!-- start about section -->
     <div class="container-fluid p-4" style="background-color:#E9ECEF">
      <div class="container" style="background-color:#E9ECEF">
        <div class="row text-center">
          <div class="col-sm">
            <h5>About Us</h5>
              <p>CBE-Oline learning provides universal access to get notes,examination,assigmentechnology
                 best education, partnerin with top universities and organizations to offer courses online</p>
          </div>
          <div class="col-sm">
            <h5>Categories</h5>
            <a class="text-dark" href="#">information technology</a><br>
            <a class="text-dark" href="#">information technology</a><br>
            <a class="text-dark" href="#">information technology</a><br>
            <a class="text-dark" href="#">information technology</a><br>
            <a class="text-dark" href="#">information technology</a><br>
            <a class="text-dark" href="#">information technology</a><br>
          </div>
          <div class="col-sm">
            <h5>contact Us</h5>
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
     </div>
     <!-- end about section -->

     <!-- start footer -->
      <footer class="container-fluid bg-blue text-center p-2">
        <small class="text-white">Copyright &copy; 2025 || Designed By m_boy || Admin</small>
      </footer>
     <!-- end footer -->

<!-- jquery and bootstrap javascript -->
<script src="js/jquery.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<!-- font awesome js -->
<script src="js/all.min.js"></script>
<!-- Initialize Owl Carousel -->
</body>
</html>