<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use setasign\Fpdi\Fpdi;  // Hier wordt nu de juiste FPDI namespace gebruikt

session_start();

// Autoload
require 'vendor/autoload.php';

function checkTicketNumberAvailability($ticketNumber){
  $servername = "localhost";
  $username = "root";
  $password = "";
  $pdo = new PDO("mysql:host=$servername;dbname=ticketsysteem",$username, $password);
  $stmt = $pdo->prepare("SELECT * FROM tickets WHERE ticket_number = ?");
  $stmt->execute(array($ticketNumber));
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($result) {
    return checkTicketNumberAvailability(rand(10000, 99999));
  } elseif (!$result) {
    $validTicketNumber = $ticketNumber;
    return $validTicketNumber;
  }
}
function generateTicket() {
    global $ticketType, $date_and_time; // Use global variables for ticket type and date/time

    $validTicketNumber = checkTicketNumberAvailability(rand(10000, 99999));

    // QR-code genereren
    $qrCode = new QrCode($validTicketNumber);
    $writer = new PngWriter();
    $qrImage = $writer->write($qrCode);
    $qrPath = __DIR__ . '/qrcode.png';
    $qrImage->saveToFile($qrPath);

    // PDF aanmaken
    $pdf = new \FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->Cell(0, 10, 'Spik en Span Ticket', 0, 1, 'C'); // Centered title
    $pdf->Ln(10); // Add some vertical space

    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(0, 10, 'Thank you for your purchase!', 0, 1, 'C'); // Centered subtitle
    $pdf->Ln(10);

    // Add ticket details
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'Ticket Number:', 0, 0); // Label
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, $validTicketNumber, 0, 1); // Value

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'E-mail:', 0, 0); // Label
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, $_SESSION['email'], 0, 1); // Value

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'Username:', 0, 0); // Label
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, $_SESSION['username'], 0, 1); // Value

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'Ticket Type:', 0, 0); // Label
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, ucfirst($ticketType), 0, 1); // Value (capitalize first letter)

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'Date and Time:', 0, 0); // Label
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, ucfirst($date_and_time), 0, 1); // Value (capitalize first letter)

    $pdf->Ln(20); // Add some vertical space

    // Add QR Code
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Scan the QR code below to validate your ticket:', 0, 1, 'C');
    $pdf->Image($qrPath, ($pdf->GetPageWidth() - 50) / 2, $pdf->GetY(), 50, 50); // Centered QR code
    $pdf->Ln(60); // Add space below the QR code

    // Footer
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->SetY(250); // Position footer at the bottom of the page
    $pdf->Cell(0, 10, 'Spik en Span - Your trusted ticketing service', 0, 1, 'C');
    $pdf->Cell(0, 10, 'Visit us at www.spikenspan.com', 0, 1, 'C');

    // Save the PDF
    $pdfPath = __DIR__ . '/ticket.pdf';
    $pdf->Output('F', $pdfPath);

    // Return the ticket number for further use
    $mail = new PHPMailer(true);
    insertTicketIntoDatabase($validTicketNumber);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'siemsirakm@gmail.com';
        $mail->Password   = 'zqtb knmz alim ovtn'; // app-wachtwoord
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('siemsirakm@gmail.com', 'Spik en Span');
        $mail->addAddress($_SESSION['email']);

        $mail->isHTML(true);
        $mail->Subject = 'Jouw ticket voor Spik en Span';
        $mail->Body    = 'Bedankt voor je aankoop! In de bijlage vind je jouw ticket met QR-code.';

        // PDF bijlage toevoegen
        $mail->addAttachment($pdfPath, 'ticket.pdf');

        $mail->send();
    } catch (Exception $e) {
        echo "Verzenden mislukt: {$mail->ErrorInfo}";
    }
}
function insertTicketIntoDatabase($validTicketNumber) {
  
  global $ticketType;
  global $date_and_time;

  try {
  $servername = "localhost";
  $username = "root";
  $password = "";
  $pdo = new PDO("mysql:host=$servername;dbname=ticketsysteem",$username, $password);
  $stmt = $pdo->prepare("INSERT INTO tickets (ticket_number, ticket_type, date_and_time, user, email) VALUES (?, ?, ?, ?, ?)");
  $stmt->execute(array($validTicketNumber, $ticketType, $date_and_time, $_SESSION["username"], $_SESSION['email']));
  echo "<h2 style=color: lightgreen;>Order completed successfully!</h2>";
  echo "<h3 style=color: lightgreen;>Check your inbox for the ticket.</h3>";
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}

if (isset($_POST["oneClickOrder"])) {

  $ticketType = $_POST['ticketType'];
  $date_and_time = $_POST['date_and_time'];

  $validTicketNumber = checkTicketNumberAvailability(rand(10000, 99999));

  if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
  }
  generateTicket();
}

