<?php
if (!isset($_SESSION)) {
    session_start();
}

$include_success = include_once(__DIR__ . '/../../dbconnection.php');
if (!$include_success) {
    error_log("Failed to include dbconnection.php in header.php");
    $stu_img = '/Online learning system/image/default.jpg'; // Fallback image
} else {
    if (isset($_SESSION['is_login']) && isset($_SESSION['stuemail']) && isset($conn)) {
        $stuemail = $_SESSION['stuemail'];
        $sql = "SELECT stu_img FROM student WHERE stuemail = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $stuemail);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $stu_img = $row['stu_img'] ?: '/Online learning system/image/default.jpg';
                error_log("Fetched stu_img for $stuemail: $stu_img");
            } else {
                $stu_img = '/Online learning system/image/default.jpg';
                error_log("No student found for $stuemail");
            }
            $stmt->close();
        } else {
            error_log("Failed to prepare SQL statement in header.php: " . $conn->error);
            $stu_img = '/Online learning system/image/default.jpg';
        }
    } else {
        $stu_img = '/Online learning system/image/default.jpg';
        error_log("Session or connection not set: is_login=" . (isset($_SESSION['is_login']) ? 'true' : 'false') . ", stuemail=" . (isset($_SESSION['stuemail']) ? $_SESSION['stuemail'] : 'unset') . ", conn=" . (isset($conn) ? 'set' : 'unset'));
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    

    <title>CBE-E-LEARNING</title>
</head>
<style>
        :root {
            --sidebar-width: 250px;
            --topbar-height: 56px;
            --primary-color: #225470;
            --secondary-color: #2c3e50;
        }
        
        body {
            font-family: 'Ubuntu', sans-serif;
            padding-top: var(--topbar-height);
        }
        
        /* Top Navbar */
        .navbar {
            background-color: var(--primary-color) !important;
            height: var(--topbar-height);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1030;
        }
        
        .navbar-brand {
            font-weight: 600;
        }
        
        /* Sidebar Styling */
        .sidebar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            width: var(--sidebar-width);
            position: fixed;
            top: var(--topbar-height);
            left: 0;
            bottom: 0;
            z-index: 1020;
            overflow-y: auto;
            transition: transform 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-sticky {
            padding: 20px 0;
        }
        
        .profile-section {
            text-align: center;
            padding: 0 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.2);
            margin: 0 auto 15px;
        }
        
        .profile-name {
            color: white;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .profile-email {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
        }
        
        .nav-item {
            margin-bottom: 5px;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            border-radius: 5px;
            padding: 12px 15px !important;
            margin: 0 10px;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.1) !important;
            color: white !important;
        }
        
        .nav-link.active {
            font-weight: 600;
        }
        
        .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: calc(100vh - var(--topbar-height));
            transition: margin 0.3s ease;
        }
        
        /* Mobile Menu Toggle */
        .navbar-toggler {
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .profile-img {
                width: 100px;
                height: 100px;
            }
        }
        
        /* Profile Form Styles */
        .profile-form {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 20px;
            max-width: 800px;
        }
        
        .profile-picture-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e3e6f0;
        }
        
        .upload-area {
            border: 2px dashed #d1d3e2;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .upload-area:hover {
            border-color: #4e73df;
            background-color: #f8f9fc;
        }
    </style>
<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-dark fixed-top flex-md-nowrap p-0 shadow">
        <button class="navbar-toggler d-lg-none ms-2" type="button" id="sidebarToggle">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand mx-auto mx-lg-0" href="studentProfile.php">E-learning</a>
    </nav>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-sticky">
            <!-- Profile Section -->
            <div class="profile-section">
                <img src="<?php echo htmlspecialchars($stu_img); ?>" alt="Student Image" class="profile-img">
                <?php if (isset($_SESSION['stuemail'])): ?>
                    <h6 class="profile-name"><?php echo htmlspecialchars(explode('@', $_SESSION['stuemail'])[0]); ?></h6>
                    <small class="profile-email"><?php echo htmlspecialchars($_SESSION['stuemail']); ?></small>
                <?php endif; ?>
            </div>
            
            <!-- Navigation Menu -->
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" 
                    href="../index.php">
                        <i class="fas fa-user"></i> Home
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'studentProfile.php') ? 'active' : ''; ?>" 
                    href="studentProfile.php">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'myCourse.php') ? 'active' : ''; ?>" 
                    href="myCourse.php">
                        <i class="fas fa-book-open"></i> My Courses
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="myBooks.php">
                        <i class="fas fa-book"></i> My Books
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'stufeedback.php') ? 'active' : ''; ?>" 
                    href="stufeedback.php">
                        <i class="fas fa-comment-alt"></i> Feedback
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'studentChangePass.php') ? 'active' : ''; ?>" 
                    href="studentChangePass.php">
                        <i class="fas fa-key"></i> Change Password
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <a class="nav-link text-danger" href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>
            <!-- Main Content Area -->
            <main class="main-content" id="mainContent">