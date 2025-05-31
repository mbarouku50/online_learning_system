<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
$response = ["status" => "error", "message" => "Invalid request"];

try {
    if (isset($_POST['checkLogemail'])) {
        include_once(__DIR__ . '/../dbconnection.php');
        
        if (empty($_POST['admin_email']) || empty($_POST['admin_pass'])) {
            $response["message"] = "Email and password are required";
            echo json_encode($response);
            exit();
        }

        $admin_email = $_POST['admin_email'];
        $password = $_POST['admin_pass'];

        $stmt = $conn->prepare("SELECT admin_id, admin_email, admin_pass FROM admin WHERE admin_email = ?");
        
        if (!$stmt) {
            $response["message"] = "Database error";
            echo json_encode($response);
            exit();
        }

        $stmt->bind_param("s", $admin_email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            // DIRECT PASSWORD COMPARISON (INSECURE)
            if ($password === $row['admin_pass']) {
                session_regenerate_id(true);
                $_SESSION['is_admin_login'] = true;
                $_SESSION['admin_id'] = $row['admin_id'];
                $_SESSION['admin_email'] = $row['admin_email'];
                
                $response = [
                    "status" => "success", 
                    "message" => "Login successful",
                    "redirect" => "admin/adminDashboard.php"
                ];
            } else {
                $response["message"] = "Invalid password";
            }
        } else {
            $response["message"] = "Account not found";
        }
        
        $stmt->close();
        $conn->close();
    }
} catch (Exception $e) {
    $response["message"] = "Server error: " . $e->getMessage();
}

echo json_encode($response);
exit();
?>