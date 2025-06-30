<?php
    session_start();
    include 'database/dbConn.php';

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        die("Error: You must be logged in to checkout.");
        exit();
    }

    $username = $_SESSION['username'];

    // Fetch cart items
    $stmt = $conn->prepare("SELECT item.itemID, item.itemName, item.price FROM cart JOIN item ON cart.itemID = item.itemID WHERE cart.username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $cartItems = [];
    $total = 0;
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
        $total += $row['price'];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['address']) && !empty(trim($_POST['address']))) {
        $orderID = uniqid('ORD');
        $address = trim($_POST['address']);

        // Insert into orders table
        $stmt = $conn->prepare("INSERT INTO orders (orderID, username, address, total, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssd", $orderID, $username, $address, $total);
        $stmt->execute();

        // Insert each item into order_items table
        $stmt = $conn->prepare("INSERT INTO order_items (orderID, itemID, price) VALUES (?, ?, ?)");
        foreach ($cartItems as $item) {
            $stmt->bind_param("ssd", $orderID, $item['itemID'], $item['price']);
            $stmt->execute();
        }

        // Update each item with the dateSold
        $dateSold = date("Y-m-d");
        $stmt = $conn->prepare("UPDATE item SET dateSold = ? WHERE itemID = ?");
        foreach ($cartItems as $item) {
            $stmt->bind_param("ss", $dateSold, $item['itemID']);
            $stmt->execute();
        }

        header("Location: checkoutPayment.php?orderID=$orderID");
        exit();
    }

    include 'header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Checkout</title>
    <link rel="stylesheet" href="css/checkout.css">
</head>
<body>
    <main class="cart-container">
        <h2>Checkout</h2>

        <?php if (count($cartItems) === 0) { ?>
            <p>Your cart is empty.</p>
        <?php } else { ?>
            <ul>
                <?php foreach ($cartItems as $item) { ?>
                    <li><?= htmlspecialchars($item['itemName']) ?> - R<?= number_format($item['price'], 2) ?></li>
                <?php } ?>
            </ul>
                <li>Shipping - R<?= number_format($total * 0.1, 2) ?></li>
            <h3>Total: R<?= number_format($total * 1.1, 2) ?></h3>

            <form method="POST">
                <label for="address">Delivery Address:</label><br>
                <textarea name="address" id="address" required></textarea><br><br>
                <button type="submit">Confirm Order</button>
            </form>
        <?php } ?>
    </main>
</body>
</html>