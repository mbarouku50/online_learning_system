<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once(__DIR__ . '/../dbconnection.php');

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

if(isset($_POST['stusignup']) && isset($_POST['studname']) && isset($_POST['studreg']) 
&& isset($_POST['stuemail']) && isset($_POST['stupass'])){

    $studname = $conn->real_escape_string($_POST['studname']);
    $studreg = $conn->real_escape_string($_POST['studreg']);
    $stuemail = $conn->real_escape_string($_POST['stuemail']);
    $stupass = password_hash($conn->real_escape_string($_POST['stupass']), PASSWORD_DEFAULT);

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO student(studname, studreg, stuemail, stupass) VALUES(?, ?, ?, ?)");
    $stmt->bind_param("ssss", $studname, $studreg, $stuemail, $stupass);

    if($stmt->execute()){
        echo json_encode(["status" => "success", "message" => "Registration successful"]);
    }else{
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
}
$conn->close();
?>