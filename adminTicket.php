<?php
session_start();
require 'connect.php';

// Check if Admin/Staff is logged in
if (!isset($_SESSION['account_id'])) {
    header("Location: login.php");
    exit();
}

// Ensure admin/staff access
if (!isset($_SESSION['account_id']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
    header("Location: login.php");
    exit;
}

// --- CLOSE TICKET HANDLER --- ⬅️
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['close_ticket'])) {
    $ticketId = intval($_POST['ticket_id']);
    $update = $conn->prepare("UPDATE ticket SET status = 'Closed' WHERE ticket_id = ?");
    $update->bind_param("i", $ticketId);
    $update->execute();
    header("Location: adminTicket.php?ticket_id=" . $ticketId);
    exit;
}

// Get ticket_id from URL
if (isset($_GET['ticket_id'])) {
    $ticketId = intval($_GET['ticket_id']);

    // Fetch ticket details
    $sql = "SELECT * FROM ticket WHERE ticket_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ticketId);
    $stmt->execute();
    $ticket = $stmt->get_result()->fetch_assoc();

    if (!$ticket) {
        echo "Ticket not found!";
        exit;
    }

    // Fetch creator info
    $creator_sql = "SELECT account_id, name, role FROM account WHERE account_id = ?";
    $creator_stmt = $conn->prepare($creator_sql);
    $creator_stmt->bind_param("i", $ticket['created_by']);
    $creator_stmt->execute();
    $creator = $creator_stmt->get_result()->fetch_assoc();

    // Fetch assigned staff info
    $assignee = null;
    if (!empty($ticket['assigned_to'])) {
        $assignee_sql = "SELECT name, role FROM account WHERE account_id = ?";
        $assignee_stmt = $conn->prepare($assignee_sql);
        $assignee_stmt->bind_param("i", $ticket['assigned_to']);
        $assignee_stmt->execute();
        $assignee = $assignee_stmt->get_result()->fetch_assoc();
    }

    // Fetch messages
    $msg_sql = "SELECT m.*, a.name, a.role, a.account_id 
                FROM message m
                JOIN account a ON m.sender_id = a.account_id
                WHERE m.ticket_id = ?
                ORDER BY m.timestamp ASC";
    $msg_stmt = $conn->prepare($msg_sql);
    $msg_stmt->bind_param("i", $ticketId);
    $msg_stmt->execute();
    $messages = $msg_stmt->get_result();

} else {
    echo "No ticket selected.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ticket Chat</title>
  <link rel="stylesheet" href="css/adminTicketStyle.css">
  <link rel="stylesheet" href="css/adminStyle.css">
  <style>
    .main-content {
      display: flex;
      flex: 1;
      background: #f4f4f4;
    }
    .ticket-actions {
      margin-top: 15px;
      display: flex;
      gap: 10px;
    }
    .ticket-actions form button {
      background: #007bff;
      border: none;
      color: white;
      padding: 8px 12px;
      border-radius: 6px;
      cursor: pointer;
    }
    .ticket-actions form button:hover {
      background: #0056b3;
    }
    .close-btn {
      background: #dc3545 !important;
    }
    .close-btn:hover {
      background: #a71d2a !important;
    }

    .sidebar{
      position: static;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <div>
      <div class="LogoContainer">
        <img class="logo image" src="images/Logo.jpg" alt="LigtasTalk Logo">
        <h2 class="logo title">LigtasTalk</h2>
      </div>
      <div class="menu">
        <a href="adminDashboard.php"><h4>Dashboard</h4></a>
        <h4>Active Tickets</h4>
        <ul>
          <?php
          $categories = $conn->query("SELECT category, COUNT(*) AS total FROM ticket GROUP BY category");
          if ($categories->num_rows > 0):
            while ($cat = $categories->fetch_assoc()):
          ?>
              <li><?= htmlspecialchars($cat['category']) ?> <span class="badge"><?= $cat['total'] ?></span></li>
          <?php endwhile; else: ?>
              <li>No tickets yet</li>
          <?php endif; ?>
        </ul>
        <h4>Suggestions</h4>
        <ul>
          <a href="adminSuggestions.php">
            <li>
              All 
              <span class="badge">
                <?php
                $suggestionCountQuery = "SELECT COUNT(*) AS total FROM suggestion";
                $result = $conn->query($suggestionCountQuery);
                if ($result && $row = $result->fetch_assoc()) {
                    echo htmlspecialchars($row['total']);
                } else {
                    echo "0";
                }
                ?>
              </span>
            </li>
          </a>
        </ul>
      </div>
    </div>
    <div class="bottom">
      <div class="profile">
        <div class="profile-pic"></div>
        <div class="username"><?php echo htmlspecialchars($_SESSION['name']); ?></div>
      </div>
      <div class="usern-container">
        <input type="checkbox" id="ellipsisToggle" class="ellipsis-checkbox">
        <label for="ellipsisToggle" class="ellipsis">⋮</label>
        <div class="user-menu">
          <?php if ($_SESSION['role'] === 'Admin') {
              echo '<a href="adminRegister.php">Create Account</a>';
            } else {
              echo '';
          }?>

          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="main-content">
    <div class="chat-container">
      <div class="chat-header">
        <h2>Ticket #<?= $ticket['ticket_id']; ?></h2>
        <p>Issue: <?= htmlspecialchars($ticket['title']); ?> (<?= htmlspecialchars($ticket['status']); ?>)</p>
        <p>Details: <?= nl2br(htmlspecialchars($ticket['details'])); ?></p>

        <div class="ticket-actions">
          <!-- Assign button -->
          <?php if (empty($ticket['assigned_to']) && in_array($_SESSION['role'], ['Admin', 'Staff'])&& $ticket['status'] !== 'Closed'): ?>
            <form method="POST" action="assign_ticket.php">
              <input type="hidden" name="ticket_id" value="<?= $ticket['ticket_id']; ?>">
              <button type="submit">Assign to Me</button>
            </form>
          <?php endif; ?>

          <!-- Close ticket button --> <!-- ⬅️ NEW -->
          <?php if ($ticket['status'] !== 'Closed' && $_SESSION['role'] === 'Admin'): ?>
            <form method="POST" onsubmit="return confirm('Are you sure you want to close this ticket?');">
              <input type="hidden" name="ticket_id" value="<?= $ticket['ticket_id']; ?>">
              <button type="submit" name="close_ticket" class="close-btn">Close Ticket</button>
            </form>
          <?php endif; ?>
        </div>
      </div>

      <!-- Messages -->
      <div class="messages">
        <?php while ($row = $messages->fetch_assoc()) {
            if ($row['account_id'] == $_SESSION['account_id']) {
                $senderClass = strtolower($_SESSION['role']);
            } else {
                $senderClass = strtolower($row['role']);
            }

            $displayName = ($ticket['is_anonymous'] && $row['role'] === 'User')
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
        <?php } ?>
      </div>

      <!-- Chat Input -->
      <div class="chat-input">
        <form method="POST" action="send_message.php" style="display:flex; gap:10px; width:100%;">
          <input type="hidden" name="ticket_id" value="<?= $ticket['ticket_id']; ?>">
          <input type="text" name="message" placeholder="Type your message..." autocomplete="off" required />
          <button type="submit">Send</button>
        </form>
      </div>
    </div>

    <!-- Right Sidebar -->
    <div class="right-sidebar">
      <h3>Participants</h3>
      <ul>
        <li>
          <div class="user-icon user"></div>
          <span><?= $ticket['is_anonymous'] ? "Anonymous" : htmlspecialchars($creator['name']); ?></span>
        </li>

        <?php if ($assignee): ?>
          <li>
            <div class="user-icon staff"></div>
            <span><?= htmlspecialchars($assignee['name']); ?> (<?= $assignee['role']; ?>)</span>
          </li>
        <?php else: ?>
          <li><span style="color: gray;">Not assigned</span></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</body>
<script>
  const messagesDiv = document.querySelector(".messages");
  const ticketId = <?php echo json_encode($ticket['ticket_id']); ?>;
  const isAnonymous = <?php echo json_encode($ticket['is_anonymous']); ?>;

  function fetchMessages() {
    fetch(`updateMessage.php?ticket_id=${ticketId}&is_anonymous=${isAnonymous}`)
      .then(res => res.text())
      .then(html => {
        messagesDiv.innerHTML = html;
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
      });
  }

  // Refresh messages every 3 seconds
  setInterval(fetchMessages, 3000);
</script>
</html>
