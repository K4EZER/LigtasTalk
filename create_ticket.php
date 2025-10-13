<?php
session_start();
require 'connect.php';

// Ensure connection uses UTF-8
$conn->set_charset("utf8mb4");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $details = trim($_POST['details'] ?? '');
    $anonymous = isset($_POST['beAnonymous']) ? 1 : 0;
    $created_by = $_SESSION['account_id'] ?? null;

    if (empty($title) || empty($category) || empty($details)) {
        echo "<script>alert('Please fill in all fields.'); window.location='userHome.php';</script>";
        exit;
    }

    $sql = "INSERT INTO ticket (created_by, title, category, details, is_anonymous) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("isssi", $created_by, $title, $category, $details, $anonymous);

    if ($stmt->execute()) {
        $ticketId = $conn->insert_id;

        // Log metadata
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
        $log_sql = "INSERT INTO ticket_log (ticket_id, real_user_id, ip_address, user_agent) VALUES (?, ?, ?, ?)";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->bind_param("iiss", $ticketId, $created_by, $ip, $ua);
        $log_stmt->execute();

        echo "<script>alert('Ticket created successfully!'); window.location='userHome.php';</script>";
    } else {
        echo "<script>alert('Error creating ticket: " . addslashes($stmt->error) . "'); window.location='userHome.php';</script>";
    }
}
?>
