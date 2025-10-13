<?php
require 'connect.php';
session_start();

if (!isset($_SESSION['account_id']) || ($_SESSION['role'] !== 'Admin')) {
    exit("Unauthorized access!");
}

if (isset($_POST['ticket_id'])) {
    $ticketId = intval($_POST['ticket_id']);
    $sql = "UPDATE ticket SET status = 'Reopened' WHERE ticket_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ticketId);
    $stmt->execute();

    header("Location: adminDashboard.php");
    exit;
} else {
    exit("Invalid request!");
}
?>
