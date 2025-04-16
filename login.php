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
    
      $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
      $stmt->execute(array($username, $username));
      $user = $stmt->fetch();
    
      if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
      
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
    <title>Spik en Span - Login</title>
</head>
<body>
    <?php if (isset($message)): ?>
        <div><?php echo $message; ?></div>
    <?php endif; ?>
    
    <div class="container">
        <h1>Login</h1>
        <form method="post">
            <label for="username"><b>Username or email:</b></label>
            <input type="text" placeholder="" name="username" required>
            <br>
            <label for="password"><b>Password:</b></label>
            <input type="text" placeholder="" name="password" required>
            <br>
            <button type="submit" name="login">Login</button>
            <br>
            <button type="button" onclick="window.location.href='register.php'">Doesn't have an account?</button>
        </form>
    </div>
</body>
<style>
body {
    font-family: Arial, sans-serif;
    background-image: url('achtergrond.jpeg'); /* Vervang 'jouw-foto.jpg' met de naam van je foto */
    background-size: cover; /* Zorgt ervoor dat de foto het hele scherm bedekt */
    background-position: center; /* Centreert de foto op de pagina */
    margin: 0;
    padding: 0;
    color: rgb(255, 94, 0); /* Tekstkleur die bij de achtergrond past */
}

.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

button {
  font-size: 18px;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  background-color: rgb(255, 94, 0);
  color: white;
  cursor: pointer;
}

button:hover {
  background-color: rgb(255, 94, 0);
}

input[type="text"], input[type="password"] {
  font-size: 18px;
  padding: 10px;
  margin: 10px 0;
  border: 1px solid #ccc;
  border-radius: 5px;
  width: 100%;
  box-sizing: border-box;
}
</style>
</html>
