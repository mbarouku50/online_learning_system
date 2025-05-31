<?php
include("./templates/header.php");
include("./dbconnection.php");

// Check database connection
if (!$conn) {
    die("<div class='alert alert-danger text-center'>Database connection failed: " . mysqli_connect_error() . "</div>");
}

// Start session if not already started
if (!isset($_SESSION)) {
    session_start();
}

$isLoggedIn = isset($_SESSION['is_login']);

// Configuration
define('BASE_URL', "http://localhost/online_learning_system/");
define('BASE_PATH', "/var/www/html/online_learning_system/");

// Function to check file availability
function checkFileAvailability($file_path) {
    $filename = basename($file_path);
    $server_path = BASE_PATH . "Uploads/Books/" . $filename;
    $web_path = BASE_URL . "Uploads/Books/" . $filename;
    
    $file_exists = file_exists($server_path) && is_readable($server_path);
    
    return [
        'exists' => $file_exists,
        'web_path' => $web_path,
        'server_path' => $server_path
    ];
}

// Function to get department icon
function getDepartmentIcon($department) {
    $icons = [
        'ICT' => 'fas fa-laptop-code',
        'Metrology' => 'fas fa-ruler-combined',
        'Business' => 'fas fa-briefcase',
        'Procurement' => 'fas fa-shopping-cart',
        'Marketing' => 'fas fa-bullhorn',
        'Accountancy' => 'fas fa-calculator',
        'Business_Admin' => 'fas fa-user-tie'
    ];
    return $icons[$department] ?? 'fas fa-book';
}

// Function to get proper image path
function getImagePath($image_path) {
    if (empty($image_path)) {
        return false;
    }
    
    // Clean up the path
    $clean_path = ltrim(str_replace(['../', './'], '', $image_path), '/');
    $server_path = BASE_PATH . $clean_path;
    
    if (file_exists($server_path) && is_readable($server_path)) {
        return BASE_URL . $clean_path;
    }
    return false;
}

// Get all books from the database
$sql = "SELECT b.*, c.course_name FROM books b LEFT JOIN course c ON b.course_id = c.course_id ORDER BY b.book_title ASC";
$result = $conn->query($sql);

