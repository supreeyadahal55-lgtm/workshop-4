<?php
$errors = [];
$success = "";

$name = $email = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get form data
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";

    // Validation
    if ($name === "") {
        $errors["name"] = "Name is required";
    }

    if ($email === "") {
        $errors["email"] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Invalid email format";
    }

    if ($password === "") {
        $errors["password"] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors["password"] = "Password must be at least 6 characters";
    }

    if ($confirm === "") {
        $errors["confirm"] = "Please confirm your password";
    } elseif ($password !== $confirm) {
        $errors["confirm"] = "Passwords do not match";
    }

    // If no errors → save to JSON
    if (empty($errors)) {

        if (!file_exists("users.json")) {
            $errors["file"] = "users.json file not found";
        } else {
            $jsonData = file_get_contents("users.json");
            $users = json_decode($jsonData, true);

            if (!is_array($users)) {
                $users = [];
            }

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // New user data
            $users[] = [
                "name" => $name,
                "email" => $email,
                "password" => $hashedPassword
            ];

            // Save back to JSON
            if (file_put_contents("users.json", json_encode($users, JSON_PRETTY_PRINT))) {
                $success = "Registration successful!";
                $name = $email = "";
            } else {
                $errors["file"] = "Error writing to users.json";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Registration</title>

<style>
body {
    font-family: Arial;
    min-height: 100vh;
    margin: 0;
    background: url("https://i.pinimg.com/1200x/43/b2/fc/43b2fcc9cb84c925a1b015f5e71b5b61.jpg");
    background-size: cover;
    background-position: center;
    display: flex;
    justify-content: center;
    align-items: center;
}

.container {
    width: 400px;
    background: rgba(255,255,255,0.8);
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 15px 30px rgba(0,0,0,0.3);
}

label {
    font-weight: bold;
}

input {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
}

.error {
    color: red;
    font-size: 14px;
}

.success {
    background: #d4edda;
    color: #155724;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 6px;
}

button {
    width: 100%;
    padding: 12px;
    background: #007bff;
    color: white;
    border: none;
    cursor: pointer;
}
</style>
</head>

<body>

<div class="container">
    <h2>User Registration</h2>

    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>">
        <div class="error"><?= $errors["name"] ?? "" ?></div>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>">
        <div class="error"><?= $errors["email"] ?? "" ?></div>

        <label>Password</label>
        <input type="password" name="password">
        <div class="error"><?= $errors["password"] ?? "" ?></div>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password">
        <div class="error"><?= $errors["confirm"] ?? "" ?></div>

        <button type="submit">Register</button>
    </form>

    <div class="error"><?= $errors["file"] ?? "" ?></div>
</div>

</body>
</html>
