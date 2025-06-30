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

    // Delete User from database
    if (isset($_GET['delete'])) {
        $deleteUsername = $_GET['delete'];
        $stmt = $conn->prepare("DELETE FROM user WHERE username = ?");
        $stmt->bind_param("s", $deleteUsername);
        $stmt->execute();
        header("Location: ./viewUser.php");
        exit;
    }

    // Get all users and also ensure search can be done
    $search = $_GET['search'] ?? '';
    $searchQuery = "%$search%";

    $stmt = $conn->prepare("SELECT * FROM user WHERE (username LIKE ? OR name LIKE ? OR surname LIKE ? OR email LIKE ?)");
    $stmt->bind_param("ssss", $searchQuery, $searchQuery, $searchQuery, $searchQuery);
    $stmt->execute();
    $user = $stmt->get_result();

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
        <h1>User Management</h1>

        <form method="GET" class="searchForm">
            <input type="text" name="search" placeholder="Search employees..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Birth Date</th>
                    <th>ID Number</th>
                    <th>Gender</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($usr = $user->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($usr['username']) ?></td>
                        <td><?= htmlspecialchars($usr['name'] . ' ' . $usr['surname']) ?></td>
                        <td><?= htmlspecialchars($usr['email']) ?></td>
                        <td><?= htmlspecialchars($usr['phoneNr']) ?></td>
                        <td><?= htmlspecialchars($usr['birthDate']) ?></td>
                        <td><?= htmlspecialchars($usr['idNumber']) ?></td>
                        <td><?= htmlspecialchars($usr['gender']) ?></td>
                        <td>
                            <a class="btn edit" href="editUser.php?username=<?= urlencode($usr['username']) ?>">Edit</a>
                            <a class="btn delete" href="viewUser.php?delete=<?= urlencode($usr['username']) ?>" onclick="return confirm('Are you sure you want to delete this employee?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>