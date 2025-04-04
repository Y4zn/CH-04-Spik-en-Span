<?php
session_start();

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

  <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>

  <p>You have successfully logged in.</p>

  <h2>Your Tickets</h2>
  <ul>
    <li>Ticket 1</li>
    <li>Ticket 2</li>
    <li>Ticket 3</li>
  </ul>

</body>
</html>
