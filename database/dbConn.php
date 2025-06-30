<?php
// This file is to centralize the connection and makes it easier to change database locations without having to go to  every istance where the database it being called
// Database connection
/* $host= "sql113.infinityfree.com";
$db_user="if0_39159696";
$db_password="gazL4NGPWxr8HLu";
$db_name="if0_39159696_edu_db"; */

$host= "localhost";
$db_user="root";
$db_password="";
$db_name="mywebsitedb2";

$conn = mysqli_connect($host, $db_user, $db_password, $db_name);
//Check to see if connection is valid
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>