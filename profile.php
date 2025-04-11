<?php
$servername = "localhost";
$username = "root";
$password = "";

$pdo = new PDO("mysql:host=$servername;dbname=ticketsysteem",$username, $password);

session_start();

$stmt = $pdo->prepare('SELECT * FROM tickets WHERE user = ?');
$stmt->execute([$_SESSION['username']]);

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Tickets</title>
</head>
<body>
  <p>You have successfully logged in.</p>
  <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
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
    <h3>No tickets found.</h3>
  <?php endif; ?>
  <br>
  <h2>Buy Tickets</h2>
  <button onclick="window.location.href='order.php'">Buy Tickets</button>
</body>
</html>
