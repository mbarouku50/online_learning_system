<style>
    /* Footer Styles */
    .main-footer {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 2rem 0;
        font-size: 0.9rem;
    }
    
    .footer-copyright {
        padding: 1rem 0;
        border-top: 1px solid rgba(255,255,255,0.1);
    }
    
    .footer-link {
        color: rgba(255,255,255,0.7);
        text-decoration: none;
        transition: all 0.3s;
    }
    
    .footer-link:hover {
        color: white;
        text-decoration: underline;
    }
    
    /* Modal Styles */
    .modal-content {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    }
    
    .modal-header {
        background: var(--primary-color);
        color: white;
        border-bottom: none;
        padding: 1.5rem;
    }
    
    .modal-title {
        font-weight: 600;
    }
    
    .modal-body {
        padding: 2rem;
    }
    
    .form-group {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .form-group i {
        position: absolute;
        top: 38px;
        left: 15px;
        color: var(--primary-color);
    }
    
    .form-control {
        padding-left: 40px;
        border-radius: 5px;
        border: 1px solid #ddd;
        height: 50px;
    }
    
    .form-control:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }
    
    .btn-modal-primary {
        background-color: var(--primary-color);
        border: none;
        padding: 10px 25px;
        font-weight: 500;
    }
    
    .btn-modal-primary:hover {
        background-color: var(--secondary-color);
    }
    
    .form-text {
        color: #6c757d;
        font-size: 0.8rem;
    }
    
    .switch-modal-link {
        color: var(--primary-color);
        font-weight: 500;
        cursor: pointer;
    }
    
    .switch-modal-link:hover {
        text-decoration: underline;
    }
    
    #statusRegMsg, #statusStudLogMsg, #statusadminLogMsg {
        font-weight: 500;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
    }
</style>

<!-- Footer -->
<footer class="main-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-md-start text-center mb-3 mb-md-0">
                <small>&copy; 2025 CBE E-Learning. All rights reserved.</small>
            </div>
            <div class="col-md-6 text-md-end text-center">
                <small>Designed by <a href="#" class="footer-link">m_boy</a> | 
                <a href="#" class="footer-link" data-bs-toggle="modal" data-bs-target="#adminLoginModalCenter">Admin Login</a></small>
            </div>
        </div>
    </div>
</footer>

<!-- Student Registration Modal -->
<div class="modal fade" id="studRegModalCenter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Student Registration</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="studRegForm">
                    <div class="form-group">
                        <i class="fas fa-user"></i>
                        <label for="studname" class="form-label">Full Name</label>
                        <input type="text" class="form-control" placeholder="Enter your full name" name="studname" id="studname" required>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-id-card"></i>
                        <label for="studreg" class="form-label">Registration Number</label>
                        <input type="text" class="form-control" placeholder="Enter your registration number" name="studreg" id="studreg" required>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-envelope"></i>
                        <label for="stuemail" class="form-label">Email Address</label>
                        <input type="email" class="form-control" placeholder="Enter your email" name="stuemail" id="stuemail" required>
                        <div class="form-text">We'll never share your email with anyone else.</div>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-lock"></i>
                        <label for="stupass" class="form-label">Password</label>
                        <input type="password" class="form-control" placeholder="Create a password" name="stupass" id="stupass" required>
                    </div>
                    <div id="statusRegMsg"></div>
                    <div class="text-center mt-3">
                        <p> have an account? <a href="#" class="switch-modal-link" data-bs-toggle="modal" data-bs-target="#studLoginModalCenter" data-bs-dismiss="modal">Login here</a></p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-modal-primary" onclick="addstu()">Register</button>
            </div>
        </div>
    </div>
</div>

