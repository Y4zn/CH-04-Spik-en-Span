<?php
session_start();

if (!isset($_SESSION['username'])) {
  session_destroy();
  header("Location: login.php");
  exit;
}

if (isset($_POST['logout'])) {
  session_destroy();
  header("Location: login.php");
  exit;
}

$servername = "localhost";
$username = "root";
$password = "";

$pdo = new PDO("mysql:host=$servername;dbname=ticketsysteem",$username, $password);

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND admin = '1'");
$stmt->execute([$_SESSION['username']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT * FROM tickets WHERE user = ?');
$stmt->execute([$_SESSION['username']]);

?>
<!DOCTYPE html>
<html>
<head>
  <title>Tickets</title>
</head>
<body>
  <form method="post">
    <button type="submit" name="logout">Logout</button>
  </form>
  <?php if ($admin):?>
  <button onclick="window.location.href='scanner.php'">ADMIN Page</button>
  <?php endif; ?>
  <h2>Welcome back <?php echo $_SESSION['firstname']." ". $_SESSION['lastname']; ?>!</h2>
  <?php if ($stmt->rowCount() > 0): ?>
      <h2>Your Tickets:</h2>
  <table border="1">
  <tr>
    <th>Ticket Number</th>
    <th>User</th>
    <th>Email</th>
    <th>Timestamp</th>
  </tr>
  <?php while ($result = $stmt->fetch()): ?>
    <tr>
      <td><?php echo $result['ticket_number']; ?></td>
      <td><?php echo $result['user']; ?></td>
      <td><?php echo $result['email']; ?></td>
      <td><?php echo $result['timestamp']; ?></td>
    </tr>
  <?php endwhile; ?>
  </table>
  <?php else: ?>
    <br>
    <h1>You currently have no tickets.</h1>
  <?php endif; ?>
  <br>
  <h2>Buy Tickets</h2>
  <button onclick="window.location.href='order.php'">Buy Tickets</button>
</body>
</html>
