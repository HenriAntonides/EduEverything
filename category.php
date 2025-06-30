<?php 
    session_start();
    
    if(!isset($_SESSION["loggedin"])||$_SESSION["loggedin"]!==true){

    }

    include 'database/dbConn.php';  

    $categoryID = $_GET['category'] ?? null;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $itemsPerPage = 20;
    $offset = ($page - 1) * $itemsPerPage;

    $selectedCategoryName = '';
    $parentCategory = null;
    $subcategories = [];
    $items = [];
    $totalItems = 0;

    if($categoryID){
        // Get the specific category from the database 
        $stmt = $conn->prepare("SELECT * FROM category WHERE categoryID = ?");
        $stmt->bind_param("i", $categoryID);
        $stmt->execute();
        $categoryResult = $stmt->get_result();
        $selectedCategory = $categoryResult->fetch_assoc();

        // Get the subcategory, if it has any
        if($selectedCategory){
            $selectedCategoryName = $selectedCategory['categoryName'];
            $subQuery = $conn->prepare("SELECT * FROM category WHERE parentID = ?");
            $subQuery->bind_param("i", $categoryID);
            $subQuery->execute();

            $result = $subQuery->get_result();
            $subcategories = [];
            while ($row = $result->fetch_assoc()) {
                $subcategories[] = $row;
            }

            // Get items in subcategories
            if (!empty($subcategories)) {

                //Get the items from all the sub categories
                // Ensures only the correct amount of subcategories is added
                $placeholders = implode(',', array_fill(0, count($subcategories), '?'));
                //Only extract subcategories IDs
                $subIDs = array_column($subcategories, 'categoryID');
                // Make sure when binding that the IDs is not count together
                $types = str_repeat('i', count($subIDs));

                // Get the total items in the category
                $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM item WHERE categoryID IN ($placeholders) AND dateSold IS NULL");
                $countStmt->bind_param($types, ...$subIDs);
                $countStmt->execute();
                $totalItems = $countStmt->get_result()->fetch_assoc()['total'];
                $totalPages = ceil($totalItems / $itemsPerPage);

                // Get the items from the database
                $query = "SELECT item.*, (SELECT media.url FROM media WHERE media.itemID = item.itemID ORDER BY media.mediaID ASC LIMIT 1) as url FROM item WHERE categoryID IN ($placeholders) AND dateSold IS NULL ORDER BY dateAdded DESC LIMIT ?, ?";

                $stmt = $conn->prepare($query);
                // Add the last two parameters types
                $types .= 'ii';
                $params = array_merge($subIDs, [$offset, $itemsPerPage]);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                $items = [];

                while ($row = $result->fetch_assoc()) {
                    $items[] = $row;
                }
            } else {
                // Get the total items in the sub category
                $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM item WHERE categoryID = ? AND dateSold IS NULL");
                $countStmt->bind_param("i", $categoryID);
                $countStmt->execute();
                $totalItems = $countStmt->get_result()->fetch_assoc()['total'];
                $totalPages = ceil($totalItems / $itemsPerPage);

                // Get the items from the database
                $stmt = $conn->prepare("SELECT item.*, (SELECT media.url FROM media WHERE media.itemID = item.itemID ORDER BY media.mediaID ASC LIMIT 1) as url FROM item WHERE categoryID = ? AND dateSold IS NULL ORDER BY dateAdded DESC LIMIT ?, ?");

                $stmt->bind_param("iii", $categoryID, $offset, $itemsPerPage);
                $stmt->execute();
                $result = $stmt->get_result();
                $items = [];

                while ($row = $result->fetch_assoc()) {
                    $items[] = $row;
                }

                // Get sibling subcategories
                if ($selectedCategory['parentID']) {
                    $parentID = $selectedCategory['parentID'];

                    $stmt = $conn->prepare("SELECT * FROM category WHERE parentID = ?");
                    $stmt->bind_param("i", $parentID);
                    $stmt->execute();

                    $result = $stmt->get_result();
                    $subcategories = [];

                    while ($row = $result->fetch_assoc()) {
                        $subcategories[] = $row;
                    }

                    $parentQuery = $conn->prepare("SELECT * FROM category WHERE categoryID = ?");
                    $parentQuery->bind_param("i", $parentID);
                    $parentQuery->execute();
                    $parentCategory = $parentQuery->get_result()->fetch_assoc();
                }
            }
        }
    }

    // Call the header to display
    include 'header.php';
    include 'navigationPanel.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $selectedCategoryName ?? '' ?> - EduEverything</title>
    <link rel="stylesheet" href="css/dashStyles.css" />
</head>
<body>

<section class="categoryHero">
    <h1><?= $selectedCategoryName ?></h1>
    <p>Browse top-rated items in <?= $selectedCategoryName ?? 'this section' ?>.</p>
</section>

<?php if (!empty($subcategories)) { ?>
    <nav class="subcategoryNav">
         <?php if (!empty($parentCategory)){ ?>
            <a href="category.php?category=<?= $parentCategory['categoryID'] ?>">← All <?= htmlspecialchars($parentCategory['categoryName']) ?></a>
        <?php } 

            // Go through all subcategories and display them
            foreach ($subcategories as $subKey){ ?> 
                <a 
                    href="category.php?category=<?= $subKey['categoryID'] ?>" 
                    <?= $subKey['categoryID'] === $categoryID ? 'style="font-weight:bold"' : '' ?>>
                    <?= $subKey['categoryName'] ?>
                </a>
        <?php } ?>
    </nav>
<?php } ?>

<main class="contentArea">
    <section class="productGrid">
        <!-- Go through all the products and display it -->
        <?php 
            if (!empty($items)){
                foreach ($items as $item){ ?>
                    <div class="productCard">
                        <img src="<?= htmlspecialchars($item['url'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($item['itemName']) ?>" />
                        <h3><?= htmlspecialchars($item['itemName']) ?></h3>
                        <p class="price">R<?= number_format($item['price'], 2) ?></p>
                        <a href="product.php?id=<?= urlencode($item['itemID']) ?>">
                            <button>Buy Now</button>
                        </a>
                    </div>
                <?php } 
            }else{ ?>
                <p>No products found in this category.</p>
        <?php } ?>
    </section>
</main>

    <?php if ($totalItems > $itemsPerPage){ ?>
    <div class="pageNext">
        <?php if ($page > 1){ ?>
            <a href="?category=<?= $categoryID ?>&page=<?= $page - 1 ?>">&laquo; Previous</a>
        <?php }

        for ($i = 1; $i <= $totalPages; $i++){ ?>
            <?php if ($i == $page){ ?>
                <span class="currentPage"><?= $i ?></span>
            <?php } else { ?>
                <a href="?category=<?= $categoryID ?>&page=<?= $i ?>"><?= $i ?></a>
            <?php } ?>
        <?php }

        if ($page < $totalPages): ?>
            <a href="?category=<?= $categoryID ?>&page=<?= $page + 1 ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
    <?php }

    include 'footer.php'; ?>

    <?php $conn->close(); ?>
</body>
</html>
