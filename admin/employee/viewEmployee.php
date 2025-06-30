<?php
    session_start();

    require_once '../../database/dbConn.php';

    // Check to see if a user is a ADMIN
    if(!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
        if (!isset($_SESSION['role'])){
            header("Location: ../adminLogin.php");
            exit;
        }
        echo ("Only Admins may access this page");
        exit;
    }

    // Delete Employee from database
    if (isset($_GET['delete'])) {
        $deleteUsername = $_GET['delete'];
        $stmt = $conn->prepare("DELETE FROM employee WHERE username = ?");
        $stmt->bind_param("s", $deleteUsername);
        $stmt->execute();
        header("Location: ./viewEmployee.php");
        exit;
    }

    // Get all employees and also ensure search can be done
    $search = $_GET['search'] ?? '';
    $searchQuery = "%$search%";

    $stmt = $conn->prepare("SELECT * FROM employee WHERE (username LIKE ? OR name LIKE ? OR surname LIKE ? OR email LIKE ?)");
    $stmt->bind_param("ssss", $searchQuery, $searchQuery, $searchQuery, $searchQuery);
    $stmt->execute();
    $employees = $stmt->get_result();

    include '../adminHeader.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Employees</title>
    <link rel="stylesheet" href="../css/view.css">
</head>
<body>
    <div class="Management">
        <h1>Employee Management</h1>

        <form method="GET" class="searchForm">
            <input type="text" name="search" placeholder="Search employees..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($emp = $employees->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($emp['username']) ?></td>
                        <td><?= htmlspecialchars($emp['name']) ?></td>
                        <td><?= htmlspecialchars($emp['surname']) ?></td>
                        <td><?= htmlspecialchars($emp['email']) ?></td>
                        <td><?= $emp['role'] == 1 ? 'Admin' : 'Support' ?></td>
                        <td>
                            <a href="editEmployee.php?username=<?= urlencode($emp['username']) ?>">Edit</a> |
                            <a href="deleteEmployee.php?username=<?= urlencode($emp['username']) ?>" onclick="return confirm('Are you sure you want to delete this employee?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>