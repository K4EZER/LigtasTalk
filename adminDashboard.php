<?php
session_start();
require 'connect.php';

// -------- Fetch counts for dashboard --------
$totalTickets = $conn->query("SELECT COUNT(*) AS total FROM ticket")->fetch_assoc()['total'];
$openTickets = $conn->query("SELECT COUNT(*) AS total FROM ticket WHERE status='Open'")->fetch_assoc()['total'];
$progressTickets = $conn->query("SELECT COUNT(*) AS total FROM ticket WHERE status='In-progress'")->fetch_assoc()['total'];
$closedTickets = $conn->query("SELECT COUNT(*) AS total FROM ticket WHERE status='Closed'")->fetch_assoc()['total'];

// -------- Fetch tickets for table display --------
$tickets = $conn->query("
  SELECT t.ticket_id, t.title, t.category, t.status, t.assigned_to, t.created_at
  FROM ticket t
  ORDER BY t.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LigtasTalk | Ticket Dashboard</title>
  <link rel="stylesheet" href="css/adminStyle.css">
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
          // dynamically count categories
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
          <li>Ideas <span class="badge">20</span></li>
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
          <a href="adminRegister.php">Create Account</a>
          <a href="editprofile.php">Edit Profile</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>
  </aside>

  <!-- Main -->
  <main class="main">
    <div class="header">
      <h2>Ticket Dashboard</h2>
      <p>Manage and track support tickets</p>
    </div>

    <!-- Dashboard -->
    <div class="dashboard">
      <div class="dashboardBox"><h3><?= $totalTickets ?></h3><p>Total Tickets</p></div>
      <div class="dashboardBox"><h3 class="open"><?= $openTickets ?></h3><p>Open</p></div>
      <div class="dashboardBox"><h3 class="progress"><?= $progressTickets ?></h3><p>In Progress</p></div>
      <div class="dashboardBox"><h3 class="closed"><?= $closedTickets ?></h3><p>Closed</p></div>
    </div>

    <!-- Search and filters -->
    <div class="search-filter">
      <input type="text" placeholder="Search tickets...">
      <select>
        <option>All Status</option>
      </select>
      <select>
        <option>All Priority</option>
      </select>
      <button>Filter</button>
    </div>

    <!-- Tickets table -->
    <table>
      <thead>
        <tr>
          <th>Ticket</th>
          <th>Status</th>
          <th>Category</th>
          <th>Assigned To</th>
          <th>Last Activity</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($tickets->num_rows > 0): ?>
          <?php while ($row = $tickets->fetch_assoc()): ?>
            <tr>
              <td>
                <a href="adminTicket.php?ticket_id=<?= urlencode($row['ticket_id']) ?>">
                  <?= htmlspecialchars($row['title']) ?>
                </a>
              </td>
              <td>
                <?php
                  $statusClass = '';
                  if ($row['status'] == 'Open') $statusClass = 'open';
                  elseif ($row['status'] == 'In-progress') $statusClass = 'progress';
                  elseif ($row['status'] == 'Closed') $statusClass = 'closed';
                ?>
                <span class="status <?= $statusClass ?>"><?= htmlspecialchars($row['status']) ?></span>
              </td>
              <td><?= htmlspecialchars($row['category'] ?? 'N/A') ?></td>
              <td><?= htmlspecialchars($row['assigned_to'] ?? 'Unassigned') ?></td>
              <td>
                <?php
                  // Convert last activity (created_at) into “time ago”
                  $timeAgo = '';
                  $now = new DateTime();
                  $created = new DateTime($row['created_at']);
                  $diff = $now->diff($created);
                  if ($diff->d > 0) $timeAgo = $diff->d . 'd ago';
                  elseif ($diff->h > 0) $timeAgo = $diff->h . 'h ago';
                  elseif ($diff->i > 0) $timeAgo = $diff->i . 'm ago';
                  else $timeAgo = 'Just now';
                  echo $timeAgo;
                ?>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6">No tickets found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </main>
</body>
</html>
