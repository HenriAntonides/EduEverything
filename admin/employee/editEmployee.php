<?php
    session_start();
    include '../../database/dbConn.php';

    // Ensure the user is logged in and an admin
    if(!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
        if (!isset($_SESSION['role'])){
            header("Location: ../adminLogin.php");
            exit;
        }
        echo ("Only Admins may access this page");
        exit;
    }

    // Ensure the username exist
    $username = $_GET['username'] ?? null;
    if (!$username) {
        header("Location: viewEmployee.php");
        exit;
    }

    // Make sure every POST is used
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $phoneNr = $_POST['phoneNr'];
        $role = $_POST['role'];
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $password = $_POST['password'];

        //hashing password
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Update the employee data
        if (isset($password)){
            $stmt = $conn->prepare("UPDATE employee SET username = ?, name = ?, surname = ?, email = ?, phoneNr = ?, role = ?, password = ? WHERE username = ?");
            $stmt->bind_param("sssssiss", $username, $name, $surname, $email, $phoneNr, $role, $hash, $username);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("UPDATE employee SET username = ?, name = ?, surname = ?, email = ?, phoneNr = ?, role = ? WHERE username = ?");
            $stmt->bind_param("sssssis", $username, $name, $surname, $email, $phoneNr, $role, $username);
            $stmt->execute();
        }
        
        header("Location: viewEmployee.php");
        exit;
    }

    // Fetch existing data
    $stmt = $conn->prepare("SELECT * FROM employee WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();

    include '../adminHeader.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Employees</title>
    <link rel="stylesheet" href="../css/edit.css">
</head>
<body>
    <h2>Edit Employee</h2>
    <div class="changeEdit">
        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($employee['username']) ?>">

            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($employee['name']) ?>">

            <label>Surname:</label>
            <input type="text" name="surname" value="<?= htmlspecialchars($employee['surname']) ?>">

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($employee['email']) ?>" required>

            <label>Phone Number:</label>
            <input type="text" name="phoneNr" value="<?= htmlspecialchars($employee['phoneNr']) ?>">

            <label>Role:</label>
            <select name="role">
                <option value="1" <?= $employee['role'] == 1 ? 'selected' : '' ?>>Admin</option>
                <option value="2" <?= $employee['role'] == 2 ? 'selected' : '' ?>>Support</option>
            </select>

            <label>Password:</label>
            <input type="text" name="password" value=" ">

            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>