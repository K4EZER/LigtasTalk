<?php
session_start();
require 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ticketId = intval($_POST['ticket_id']);
    $content = trim($_POST['message']);
    $senderId = $_SESSION['account_id'];

    if (!empty($content)) {
        $sql = "INSERT INTO message (ticket_id, sender_id, content) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $ticketId, $senderId, $content);
        $stmt->execute();
    }
}

header("Location: userTicket.php?ticket_id=" . $ticketId);
exit;
?>
