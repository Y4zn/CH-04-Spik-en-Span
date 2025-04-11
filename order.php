<?php
session_start();

if (!isset($_SESSION["username"])) {
  header("Location: login.php");
  exit;
} else {
  echo "<h2>Logged in as, ".$_SESSION["username"]."</h1>";
}

if (isset($_POST["oneClickOrder"])) {
  $servername = "localhost";
  $username = "root";
  $password = "";
  $pdo = new PDO("mysql:host=$servername;dbname=ticketsysteem",$username, $password);
  $ticketNumber = rand(1000, 9999);
  $checkEmail = $_POST["email"];
  $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

  if (preg_match($pattern, $checkEmail)) {
    $validEmail = $checkEmail;
    $stmt = $pdo->prepare("INSERT INTO tickets (ticket_number, user, email) VALUES (?, ?, ?)");
    $stmt->execute(array($ticketNumber, $_SESSION["username"], $validEmail));
    // DATA IS NOT BEING SENT TO THE DATABASE!!!!!!!!!!!!!!!!!!!!!!!!! FIX THIS 
    echo "<h2>Order completed successfully!</h2>";
    echo "<h3>Check your inbox for the ticket.</h3>";
    return;
  } else {
    $message = "Invalid email format.";
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Complete order</title>
</head>
<body>
  <h1>Buy Tickets</h1>
  <h2>Complete your order</h2>
  <br>
  <form method="post">
    <label for="email"><b>Your e-mail:</b></label>
    <input type="text" placeholder="example@domain.com" name="email" required>
    <p>You'll receive your ticket via e-mail</p>
    <button type="submit" name="oneClickOrder">One click purchase</button>
  </form>
  <?php if (isset($message)): ?> 
    <div><?php echo $message; ?></div>
  <?php endif; ?>
</body>
</html>
