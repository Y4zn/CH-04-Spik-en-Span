<?php
session_start();

if (isset($_SESSION["username"])) {
  echo "<h2>Welcome back, ".$_SESSION["username"]."!</h1>";
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Spik en Span - Main Page</title>
</head>
<body>
  <h1>Main page</h1>
  <br>
    <b>Click this button to buy a ticket</b>
    <p>Description</p>
    <button onclick="window.location.href='order.php'">Buy Tickets</button>
</body>
</html>
