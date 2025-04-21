<?php
$db_host = "localhost";
$db_user = "03.2481.01.01.2023_user";
$db_password = "mbarouk@2481";
$db_name = "03.2481.01.01.2023_db";

//create connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

//check connection
if($conn->connect_error){
    die("connection failed");
}


?>
