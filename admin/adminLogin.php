<?php 
    session_start();
    require_once '../database/dbConn.php';

    $error = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];

        // Prepare statement to avoid SQL injection
        $stmt = $conn->prepare("SELECT * FROM employee WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $allowedRoles = [1, 2];

        if ($user && in_array($user['role'], $allowedRoles) && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = true;

            // Select the correct role for the user
            switch ($user['role']) {
                case 1:
                    $_SESSION["role"] = 'admin';
                    break;
                case 2:
                    $_SESSION["role"] = 'support';
                    break;
            }

            // Optionally update last login
            $updateStmt = $conn->prepare("UPDATE employee SET lastLogin = NOW() WHERE username = ?");
            $updateStmt->bind_param("s", $username);
            $updateStmt->execute();

            header("Location: ./index.php"); // redirect to admin dashboard
            exit;
        } else {
            $error = "Invalid login or not authorized.";
        }
    }


    ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="./css/adminlogin.css">
</head>
<body>

    <div class="login-container">
        <h2>Admin Login</h2>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="username">Username</label>
            <input type="text" name="username" required>

            <label for="password">Password</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>

</body>
</html>