<!-- Student Login Modal -->
<div class="modal fade" id="studLoginModalCenter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Student Login</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="stuLoginForm">
                    <div class="form-group">
                        <i class="fas fa-envelope"></i>
                        <label for="stuLogemail" class="form-label">Email Address</label>
                        <input type="email" class="form-control" placeholder="Enter your email" name="stuemail" id="stuLogemail" required>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-lock"></i>
                        <label for="stuLogpass" class="form-label">Password</label>
                        <input type="password" class="form-control" placeholder="Enter your password" name="stupass" id="stuLogpass" required>
                    </div>
                    <div class="text-end mb-3">
                        <a href="#" class="switch-modal-link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" data-bs-dismiss="modal">Forgot Password?</a>
                    </div>
                    <div id="statusStudLogMsg"></div>
                    <div class="text-center mt-3">
                        <p>Don't have an account? <a href="#" class="switch-modal-link" data-bs-toggle="modal" data-bs-target="#studRegModalCenter" data-bs-dismiss="modal">Register here</a></p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-modal-primary" id="stuLoginBtn" onclick="checkStudLogin()">Login</button>
            </div>
        </div>
    </div>
</div>

<!-- Admin Login Modal -->
<div class="modal fade" id="adminLoginModalCenter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Admin Login</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="adminLoginForm">
                    <div class="form-group">
                        <i class="fas fa-envelope"></i>
                        <label for="admin_email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" placeholder="Enter admin email" name="admin_email" id="admin_email" required>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-lock"></i>
                        <label for="admin_pass" class="form-label">Password</label>
                        <input type="password" class="form-control" placeholder="Enter password" name="admin_pass" id="admin_pass" required>
                    </div>
                    <div class="text-end mb-3">
                        <a href="#" class="switch-modal-link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" data-bs-dismiss="modal">Forgot Password?</a>
                    </div>
                    <div id="statusadminLogMsg"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-modal-primary" id="adminLoginBtn" onclick="checkAdminLogin()">Login</button>
            </div>
        </div>
    </div>
</div>
<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="forgotPasswordForm">
                    <div class="form-group">
                        <i class="fas fa-envelope"></i>
                        <label for="resetEmail" class="form-label">Email Address</label>
                        <input type="email" class="form-control" placeholder="Enter your registered email" name="resetEmail" id="resetEmail" required>
                        <div class="form-text">We'll send a password reset link to this email.</div>
                    </div>
                    <div id="statusForgotPassMsg"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-modal-primary" onclick="sendResetLink()">Send Reset Link</button>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Password</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="resetPasswordForm">
                    <input type="hidden" id="resetToken" name="resetToken">
                    <div class="form-group">
                        <i class="fas fa-lock"></i>
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" placeholder="Enter new password" name="newPassword" id="newPassword" required>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-lock"></i>
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" placeholder="Confirm new password" name="confirmPassword" id="confirmPassword" required>
                    </div>
                    <div id="statusResetPassMsg"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-modal-primary" onclick="updatePassword()">Update Password</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Local JS Files - Fixed Paths -->
<script src="js/ajaxrequest.js"></script>
<script src="js/auth.js"></script>
<script src="js/adminajaxrequest.js"></script>

