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

    $ticketNumber = rand(1000000, 9999999); // Generate a random ticket number between 1000 and 9999

    $stmt = $pdo->prepare("INSERT INTO tickets (ticket_number, user) VALUES (?, ?)");
    $stmt->execute(array($ticketNumber, $_SESSION["username"]));
    echo "<h2>Order completed successfully!</h2>";
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Complete order</title>
</head>
<body>
  <h1>Complete order</h1>
  <br>
  <form method="post">
    <button type="submit" name="oneClickOrder">One click order</button>
  </form>
</body>
</html>
