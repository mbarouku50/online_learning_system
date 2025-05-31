<?php
if (!isset($_SESSION)) {
    session_start();
}

include("./admininclude/header.php");
include("../dbconnection.php");

// Check if database connection is successful
if (!$conn) {
    die("<script>alert('Database connection failed: " . mysqli_connect_error() . "'); location.href='../index.php';</script>");
}

if (isset($_SESSION['is_admin_login'])) {
    $adminEmail = $_SESSION['admin_email'];
} else {
    echo "<script> location.href='../index.php'; </script>";
    exit;
}

// Handle book update
if (isset($_POST['updateBook'])) {
    $book_id = $_POST['book_id'];
    $book_title = trim($_POST['book_title']);
    $author = trim($_POST['author']);
    $department = trim($_POST['department']);
    $course_id = !empty($_POST['course_id']) ? $_POST['course_id'] : null;
    $isbn = trim($_POST['isbn']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);

    // Update basic book info
    $sql = "UPDATE books SET 
            book_title = ?, 
            author = ?, 
            department = ?, 
            course_id = ?, 
            isbn = ?, 
            description = ?, 
            price = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ssssssdi", $book_title, $author, $department, $course_id, $isbn, $description, $price, $book_id);
        if (!$stmt->execute()) {
            echo "<script>alert('Error updating book: " . addslashes($stmt->error) . "');</script>";
        }
        $stmt->close();
    }

    // Handle file upload if a new file is provided
    if (!empty($_FILES["book_file"]["name"])) {
        $target_dir = "../Uploads/Books/";
        $file_name = basename($_FILES["book_file"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($file_type === "pdf" && $_FILES["book_file"]["size"] <= 5000000) {
            if (move_uploaded_file($_FILES["book_file"]["tmp_name"], $target_file)) {
                // Update file path in database
                $update_file_sql = "UPDATE books SET file_path = ? WHERE id = ?";
                $stmt = $conn->prepare($update_file_sql);
                if ($stmt) {
                    $stmt->bind_param("si", $target_file, $book_id);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }

    // Handle image upload if a new image is provided
    if (!empty($_FILES["book_image"]["name"])) {
        $target_dir = "../Uploads/Books/";
        $image_name = basename($_FILES["book_image"]["name"]);
        $target_image = $target_dir . time() . "_" . $image_name;
        $image_type = strtolower(pathinfo($target_image, PATHINFO_EXTENSION));
        $allowed_image_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($image_type, $allowed_image_types)) {
            if (move_uploaded_file($_FILES["book_image"]["tmp_name"], $target_image)) {
                // Update image path in database
                $update_image_sql = "UPDATE books SET image_path = ? WHERE id = ?";
                $stmt = $conn->prepare($update_image_sql);
                if ($stmt) {
                    $stmt->bind_param("si", $target_image, $book_id);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }

    echo "<script>alert('Book updated successfully!'); window.location.href='books.php';</script>";
}

// Get book details
$book_id = isset($_GET['book_id']) ? $_GET['book_id'] : null;
if ($book_id) {
    $sql = "SELECT b.*, c.course_name FROM books b LEFT JOIN course c ON b.course_id = c.course_id WHERE b.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "<script>alert('No book selected.'); location.href='books.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View/Edit Book</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 1.5rem;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-control, .form-control-file, .btn {
            font-size: 1rem;
        }
        .btn-primary {
            width: 100%;
            padding: 10px;
        }
        .book-preview {
            margin-bottom: 20px;
        }
        .book-preview img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="form-container">
        <h3 class="mb-4">View/Edit Book</h3>
        
        <div class="book-preview text-center mb-4">
            <?php if (!empty($book['image_path'])): ?>
                <img src="../<?php echo htmlspecialchars($book['image_path']); ?>" alt="Book Cover" class="img-fluid mb-2">
            <?php else: ?>
                <i class="fas fa-book-open fa-5x text-muted mb-2"></i>
            <?php endif; ?>
            <h4><?php echo htmlspecialchars($book['book_title']); ?></h4>
            <p class="text-muted"><?php echo htmlspecialchars($book['author']); ?></p>
            <a href="../<?php echo htmlspecialchars($book['file_path']); ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                <i class="fas fa-download mr-1"></i> Download Book
            </a>
        </div>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['id']); ?>">
            
            <div class="form-group">
                <label for="book_title">Book Title</label>
                <input type="text" class="form-control" id="book_title" name="book_title" 
                       value="<?php echo htmlspecialchars($book['book_title']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" class="form-control" id="author" name="author" 
                       value="<?php echo htmlspecialchars($book['author']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="department">Department</label>
                <select class="form-control" id="department" name="department" required>
                    <option value="ICT" <?php echo $book['department'] === 'ICT' ? 'selected' : ''; ?>>ICT</option>
                    <option value="Metrology" <?php echo $book['department'] === 'Metrology' ? 'selected' : ''; ?>>Metrology</option>
                    <option value="Business" <?php echo $book['department'] === 'Business' ? 'selected' : ''; ?>>Business Studies</option>
                    <option value="Procurement" <?php echo $book['department'] === 'Procurement' ? 'selected' : ''; ?>>Procurement</option>
                    <option value="Marketing" <?php echo $book['department'] === 'Marketing' ? 'selected' : ''; ?>>Marketing</option>
                    <option value="Accountancy" <?php echo $book['department'] === 'Accountancy' ? 'selected' : ''; ?>>Accountancy</option>
                    <option value="Business_Admin" <?php echo $book['department'] === 'Business_Admin' ? 'selected' : ''; ?>>Business Administration</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="course_id">Course (Optional)</label>
                <select class="form-control" id="course_id" name="course_id">
                    <option value="">Select Course</option>
                    <?php
                    $sql = "SELECT course_id, course_name FROM course";
                    $result = $conn->query($sql);
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            $selected = ($book['course_id'] == $row['course_id']) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($row['course_id']) . "' $selected>" . htmlspecialchars($row['course_name']) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="isbn">ISBN (Optional)</label>
                <input type="text" class="form-control" id="isbn" name="isbn" 
                       value="<?php echo htmlspecialchars($book['isbn']); ?>">
            </div>
            
            <div class="form-group">
                <label for="price">Price (Tsh)</label>
                <input type="text" class="form-control" id="price" name="price" 
                       value="<?php echo htmlspecialchars($book['price']); ?>" step="1" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($book['description']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="book_image">Change Book Cover Image (Optional)</label>
                <input type="file" class="form-control-file" id="book_image" name="book_image" accept="image/*">
                <small class="form-text text-muted">JPG, PNG, or GIF</small>
            </div>
            
            <div class="form-group">
                <label for="book_file">Change Book File (PDF)</label>
                <input type="file" class="form-control-file" id="book_file" name="book_file" accept=".pdf">
                <small class="form-text text-muted">Max file size: 5MB</small>
            </div>
            
            <button type="submit" name="updateBook" class="btn btn-primary">Update Book</button>
        </form>
        <a href="books.php" class="btn btn-secondary mt-3">Back to Books</a>
    </div>
</div>

<!-- JavaScript Dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        console.log('jQuery and Bootstrap loaded');
    });
</script>

<?php
include("./admininclude/footer.php");
$conn->close();
?>
</body>
</html>