<script>
    // Ensure jQuery is loaded before executing
    $(document).ready(function(){
        // Make modals draggable
        $('.modal').draggable({
            handle: ".modal-header"
        });
        
        // Switch between login and register modals
        $('.switch-modal-link').click(function(){
            var targetModal = $(this).attr('data-bs-target');
            $('.modal').modal('hide');
            $(targetModal).modal('show');
        });
    });

    // Carousel initialization
    document.addEventListener('DOMContentLoaded', function() {
        var myCarousel = document.getElementById('feedbackCarousel');
        if(myCarousel) {
            var carousel = new bootstrap.Carousel(myCarousel, {
                interval: 3000,
                pause: 'hover'
            });
        }
    });




    //////////////////////////mmm
    function sendResetLink() {
    const email = $('#resetEmail').val().trim();
    
    if(!email) {
        $('#statusForgotPassMsg').html('<div class="alert alert-danger">Please enter your email address</div>');
        return;
    }
    
    $('#statusForgotPassMsg').html('<div class="alert alert-info">Sending reset link...</div>');
    
    $.ajax({
        url: 'php/forgot_password.php',
        type: 'POST',
        data: { email: email },
        success: function(response) {
            try {
                const data = JSON.parse(response);
                if(data.status === 'success') {
                    $('#statusForgotPassMsg').html(`<div class="alert alert-success">${data.message}</div>`);
                    // For testing, show the demo link (remove in production)
                    if(data.demo_reset_link) {
                        $('#statusForgotPassMsg').append(`<div class="mt-2"><small>Demo link: <a href="${data.demo_reset_link}" target="_blank">${data.demo_reset_link}</a></small></div>`);
                    }
                    setTimeout(() => {
                        $('#forgotPasswordModal').modal('hide');
                    }, 3000);
                } else {
                    $('#statusForgotPassMsg').html(`<div class="alert alert-danger">${data.message}</div>`);
                }
            } catch(e) {
                $('#statusForgotPassMsg').html('<div class="alert alert-danger">Error processing request</div>');
            }
        },
        error: function() {
            $('#statusForgotPassMsg').html('<div class="alert alert-danger">Failed to send reset link. Please try again.</div>');
        }
    });
}

function updatePassword() {
    const newPassword = $('#newPassword').val();
    const confirmPassword = $('#confirmPassword').val();
    const token = $('#resetToken').val();
    
    if(newPassword !== confirmPassword) {
        $('#statusResetPassMsg').html('<div class="alert alert-danger">Passwords do not match</div>');
        return;
    }
    
    if(newPassword.length < 6) {
        $('#statusResetPassMsg').html('<div class="alert alert-danger">Password must be at least 6 characters</div>');
        return;
    }
    
    $('#statusResetPassMsg').html('<div class="alert alert-info">Updating password...</div>');
    
    $.ajax({
        url: 'php/reset_password.php',
        type: 'POST',
        data: { 
            token: token,
            password: newPassword,
            confirm_password: confirmPassword
        },
        success: function(response) {
            try {
                const data = JSON.parse(response);
                if(data.status === 'success') {
                    $('#statusResetPassMsg').html(`<div class="alert alert-success">${data.message}</div>`);
                    setTimeout(() => {
                        $('#resetPasswordModal').modal('hide');
                        // Show appropriate login modal
                        if(window.location.pathname.includes('admin')) {
                            $('#adminLoginModalCenter').modal('show');
                        } else {
                            $('#studLoginModalCenter').modal('show');
                        }
                    }, 2000);
                } else {
                    $('#statusResetPassMsg').html(`<div class="alert alert-danger">${data.message}</div>`);
                }
            } catch(e) {
                $('#statusResetPassMsg').html('<div class="alert alert-danger">Error processing request</div>');
            }
        },
        error: function() {
            $('#statusResetPassMsg').html('<div class="alert alert-danger">Failed to update password. Please try again.</div>');
        }
    });
}

// Check URL for token on page load
function checkForPasswordResetToken() {
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');
    
    if(token) {
        // Verify token first
        $.ajax({
            url: 'php/verify_token.php?token=' + token,
            type: 'GET',
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    if(data.status === 'success') {
                        $('#resetToken').val(token);
                        $('#resetPasswordModal').modal('show');
                        // Clean the URL
                        window.history.replaceState({}, document.title, window.location.pathname);
                    } else {
                        alert('Invalid or expired token');
                    }
                } catch(e) {
                    alert('Error processing token');
                }
            },
            error: function() {
                alert('Failed to verify token');
            }
        });
    }
}

// Call this on page load
$(document).ready(function() {
    checkForPasswordResetToken();
    
    // Add forgot password links to your login modals
    $('.login-modal').each(function() {
        $(this).find('.modal-body').append(`
            <div class="text-end mb-3">
                <a href="#" class="switch-modal-link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" data-bs-dismiss="modal">Forgot Password?</a>
            </div>
        `);
    });
});
</script>


</body>
</html>