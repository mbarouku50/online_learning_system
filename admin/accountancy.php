<?php
include("../dbconnection.php");

$sql = "SELECT b.*, c.course_name FROM `accountancy_books` b LEFT JOIN course c ON b.course_id = c.course_id";
$result = $conn->query($sql);
?>

<div class="row">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            ?>
            <div class="col-md-4 mb-4">
                <div class="card book-card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['book_title']); ?></h5>
                        <p class="card-text"><strong>Author:</strong> <?php echo htmlspecialchars($row['author']); ?></p>
                        <p class="card-text"><strong>Course:</strong> <?php echo htmlspecialchars($row['course_name'] ?? 'N/A'); ?></p>
                        <p class="card-text"><?php echo htmlspecialchars($row['description'] ?? 'No description available'); ?></p>
                        <a href="<?php echo htmlspecialchars($row['file_path']); ?>" class="btn btn-primary" target="_blank">View Book</a>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        ?>
        <div class="col-md-12">
            <p>No books available in the Accountancy department.</p>
        </div>
        <?php
    }
    ?>
</div>

<?php
$conn->close();
?>