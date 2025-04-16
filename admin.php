<?php
session_start();

if (!isset($_SESSION['username'])) {
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
if (!$admin) {
    header("Location: profile.php");
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM tickets');
$stmt->execute();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Spik en Span - Admin Page</title>
</head>
<body>
  <button onclick="window.location.href='scanner.php'">QR-Code Scanner Page</button>
  <p>You have successfully logged in as Administrator.</p>
  <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
  <?php if ($stmt->rowCount() > 0): ?>
      <h2>All Tickets:</h2>
  <table border="1">
  <tr>
    <th>Ticket Number</th>
    <th>Validation (1 = Valid / 0 = Expired)</th>
    <th>Date and time</th>
    <th>Ticket Type</th>
    <th>User</th>
    <th>Email</th>
    <th>Timestamp</th>
  </tr>
  <?php while ($result = $stmt->fetch()): ?>
    <tr>
      <td><?php echo $result['ticket_number']; ?></td>
      <td><?php echo $result['valid']; ?></td>
      <td><?php echo $result['date_and_time']; ?></td>
      <td><?php echo $result['ticket_type']; ?></td>
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
</body>
</html>
