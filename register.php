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
    <title>Spik en Span- Login Page</title>
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
    <footer>
        &copy; 2025 Spik en Span. Alle rechten voorbehouden.
        <a href="privacyverklaring.html">Bekijk onze privacyverklaring</a>
    </footer>
</body>
<style>
body {
    background-color: rgba(82, 82, 82, 0.6);
    background-blend-mode: darken;
    font-family: Arial, sans-serif;
    background-image: url('carnaval-background.jpg'); /* Voeg een carnavalsfoto toe als achtergrond */
    background-size: cover;
    background-position: center;
    color: #FFEB3B; /* Geel thema voor de tekstkleur */
    text-align: center;
    margin: 0;
    padding: 0;
}

footer {
    background-color: #F44336;
    color: white;
    padding: 10px 0px;
    position: relative;
    bottom: 0;
    width: 100%;
}

h1 {
    text-align: center;
    color: #F44336; /* Rode kleur voor de titel */
    text-shadow: 2px 2px #4CAF50; /* Groen schaduweffect voor een vrolijke uitstraling */
}

.container {
    background-color: rgba(255, 255, 255, 0.8); /* Licht transparante achtergrond voor het formulier */
    border-radius: 10px;
    padding: 20px;
    width: 400px;
    margin: 50px auto;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); /* Schaduw voor een modern effect */
}

label {
    font-weight: bold;
    color: #4CAF50; /* Groen thema */
    display: block;
    margin-top: 15px;
}

input[type="text"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 2px solid #F44336; /* Rode rand voor een feestelijk detail */
    border-radius: 5px;
}

button {
    background-color: #4CAF50; /* Groen thema */
    color: white;
    border: none;
    padding: 15px 30px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 10px;
    margin-top: 10px;
}

button:hover {
    background-color: #45a049; /* Donkergroen bij hover voor interactie */
}

div {
    text-align: center;
    color: #F44336; /* Rode kleur voor foutmeldingen of berichten */
    font-weight: bold;
    margin-top: 10px;
}
</style>
</html>
