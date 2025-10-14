<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['account_id'])) {
    header("Location: login.php");
    exit;
}

// Get ticket_id from URL
if (isset($_GET['ticket_id'])) {
    $ticketId = intval($_GET['ticket_id']);

    // Fetch the ticket details
    $sql = "SELECT * FROM ticket WHERE ticket_id = ? AND created_by = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $ticketId, $_SESSION['account_id']);
    $stmt->execute();
    $ticket = $stmt->get_result()->fetch_assoc();

    if (!$ticket) {
        echo "Ticket not found or you don’t have access!";
        exit;
    }

    // Creator info
    $creator_sql = "SELECT account_id, name, role FROM account WHERE account_id = ?";
    $creator_stmt = $conn->prepare($creator_sql);
    $creator_stmt->bind_param("i", $ticket['created_by']);
    $creator_stmt->execute();
    $creator = $creator_stmt->get_result()->fetch_assoc();

    // Get assignee info (if any)
    $assignee = null;
    if (!empty($ticket['assigned_to'])) {
        $assignee_sql = "SELECT name, role FROM account WHERE account_id = ?";
        $assignee_stmt = $conn->prepare($assignee_sql);
        $assignee_stmt->bind_param("i", $ticket['assigned_to']);
        $assignee_stmt->execute();
        $assignee = $assignee_stmt->get_result()->fetch_assoc();
    }

    // Fetch messages for this ticket
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
  <title>User Ticket Chat</title>
  <link rel="stylesheet" href="css/userTicketStyle.css">
  <link rel="stylesheet" href="css/userStyle.css">
  <style>
    .main-content {
      display: flex;
      flex: 1;
      background: #f4f4f4;
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
        <label for="toggleTicket" class="createTicket">Create a Ticket</label>
        <a href="userHome.php"><h4>Home</h4></a>
        <label for="ticketDropdown" class="dropdown-label">
          <h4>Your Tickets ▼</h4>
        </label>
        <input type="checkbox" id="ticketDropdown" class="dropdown-checkbox">
        <div class="dropdown-menu">
          <?php
          $userId = $_SESSION['account_id'];

          $sql = "SELECT ticket_id, title, status, is_anonymous 
                  FROM ticket 
                  WHERE created_by = ? AND status != 'Closed' 
                  ORDER BY created_at DESC";

          $stmt = $conn->prepare($sql);
          $stmt->bind_param("i", $userId);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  $title = $row['title'] ?: "Untitled Ticket";

                  if ($row['is_anonymous']) {
                      $title .= " (Anonymous)";
                  }

                  $status = htmlspecialchars($row['status']);
                  $ticketId = htmlspecialchars($row['ticket_id']);

                  echo "<a href='userTicket.php?ticket_id={$ticketId}'>
                          " . htmlspecialchars($title) . "
                          <span style='font-size:12px; color:gray;'>[$status]</span>
                        </a>";
              }
          } else {
              echo "<p style='padding:5px; color:gray;'>No open tickets</p>";
          }
          ?>
        </div>
        <h4>Suggestions</h4>
        <ul>
          <a href="suggestions.php">
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
          <a href="editprofile.php">Edit Profile</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>
  </aside>

  <!-- Main Content: Chat + Right Sidebar -->
  <div class="main-content">
    <!-- Chat Container -->
    <div class="chat-container">
      <div class="chat-header">
        <h2>Ticket #<?php echo $ticket['ticket_id']; ?></h2>
        <p>Issue: <?php echo htmlspecialchars($ticket['title']); ?> (<?php echo htmlspecialchars($ticket['status']); ?>)</p>
        <p>Details:<?php echo nl2br(htmlspecialchars($ticket['details'])); ?></p>
      </div>

      <!-- Messages -->
      <div class="messages">
        <?php while ($row = $messages->fetch_assoc()) { 
            // Determine sender class based on role
            if ($row['account_id'] == $_SESSION['account_id']) {
                $senderClass = strtolower($_SESSION['role']); // Logged-in user's role
            } else {
                $senderClass = strtolower($row['role']); // Sender's role from DB
            }

            // Handle anonymous display
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
          <input type="hidden" name="ticket_id" value="<?php echo $ticket['ticket_id']; ?>">
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
          <span>
            <?php 
              if ($ticket['is_anonymous']) {
                  echo "Anonymous";
                  if ($ticket['created_by'] == $_SESSION['account_id']) {
                      echo " (You)";
                  }
              } else {
                  echo htmlspecialchars($creator['name']);
                  if ($creator['account_id'] == $_SESSION['account_id']) {
                      echo " (You)";
                  }
              }
            ?>
          </span>
        </li>

        <?php if ($assignee) { ?>
          <li>
            <div class="user-icon staff"></div>
            <span><?php echo htmlspecialchars($assignee['name']); ?> (<?php echo $assignee['role']; ?>)</span>
          </li>
        <?php } else { ?>
          <li><span style="color: gray;">No staff assigned yet</span></li>
        <?php } ?>
      </ul>
    </div>
  </div>

  <input type="checkbox" id="toggleTicket" hidden>
  <!-- Ticket Overlay -->
  <div class="ticket-content">
    <div class="tickets">
      <h1>Create a Ticket</h1>
      <form method="POST" action="create_ticket.php">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" placeholder="Enter ticket title" required>

        <label for="category">Category</label>
        <select id="category" name="category" required>
          <option>Harassment</option>
          <option>Bullying</option>
          <option>Misconduct</option>
          <option>Vandalism or Theft</option>
          <option>Academic Concerns</option>
          <option>Health and Safety</option>
          <option>Peer Conflicts</option>
          <option>Discrimination</option>
          <option>Substance Abuse</option>
          <option>Mental Health</option>
          <option>Attendance and Truancy</option>
          <option>Teacher Misconduct</option>
          <option>Facilities Issues</option>
          <option>Others</option>
        </select>

        <label for="details">Details</label>
        <textarea id="details" name="details" placeholder="Describe your issue..." required></textarea>

        <label for="beAnonymous" class="checkbox-label">
          <input type="checkbox" id="beAnonymous" name="beAnonymous" value="1">
          Submit Anonymously
        </label>

        <div class="form-actions">
          <button type="submit" class="submit-btn">Submit</button>
          <button type="reset" class="reset-btn">Reset</button>
        </div>
      </form>
      <!-- Close button -->
      <label for="toggleTicket" class="close-btn">&times;</label>
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
        messagesDiv.scrollTop = messagesDiv.scrollHeight; // auto-scroll to bottom
      });
  }

  // Fetch every 3 seconds
  setInterval(fetchMessages, 3000);
</script>

</html>
