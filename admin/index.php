<?php
    session_start();

    // Ensure only admins can access this page
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'support'])) {
        header("Location: ./adminLogin.php");
        exit;
    }

    include "./adminHeader.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - EduEverything</title>
    <link rel="stylesheet" href="./css/admin.css">
</head>
<body>

    <main class="admin-main">
        <div class="admin-dashboard">
            <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
            <p>Select an option from the navigation to manage the platform.</p>

            <div class="admin-cards">
                <?php if ($_SESSION['role'] == 'admin') {?>
                <div class="card">
                    <h3>Employees</h3>
                    <p><a href="./employee/addEmployee.php">Add</a></p>
                    <p><a href="./employee/viewEmployee.php">View</a></p>
                </div>
                <?php } ?>
                <div class="card">
                    <h3>Users</h3>
                    <p><a href="./user/viewUser.php">Manage Users</a></p>
                </div>
                <div class="card">
                    <h3>Listings</h3>
                    <p><a href="./item/viewItem.php">Review Listings</a></p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>