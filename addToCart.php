<?php
    session_start();
    include 'database/dbConn.php';

    // Redirect to login if not logged in
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        die("Error: You must be logged in to add items to your cart.");
        exit();
    }

    //Check to see if an itemID was sent through
    if (isset($_POST['itemID'])){
        $itemID = $_POST['itemID'];
        $username = $_SESSION["username"];

        // get the total amount of items to keep the amount below 10
        echo ("ID ".$itemID." ".$username);
        $countQuery = $conn->prepare("SELECT COUNT(username) AS total FROM cart WHERE username = ?");
        $countQuery->bind_param("s", $username);
        $countQuery->execute();
        $countResult = $countQuery->get_result();
        $row = $countResult->fetch_assoc();
        $totalItems = (int)$row['total'];

        echo ("TOT ".$totalItems);
        if ($totalItems >= 10) {
            $_SESSION["cart_error"] = "You cannot add more than 10 items to your cart.";
            header("Location: product.php?id=" . urlencode($itemID));
            exit();
        }

        // Check if item already in cart
        $stmt = $conn->prepare("SELECT itemID FROM cart WHERE username = ? AND itemID = ?");
        $stmt->bind_param("ss", $username, $itemID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Already in cart
            header("Location: Cart.php");
            exit();
        } else {
            // Insert new row
            $insert = $conn->prepare("INSERT INTO cart (username, itemID) VALUES (?, ?)");
            $insert->bind_param("ss", $username, $itemID);
            $insert->execute();

            header("Location: Cart.php");
            exit();
        }
    } else {
        echo "Error: Invalid request.";
        exit();
    }
?>