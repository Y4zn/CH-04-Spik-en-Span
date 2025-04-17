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

$stmt = $pdo->prepare('SELECT * FROM tickets WHERE user = ? AND valid = 1');
$stmt->execute([$_SESSION['username']]);

?>
<!DOCTYPE html>
<html>
<head>
  <title>Spik en Span - Your Profile</title>
</head>
<body>
  <form method="post">
    <button type="submit" name="logout">Logout</button>
  </form>
  <?php if ($admin):?>
  <button onclick="window.location.href='admin.php'">ADMIN Page</button>
  <button onclick="window.location.href='scanner.php'">QR-Code Scanner Page</button>
  <?php endif; ?>
  <h2>Welcome back <?php echo $_SESSION['firstname']." ". $_SESSION['lastname']; ?>!</h2>
  <?php if ($stmt->rowCount() > 0): ?>
      <h2>Your Tickets:</h2>
  <table border="1">
  <tr>
    <th>Ticket Number</th>
    <th>Type</th>
    <th>Date and time</th>
    <th>Email</th>
  </tr>
  <?php while ($result = $stmt->fetch()): ?>
    <tr>
      <td><?php echo $result['ticket_number']; ?></td>
      <td><?php echo $result['ticket_type']; ?></td>
      <td><?php echo $result['date_and_time']; ?></td>
      <td><?php echo $result['email']; ?></td>
    </tr>
  <?php endwhile; ?>
  </table>
  <?php else: ?>
    <br>
    <h1>You currently have no tickets.</h1>
  <?php endif; ?>
  <br>
  <button onclick="window.location.href='order.php'" id="btn-buyTickets">Buy Tickets</button>
</body>
<style>

body {
    background-color: rgba(82, 82, 82, 0.6);
    background-blend-mode: darken;
    font-family: Arial, sans-serif;
    background-image: url('carnaval-background.jpg'); /* Voeg een carnavalsachtergrond toe */
    background-size: cover;
    background-position: center;
    margin: 0;
    padding: 0;
    color: #FFEB3B; /* Gele tekstkleur voor carnavalsvibes */
}

h1, h2 {
    text-align: center;
    color: #F44336; /* Rode kleur voor koppen */
    text-shadow: 2px 2px #4CAF50; /* Groene schaduweffecten voor een vrolijke uitstraling */
    margin-top: 20px;
}

#btn-buyTickets {
    background-color: #FF5722; /* Bright orange for a festive look */
    color: white; /* White text for contrast */
    border: none;
    padding: 15px 30px;
    font-size: 18px; /* Slightly larger font size */
    cursor: pointer;
    border-radius: 12px; /* Rounded corners for a modern look */
    margin: 20px auto; /* Center the button horizontally */
    display: block; /* Make it a block element for centering */
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3); /* Add a shadow for depth */
    transition: all 0.3s ease; /* Smooth hover effect */
}

button {
    background-color: #4CAF50; /* Groen thema voor knoppen */
    color: white;
    border: none;
    padding: 15px 30px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 10px; /* Ronding voor een speelse look */
    margin: 40px;
}

button:hover {
    background-color: #45a049; /* Donkergroene kleur bij hover */
}

table {
    border-collapse: collapse;
    width: 80%;
    margin: 20px auto;
    background-color: rgba(255, 255, 255, 0.8); /* Licht transparant voor leesbaarheid */
    border-radius: 10px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); /* Schaduw voor een modern effect */
}

th, td {
    border: 2px solid #F44336; /* Rode rand voor feestelijk detail */
    padding: 10px;
    text-align: center;
}

th {
    background-color: #FFEB3B; /* Gele kop van de tabel */
    color: #F44336; /* Rode tekst voor contrast */
}

td {
    background-color: #FFFFFF; /* Witte achtergrond */
    color: #4CAF50; /* Groen thema voor tekst */
}

p {
    text-align: center;
    font-size: 18px;
    color: #FFEB3B; /* Gele kleur voor een feestelijke touch */
}

h3 {
    text-align: center;
    color: #F44336; /* Rode kleur voor kopteksten */
}

</style>
</html>
