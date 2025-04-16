<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use setasign\Fpdi\Fpdi;  // Hier wordt nu de juiste FPDI namespace gebruikt

session_start();

if (isset($_SESSION["username"])) {
  echo "<h2>Welcome back ".$_SESSION["firstname"]." ".$_SESSION["lastname"]."!</h1>";
}

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
function generateTicket($validEmail) {

    $validTicketNumber = checkTicketNumberAvailability(rand(10000, 99999));

    $uniekeData = $validEmail . '_' . uniqid();
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
    
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'Ticket Number:', 0, 0); // Label
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, $validTicketNumber, 0, 1); // Value
    
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'E-mail:', 0, 0); // Label
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, $validEmail, 0, 1); // Value
    
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'Username:', 0, 0); // Label
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, $_SESSION['username'], 0, 1); // Value
    
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
    $pdfPath = __DIR__ . '/ticket.pdf';
    $pdf->Output('F', $pdfPath);

    // E-mail verzenden
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'siemsirakm@gmail.com';
        $mail->Password   = 'hhxv zash qgpe lxyd'; // app-wachtwoord
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('siemsirakm@gmail.com', 'Spik en Span');
        $mail->addAddress($validEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Jouw ticket voor Spik en Span';
        $mail->Body    = 'Bedankt voor je aankoop! In de bijlage vind je jouw ticket met QR-code.';

        // PDF bijlage toevoegen
        $mail->addAttachment($pdfPath, 'ticket.pdf');

        $mail->send();
        insertTicketIntoDatabase($validEmail, $validTicketNumber);
    } catch (Exception $e) {
        echo "Verzenden mislukt: {$mail->ErrorInfo}";
    }
}
function insertTicketIntoDatabase($validEmail, $validTicketNumber) {
  try {
  $servername = "localhost";
  $username = "root";
  $password = "";
  $pdo = new PDO("mysql:host=$servername;dbname=ticketsysteem",$username, $password);
  $stmt = $pdo->prepare("INSERT INTO tickets (ticket_number, user, email) VALUES (?, ?, ?)");
  $stmt->execute(array($validTicketNumber, $_SESSION["username"], $validEmail));
  echo "<h2>Order completed successfully!</h2>";
  echo "<h3>Check your inbox for the ticket.</h3>";
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}

if (isset($_POST["oneClickOrder"])) {

  if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
  }

  // generateTicket($_SESSION['email']);
  
}

?>
<!DOCTYPE html>
<html>
<head>
  <title>Complete order</title>
</head>
<body>
  <h1>Order Tickets</h1>
  <form method="pos2t">
    <p>You'll receive your ticket via e-mail</p>
    <button type="submit" name="oneClickOrder">One click purchase</button>
  </form>
  <?php if (isset($message)): ?> 
    <div><?php echo $message; ?></div>
  <?php endif; ?>
</body>
</html>
