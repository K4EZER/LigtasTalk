<?php
require 'connect.php';
session_start();

if (!isset($_GET['ticket_id'])) exit;

$ticketId = intval($_GET['ticket_id']);

$sql = "SELECT m.*, a.name, a.role, a.account_id
        FROM message m
        JOIN account a ON m.sender_id = a.account_id
        WHERE m.ticket_id = ?
        ORDER BY m.timestamp ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticketId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $senderClass = strtolower($row['role']);
    $displayName = ($row['role'] === 'User' && $_GET['is_anonymous'] == 1)
      ? 'Anonymous'
      : htmlspecialchars($row['name']);
?>
    <div class="message <?php echo $senderClass; ?>">
        <div class="sender-name">
            <h4><?php echo $displayName; ?></h4>
            <span class="role">(<?php echo htmlspecialchars($row['role']); ?>)</span>
        </div>
        <div class="message-content">
            <?php echo nl2br(htmlspecialchars($row['content'])); ?>
        </div>
        <span class="time"><?php echo date("h:i A", strtotime($row['timestamp'])); ?></span>
    </div>
<?php
}
?>
