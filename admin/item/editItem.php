<?php
    session_start();
    include '../../database/dbConn.php';

    // Check to see if a user is a Admin/Support
    if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'support'])) {
        if (!isset($_SESSION['role'])){
            header("Location: ../adminLogin.php");
            exit;
        }
        echo ("Only Admins/Support may access this page");
        exit;
    }

    $itemID = $_GET['itemID'] ?? null;
    if (!$itemID) {
        header("Location: viewItem.php");
        exit;
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $itemName = $_POST['itemName'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $sold = isset($_POST['sold']) ? 1 : 0;

        $stmt = $conn->prepare("UPDATE item SET itemName = ?, description = ?, price = ?, dateSold = ? WHERE itemID = ?");
        $soldDate = $sold ? date('Y-m-d') : null;
        $stmt->bind_param("ssdsi", $itemName, $description, $price, $soldDate, $itemID);
        $stmt->execute();

        if (!empty($_POST['deleteMedia'])) {
            foreach ($_POST['deleteMedia'] as $mediaID) {
                // Get file path to delete from server
                $getPathStmt = $conn->prepare("SELECT url FROM media WHERE mediaID = ?");
                $getPathStmt->bind_param("s", $mediaID);
                $getPathStmt->execute();
                $getPathResult = $getPathStmt->get_result();
                $file = $getPathResult->fetch_assoc();

                if ($file && file_exists('../../' . $file['url'])) {
                    $filePath = './' . $file['url'];
                    if (file_exists($filePath)) {
                        // Delete file from server
                        unlink($filePath); 

                        $folderPath = dirname($filePath);

                        if (is_dir($folderPath) && count(glob($folderPath . "/*")) === 0) {
                            // Delete folder if empty
                            rmdir($folderPath); 
                        }
                    }
                }

                // Delete from DB
                $delStmt = $conn->prepare("DELETE FROM media WHERE mediaID = ?");
                $delStmt->bind_param("s", $mediaID);
                $delStmt->execute();
            }
        }

        header("Location: viewItem.php");
        exit;
    }

    // Fetch item details
    $stmt = $conn->prepare("SELECT * FROM item WHERE itemID = ?");
    $stmt->bind_param("s", $itemID);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    if (!$item) {
        echo "Item not found.";
        exit;
    }

    // Fetch media for the item
    $mediaStmt = $conn->prepare("SELECT * FROM media WHERE itemID = ?");
    $mediaStmt->bind_param("s", $itemID);
    $mediaStmt->execute();
    $mediaResult = $mediaStmt->get_result();
    $mediaFiles = $mediaResult->fetch_all(MYSQLI_ASSOC);

    include '../adminHeader.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Item</title>
    <link rel="stylesheet" href="../css/edit.css">
</head>
<body>
    <div class="changeEdit">
        <h2>Edit Item</h2>
        <form method="POST">
            <label>Item Name:</label>
            <input type="text" name="itemName" value="<?= htmlspecialchars($item['itemName']) ?>" required>

            <label>Description:</label>
            <textarea name="description" required><?= htmlspecialchars($item['description']) ?></textarea>

            <label>Price (R):</label>
            <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($item['price']) ?>" required>

            <?php if (!empty($mediaFiles)){ ?>
                <label>Existing Photos:</label>
                <div class="mediaGallery">
                    <?php foreach ($mediaFiles as $media){ ?>
                        <div class="mediaItem">
                            
                            <img src="../../<?= htmlspecialchars($media['url']) ?>" alt="Media" />
                            <label>
                                <input type="checkbox" name="deleteMedia[]" value="<?= $media['mediaID'] ?>"> Delete
                            </label>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <label>Status:</label>
            <input type="checkbox" name="sold" <?= $item['dateSold'] ? 'checked' : '' ?>> Mark as Sold

            <button type="submit">Update Item</button>
        </form>
    </div>
</body>
</html>