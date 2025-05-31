<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- jQuery FIRST -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- google-font -->
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    
    <!-- jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

    <!-- custom css -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #225470;
            --secondary-color: #2c3e50;
            --accent-color: #4e73df;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }
        
        /* Navigation */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 0.8rem 1rem;
        }
        
        .navbar-brand {
            font-family: 'Ubuntu', sans-serif;
            font-weight: 700;
            font-size: 1.8rem;
            color: white !important;
        }
        
        .navbar-text {
            color: rgba(255,255,255,0.8);
            font-size: 1.1rem;
            margin-left: 0.5rem;
        }
        
        .custom-nav-item {
            margin: 0 0.5rem;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s;
            border-radius: 4px;
        }
        
        .nav-link:hover, .nav-link:focus {
            color: white !important;
            background: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }
        
        .navbar-toggler {
            border-color: rgba(255,255,255,0.5);
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3e%3cpath stroke='rgba(255, 255, 255, 0.8)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
    </style>

    <title>CBE-E-LEARNING</title>
</head>
<body>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">CBE</a>
        <span class="navbar-text d-none d-md-block">E-Learning</span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item custom-nav-item">
                    <a href="index.php" class="nav-link">Home</a>
                </li>
                <li class="nav-item custom-nav-item">
                    <a href="courses.php" class="nav-link">Courses</a>
                </li>
                <li class="nav-item custom-nav-item">
                    <a href="books.php" class="nav-link">Books</a>
                </li>
                <?php
                    session_start();
                    if(isset($_SESSION['is_login'])) {
                        echo '
                        <li class="nav-item custom-nav-item">
                            <a href="student/studentProfile.php" class="nav-link">My Profile</a>
                        </li>
                        <li class="nav-item custom-nav-item">
                            <a href="logout.php" class="nav-link">Logout</a>
                        </li>';
                    } else {
                        echo '
                        <li class="nav-item custom-nav-item">
                            <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#studLoginModalCenter">Login</a>
                        </li>
                        <li class="nav-item custom-nav-item">
                            <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#studRegModalCenter">Signup</a>
                        </li>';
                    }
                ?>
                <li class="nav-item custom-nav-item">
                    <a href="Feedback.php" class="nav-link">Feedback</a>
                </li>
                <li class="nav-item custom-nav-item">
                    <a href="contact.php" class="nav-link">Contact</a>
                </li>
            </ul>
        </div>
    </div>
</nav>