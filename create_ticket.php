<?php
session_start();
require 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $details = trim($_POST['details']);
    $anonymous = isset($_POST['beAnonymous']) ? 1 : 0;

    // Always store real user internally
    $created_by = $_SESSION['account_id'] ?? null;

    // Insert ticket (created_by still stored, but shown as Anonymous in UI if $anonymous=1)
    $sql = "INSERT INTO ticket (created_by, title, category, details, is_anonymous) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssi", $created_by, $title, $category, $details, $anonymous);

    if ($stmt->execute()) {
        $ticketId = $conn->insert_id; // ✅ correct way

        // Log metadata for spam protection
        $ip = $_SERVER['REMOTE_ADDR'];
        $ua = $_SERVER['HTTP_USER_AGENT'];

        $log_sql = "INSERT INTO ticket_log (ticket_id, real_user_id, ip_address, user_agent) 
                    VALUES (?, ?, ?, ?)";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->bind_param("iiss", $ticketId, $created_by, $ip, $ua);
        $log_stmt->execute();

        echo "<script>alert('Ticket created successfully!'); window.location='userHome.php';</script>";
    } else {
        echo "<script>alert('Error creating ticket: " . $conn->error . "'); window.location='userHome.php';</script>";
    }
}
?>
