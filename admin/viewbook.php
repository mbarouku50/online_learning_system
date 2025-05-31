<?php
if (!isset($_SESSION)) {
    session_start();
}

include("./admininclude/header.php");
include("../dbconnection.php");

// Check database connection
if (!$conn) {
    die("<script>alert('Database connection failed: " . mysqli_connect_error() . "'); location.href='../index.php';</script>");
}

// Admin authentication
if (!isset($_SESSION['is_admin_login'])) {
    echo "<script> location.href='../index.php'; </script>";
    exit;
}

// Function to safely handle file paths
function sanitizePath($path) {
    // Remove any attempts to traverse directories
    $path = str_replace(['../', '..\\'], '', $path);
    // Normalize directory separators
    return rtrim(str_replace('\\', '/', $path), '/');
}

// Handle book update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateBook'])) {
    $book_id = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
    if (!$book_id) {
        die("<script>alert('Invalid book ID'); location.href='books.php';</script>");
    }

    // Sanitize inputs
    $book_title = trim(htmlspecialchars($_POST['book_title']));
    $author = trim(htmlspecialchars($_POST['author']));
    $department = trim(htmlspecialchars($_POST['department']));
    $course_id = !empty($_POST['course_id']) ? filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT) : null;
    $isbn = trim(htmlspecialchars($_POST['isbn']));
    $description = trim(htmlspecialchars($_POST['description']));
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Prepare update statement
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
    if (!$stmt) {
        die("<script>alert('Database error: " . addslashes($conn->error) . "');</script>");
    }
    
    $stmt->bind_param("ssssssdi", $book_title, $author, $department, $course_id, $isbn, $description, $price, $book_id);
    if (!$stmt->execute()) {
        echo "<script>alert('Error updating book: " . addslashes($stmt->error) . "');</script>";
    }
    $stmt->close();

    // Handle file uploads - FIXED PATH TO GO DIRECTLY TO Books FOLDER
    $base_dir = '/var/www/html/online_learning_system/';
    $upload_dir = $base_dir . 'Uploads/Books/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Process PDF file upload
    if (!empty($_FILES["book_file"]["name"])) {
        $file_name = basename($_FILES["book_file"]["name"]);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if ($file_ext === "pdf" && $_FILES["book_file"]["size"] <= 5000000) {
            $new_filename = uniqid() . '_' . preg_replace('/[^a-z0-9]/i', '_', pathinfo($file_name, PATHINFO_FILENAME)) . '.pdf';
            $target_file = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["book_file"]["tmp_name"], $target_file)) {
                // Delete old file if exists
                if (!empty($book['file_path'])) {
                    $old_file = $base_dir . $book['file_path'];
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
                }
                
                // Update database with correct path
                $relative_path = 'Uploads/Books/' . $new_filename;
                $update_sql = "UPDATE books SET file_path = ? WHERE id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("si", $relative_path, $book_id);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // Process image upload
    if (!empty($_FILES["book_image"]["name"])) {
        $image_name = basename($_FILES["book_image"]["name"]);
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $allowed_image_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($image_ext, $allowed_image_types)) {
            $new_imagename = uniqid() . '_' . preg_replace('/[^a-z0-9]/i', '_', pathinfo($image_name, PATHINFO_FILENAME)) . '.' . $image_ext;
            $target_image = $upload_dir . $new_imagename;
            
            if (move_uploaded_file($_FILES["book_image"]["tmp_name"], $target_image)) {
                // Delete old image if exists
                if (!empty($book['image_path'])) {
                    $old_image = $base_dir . $book['image_path'];
                    if (file_exists($old_image)) {
                        unlink($old_image);
                    }
                }
                
                // Update database with correct path
                $relative_path = 'Uploads/Books/' . $new_imagename;
                $update_sql = "UPDATE books SET image_path = ? WHERE id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("si", $relative_path, $book_id);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    echo "<script>alert('Book updated successfully!'); window.location.href='books.php';</script>";
}

// Get book details
$book_id = filter_input(INPUT_GET, 'book_id', FILTER_VALIDATE_INT);
if (!$book_id) {
    die("<script>alert('Invalid book ID'); location.href='books.php';</script>");
}

$sql = "SELECT b.*, c.course_name FROM books b LEFT JOIN course c ON b.course_id = c.course_id WHERE b.id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("<script>alert('Database error: " . addslashes($conn->error) . "'); location.href='books.php';</script>");
}

$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
$stmt->close();

if (!$book) {
    die("<script>alert('Book not found'); location.href='books.php';</script>");
}

// Function to check if file exists and is accessible
function fileExists($path) {
    $full_path = '/var/www/html/online_learning_system/' . $path;
    return !empty($path) && file_exists($full_path);
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
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .book-preview {
            margin-bottom: 2rem;
            text-align: center;
        }
        .book-cover {
            max-width: 300px;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        .file-download {
            display: inline-block;
            margin-top: 1rem;
        }
        .department-badge {
            font-size: 0.9rem;
            padding: 5px 10px;
            border-radius: 50px;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .file-info {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="form-container bg-white">
        <h3 class="mb-4 text-center">View/Edit Book</h3>
        
        <div class="book-preview">
            <?php if (fileExists($book['image_path'])): ?>
                <img src="../<?= htmlspecialchars($book['image_path']) ?>" 
                     alt="Book Cover" 
                     class="book-cover img-fluid mb-2">
            <?php else: ?>
                <div class="book-cover-placeholder bg-light d-flex align-items-center justify-content-center" 
                     style="width:300px; height:400px; margin:0 auto;">
                    <i class="fas fa-book-open fa-5x text-muted"></i>
                </div>
            <?php endif; ?>
            
            <h4><?= htmlspecialchars($book['book_title']) ?></h4>
            <p class="text-muted">by <?= htmlspecialchars($book['author']) ?></p>
            
            <span class="badge department-badge 
                <?= $book['department'] === 'ICT' ? 'badge-primary' : 
                   ($book['department'] === 'Business' ? 'badge-success' : 'badge-info') ?>">
                <?= str_replace('_', ' ', $book['department']) ?>
            </span>
            
            <?php if (fileExists($book['file_path'])): ?>
                <div class="file-info">
                    <i class="fas fa-file-pdf text-danger mr-2"></i>
                    <span><?= basename(htmlspecialchars($book['file_path'])) ?></span>
                    <a href="../<?= htmlspecialchars($book['file_path']) ?>" 
                       class="btn btn-sm btn-outline-primary ml-3 file-download"
                       target="_blank">
                        <i class="fas fa-download mr-1"></i> Download
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="book_id" value="<?= htmlspecialchars($book['id']) ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="book_title">Book Title *</label>
                        <input type="text" class="form-control" id="book_title" name="book_title" 
                               value="<?= htmlspecialchars($book['book_title']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="author">Author *</label>
                        <input type="text" class="form-control" id="author" name="author" 
                               value="<?= htmlspecialchars($book['author']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="department">Department *</label>
                        <select class="form-control" id="department" name="department" required>
                            <option value="ICT" <?= $book['department'] === 'ICT' ? 'selected' : '' ?>>ICT</option>
                            <option value="Metrology" <?= $book['department'] === 'Metrology' ? 'selected' : '' ?>>Metrology</option>
                            <option value="Business" <?= $book['department'] === 'Business' ? 'selected' : '' ?>>Business Studies</option>
                            <option value="Procurement" <?= $book['department'] === 'Procurement' ? 'selected' : '' ?>>Procurement</option>
                            <option value="Marketing" <?= $book['department'] === 'Marketing' ? 'selected' : '' ?>>Marketing</option>
                            <option value="Accountancy" <?= $book['department'] === 'Accountancy' ? 'selected' : '' ?>>Accountancy</option>
                            <option value="Business_Admin" <?= $book['department'] === 'Business_Admin' ? 'selected' : '' ?>>Business Administration</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="course_id">Course (Optional)</label>
                        <select class="form-control" id="course_id" name="course_id">
                            <option value="">Select Course</option>
                            <?php
                            $sql = "SELECT course_id, course_name FROM course ORDER BY course_name";
                            $result = $conn->query($sql);
                            if ($result) {
                                while ($row = $result->fetch_assoc()) {
                                    $selected = ($book['course_id'] == $row['course_id']) ? 'selected' : '';
                                    echo "<option value='" . htmlspecialchars($row['course_id']) . "' $selected>" . 
                                         htmlspecialchars($row['course_name']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="isbn">ISBN (Optional)</label>
                        <input type="text" class="form-control" id="isbn" name="isbn" 
                               value="<?= htmlspecialchars($book['isbn']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price (Tsh) *</label>
                        <input type="number" class="form-control" id="price" name="price" 
                               value="<?= htmlspecialchars($book['price']) ?>" step="100" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5"><?= htmlspecialchars($book['description']) ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="book_image">Change Book Cover Image</label>
                        <input type="file" class="form-control-file" id="book_image" name="book_image" accept="image/*">
                        <small class="text-muted">JPG, PNG, or GIF (Max 2MB)</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="book_file">Change Book File (PDF)</label>
                        <input type="file" class="form-control-file" id="book_file" name="book_file" accept=".pdf">
                        <small class="text-muted">PDF only (Max 5MB)</small>
                    </div>
                </div>
            </div>
            
            <div class="form-group text-center mt-4">
                <button type="submit" name="updateBook" class="btn btn-primary px-4">
                    <i class="fas fa-save mr-2"></i> Update Book
                </button>
                <a href="books.php" class="btn btn-secondary ml-2">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Books
                </a>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    // File input validation
    $('#book_image').change(function() {
        const file = this.files[0];
        if (file) {
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                alert('Only JPG, PNG, or GIF images are allowed');
                $(this).val('');
            }
            if (file.size > 2000000) {
                alert('Image must be less than 2MB');
                $(this).val('');
            }
        }
    });
    
    $('#book_file').change(function() {
        const file = this.files[0];
        if (file) {
            if (file.type !== 'application/pdf') {
                alert('Only PDF files are allowed');
                $(this).val('');
            }
            if (file.size > 5000000) {
                alert('PDF must be less than 5MB');
                $(this).val('');
            }
        }
    });
});
</script>

<?php
include("./admininclude/footer.php");
$conn->close();
?>
</body>
</html>