<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- bootstrap css -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <!-- font awesome css -->
    <link rel="stylesheet" href="../css/all.min.css">
    <!-- google-font -->
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <!-- custom css -->
    <link rel="stylesheet" href="../css/adminstyle1.css">
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>
<body>
    <!-- top navbar -->
     <nav class="navbar navbar-dark fixed-top p-0 shadow" 
     style="background-color: #225470;">
        <a class="navbar-brand col-sm-3 col-md-2 mr-0" 
        href="adminDashboard.php">E-learning<small class="text-white">Admin Area</small></a>
     </nav>

     <!-- side bar -->
      <div class="container-fluit mb-5" style="margin-top: 40px;">
        <div class="row">
            <nav class="col-sm-3 col-md-2 bg-light sidebar py-5 d-print-none">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="adminDashboard.php">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="courses.php">
                                <i class="fab fa-accessible-icon"></i>
                                courses
                            </a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="books.php">
                            <i class="fas fa-book"></i> <!-- Changed icon to book -->
                            Books Management
                        </a>
                        </li>
                            <li class="nav-item">
                            <a class="nav-link" href="lessons.php">
                                <i class="fab fa-accessible-icon"></i>
                                Lessons
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="student.php">
                                <i class="fas fa-users"></i>
                                Students
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="adminSellReport.php">
                                <i class="fas fa-table"></i>
                                Sell Report
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="adminPaymentStatus.php">
                                <i class="fas fa-table"></i>
                                Payment Status
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="adminBookPaymentStatus.php">
                                <i class="fas fa-table"></i>
                                Payment book Status
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="feedback.php">
                                <i class="fab fa-accessible-icon"></i>
                                Feedback
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="adminChangePassword.php">
                                <i class="fas fa-key"></i>
                                change password
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>