<?php
    session_start();

    if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
        header("Location: ../index.php");
        exit;
    }

    include '../database/dbConn.php';
    include 'adminHeader.php'; // optional shared admin layout

    $stmt = $conn->query("SELECT itemID, itemName, price, dateAdded FROM item ORDER BY dateAdded DESC");
?>

<h2>Manage Items</h2>
<table>
    <tr><th>ID</th><th>Name</th><th>Price</th><th>Date Added</th></tr>
    <?php while ($item = $stmt->fetch_assoc()): ?>
        <tr>
            <td><?= $item['itemID'] ?></td>
            <td><?= htmlspecialchars($item['itemName']) ?></td>
            <td>R<?= number_format($item['price'], 2) ?></td>
            <td><?= $item['dateAdded'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>