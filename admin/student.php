<?php
if(!isset($_SESSION)){
    session_start();
}

include("./admininclude/header.php");
include("../dbconnection.php");

if(isset($_SESSION['is_admin_login'])){
    $adminEmail = $_SESSION['admin_email'];
} else {
    echo "<script> location.href='../index.php'; </script>";
    exit;
}

// Pagination variables
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// Get total records for pagination
$total_query = "SELECT COUNT(*) as total FROM student";
$total_result = $conn->query($total_query);
$total_rows = $total_result->fetch_assoc()['total'];
$pages = ceil($total_rows / $limit);

// Search functionality
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$search_condition = $search ? " WHERE studname LIKE '%$search%' OR stuemail LIKE '%$search%'" : '';

// Main query with pagination and search
$sql = "SELECT * FROM student $search_condition LIMIT $start, $limit";
$result = $conn->query($sql);
?>

<style>
    .student-table {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .student-table th {
        background-color: #4e73df;
        color: white;
    }
    
    .action-btn {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.9rem;
    }
    
    .add-student-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        z-index: 1000;
    }
    
    .search-box {
        max-width: 300px;
        margin-bottom: 20px;
    }
    
    .pagination {
        justify-content: center;
    }
    
    .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    
    .page-link {
        color: #4e73df;
    }
    
    .no-results {
        padding: 40px;
        text-align: center;
        color: #6c757d;
    }
</style>

<div class="col-sm-9 mt-5">
    <div class="student-table">
        <p class="bg-dark text-white p-2">List of Students</p>
        
        <!-- Search Box -->
        <form method="GET" action="" class="mb-4">
            <div class="input-group search-box">
                <input type="text" class="form-control" placeholder="Search students..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
                <?php if($search): ?>
                    <a href="?" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
        
        <?php if($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Student ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Registration No</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <th scope="row"><?php echo htmlspecialchars($row['stud_id']); ?></th>
                        <td><?php echo htmlspecialchars($row['studname']); ?></td>
                        <td><?php echo htmlspecialchars($row['stuemail']); ?></td>
                        <td><?php echo htmlspecialchars($row['studreg'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="editstudent.php?id=<?php echo $row['stud_id']; ?>" class="btn btn-info action-btn" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            
                            <button class="btn btn-secondary action-btn delete-btn" 
                                    title="Delete" 
                                    data-id="<?php echo $row['stud_id']; ?>"
                                    data-name="<?php echo htmlspecialchars($row['studname']); ?>">
                                <i class="far fa-trash-alt"></i>
                            </button>
                            
                            
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if($pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination mt-4">
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>" tabindex="-1">Previous</a>
                    </li>
                    
                    <?php for($i = 1; $i <= $pages; $i++): ?>
                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?php echo $page >= $pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="no-results">
            <i class="fas fa-user-graduate fa-3x mb-3"></i>
            <p>No students found</p>
            <?php if($search): ?>
                <a href="?" class="btn btn-primary">Show All Students</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Student Button -->
<a class="btn btn-danger add-student-btn" href="./addnewstudent.php">
    <i class="fas fa-plus"></i>
</a>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete student: <strong id="studentName"></strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="id" id="deleteId">
                    <button type="submit" class="btn btn-danger" name="delete">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Delete confirmation modal
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('studentName').textContent = this.getAttribute('data-name');
            document.getElementById('deleteId').value = this.getAttribute('data-id');
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        });
    });
    
    // Success message after deletion
    <?php if(isset($_GET['deleted'])): ?>
        alert('Student deleted successfully');
        window.history.replaceState({}, document.title, window.location.pathname);
    <?php endif; ?>
</script>

<?php
// Handle delete action
if(isset($_POST['delete'])){
    $id = (int)$_POST['id'];
    $delete_query = $conn->prepare("DELETE FROM student WHERE stud_id = ?");
    $delete_query->bind_param("i", $id);
    
    if($delete_query->execute()){
        echo '<script>
                window.location.href = "?deleted";
              </script>';
    } else {
        echo '<script>alert("Unable to delete student: '.$conn->error.'");</script>';
    }
    $delete_query->close();
}

include("./admininclude/footer.php");
?>