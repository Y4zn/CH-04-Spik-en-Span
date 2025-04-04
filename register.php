<?php
    $servername = "localhost";
    $username = "root";
    $password = "";

    $pdo = new PDO("mysql:host=$servername;dbname=ticketsysteem",$username, $password);

    session_start();

    if (isset($_SESSION['username'])) {
        header("Location: index.php");
        exit;
    }
    
    if (isset($_POST['register'])) {
    
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
    
        if ($confirmPassword != $password) {
            $message = "Passwords dont match.";
        } else if ($user) {
            $message = "User already exist.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute(array($username, $hashed_password));
            $message = 'Success';
        }
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticketsysteem - Login</title>
</head>
<body>
    <?php if (isset($message)): ?>
        <div><?php echo $message; ?></div>
    <?php endif; ?>
    
    <div class="container">
        <h1>Ticketsysteem</h1>
        <h2>Register</h2>
        <form method="post">
            <label for="username"><b>Username:</b></label>
            <input type="text" placeholder="username" name="username" required>
            <br>
            <label for="password"><b>Password:</b></label>
            <input type="text" placeholder="password" name="password" required>
            <br>
            <label for="confirmPassword"><b>Confirm password:</b></label>
            <input type="text" placeholder="confirm password" name="confirmPassword" required>
            <br>
            <button type="submit" name="register">Register</button>
            <button name="login" onclick="window.location.href='login.php'">Login</button>
        </form>
    </div>
</body>
</html>
