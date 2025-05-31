<?php
include('./dbconnection.php');
include("./templates/header.php");
?>

<style>
    :root {
        --primary-color: #225470;
        --secondary-color: #2c3e50;
        --accent-color: #4e73df;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --success-color: #28a745;
        --error-color: #dc3545;
    }

    /* Banner */
    .banner {
        position: relative;
        height: 400px;
        overflow: hidden;
        background: var(--dark-color);
    }

    .banner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.7);
        transition: transform 0.5s ease;
    }

    .banner:hover img {
        transform: scale(1.05);
    }

    .banner-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: var(--light-color);
        width: 90%;
        max-width: 800px;
        padding: 20px;
    }

    .banner-content h1 {
        font-size: 3.5rem;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        margin-bottom: 15px;
    }

    /* Auth Section */
    .auth-section {
        padding: 80px 0;
        background: var(--light-color);
    }

    .auth-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin-bottom: 30px;
        transition: all 0.3s ease;
    }

    .auth-card:hover {
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .auth-card h5 {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 20px;
        text-align: center;
    }

    .form-group {
        position: relative;
        margin-bottom: 25px;
    }

    .form-group i {
        position: absolute;
        top: 50%;
        left: 15px;
        transform: translateY(-50%);
        color: var(--secondary-color);
        font-size: 1.2rem;
    }

    .form-control {
        padding-left: 40px;
        border-radius: 8px;
        border: 1px solid #ced4da;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 8px rgba(78, 115, 223, 0.3);
    }

    .form-label {
        font-size: 0.95rem;
        font-weight: 500;
        color: var(--secondary-color);
        margin-bottom: 8px;
    }

    .form-text {
        font-size: 0.85rem;
        color: #666;
        margin-top: 5px;
    }

    .btn-modal-primary {
        background: var(--accent-color);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 500;
        width: 100%;
        transition: all 0.3s ease;
    }

    .btn-modal-primary:hover {
        background: var(--primary-color);
        transform: translateY(-2px);
    }

    #statusStudLogMsg,
    #statusRegMsg {
        margin-top: 15px;
        font-size: 0.9rem;
        text-align: center;
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .banner {
            height: 300px;
        }
        .banner-content h1 {
            font-size: 2.5rem;
        }
    }

    @media (max-width: 768px) {
        .banner {
            height: 250px;
        }
        .banner-content h1 {
            font-size: 2rem;
        }
        .auth-card {
            padding: 20px;
        }
        .auth-card h5 {
            font-size: 1.6rem;
        }
    }

    @media (max-width: 576px) {
        .auth-card {
            padding: 15px;
        }
        .form-group i {
            font-size: 1rem;
            left: 10px;
        }
        .form-control {
            padding-left: 35px;
            font-size: 0.95rem;
        }
        .btn-modal-primary {
            padding: 10px;
        }
    }
</style>

<!-- Banner -->
<section class="banner">
    <img src="./image/courses.jpeg" alt="courses" class="banner-img">
    <div class="banner-content">
        <h1>Login or Sign Up</h1>
    </div>
</section>

<!-- Auth Section -->
<section class="auth-section">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="auth-card">
                    <h5>If already Registered || Login</h5>
                    <form role="form" id="stuLoginForm">
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
                        <button type="button" class="btn btn-modal-primary" id="stuLoginBtn" 
                        onclick="checkStudLogin()">Login</button><br/>
                        <div id="statusStudLogMsg"></div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="auth-card">
                    <h5>New user || Sign Up</h5>
                    <form role="form" id="studRegForm">
                        <div class="form-group">
                            <i class="fas fa-user"></i>
                            <label for="studname" class="form-label">Full Name</label>
                            <input type="text" class="form-control" placeholder="Enter your full name" 
                            name="studname" id="studname" required>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-id-card"></i>
                            <label for="studreg" class="form-label">Registration Number</label>
                            <input type="text" class="form-control" placeholder="Enter your registration number" 
                            name="studreg" id="studreg" required>
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
                        <button type="button" class="btn btn-modal-primary" 
                        onclick="addstu()">Register</button>
                    </form> <br/>
                    <div id="statusRegMsg"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- start including footer -->
<?php
include("./contact.php");
?>
<!-- end including footer -->

