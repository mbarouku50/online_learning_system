<!-- start including header -->
<?php
include("./templates/header.php");
?>
<!-- end including header -->

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

    /* Payment Status Section */
    .payment-status-section {
        padding: 80px 0;
        background: var(--light-color);
    }

    .status-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin-bottom: 30px;
        transition: all 0.3s ease;
    }

    .status-card:hover {
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .status-card h2 {
        font-size: 2rem;
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

    .btn-view {
        background: var(--accent-color);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-view:hover {
        background: var(--primary-color);
        transform: translateY(-2px);
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
        .status-card {
            padding: 20px;
        }
        .status-card h2 {
            font-size: 1.8rem;
        }
        .form-group.row {
            flex-direction: column;
            align-items: center;
        }
        .form-group .col-form-label {
            margin-bottom: 10px;
        }
        .form-control {
            width: 100%;
            margin: 0;
        }
        .btn-view {
            width: 100%;
            margin-top: 10px;
        }
    }

    @media (max-width: 576px) {
        .status-card {
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
        .btn-view {
            padding: 10px;
        }
    }
</style>

<!-- start course page Banner -->
<section class="banner">
    <img src="./image/courses.jpeg" alt="Payment Status" class="banner-img">
    <div class="banner-content">
        <h1>Payment Status</h1>
    </div>
</section>
<!-- end course page Banner -->

<!-- start main content -->
<section class="payment-status-section">
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="status-card">
                    <h2 class="text-center my-4">Payment Status</h2>
                    <form action="" method="post">
                        <div class="form-group row">
                            <label class="col-form-label" for="orderId">Order ID:</label>
                            <div class="col-sm-7 position-relative">
                                <i class="fas fa-barcode"></i>
                                <input type="text" class="form-control" id="orderId" name="orderId" placeholder="Enter Order ID" required aria-label="Order ID">
                            </div>
                            <div class="col-sm-3">
                                <input type="submit" class="btn-view" value="View" aria-label="View Payment Status">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end main content -->

<!-- start contact us -->
<?php
include("./contact.php");
?>
<!-- end contact us -->

<!-- start including footer -->
<?php
include("./templates/footer.php");
?>
<!-- end including footer -->