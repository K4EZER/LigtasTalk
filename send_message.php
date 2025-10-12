<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['account_id'])) {
    exit("Unauthorized access!");
}

$accountId = $_SESSION['account_id'];
$role = $_SESSION['role'] ?? null;

// 🗑️ If Admin wants to delete a message
if ($role === 'Admin' && isset($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']);

    // Verify that the message exists
    $checkMsg = $conn->prepare("SELECT ticket_id FROM message WHERE message_id = ?");
    $checkMsg->bind_param("i", $deleteId);
    $checkMsg->execute();
    $result = $checkMsg->get_result();

    if ($result->num_rows > 0) {
        $ticket = $result->fetch_assoc();
        $ticketId = $ticket['ticket_id'];

        // Delete the message
        $delete = $conn->prepare("DELETE FROM message WHERE message_id = ?");
        $delete->bind_param("i", $deleteId);
        $delete->execute();

        // Redirect back to the ticket page
        header("Location: adminTicket.php?ticket_id=" . $ticketId);
        exit;
    } else {
        exit("Message not found!");
    }
}

// 📨 Sending a new message
if (!isset($_POST['ticket_id']) || !isset($_POST['message'])) {
    exit("Invalid access!");
}

$ticketId = intval($_POST['ticket_id']);
$message = trim($_POST['message']);

if ($message === '') {
    exit("Message cannot be empty!");
}

// 🧾 Check if the sender is allowed to message
if ($role === 'Admin') {
    $checkSql = "SELECT ticket_id FROM ticket WHERE ticket_id = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("i", $ticketId);
} else {
    // Staff and Users can only message tickets they created or are assigned to
    $checkSql = "SELECT ticket_id FROM ticket WHERE ticket_id = ? AND (created_by = ? OR assigned_to = ?)";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("iii", $ticketId, $accountId, $accountId);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    exit("Ticket not found or you don't have access!");
}

// 💬 Insert message into database
$insertSql = "INSERT INTO message (ticket_id, sender_id, content, timestamp) VALUES (?, ?, ?, NOW())";
$insertStmt = $conn->prepare($insertSql);
$insertStmt->bind_param("iis", $ticketId, $accountId, $message);
$insertStmt->execute();

// 🔁 Redirect back to correct page
if ($role === 'Admin' || $role === 'Staff') {
    header("Location: adminTicket.php?ticket_id=" . $ticketId);
} else {
    header("Location: userTicket.php?ticket_id=" . $ticketId);
}
exit;
?>
