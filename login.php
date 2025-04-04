<?php
    $servername = "localhost";
    $username = "root";
    $password = "";

    $pdo = new PDO("mysql:host=$servername;dbname=ticketsysteem",$username, $password);

    session_start();

    if (isset($_SESSION['username'])) {
      header("Location: profile.php");
      exit;
    }
    
    if (isset($_POST['login'])) {
    
      $username = $_POST['username'];
      $password = $_POST['password'];
    
      $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
      $stmt->execute([$username]);
      $user = $stmt->fetch();
    
      if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        header("Location: profile.php");
        exit;
      } else {
        $message = "Invalid username or password.";
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
        <h1>Login</h1>
        <form method="post">
            <label for="username"><b>Username:</b></label>
            <input type="text" placeholder="username" name="username" required>
            <br>
            <label for="password"><b>Password:</b></label>
            <input type="text" placeholder="password" name="password" required>
            <br>
            <button type="submit" name="login">Login</button>
            <button type="button" onclick="window.location.href='register.php'">Register</button>
        </form>
    </div>
</body>
</html>
