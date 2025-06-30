<?php
    session_start();
    include './database/dbConn.php';

    if (!isset($_SESSION['username'])) {
        header("Location: ../login.php");
        exit;
    }

    if (!isset($_GET['orderID'])) {
        echo "Invalid order ID.";
        exit;
    }

    $orderID = $_GET['orderID'];
    $username = $_SESSION['username'];

    // Check if the order belongs to the current user
    $stmtCheck = $conn->prepare("SELECT orders.orderID FROM orders WHERE orders.orderID = ? AND orders.username = ?");
    $stmtCheck->bind_param("ss", $orderID, $username);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if (empty($resultCheck)) {
        echo "You are not authorized to view this order.";
        exit;
    }
    $stmtCheck->close();

    // Fetch the order items
    $stmtOItem = $conn->prepare("SELECT item.itemName, order_items.price FROM order_items JOIN item ON item.itemID = order_items.itemID WHERE orderID = ?");
    $stmtOItem->bind_param("s", $orderID);
    $stmtOItem->execute();
    $result = $stmtOItem->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    $stmtOItem->close();
    include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?= htmlspecialchars($orderID) ?> Details</title>
    <link rel="stylesheet" href="css/orderDetails.css">
</head>
<body>
    <div class="orderDetailsContainer">
        <h2>Order #<?= htmlspecialchars($orderID) ?> Details</h2>

        <?php if (empty($items)) { ?>
            <p>No items found for this order.</p>
        <?php } else { ?>
            <table class="orderDetailsTable">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Price (R)</th>
                        <th>Arrived</th>
                        <th>Subtotal (R)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach ($items as $item) {
                        $subtotal = $item['price'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($item['itemName']) ?></td>
                            <td><?= number_format($item['price'], 2) ?></td>
                            <td>Arrived</td>
                            <td><?= number_format($subtotal, 2) ?></td>
                        </tr>
                    <?php } ?>
                    <tr class="totalRow">
                        <td colspan="3"><strong>Total:</strong></td>
                        <td><strong>R<?= number_format($total, 2) ?></strong></td>
                    </tr>
                </tbody>
            </table>
        <?php } ?>
    </div>

    <?php include 'footer.php';?>
</body>
</html>