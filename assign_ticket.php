<?php
session_start();
require 'connect.php';

if ($_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

if (isset($_POST['ticket_id'])) {
    $ticket_id = intval($_POST['ticket_id']);
    $admin_id = $_SESSION['account_id'];

    $sql = "UPDATE ticket SET assigned_to = ? WHERE ticket_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $admin_id, $ticket_id);
    $stmt->execute();

    header("Location: adminTicket.php?ticket_id=$ticket_id");
    exit;
}
?>
