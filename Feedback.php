<?php 
include("./templates/header.php");
include("./dbconnection.php");
?>

<br><br><br>
<!-- Testimonials -->
<div class="container-fluid py-5" style="background-color: var(--primary-color);" id="Feedback">
    <div class="container">
        <h1 class="text-center text-white mb-5">Student's Feedback</h1>
        
        <!-- Carousel Wrapper -->
        <div id="feedbackCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                $sql = "SELECT s.studname, s.stu_occ, s.stu_img, f.f_content FROM student 
                        AS s JOIN feedback AS f ON s.stud_id = f.stud_id";
                $result = $conn->query($sql);
                if($result->num_rows > 0) {
                    $active = true;
                    while($row = $result->fetch_assoc()) {
                        $s_img = $row["stu_img"];
                        $n_img = str_replace('..', '.', $s_img);
                ?>
                <div class="carousel-item <?php echo $active ? 'active' : ''; ?>">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="feedback-content text-center p-4">
                                <img src="<?php echo $n_img ?>" alt="<?php echo $row['studname'] ?>" 
                                     class="rounded-circle mb-3" width="100" height="100">
                                <div class="feedback-text mb-3">
                                    <i class="fas fa-quote-left text-primary mr-2"></i>
                                    <?php echo $row['f_content']; ?>
                                    <i class="fas fa-quote-right text-primary ml-2"></i>
                                </div>
                                <h5 class="card-name mb-1"><?php echo $row['studname'] ?></h5>
                                <small class="text-muted"><?php echo $row['stu_occ'] ?></small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                        $active = false;
                    }
                } else {
                    echo '<div class="carousel-item active">
                            <div class="row justify-content-center">
                                <div class="col-lg-8 text-center text-white">
                                    <p>No feedback available yet.</p>
                                </div>
                            </div>
                          </div>';
                } 
                ?>
            </div>
            
            <!-- Carousel Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#feedbackCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#feedbackCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</div>


<?php include("./templates/footer.php"); ?>