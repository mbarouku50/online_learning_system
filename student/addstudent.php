<?php
if(!isset($_SESSION)){
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once(__DIR__ . '/../dbconnection.php');

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Handle registration
if(isset($_POST['stusignup'])) {
    // Verify all required fields are present
    if(!isset($_POST['studname']) || !isset($_POST['studreg']) || 
       !isset($_POST['stuemail']) || !isset($_POST['stupass'])) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        $conn->close();
        exit();
    }

    $studname = $conn->real_escape_string($_POST['studname']);
    $studreg = $conn->real_escape_string($_POST['studreg']);
    $stuemail = $conn->real_escape_string($_POST['stuemail']);
    $stupass = password_hash($_POST['stupass'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO student(studname, studreg, stuemail, stupass) VALUES(?, ?, ?, ?)");
    $stmt->bind_param("ssss", $studname, $studreg, $stuemail, $stupass);

    if($stmt->execute()){
        echo json_encode(["status" => "success", "message" => "Registration successful"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }
    $stmt->close();
    $conn->close();
    exit();
}

// Handle login
if(!isset($_SESSION['is_login'])){
if(isset($_POST['checkLogemail'])) {
    
    include_once(__DIR__ . '/../dbconnection.php');
    if ($conn->connect_error) {
        die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
    }

    if(!isset($_POST['stuLogemail']) || !isset($_POST['stuLogpass'])) {
        echo json_encode(["status" => "error", "message" => "Email and password required"]);
        exit();
    }

    $stuLogemail = $conn->real_escape_string($_POST['stuLogemail']);
    $password = $_POST['stuLogpass'];

    $stmt = $conn->prepare("SELECT studname, stupass FROM student WHERE stuemail = ?");
    $stmt->bind_param("s", $stuLogemail);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if(password_verify($password, $row['stupass'])) {
            $_SESSION['stuemail'] = $stuLogemail;
            $_SESSION['studname'] = $row['studname'];
            echo json_encode(["status" => "success", "message" => "Login successful", "redirect" => "index.php"]);
            $_SESSION['is_login'] = true;
            $_SESSION['stuLogemail'] =$stuLogemail;
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }
    
    $stmt->close();
    $conn->close();
    exit();
}

echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>