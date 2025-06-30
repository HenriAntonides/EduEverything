<?php
    session_start();
    include 'header.php';

    include 'database/dbConn.php';

    //checked to see if logged in
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        die("You need to be logged in");
        exit();
    }

    // Check to see if there is a valid order
    if (!isset($_GET['orderID'])) {
        echo "Invalid order.";
        exit();
    }

    $orderID = htmlspecialchars($_GET['orderID']);
    $username = $_SESSION['username'];

    // Get user email
    $userQuery = $conn->prepare("SELECT email FROM user WHERE username = ?");
    $userQuery->bind_param("s", $username);
    $userQuery->execute();
    $userResult = $userQuery->get_result();
    $userEmail = $userResult->fetch_assoc()['email'] ?? null;

    // Get back to email function
    /* if ($userEmail) {
        // Get order items
        $itemQuery = $conn->prepare("SELECT item.itemName, order_items.price FROM order_items JOIN item ON order_items.itemID = item.itemID WHERE order_items.orderID = ?");
        $itemQuery->bind_param("s", $orderID);
        $itemQuery->execute();
        $items = $itemQuery->get_result();

        $itemList = "";
        $total = 0;
        while ($row = $items->fetch_assoc()) {
            $itemList .= "- " . $row['itemName'] . ": R" . number_format($row['price'], 2) . "\n";
            $total += $row['price'];
        }

        // Email content
        $subject = "Your Order #$orderID Confirmation";
        $message = "Hi $username,\n\nThank you for your order!\n\nOrder Details:\n$orderID\n\nItems:\n$itemList\nTotal: R" . number_format($total, 2) . "\n\nWe’ll update you once it's shipped!\n\nRegards,\nEverything, Student";

        $headers = "From: no-reply@everythingstudent.com\r\n";

        // Send the email
        mail($userEmail, $subject, $message, $headers);
    } */

    // Clear the cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Successful</title>
    <link rel="stylesheet" href="css/orderSuccess.css">
</head>
<body>
    <div class="order-success-container">
        <h1>Order Confirmed</h1>
        <p>Thank you for your purchase!</p>
        <p>An email confirmation has been sent to your address.</p>

        <?php if ($orderID){ ?>
            <div class="order-id">
                Your Order ID: <?= $orderID ?>
            </div>
        <?php }else{ ?>
            <p>No order ID provided.</p>
        <?php } ?>

        <a class="back-home" href="dashboard.php">Back to Home</a>
    </div>
</body>
</html>