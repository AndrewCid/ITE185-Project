<?php
session_start();
require_once "includes/db.php";
$conn = getDB();

// If already logged in, skip login page
if (isset($_SESSION["user"])) {
    header("Location: home.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Fetch user by username
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        // store user in session
        $_SESSION["user"] = [
            "id" => $user["id"],
            "username" => $user["username"],
            "role" => $user["role"]
        ];

        header("Location: home.php");
        exit;
    } 
    else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - WDN App</title>
    <style>
        body {
            background: linear-gradient(to right, #1d3557, #457b9d);
            font-family: Arial, sans-serif;
            color: white;
            display: flex; justify-content:center; align-items:center;
            height: 100vh;
        }
        .login-box {
            background:#ffffff22;
            padding:30px;
            border-radius:12px;
            width:320px;
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
        .error {
            color:#ff4b4b; margin-top:10px;
            text-align:center;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 style="text-align:center;">WDN App Login</h2>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>

            <input type="password" name="password" placeholder="Password" required>

            <button type="submit">Login</button>
        </form>

        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
