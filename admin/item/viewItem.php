<?php
    session_start();

    require_once '../../database/dbConn.php';

    // Check to see if a user is a Admin/Support
    if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'support'])) {
        if (!isset($_SESSION['role'])){
            header("Location: ../adminLogin.php");
            exit;
        }
        echo ("Only Admins/Support may access this page");
        exit;
    }

    // Delete Item from database
    if (isset($_GET['delete'])) {
        $deleteItem = $_GET['delete'];
        $stmt = $conn->prepare("DELETE FROM item WHERE itemID = ?");
        $stmt->bind_param("s", $deleteItem);
        $stmt->execute();
        header("Location: ./viewItem.php");
        exit;
    }

    // Get all items and also ensure search can be done
    $search = $_GET['search'] ?? '';
    $searchQuery = "%$search%";

    $stmt = $conn->prepare("SELECT item.*, user.username, user.name, user.surname FROM item LEFT JOIN user ON item.username = user.username WHERE (item.itemID LIKE ? OR item.itemName LIKE ? OR user.username LIKE ? OR user.name LIKE ? OR user.surname LIKE ?) ORDER BY item.dateAdded DESC, item.dateSold ASC");
    $stmt->bind_param("sssss", $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery);
    $stmt->execute();
    $item = $stmt->get_result();

    include '../adminHeader.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../css/view.css">
</head>
<body>
    <div class="Management">
        <h1>Item Management</h1>

        <form method="GET" class="searchForm">
            <input type="text" name="search" placeholder="Search employees..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Price (R)</th>
                    <th>Seller</th>
                    <th>Seller Username</th>
                    <th>Date Posted</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($itm = $item->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($itm['itemName']) ?></td>
                        <td><?= htmlspecialchars($itm['price']) ?></td>
                        <td><?= htmlspecialchars($itm['name'] . " " . $itm['surname']) ?></td>
                        <td><?= htmlspecialchars($itm['username']) ?></td>
                        <td><?= htmlspecialchars($itm['dateAdded']) ?></td>
                        <td><?= $itm['dateSold'] ? 'Sold' : 'Available'?></td>
                        <td>
                            <a class="btn edit" href="editItem.php?itemID=<?= urlencode($itm['itemID']) ?>">Edit</a>
                            <a class="btn delete" href="viewItem.php?delete=<?= urlencode($itm['itemID']) ?>" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>