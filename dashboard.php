<?php
    session_start();

    include './database/dbConn.php';

    $search = $_GET['search'] ?? '';
    $searchQuery = "%$search%";

    // Check to see how many items per page
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $itemsPerPage = 21;
    $offset = ($page - 1) * $itemsPerPage;

    if (!empty($search)) {
        // Prepare the SQL statement to search for items
        $sql = "SELECT item.*, 
                (SELECT media.url FROM media WHERE media.itemID = item.itemID ORDER BY media.mediaID ASC LIMIT 1) AS url FROM item WHERE (itemName LIKE ? OR description LIKE ?) AND dateSold IS NULL ORDER BY dateAdded DESC LIMIT ?, ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $searchQuery, $searchQuery, $offset, $itemsPerPage);
    } else {
        // Default SQL statement to get all unsold items
        $sql = "SELECT item.*, 
                (SELECT media.url FROM media WHERE media.itemID = item.itemID ORDER BY media.mediaID ASC LIMIT 1) AS url FROM item WHERE dateSold IS NULL ORDER BY dateAdded DESC LIMIT ?, ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $offset, $itemsPerPage);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Count total items to know how many pages there would be
    if (!empty($search)) {
        $totalSql = "SELECT COUNT(*) as total FROM item WHERE (itemName LIKE ? OR description LIKE ?) AND dateSold IS NULL";
        $totalStmt = $conn->prepare($totalSql);
        $totalStmt->bind_param("ss", $searchQuery, $searchQuery);
        $totalStmt->execute();
        $totalResult = $totalStmt->get_result();
    } else {
        $totalSql = "SELECT COUNT(*) as total FROM item WHERE dateSold IS NULL";
        $totalResult = $conn->query($totalSql);
    }

    $totalItems = $totalResult->fetch_assoc()['total'];
    $totalPages = ceil($totalItems / $itemsPerPage);

    include 'header.php';
    include 'navigationPanel.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EduEverything</title>
    <link rel="stylesheet" href="css/dashStyles.css" />
</head>
<body>
    
    <section class="banner">
        <h2>This is only a Student Project!!</h2>
        <p>You cannot buy anything from this site!</p>
    </section>

    <main class="productGrid">
        <!-- Go through all the products and display it -->
         <?php 
            if($result->num_rows > 0){
                while($item = $result->fetch_assoc()){ ?>
                    <div class="productCard">
                        <img src="<?= htmlspecialchars($item['url'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($item['itemName']) ?>" />
                        <h3><?= htmlspecialchars($item['itemName']) ?></h3>
                        <p class="price">R<?= number_format($item['price'], 2) ?></p>
                        <a href="product.php?id=<?= urlencode($item['itemID']) ?>">
                            <button>Buy Now</button>
                        </a>
                    </div>
                <?php     
                }
           }else{
                echo "<p> No items found.</p>";
           }
           $conn->close()
         ?>
    </main>

    <!-- Ensure you can go to next page -->
    <div class="nextPage">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>">&laquo; Previous</a>
        <?php endif; ?>

        <?php
        // Display page numbers with current page highlighted
        for ($i = 1; $i <= $totalPages; $i++) {
            if ($i == $page) {
                echo "<span class='currentPage'>$i</span>";
            } else {
                echo "<a href='?page=$i'>$i</a>";
            }
        }
    ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>">Next &raquo;</a>
    <?php endif; ?>
</div>

    <?php include 'footer.php';?>

</body>
</html>

