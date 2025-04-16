<?php
header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "";

$pdo = new PDO("mysql:host=$servername;dbname=ticketsysteem", $username, $password);

$data = json_decode(file_get_contents('php://input'), true);
$ticketNumber = $data['ticketNumber'];

$stmt = $pdo->prepare("SELECT * FROM tickets WHERE ticket_number = ? AND valid = 1");
$stmt->execute([$ticketNumber]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if ($ticket) {
    // Mark the ticket as invalid
    makeTicketUnvalid($ticketNumber);

    // Return a valid response
    echo json_encode(['valid' => true, 'ticket' => $ticket]);
    exit;
} else {
    // Return an invalid response
    echo json_encode(['valid' => false]);
    exit;
}

function makeTicketUnvalid($ticketNumber) {
    global $pdo;

    // Update the ticket's valid status to 0
    $stmt = $pdo->prepare("UPDATE tickets SET valid = 0 WHERE ticket_number = ?");
    $stmt->execute([$ticketNumber]);
}
?>