// Get student ID if logged in
$stud_id = $isLoggedIn ? $_SESSION['stud_id'] : 0;
?>
<style>
    :root {
        --primary-color: #225470;
        --secondary-color: #2c3e50;
        --accent-color: #4e73df;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --success-color: #28a745;
        --danger-color: #dc3545;
    }

    /* Book Banner */
    .book-banner {
        margin-top: 50px;
        position: relative;
        height: 200px;
        overflow: hidden;
        background: var(--dark-color);

    }

    .book-banner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.7);
        transition: transform 0.5s ease;
    }

    .book-banner:hover img {
        transform: scale(1.05);
    }

    .book-banner-content {
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

    .book-banner h1 {
        font-size: 3.5rem;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        margin-bottom: 15px;
    }

    /* Book Section */
    .book-section {
        padding: 20px 0;
        background: var(--light-color);
    }

    .section-title {
        text-align: center;
        margin-bottom: 50px;
        color: var(--primary-color);
        font-size: 2.5rem;
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

    /* Department Filter */
    .department-filter {
        margin-bottom: 30px;
        text-align: center;
    }

    .department-btn {
        margin: 5px;
        border-radius: 20px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .department-btn.active {
        background: var(--accent-color);
        color: white;
    }

    /* Book Card */
    .book-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        background: white;
        height: 90%;
        display: flex;
        flex-direction: column;
        margin-bottom: 30px;
    }

    .book-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .book-img-container {
        height: 220px;
        width: 100%;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .book-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .book-card:hover .book-img {
        transform: scale(1.05);
    }

    .book-icon {
        font-size: 3.5rem;
        opacity: 0.2;
        color: var(--primary-color);
        position: absolute;
    }

    .card-body {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .card-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 12px;
        line-height: 1.4;
    }

    .card-meta {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }

    .card-meta i {
        margin-right: 8px;
        color: var(--accent-color);
        width: 18px;
        text-align: center;
    }

    .card-text {
        color: #555;
        font-size: 0.95rem;
        line-height: 1.6;
        margin: 15px 0;
        flex-grow: 1;
    }

    .card-footer {
        background: rgba(248, 249, 250, 0.8);
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding: 15px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .department-badge {
        background: rgba(78, 115, 223, 0.1);
        color: var(--accent-color);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .view-btn {
        background: var(--accent-color);
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        box-shadow: 0 2px 5px rgba(78, 115, 223, 0.3);
    }

    .view-btn:hover {
        background: var(--primary-color);
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 4px 8px rgba(78, 115, 223, 0.4);
    }

    .file-status {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 20px;
        margin-top: 5px;
        display: inline-flex;
        align-items: center;
        font-weight: 600;
    }
    
    .file-available {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    
    .file-missing {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    /* Search Bar */
    .search-container {
        max-width: 600px;
        margin: 0 auto 40px;
    }

    .search-bar {
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 12px 20px;
        padding-right: 50px;
        border-radius: 30px;
        border: 1px solid #ddd;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .search-input:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.25);
        outline: none;
    }
    .buy-btn {
        background: #28a745;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        box-shadow: 0 2px 5px rgba(40, 167, 69, 0.3);
    }

    .buy-btn:hover {
        background: #218838;
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4);
    }

    .buy-btn i {
        margin-left: 5px;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .book-banner {
            height: 300px;
        }
        .book-banner h1 {
            font-size: 2rem;
        }
        .section-title {
            font-size: 2rem;
        }
        .book-img-container {
            height: 180px;
        }
    }

    @media (max-width: 576px) {
        .book-banner {
            height: 250px;
        }
        .book-img-container {
            height: 160px;
        }
    }
</style>

<!-- Book Banner -->
<section class="book-banner">
    <img src="./image/books-banner.jpg" alt="Books Banner" class="banner-img">
    <div class="book-banner-content">
        <h1>Explore Our Library</h1>
        <p>Access a wealth of knowledge with our comprehensive collection of academic books across all departments</p>
    </div>
</section>

<!-- Books Section -->
<section class="book-section">
    <div class="container">
        <h2 class="section-title">Available Books</h2>
        
        <!-- Search Bar -->
        <div class="search-container">
            <div class="search-bar">
                <input type="text" class="search-input" id="bookSearch" placeholder="Search by title, author, or course..." onkeyup="filterBooks()">
            </div>
        </div>
        
        <!-- Department Filter -->
        <div class="department-filter">
            <button class="btn btn-outline-primary department-btn active" onclick="filterDepartment('all')">All</button>
            <button class="btn btn-outline-primary department-btn" onclick="filterDepartment('ICT')">ICT</button>
            <button class="btn btn-outline-primary department-btn" onclick="filterDepartment('Metrology')">Metrology</button>
            <button class="btn btn-outline-primary department-btn" onclick="filterDepartment('Business')">Business</button>
            <button class="btn btn-outline-primary department-btn" onclick="filterDepartment('Procurement')">Procurement</button>
            <button class="btn btn-outline-primary department-btn" onclick="filterDepartment('Marketing')">Marketing</button>
            <button class="btn btn-outline-primary department-btn" onclick="filterDepartment('Accountancy')">Accountancy</button>
            <button class="btn btn-outline-primary department-btn" onclick="filterDepartment('Business_Admin')">Business Admin</button>
        </div>
        
        <!-- Books Grid -->
        <div class="row" id="booksContainer">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php 
                    $file_status = checkFileAvailability($row['file_path']);
                    $department = $row['department'];
                    $image_url = getImagePath($row['image_path']);
                    ?>
                    <div class="col-lg-4 col-md-6 mb-4 book-item" data-department="<?php echo htmlspecialchars($department); ?>">
                        <div class="book-card">
                            <div class="book-img-container">
                                <?php if ($image_url): ?>
                                    <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($row['book_title']); ?>" class="book-img" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'<?php echo getDepartmentIcon($department); ?> book-icon\'></i>'">
                                <?php else: ?>
                                    <i class="<?php echo getDepartmentIcon($department); ?> book-icon"></i>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($row['book_title']); ?></h3>
                                <div class="card-meta">
                                    <i class="fas fa-user"></i>
                                    <span><?php echo htmlspecialchars($row['author']); ?></span>
                                </div>
                                <?php if (!empty($row['course_name'])): ?>
                                    <div class="card-meta">
                                        <i class="fas fa-book-open"></i>
                                        <span><?php echo htmlspecialchars($row['course_name']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="card-meta">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span>Tsh <?php echo number_format($row['price'], 0); ?></span>
                                </div>
                                <p class="card-text"><?php echo htmlspecialchars(substr($row['description'] ?? 'No description available', 0, 150) . '...'); ?></p>
                                
                                <div class="<?php echo $file_status['exists'] ? 'file-available' : 'file-missing'; ?> file-status">
                                    <i class="fas <?php echo $file_status['exists'] ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                                    <?php echo $file_status['exists'] ? 'Available' : 'Currently unavailable'; ?>
                                </div>
                            </div>
                            <div class="card-footer">
                                <span class="department-badge">
                                    <?php echo str_replace('_', ' ', $department); ?>
                                </span>
                                <a href="<?php echo $isLoggedIn ? 'checkoutbook.php?book_id='.$row['id'].'&stud_id='.$stud_id : 'loginorsignup.php?redirect=checkoutbook.php?book_id='.$row['id']; ?>" 
                                class="buy-btn">
                                    Buy Now <i class="fas fa-shopping-cart"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-book-open fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted">No Books Available</h3>
                    <p class="text-muted">Check back later for updates to our library collection</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
    // Filter books by department
    function filterDepartment(department) {
        // Update active button
        document.querySelectorAll('.department-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.textContent.trim() === department || (department === 'all' && btn.textContent.trim() === 'All')) {
                btn.classList.add('active');
            }
        });
        
        // Show/hide books
        document.querySelectorAll('.book-item').forEach(item => {
            if (department === 'all' || item.getAttribute('data-department') === department) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    // Filter books by search term
    function filterBooks() {
        const searchTerm = document.getElementById('bookSearch').value.toLowerCase();
        
        document.querySelectorAll('.book-item').forEach(item => {
            const title = item.querySelector('.card-title').textContent.toLowerCase();
            const author = item.querySelector('.card-meta span').textContent.toLowerCase();
            const course = item.querySelectorAll('.card-meta span')[1]?.textContent.toLowerCase() || '';
            
            if (title.includes(searchTerm) || author.includes(searchTerm) || course.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
</script>

<?php
$conn->close();
include("./templates/footer.php");
?>