<?php 
    session_start();     
    include 'database/dbConn.php';

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
        echo "Error: You must be logged in to view your cart.";
        exit();
    }

    $username = $_SESSION["username"];

    // Handle item removal from cart, check if request came from POSt, to ensure only then that item is removed
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeItemID'])) {
        $removeID = $_POST['removeItemID'];
        $stmt = $conn->prepare("DELETE FROM cart WHERE username = ? AND itemID = ?");
        $stmt->bind_param("ss", $username, $removeID);
        $stmt->execute();
    }

    //Check to see if an itemID was sent through
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['itemID'])){
        $itemID = $_POST['itemID'];
        $username = $_SESSION["username"];

        // get the total amount of items to keep the amount below 10
        echo ("ID ".$itemID." ".$username);
        $countQuery = $conn->prepare("SELECT COUNT(username) AS total FROM cart WHERE username = ?");
        $countQuery->bind_param("s", $username);
        $countQuery->execute();
        $countResult = $countQuery->get_result();
        $row = $countResult->fetch_assoc();
        $totalItems = (int)$row['total'];

        echo ("TOT ".$totalItems);
        if ($totalItems >= 10) {
            $_SESSION["cart_error"] = "You cannot add more than 10 items to your cart.";
            header("Location: product.php?id=" . urlencode($itemID));
            exit();
        }

        // Check if item already in cart
        $stmt = $conn->prepare("SELECT itemID FROM cart WHERE username = ? AND itemID = ?");
        $stmt->bind_param("ss", $username, $itemID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Already in cart
            header("Location: cart.php");
            exit();
        } else {
            // Insert new row
            $insert = $conn->prepare("INSERT INTO cart (username, itemID) VALUES (?, ?)");
            $insert->bind_param("ss", $username, $itemID);
            $insert->execute();

            header("Location: cart.php");
            exit();
        }
    }

    // Fetch cart items
    $stmt = $conn->prepare("SELECT item.itemID, item.itemName, item.price, (SELECT media.url FROM media WHERE media.itemID = item.itemID ORDER BY media.mediaID ASC LIMIT 1) AS url, item.dateSold FROM cart JOIN item ON cart.itemID = item.itemID WHERE cart.username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $cartItem = [];
    $soldItem = [];
    $total = 0;

    // Check to see if an item was sold, otherwise add it to the cartItem and total 
    while($row = $result->fetch_assoc()){
        if (!empty($row['dateSold'])) {
            $soldItem[] = $row;
        } else {
            $cartItem[] = $row;
            $total += $row['price'];
        }
    }

    // Delete the items from the cart
    foreach ($soldItem as $sold) {
        $stmt = $conn->prepare("DELETE FROM cart WHERE username = ? AND itemID = ?");
        $stmt->bind_param("ss", $username, $sold['itemID']);
        $stmt->execute();
    }

    include 'header.php';
    include 'navigationPanel.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=`device-width`, initial-scale=1.0">
    <title>Cart - Everything, Student</title>
    <link rel="stylesheet" href="css/cart.css" />
</head>
<body>
    
    <main class="cartContainer">
        <?php if (!empty($soldItem)){ ?>
            <div class="cartWarning">
                <p><strong>Warning:</strong> The following items were sold to someone else and have been removed from your cart:</p>
                <ul>
                    <?php foreach ($soldItem as $sold){ ?>
                        <li><?= htmlspecialchars($sold['itemName']) ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        <?php if (count($cartItem) > 0) { ?>
            <ul class="cartList">
                <?php foreach ($cartItem as $item){ ?>
                    <li class="cartItem">
                        <img src="<?= htmlspecialchars($item['url'] ?? 'placeholder.jpg') ?>" alt="<?= htmlspecialchars($item['itemName']) ?>">
                        <div class="itemDetails">
                            <h3><?= htmlspecialchars($item['itemName']) ?></h3>
                            <p>R<?= number_format($item['price'], 2) ?></p>
                        </div>

                        <form method="POST" action="Cart.php" class="removeForm" onsubmit="return showRemoveConfirmation(event)">
                            <input type="hidden" name="removeItemID" value="<?= htmlspecialchars($item['itemID']) ?>">
                            <button type="submit">Remove</button>
                        </form>            
                    </li>
                <?php } ?>
            </ul>

            <div class="cartSummary">
                <h2>Total: R<?= number_format($total, 2) ?></h2>
                <form method="POST" action="checkout.php" class="checkoutForm" onsubmit="return showCheckoutConfirmation(event)">
                    <input type="hidden" name="checkoutItems" value="<?= htmlspecialchars($username) ?>">
                    <button type="submit">Proceed to Checkout</button>
                </form>  
            </div>
        <?php } else { ?>
            <p style="color: red; text-align: center;">Your cart is empty.</p>
        <?php } ?>
    </main>

    <?php include 'footer.php'; ?>

    <div id="Confirmation" class="confirmation" style="display: none;">
        <div class="confirmationOverlay" onclick="closeRemoveConfirmation()"></div>
        <div class="confirmationBox">
            <h2>Confirm Removal</h2>
            <p>Are you sure you want to remove this from your cart?</p>
            <div class="confirmationActions">
                <button class="confirmBtn" onclick="submitRemove()">Yes, Remove</button>
                <button class="cancelBtn" onclick="closeRemoveConfirmation()">Cancel</button>
            </div>
        </div>
    </div>
</body>
</html>

<script>
    // Change the message to work for either the remove or the checkout confirmation
    let pendingForm = null;

    // This is to make sure a user wants to remove something from their cart
    function showRemoveConfirmation(event) {
        event.preventDefault();
        pendingForm = event.target;

        const modal = document.getElementById("Confirmation");
        const heading = modal.querySelector("h2");
        const message = modal.querySelector("p");
        const confBtn = modal.querySelector(".confirmBtn");

        heading.innerText = "Confirm Removal"
        message.innerText = "Are you sure you want to remove this from your cart?";
        confBtn.innerText = "Yes, Remove";

        modal.style.display = "flex";
        return false;
    }

    // This is to make sure a user wants to continue to checkout
    function showCheckoutConfirmation(event) {
        event.preventDefault();
        pendingForm = event.target;

        const modal = document.getElementById("Confirmation");
        const heading = modal.querySelector("h2");
        const message = modal.querySelector("p");
        const confBtn = modal.querySelector(".confirmBtn");

        heading.innerText = "Confirm Checkout"
        message.innerText = "Are you sure you want to proceed to checkout?";
        confBtn.innerText = "Yes, Proceed";

        modal.style.display = "flex";
        return false;
    }

    // Submit the form
    function submitRemove() {
        if (pendingForm) {
            pendingForm.submit();
        }
    }

    // Don't submit the form
    function closeRemoveConfirmation() {
        document.getElementById("Confirmation").style.display = "none";
        pendingForm = null;
    }

    
</script>