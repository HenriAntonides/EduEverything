<?php
    session_start();

    // Log out of account
    session_unset();
    session_destroy();

    //redirect
    header("Location: ./adminLogin.php");

?>