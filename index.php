<!-- start including header -->
<?php
include("./templates/header.php");
?>
<!-- end including header -->


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

      <?php
        if(!isset($_SESSION['is_login'])){
          echo'<a href="#" class="btn btn-primary mt-3" data-bs-toggle="modal" 
          data-bs-target="#studRegModalCenter">Get Started</a>';
        } else{
          echo '<a href="#" class="btn btn-primary mt-3">My Profile</a>';
        }
      ?>
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
    <?php
    include("contact.php")
    ?>
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

     <!-- start include footer -->
  <?php
    include("./templates/footer.php");
  ?>
    <!-- end include footer -->