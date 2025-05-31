<?php
session_start();
include("./templates/header.php");
include("./dbconnection.php");

$purchase_id = isset($_GET['purchase_id']) ? (int)$_GET['purchase_id'] : 0;
// Similar verification as paymentprocessing.php
// Display appropriate failure message
?>

<div class="container">
    <div class="alert alert-danger text-center">
        <h4>Payment Failed</h4>
        <p>Your payment could not be processed. Please try again.</p>
        <a href="checkout.php?book_id=<?php echo $book_id; ?>" class="btn btn-primary">Try Again</a>
    </div>
</div>

<?php include("./templates/footer.php"); ?>