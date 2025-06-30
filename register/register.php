<?php

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $username=trim($_POST["username"]);
    $password=trim($_POST["password"]);

    $name=trim($_POST["name"]);
    $surname=trim($_POST["surname"]);
    $email=trim($_POST["email"]);
    $phoneNr=trim($_POST["phoneNr"]);
    $birthDate=trim($_POST["birthDate"]);
    $IDNumber=trim($_POST["idNumber"]);
    $gender=trim($_POST["gender"]);
     
    if(empty($username)||empty($password)||empty($name)||empty($surname)||empty($email)||empty($phoneNr)||empty($birthDate)||empty($IDNumber)||empty($gender)){
        die("Error: All fields are required");
    }
     
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        die("Error: Invalid email Format");
    }

    include '../database/dbConn.php';

    // check to see if the new username/email already exist
    $sql = "SELECT * FROM user WHERE userName = '$username'";
    $check=mysqli_query($conn, $sql);

    if(mysqli_num_rows($check) > 0){
        die("Error: Username already exists");
    }

    $sql = "SELECT * FROM user WHERE email = '$email'";
    $check=mysqli_query($conn, $sql);

    if(mysqli_num_rows($check) > 0){
        die("Error: Email already exists");
    }

    //hashing password
    $hash = password_hash($password, PASSWORD_DEFAULT);

    //Insert user
    $sql="INSERT INTO user(username, password, name, surname, email, phoneNr, birthDate, idNumber, gender) VALUES ('$username', '$hash', '$name', '$surname', '$email', '$phoneNr', '$birthDate', '$IDNumber', '$gender')";
    
    if(mysqli_query($conn, $sql)){
        header("Location: ../login/login.html");
        exit;
    }else{
        echo "Error: ".mysqli_error($conn);
    }

    mysqli_Close($conn);
}
?>