?>
<!DOCTYPE html>
<html>
<head>
  <title>Spik en Span - Complete order</title>
</head>
<body>
  <br>
  <br>
  <br>
  <br>
  <br>
  <br>
  <h1>Order Tickets</h1>
  <form method="post">
    <p>You'll receive your ticket via e-mail</p>
    
    <label for="ticketType">Ticket for:</label>
    <select name="ticketType" id="ticketType" required>
      <option value="" disabled selected>Select a ticket type</option>
      <option value="adult">An Adult (12+)</option>
      <option value="child">A Child (4-11 years)</option>
    </select>
    <br>
    <label for="date_and_time">Time:</label>
    <select name="date_and_time" id="date_and_time" required>
      <option value="" disabled selected>Select a time</option>
      <option value="weekend">Weekend</option>
      <option value="weekdays">Weekdays</option>
    </select>

    <button type="submit" name="oneClickOrder">One click purchase</button>
  </form>
  <?php if (isset($message)): ?> 
    <div><?php echo $message; ?></div>
  <?php endif; ?>
  <footer>
        &copy; 2025 Spik en Span. Alle rechten voorbehouden.
        <a href="privacyverklaring.html">Bekijk onze privacyverklaring</a>
  </footer>
</body>
<style>
body {
    font-family: Arial, sans-serif;
    background-image: url('carnaval-achtergrond.jpg'); /* Voeg een carnavalsafbeelding toe als achtergrond */
    background-size: cover;
    background-position: center;
    margin: 0;
    padding: 0;
    color: #FFEB3B; /* Gele tekstkleur voor een levendig contrast */
}

footer {
    background-color: #F44336;
    color: white;
    padding: 10px 0px;
    position: absolute;
    bottom: 0;
    width: 100%;
    text-align: center;
}

h1 { 
    text-align: center;
    color: #F44336; /* Rode kleur voor koppen */
    text-shadow: 2px 2px #4CAF50; /* Groen schaduw effect voor een feestelijke uitstraling */
    margin-top: 20px;
    font-weight: bold;
    text-align: center;
}

p {
    text-align: center;
    color: #F44336; /* Rode kleur voor koppen */
    margin-top: 20px;
    font-weight: bold;
    text-align: center;
}

h2, h3{
  text-align: center;
    color:rgb(60, 244, 54); /* Rode kleur voor koppen */
    text-shadow: 2px 2px #4CAF50; /* Groen schaduw effect voor een feestelijke uitstraling */
    margin-top: 20px;
}

form {
    background-color: rgba(255, 255, 255, 0.8); /* Licht transparante achtergrond voor het formulier */
    border-radius: 10px;
    padding: 20px;
    width: 300px;
    margin: 0 auto;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); /* Schaduw voor een 3D-effect */
}

label {
    font-weight: bold;
    color: #4CAF50; /* Groen thema */
}

input[type="text"] {
    width: 90%;
    padding: 10px;
    margin: 10px 0;
    border: 2px solid #F44336; /* Rode rand */
    border-radius: 5px;
}

button {
    background-color: #4CAF50; /* Groen thema */
    color: white;
    border: none;
    padding: 15px 30px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 10px;
    margin-top: 10px;
}

button:hover {
    background-color: #45a049; /* Donkergroen bij hover */
}

div {
    color: #F44336; /* Rode foutmeldingen */
    font-weight: bold;
    text-align: center;
    margin-top: 10px;
}
</style>
</html>
