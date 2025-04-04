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
  <title>Main Page</title>
</head>
<body>

  <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>

  <p>You have successfully logged in.</p>

</body>
</html>
