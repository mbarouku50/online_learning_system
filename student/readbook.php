<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['is_login'])) {
    header("Location: ../index.php");
    exit;
}

include('./stuInclude/header.php');
include_once(__DIR__ . '/../dbconnection.php');

$book_id = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;
$stud_id = $_SESSION['stud_id'] ?? 0;

$sql = "SELECT b.* FROM books b
        JOIN book_purchases bp ON b.id = bp.book_id
        WHERE bp.student_id = ? AND bp.book_id = ? AND bp.status = 'completed'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $stud_id, $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
$stmt->close();

if (!$book) {
    echo "<div class='alert alert-danger mt-5'>You don't have access to this book or it hasn't been purchased yet.</div>";
    include('./stuInclude/footer.php');
    exit;
}

// Handle download request
if (isset($_GET['download']) && $_GET['download'] == 'true') {
    if (!empty($book['file_path'])) {
        $file_path = str_replace('../', '', $book['file_path']);
        $absolute_path = __DIR__ . '/../' . $file_path;
        
        if (file_exists($absolute_path)) {
            ob_clean();
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($absolute_path).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($absolute_path));
            flush();
            readfile($absolute_path);
            exit;
        } else {
            echo "<div class='alert alert-danger'>File not found at: " . htmlspecialchars($absolute_path) . "</div>";
        }
    }
}

// Clean paths for display - remove ../ prefix
$image_path = !empty($book['image_path']) ? str_replace('../', '', $book['image_path']) : '';
$file_path = !empty($book['file_path']) ? str_replace('../', '', $book['file_path']) : '';
$clean_file_path_for_embed = !empty($file_path) ? '../' . $file_path : ''; // Add ../ back for embed tag
?>

<style>
    .book-reader-container {
        padding: 30px;
        background-color: #f8f9fa;
        min-height: calc(100vh - 150px);
    }
    
    .book-header {
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid #4e73df;
    }
    
    .book-title {
        color: #2c3e50;
        font-weight: 600;
    }
    
    .book-author {
        color: #6c757d;
        font-style: italic;
    }
    
    .book-content {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .book-cover {
        max-width: 300px;
        margin: 0 auto 30px;
        display: block;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .book-description {
        margin-bottom: 30px;
        line-height: 1.8;
    }
    
    .back-btn {
        margin-top: 20px;
    }
    
    .reader-options {
        margin: 30px 0;
        padding: 20px;
        background: #f1f8ff;
        border-radius: 8px;
    }
    
    .pdf-viewer {
        width: 100%;
        height: 600px;
        border: 1px solid #ddd;
        margin-bottom: 20px;
    }
    
    .btn-download {
        background-color: #28a745;
        color: white;
    }
    
    .btn-read {
        background-color: #17a2b8;
        color: white;
    }
</style>

<div class="book-reader-container">
    <div class="container">
        <div class="book-header">
            <h2 class="book-title"><?php echo htmlspecialchars($book['book_title']); ?></h2>
            <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
        </div>
        
        <div class="book-content">
            <?php if (!empty($image_path)): ?>
                <img src="../<?php echo htmlspecialchars($image_path); ?>" 
                     alt="<?php echo htmlspecialchars($book['book_title']); ?>" 
                     class="img-fluid book-cover">
            <?php endif; ?>
            
            <div class="book-description">
                <?php echo nl2br(htmlspecialchars($book['description'])); ?>
            </div>
            
            <?php if (!empty($file_path)): ?>
                <div class="reader-options">
                    <h4>Reading Options</h4>
                    
                    <?php 
                    $file_ext = pathinfo($file_path, PATHINFO_EXTENSION);
                    $is_pdf = strtolower($file_ext) === 'pdf';
                    ?>
                    
                    <?php if ($is_pdf): ?>
                        <!-- PDF Viewer - use the clean path with ../ prefix -->
                        <div class="pdf-viewer">
                            <embed src="<?php echo htmlspecialchars($clean_file_path_for_embed); ?>#toolbar=1&navpanes=0" 
                                   type="application/pdf" width="100%" height="100%">
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            This book format can only be downloaded (not viewed online).
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-4">
                        <?php if ($is_pdf): ?>
                            <a href="readbook.php?book_id=<?php echo $book_id; ?>" 
                               class="btn btn-read mr-3">
                                <i class="fas fa-book-open mr-2"></i> Read Online
                            </a>
                        <?php endif; ?>
                        
                        <a href="readbook.php?book_id=<?php echo $book_id; ?>&download=true" 
                           class="btn btn-download">
                            <i class="fas fa-download mr-2"></i> Download Book
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    This book currently has no available content.
                </div>
            <?php endif; ?>
            
            <a href="myBooks.php" class="btn btn-secondary back-btn">
                <i class="fas fa-arrow-left mr-2"></i> Back to My Books
            </a>
        </div>
    </div>
</div>

<?php
include('./stuInclude/footer.php');
ob_end_flush();
?>