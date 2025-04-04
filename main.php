<?php
session_start();

if (isset($_SESSION["username"])) {
  echo "<h1>Welcome, ".$_SESSION["username"]."!</h1>";
  echo "<p>You have successfully logged in.</p>";
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Main Page</title>
</head>
<body>


</body>
</html>
