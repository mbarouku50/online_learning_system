<!-- start footer -->
<footer class="container-fluid bg-blue text-center p-2">
        <small class="text-white">Copyright &copy; 2025 || Designed By m_boy || 
          <a href="#login" data-bs-toggle="modal" data-bs-target="#adminLoginModalCenter">Admin Login</a></small>
      </footer>
     <!-- end footer -->


      <!-- start student registration modal -->
<div class="modal fade" id="studRegModalCenter" tabindex="-1" aria-labelledby="studRegModalCenterLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="studRegModalCenterLabel">Student registration</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- start student registration form -->
      <form>
  <div class="form-group">
    <i class="fas fa-user"></i>
    <label for="studname" class="pl-2 font-weight-bold">Name</label>
    <input type="text" class="form-control" placeholder="Name" name="studname" id="studname">
  </div>
  <div class="form-group">
    <i class="fas fa-user"></i>
    <label for="studreg" class="pl-2 font-weight-bold">Reg No.</label>
    <input type="text" class="form-control" placeholder="Reg no." name="studreg" id="studreg">
  </div>
  <div class="form-group">
    <i class="fas fa-envelope"></i>
    <label for="stuemail" class="pl-2 font-weight-bold">Email</label>
    <input type="email" class="form-control" placeholder="Email" name="stuemail" id="stuemail">
    <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
  </div>
  <div class="form-group">
    <i class="fas fa-key"></i>
    <label for="stupass" class="pl-2 font-weight-bold">New password</label>
    <input type="password" class="form-control" placeholder="password" name="stupass" id="stupass">
   
  </div>
</form>
<!-- end student registration form -->
      </div>
      <div class="modal-footer">
        <span id="successMsg"></span>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="addstu()">sign up</button>
      </div>
    </div>
  </div>
</div>
       <!-- end student registration modal -->


       <!-- start student login modal -->
<div class="modal fade" id="studLoginModalCenter" tabindex="-1" 
aria-labelledby="studLoginModalCenterLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="studLoginModalCenterLabel">Student Login</h1>
        <button type="button" class="btn-close" 
        data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- start student login form -->
      <form id="stuLoginForm">
  <div class="form-group">
    <i class="fas fa-envelope"></i>
    <label for="stuLogemail" class="pl-2 font-weight-bold">Email</label>
    <input type="email" class="form-control" placeholder="Email" 
    name="stuLogemail" id="stuLogemail" required>
  </div>
  <div class="form-group">
    <i class="fas fa-key"></i>
    <label for="stuLogpass" class="pl-2 font-weight-bold">password</label>
    <input type="password" class="form-control" placeholder="password" 
    name="stuLogpass" id="stuLogpass" required>
  </div>

</form>
<div class="text-center mt-3">
 <p>Don't have an account? <a href="#" data-bs-toggle="modal" 
 data-bs-target="#registerModal">Register here</a></p>
 </div>
<!-- end student login form -->
      </div>
      <div class="modal-footer">
      <div id="statusLogMsg" class="mb-3"></div>
        <button type="button" class="btn btn-secondary" 
        data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" 
        id="stuLoginBtn" onclick="checkStuLogin()">Login</button>
      </div>
    </div>
  </div>
</div>
       <!-- end student login modal -->


       <!-- start Admin login modal -->
<div class="modal fade" id="adminLoginModalCenter" tabindex="-1" 
aria-labelledby="adminLoginModalCenterLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" 
        id="adminLoginModalCenterLabel">Admin Login</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" 
        aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- start admin login form -->
      <form id="adminLoginForm">
  <div class="form-group">
    <i class="fas fa-envelope"></i>
    <label for="adminLogemail" class="pl-2 font-weight-bold">Email</label>
    <input type="email" class="form-control" placeholder="Email" 
    name="adminLogemail" id="adminLogemail">
  
  </div>
  <div class="form-group">
    <i class="fas fa-key"></i>
    <label for="adminLogpass" class="pl-2 font-weight-bold">password</label>
    <input type="password" class="form-control" placeholder="password" 
    name="adminLogpass" id="adminLogpass">
  </div>
</form>
<!-- end admin login form -->
      </div>
      <div class="modal-footer">
      <div id="statusadminLogMsg" class="mb-3"></div>
        <button type="button" class="btn btn-secondary" 
        data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" 
        id="adminLoginBtn" onclick="checkAdminLogin()">Login</button>
      </div>
    </div>
  </div>
</div>
       <!-- end Admin login modal -->


<!-- jquery and bootstrap javascript -->
<script src="js/jquery.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<!-- font awesome js -->
<script src="js/all.min.js"></script>
<!-- Initialize Owl Carousel -->

<!-- student Ajax call js -->
<script type="text/javascript" src="js/ajaxrequest.js"></script>

<!-- Admin Ajax call js -->
<script type="text/javascript" src="js/adminajaxrequest.js"></script>
</body>
</html>
