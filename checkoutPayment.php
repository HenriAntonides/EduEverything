<!-- checkoutPayment.php -->
<?php
    session_start();

    include 'database/dbConn.php';

    $orderID = htmlspecialchars($_GET['orderID']);
    $stmt = $conn->prepare("SELECT total, username FROM orders WHERE orderID = ?");
    $stmt->bind_param("s", $orderID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        die("Invalid order ID");
    }

    $amount = number_format($row['total'], 2, '.', '');
    $username = $row['username'];

    $stmt = $conn->prepare("SELECT email FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $customerEmail = $row['email'];

    $merchant_id = "10039474"; // Sandbox merchant ID
    $merchant_key = "nbmya2c1x9pl0"; // Sandbox merchant key
    $return_url = "http://localhost/dv.2022.z2v5t5/orderSuccess.php?orderID=$orderID";
    $cancel_url = "http://localhost/dv.2022.z2v5t5/checkout.php";

    // Create payment request string
    $data = [
        'merchant_id' => $merchant_id,
        'merchant_key' => $merchant_key,
        'return_url' => $return_url,
        'cancel_url' => $cancel_url,
        'amount' => $amount,
        'item_name' => "Order #" . $orderID,
        'email_address' => $customerEmail,
    ];

    // Generate query string
    $pf_url = "https://sandbox.payfast.co.za/eng/process";
    $query_string = http_build_query($data);

    // Use to test the payment gateway: https://sandbox.payfast.co.za/dashboard
    //Q9nm0RoBEXW1oQ%
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Redirecting to PayFast</title>
</head>
<body>
    <h2>Redirecting to PayFast for payment...</h2>
    <form id="payfastForm" action="<?= $pf_url ?>" method="POST">
        <?php foreach ($data as $key => $value){ ?>
            <input type="hidden" name="<?= $key ?>" value="<?= htmlspecialchars($value) ?>">
        <?php } ?>
        <noscript><button type="submit">Click here if not redirected...</button></noscript>
    </form>

    <script>
        document.getElementById("payfastForm").submit();
    </script>
</body>
</html>