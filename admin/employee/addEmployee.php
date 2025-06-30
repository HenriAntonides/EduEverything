<?php
    session_start();

    require_once '../../database/dbConn.php';

    if(!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
        if (!isset($_SESSION['role'])){
            header("Location: ../adminLogin.php");
            exit;
        }
        echo ("Only Admins may access this page");
        exit;
    }

    $success = "";
    $error = "";

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $username = $_POST["username"];
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $name = $_POST["name"];
        $surname = $_POST["surname"];
        $email = $_POST["email"];
        $phoneNr = $_POST["phoneNr"] ?: null;
        $birthDate = $_POST["birthDate"];
        $idNumber = $_POST["idNumber"];
        $gender = $_POST["gender"];
        $role = $_POST["role"];

        // Chekc to see if Username already exists
        $checkU = $conn->prepare("SELECT username FROM employee WHERE username = ?");
        $checkU->bind_param("s", $username);
        $checkU->execute();
        $checkU->store_result();

        if ($checkU->num_rows > 0){
            $error = "Username already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO employee (username, password, name, surname, email, phoneNr, birthDate, idNumber, gender, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssi", $username, $password, $name, $surname, $email, $phoneNr, $birthDate, $idNumber, $gender, $role);

            if ($stmt->execute()) {
                $success = "Employee added successfully";
            } else {
                $error = "Error: " . $conn->error;
            }

        }
    }

    include "../adminHeader.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Employee</title>
    <link rel="stylesheet" href="../css/addEmployee.css">
</head>
<body>

    <div class="employeeForm">
        <form method="POST">
            <h2>Add New Employee</h2>

            <?php if ($success): ?>
                <div class="message success"><?= htmlspecialchars($success) ?></div>
            <?php elseif ($error): ?>
                <div class="message error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>Name:</label>
            <input type="text" name="name" required>

            <label>Surname:</label>
            <input type="text" name="surname" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Phone Number:</label>
            <input type="text" name="phoneNr" maxlength="10">

            <label>Birth Date:</label>
            <input type="date" name="birthDate" required>

            <label>ID Number:</label>
            <input type="text" name="idNumber" maxlength="15" required>

            <label>Gender:</label>
            <select name="gender" required>
                <option value="">-- Select Gender --</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <label>Role:</label>
            <select name="role" required>
                <option value="1">Admin</option>
                <option value="2">Support</option>
            </select>

            <button type="submit">Add Employee</button>
        </form>
    </div>
</body>
</html>