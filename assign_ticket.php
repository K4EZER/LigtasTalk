<?php
session_start();
require 'connect.php';

// Ensure only Admin or Staff can assign
if (!isset($_SESSION['account_id']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    $ticketId = intval($_POST['ticket_id']);
    $staffId = $_SESSION['account_id'];

    // Assign ticket and update status to 'In Progress'
    $update = $conn->prepare("UPDATE ticket SET assigned_to = ?, status = 'In-progress' WHERE ticket_id = ?");
    $update->bind_param("ii", $staffId, $ticketId);

    if ($update->execute()) {
        header("Location: adminTicket.php?ticket_id=" . $ticketId);
        exit;
    } else {
        echo "Error updating ticket.";
    }
} else {
    echo "Invalid request.";
}
?>
