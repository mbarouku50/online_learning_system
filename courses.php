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
    }

    /* Course Banner */
    .course-banner {
        position: relative;
        height: 250px;
        overflow: hidden;
        background: var(--dark-color);
    }

    .course-banner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.7);
        transition: transform 0.5s ease;
    }

    .course-banner:hover img {
        transform: scale(1.05);
    }

    .course-banner-content {
        position: absolute;
        top: 80%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: var(--light-color);
        width: 90%;
        max-width: 800px;
        padding: 20px;
    }

    .course-banner h1 {
        font-size: 3.5rem;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        margin-bottom: 15px;
    }

    .course-banner p {
        font-size: 1.2rem;
        opacity: 0.9;
    }

    /* Course Section */
    .course-section {
        padding: 80px 0;
        background: var(--light-color);
    }

    .section-title {
        text-align: center;
        margin-bottom: 50px;
        color: var(--primary-color);
        font-size: 2.5rem;
        font-weight: 700;
        position: relative;
    }

    .section-title:after {
        content: '';
        display: block;
        width: 100px;
        height: 4px;
        background: var(--accent-color);
        margin: 20px auto;
        border-radius: 2px;
    }

    /* Course Card */
    .course-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        background: white;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .course-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .course-img {
        height: 220px;
        width: 100%;
        object-fit: cover;
        object-position: center;
    }

    .card-body {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .card-title {
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 12px;
        line-height: 1.3;
    }

    .card-text {
        color: #555;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 15px;
        flex-grow: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }

    .card-footer {
        background: white;
        border-top: none;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .price-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .original-price {
        text-decoration: line-through;
        color: #999;
        font-size: 0.9rem;
    }

    .discounted-price {
        color: var(--success-color);
        font-size: 1.2rem;
        font-weight: 700;
    }

    .enroll-btn {
        background: var(--accent-color);
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .enroll-btn:hover {
        background: var(--primary-color);
        transform: translateY(-2px);
        color: white;
    }

    /* Loading State */
    .loading-spinner {
        text-align: center;
        padding: 50px 0;
    }

    /* No Courses Message */
    .no-courses {
        text-align: center;
        padding: 50px 0;
        color: #666;
        font-size: 1.2rem;
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .course-banner h1 {
            font-size: 2.5rem;
        }
        .course-img {
            height: 200px;
        }
    }

    @media (max-width: 768px) {
        .course-banner {
            height: 300px;
        }
        .course-banner h1 {
            font-size: 2rem;
        }
        .course-banner p {
            font-size: 1rem;
        }
        .section-title {
            font-size: 2rem;
        }
        .course-img {
            height: 180px;
        }
    }

    @media (max-width: 576px) {
        .course-banner {
            height: 250px;
        }
        .course-img {
            height: 160px;
        }
        .card-body {
            padding: 20px;
        }
        .card-footer {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        .enroll-btn {
            width: 100%;
            text-align: center;
        }
    }
</style>

<!-- Course Banner -->
<section class="course-banner">
    <img src="./image/courses.jpeg" alt="Courses Banner" class="banner-img">
    <div class="course-banner-content">
        <h1>Explore Our Courses</h1>
        <p>Unlock your potential with our wide range of professional courses designed for all levels.</p>
    </div>
</section>

<!-- All Courses Section -->
<section class="course-section">
    <div class="container">
        <h2 class="section-title">All Available Courses</h2>
        <div class="row mt-4" id="course-list">
            <?php 
            $sql = "SELECT * FROM course";
            $result = $conn->query($sql);
            if ($result === false) {
                echo '<div class="col-12 no-courses">Error fetching courses. Please try again later.</div>';
            } elseif ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $course_id = $row['course_id'];
                    $course_img = !empty($row['course_img']) ? htmlspecialchars(str_replace('..', '.', $row['course_img'])) : './image/default-course.jpg';
                    $course_name = htmlspecialchars($row['course_name']);
                    $course_desc = htmlspecialchars($row['course_desc']);
                    $original_price = htmlspecialchars($row['course_original_price']);
                    $course_price = htmlspecialchars($row['course_price']);
                    
                    echo '
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="course-card">
                            <img src="'.$course_img.'" class="course-img" alt="'.$course_name.'" loading="lazy">
                            <div class="card-body">
                                <h5 class="card-title">'.$course_name.'</h5>
                                <p class="card-text">'.$course_desc.'</p>
                            </div>
                            <div class="card-footer">
                                <div class="price-container">
                                    <span class="original-price">$'.$original_price.'</span>
                                    <span class="discounted-price">$'.$course_price.'</span>
                                </div>
                                <a href="coursedetails.php?course_id='.$course_id.'" class="enroll-btn" aria-label="Enroll in '.$course_name.'">Enroll Now</a>
                            </div>
                        </div>
                    </div>
                    ';
                }
            } else {
                echo '<div class="col-12 no-courses">No courses available at the moment.</div>';
            }
            ?>
        </div>
    </div>
</section>

<?php include("./templates/footer.php"); ?>