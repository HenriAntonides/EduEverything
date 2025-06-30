<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - EduEverything</title>
    <link rel="stylesheet" href="/dv.2022.z2v5t5/admin/css/adminHeader.css">
</head>
<body>
    
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-6QDZGG51XB"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-6QDZGG51XB');
    </script>

    <header class="admin-header">
        <div class="admin-container">
            <h1>Admin Panel</h1>
            <nav class="admin-nav">
                <a href="/dv.2022.z2v5t5/admin/index.php">Dashboard</a>
                <?php if ($_SESSION['role'] == 'admin') {?>
                    <a href="/dv.2022.z2v5t5/admin/employee/addEmployee.php">Add Employee</a>
                    <a href="/dv.2022.z2v5t5/admin/employee/viewEmployee.php">View Employee</a>
                <?php } ?>
                <a href="/dv.2022.z2v5t5/admin/user/viewUser.php">Manage Users</a>
                <a href="/dv.2022.z2v5t5/admin/item/viewItem.php">Listings</a>
                <a href="/dv.2022.z2v5t5/admin/logout.php">Logout</a>
            </nav>
        </div>
    </header>
</body>