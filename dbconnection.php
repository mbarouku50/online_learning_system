<?php
$db_host = "localhost";
$db_user = "phpmyadmin";
$db_password = "m";
$db_name = "03_2481_01_01_2023";

//create connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

//check connection
if($conn->connect_error){
    die("connection failed");
}


?>
