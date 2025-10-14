<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['account_id'])) {
  exit("Unauthorized access.");
}

$role = $_SESSION['role'] ?? '';
$accountId = $_SESSION['account_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_ticket_id'])) {
  if ($role === 'Admin') {
    $ticket_id = intval($_POST['delete_ticket_id']);
    $stmt = $conn->prepare("DELETE FROM ticket WHERE ticket_id = ?");
    $stmt->bind_param("i", $ticket_id);

    if ($stmt->execute()) {
      echo "<script>alert('Ticket deleted successfully!'); window.location.href = window.location.href;</script>";
      exit;
    } else {
      echo "<script>alert('Failed to delete ticket.');</script>";
    }
    $stmt->close();
  } else {
    echo "<script>alert('Unauthorized action.');</script>";
  }
}

$search = $_POST['search'] ?? '';
$status = $_POST['status'] ?? '';
$category = $_POST['category'] ?? '';

$query = "SELECT 
            t.ticket_id, 
            t.title, 
            t.category, 
            t.status, 
            a.name AS assigned_name, 
            t.created_at
          FROM ticket t
          LEFT JOIN account a ON t.assigned_to = a.account_id
          WHERE 1";

if ($role === 'Staff') {
  $query .= " AND (t.assigned_to IS NULL OR t.assigned_to = '' OR t.assigned_to = '$accountId')";
}

if (!empty($search)) {
  $search = $conn->real_escape_string($search);
  $query .= " AND (t.title LIKE '%$search%' OR t.ticket_id LIKE '%$search%')";
}

if (!empty($status)) {
  $status = $conn->real_escape_string($status);
  $query .= " AND t.status = '$status'";
}

if (!empty($category)) {
  $category = $conn->real_escape_string($category);
  $query .= " AND t.category = '$category'";
}

$query .= " ORDER BY t.created_at DESC";
$result = $conn->query($query);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $statusClass = '';
    if ($row['status'] == 'Open') $statusClass = 'open';
    elseif ($row['status'] == 'In-progress') $statusClass = 'progress';
    elseif ($row['status'] == 'Closed') $statusClass = 'closed';

    $now = new DateTime();
    $created = new DateTime($row['created_at']);
    $diff = $now->diff($created);
    if ($diff->d > 0) $timeAgo = $diff->d . 'd ago';
    elseif ($diff->h > 0) $timeAgo = $diff->h . 'h ago';
    elseif ($diff->i > 0) $timeAgo = $diff->i . 'm ago';
    else $timeAgo = 'Just now';

    echo "<tr>
        <td><a href='adminTicket.php?ticket_id=" . urlencode($row['ticket_id']) . "' style='text-decoration:none; color:inherit;'>" . htmlspecialchars($row['title']) . "</a></td>
        <td><span class='status $statusClass'>" . htmlspecialchars($row['status']) . "</span></td>
        <td>" . htmlspecialchars($row['category'] ?? 'N/A') . "</td>
        <td>" . htmlspecialchars($row['assigned_name'] ?? 'Unassigned') . "</td>
        <td>$timeAgo</td>";

    echo "<td>";
    if ($role === 'Admin') {
      echo "
        <form method='POST' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete this ticket?');\">
          <input type='hidden' name='delete_ticket_id' value='" . htmlspecialchars($row['ticket_id']) . "'>
          <button type='submit' style='padding:4px 10px; background:#E53935; color:white; border:none; border-radius:4px; cursor:pointer;'>
            Delete
          </button>
        </form>";
    }
    echo "</td>";

    if ($row['status'] === 'Closed' && $role === 'Admin') {
      echo "
        <td>
          <form action='reopenTicket.php' method='POST' style='display:inline;'>
            <input type='hidden' name='ticket_id' value='" . htmlspecialchars($row['ticket_id']) . "'>
            <button type='submit' style='padding:4px 10px; background:#4CAF50; color:white; border:none; border-radius:4px; cursor:pointer;'>
              Reopen Ticket
            </button>
          </form>
        </td>";
    } else {
      echo "<td></td>";
    }

    echo "</tr>";
  }
} else {
  echo "<tr><td colspan='7'>No tickets found.</td></tr>";
}
?>
