<!-- start including header -->
<?php
include("./templates/header.php");
?>
<!-- end including header -->

   <!-- start course page Banner -->
    <div class="container-fluid bg-dark">
        <div class="row">
            <img src="./image/courses.jpeg" alt="courses"
            style="height: 400px; width:100%; object-fit: cover; box-shadow:10px;"/>
        </div>
    </div>
   <!-- end course page Banner -->

   <!-- start main content -->
    <div class="container">
        <h2 class="text-center my-4">
            payment status
        </h2>
        <form action="" method="post">
            <div class="form-group row">
                <label class="offset-sm-3 col-form-label">Order ID:</label>
                <div>
                    <input type="text" class="form-control mx-3">
                </div>
                <div>
                    <input type="submit" class="btn btn-primary mx-4" value="View">
                </div>
            </div>
        </form>
    </div>
    <!-- end main content -->

    <!-- start contact us -->
    <?php
    include("./contact.php")
    ?>

     </div>
     <!-- end contact us -->

   <!-- start including footer -->
<?php
include("./templates/footer.php");
?>
<!-- end including footer -->
