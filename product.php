<?php
    session_start();

    include 'database/dbConn.php';

    $productID = $_GET['id'] ?? null;
    $product = null;

    if ($productID){
        $stmt = $conn->prepare("SELECT item.*, media.url, user.name, user.surname, user.email FROM item LEFT JOIN media ON item.itemID = media.itemID LEFT JOIN user ON item.username = user.username WHERE item.itemID = ?");
        $stmt->bind_param("s", $productID);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
    }

    // Get all the images for this product
    $mediaStmt = $conn->prepare("SELECT url FROM media WHERE itemID = ?");
    $mediaStmt->bind_param("s", $productID);
    $mediaStmt->execute();
    $mediaResult = $mediaStmt->get_result();
    $mediaFiles = [];

    while($row = $mediaResult->fetch_assoc()){
        
        $mediaFiles[] = $row['url'];
    }

    include 'header.php';
    include 'navigationPanel.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $product ? htmlspecialchars($product['itemName'])  : "Product Not Found" ?> - EduEverything</title>
    <link rel="stylesheet" href="css/product.css" />
</head>
<body>

<main class="product-detail">
    <?php if ($product){ ?>
        <div class="carouselContainer">
            <?php //This is to ensure button is hidden if no images is shown
                if (!empty($mediaFiles)) { ?>
                    <button class="carouselBtn prev" onclick="moveSlide(-1)">&#10094;</button>
            <?php } ?>

            <div class="carouselImg" id="carouselImg">
                <?php if (!empty($mediaFiles)) { ?>
                    <?php foreach ($mediaFiles as $imgUrl) { ?>
                        <img src="<?= htmlspecialchars($imgUrl) ?>" alt="Product Image">
                    <?php } ?>
                <?php } else { ?>
                    <p>No images available for this item.</p>
                <?php } ?>
            </div>

            <?php if (!empty($mediaFiles)) { ?>
                <button class="carouselBtn next" onclick="moveSlide(1)">&#10095;</button>
            <?php } ?>
        </div>

        <div class="info-wrapper">
            <div class="details">
                <div style="clear: both;"></div>
                <h2><?= htmlspecialchars($product['itemName']) ?></h2>
                <p class="price">R<?= number_format($product['price'], 2) ?></p>
                <p><?= htmlspecialchars($product['description'] ?? 'No description available.') ?></p>

                <?php if (!empty($product['dateSold'])) { ?>
                        <p class="soldNotice"> This Item has already been sold. </p>
                        <button id="addToCartBtn" disabled style="opacity: 0.6; cursor: not-allowed;">Sold</button>
                <?php } else { ?>
                    <form id="addToCartForm" method="POST" action="cart.php">
                        <input type="hidden" name="itemID" value="<?= htmlspecialchars($product['itemID']) ?>">
                        <button type="submit" id="addToCartBtn">Add to Cart</button>
                    </form>
                <?php } ?>
            </div>

            <div class="seller">
                <strong>Sold by:</strong> 
                <span><?= htmlspecialchars($product['name']." ".$product['surname'] ?? $product['username']); ?></span>
                <strong>Email address:</strong> 
                <span><?= htmlspecialchars($product['email'] ?? "No email");?></span>
            </div>
        </div>

    <?php }else{ ?>
        <p>Sorry, product not found.</p>
    <?php } ?>
</main>

    <?php include 'footer.php'; ?>

    <script>
        // This is to go through the images of the product
        let currentSlide = 0;

        function moveSlide(direction) {
            const carousel = document.getElementById("carouselImg");
            const images = carousel.querySelectorAll("img");
            const total = images.length;

            currentSlide += direction;

            if (currentSlide < 0) {
                currentSlide = total - 1;
            }

            if (currentSlide >= total) {
                currentSlide = 0;
            }

            const offset = -currentSlide * 100;
            carousel.style.transform = `translateX(${offset}%)`;
        }
    </script>

    <?php $conn->close(); ?>
</body>
</html>