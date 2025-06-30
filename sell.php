<?php
    session_start();
    include 'database/dbConn.php';

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        echo "You must be logged in to list an item.";
        exit;
    }

    $username = $_SESSION['username'];

    include 'header.php';

    // Fetch categories
    $categories = [];
    $result = $conn->query("SELECT categoryID, categoryName FROM category WHERE categoryID NOT IN (1, 2, 12, 13, 14, 15)");
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }    

    // Make sure the post comes from the form sellItemForm
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $itemID = uniqid('item_'); 
        $itemName = $_POST['itemName'] ?? '';
        $description = $_POST['description'] ?? '';
        $categoryID = $_POST['categoryID'] ?? 0;
        $price = $_POST['price'] ?? 0;
        $dateAdded = date('Y-m-d');

        $uploadDir = 'mediaUploads/';
        $itemDir = $uploadDir . $itemID . '/';
        $mediaCount = 0;
        $mediaFiles = [];

        // Check to see if folder exist for item, otherwise create folder to store media
        if(!file_exists($itemDir)){
            mkdir($itemDir, 0777, true);
        }

        if (isset($_FILES['mediaFiles'])) {
            $seenFiles = [];
            foreach ($_FILES['mediaFiles']['tmp_name'] as $key => $tmpName) {
                if ($mediaCount >= 5) { break; }

                $originalName = $_FILES['mediaFiles']['name'][$key];
                $fileSize = $_FILES['mediaFiles']['size'][$key];

                // Skip if file is too large
                if ($fileSize > 2 * 1024 * 1024) { continue; }

                // Prevent duplicate filenames
                if (in_array($originalName, $seenFiles)) { continue; }
                $seenFiles[] = $originalName;

                $targetFile = $itemDir . uniqid('img_', true) . "_" . basename($originalName);

                if (move_uploaded_file($tmpName, $targetFile)) {
                    $mediaFiles[] = $targetFile;
                    $mediaCount++;
                }
            }
        }

        $stmt = $conn->prepare("INSERT INTO item (itemID, itemName, description, username, categoryID, totMedia, price, dateAdded) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiids", $itemID, $itemName, $description, $username, $categoryID, $mediaCount, $price, $dateAdded);
        
        if ($stmt->execute()) {
            $insertMediaStmt = $conn->prepare("INSERT INTO media (mediaID, itemID, url) VALUES (?, ?, ?)");
            foreach ($mediaFiles as $fileUrl) {
                $mediaID = uniqid('media_'); 
                $insertMediaStmt->bind_param("sss", $mediaID, $itemID, $fileUrl);
                $insertMediaStmt->execute();
            }

            $_SESSION['success'] = "Item listed successfully!";
            header("Location: sell.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sell an Item</title>
    <link rel="stylesheet" href="css/sell.css">
</head>
<body>
    <?php if (isset($_SESSION['success'])){ ?>
        <div class="successMessage"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php } ?>

    <div class="sellPage">
        <div class="formContainer">
            <h1>List a New Item for Sale</h1>
            <form id="sellItemForm" method="POST" action="sell.php" onsubmit="return validateSellForm()" enctype="multipart/form-data">
                <div class="formGroup">
                    <label>Item Name:</label>
                    <input type="text" name="itemName" required>
                    <span id="itemNameError" class="error"> </span><br>
                </div>

                <div class="formGroup">
                    <label>Description:</label>
                    <textarea name="description" required></textarea>
                    <span id="descriptionError" class="error"> </span><br>
                </div>

                <div class="formGroup">
                    <label>Category:</label>
                    <select name="categoryID" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat){ ?>
                            <option value="<?= htmlspecialchars($cat['categoryID']) ?>">
                                <?= htmlspecialchars($cat['categoryName']) ?>
                            </option>
                        <?php } ?>
                    </select>
                    <span id="categoryIDError" class="error"> </span><br>
                </div>

                <div class="formGroup">
                    <label for="media">Upload Images (up to 5):</label>
                    <input type="file" name="mediaFiles[]" multiple accept="image/*" required>
                    <span id="mediaFilesError" class="error"> </span><br>
                </div>

                <div class="formGroup">
                    <label>Price (R):</label>
                    <input type="number" step="1" name="price" min="1" required >
                    <span id="priceError" class="error"> </span><br>
                </div>

                <button class="submitBtn" type="submit">Submit</button>
            </form>
        </div>
    </div>

    <script src="javascript/sell.js"></script>

    <?php $conn->close(); ?>
</body>
</html>