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

// Handle book addition
if (isset($_POST['addBook'])) {
    $book_title = trim($_POST['book_title']);
    $author = trim($_POST['author']);
    $department = trim($_POST['department']);
    $course_id = !empty($_POST['course_id']) ? $_POST['course_id'] : null;
    $isbn = trim($_POST['isbn']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);

    // File upload handling
    $target_dir = "../Uploads/Books/";
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0755, true)) {
            echo "<script>alert('Failed to create directory: $target_dir');</script>";
        }
    }

    $file_name = basename($_FILES["book_file"]["name"]);
    $target_file = $target_dir . time() . "_" . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Image upload handling
    $image_path = null;
    if (!empty($_FILES["book_image"]["name"])) {
        $image_name = basename($_FILES["book_image"]["name"]);
        $target_image = $target_dir . time() . "_" . $image_name;
        $image_type = strtolower(pathinfo($target_image, PATHINFO_EXTENSION));
        $allowed_image_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($image_type, $allowed_image_types)) {
            if (move_uploaded_file($_FILES["book_image"]["tmp_name"], $target_image)) {
                $image_path = $target_image;
            }
        }
    }

    // Validate file
    if (empty($file_name)) {
        echo "<script>alert('No file selected. Please choose a PDF file.');</script>";
    } elseif ($file_type !== "pdf") {
        echo "<script>alert('Only PDF files are allowed.');</script>";
    } elseif ($_FILES["book_file"]["size"] > 5000000) {
        echo "<script>alert('File size must be less than 5MB.');</script>";
    } elseif (!is_writable($target_dir)) {
        echo "<script>alert('Upload directory is not writable.');</script>";
    } elseif (move_uploaded_file($_FILES["book_file"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO books (book_title, author, department, course_id, isbn, description, price, image_path, file_path) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssssssdss", $book_title, $author, $department, $course_id, $isbn, $description, $price, $image_path, $target_file);
            if ($stmt->execute()) {
                echo "<script>
                        alert('Book added successfully!');
                        window.location.href = 'books.php';
                      </script>";
            } else {
                echo "<script>alert('Error adding book: " . addslashes($stmt->error) . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Error preparing statement: " . addslashes($conn->error) . "');</script>";
        }
    } else {
        echo "<script>alert('Error uploading file. Please check file and try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book</title>
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
    </style>
</head>
<body>
<div class="container">
    <div class="form-container">
        <h3 class="mb-4">Add New Book</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="book_title">Book Title</label>
                <input type="text" class="form-control" id="book_title" name="book_title" required>
            </div>
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" class="form-control" id="author" name="author" required>
            </div>
            <div class="form-group">
                <label for="department">Department</label>
                <select class="form-control" id="department" name="department" required>
                    <option value="" disabled selected>Select Department</option>
                    <option value="ICT">ICT</option>
                    <option value="Metrology">Metrology</option>
                    <option value="Business">Business Studies</option>
                    <option value="Procurement">Procurement</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Accountancy">Accountancy</option>
                    <option value="Business_Admin">Business Administration</option>
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
                            echo "<option value='" . htmlspecialchars($row['course_id']) . "'>" . htmlspecialchars($row['course_name']) . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>Error loading courses: " . addslashes($conn->error) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="isbn">ISBN (Optional)</label>
                <input type="text" class="form-control" id="isbn" name="isbn">
            </div>
            <div class="form-group">
                <label for="price">Price (Tsh)</label>
                <input type="text" class="form-control" id="price" name="price" step="1" min="0" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label for="book_image">Book Cover Image (Optional)</label>
                <input type="file" class="form-control-file" id="book_image" name="book_image" accept="image/*">
                <small class="form-text text-muted">JPG, PNG, or GIF</small>
            </div>
            <div class="form-group">
                <label for="book_file">Book File (PDF)</label>
                <input type="file" class="form-control-file" id="book_file" name="book_file" accept=".pdf" required>
                <small class="form-text text-muted">Max file size: 5MB</small>
            </div>
            <button type="submit" name="addBook" class="btn btn-primary">Add Book</button>
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