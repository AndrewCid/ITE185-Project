<?php
session_start();
require_once "includes/db.php";
$conn = getDB();

// If already logged in, block access to register
if (isset($_SESSION["user"])) {
    header("Location: home.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Basic validation
    if (!$name || !$email || !$username || !$password) {
        $error = "All fields are required.";
    } else {

        // Check if username or email exists
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);

        if ($check->rowCount() > 0) {
            $error = "Username or email already exists.";
        } else {

            // Insert account as STAFF by default
            $stmt = $conn->prepare("
                INSERT INTO users (name, email, username, password, role)
                VALUES (?, ?, ?, ?, 'staff')
            ");

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            if ($stmt->execute([$name, $email, $username, $hashed])) {
                $success = "Account created successfully!";
            } else {
                $error = "Failed to create account.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - WDN App</title>
    <style>
        body {
            background: linear-gradient(to right, #1d3557, #457b9d);
            font-family: Arial, sans-serif;
            color: white;
            display: flex; justify-content:center; align-items:center;
            height: 100vh;
        }
        .reg-box {
            background:#ffffff22;
            padding:30px;
            width:350px;
            border-radius:12px;
            backdrop-filter: blur(6px);
        }
        input {
            width:100%; padding:10px; margin:10px 0;
            border-radius:8px; border:none;
        }
        button {
            width:100%; padding:10px; margin-top:10px;
            border:none; border-radius:8px; cursor:pointer;
            background:#a8dadc; font-weight:bold;
        }
        .error { color:#ff4b4b; text-align:center; margin-top:10px; }
        .success { color:#98ff98; text-align:center; margin-top:10px; }
        a { color:#f1faee; text-decoration:underline; }
    </style>
</head>
<body>

<div class="reg-box">
    <h2 style="text-align:center;">Create Account</h2>

    <form method="POST">

        <input type="text" name="name" placeholder="Full Name" required>

        <input type="email" name="email" placeholder="Email Address" required>

        <input type="text" name="username" placeholder="Username" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Register</button>
    </form>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
        <div style="text-align:center; margin-top:10px;">
            <a href="login.php">Back to login</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
