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
        $email = $_POST['email'];
        $confirmEmail = $_POST['confirmEmail'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute(array($username));
        $user = $stmt->fetch();

        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute(array($email));
        $existedEmail = $stmt->fetch();

        $emailPattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        $passwordPattern = '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.* ).{8,16}$/';
        $usernamePattern = '/^[a-zA-Z0-9]{3,16}$/';

        if (!preg_match($usernamePattern, $username)) {
            $message = "Username must be 3 to 16 characters long and contain only letters and numbers.";
        } else if ($user) {
            $message = "Username already exist.";
        } else if (!preg_match($emailPattern, $email)) {
            $message = "Invalid email format.";
        } else if ($email != $confirmEmail) {
            $message = "Emails dont match.";
        } else if ($existedEmail) {
            $message = "Email already exists.";
        } else if (!preg_match($passwordPattern, $password)) {
            $message = "Password must be 8-16 characters. Contains a number, a lowercase letter, an uppercase letter and a special character.";
        } else if ($password != $confirmPassword) {
            $message = "Passwords dont match.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, username, email, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute(array($_POST['firstname'], $_POST['lastname'], $username, $email, $hashed_password));
            $message = 'Success, you can login now.';
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
        <h1>Register</h1>
        <form method="post">
            <label for="firstname"><b>Firstname:</b></label>
            <input type="text" placeholder="" name="firstname" required>
            <br>
            <label for="lastname"><b>Lastname:</b></label>
            <input type="text" placeholder="" name="lastname" required>
            <br>
            <label for="username"><b>Username:</b></label>
            <input type="text" placeholder="" name="username" required>
            <br>
            <label for="email"><b>Email:</b></label>
            <input type="text" placeholder="" name="email" required>
            <br>
            <label for="confirmEmail"><b>Confirm email:</b></label>
            <input type="text" placeholder="" name="confirmEmail" required>
            <br>
            <label for="password"><b>Password:</b></label>
            <input type="text" placeholder="" name="password" required>
            <br>
            <label for="confirmPassword"><b>Confirm password:</b></label>
            <input type="text" placeholder="" name="confirmPassword" required>
            <br>
            <button type="submit" name="register">Register</button>
            <br>
            <button name="login" onclick="window.location.href='login.php'">Already have an account?</button>
        </form>
    </div>
</body>
</html>
