<?php
// Set headers for JSON response
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Initialize response array
$response = ["status" => "error", "message" => "Invalid request"];

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Only process POST requests with checkLogStemail parameter
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkLogStemail'])) {
        // Include database connection
        require_once(__DIR__ . '/../dbconnection.php');
        
        // Validate required fields
        if (empty($_POST['stuemail']) || empty($_POST['stupass'])) {
            $response["message"] = "Email and password are required";
            echo json_encode($response);
            exit();
        }

        // Sanitize inputs
        $stuemail = filter_var($_POST['stuemail'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['stupass'];

        // Prepare SQL statement
        $stmt = $conn->prepare("SELECT stud_id, stuemail, stupass FROM student WHERE stuemail = ? LIMIT 1");
        
        if (!$stmt) {
            throw new Exception("Database preparation error: " . $conn->error);
        }

        $stmt->bind_param("s", $stuemail);
        
        if (!$stmt->execute()) {
            throw new Exception("Execution error: " . $stmt->error);
        }

        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $row['stupass'])) {
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                // Set session variables
                $_SESSION['is_login'] = true;
                $_SESSION['stud_id'] = $row['stud_id'];
                $_SESSION['stuemail'] = $row['stuemail'];
                
                $response = [
                    "status" => "success", 
                    "message" => "Login successful",
                    "redirect" => "index.php"
                ];
            } else {
                $response["message"] = "Invalid email or password";
            }
        } else {
            $response["message"] = "Invalid email or password";
        }
        
        $stmt->close();
        $conn->close();
    }
} catch (Exception $e) {
    $response["message"] = "Server error: " . $e->getMessage();
    error_log("Login error: " . $e->getMessage());
}

// Send JSON response
echo json_encode($response);
exit();
?>