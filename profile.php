<?php
    session_start();

    include './database/dbConn.php';

    if (!isset($_SESSION['username'])) {
        header("Location: ../login.php");
        exit;
    }

    $username = $_SESSION['username'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['about'])) {
        $username = $_POST['username'];
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $email = $_POST['email'];
        $phoneNr = $_POST['phoneNr'];
        $gender = $_POST['gender'];

        $updateStmt = $conn->prepare("UPDATE user SET username = ?, name = ?, surname = ?, email = ?, phoneNr = ?, gender = ? WHERE username = ?");
        $updateStmt->bind_param("sssssss", $username, $name, $surname, $email, $phoneNr, $gender, $username);
        $updateStmt->execute();
        $stmt->execute();
    }

    // Fetch user data
    $stmtUser = $conn->prepare("SELECT name, surname, email, phoneNr, gender FROM user WHERE username = ?");
    $stmtUser->bind_param("s", $username);
    $stmtUser->execute();
    $user = $stmtUser->get_result()->fetch_assoc();

    // Handle deletion
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteItemID'])) {
        $itemID = $_POST['deleteItemID'];

        // Check if item belongs to this user
        $checkStmt = $conn->prepare("SELECT * FROM item WHERE itemID = ? AND username = ?");
        $checkStmt->bind_param("ss", $itemID, $username);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            $deleteStmt = $conn->prepare("DELETE FROM item WHERE itemID = ?");
            $deleteStmt->bind_param("s", $itemID);
            $deleteStmt->execute();
        }
        header("Location: profile.php");
        exit;
    }

    //Fetch items
    $stmtItems = $conn->prepare("SELECT item.*, (SELECT media.url FROM media WHERE media.itemID = item.itemID ORDER BY media.mediaID ASC LIMIT 1) AS url FROM item WHERE username = ?");
    $stmtItems->bind_param("s", $username);
    $stmtItems->execute();
    $result = $stmtItems->get_result();

    $items = [];

    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    $orders = [];
    $orderStmt = $conn->prepare("SELECT orders.orderID, orders.created_at, COUNT(order_items.itemID) AS totalItems, SUM(order_items.price) AS totalPrice FROM orders JOIN order_items ON orders.orderID = order_items.orderID WHERE orders.username = ? GROUP BY orders.orderID ORDER BY orders.created_at DESC");
    $orderStmt->bind_param("s", $username);
    $orderStmt->execute();
    $orders = $orderStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $username ? htmlspecialchars($username)  : "User Not Found" ?>  Profile</title>
    <link rel="stylesheet" href="css/profile.css" />
</head>
<body>
    <div class="profile">
        <h2>My profile</h2>

        <div class="profileSections">
            <div class="aboutSection">
                <h3>About You</h3>
                <form method="POST">
                    <label>UserName:</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($user['name']) ?>" required>

                    <label>First Name:</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

                    <label>Last Name:</label>
                    <input type="text" name="surname" value="<?= htmlspecialchars($user['surname']) ?>" required>

                    <label>Email:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

                    <label>Phone Number:</label>
                    <input type="text" name="phoneNr" value="<?= htmlspecialchars($user['phoneNr']) ?>" required>

                    <label>Gender:</label>
                    <select name="gender" required>
                        <option value="male" <?= $user['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= $user['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                        <option value="other" <?= $user['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>

                    <button type="submit">Update Info</button>
                </form>
            </div>

            <div class="ordersSection">
                <h3>Your Orders</h3>
                <?php if (!empty($orders)) { ?>
                    <table class="ordersTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Total Items</th>
                                <th>Price (R)</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order){ ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['orderID']) ?></td>
                                    <td><?= htmlspecialchars($order['totalItems']) ?></td>
                                    <td><?= number_format($order['totalPrice'], 2) ?></td>
                                    <td>
                                        <a href="orderDetails.php?orderID=<?= $order['orderID'] ?>">View</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p>You haven’t placed any orders yet.</p>
                <?php } ?>
            </div>
        </div>

        
        <h2>My Items listed</h2>
        <?php if(empty($items)) { ?>
            <p> You have not listed any items </p>
        <?php } else { ?>
            <div class="itemGrid">
                
                <?php foreach ($items as $item) { ?>
                    <div class="itemCard">
                        <img src="<?= htmlspecialchars($item['url'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($item['itemName']) ?>" />
                        <h3><?= htmlspecialchars($item['itemName']) ?></h3>
                        <p><?= htmlspecialchars($item['description']) ?></p>
                        <p>Price: R<?= number_format($item['price'], 2) ?></p>
                        <p>Status: <?= $item['dateSold'] ? "Sold" : "Available" ?></p>
                        <div class="actions">
                            <a href="editItemUser.php?itemID=<?= $item['itemID'] ?>">Edit</a>
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                <input type="hidden" name="deleteItemID" value="<?= $item['itemID'] ?>">
                                <button type="submit" class="deleteBtn">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>    

    <?php include 'footer.php';?>

</body>
</html>