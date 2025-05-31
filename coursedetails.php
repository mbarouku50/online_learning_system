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
        height: 400px;
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
        top: 50%;
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

    /* Course Details Section */
    .course-details-section {
        padding: 80px 0;
        background: var(--light-color);
    }

    .course-image {
        width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }

    .course-info {
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        height: 100%;
    }

    .card-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 15px;
    }

    .card-text {
        font-size: 1rem;
        color: #555;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .price-container {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .original-price {
        text-decoration: line-through;
        color: #999;
        font-size: 1.1rem;
    }

    .discounted-price {
        color: var(--success-color);
        font-size: 1.4rem;
        font-weight: 700;
    }

    .buy-btn {
        background: var(--accent-color);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .buy-btn:hover {
        background: var(--primary-color);
        transform: translateY(-2px);
    }

    /* Lessons Table */
    .lessons-section {
        padding: 0 0 80px;
    }

    .section-title {
        text-align: center;
        margin-bottom: 40px;
        color: var(--primary-color);
        font-size: 2rem;
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

    .lesson-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }

    .lesson-table th,
    .lesson-table td {
        padding: 15px;
        text-align: left;
        font-size: 0.95rem;
    }

    .lesson-table th {
        background: var(--primary-color);
        color: white;
        font-weight: 600;
    }

    .lesson-table tr:nth-child(even) {
        background: #f8f9fa;
    }

    .lesson-table tr:hover {
        background: #e9ecef;
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .course-banner {
            height: 300px;
        }
        .course-banner h1 {
            font-size: 2.5rem;
        }
        .course-image {
            height: 250px;
        }
    }

    @media (max-width: 768px) {
        .course-banner {
            height: 250px;
        }
        .course-banner h1 {
            font-size: 2rem;
        }
        .card-title {
            font-size: 1.8rem;
        }
        .course-image {
            height: 200px;
            margin-bottom: 20px;
        }
    }

    @media (max-width: 576px) {
        .course-image {
            height: 180px;
        }
        .buy-btn {
            width: 100%;
            text-align: center;
        }
        .lesson-table th,
        .lesson-table td {
            font-size: 0.85rem;
            padding: 10px;
        }
        .price-container {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
    }
</style>

<!-- start course page Banner -->
<section class="course-banner">
    <img src="./image/courses.jpeg" alt="courses" class="banner-img">
    <div class="course-banner-content">
        <h1>Course Details</h1>
    </div>
</section>
<!-- end course page Banner -->

<!-- start main content -->
<section class="course-details-section">
    <div class="container mt-5">
        <?php
            if(isset($_GET["course_id"])){
                $course_id = $_GET["course_id"];
                $sql = "SELECT * FROM course WHERE course_id = '$course_id'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    // Set all required session variables
                    $_SESSION["course_id"] = $course_id;
                    $_SESSION["course_name"] = $row['course_name'];
                    $_SESSION["course_price"] = $row['course_price'];
                } else {
                    echo "<div class='alert alert-danger'>Course not found.</div>";
                    exit();
                }
            } else {
                echo "<div class='alert alert-danger'>No course ID provided.</div>";
                exit();
            }
        ?>
        <div class="row">
            <div class="col-md-4">
                <img src="<?php echo str_replace('..', '.', $row['course_img']) ?>" class="course-image" alt="IT" />
            </div>
            <div class="col-md-8">
                <div class="course-info">
                    <h5 class="card-title">Course Name: <?php echo htmlspecialchars($row['course_name']) ?></h5>
                    <p class="card-text">Description: <?php echo htmlspecialchars($row['course_desc']) ?></p>
                    <p class="card-text">Duration: <?php echo htmlspecialchars($row['course_duration']) ?></p>

                    <form action="checkout.php" method="post">
                        <p class="price-container">price:
                            <span class="original-price"><del>Tsh:<?php echo htmlspecialchars($row['course_original_price']) ?></del></span>
                            <span class="discounted-price">Tsh:<?php echo htmlspecialchars($row['course_price']) ?></span>
                        </p>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['course_price']) ?>">
                        <button type="submit" class="buy-btn" name="buy" aria-label="Buy <?php echo htmlspecialchars($row['course_name']); ?> course">Buy Now</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="lessons-section">
    <div class="container">
        <h2 class="section-title">Course Lessons</h2>
        <div class="row">
            <table class="lesson-table">
                <thead>
                    <tr>
                        <th scope="col">Lesson No.</th>
                        <th scope="col">Lesson Name.</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $sql = "SELECT * FROM lesson WHERE course_id = '$course_id'";
                    $result = $conn->query($sql);
                    if($result->num_rows > 0){
                        $num = 0;
                        while($row = $result->fetch_assoc()){
                            $num++;
                            echo '<tr>
                                <th scope="row">'.$num.'</th>
                                <td>'.htmlspecialchars($row['lesson_name']).'</td>
                            </tr>';
                        }
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<!-- end main content -->

<!-- start including footer -->
<?php
include("./templates/footer.php");
?>
<!-- end including footer -->