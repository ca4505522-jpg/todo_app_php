<?php
session_start();
include 'db.php';

$error = '';
$success = '';

// Signup Logic
if (isset($_POST['register'])) {
    $user = trim($_POST['username']);
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT); // Password secure karne ke liye

    if (!empty($user) && !empty($_POST['password'])) {
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $user, $pass);
        if ($stmt->execute()) {
            $success = "Account ban gaya hai! Ab aap login kar sakte hain.";
        } else {
            $error = "Yeh username pehle se mojood hai!";
        }
        $stmt->close();
    }
}

// Login Logic
if (isset($_POST['login'])) {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();
        if (password_verify($pass, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $user;
            header("Location: index.php");
            exit();
        } else {
            $error = "Galat password!";
        }
    } else {
        $error = "User nahi mila!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login / Signup</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .auth-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); width: 300px; }
        h2 { text-align: center; color: #333; }
        input { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; }
        button:hover { background: #0056b3; }
        .error { color: red; font-size: 14px; text-align: center; }
        .success { color: green; font-size: 14px; text-align: center; }
    </style>
</head>
<body>

<div class="auth-box">
    <h2>Login</h2>
    <?php if($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
    <?php if($success): ?><p class="success"><?php echo $success; ?></p><?php endif; ?>
    
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <hr style="margin: 20px 0;">

    <h2>Sign Up</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Choose Username" required>
        <input type="password" name="password" placeholder="Choose Password" required>
        <button type="submit" name="register" style="background: #28a745;">Register</button>
    </form>
</div>

</body>
</html>