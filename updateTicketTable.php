<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['account_id'])) {
    exit("Unauthorized access.");
}

$role = $_SESSION['role'] ?? '';
$accountId = (int)($_SESSION['account_id'] ?? 0);

//auto delete ticket
$oneYearAgo = (new DateTime())->modify('-1 year')->format('Y-m-d H:i:s');
$stmtDelete = $conn->prepare("DELETE FROM ticket WHERE created_at < ?");
$stmtDelete->bind_param("s", $oneYearAgo);
$stmtDelete->execute();
$stmtDelete->close();

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

// Staff can only see tickets assigned to them or unassigned
if ($role === 'Staff') {
    $query .= " AND (t.assigned_to IS NULL OR t.assigned_to = '' OR t.assigned_to = ?)";
}

$params = [];
$types = '';

if ($role === 'Staff') {
    $types .= 'i';
    $params[] = $accountId;
}

if (!empty($search)) {
    $query .= " AND (t.title LIKE ? OR t.ticket_id LIKE ?)";
    $types .= 'ss';
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
}

if (!empty($status)) {
    $query .= " AND t.status = ?";
    $types .= 's';
    $params[] = $status;
}

if (!empty($category)) {
    $query .= " AND t.category = ?";
    $types .= 's';
    $params[] = $category;
}

$query .= " ORDER BY t.created_at DESC";

$stmt = $conn->prepare($query);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

//table rows
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $statusClass = '';
        if ($row['status'] == 'Open') $statusClass = 'open';
        elseif ($row['status'] == 'In-progress') $statusClass = 'progress';
        elseif ($row['status'] == 'Closed') $statusClass = 'closed';

        $now = new DateTime();
        $created = new DateTime($row['created_at']);
        $diff = $now->diff($created);
        if ($diff->y > 0) $timeAgo = $diff->y . 'y ago';
        elseif ($diff->d > 0) $timeAgo = $diff->d . 'd ago';
        elseif ($diff->h > 0) $timeAgo = $diff->h . 'h ago';
        elseif ($diff->i > 0) $timeAgo = $diff->i . 'm ago';
        else $timeAgo = 'Just now';

        echo "<tr>
            <td><a href='adminTicket.php?ticket_id=" . urlencode($row['ticket_id']) . "' style='text-decoration:none; color:inherit;'>" . htmlspecialchars($row['title']) . "</a></td>
            <td><span class='status $statusClass'>" . htmlspecialchars($row['status']) . "</span></td>
            <td>" . htmlspecialchars($row['category'] ?? 'N/A') . "</td>
            <td>" . htmlspecialchars($row['assigned_name'] ?? 'Unassigned') . "</td>
            <td>$timeAgo</td>
            <td></td>";

        // Reopen Ticket button (only Admin, only Closed)
        if ($row['status'] === 'Closed' && $role === 'Admin') {
            echo "<td>
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

$stmt->close();
?>
