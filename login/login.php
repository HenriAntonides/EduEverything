<?php
    session_start();

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $username=trim($_POST["username"]);
        $password=trim($_POST["password"]);
     
    if(empty($username)||empty($password)){
        die("Error: All fields are required");
    }

    include '../database/dbConn.php';

    //check to see if username exist
    $sql = "SELECT password FROM user WHERE userName = '$username'";
    $check=mysqli_query($conn, $sql);

    if(mysqli_num_rows($check) > 0){
        $user=mysqli_fetch_assoc($check);

        // Make sure password exist
        if(password_verify($password, $user["password"])){
            $_SESSION["loggedin"]=true;
            $_SESSION["username"]=$username;

            // Update last login
            $today = date("Y/m/d");
            $updateStmt = $conn->prepare("UPDATE user SET lastLogin = ? WHERE userName = ?");
            $updateStmt->bind_param("ss", $today, $username);
            $updateStmt->execute();

            mysqli_query($conn, $sql);

            if (!empty($_POST["redirect"])) {
                //Redirect to last location
                $redirect = basename($_POST["redirect"]);
                $updateStmt -> close();
                $conn -> close();
                header("Location: ../" . $redirect);
                exit;
            } else {
                //Redirect to dashboard
                $updateStmt -> close();
                $conn -> close();
                header("Location: ../dashboard.php");
                exit();
            }
        }else{
            echo "Error: Incorrect Password";
        }
    }else {
        echo "Error: Username not found";
    }
}
?>