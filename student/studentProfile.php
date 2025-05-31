<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION)) {
    session_start();
}

include('./stuInclude/header.php');
include_once(__DIR__ . '/../dbconnection.php');

if (!isset($_SESSION['is_login'])) {
    echo "<script>location.href='../index.php';</script>";
    exit;
}

// Configuration - Update these to match your server setup
define('BASE_URL', "http://localhost/Online learning system/");
define('BASE_PATH', "/var/www/html/Online learning system/");
define('IMAGE_UPLOAD_DIR', BASE_PATH . "image/stu/");
define('IMAGE_UPLOAD_URL', BASE_URL . "image/stu/");

$stuemail = $_SESSION['stuemail'];
$passmsg = '';

// Get current student data
if (!$conn) {
    $passmsg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Database connection failed: ' . mysqli_connect_error() . '</div>';
} else {
    $sql = "SELECT * FROM student WHERE stuemail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $stuemail);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $stud_id = $row["stud_id"];
        $studname = $row["studname"];
        $studreg = $row["studreg"];
        $stu_occ = $row["stu_occ"];
        $stu_img = $row["stu_img"];
    } else {
        $passmsg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Student not found</div>';
    }
    $stmt->close();
}

if (isset($_POST["updateStuNameBtn"])) {
    $studname = trim($_POST["studname"]);
    $stu_occ = trim($_POST["stu_occ"]);
    
    if (empty($studname)) {
        $passmsg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Fill all required fields</div>';
    } else {
        // Initialize image path with existing value
        $img_folder = $stu_img;
        
        // Handle file upload if a new image was provided
        if (!empty($_FILES['stu_img']['name']) && $_FILES['stu_img']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            // Sanitize filename
            $original_name = $_FILES['stu_img']['name'];
            $file_ext = pathinfo($original_name, PATHINFO_EXTENSION);
            $file_name = 'stu_' . $stud_id . '_' . time() . '.' . $file_ext;
            $file_tmp = $_FILES['stu_img']['tmp_name'];
            $file_size = $_FILES['stu_img']['size'];
            
            // Validate file
            $file_type = mime_content_type($file_tmp);
            
            if (!in_array($file_type, $allowed_types)) {
                $passmsg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Invalid file type. Only JPEG, PNG, or GIF allowed.</div>';
            } elseif ($file_size > $max_size) {
                $passmsg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">File size exceeds 5MB limit.</div>';
            } else {
                // Create directory if it doesn't exist
                if (!file_exists(IMAGE_UPLOAD_DIR)) {
                    mkdir(IMAGE_UPLOAD_DIR, 0755, true);
                }
                
                // Set full paths
                $destination = IMAGE_UPLOAD_DIR . $file_name;
                $img_folder = IMAGE_UPLOAD_URL . $file_name;
                
                // Delete old image if it exists and is not the default
                if (!empty($stu_img) && $stu_img != '../image/default-profile.jpg' && file_exists(BASE_PATH . str_replace(BASE_URL, '', $stu_img))) {
                    unlink(BASE_PATH . str_replace(BASE_URL, '', $stu_img));
                }
                
                // Move uploaded file
                if (move_uploaded_file($file_tmp, $destination)) {
                    $passmsg = '<div class="alert alert-success col-sm-6 ml-5 mt-2">Image uploaded successfully</div>';
                    error_log("Image uploaded to $destination");
                } else {
                    $passmsg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Failed to upload image. Please try again.</div>';
                    error_log("Failed to move uploaded file to $destination");
                    $img_folder = $stu_img; // Revert to old image if upload failed
                }
            }
        }
        
        // Only proceed with update if there were no errors
        if (empty($passmsg) || strpos($passmsg, 'successfully') !== false) {
            $sql = "UPDATE student SET studname = ?, stu_occ = ?, stu_img = ? WHERE stuemail = ?";
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("ssss", $studname, $stu_occ, $img_folder, $stuemail);
                
                if ($stmt->execute()) {
                    $passmsg = '<div class="alert alert-success col-sm-6 ml-5 mt-2">Profile updated successfully</div>';
                    // Update session data if needed
                    $_SESSION['studname'] = $studname;
                    $_SESSION['stu_img'] = $img_folder;
                } else {
                    $passmsg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Unable to update profile: ' . $stmt->error . '</div>';
                }
                $stmt->close();
            } else {
                $passmsg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Database error: ' . $conn->error . '</div>';
            }
        }
    }
}
?>


<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="profile-form" method="POST" enctype="multipart/form-data">
                <h3 class="mb-4 text-center text-primary">Update Profile</h3>
                
                <!-- Profile Picture Preview -->
                <div class="text-center mb-4">
                    <?php if (!empty($stu_img)): ?>
                        <img src="<?php echo htmlspecialchars($stu_img); ?>" class="profile-picture-preview" id="imagePreview" alt="Profile Picture">
                    <?php else: ?>
                        <img src="../image/default-profile.jpg" class="profile-picture-preview" id="imagePreview" alt="Default Profile Picture">
                    <?php endif; ?>
                </div>
                
                <div class="upload-area mb-4">
                    <label for="stu_img" class="d-block mb-2">
                        <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                        <p class="mb-1">Click to upload profile picture</p>
                        <small class="text-muted">(JPEG, PNG or GIF, max 5MB)</small>
                    </label>
                    <input type="file" class="d-none" id="stu_img" name="stu_img" accept="image/jpeg,image/png,image/gif" onchange="previewImage(this)">
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="stuId">Student ID</label>
                            <input type="text" class="form-control" id="stuId" value="<?php echo htmlspecialchars($stud_id ?? ''); ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="stuemail">Email</label>
                            <input type="email" class="form-control" id="stuemail" value="<?php echo htmlspecialchars($stuemail ?? ''); ?>" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label for="studname">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="studname" name="studname" value="<?php echo htmlspecialchars($studname ?? ''); ?>" required>
                </div>
                
                <div class="form-group mb-4">
                    <label for="stu_occ">Occupation</label>
                    <input type="text" class="form-control" id="stu_occ" name="stu_occ" value="<?php echo htmlspecialchars($stu_occ ?? ''); ?>">
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4" name="updateStuNameBtn">
                        <i class="fas fa-save mr-2"></i> Update Profile
                    </button>
                </div>
                
                <?php if ($passmsg) echo $passmsg; ?>
            </form>
        </div>
                    </main>
    </div>
</div>

<script>
    // Toggle sidebar on mobile
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });

    // Image preview function
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const file = input.files[0];
        const reader = new FileReader();
        
        reader.onloadend = function() {
            preview.src = reader.result;
        }
        
        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "<?php echo !empty($stu_img) ? htmlspecialchars($stu_img) : '../image/default-profile.jpg'; ?>";
        }
    }
</script>

<?php
include('./stuInclude/footer.php');
?>