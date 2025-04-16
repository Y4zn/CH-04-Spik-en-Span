<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";

$pdo = new PDO("mysql:host=$servername;dbname=ticketsysteem", $username, $password);

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
  <title>Spik en Span - QR Code Scanner</title>
  <script type="text/javascript" src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
</head>
<body>
  <button onclick="window.location.href='admin.php'">ADMIN Page</button>
  <p>You have successfully logged in as Administrator.</p>
  <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
  <h2>QR Code Scanner 5000</h2>
  <h1 id="message"></h1>

  <!-- Video element for displaying the camera feed -->
  <video id="preview" style="width: 400px; border: 1px solid #ccc;"></video>

  <script>
    // Initialize the scanner
    let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
    let resetBackgroundTimeout; // Variable to store the timeout ID

    // Listen for scanned content
    scanner.addListener('scan', function (content) {
      document.getElementById("message").innerHTML = "Scanned ticket: " + content;

      // Clear any existing timeout to prevent premature reset
      clearTimeout(resetBackgroundTimeout);

      // Optionally, send the scanned content to the server for validation
      fetch('validate_ticket.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ ticketNumber: content })
      })
      .then(response => response.json())
      .then(data => {
        if (data.valid) {
          document.querySelector("body").style.backgroundColor = "green";
        } else {
          document.querySelector("body").style.backgroundColor = "red";
        }

        // Set a timeout to reset the background color after 2 seconds
        resetBackgroundTimeout = setTimeout(() => {
          document.querySelector("body").style.backgroundColor = "yellow";
          document.getElementById("message").innerHTML = ""; // Clear the message
        }, 4000);
      })
      .catch(error => console.error('Error:', error));
    });

    // Request camera access and start scanning
    Instascan.Camera.getCameras().then(function (cameras) {
      if (cameras.length > 0) {
        scanner.start(cameras[0]); // Use the first available camera
      } else {
        console.error('No cameras found.');
        alert('No cameras found. Please connect a camera and try again.');
      }
    }).catch(function (e) {
      console.error('Error accessing cameras:', e);
      alert('Error accessing cameras. Please check your camera permissions.');
    });
  </script>
</body>
<style>

body {
  background-color: yellow;
}

</style>
</html>